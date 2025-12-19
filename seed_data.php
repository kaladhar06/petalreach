<?php
// seed_data.php
require_once 'config.php';

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Insert sample sellers
    $pdo->exec("
        INSERT INTO sellers (name, phone, address) VALUES
        ('Ramesh Kumar', '9999999999', 'Bangalore'),
        ('Anita Sharma', '8888888888', 'Bangalore')
    ;");

    // Insert sample flowers (lat/lon approximate for Bangalore)
    $stmt = $pdo->prepare("
      INSERT INTO flowers (seller_id, name, price, in_stock, stock_qty, lat, lon)
      VALUES (:seller_id, :name, :price, :in_stock, :stock_qty, :lat, :lon)
    ");

    $samples = [
      [1, 'Sunflower', 350.00, 1, 100, 12.9716, 77.5946],
      [1, 'Premium Red', 450.00, 1, 50, 12.9750, 77.5990],
      [2, 'White Rose', 400.00, 1, 80, 12.9650, 77.5900],
      [2, 'Red Carnation', 500.00, 0, 0, 12.9800, 77.6000]  // out of stock example
    ];

    foreach ($samples as $s) {
        $stmt->execute([
            ':seller_id' => $s[0],
            ':name' => $s[1],
            ':price' => $s[2],
            ':in_stock' => $s[3],
            ':stock_qty' => $s[4],
            ':lat' => $s[5],
            ':lon' => $s[6]
        ]);
    }

    // Insert some reviews and update aggregates
    $pdo->beginTransaction();

    $pdo->exec("
      INSERT INTO reviews (flower_id, rating, comment) VALUES
      (1, 5, 'Great!'),
      (1, 4, 'Nice'),
      (2, 5, 'Excellent!'),
      (3, 5, 'Lovely'),
      (3, 4, 'Good')
    ");

    // Recompute aggregates for all flowers (simple approach)
    $updateStmt = $pdo->prepare("
      UPDATE flowers f
      LEFT JOIN (
        SELECT flower_id, COALESCE(SUM(rating),0) as sum_rating, COUNT(*) as cnt
        FROM reviews
        GROUP BY flower_id
      ) t ON t.flower_id = f.id
      SET f.rating_sum = COALESCE(t.sum_rating,0), f.rating_count = COALESCE(t.cnt,0)
    ");
    $updateStmt->execute();

    $pdo->commit();

    echo "Sample data seeded.\n";

} catch (PDOException $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
