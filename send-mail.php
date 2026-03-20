<?php
header("Access-Control-Allow-Origin: *"); // or restrict to a specific origin
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
session_start();

// =====================
// Database connection
// =====================
$host = "localhost";
$user = "xcbrfudmma";    
$pass = "rrsAaD44H6";        
$db   = "xcbrfudmma";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB Connection failed: " . $conn->connect_error]));
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

// =====================
// Send Email via PHPMailer
// =====================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 0; 
$mail->isSMTP();
$mail->Host       = 'smtp.gmail.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'info@jewarinternational.com';   // ✅ Google Workspace email
$mail->Password   = 'arec icnx qkpa owle';           // ✅ App Password (NOT your Gmail login password)
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // or 'tls'
$mail->Port       = 587;

$mail->setFrom('info@jewarinternational.com', 'Jewar International'); // ✅ sender
$mail->addAddress('tushar@jewarinternational.com', 'Admin');          // ✅ recipient


    // Content
    $mail->isHTML(true);
    $mail->Subject = "New Contact Form Submission";
    $mail->Body    = "
        <h2>New Contact Form Submission</h2>
        <p><b>Name:</b> {$name}</p>
        <p><b>Email:</b> {$email}</p>
        <p><b>Phone:</b> {$phone}</p>
        <p><b>Subject:</b> {$subject}</p>
        <p><b>Message:</b><br>{$message}</p>
    ";

    // Try to send
    if ($mail->send()) {
        // =====================
        // Save into database only if mail sent
        // =====================
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
        $stmt->execute();
        $stmt->close();

        echo json_encode(["status" => "success", "message" => "Message sent and saved successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Mail could not be sent"]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
}
