<?php
// create_yourjourney_table.php
// Run this once to create/update the yourjourney table

$host = "localhost";
$db_name = "petalreach";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create yourjourney table if not exists
$sql = "CREATE TABLE IF NOT EXISTS yourjourney (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_image VARCHAR(500),
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    price DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Order Placed',
    address TEXT,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "✅ Table 'yourjourney' created/exists successfully!<br>";
} else {
    echo "❌ Error creating table: " . $conn->error . "<br>";
}

// Add address column if not exists
$sql_address = "ALTER TABLE yourjourney ADD COLUMN IF NOT EXISTS address TEXT";
if ($conn->query($sql_address) === TRUE) {
    echo "✅ Column 'address' added successfully!<br>";
}

// Add quantity column if not exists
$sql_quantity = "ALTER TABLE yourjourney ADD COLUMN IF NOT EXISTS quantity INT DEFAULT 1";
if ($conn->query($sql_quantity) === TRUE) {
    echo "✅ Column 'quantity' added successfully!<br>";
}

echo "<br>🎉 Setup complete!";

$conn->close();
?>
