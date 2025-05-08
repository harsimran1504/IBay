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

$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';

// Validate input
if (!$category) {
    echo "No category selected.";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($category); ?> Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <h1><?php echo htmlspecialchars($category); ?> Items</h1>

    <div class="Products">
        <?php

        $category = $_GET['category'] ?? '';

        if ($category === 'Electronic') {
            // Group Electronics and Computing
            $sql = "SELECT title FROM iBayItems WHERE category IN ('Electronic', 'Computing')";
            $result = mysqli_query($conn, $sql);
        } elseif (in_array($category, ['Clothing', 'Book', 'Furniture', 'Toy'])) {
            // Standard category
            $sql = "SELECT title FROM iBayItems WHERE category = '$category'";
            $result = mysqli_query($conn, $sql);
        } elseif ($category === 'Miscellaneous') {
            // Exclude all listed categories including Computing
            $excluded = ['Electronic', 'Computing', 'Clothing', 'Book', 'Furniture', 'Toy'];
            $exclude_str = "'" . implode("','", $excluded) . "'";
            $sql = "SELECT title FROM iBayItems WHERE category NOT IN ($exclude_str)";
            $result = mysqli_query($conn, $sql);
        }

        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "<div class='product-item'><h2>" . htmlspecialchars($row["title"]) . "</h2></div>";
            }
        } else {
            echo "No products found in this category.";
        }
        mysqli_close($conn);
        ?>
    </div>

</body>
</html>