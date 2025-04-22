<?php

echo "<!-- This is a test file for the PHP project -->";



?>

<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <div class="container mt-5"> <!-- BootStrap: makes input, buttons look nice-->
        <h2 class="text-center">Login</h2>
        <form action="/login" method="POST" class="mt-4">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required><!-- Automatic Check for email -> javascript for more Adv check-->
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <p class="text-center mt-3">
                Don't have an account? 
                <a href="register.php">Register here</a>
            </p>
        </form>
    </div>
</body>

</html>