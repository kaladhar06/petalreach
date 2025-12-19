<?php
// add_review.php
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Only POST allowed']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $flowerId = isset($data['flower_id']) ? (int)$data['flower_id'] : null;
    $rating = isset($data['rating']) ? (int)$data['rating'] : null;
    $comment = isset($data['comment']) ? trim($data['comment']) : null;

    if (!$flowerId || !$rating || $rating < 1 || $rating > 5) {
        http_response_code(400);
        echo json_encode(['error' => 'flower_id and rating (1-5) are required']);
        exit;
    }

    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Transactionally insert review and update aggregates
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO reviews (flower_id, rating, comment) VALUES (:flower_id, :rating, :comment)");
    $stmt->execute([':flower_id' => $flowerId, ':rating' => $rating, ':comment' => $comment]);

    // Update aggregate columns
    $upd = $pdo->prepare("UPDATE flowers SET rating_sum = rating_sum + :r, rating_count = rating_count + 1 WHERE id = :id");
    $upd->execute([':r' => $rating, ':id' => $flowerId]);

    $pdo->commit();

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
    exit;
}
