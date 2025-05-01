<?php 
session_start(); 

$userId = $_SESSION['name'] ?? null;
if (!$userId) {
    header("Location: login.php");
    exit();
}

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

    <class="ChangeAccountSettings">
        <h2 class="text-center">Account Settings</h2>
        <p class="text-center">Change your:</p>
        <div class="d-flex justify-content-center">
            <method="POST" action="" class="mt-4">
                <div class="mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" style="width: 500px;"  id="firstName" name="firstName" 
                        value="<?php echo isset($firstName) ? htmlspecialchars($firstName) : ''; ?>" required> <!-- They stay onscreen if error occurs -->
                </div>

                <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" style="width: 500px;" id="lastName" name="lastName" 
                        value="<?php echo isset($lastName) ? htmlspecialchars($lastName) : ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" style="width: 500px;" id="email" name="email" 
                        value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="address" class="form-control" style="width: 500px;" id="address" name="address" required>
                </div>

                <div class="mb-3">
                    <label for="postcode" class="form-label">Postcode</label>
                    <input type="postcode" class="form-control" style="width: 500px;" id="postcode" name="postcode" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Account</button>
            </method>
        </div>
    </>

</body>
</html>





