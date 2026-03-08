<?php
session_start();

$db_host = getenv('DB_HOST') ?: (isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : "localhost");
$db_user = getenv('DB_USER') ?: (isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : "root");
$db_pass = getenv('DB_PASS') ?: (isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : "");
$db_name = getenv('DB_NAME') ?: (isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : "realestate_db");
$db_port = getenv('DB_PORT') ?: (isset($_ENV['DB_PORT']) ? $_ENV['DB_PORT'] : 3306);

// Extract port from host if provided in host:port format
if (strpos($db_host, ':') !== false) {
    list($db_host, $db_port) = explode(':', $db_host);
}

try {
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, (int)$db_port);
    if (!$conn) {
        throw new Exception(mysqli_connect_error());
    }
} catch (Exception $e) {
    die("Database connection failed. Please ensure your database environment variables (DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT) are set correctly in the Render Dashboard.<br><br><b>Host Attempted:</b> " . htmlspecialchars($db_host) . "<br><b>Error:</b> " . $e->getMessage());
}

$base_url = getenv('BASE_URL') ?: "http://localhost/realestate/";
define("BASE_URL", $base_url);
define("UPLOAD_DIR", "uploads/");

define("TWILIO_SID", "YOUR_TWILIO_SID");
define("TWILIO_AUTH_TOKEN", "YOUR_TWILIO_AUTH_TOKEN");
define("TWILIO_PHONE_NUMBER", "YOUR_TWILIO_PHONE_NUMBER");

function sendSMS($to, $message) {
    $sid = TWILIO_SID;
    $token = TWILIO_AUTH_TOKEN;
    $from = TWILIO_PHONE_NUMBER;
    
    if ($sid === "YOUR_TWILIO_SID") {
        return ["status" => "demo", "message" => $message, "otp" => substr($message, -6)];
    }
    
    $url = "https://api.twilio.com/2010-04-01/Accounts/$sid/Messages.json";
    $data = [
        "To" => $to,
        "From" => $from,
        "Body" => $message
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_USERPWD, "$sid:$token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isAgent() {
    return isset($_SESSION['role']) && ($_SESSION['role'] === 'agent' || $_SESSION['role'] === 'admin');
}

function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

function getUserById($id) {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
    return mysqli_fetch_assoc($result);
}

function getPropertyById($id) {
    global $conn;
    $result = mysqli_query($conn, "SELECT p.*, u.name as owner_name, u.email as owner_email, u.phone as owner_phone 
                                   FROM properties p 
                                   JOIN users u ON p.user_id = u.id 
                                   WHERE p.id = $id");
    return mysqli_fetch_assoc($result);
}

function getPropertyImages($property_id) {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM property_images WHERE property_id = $property_id");
    $images = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $images[] = $row;
    }
    return $images;
}

function getPrimaryImage($property_id) {
    global $conn;
    $result = mysqli_query($conn, "SELECT image_name FROM property_images WHERE property_id = $property_id AND is_primary = 1 LIMIT 1");
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        $result = mysqli_query($conn, "SELECT image_name FROM property_images WHERE property_id = $property_id LIMIT 1");
        $row = mysqli_fetch_assoc($result);
    }
    return $row ? $row['image_name'] : 'default.jpg';
}
