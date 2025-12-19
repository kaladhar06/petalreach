<?php
// flower.php
// Minimal backend: store stock_status, contact_phone, media, status (draft/published).
// Single-file: creates table if missing. Returns JSON only.

// ---- CONFIG ----
$dbHost = '127.0.0.1';
$dbName = 'petalreach';
$dbUser = 'root';
$dbPass = '';
$uploadBaseDir = __DIR__ . '/uploads/flowers'; // ensure writable

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);

// ---- CONNECT ----
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

// ---- ENSURE TABLE (minimal fields) ----
try {
    $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `flower` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `stock_status` TINYINT(1) NOT NULL DEFAULT 1,
  `contact_phone` VARCHAR(30) DEFAULT NULL,
  `media` JSON DEFAULT NULL,
  `status` ENUM('draft','published') NOT NULL DEFAULT 'draft',
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

// ---- Helpers ----
function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
function is_image_mime($m) {
    return in_array($m, ['image/jpeg','image/png','image/webp','image/jpg']);
}
function is_video_mime($m) {
    return in_array($m, ['video/mp4','video/quicktime','video/x-msvideo']);
}

// ---- Parse input ----
// Query action: ?action=draft or ?action=publish optional. If none, script checks JSON 'status' or defaults to 'draft'.
$action = strtolower($_GET['action'] ?? '');
$raw = file_get_contents('php://input');
$json = json_decode($raw, true);
$input = is_array($json) ? $json : $_POST;

// stock_status: boolean/0-1, contact_phone: string
$stock_status = isset($input['stock_status']) ? (int)$input['stock_status'] : null;
if ($stock_status !== 0 && $stock_status !== 1) {
    // allow 'on' from form
    if (isset($input['stock_status']) && ($input['stock_status'] === 'on' || $input['stock_status'] === 'true')) $stock_status = 1;
}

// contact phone
$contact_phone = trim($input['contact_phone'] ?? '');

// status determination
if ($action === 'publish') $status = 'published';
elseif ($action === 'draft') $status = 'draft';
else $status = in_array(($input['status'] ?? ''), ['draft','published']) ? $input['status'] : 'draft';

// If no explicit stock_status provided, default to 1 (available)
if ($stock_status === null) $stock_status = 1;

// ---- Basic validation ----
$errors = [];
if ($contact_phone !== '' && !preg_match('/^[0-9+\-\s]{6,30}$/', $contact_phone)) {
    $errors[] = 'Invalid contact_phone';
}

// If multipart files are present, they will be in $_FILES
$media_list = [];

// ---- If files provided (multipart/form-data), handle validations first ----
if (!empty($_FILES)) {
    // collect all uploaded files entries
    $allFiles = [];
    foreach ($_FILES as $f) {
        if (is_array($f['name'])) {
            for ($i = 0; $i < count($f['name']); $i++) {
                $allFiles[] = [
                    'name' => $f['name'][$i],
                    'type' => $f['type'][$i],
                    'tmp_name' => $f['tmp_name'][$i],
                    'error' => $f['error'][$i],
                    'size' => $f['size'][$i]
                ];
            }
        } else {
            $allFiles[] = $f;
        }
    }
    $imgCount = 0; $vidCount = 0;
    foreach ($allFiles as $f) {
        if ($f['error'] !== UPLOAD_ERR_OK) continue;
        $mime = $f['type'] ?? mime_content_type($f['tmp_name']);
        if (is_image_mime($mime)) $imgCount++;
        elseif (is_video_mime($mime)) $vidCount++;
        else $errors[] = 'Unsupported file type: ' . ($f['name'] ?? 'file');
    }
    if ($imgCount > 3) $errors[] = 'Maximum 3 images allowed';
    if ($vidCount > 1) $errors[] = 'Maximum 1 video allowed';
}

if (!empty($errors)) respond(['success'=>false,'errors'=>$errors], 400);

// ---- Insert DB record minimal (media NULL for now) ----
try {
    $stmt = $pdo->prepare("INSERT INTO flower (stock_status, contact_phone, media, status) VALUES (:stock_status, :contact_phone, :media, :status)");
    $stmt->execute([
        ':stock_status' => $stock_status ? 1 : 0,
        ':contact_phone' => $contact_phone !== '' ? $contact_phone : null,
        ':media' => null,
        ':status' => $status
    ]);
    $id = (int)$pdo->lastInsertId();
} catch (PDOException $e) {
    respond(['success'=>false,'error'=>'DB_INSERT_FAILED'],500);
}

// ---- If files were uploaded, move them and update media JSON ----
if (!empty($_FILES)) {
    $uploadDir = rtrim($uploadBaseDir, '/') . '/' . $id;
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            respond(['success'=>false,'error'=>'UPLOAD_DIR_FAILED'],500);
        }
    }

    $allFiles = [];
    foreach ($_FILES as $f) {
        if (is_array($f['name'])) {
            for ($i = 0; $i < count($f['name']); $i++) {
                $allFiles[] = [
                    'name' => $f['name'][$i],
                    'type' => $f['type'][$i],
                    'tmp_name' => $f['tmp_name'][$i],
                    'error' => $f['error'][$i],
                    'size' => $f['size'][$i]
                ];
            }
        } else {
            $allFiles[] = $f;
        }
    }

    foreach ($allFiles as $f) {
        if ($f['error'] !== UPLOAD_ERR_OK) continue;
        $mime = $f['type'] ?? mime_content_type($f['tmp_name']);
        $ext = pathinfo($f['name'], PATHINFO_EXTENSION) ?: '';
        // safe random name
        $basename = bin2hex(random_bytes(8)) . ($ext ? ('.' . preg_replace('/[^a-zA-Z0-9]/','', $ext)) : '');
        $dest = $uploadDir . '/' . $basename;
        if (!move_uploaded_file($f['tmp_name'], $dest)) continue;
        $webPath = 'uploads/flowers/' . $id . '/' . $basename;
        if (is_image_mime($mime)) $media_list[] = ['type'=>'image','file'=>$webPath];
        elseif (is_video_mime($mime)) $media_list[] = ['type'=>'video','file'=>$webPath];
        else $media_list[] = ['type'=>'file','file'=>$webPath];
    }

    // update DB media column (JSON)
    try {
        $upd = $pdo->prepare("UPDATE flower SET media = :media WHERE id = :id");
        $upd->execute([':media' => empty($media_list) ? null : json_encode($media_list, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), ':id' => $id]);
    } catch (PDOException $e) {
        respond(['success'=>false,'error'=>'DB_MEDIA_UPDATE_FAILED'],500);
    }
}

// ---- Final response ----
$resp = [
    'success' => true,
    'id' => $id,
    'stock_status' => (bool)$stock_status,
    'contact_phone' => $contact_phone !== '' ? $contact_phone : null,
    'status' => $status,
    'media' => $media_list
];
respond($resp, 201);
