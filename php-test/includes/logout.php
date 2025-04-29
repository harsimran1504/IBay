<?php // Process to signout user and go back to homepage
session_start(); // Start the session to access session variables
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session

session_start();
$_SESSION['logout_msg'] = "You have been logged out successfully.";

// Redirect to homepage (or login page)
header("Location: ../pages/index.php");
exit();
?>
