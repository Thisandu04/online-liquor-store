<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT o.*, p.name AS product_name 
        FROM orders o 
        LEFT JOIN products p ON o.product_id = p.id 
        WHERE 1=1";
$params = [];

if ($statusFilter !== '' && in_array($statusFilter, ['Pending', 'Paid', 'Failed'], true)) {
    $sql .= " AND o.payment_status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY o.order_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Orders - Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-header">
        <h2>Order Management</h2>
        <nav>
            <a href="dashboard.php">Inventory</a>
            <a href="orders.php">Orders</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>

    <div class="admin-content">
        <form method="GET" action="orders.php" style="margin-bottom:15px;">
            <label for="status">Filter by Payment Status</label>
            <select id="status" name="status" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="Pending" <?= $statusFilter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Paid" <?= $statusFilter === 'Paid' ? 'selected' : '' ?>>Paid</option>
                <option value="Failed" <?= $statusFilter === 'Failed' ? 'selected' : '' ?>>Failed</option>
            </select>
        </form>

        <table class="admin-table">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Total (LKR)</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            <?php if (empty($orders)): ?>
                <tr><td colspan="10">No orders found.</td></tr>
            <?php endif; ?>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= htmlspecialchars($o['id']) ?></td>
                <td><?= htmlspecialchars($o['customer_name']) ?></td>
                <td><?= htmlspecialchars($o['email']) ?></td>
                <td><?= htmlspecialchars($o['phone']) ?></td>
                <td><?= htmlspecialchars($o['product_name'] ?? 'Deleted Product') ?></td>
                <td><?= htmlspecialchars($o['quantity']) ?></td>
                <td><?= htmlspecialchars(number_format($o['total_price'], 2)) ?></td>
                <td><?= htmlspecialchars($o['payment_method']) ?></td>
                <td><?= htmlspecialchars($o['payment_status']) ?></td>
                <td><?= htmlspecialchars($o['order_date']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>