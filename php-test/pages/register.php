<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";       // Connection parameters for database
$user = "295group5";
$pass = "becvUgUxpXMijnWviR7h";
$dbname = "295group5";

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form is submitted
    $firstName = $_POST['firstName']; //values from the form
    $lastName = $_POST['lastName'];
    $Name = $firstName . " " . $lastName; //Concatenate first and last name
    $email = $_POST['email'];
    $password = $_POST['newPassword'];
    $confirm = $_POST['reEnterPassword'];


    if (strlen($password) < 8) { //To check if password is at least 8 characters long
        echo "<div class='alert alert-danger'>Password must be at least 8 characters long!</div>";
        exit("User registration failed.");
    } 
    if (!preg_match("/[A-Z]/", $password)) { //all checks for password strength required
        echo "Password must include an uppercase letter.";
        exit("User registration failed.");
    }
    if (!preg_match("/[a-z]/", $password)) {
        echo "Password must include a lowercase letter.";
        exit("User registration failed.");
    }
    if (!preg_match("/[0-9]/", $password)) {
        echo "Password must include a number.";
        exit("User registration failed.");
    }
    if (!preg_match("/[\W]/", $password)) {
        echo "Password must include a special character(_ is a Word Character).";
        exit("User registration failed.");
    }
    if ($password !== $confirm) { //To check if password inputs match
        echo "<div class='alert alert-danger'>Passwords do not match!</div>";
        //exit("User registration failed.");
    }


    $conn = new mysqli($host, $user, $pass, $dbname);


    // Check connection
    if ($conn->connect_error) {
        die("<div class='alert alert-danger'>Connection failed: " . $conn->connect_error . "</div>");
    }


    // Insert user data into the database
    $sql = "INSERT INTO iBayMembers (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("<div class='alert alert-danger'>Prepare failed: " . $conn->error . "</div>");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); //Hash password
    $stmt->bind_param("sss", $Name, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        // Success: Redirect to login page
        header("Location: login.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

}

?>

<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body> 

    <div class="container mt-5"> <!-- BootStrap: makes input, buttons look nice-->
        <h2 class="text-center">Registration</h2>
        <p class="text-center">Enter your:</p>
        <form method="POST" action="" class="mt-4">

            <div class="mb-3">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstName" name="firstName" required>
            </div>

            <div class="mb-3">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastName" name="lastName" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required><!-- Automatic Check for email -> javascript for more Adv check-->
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Re-Enter Password</label>
                <input type="password" class="form-control" id="reEnterPassword" name="reEnterPassword" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
           
        </form>
    </div>

</body>


</html>