<?php
// addflower.php
// Single-file: create table if missing, accept POST (JSON or form) and insert a row into addflower table.
// Edit DB credentials below before use.

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0); // set 1 for debug locally only

// ---------- DB CONFIG ----------
$dbHost = '127.0.0.1';
$dbName = 'petalreach';
$dbUser = 'root';
$dbPass = '';
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB_CONNECTION_FAILED', 'message' => $e->getMessage()]);
    exit;
}

// ---------- ENSURE TABLE ----------
try {
    $create = <<<SQL
CREATE TABLE IF NOT EXISTS `addflower` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `flower_name` VARCHAR(255) NOT NULL,
  `available_qty_kg` DECIMAL(10,2) DEFAULT 0.00,
  `available_count` INT DEFAULT 0,
  `price_per_kg` DECIMAL(12,2) DEFAULT 0.00,
  `price_for_flowers` DECIMAL(12,2) DEFAULT 0.00,
  `number_of_flowers` INT DEFAULT 0,
  `state` VARCHAR(150) DEFAULT NULL,
  `district` VARCHAR(150) DEFAULT NULL,
  `village` VARCHAR(150) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
    $pdo->exec($create);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB_TABLE_CREATE_FAILED', 'message' => $e->getMessage()]);
    exit;
}

// ---------- INPUT PARSING ----------
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) {
    // fallback to form-encoded POST
    $input = $_POST;
}

// Map and sanitize inputs
$flower_name         = trim($input['flower_name'] ?? $input['flowerName'] ?? '');
$available_qty_kg    = $input['available_qty_kg'] ?? $input['availableQtyKg'] ?? $input['available_quantity_kg'] ?? $input['available_quantity'] ?? null;
$available_count     = $input['available_count'] ?? $input['availableCount'] ?? null;
$price_per_kg        = $input['price_per_kg'] ?? $input['pricePerKg'] ?? null;
$price_for_flowers   = $input['price_for_flowers'] ?? $input['priceForFlowers'] ?? null;
$number_of_flowers   = $input['number_of_flowers'] ?? $input['numberOfFlowers'] ?? null;
$state               = trim($input['state'] ?? '');
$district            = trim($input['district'] ?? '');
$village             = trim($input['village'] ?? $input['village_area'] ?? '');

// ---------- BASIC VALIDATION ----------
$errors = [];

// flower name required
if ($flower_name === '') {
    $errors[] = 'Flower name is required.';
}

// numeric validations (allow empty -> default 0)
if ($available_qty_kg === null || $available_qty_kg === '') {
    $available_qty_kg = 0;
}
if (!is_numeric($available_qty_kg) || $available_qty_kg < 0) {
    $errors[] = 'Available quantity (KG) must be a non-negative number.';
}

if ($available_count === null || $available_count === '') {
    $available_count = 0;
}
if (!ctype_digit(strval($available_count)) || intval($available_count) < 0) {
    $errors[] = 'Available count must be a non-negative integer.';
}

if ($price_per_kg === null || $price_per_kg === '') {
    $price_per_kg = 0;
}
if (!is_numeric($price_per_kg) || $price_per_kg < 0) {
    $errors[] = 'Price per 1 KG must be a non-negative number.';
}

if ($price_for_flowers === null || $price_for_flowers === '') {
    $price_for_flowers = 0;
}
if (!is_numeric($price_for_flowers) || $price_for_flowers < 0) {
    $errors[] = 'Price for flowers must be a non-negative number.';
}

if ($number_of_flowers === null || $number_of_flowers === '') {
    $number_of_flowers = 0;
}
if (!ctype_digit(strval($number_of_flowers)) || intval($number_of_flowers) < 0) {
    $errors[] = 'Number of flowers must be a non-negative integer.';
}

// optional: trim lengths
$flower_name = mb_substr($flower_name, 0, 255);
$state = mb_substr($state, 0, 150);
$district = mb_substr($district, 0, 150);
$village = mb_substr($village, 0, 150);

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// cast numeric values properly
$available_qty_kg  = number_format((float)$available_qty_kg, 2, '.', '');
$available_count   = intval($available_count);
$price_per_kg      = number_format((float)$price_per_kg, 2, '.', '');
$price_for_flowers = number_format((float)$price_for_flowers, 2, '.', '');
$number_of_flowers = intval($number_of_flowers);

// ---------- INSERT ----------
try {
    $sql = "INSERT INTO addflower
        (flower_name, available_qty_kg, available_count, price_per_kg, price_for_flowers, number_of_flowers, state, district, village)
        VALUES (:flower_name, :available_qty_kg, :available_count, :price_per_kg, :price_for_flowers, :number_of_flowers, :state, :district, :village)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':flower_name' => $flower_name,
        ':available_qty_kg' => $available_qty_kg,
        ':available_count' => $available_count,
        ':price_per_kg' => $price_per_kg,
        ':price_for_flowers' => $price_for_flowers,
        ':number_of_flowers' => $number_of_flowers,
        ':state' => $state ?: null,
        ':district' => $district ?: null,
        ':village' => $village ?: null,
    ]);

    $insertId = (int)$pdo->lastInsertId();

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Flower added successfully.',
        'id' => $insertId,
        'data' => [
            'flower_name' => $flower_name,
            'available_qty_kg' => $available_qty_kg,
            'available_count' => $available_count,
            'price_per_kg' => $price_per_kg,
            'price_for_flowers' => $price_for_flowers,
            'number_of_flowers' => $number_of_flowers,
            'state' => $state,
            'district' => $district,
            'village' => $village,
        ]
    ]);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB_INSERT_FAILED', 'message' => $e->getMessage()]);
    exit;
}
