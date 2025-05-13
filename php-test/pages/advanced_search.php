<?php
session_start();
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');

$servername = 'sci-project.lboro.ac.uk';
$dbname = '295group5';
$username = '295group5';
$password = 'becvUgUxpXMijnWviR7h';

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve and sanitize inputs
$query      = isset($_GET['query'])     ? mysqli_real_escape_string($conn, $_GET['query'])     : '';
$category   = isset($_GET['category'])  ? mysqli_real_escape_string($conn, $_GET['category'])  : '';
$min_price  = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : 0;
$max_price  = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : PHP_INT_MAX;

// Base SQL without finish filter
$sql = "SELECT 
    iBayItems.*, 
    iBayImages2.image, 
    iBayImages2.mimeType 
FROM iBayItems 
LEFT JOIN iBayImages2 ON iBayItems.itemId = iBayImages2.itemId";

$conditions = [];
$params     = [];
$types      = '';

if (!empty($query)) {
    $conditions[] = "(title LIKE ? OR description LIKE ?)";
    $params[] = "%$query%";
    $params[] = "%$query%";
    $types   .= 'ss';
}
if (!empty($category)) {
    $conditions[] = "category = ?";
    $params[] = $category;
    $types   .= 's';
}
if ($min_price > 0) {
    $conditions[] = "price >= ?";
    $params[] = $min_price;
    $types   .= 'd';
}
if ($max_price < PHP_INT_MAX) {
    $conditions[] = "price <= ?";
    $params[] = $max_price;
    $types   .= 'd';
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}
$sql .= " GROUP BY iBayItems.itemId";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("SQL Prepare Error: " . mysqli_error($conn));
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Output HTML for AJAX response
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<a href='product_details.php?id=" . $row['itemId'] . "' class='product-link'>";
        echo "<div class='product-item'>";
        if (!empty($row['image']) && !empty($row['mimeType'])) {
            $imageData = base64_encode($row['image']);
            $imageMime = $row['mimeType'];
            echo "<img src='data:$imageMime;base64,$imageData' alt='Product Image' class='product-image'>";
        } else {
            echo "<img src='default_image.png' alt='No Image' class='product-image'>"; // Use a placeholder
        }
        echo "<div class='product-info'>";
        echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
        echo "<p>£" . number_format($row['price'], 2) . " + " . htmlspecialchars($row['postage']) . "</p>";
        echo "</div></div>";
        echo "</a>";
    }
} else {
    echo "<p class='no-results'>No items match your search criteria.</p>";
}

mysqli_close($conn);
?>
