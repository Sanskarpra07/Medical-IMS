<?php
// Dislays the error whenever it shows unable to handle request
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// No-cache headers — prevents browser back button from showing cached page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

//Protects Page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
    require_once 'db.php'; // Get $conn

// Fetch stats for cards
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM users"))['c'];
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM products"))['c'];
$total_category = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM categories"))['c'];
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM products WHERE stock<10"));['c'];

// Recent Products
$recent = mysqli_query($conn, 
  "SELECT p.product_name, p.price, p.stock, c.name As category
  From products p
  LEFT JOIN categories c ON p.category_id = c.id
  ORDER BY p.created_at DESC
  LIMIT 5"
);

$current_page = 'dashboard.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
        <style>
        body { background-color: #f0f2f5; }
        .stat-card { border: none; border-radius: 12px; transition: transform .2s; }
        .stat-card:hover { transform: translateY(-4px); }
        .stat-icon { font-size: 2.5rem; opacity: .85; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
        <!-- Sidebar -->
         <?php require_once 'includes/sidebar.php'; ?>

         <!-- Main Contents -->
        <div class="col py-4 px-4">

        <!-- Page Title -->
         <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold mb-0">
                    <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard
                </h4>
        <span class="text-muted small">Welcome back, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> 👋</span>
            </div>
        
        <!-- Stats Card  -->

        <div class="row g-4 mb-4">

        <!-- Total Users for Admin Only -->
        <div class="col-sm-6 col-xl-3">
            <?php if ($_SESSION['role'] === 'admin'): ?>
            <div class="card stat-card bg-primary text-white shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-2 fw-bold"><?= $total_users ?></div>
                        <div class="small">Total Users</div>
                    </div>
                    <i class="bi bi-people stat-icon"></i>
                </div>
                <a href="users.php" class="text-white-50 small mt-2 d-block text-decoration-none">View all →</a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Total Products -->
        <div class="class-sm-6 col-xl-3">
            <div class="card stat-card bg-primary text-white shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-2 fw-bold"><?= $total_products ?></div>
                        <div class="small">Total Products</div>
                    </div>
                        <i class="bi bi-box-seam stat-icon"></i>
                    </div>
                        <a href="products.php" class="text-white-50 small mt-2 d-block text-decoration-none">View all →</a>
            </div>
        </div>
        
        <!-- Total Category -->
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card bg-info text-white shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-2 fw-bold"><?= $total_category ?></div>
                        <div class="small">Categories</div>
                    </div>
                    <i class="bi bi-tags stat-icon"></i>
                </div>
                <a href="categories.php" class="text-white-50 small mt-2 d-block text-decoration-none">View all →</a>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card bg-warning text-dark shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fs-2 fw-bold"><?= $low_stock['c'] ?></div>
                                <div class="small">Low Stock Items</div>
                            </div>
                            <i class="bi bi-exclamation-triangle stat-icon"></i>
                        </div>
                        <span class="text-dark-50 small mt-2 d-block">Stock below 10 units</span>
                    </div>
         </div>

    </div><!-- /row stats -->

<!-- ── Recent Products Table  -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-secondary"></i>Recently Added Products</h6>
                    <a href="products.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while($row = mysqli_fetch_assoc($recent)): ?>
                            <tr>
                                <td class="ps-4"><?= htmlspecialchars($row['product_name']) ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($row['category'] ?? 'Uncategorized') ?></span>
                                </td>
                                <td>Rs. <?= number_format($row['price'], 2) ?></td>
                                <td>
                                    <?php if ($row['stock'] < 10): ?>
                                        <span class="badge bg-danger"><?= $row['stock'] ?> (Low)</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $row['stock'] ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- /main content -->
    </div><!-- /row -->
</div>

<!-- Bootstrap 5 JS -->
<script src="assests/js/bootstrap.bundle.min.js"></script>

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