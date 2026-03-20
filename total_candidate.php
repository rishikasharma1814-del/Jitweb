<?php
// total/apply_job.php (Example for Total Applications Count - PUBLIC ACCESS)

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type"); // Authorization header removed
// IMPORTANT: Only remove Authorization if you are certain this data should be public.

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
header("Content-Type: application/json");

// DB Connection (Use your credentials here)
// WARNING: DB credentials are hardcoded and visible in this script.
$conn = new mysqli('localhost', 'xcbrfudmma', 'rrsAaD44H6', 'xcbrfudmma');
if ($conn->connect_error) { die(json_encode(["status"=>"error","message"=>"DB Connection failed"])); }

/*
// Token validation function is now UNUSED
function validate_token($conn){
    $headers = getallheaders();
    if (empty($headers['Authorization'])) { http_response_code(401); echo json_encode(["status"=>"error","message"=>"Missing Authorization"]); exit; }
    list($type, $token) = explode(" ", $headers['Authorization'], 2);
    if ($type !== 'Bearer' || empty($token)) { http_response_code(401); echo json_encode(["status"=>"error","message"=>"Invalid Authorization"]); exit; }
    
    // SAFE: Uses prepared statement for token validation
    $stmt = $conn->prepare("SELECT id FROM users WHERE token=? LIMIT 1");
    $stmt->bind_param("s",$token);
    $stmt->execute(); $stmt->store_result();
    if ($stmt->num_rows !== 1) { http_response_code(401); echo json_encode(["status"=>"error","message"=>"Invalid or expired token"]); exit; }
}
*/

// GET Total Applications Count (Publicly Accessible)
if($_SERVER['REQUEST_METHOD']==='GET'){
    // Removed: validate_token($conn); // API is now public
    
    // Simple query to get the total count
    $res = $conn->query("SELECT COUNT(*) as cnt FROM applications");
    if (!$res) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database query failed"]);
        exit;
    }
    
    $total = $res->fetch_assoc()['cnt'];
    
    echo json_encode(["status"=>"success", "total_candidates"=>$total]);
    exit;
}

http_response_code(405); echo json_encode(["status"=>"error","message"=>"Method not allowed"]);
$conn->close();
?>