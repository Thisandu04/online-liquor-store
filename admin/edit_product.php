<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$error = '';
$success = '';

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: dashboard.php");
    exit;
}

$catStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $catStmt->fetchAll();

$mockImages = ['beer.jpg', 'wine.jpg', 'whiskey.jpg', 'vodka.jpg', 'default.jpg'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $volume_ml = $_POST['volume_ml'] ?? '';
    $abv = $_POST['abv'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $image = $_POST['image'] ?? 'default.jpg';
    $description = trim($_POST['description'] ?? '');

    if ($name === '' || $brand === '' || $volume_ml === '' || $abv === '' || $price === '' || $stock === '') {
        $error = 'Please fill in all required fields.';
    } elseif (!is_numeric($volume_ml) || !is_numeric($abv) || !is_numeric($price) || !is_numeric($stock)) {
        $error = 'Volume, ABV, price and stock must be numeric.';
    } elseif ($price < 0 || $stock < 0 || $abv < 0 || $abv > 100) {
        $error = 'Please enter valid values (ABV must be 0-100, price and stock cannot be negative).';
    } elseif (!in_array($image, $mockImages, true)) {
        $error = 'Invalid image selection.';
    } else {
        $image_url = 'assets/images/' . $image;
        $stmt = $pdo->prepare("UPDATE products SET 
            name = ?, brand = ?, category_id = ?, volume_ml = ?, abv = ?, 
            price = ?, stock = ?, image_url = ?, description = ? 
            WHERE id = ?");
        $stmt->execute([
            $name, $brand, $category_id ?: null, $volume_ml, $abv, $price, $stock, $image_url, $description, $id
        ]);
        $success = 'Product updated successfully.';
    }
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Product - Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-header">
        <h2>Edit Product</h2>
        <nav>
            <a href="dashboard.php">Back to Inventory</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>

    <div class="admin-content">
        <?php if ($error): ?><p class="error-msg"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success-msg"><?= htmlspecialchars($success) ?></p><?php endif; ?>

        <form method="POST" action="edit_product.php">
            <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">

            <label for="name">Product Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

            <label for="brand">Brand</label>
            <input type="text" id="brand" name="brand" value="<?= htmlspecialchars($product['brand']) ?>" required>

            <label for="category_id">Category</label>
            <select id="category_id" name="category_id">
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= htmlspecialchars($c['id']) ?>" 
                        <?= $c['id'] == $product['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="volume_ml">Volume (ml)</label>
            <input type="number" id="volume_ml" name="volume_ml" min="0" value="<?= htmlspecialchars($product['volume_ml']) ?>" required>

            <label for="abv">ABV (%)</label>
            <input type="number" id="abv" name="abv" step="0.01" min="0" max="100" value="<?= htmlspecialchars($product['abv']) ?>" required>

            <label for="price">Price (LKR)</label>
            <input type="number" id="price" name="price" step="0.01" min="0" value="<?= htmlspecialchars($product['price']) ?>" required>

            <label for="stock">Stock Quantity</label>
            <input type="number" id="stock" name="stock" min="0" value="<?= htmlspecialchars($product['stock']) ?>" required>

            <label for="image">Product Image</label>
            <select id="image" name="image">
                <?php 
                $currentImage = basename($product['image_url']);
                foreach ($mockImages as $img): ?>
                    <option value="<?= htmlspecialchars($img) ?>" <?= $img === $currentImage ? 'selected' : '' ?>>
                        <?= htmlspecialchars($img) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>

            <button type="submit">Update Product</button>
        </form>
    </div>
</body>
</html>