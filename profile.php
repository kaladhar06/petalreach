<?php
header("Content-Type: application/json");

/* ---------- DB CONNECTION ---------- */
$conn = new mysqli("localhost", "root", "", "user_system");
if ($conn->connect_error) {
    die(json_encode(["status" => false, "message" => "DB connection failed"]));
}

/* ---------- USER ID (FROM SESSION LATER) ---------- */
$user_id = 1;

/* ---------- FETCH PROFILE ---------- */
$stmt = $conn->prepare("SELECT * FROM profile WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "status" => false,
        "message" => "Profile not found"
    ]);
    exit;
}

$data = $result->fetch_assoc();

/* ---------- RESPONSE ---------- */
echo json_encode([
    "status" => true,
    "profile" => [
        "name" => $data['name'],
        "username" => "@" . $data['username'],
        "role" => $data['role'],
        "profile_image" => $data['profile_image'],
        "stats" => [
            "growers_listed" => $data['growers_listed'],
            "bulk_orders" => $data['bulk_orders'],
            "events_supported" => $data['events_supported']
        ]
    ]
]);
?>
