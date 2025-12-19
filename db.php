<?php
// db.php - shared DB connection and helpers
header('Content-Type: application/json; charset=utf-8');

$DB_HOST = '127.0.0.1';
$DB_NAME = 'petalreach';
$DB_USER = 'root';
$DB_PASS = '';
$DELIVERY_FEE = 50.00; // change if needed

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'DB_CONNECTION_FAILED']);
    exit;
}

session_start();

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function get_or_create_cart(PDO $pdo, $user_id = null) {
    $session_id = session_id();
    if ($user_id) {
        $stmt = $pdo->prepare("SELECT id FROM carts WHERE user_id = :uid LIMIT 1");
        $stmt->execute([':uid'=>$user_id]);
        $r = $stmt->fetch();
        if ($r) return (int)$r['id'];
        $stmt = $pdo->prepare("INSERT INTO carts (user_id, session_id) VALUES (:uid, :sid)");
        $stmt->execute([':uid'=>$user_id, ':sid'=>$session_id]);
        return (int)$pdo->lastInsertId();
    } else {
        $stmt = $pdo->prepare("SELECT id FROM carts WHERE session_id = :sid LIMIT 1");
        $stmt->execute([':sid'=>$session_id]);
        $r = $stmt->fetch();
        if ($r) return (int)$r['id'];
        $stmt = $pdo->prepare("INSERT INTO carts (session_id) VALUES (:sid)");
        $stmt->execute([':sid'=>$session_id]);
        return (int)$pdo->lastInsertId();
    }
}

function fetch_cart(PDO $pdo, $cart_id) {
    global $DELIVERY_FEE;
    $stmt = $pdo->prepare("
        SELECT ci.id AS cart_item_id, p.id AS product_id, p.name, p.unit, ci.quantity, ci.price_at_add
        FROM cart_items ci
        JOIN products p ON p.id = ci.product_id
        WHERE ci.cart_id = :cart_id
    ");
    $stmt->execute([':cart_id'=>$cart_id]);
    $rows = $stmt->fetchAll();
    $items = [];
    $subtotal = 0.0;
    foreach ($rows as $r) {
        $line = round((float)$r['price_at_add'] * (float)$r['quantity'], 2);
        $subtotal += $line;
        $items[] = [
            'cart_item_id' => (int)$r['cart_item_id'],
            'product_id' => (int)$r['product_id'],
            'name' => $r['name'],
            'unit' => $r['unit'],
            'price' => (float)$r['price_at_add'],
            'quantity' => (float)$r['quantity'],
            'line_total' => $line
        ];
    }
    $delivery = $subtotal > 0 ? $DELIVERY_FEE : 0.0;
    $total = round($subtotal + $delivery, 2);
    return ['items'=>$items,'subtotal'=>round($subtotal,2),'delivery_fee'=>round($delivery,2),'total'=>$total];
}
