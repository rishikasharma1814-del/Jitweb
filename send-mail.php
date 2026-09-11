<?php
// CORS first — InfinityFree / proxies may serve HTML before PHP; when PHP runs, these must be present.
header("Access-Control-Allow-Origin: *", true);
header("Access-Control-Allow-Methods: POST, OPTIONS, GET", true);
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With", true);
header("Access-Control-Max-Age: 86400", true);
header("Vary: Origin", true);
header("Content-Type: application/json; charset=utf-8", true);

// If a runtime fatal happens later, still return JSON + CORS headers.
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null) {
        if (!headers_sent()) {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: POST, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
            header("Content-Type: application/json");
        }
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Server fatal error",
            "detail" => $error["message"] ?? "Unknown error",
        ]);
    }
});

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Quick CORS health check: GET /send-mail.php?ping=1
if (isset($_GET['ping'])) {
    echo json_encode(["status" => "success", "message" => "CORS OK"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

session_start();

// =====================
// Database connection
// =====================
$host = "sql305.infinityfree.com";
$user = "if0_41462718";
$pass = "XeIA4BTfsG0";
$db   = "if0_41462718_jit";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "DB Connection failed"]);
    exit;
}

// =====================
// Form data
// =====================
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// =====================
// Validation
// =====================
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(["status" => "error", "message" => "Name, Email, and Message are required"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email format"]);
    exit;
}

// Save into database first so form still works even if mail fails on host.
$stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to prepare DB query"]);
    exit;
}
$stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to save message"]);
    $stmt->close();
    exit;
}
$stmt->close();

// Prefer a real outbound send to the company's mailbox.
// If the hosting account does not allow SMTP, PHP mail() is still the most reliable fallback.
$targetTo = trim($_POST['to'] ?? $_POST['recipient'] ?? '');
$source   = trim($_POST['source'] ?? $_POST['page'] ?? '');

if (!empty($targetTo) && filter_var($targetTo, FILTER_VALIDATE_EMAIL)) {
    $recipient = $targetTo;
} elseif ($source === 'landing-page' || $source === 'landingpage') {
    $recipient = 'tushar@jewarinternational.com';
} else {
    $recipient = getenv('CONTACT_TO_EMAIL') ?: 'info@jewarinternational.com';
}

$fromEmail = getenv('CONTACT_FROM_EMAIL') ?: 'noreply@jewarinternational.com';
$fromName = ($source === 'landing-page' || $source === 'landingpage') ? 'JIT Landing Page Lead' : 'JIT Contact Form';
$subjectText = !empty($subject) ? $subject : 'Website contact inquiry';
$mailBody = "Name: $name\n" .
    "Email: $email\n" .
    "Phone: " . ($phone !== '' ? $phone : '-') . "\n\n" .
    "Subject: $subjectText\n\n" .
    "Message:\n$message\n";

$headers = [
    "From: {$fromName} <{$fromEmail}>",
    "Reply-To: {$email}",
    "MIME-Version: 1.0",
    "Content-Type: text/plain; charset=UTF-8"
];

$mailSent = @mail($recipient, $subjectText, $mailBody, implode("\r\n", $headers));
$mailError = $mailSent ? '' : 'Email delivery attempt failed on this hosting environment.';

echo json_encode([
    "status" => $mailSent ? "success" : "success",
    "message" => $mailSent ? "Message sent and saved successfully" : "Message saved successfully",
    "mail_sent" => $mailSent,
    "mail_note" => $mailError,
    "recipient" => $recipient,
    "subject" => $subjectText
]);
