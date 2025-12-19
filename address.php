<?php
// address.php
// JSON-only API: create address rows in `address` table.
// Edit DB credentials below.

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);

// ---------- DB CONFIG ----------
$dbHost = '127.0.0.1';
$dbName = 'petalreach';
$dbUser = 'root';
$dbPass = '';

// ---------- CONNECT ----------
try {
    $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB_CONNECTION_FAILED']);
    exit;
}

// ---------- ENSURE TABLE ----------
try {
    $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `address` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(255) NOT NULL,
  `phone_number` VARCHAR(30) NOT NULL,
  `street_address` VARCHAR(500) DEFAULT NULL,
  `city` VARCHAR(200) DEFAULT NULL,
  `pincode` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB_TABLE_CREATE_FAILED']);
    exit;
}

// ---------- HELPERS ----------
function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function parse_input() {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (is_array($json)) return $json;
    return $_POST;
}

// ---------- ROUTING: POST to create an address ----------
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'POST') {
    respond(['success' => false, 'error' => 'ONLY_POST_ALLOWED'], 405);
}

$input = parse_input();

// expected fields:
// full_name (required), phone_number (required), street_address (optional), city (optional), pincode (optional)
$full_name = trim($input['full_name'] ?? $input['name'] ?? '');
$phone_number = trim($input['phone_number'] ?? $input['phone'] ?? '');
$street_address = trim($input['street_address'] ?? $input['street'] ?? '');
$city = trim($input['city'] ?? '');
$pincode = trim($input['pincode'] ?? $input['pin'] ?? '');

// validation
$errors = [];
if ($full_name === '') $errors[] = 'full_name is required';
if ($phone_number === '') $errors[] = 'phone_number is required';
if ($phone_number !== '' && !preg_match('/^[0-9+\-\s]{6,30}$/', $phone_number)) $errors[] = 'phone_number invalid (allow digits, +, -, spaces)';

if ($pincode !== '' && !preg_match('/^[0-9A-Za-z\s\-]{3,12}$/', $pincode)) {
    $errors[] = 'pincode looks invalid';
}

if (!empty($errors)) {
    respond(['success' => false, 'errors' => $errors], 400);
}

// insert
try {
    $stmt = $pdo->prepare("INSERT INTO address (full_name, phone_number, street_address, city, pincode) VALUES (:full_name, :phone_number, :street_address, :city, :pincode)");
    $stmt->execute([
        ':full_name' => mb_substr($full_name, 0, 255),
        ':phone_number' => mb_substr($phone_number, 0, 30),
        ':street_address' => $street_address !== '' ? mb_substr($street_address, 0, 500) : null,
        ':city' => $city !== '' ? mb_substr($city, 0, 200) : null,
        ':pincode' => $pincode !== '' ? mb_substr($pincode, 0, 20) : null,
    ]);
    $id = (int)$pdo->lastInsertId();

    respond([
        'success' => true,
        'message' => 'Address saved',
        'id' => $id,
        'data' => [
            'full_name' => $full_name,
            'phone_number' => $phone_number,
            'street_address' => $street_address,
            'city' => $city,
            'pincode' => $pincode
        ]
    ], 201);
} catch (PDOException $e) {
    respond(['success' => false, 'error' => 'DB_INSERT_FAILED'], 500);
}
