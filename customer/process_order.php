<?php

session_start();

if (!isset($_SESSION['age_verified']) || $_SESSION['age_verified'] !== true) {
    header("Location: ../index.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$product_id = $_POST['product_id'] ?? null;
$customer_name = trim($_POST['customer_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$quantity = $_POST['quantity'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';

$errors = [];

if (!$product_id || !is_numeric($product_id)) {
    $errors[] = 'Invalid product.';
}
if ($customer_name === '') {
    $errors[] = 'Name is required.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required.';
}
if ($phone === '') {
    $errors[] = 'Phone number is required.';
}
if ($address === '') {
    $errors[] = 'Address is required.';
}
if (!is_numeric($quantity) || $quantity < 1) {
    $errors[] = 'Invalid quantity.';
}
if (!in_array($payment_method, ['Card', 'COD'], true)) {
    $errors[] = 'Invalid payment method.';
}

if (!empty($errors)) {
    die('Order failed: ' . htmlspecialchars(implode(' ', $errors)));
}

// --- Fetch authoritative product data fresh from DB (never trust client price) ---
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    die('Order failed: Product not found.');
}

$quantity = (int) $quantity;

if ($quantity > $product['stock']) {
    die('Order failed: Not enough stock available.');
}

// Server calculates the real total — client can never influence this
$total_price = $product['price'] * $quantity;

// Mock payment simulation — in a real gateway this would call an external API
$payment_status = 'Paid'; // simulate a successful payment
if ($payment_method === 'COD') {
    $payment_status = 'Pending';
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO orders 
        (customer_name, email, phone, address, product_id, quantity, total_price, payment_method, payment_status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $customer_name, $email, $phone, $address, $product_id, $quantity, $total_price, $payment_method, $payment_status
    ]);

    $updateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    $updateStock->execute([$quantity, $product_id]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    die('Order failed: Please try again.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Confirmation</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="age-verify-container">
        <h2>Order Placed Successfully!</h2>
        <p>Thank you, <?= htmlspecialchars($customer_name) ?>.</p>
        <p>You ordered <?= htmlspecialchars($quantity) ?> x <?= htmlspecialchars($product['name']) ?></p>
        <p class="price">Total: LKR <?= htmlspecialchars(number_format($total_price, 2)) ?></p>
        <p>Payment Status: <?= htmlspecialchars($payment_status) ?></p>
        <a href="index.php" class="btn-buy">Continue Shopping</a>
    </div>
</body>
</html>