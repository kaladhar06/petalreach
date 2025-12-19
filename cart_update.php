<?php
// cart_update.php
require_once __DIR__ . '/db.php';
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$cart_item_id = isset($input['cart_item_id']) ? (int)$input['cart_item_id'] : 0;
$quantity = isset($input['quantity']) ? (float)$input['quantity'] : null;
$user_id = isset($input['user_id']) ? (int)$input['user_id'] : null;

if ($cart_item_id <= 0 || $quantity === null || $quantity < 0) respond(['success'=>false,'error'=>'INVALID_INPUT'],400);

$cart_id = get_or_create_cart($pdo, $user_id);
$stmt = $pdo->prepare("SELECT ci.id, ci.product_id, p.stock_qty FROM cart_items ci JOIN products p ON p.id = ci.product_id WHERE ci.id = :id AND ci.cart_id = :cid LIMIT 1");
$stmt->execute([':id'=>$cart_item_id, ':cid'=>$cart_id]);
$row = $stmt->fetch();
if (!$row) respond(['success'=>false,'error'=>'CART_ITEM_NOT_FOUND'],404);

if ($quantity == 0) {
    $del = $pdo->prepare("DELETE FROM cart_items WHERE id = :id");
    $del->execute([':id'=>$cart_item_id]);
    $cart = fetch_cart($pdo, $cart_id);
    respond(['success'=>true,'message'=>'REMOVED','cart'=>$cart]);
}

if (is_numeric($row['stock_qty']) && $row['stock_qty'] < $quantity) respond(['success'=>false,'error'=>'INSUFFICIENT_STOCK'],400);

$upd = $pdo->prepare("UPDATE cart_items SET quantity = :q WHERE id = :id");
$upd->execute([':q'=>$quantity, ':id'=>$cart_item_id]);
$cart = fetch_cart($pdo, $cart_id);
respond(['success'=>true,'message'=>'UPDATED','cart'=>$cart]);
