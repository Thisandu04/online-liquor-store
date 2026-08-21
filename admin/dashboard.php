<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$stmt = $pdo->query("SELECT p.*, c.name AS category_name 
                      FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      ORDER BY p.id DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - Online Liquor Store</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-header">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?></h2>
        <nav>
            <a href="dashboard.php">Inventory</a>
            <a href="orders.php">Orders</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>

    <div class="admin-content">
        <h3>Inventory Management</h3>
        <a href="add_product.php" class="btn-add">+ Add New Product</a>

        <table class="admin-table">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Volume (ml)</th>
                <th>ABV %</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['id']) ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['brand']) ?></td>
                <td><?= htmlspecialchars($p['category_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($p['volume_ml']) ?></td>
                <td><?= htmlspecialchars($p['abv']) ?></td>
                <td><?= htmlspecialchars(number_format($p['price'], 2)) ?></td>
                <td><?= htmlspecialchars($p['stock']) ?></td>
                <td>
                    <a href="edit_product.php?id=<?= urlencode($p['id']) ?>">Edit</a>
                    <a href="delete_product.php?id=<?= urlencode($p['id']) ?>" 
                       onclick="return confirm('Delete this product?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>