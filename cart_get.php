<?php
// cart_get.php
require_once __DIR__ . '/db.php';

$inputUser = isset($_GET['user_id']) ? (int)$_GET['user_id'] : (isset($_POST['user_id']) ? (int)$_POST['user_id'] : null);
$cart_id = get_or_create_cart($pdo, $inputUser);
$cart = fetch_cart($pdo, $cart_id);
respond(['success'=>true,'cart'=>$cart]);
