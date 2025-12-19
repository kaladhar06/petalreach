<?php
// cart_clear.php
require_once __DIR__ . '/db.php';
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$user_id = isset($input['user_id']) ? (int)$input['user_id'] : null;

$cart_id = get_or_create_cart($pdo, $user_id);
$del = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = :cid");
$del->execute([':cid'=>$cart_id]);
respond(['success'=>true,'message'=>'CART_CLEARED']);
