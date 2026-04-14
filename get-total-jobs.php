<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
header("Content-Type: application/json");

// DB Connection
$conn = new mysqli('sql305.infinityfree.com', 'if0_41462718', 'XeIA4BTfsG0', 'if0_41462718_jit');
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB Connection failed"]));
}

// Token validation function
function validate_token($conn) {
    $headers = getallheaders();
    if (empty($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Missing Authorization"]);
        exit;
    }
    list($type, $token) = explode(" ", $headers['Authorization'], 2);
    if ($type !== 'Bearer' || empty($token)) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid Authorization"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE token=? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows !== 1) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid or expired token"]);
        exit;
    }
}

// ✅ Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    validate_token($conn);

    $res = $conn->query("SELECT COUNT(*) as total_jobs FROM jobs");
    $data = $res->fetch_assoc();

    echo json_encode([
        "status" => "success",
        "total_jobs" => (int)$data['total_jobs']
    ]);
    exit;
}

http_response_code(405);
echo json_encode(["status" => "error", "message" => "Method not allowed"]);
$conn->close();
?>
