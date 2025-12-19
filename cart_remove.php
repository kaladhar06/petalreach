<?php
// cart_remove.php
require_once __DIR__ . '/db.php';
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$cart_item_id = isset($input['cart_item_id']) ? (int)$input['cart_item_id'] : 0;
$user_id = isset($input['user_id']) ? (int)$input['user_id'] : null;

if ($cart_item_id <= 0) respond(['success'=>false,'error'=>'INVALID_INPUT'],400);

$cart_id = get_or_create_cart($pdo, $user_id);
$del = $pdo->prepare("DELETE FROM cart_items WHERE id = :id AND cart_id = :cid");
$del->execute([':id'=>$cart_item_id, ':cid'=>$cart_id]);
$cart = fetch_cart($pdo, $cart_id);
respond(['success'=>true,'message'=>'REMOVED','cart'=>$cart]);
