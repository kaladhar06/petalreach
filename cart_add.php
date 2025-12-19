<?php
// cart_add.php
require_once __DIR__ . '/db.php';
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$product_id = isset($input['product_id']) ? (int)$input['product_id'] : 0;
$quantity = isset($input['quantity']) ? (float)$input['quantity'] : 1;
$user_id = isset($input['user_id']) ? (int)$input['user_id'] : null;

if ($product_id <= 0 || $quantity <= 0) respond(['success'=>false,'error'=>'INVALID_INPUT'],400);

$stmt = $pdo->prepare("SELECT id, price, stock_qty FROM products WHERE id = :pid LIMIT 1");
$stmt->execute([':pid'=>$product_id]);
$p = $stmt->fetch();
if (!$p) respond(['success'=>false,'error'=>'PRODUCT_NOT_FOUND'],404);
if (is_numeric($p['stock_qty']) && $p['stock_qty'] < $quantity) respond(['success'=>false,'error'=>'INSUFFICIENT_STOCK'],400);

$cart_id = get_or_create_cart($pdo, $user_id);

// check existing item
$stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = :cid AND product_id = :pid LIMIT 1");
$stmt->execute([':cid'=>$cart_id,':pid'=>$product_id]);
$ex = $stmt->fetch();
if ($ex) {
    $newQty = (float)$ex['quantity'] + $quantity;
    $upd = $pdo->prepare("UPDATE cart_items SET quantity = :q WHERE id = :id");
    $upd->execute([':q'=>$newQty, ':id'=>$ex['id']]);
    respond(['success'=>true,'message'=>'UPDATED_QUANTITY','cart_item_id'=>(int)$ex['id']]);
} else {
    $ins = $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity, price_at_add) VALUES (:cid,:pid,:qty,:price)");
    $ins->execute([':cid'=>$cart_id,':pid'=>$product_id,':qty'=>$quantity,':price'=>$p['price']]);
    respond(['success'=>true,'message'=>'ADDED','cart_item_id'=>(int)$pdo->lastInsertId()]);
}
