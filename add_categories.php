<?php 
session_start();

// Redirect if not logged in
if (!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

require_once 'db.php'; // Corrected syntax

$errors = [];
$success = "";
$is_edit = false;
$category = [
    'id' => '',
    'name' => '',
    'description' => ''
];

// ── HANDLE EDIT: Fetch existing data if ID is provided ──────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $res = mysqli_query($conn, "SELECT * FROM categories WHERE id = $id");
    if ($row = mysqli_fetch_assoc($res)) {
        $is_edit = true;
        $category = $row;
    }
}

// ── HANDLE DELETE ───────────────────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // Check if products are using this category first
    $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products WHERE category_id = $id"));
    
    if ($check['c'] > 0) {
        $_SESSION['error'] = "Cannot delete: {$check['c']} products are linked to this category.";
    } else {
        mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
        $_SESSION['success'] = "Category deleted successfully.";
    }
    header("Location: categories.php");
    exit();
}

// ── HANDLE POST: Add or Update ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $cat_id = $_POST['category_id'] ?? '';

    if (empty($name)) {
        $errors[] = "Category name is required.";
    }

    if (empty($errors)) {
        if (!empty($cat_id)) {
            // UPDATE existing
            $stmt = mysqli_prepare($conn, "UPDATE categories SET name = ?, description = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $cat_id);
        } else {
            // INSERT new
            $stmt = mysqli_prepare($conn, "INSERT INTO categories (name, description) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ss", $name, $description);
        }

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Category saved successfully!";
            header("Location: categories.php");
            exit();
        } else {
            $errors[] = "Error saving category. It might already exist.";
        }
    }
}

// ── FETCH ALL CATEGORIES ────────────────────────────────────────────────────
$categories_result = mysqli_query($conn, "
    SELECT c.*, COUNT(p.id) AS product_count 
    FROM categories c 
    LEFT JOIN products p ON p.category_id = c.id 
    GROUP BY c.id 
    ORDER BY c.created_at DESC
");

$current_page = 'categories';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories — Admin Panel</title>
    <link rel="icon" type="image/x-icon" href="assests/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style> body { background-color: #f0f2f5; } </style>
</head>
<body>

<div class="container-fluid">
    <div class="row flex-nowrap">
        <?php require_once 'includes/sidebar.php'; ?>

        <div class="col py-4 px-4">
            <h4 class="fw-bold mb-4"><i class="bi bi-tags me-2 text-info"></i>Category Management</h4>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success py-2 small"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger py-2 small"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="row g-4">
                
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white fw-semibold py-3">
                            <i class="bi <?= $is_edit ? 'bi-pencil-square' : 'bi-plus-circle' ?> me-2 text-info"></i>
                            <?= $is_edit ? 'Edit Category' : 'Add New Category' ?>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger py-2 small">
                                    <?php foreach ($errors as $e) echo "<div>$e</div>"; ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="categories.php">
                                <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Category Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" 
                                           value="<?= htmlspecialchars($is_edit ? $category['name'] : ($_POST['name'] ?? '')) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Description</label>
                                    <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($is_edit ? $category['description'] : ($_POST['description'] ?? '')) ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-info w-100 text-white">
                                    <?= $is_edit ? 'Update Category' : 'Add Category' ?>
                                </button>
                                <?php if($is_edit): ?>
                                    <a href="categories.php" class="btn btn-light w-100 mt-2">Cancel</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Handles browser back button / bfcache -->
    <script>
        window.addEventListener('pageshow', function(event) {
            // event.persisted = true means page was loaded from bfcache
            if (event.persisted) {
                window.location.replace('login.php');
            }
        });
    </script>
</body>
</html>