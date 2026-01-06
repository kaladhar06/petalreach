<?php
// deleteflower.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = "localhost";
$db_name = "petalreach";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$flower_id = isset($_GET['flower_id']) ? intval($_GET['flower_id']) : 0;

if ($flower_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid flower ID"]);
    exit;
}

// Changed table name from 'flowers' to 'addflower'
$sql = "DELETE FROM addflower WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $flower_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Flower deleted successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Flower not found"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete"]);
}

$stmt->close();
$conn->close();
?>