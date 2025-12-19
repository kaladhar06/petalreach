<?php
// signup.php (fixed) — JSON-only response, uses table `signup`
header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false, 'errors' => [], 'message' => ''];

// === DB config - change to your settings ===
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'petalreach';

// === Read input (JSON if provided, otherwise use $_POST) ===
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) {
    // Not valid JSON, fallback to $_POST
    $input = $_POST;
}

// Use null-coalescing to avoid undefined index warnings
$full_name = trim($input['full_name'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$confirm_password = $input['confirm_password'] ?? '';

// === Basic validation ===
if ($full_name === '') {
    $response['errors'][] = 'Full name is required.';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['errors'][] = 'A valid email is required.';
}
if ($password === '') {
    $response['errors'][] = 'Password is required.';
}
if ($password !== $confirm_password) {
    $response['errors'][] = 'Passwords do not match.';
}
if ($password !== '' && strlen($password) < 8) {
    $response['errors'][] = 'Password must be at least 8 characters long.';
}

if (!empty($response['errors'])) {
    http_response_code(400);
    $response['message'] = 'Validation failed.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}

// === Connect to DB using mysqli ===
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    http_response_code(500);
    $response['message'] = 'Database connection failed.';
    $response['errors'][] = $mysqli->connect_error;
    echo json_encode($response, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}
$mysqli->set_charset('utf8mb4');

// === Check duplicate email (table name: signup) ===
$query = "SELECT id FROM signup WHERE email = ? LIMIT 1";
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    http_response_code(500);
    $response['message'] = 'DB error preparing statement.';
    $response['errors'][] = $mysqli->error;
    $mysqli->close();
    echo json_encode($response, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}

$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    http_response_code(409);
    $response['message'] = 'Email already registered.';
    $stmt->close();
    $mysqli->close();
    echo json_encode($response, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}
$stmt->close();

// === Hash password and insert into signup table ===
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$insertQuery = "INSERT INTO signup (full_name, email, password_hash, created_at) VALUES (?, ?, ?, NOW())";
$stmt = $mysqli->prepare($insertQuery);
if ($stmt === false) {
    http_response_code(500);
    $response['message'] = 'DB error preparing insert.';
    $response['errors'][] = $mysqli->error;
    $mysqli->close();
    echo json_encode($response, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}

$stmt->bind_param('sss', $full_name, $email, $password_hash);
if ($stmt->execute()) {
    http_response_code(201);
    $response['success'] = true;
    $response['message'] = 'Account created successfully.';
    // mysqli->insert_id contains last inserted id
    $response['user_id'] = $mysqli->insert_id;
} else {
    http_response_code(500);
    $response['message'] = 'Failed to create account.';
    $response['errors'][] = $stmt->error;
}
$stmt->close();

$mysqli->close();
echo json_encode($response, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
exit;
