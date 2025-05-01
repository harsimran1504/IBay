<?php //Settings for profile
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
    
    <div class="WelcomeBar">
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></p>
    </div>

    <div class = "ProfileBar"> 
        <div class="Profile">
            <a href="index.php">Back to Homepage</a>
            <a href="../includes/logout.php">Sign Out</a>
        </div>  
        
        <div class="BasketAndSellers">
            <a href="basket.php"><img src="..." alt="View Basket"></a>
            <a href="sell.php"><img src="..." alt="Sell Item"></a>
        </div>
    </div>

    <div class= "Divider"> 
        <hr class="solid">
    </div>





</body>
</html>





