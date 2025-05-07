<?php

// TEST LOGIN INFO, test@test.com, Test123.

session_start(); // Start the session to access session variables
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";       //Connection parameters for database
$user = "295group5";
$pass = "becvUgUxpXMijnWviR7h";
$dbname = "295group5";

$errorMessage = ""; //error message variable here

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form is submitted

    $firstName = $_POST['firstName']; //values from the form
    $lastName = $_POST['lastName'];
    $Name = $firstName . " " . $lastName; //Concatenate first and last name
    $email = $_POST['email'];
    $address = $_POST['address'];
    $postcode = $_POST['postcode'];
    $password = $_POST['newPassword'];
    $confirm = $_POST['reEnterPassword'];
    $rating = 0; //Default rating for new users

    //format checks here for password and email

    if (strlen($password) < 8) { //To check if password is at least 8 characters long 
        $errorMessage =  "<div class='alert alert-danger'>Password must be at least 8 characters long!</div>";
        
    } 
    if (!preg_match("/[A-Z]/", $password)) { //all checks for password strength required
        $errorMessage = "Password must include an uppercase letter.";
        
    }
    if (!preg_match("/[a-z]/", $password)) {
        $errorMessage = "Password must include a lowercase letter.";
        
    }
    if (!preg_match("/[0-9]/", $password)) {
        $errorMessage = "Password must include a number.";
        
    }
    if (!preg_match("/[\W]/", $password)) {
        $errorMessage = "Password must include a special character(_ is a Word Character).";
        
    }
    if ($password !== $confirm) { //To check if password inputs match
        $errorMessage = "<div class='alert alert-danger'>Passwords do not match!</div>";
        
    }

    mysqli_report(MYSQLI_REPORT_OFF);  

    // Check for Error and connection
    if (empty($errorMessage)) {
        $conn = new mysqli($host, $user, $pass, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "INSERT INTO iBayMembers (name, email, password, address, postcode, rating) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("sssssi", $Name, $email, $hashedPassword, $address, $postcode, $rating);

            if ($stmt->execute()) {
                $_SESSION['registration_success'] = true; // Set session variable to indicate success
                header("Location: login.php");
                exit();
            } 
            else {
                if ($conn->errno == 1062) { // Duplicate entry error code
                    $errorMessage = "Error: Email already exists. Please use a different email address.";
                } 
                else {
                    $errorMessage = "Error: " . $stmt->error;
                }
                $errorMessage = "Error: " . $stmt->error;
            }
            $stmt->close();
        } 
        else {
            $errorMessage = "Prepare failed: " . $conn->error;
        }
        $conn->close();
    }
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

        <?php if (!empty($errorMessage)): ?> <!-- In the case of an error, show msg -->
            <div class="alert alert-danger">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="" class="mt-4">

            <div class="mb-3">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstName" name="firstName" 
                    value="<?php echo isset($firstName) ? htmlspecialchars($firstName) : ''; ?>" required> <!-- They stay onscreen if error occurs -->
            </div>

            <div class="mb-3">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastName" name="lastName" 
                    value="<?php echo isset($lastName) ? htmlspecialchars($lastName) : ''; ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" 
                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="address" class="form-control" id="address" name="address" required>
            </div>

            <div class="mb-3">
                <label for="postcode" class="form-label">Postcode</label>
                <input type="postcode" class="form-control" id="postcode" name="postcode" required>
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

        <div class="text-center mt-3"> <!-- Center the title and add margin below -->
            Got an Account?
            <a href="login.php">Login here</a> <!-- Link to login page -->
        </div>

        <div class="text-center mt-3">
        <a href="index.php">Back to Homepage</a> <!-- Link to Homepage page -->
        </div>
        
    </div>

</body>


</html>