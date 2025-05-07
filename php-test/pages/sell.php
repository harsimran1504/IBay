<?php

// TEST LOGIN INFO, test@test.com, Test123.

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $productTitle = $_POST['product-title'];
    $productDescription = $_POST['product-description'];
    $productPrice = floatval($_POST['product-price']);
    $productPostage = $_POST['product-postage'];
    $productCategory = $_POST['product-category'];

    // $productImages = $_FILES['product-images'];
    // if (is_array($productImages['tmp_name'])) {
    //     $imageData = $productImages['tmp_name'][0];
    //     $imageSize = $productImages['size'][0];
    // } else {
    //     $imageData = $productImages['tmp_name'];
    //     $imageSize = $productImages['size'];
    // }

    // $mimeType = mime_content_type($imageData);
    // $imageData = file_get_contents($imageData);
    // $imageData = base64_encode($imageData); // Encode the image data to store in the database
    // $imageSize = $imageSize / 1024; // Convert size to KB


    $userId = $_SESSION['user_id'];

    $sqlItem = "INSERT INTO `iBayItems`(`userId`, `title`, `category`, `description`, `price`, `postage`)
     VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlItem);

    if ($stmt){

    }

    $stmt->bind_param("ssssds", $userId, $productTitle, $productCategory, $productDescription, $productPrice, $productPostage);
    $stmt->execute();
    $itemId = $stmt->insert_id;
    $stmt->close();

    // $sqlItemImages = "INSERT INTO `iBayImages`(`image`, `mimeType`, `imageSize`, `itemId`) 
    // VALUES (?, ?, ?, ?)";
    // $stmt = $conn->prepare($sqlItemImages);
    // $stmt->bind_param("sssi", $imageData, $mimeType, $imageSize, $itemId);
    // $stmt->execute();
    // $stmt->close();

}



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sellers Page</title>
    <link rel="stylesheet" href="../css/sellersPage.css">
    
      
</head>
<body>

    <?php if (isset($_SESSION['name'])): ?>
    <div class = 'form'>
        <h2>Item Specifics</h2>
        <form method="POST" action="" enctype="multipart/form-data" class="mt-4">

            <label for="product-title">Product Title:</label>
            <input type="text" id="product-title" name="product-title" required>

            <label for="product-price">Product Price:</label>
            <input type="text" id="product-price" name="product-price" required>

            <label for="product-postage">Product Postage:</label>
            <input type="text" id="product-postage" name="product-postage" required>

            <label for="product-category">Product Category:</label>
            <input type="text" id="product-category" name="product-category" required>

            <!-- <label for="product-condition">Product Condition:</label>
            <select id="product-condition" name="product-condition" required>
                <option value="Brand new">New</option>
                <option value="Used">Used</option>
                <option value="Refurbished">Refurbished</option>
            </select> -->

            <label for="product-description">Product Description:</label>
            <textarea id="product-description" name="product-description" required></textarea>

            <label for="product-images">Photos:</label>
            <div class = "drop-area">
                <p>Drag and drop images here</p>
                <input type="file" id="product-images" name="product-images" accept="image/*" required multiple>
            </div>

            <button type="submit">Submit</button>

            <?php echo $productImages; ?>
        </form>
    </div>

    <?php else: ?>
        <script type="text/javascript">
            window.location.href = "login.php";
        </script>

    <?php endif; ?>


    
</body>
</html>