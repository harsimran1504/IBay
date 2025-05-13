<?php

session_start();

if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}

$servername = 'sci-project.lboro.ac.uk';
$dbname = '295group5';
$username = '295group5';
$password = 'becvUgUxpXMijnWviR7h';

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}



// Check if the basket is empty
if (empty($_SESSION['basket'])) {
    header("Location: index.php");
    exit();
}

echo "<h1> Thank you for your purchase</h1>";
echo "<a href='index.php'>Return to Homepage</a>";



$currentDateTime = date('Y-m-d H:i:s');


foreach ($_SESSION['basket'] as $itemId) {
    $itemId = intval($itemId);
    $sql = "UPDATE iBayItems SET finish=? WHERE itemId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $currentDateTime, $itemId);
    $stmt->execute();
    $stmt->close();
    // Remove the item from the basket
    $_SESSION['basket'] = [];   
}


?>