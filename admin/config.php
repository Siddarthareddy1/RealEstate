<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "realestate_db");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

define("BASE_URL", "http://localhost/realestate/");
define("UPLOAD_DIR", "../uploads/");

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

if (!isAdmin()) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}
