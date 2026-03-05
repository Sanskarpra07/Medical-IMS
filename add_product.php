<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
} 

require_once 'db.php';

$errors = [];
$is_edit = false;
$product = [
    'id' => '',
    'product_name' => '',
    'description' => '',
    'price' => '',
    'stock' => '',
    'category_id' => ''
];

// EDIT MODE: Pre-fill form with existing data
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = (int) $_GET['id'];
            $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);

            if (!$product) {
                $_SESSION['error'] = "Product not found.";
                header("Location: products.php");
                exit();
            }
            $is_edit = true;
        }

// Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Read and sanitize inputs
    $product_name = trim($_POST['product_name'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $price        = $_POST['price'] ?? '';
    $stock        = $_POST['stock'] ?? '';
    $category_id  = $_POST['category_id'] ?? null;
    $edit_id      = (int)($_POST['edit_id'] ?? 0);

    // Basic validation
    if (empty($product_name))         $errors[] = "Product name is required.";
    if (!is_numeric($price) || $price < 0) $errors[] = "Enter a valid price.";
    if (!is_numeric($stock) || $stock < 0) $errors[] = "Enter a valid stock quantity.";

    if (empty($errors)) {
        if ($edit_id > 0) {
            // ── UPDATE existing product ──────────────────────────────────
            $stmt = mysqli_prepare($conn,
                "UPDATE products SET product_name=?, description=?, price=?, stock=?, category_id=? WHERE id=?"
            );
            $cat = $category_id ?: null;
            mysqli_stmt_bind_param($stmt, "ssdiii", $product_name, $description, $price, $stock, $cat, $edit_id);
        } else {
            // ── INSERT new product ───────────────────────────────────────
            $stmt = mysqli_prepare($conn,
                "INSERT INTO products (product_name, description, price, stock, category_id) VALUES (?, ?, ?, ?, ?)"
            );
            $cat = $category_id ?: null;
            mysqli_stmt_bind_param($stmt, "ssdii", $product_name, $description, $price, $stock, $cat);
        }

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = $edit_id > 0 ? "Product updated successfully!" : "Product added successfully!";
            header("Location: products.php");
            exit();
        } else {
            $errors[] = "Database error: " . mysqli_error($conn);
        }
    }

    // If there were errors, re-fill the form so the user doesn't lose their input
        $product = [
            'id'           => $_POST['edit_id'] ?? '',
            'product_name' => $product_name,
            'description'  => $description,
            'price'        => $price,
            'stock'        => $stock,
            'category_id'  => $category_id,
        ];
        $is_edit = !empty($product['id']);
    }

    //Fetch categories for dropdown
    $categories_result = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC");

$current_page = 'products';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_edit ? 'Edit' : 'Add' ?> Product — Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style> body { background-color: #f0f2f5; } </style>
</head>
<body>

<div class="container-fluid">
    <div class="row flex-nowrap">

        <?php require_once 'includes/sidebar.php'; ?>

        <div class="col py-4 px-4">

            <!-- Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold mb-0">
                    <i class="bi bi-<?= $is_edit ? 'pencil-square' : 'plus-circle' ?> me-2 text-success"></i>
                    <?= $is_edit ? 'Edit Product' : 'Add New Product' ?>
                </h4>
                <a href="products.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Products
                </a>
            </div>

             <!-- Error messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Product form -->
             <div class="card shadow-sm border-0 rounded-3" style="max-width: 700px;">
                <div class="card-body p-4">
                    <form method="POST" action="add_product.php">

                        <!-- Hidden field to pass the product ID when editing -->
                        <input type="hidden" name="edit_id" value="<?= htmlspecialchars($product['id']) ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="product_name" class="form-control"
                                   value="<?= htmlspecialchars($product['product_name']) ?>"
                                   placeholder="e.g. Paracetamol 500mg" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Short product description..."><?= htmlspecialchars($product['description']) ?></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Price (Rs.) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0"
                                       value="<?= htmlspecialchars($product['price']) ?>"
                                       placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control" min="0"
                                       value="<?= htmlspecialchars($product['stock']) ?>"
                                       placeholder="0" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">— Select a category —</option>
                                <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                                    <option value="<?= $cat['id'] ?>"
                                        <?= ($product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check-circle me-1"></i>
                                <?= $is_edit ? 'Update Product' : 'Add Product' ?>
                            </button>
                            <a href="products.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

