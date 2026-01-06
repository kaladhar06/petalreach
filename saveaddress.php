<?php
header('Content-Type: application/json');

$host = "localhost";
$username = "root";
$password = "";
$database = "petalreach";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Check if this is a save request (has full_name parameter)
if (isset($_REQUEST['full_name']) || isset($_POST['full_name'])) {
    // SAVE ADDRESS
    $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
    $full_name = isset($_REQUEST['full_name']) ? $_REQUEST['full_name'] : '';
    $phone = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : '';
    $street = isset($_REQUEST['street']) ? $_REQUEST['street'] : '';
    $city = isset($_REQUEST['city']) ? $_REQUEST['city'] : '';
    $pincode = isset($_REQUEST['pincode']) ? $_REQUEST['pincode'] : '';
    $address_type = isset($_REQUEST['address_type']) ? $_REQUEST['address_type'] : 'HOME';
    
    if ($user_id <= 0 || empty($full_name) || empty($phone)) {
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
        exit;
    }
    
    $sql = "INSERT INTO addresses (user_id, full_name, phone, street, city, pincode, address_type) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $user_id, $full_name, $phone, $street, $city, $pincode, $address_type);
    
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true, 
            "message" => "Address saved successfully!",
            "address_id" => $conn->insert_id
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to save: " . $stmt->error]);
    }
    $stmt->close();
}
// Check if this is a delete request
else if (isset($_REQUEST['address_id']) && isset($_REQUEST['delete'])) {
    $address_id = intval($_REQUEST['address_id']);
    $sql = "DELETE FROM addresses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $address_id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Address deleted"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete"]);
    }
    $stmt->close();
}
// GET addresses
else if (isset($_REQUEST['user_id'])) {
    $user_id = intval($_REQUEST['user_id']);
    
    $sql = "SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $addresses = [];
    while ($row = $result->fetch_assoc()) {
        $addresses[] = [
            "id" => intval($row['id']),
            "user_id" => intval($row['user_id']),
            "full_name" => $row['full_name'],
            "phone" => $row['phone'],
            "street" => $row['street'],
            "city" => $row['city'],
            "pincode" => $row['pincode'],
            "address_type" => $row['address_type'] ?? 'HOME',
            "is_default" => intval($row['is_default'] ?? 0),
            "created_at" => $row['created_at']
        ];
    }
    
    echo json_encode(["success" => true, "addresses" => $addresses]);
    $stmt->close();
}
else {
    echo json_encode(["success" => false, "message" => "No parameters provided"]);
}

$conn->close();
?>