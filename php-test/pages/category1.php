<?php
session_start();


$servername = 'sci-project.lboro.ac.uk';
$dbname     = '295group5';
$username   = '295group5';
$password   = 'becvUgUxpXMijnWviR7h';
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get category (empty means all)
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';

// Base SQL with finish filter
$sql = "
SELECT i.*, img.image, img.mimeType
FROM iBayItems AS i
LEFT JOIN iBayImages2 AS img ON i.itemId = img.itemId
WHERE (i.finish = '0000-00-00 00:00:00' OR i.finish > NOW())";

// Categories filter
if ($category !== '') {
    if ($category === 'Electronics') {
        $sql .= " AND i.category IN ('Electronics','Computing')";
    } elseif ($category === 'Miscellaneous') {
        $excluded = ['Electronics','Computing','Clothing','Book','Furniture','Toy'];
        $sql .= " AND i.category NOT IN ('" . implode("','", $excluded) . "')";
    } else {
        $sql .= " AND i.category = '$category'";
    }
}

$sql .= "\nGROUP BY i.itemId
ORDER BY i.start DESC
LIMIT 50";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "<p class='no-results'>Query error: " . mysqli_error($conn) . "</p>";
    exit;
}

if (mysqli_num_rows($result) === 0) {
    $label = $category === '' ? 'all categories' : htmlspecialchars($category);
    echo "<p class='no-results'>No products in {$label}.</p>";
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<a href='product_details.php?id={$row['itemId']}' class='product-link'>";
        echo "<div class='product-item'>";
        if (!empty($row['image']) && !empty($row['mimeType'])) {
            $data = base64_encode($row['image']);
            echo "<img src='data:{$row['mimeType']};base64,{$data}' class='product-image'>";
        } else {
            echo "<img src='placeholder.jpg' class='product-image'>";
        }
        echo "<div class='product-info'>";
        echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
        echo "<p>£" . number_format($row['price'], 2) . " + " . htmlspecialchars($row['postage']) . "</p>";
        echo "</div></div></a>";
    }
}
mysqli_close($conn);
?>
