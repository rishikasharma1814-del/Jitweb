<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
header("Content-Type: application/json");

// DB Connection
$conn = new mysqli('localhost', 'xcbrfudmma', 'rrsAaD44H6', 'xcbrfudmma');
if ($conn->connect_error) { die(json_encode(["status"=>"error","message"=>"DB Connection failed"])); }

$input = json_decode(file_get_contents("php://input"), true);
if (empty($input['email']) || empty($input['password'])) {
    http_response_code(400);
    echo json_encode(["status"=>"error","message"=>"Email and password are required"]);
    exit;
}

$email = $conn->real_escape_string($input['email']);
$password = $conn->real_escape_string($input['password']);

$sql = "SELECT * FROM users WHERE email='$email' AND password='$password' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $token = bin2hex(random_bytes(32));
    $conn->query("UPDATE users SET token='$token', token_created_at=NOW() WHERE email='$email'");
    echo json_encode(["status"=>"success","message"=>"Login successful","token"=>$token]);
} else {
    http_response_code(401);
    echo json_encode(["status"=>"error","message"=>"Invalid email or password"]);
}
$conn->close();
?>
