<?php
header("Content-Type: application/json");

// Database connection
$pdo = new PDO(
    "mysql:host=127.0.0.1;dbname=petalreach;charset=utf8mb4",
    "root",
    ""
);

// Accept JSON or POST form-data
$data = json_decode(file_get_contents("php://input"), true);

$email = $data["email"] ?? $_POST["email"] ?? null;
$password = $data["password"] ?? $_POST["password"] ?? null;

if (!$email || !$password) {
    echo json_encode([
        "status" => "error",
        "message" => "Email and password are required"
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid email format"
    ]);
    exit;
}

// Check if email exists
$stmt = $pdo->prepare("SELECT id FROM loginpage WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    echo json_encode([
        "status" => "error",
        "message" => "Email already registered"
    ]);
    exit;
}

// Insert new user
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO loginpage (email, password_hash) VALUES (?, ?)");
$stmt->execute([$email, $hash]);

echo json_encode([
    "status" => "success",
    "message" => "User created successfully"
]);
