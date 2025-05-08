<?php

// TEST LOGIN INFO, test@test.com, Test123.

session_start();

$host = "localhost";       //Connection parameters for database
$user = "295group5";
$pass = "becvUgUxpXMijnWviR7h";
$dbname = "295group5";

$errorMessage = ""; //error message variable here

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form is submitted

    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($stmt = $conn->prepare("SELECT userId, name, email, password FROM iBayMembers WHERE email = ?")) {
        $stmt->bind_param("s", $email); // Bind email only
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                //debug
    
                //echo "Hashed password from DB: " . $row['password'] . "<br>";
                //echo "User-entered password: " . $password . "<br>";
                //var_dump(password_verify($password, $row['password']));
                //var_dump( password_verify("Bonbon-123", $row['password']));

                // Fetch hashed password from DB
                if (password_verify($password, $row['password'])) { // Verify password
                
                    // Password correct
                    $_SESSION['name'] = $row['name']; //Get user name from DB
                    $_SESSION['userId'] = $row['userId']; //Get userId from DB --> for Id of item seller
                    header("Location: index.php");
                    exit();
                } else {
                    // Password incorrect
                    $errorMessage = "<div class='alert alert-danger'>Invalid email or password</div>";
                }
            } else {
                // No user with that email
                $errorMessage = "<div class='alert alert-danger'>Invalid email or password</div>"; // Can change to make error msgs specific, but for security reasons, we keep it generic
            }
        } else {
            // Error executing query
            $errorMessage = "<div class='alert alert-danger'>Error executing query</div>";
        }
        $stmt->close();
    } else {
        // Error preparing statement
        $errorMessage = "<div class='alert alert-danger'>Error preparing statement</div>";
    }
    
    $conn->close();

}
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

<div class="container mt-5">

    <!-- Success Alert (inside body, before form) -->
    <?php if (isset($_SESSION['registration_success']) && $_SESSION['registration_success'] === true): ?>
        <div class="alert alert-success alert-dismissible fade show p-3 rounded-3 shadow-sm text-center fw-bold" role="alert" id="successAlert">
            Registration successful! Please log in below.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['registration_success']); ?>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?> <!-- In the case of an error, show msg -->
            <div class="alert alert-danger">
                <?php echo $errorMessage; ?>
            </div>
    <?php endif; ?>

    <h2 class="text-center">Login</h2>
    <form action="" method="POST" class="mt-4">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" 
            value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required> <!-- They stay onscreen if error occurs -->
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>

        <p class="text-center mt-3">
            Don't have an account? 
            <a href="register.php">Register here</a> <!-- Link to register page -->
        </p>
        <p class="text-center mt-3">
            <a href="index.php">Back to Homepage</a> <!-- Link to Homepage page -->
        </p>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap JS for alert functionality -->

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
