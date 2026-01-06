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

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$product_name = isset($_POST['product_name']) ? $_POST['product_name'] : '';
$product_image = isset($_POST['product_image']) ? $_POST['product_image'] : '';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$address = isset($_POST['address']) ? $_POST['address'] : '';
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$status = "Order Placed";

if ($user_id <= 0 || empty($product_name)) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

$sql = "INSERT INTO orders (user_id, product_name, product_image, price, address, quantity, status, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issdsis", $user_id, $product_name, $product_image, $price, $address, $quantity, $status);

if ($stmt->execute()) {
    $order_id = $conn->insert_id;
    echo json_encode([
        "success" => true,
        "message" => "Order placed successfully!",
        "order_id" => $order_id,
        "order_details" => [
            "id" => $order_id,
            "user_id" => $user_id,
            "product_name" => $product_name,
            "product_image" => $product_image,
            "price" => $price,
            "address" => $address,
            "quantity" => $quantity,
            "status" => $status,
            "order_date" => date("Y-m-d H:i:s")
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to place order"]);
}

$stmt->close();
$conn->close();
?>