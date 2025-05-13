<?php
session_start();

// Initialize the basket if it doesn't exist
if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}

// Add item to the basket
if (isset($_POST['itemId'])) {
    $itemId = $_POST['itemId'];

    // Avoid duplicate items in the basket
    if (!in_array($itemId, $_SESSION['basket'])) {
        $_SESSION['basket'][] = $itemId;
    }

    // Redirect back to the product page or basket
    header("Location: basket.php");
    exit();
}

// Clear the basket
if (isset($_POST['clearBasket'])) {
    $_SESSION['basket'] = [];
    header("Location: basket.php");
    exit();
}

$servername = 'sci-project.lboro.ac.uk';
$dbname = '295group5';
$username = '295group5';
$password = 'becvUgUxpXMijnWviR7h';

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles2.css">
</head>
<body>
    <?php if (isset($_SESSION['logout_msg'])) :?>
        <div class="alert alert-success alert-dismissible fade show p-3 rounded-3 shadow-sm text-center fw-bold" role="alert" id="successAlert">
            Sign out successful! You have been logged out.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['logout_msg']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['item_added'])) :?>
        <div class="alert alert-success alert-dismissible fade show p-3 rounded-3 shadow-sm text-center fw-bold" role="alert" id="successAlert">
            Item added successfully to Sellers List! You can now view it on Homepage
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['item_added']); ?>
    <?php endif; ?>

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

    <div class="Basket-Items">
        <h2>Basket</h2>
        <div class="basket-items-list">
            <table>
                
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Postage</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($_SESSION['basket'] as $itemId) {
                        $sql = "SELECT * FROM iBayItems LEFT JOIN iBayImages2 ON iBayItems.itemId = iBayImages2.itemId
                                WHERE iBayItems.itemId = '$itemId'";
                        $result = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['itemId']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                                echo "<td>£" . number_format($row['price'], 2) . "</td>";
                                echo "<td>£" . htmlspecialchars($row['postage']) . "</td>";
                                if (!empty($row['image'])) {
                                    $imageData = base64_encode($row['image']);
                                    $imageMime = $row['mimeType'];
                                    echo "<td><img src='data:$imageMime;base64,$imageData' class='img-fluid' alt='Product Image'></td>";
                                } else {
                                    echo "<td>No Image</td>";
                                }
                                echo "</tr>";
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="Price">
        <h3>Subtotal: 
            <?php
            $subtotal = 0;
            foreach ($_SESSION['basket'] as $itemId) {
                $sql = "SELECT price FROM iBayItems WHERE itemId = '$itemId'";
                $result = mysqli_query($conn, $sql);
                $item = mysqli_fetch_assoc($result);
                $subtotal += $item['price'];
            }
            echo '£' . number_format($subtotal, 2);
            ?>
        </h3>
        <h3>Postage: 
            <?php
            $postage = 0;
            foreach ($_SESSION['basket'] as $itemId) {
                $sql = "SELECT postage FROM iBayItems WHERE itemId = '$itemId'";
                $result = mysqli_query($conn, $sql);
                $item = mysqli_fetch_assoc($result);
                $itemPostage = preg_replace("/[^0-9\.]/", '', $item['postage']);
                $itemPostage = floatval($itemPostage);
                $itemPostage = round($itemPostage, 2);
                $postage += $itemPostage;
            }
            echo '£' . number_format($postage, 2);
            ?>
        </h3>
        <h3>Total: 
            <?php
            $total = $subtotal + $postage;
            echo '£' . number_format($total, 2);
            ?>
        </h3>
    </div>

    <div class="Basket-Buttons">
        <form action="checkout.php" method="POST">
            <button type="submit" class="btn btn-primary">Checkout</button>
        </form>
        <form method="POST">
            <button type="submit" name="clearBasket" class="btn btn-danger">Clear Basket</button>
        </form>
    </div>
</body>
</html>