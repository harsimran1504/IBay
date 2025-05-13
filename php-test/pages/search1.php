<?php
session_start();

// Database connection
$conn = mysqli_connect(
  'sci-project.lboro.ac.uk',
  '295group5',
  'becvUgUxpXMijnWviR7h',
  '295group5'
);
if (!$conn) die('DB Error: '.mysqli_connect_error());

// Get and validate search term
$query = trim($_GET['query'] ?? '');
if ($query === '') {
    echo "<p>Please enter a search term.</p>";
    exit;
}

// Prepare and execute search query
$safe  = mysqli_real_escape_string($conn, $query);
$sql   = "
  SELECT i.*, img.image, img.mimeType
  FROM iBayItems i
  LEFT JOIN iBayImages2 img ON i.itemId = img.itemId
  WHERE (i.title LIKE '%{$safe}%' OR i.description LIKE '%{$safe}%')
    AND (i.finish = '0000-00-00 00:00:00' OR i.finish > NOW())
  GROUP BY i.itemId
  ORDER BY i.start DESC
  LIMIT 50
";

$res = mysqli_query($conn, $sql);
if (!$res) {
    echo "<p class='alert alert-danger'>Query error: ".mysqli_error($conn)."</p>";
    exit;
}

if (mysqli_num_rows($res) === 0) {
    echo "<p>No items found for '<em>".htmlspecialchars($query)."</em>'.</p>";
    exit;
}

// Output products
while ($row = mysqli_fetch_assoc($res)) {
    echo "<a href='product_details.php?id={$row['itemId']}' class='product-link'>";
    echo "<div class='product-item'>";
    if (!empty($row['image']) && !empty($row['mimeType'])) {
        $data = base64_encode($row['image']);
        echo "<img src='data:{$row['mimeType']};base64,{$data}' class='product-image'>";
    } else {
        echo "<img src='placeholder.jpg' class='product-image'>";
    }
    echo "<div class='product-info'>";
    echo "<h2>".htmlspecialchars($row['title'])."</h2>";
    echo "<p>£".number_format($row['price'],2)." + ".htmlspecialchars($row['postage'])."</p>";
    echo "</div></div></a>";
}

mysqli_close($conn);
?>