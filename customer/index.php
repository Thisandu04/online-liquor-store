<?php
// customer/index.php — Catalog with search/filter
session_start();

if (!isset($_SESSION['age_verified']) || $_SESSION['age_verified'] !== true) {
    header("Location: ../index.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

$search = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';

$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND p.name LIKE ?";
    $params[] = '%' . $search . '%';
}

if ($category !== '' && is_numeric($category)) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category;
}

$sql .= " ORDER BY p.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$catStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $catStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Online Liquor Store - Catalog</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <h1>Online Liquor Store</h1>
        <nav>
            <a href="checkout.php">Cart / Checkout</a>
        </nav>
    </header>

    <div class="filter-bar">
        <form method="GET" action="index.php" id="filterForm">
            <input type="text" name="search" placeholder="Search by name..." value="<?= htmlspecialchars($search) ?>">
            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= htmlspecialchars($c['id']) ?>" 
                        <?= $category == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>

    <div class="product-grid">
        <?php if (empty($products)): ?>
            <p>No products found.</p>
        <?php endif; ?>

        <?php foreach ($products as $p): ?>
        <div class="product-card">
            <img src="../<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <p class="brand"><?= htmlspecialchars($p['brand']) ?></p>
            <p><?= htmlspecialchars($p['volume_ml']) ?>ml &middot; ABV <?= htmlspecialchars($p['abv']) ?>%</p>
            <p class="category"><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></p>
            <p class="price">LKR <?= htmlspecialchars(number_format($p['price'], 2)) ?></p>
            <?php if ($p['stock'] > 0): ?>
                <a href="checkout.php?product_id=<?= urlencode($p['id']) ?>" class="btn-buy">Buy Now</a>
            <?php else: ?>
                <p class="out-of-stock">Out of Stock</p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <script src="../assets/js/search-filter.js"></script>
</body>
</html>