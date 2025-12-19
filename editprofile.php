<?php
/* ================= DATABASE CONNECTION ================= */
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "petalreach";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ================= USER ID (FROM SESSION LATER) ================= */
$user_id = 1; // example logged-in user id

/* ================= CREATE UPLOAD FOLDER ================= */
$uploadDir = "uploads/profiles/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

/* ================= READ INPUT (JSON OR FORM) ================= */
$data = json_decode(file_get_contents("php://input"), true);

$name  = $data['name']  ?? $_POST['name']  ?? '';
$email = $data['email'] ?? $_POST['email'] ?? '';
$phone = $data['phone'] ?? $_POST['phone'] ?? '';

$imagePath = null;

/* ================= DP FROM JSON (BASE64) ================= */
if (!empty($data['profile_image_base64'])) {
    $imageData = base64_decode($data['profile_image_base64']);

    $fileName = "profile_" . $user_id . "_" . time() . ".jpg";
    $fullPath = $uploadDir . $fileName;

    file_put_contents($fullPath, $imageData);
    $imagePath = $fullPath;
}

/* ================= DP FROM WEB FORM ================= */
if (!empty($_FILES['profile_image']['name'])) {
    $fileName = "profile_" . $user_id . "_" . time() . ".jpg";
    $fullPath = $uploadDir . $fileName;

    move_uploaded_file($_FILES['profile_image']['tmp_name'], $fullPath);
    $imagePath = $fullPath;
}

/* ================= UPDATE PROFILE + DP ================= */
if ($imagePath) {
    $stmt = $conn->prepare(
        "UPDATE editprofile 
         SET name=?, email=?, phone=?, profile_image=? 
         WHERE id=?"
    );
    $stmt->bind_param("ssssi", $name, $email, $phone, $imagePath, $user_id);
} else {
    $stmt = $conn->prepare(
        "UPDATE editprofile 
         SET name=?, email=?, phone=? 
         WHERE id=?"
    );
    $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
}

if ($stmt->execute()) {
    echo json_encode([
        "status" => true,
        "message" => "DP and profile updated successfully"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Update failed"
    ]);
}
?>
