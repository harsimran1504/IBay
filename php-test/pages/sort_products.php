<?php
session_start();
$servername = 'sci-project.lboro.ac.uk';
$dbname = '295group5';
$username = '295group5';
$password = 'becvUgUxpXMijnWviR7h';

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sort = $_GET['sort'] ?? '';
$query = $_GET['query'] ?? '';

$orderBy = "iBayItems.itemId DESC";
if ($sort === 'price_asc') {
    $orderBy = "iBayItems.price ASC";
} elseif ($sort === 'price_desc') {
    $orderBy = "iBayItems.price DESC";
}

$sql = "SELECT * FROM iBayItems LEFT JOIN iBayImages2 ON iBayItems.itemId = iBayImages2.itemId
        WHERE iBayItems.finish = '0000-00-00 00:00:00'";

if (!empty($query)) {
    $safeQuery = mysqli_real_escape_string($conn, $query);
    $sql .= " AND (iBayItems.title LIKE '%$safeQuery%' OR iBayItems.description LIKE '%$safeQuery%')";
}

$sql .= " ORDER BY $orderBy";

$result = mysqli_query($conn, $sql);
$displayed = [];
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        if (!in_array($row['itemId'], $displayed)) {
            $displayed[] = $row['itemId'];
            echo "<a href='product_details.php?id=" . $row['itemId'] . "' class='product-link'>";
            echo "<div class='product-item'>";
            if (!empty($row['image'])) {
                $imageData = base64_encode($row['image']);
                $imageMime = $row['mimeType'];
                echo "<img src='data:$imageMime;base64,$imageData' alt='Product Image' class='product-image'>";
            } else {
                echo "<img src='../img/no-image.png' alt='No Image' class='product-image'>";
            }
            echo "<div class='product-info'>";
            echo "<h2>" . htmlspecialchars($row["title"]) . "</h2>";
            echo "<p>£" . number_format($row["price"], 2) . " + " . htmlspecialchars($row["postage"]) . "</p>";
            echo "</div></div>";
            echo "</a>";
        }
    }
} else {
    echo "<p>No items found.</p>";
}
mysqli_close($conn);
?>