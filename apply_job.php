<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
header("Content-Type: application/json");

// DB Connection
$conn = new mysqli('localhost', 'xcbrfudmma', 'rrsAaD44H6', 'xcbrfudmma');
if ($conn->connect_error) { die(json_encode(["status"=>"error","message"=>"DB Connection failed"])); }

// Token validation
function validate_token($conn){
    $headers = getallheaders();
    if (empty($headers['Authorization'])) { http_response_code(401); echo json_encode(["status"=>"error","message"=>"Missing Authorization"]); exit; }
    list($type, $token) = explode(" ", $headers['Authorization'], 2);
    if ($type !== 'Bearer' || empty($token)) { http_response_code(401); echo json_encode(["status"=>"error","message"=>"Invalid Authorization"]); exit; }
    $stmt = $conn->prepare("SELECT id FROM users WHERE token=? LIMIT 1");
    $stmt->bind_param("s",$token);
    $stmt->execute(); $stmt->store_result();
    if ($stmt->num_rows !== 1) { http_response_code(401); echo json_encode(["status"=>"error","message"=>"Invalid or expired token"]); exit; }
}

// GET applications (paginated)
if($_SERVER['REQUEST_METHOD']==='GET'){
    validate_token($conn);
    $page = isset($_GET['page'])?(int)$_GET['page']:1;
    $perPage = isset($_GET['per_page'])?(int)$_GET['per_page']:10;
    $offset = ($page-1)*$perPage;
    $total = $conn->query("SELECT COUNT(*) as cnt FROM applications")->fetch_assoc()['cnt'];
    $res = $conn->query("SELECT * FROM applications LIMIT $offset,$perPage");
    $data=[]; while($row=$res->fetch_assoc()){$data[]=$row;}
    echo json_encode(["status"=>"success","page"=>$page,"per_page"=>$perPage,"total"=>$total,"total_pages"=>ceil($total/$perPage),"data"=>$data]); exit;
}

// GET applications (paginated)

// POST application
if($_SERVER['REQUEST_METHOD']==='POST'){
    // validate_token($conn);
    if(!isset($_POST['job_id'],$_POST['name'],$_POST['email'],$_POST['phone'],$_FILES['resume'])){
        http_response_code(400); echo json_encode(["status"=>"error","message"=>"All fields required"]); exit;
    }
    $job_id=(int)$_POST['job_id']; $name=$_POST['name']; $email=$_POST['email']; $phone=$_POST['phone'];
    $resume=$_FILES['resume'];

    $job = $conn->query("SELECT title FROM jobs WHERE id=$job_id")->fetch_assoc();
    if(!$job){ http_response_code(404); echo json_encode(["status"=>"error","message"=>"Job not found"]); exit; }

    if($resume['error']!==0 || $resume['type']!=='application/pdf'){ http_response_code(400); echo json_encode(["status"=>"error","message"=>"Resume must be PDF"]); exit; }

    $resume_dir='resumes/';
    if(!file_exists($resume_dir)){ mkdir($resume_dir,0777,true); }
    $resume_path = $resume_dir.uniqid().'_'.basename($resume['name']);
    move_uploaded_file($resume['tmp_name'],$resume_path);

    $stmt=$conn->prepare("INSERT INTO applications (job_id,job_title,name,email,phone,resume) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("isssss",$job_id,$job['title'],$name,$email,$phone,$resume_path);
    $stmt->execute();

    echo json_encode(["status"=>"success","message"=>"Application submitted","application_id"=>$stmt->insert_id]);
    exit;
}

http_response_code(405); echo json_encode(["status"=>"error","message"=>"Method not allowed"]);
$conn->close();
?>
