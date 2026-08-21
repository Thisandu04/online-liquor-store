<?php

session_start();

if (!isset($_SESSION['age_verified']) || $_SESSION['age_verified'] !== true) {
    header("Location: ../index.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

$product_id = $_GET['product_id'] ?? null;
$product = null;

if ($product_id && is_numeric($product_id)) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
}

if (!$product) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout - Online Liquor Store</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <h1>Checkout</h1>
        <nav>
            <a href="index.php">Back to Catalog</a>
        </nav>
    </header>

    <div class="admin-content">
        <div class="product-card" style="max-width:350px; margin-bottom:20px;">
            <img src="../<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p class="brand"><?= htmlspecialchars($product['brand']) ?></p>
            <p><?= htmlspecialchars($product['volume_ml']) ?>ml &middot; ABV <?= htmlspecialchars($product['abv']) ?>%</p>
            <p class="price">LKR <?= htmlspecialchars(number_format($product['price'], 2)) ?> each</p>
            <p>In stock: <?= htmlspecialchars($product['stock']) ?></p>
        </div>

        <form method="POST" action="process_order.php" id="checkoutForm">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

            <label for="customer_name">Full Name</label>
            <input type="text" id="customer_name" name="customer_name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" required>

            <label for="address">Delivery Address</label>
            <textarea id="address" name="address" rows="2" required></textarea>

            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" min="1" max="<?= htmlspecialchars($product['stock']) ?>" value="1" required>

            <label for="payment_method">Payment Method</label>
            <select id="payment_method" name="payment_method" required>
                <option value="Card">Credit / Debit Card</option>
                <option value="COD">Cash on Delivery</option>
            </select>

            <div id="cardFields">
                <label for="card_number">Card Number</label>
                <input type="text" id="card_number" name="card_number" maxlength="16" placeholder="1234 5678 9012 3456">

                <label for="card_expiry">Expiry (MM/YY)</label>
                <input type="text" id="card_expiry" name="card_expiry" maxlength="5" placeholder="MM/YY">

                <label for="card_cvv">CVV</label>
                <input type="text" id="card_cvv" name="card_cvv" maxlength="3" placeholder="123">
            </div>

            <button type="submit">Place Order</button>
        </form>
    </div>

    <script src="../assets/js/checkout.js"></script>
</body>
</html>