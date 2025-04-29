<?php
session_start(); // Start the session

$servername = 'sci-project.lboro.ac.uk';
$dbname = '295group5';
$username = '295group5';
$password = 'becvUgUxpXMijnWviR7h';


$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    // echo "Connected successfully";
}
session_start();

?>
<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <?php if (isset($_SESSION['logout_msg'])) :?>
        <div class="alert alert-success alert-dismissible fade show p-3 rounded-3 shadow-sm text-center fw-bold" role="alert" id="successAlert">
            Sign out successful! You have been logged out.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

    <?php unset($_SESSION['logout_msg']); // Clear it after showing ?>
    <?php endif; ?>
    

    <?php if (isset($_SESSION['name'])): ?>
        <div class="WelcomeBar">
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></p>
        </div>

        <div class = "ProfileBar"> 
            <div class="Profile">
                <a href="#...">My Profile</a>
                <a href="../includes/logout.php">Sign Out</a>
            </div>  
            
            <div class="BasketAndSellers">
                <a href="basket.php"><img src="..." alt="View Basket"></a>
                <a href="sell.php"><img src="..." alt="Sell Item"></a>
            </div>
        </div>

    <?php else: ?>

        <div class = "ProfileBar"> 
            <div class="Profile">
                <a href="login.php">Login</a><!--Change the links according to page -->
                <a href="register.php">Register</a>
            </div>  
            
            <div class="BasketAndSellers">
                <a href="basket.php"><img src="..." alt="View Basket"></a>
                <a href="sell.php"><img src="..." alt="Sell Item"></a>
            </div>
        </div>
    
    <?php endif; ?>

    <div class= "Divider"> 
        <hr class="solid">
    </div>

    <div class= "Title-SearchBar">
        <header> 
            <h1>Ibay</h1>
        </header>
        <!-- Search bar test-->
        <form action="/search" method="GET">
            <input type="text" name="query" placeholder="Search for any item" aria-label="Search">
            <button class="btn btn-primary">Search</button> <!-- BootStrap -->
        </form>
    </div>

    <div class= "Divider"> 
        <hr class="solid">
    </div>

    <div class="NavButtons">
        <nav>
            <ul>
                <li><a href="...">Home</a></li>
                <li><a href="...">Electronics</a></li>
                <li><a href="...">Fashion</a></li>
                <li><a href="...">Books</a></li>
                <li><a href="...">Furniture</a></li>
                <li><a href="...">Toys</a></li>
                <li><a href="...">Miscellaneous</a></li>
            </ul>
        </nav>
    </div>

    <div class="MainContent">
        <div class="Products">
            <?php
            // Example query to fetch products from the database
            $sql = "SELECT title FROM iBayItems";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                // Output data of each row
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='product-item'>";
                    echo "<h2>" . $row["title"] . "</h2>";
                    echo "</div>";
                }
            } else {
                echo "0 results";
            }
            mysqli_close($conn);

            ?>
        </div>
    </div>

    <!-- Change NavButtons, Main Content above, Copilot fill in -->

    <div class= "Divider"> 
        <hr class="solid">
    </div>

    <footer>
        <div class="footer">
            <p>© 2025 Ibay. All Rights Reserved.</p>
        </div>
    </footer>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>//button to close registration succesful alert, fades out after 5s
    window.addEventListener('DOMContentLoaded', (event) => {
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.classList.remove('show');
                setTimeout(() => {
                    successAlert.remove();
                }, 500); // Fade out timing
            }, 5000); // 5s delay
        }
    });
    </script>

</body>
</html>
