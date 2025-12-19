<?php
// checkout.php (debug-friendly)
require_once __DIR__ . '/db.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

// allow optional cart_id for testing
$cart_id_override = isset($input['cart_id']) ? (int)$input['cart_id'] : 0;
$payment_method = strtoupper(trim($input['payment_method'] ?? ''));
$user_id = isset($input['user_id']) ? (int)$input['user_id'] : null;
$valid_methods = ['UPI','CARD','COD'];

if (!in_array($payment_method, $valid_methods)) respond(['success'=>false,'error'=>'INVALID_PAYMENT_METHOD'],400);

// determine cart id: prefer explicit cart_id, else use get_or_create_cart
if ($cart_id_override > 0) {
    $cart_id = $cart_id_override;
} else {
    $cart_id = get_or_create_cart($pdo, $user_id);
}

// fetch cart and return it in debug if empty
$cart = fetch_cart($pdo, $cart_id);
if (empty($cart['items'])) {
    // helpful debug output
    respond([
        'success' => false,
        'error' => 'CART_EMPTY',
        'debug' => [
            'cart_id' => $cart_id,
            'user_id' => $user_id,
            'session_id' => session_id(),
            'cart' => $cart
        ]
    ], 400);
}

$subtotal = (float)$cart['subtotal'];
$delivery_fee = (float)$cart['delivery_fee'];
$total = (float)$cart['total'];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, payment_method, subtotal, delivery_fee, total_amount, status)
                           VALUES (:uid,:pm,:sub,:del,:tot,'pending')");
    $stmt->execute([':uid'=>$user_id ?: null, ':pm'=>$payment_method, ':sub'=>$subtotal, ':del'=>$delivery_fee, ':tot'=>$total]);
    $order_id = (int)$pdo->lastInsertId();

    $insItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price)
                              VALUES (:oid,:pid,:name,:qty,:price)");

    $stmt = $pdo->prepare("SELECT ci.product_id, p.name AS product_name, ci.quantity, ci.price_at_add
                           FROM cart_items ci
                           JOIN products p ON p.id = ci.product_id
                           WHERE ci.cart_id = :cid");
    $stmt->execute([':cid'=>$cart_id]);
    $rows = $stmt->fetchAll();
    foreach ($rows as $r) {
        $insItem->execute([':oid'=>$order_id, ':pid'=>$r['product_id'], ':name'=>$r['product_name'], ':qty'=>$r['quantity'], ':price'=>$r['price_at_add']]);
    }

    // clear cart
    $del = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = :cid");
    $del->execute([':cid'=>$cart_id]);

    $pdo->commit();
    respond(['success'=>true,'message'=>'ORDER_CREATED','order_id'=>$order_id,'total'=>$total],201);
} catch (Exception $e) {
    $pdo->rollBack();
    respond(['success'=>false,'error'=>'ORDER_FAILED','message'=>$e->getMessage()],500);
}
