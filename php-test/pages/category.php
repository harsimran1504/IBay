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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category); ?> Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles2.css">
</head>
<body>

    <div class="Title-SearchBar">
            <header> 
                <h1>Ibay</h1>
            </header>
            <form action="search.php" method="GET">
                <input type="text" name="query" placeholder="Search for any item" aria-label="Search">
                <button class="btn btn-primary">Search</button>
            </form>

            <div class="profile-dropdown">
                <button class="profile-dropdown-btn">
                    <?php echo isset($_SESSION['name']) ? 'Welcome, ' . htmlspecialchars($_SESSION['name']) : 'Account'; ?>
                </button>
                <div class="profile-dropdown-content">
                    <?php if (isset($_SESSION['name'])): ?>
                        <a href="profile.php">My Profile</a>
                        <a href="basket.php">View Basket</a>
                        <a href="sell.php">Sell Item</a>
                        <a href="../includes/logout.php">Sign Out</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                        <a href="register.php">Register</a>
                        <a href="sell.php">Sell Item</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="Divider"> 
            <hr class="solid">
        </div>

        <div class="NavButtons">
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="category.php?category=Electronic">Electronics</a></li>
                    <li><a href="category.php?category=Clothing">Fashion</a></li>
                    <li><a href="category.php?category=Book">Books</a></li>
                    <li><a href="category.php?category=Furniture">Furniture</a></li>
                    <li><a href="category.php?category=Toy">Toys</a></li>
                    <li><a href="category.php?category=Miscellaneous">Miscellaneous</a></li>
                </ul>
            </nav>
        </div>

        <div class="Divider"> 
            <hr class="solid">
        </div>

    <h1><?php echo htmlspecialchars($category); ?> Items</h1>

    <div class="MainContent">
        <div class="Products">
            <?php
            // Fetch the category from the URL
            $category = $_GET['category'] ?? '';

            if ($category === 'Electronic') {
                // Group Electronics and Computing
                $sql = "SELECT title, price, postage FROM iBayItems WHERE category IN ('Electronic', 'Computing')";
            } elseif (in_array($category, ['Clothing', 'Book', 'Furniture', 'Toy'])) {
                // Standard category
                $sql = "SELECT title, price, postage FROM iBayItems WHERE category = '$category'";
            } elseif ($category === 'Miscellaneous') {
                // Exclude all listed categories including Computing
                $excluded = ['Electronic', 'Computing', 'Clothing', 'Book', 'Furniture', 'Toy'];
                $exclude_str = "'" . implode("','", $excluded) . "'";
                $sql = "SELECT title, price, postage FROM iBayItems WHERE category NOT IN ($exclude_str)";
            }

            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    // Output each product's details
                    echo "<div class='product-item'>";
                    echo "<div class='product-info'>";
                    echo "<h2>" . htmlspecialchars($row["title"]) . "</h2>";
                    echo "<img src='" . htmlspecialchars($row["image_url"] ?? "placeholder.jpg") . "' alt='Product Image'>";
                    echo "<p>£" . number_format($row["price"], 2) . " + " . htmlspecialchars($row["postage"]) . "</p>";
                    echo "</div></div>";
                }
            } else {
                echo "No products found in this category.";
            }

            mysqli_close($conn);
            ?>
        </div>
    </div>

</body>
</html>
