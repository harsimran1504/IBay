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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
// iBayItems.*, iBayImages2.image, iBayImages2.mimeType
$stmt = mysqli_prepare($conn, "SELECT *
                              FROM iBayItems 
                              LEFT JOIN iBayImages2 
                              ON iBayItems.itemId = iBayImages2.itemId 
                              WHERE iBayItems.itemId = ?");
mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    header("Location: index.php");
    exit();
}

$item = mysqli_fetch_assoc($result);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['title']); ?> - Ibay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles2.css">
</head>
<body>
    <?php if (isset($_SESSION['logout_msg'])) : ?>
        <div class="alert alert-success alert-dismissible fade show p-3 rounded-3 shadow-sm text-center fw-bold" role="alert" id="successAlert">
            Sign out successful! You have been logged out.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['logout_msg']); ?>
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

    <div class="MainContent">
        <div class="product-details container mt-4">
            <div class="row">
                <div class="col-md-6">
                     <?php if(!empty($item['image'])): ?>
                        <?php
                        $imageData = base64_encode($item['image']);
                        $imageMime = $item['mimeType'];
                        ?>
                        <img src="data:<?php echo $imageMime; ?>;base64,<?php echo $imageData; ?>" 
                            class="img-fluid rounded-3" 
                            alt="Product Image">
                    <?php else: ?>
                        <img src="placeholder.jpg" class="img-fluid rounded-3" alt="Product Image">
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h1 class="mb-4"><?php echo htmlspecialchars($item['title']); ?></h1>
                    <div class="bg-white p-4 rounded-3 shadow-sm">
                        <h3 class="text-primary">£<?php echo number_format($item['price'], 2); ?></h3>
                        <p class="text-muted">+ <?php echo htmlspecialchars($item['postage']); ?> postage</p>
                        <hr>
                        <p class="lead"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                        
                        <?php if(isset($_SESSION['name'])): ?>
                            <form action="basket.php" method="POST" class="mt-4">
                                <input type="hidden" name="itemId" value="<?php echo $item['itemId']; ?>">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    Add to Basket
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="basket.php" class="btn btn-outline-primary btn-lg w-100">
                                Buy <!--Change to prompt login to buy-->
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="Divider"> 
        <hr class="solid">
    </div>

    <footer>
        <div class="footer">
            <p>© 2025 Ibay. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    window.addEventListener('DOMContentLoaded', (event) => {
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.classList.remove('show');
                setTimeout(() => {
                    successAlert.remove();
                }, 500);
            }, 5000);
        }
    });
    </script>
</body>
</html>