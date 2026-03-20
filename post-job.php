<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
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

// GET Jobs
// ----------------- GET Jobs -----------------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Optional: GET single job by ID
    if (!empty($_GET['id'])) {
        $id = (int)$_GET['id'];
        $res = $conn->query("SELECT * FROM jobs WHERE id=$id LIMIT 1");
        if ($res->num_rows == 1) {
            $job = $res->fetch_assoc();
            // Set default values if null
            $job['working_hours'] = $job['working_hours'] ?? "10 AM to 7 PM";
            $job['working_days'] = $job['working_days'] ?? "5 days Monday to Friday";
            $job['vacancy'] = $job['vacancy'] ?? "2";
            $job['deadline'] = $job['deadline'] ?? date('Y-m-d', strtotime($job['post_date'].' +7 days'));

            echo json_encode([
                "status" => "success",
                "data" => $job
            ]);
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Job not found"]);
        }
        exit;
    }

    // Pagination
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
    $offset = ($page - 1) * $limit;

    // Total jobs
    $total = $conn->query("SELECT COUNT(*) as cnt FROM jobs")->fetch_assoc()['cnt'];

    // Fetch paginated jobs
    $res = $conn->query("SELECT * FROM jobs LIMIT $offset,$limit");
    $jobs = [];
    while ($row = $res->fetch_assoc()) {
        // Set default values if null
        $row['working_hours'] = $row['working_hours'] ?? "10 AM to 7 PM";
        $row['working_days'] = $row['working_days'] ?? "5 days Monday to Friday";
        $row['vacancy'] = $row['vacancy'] ?? "2";
        $row['deadline'] = $row['deadline'] ?? date('Y-m-d', strtotime($row['post_date'].' +7 days'));
        $jobs[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "page" => $page,
        "limit" => $limit,
        "total" => $total,
        "data" => $jobs
    ]);
    exit;
}


// POST Create Job
// POST Method: Create or Update Job
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_token($conn);
    $input = json_decode(file_get_contents("php://input"), true);

    // If job ID is present => UPDATE
    if (!empty($input['id'])) {
        $id = (int)$input['id'];
        unset($input['id']);

        $fields = [];
        $types = '';
        $values = [];

        foreach ($input as $k => $v) {
            $fields[] = "$k=?";
            $types .= 's';
            $values[] = $v;
        }

        $values[] = $id;
        $types .= 'i';

        $stmt = $conn->prepare("UPDATE jobs SET " . implode(",", $fields) . " WHERE id=?");
        $stmt->bind_param($types, ...$values);
        $stmt->execute();

        echo json_encode(["status" => "success", "message" => "Job updated"]);
        exit;
    }

    // Else => CREATE new job
    $required = ['title','location','post_date','experience','description','what_you_will_do','requirements','perks_benefits','end_para'];
    foreach($required as $field){ 
        if(empty($input[$field])){ 
            http_response_code(400); 
            echo json_encode(["status"=>"error","message"=>"$field required"]); 
            exit; 
        } 
    }

    $stmt = $conn->prepare("INSERT INTO jobs (title,location,post_date,experience,description,what_you_will_do,requirements,perks_benefits,end_para) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssssss",$input['title'],$input['location'],$input['post_date'],$input['experience'],$input['description'],$input['what_you_will_do'],$input['requirements'],$input['perks_benefits'],$input['end_para']);
    $stmt->execute();

    echo json_encode(["status"=>"success","message"=>"Job post created","job_id"=>$stmt->insert_id]); 
    exit;
}


// PUT Update Job
if ($_SERVER['REQUEST_METHOD']==='POST'){
    validate_token($conn);
    parse_str(file_get_contents("php://input"), $input);
    if(empty($input['id'])){ http_response_code(400); echo json_encode(["status"=>"error","message"=>"Job ID required"]); exit; }
    $id=(int)$input['id']; unset($input['id']);
    $fields=[]; $types=''; $values=[];
    foreach($input as $k=>$v){ $fields[]="$k=?"; $types.='s'; $values[]=$v; }
    $values[]=$id; $types.='i';
    $stmt=$conn->prepare("UPDATE jobs SET ".implode(",",$fields)." WHERE id=?");
    $stmt->bind_param($types,...$values); $stmt->execute();
    echo json_encode(["status"=>"success","message"=>"Job updated"]); exit;
}

// DELETE Job
if ($_SERVER['REQUEST_METHOD']==='DELETE'){
    validate_token($conn);
    parse_str(file_get_contents("php://input"), $input);
    if(empty($input['id'])){ http_response_code(400); echo json_encode(["status"=>"error","message"=>"Job ID required"]); exit; }
    $id=(int)$input['id'];
    $conn->query("DELETE FROM jobs WHERE id=$id");
    echo json_encode(["status"=>"success","message"=>"Job deleted"]); exit;
}

http_response_code(405); echo json_encode(["status"=>"error","message"=>"Method not allowed"]);
$conn->close();
?>
