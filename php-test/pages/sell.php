<?php

// TEST LOGIN INFO, test@test.com, Test123.

session_start(); // Start the session

$servername = "localhost"; // Database connection parameters
$dbname = "295group5";
$username = "295group5";
$password = "becvUgUxpXMijnWviR7h";

$errorMessage = ""; // Initialize error message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productTitle = $_POST['product-title'];
    $productDescription = $_POST['product-description'];
    $productCategory = $_POST['product-category'];
    $productCondition = $_POST['product-condition'];
    $productPrice = floatval($_POST['product-price']);

    $productPostageWay = $_POST['product-postage-way'];
    $productPostagePrice = floatval($_POST['product-postage-price']);

    // Product images
    //$productImages = $_FILES['product-images'];
    //if (is_array($productImages['tmp_name'])) {
        //$imageData = $productImages['tmp_name'][0];
        //$imageSize = $productImages['size'][0];
    //} else {
    //$imageData = $productImages['tmp_name'];
        //$imageSize = $productImages['size'];
    //}
    // $mimeType = mime_content_type($imageData);
    // $imageData = file_get_contents($imageData);
    // $imageData = base64_encode($imageData); // Encode the image data to store in the database
    // $imageSize = $imageSize / 1024; // Convert size to KB



    // Validate product price
    if (isset($_POST['product-price']) && is_numeric($_POST['product-price']) && $_POST['product-price'] > 0) {
        $productPrice = floatval($_POST['product-price']);
    } else {
        // Handle invalid price case
        $errorMessage =  "Invalid product price.";
        exit;
    }

    // Validate postage price
    if (isset($_POST['product-postage-price']) && is_numeric($_POST['product-postage-price']) && $_POST['product-postage-price'] >= 0) {
        $productPostagePrice = floatval($_POST['product-postage-price']);
    } else {
        // Handle invalid postage price case
        $errorMessage = "Invalid postage price.";
        exit;
    }
    

    if (empty($errorMessage)) {
        $conn = new mysqli($servername, $username, $password, $dbname); // Create connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $productPostage = $productPostagePrice . " - " . $productPostageWay; // Concatenate the two values
        $userId = $_SESSION['user_id'];
        $sqlItem = "INSERT INTO iBayItems (`userId`, `title`, `category`, `description`, `price`, `postage`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sqlItem);

        if ($stmt) {
            $stmt->bind_param("ssssds", $userId, $productTitle, $productCategory, $productDescription, $productPrice, $productPostage);

            if ($stmt->execute()) {
                $_SESSION['item_added'] = true; // Set session variable to indicate success
                header("Location: index.php");
                exit();
            } 
            else {
                $errorMessage = "Error: " . $stmt->error;
            }
                
        }
            $stmt->close();
} 
else {
    $errorMessage = "Prepare failed: " . $conn->error;
}

    }
    

$conn->close(); // Close the connection


    // $sqlItemImages = "INSERT INTO `iBayImages`(`image`, `mimeType`, `imageSize`, `itemId`) 
    // VALUES (?, ?, ?, ?)";
    // $stmt = $conn->prepare($sqlItemImages);
    // $stmt->bind_param("sssi", $imageData, $mimeType, $imageSize, $itemId);
    // $stmt->execute();
    // $stmt->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sellers Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/sellersPage.css">
    
      
</head>

<body>
   
    <?php if (isset($_SESSION['name'])): ?>
    <div class = 'form'>
        <h2>Item Specifics</h2>

        <?php if (!empty($errorMessage)): ?> <!-- In the case of an error, show msg -->
            <div class="alert alert-danger">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data" class="mt-4">

            <label for="product-title">Product Title:</label>
            <input type="text" id="product-title" name="product-title" required>

            <label for="product-price">Product Price:</label>
            <input type="text" id="product-price" name="product-price" required>

            <label for="product-category">Product Category:</label>
            <select id="product-condition" name="product-condition" required>
                <option value="Brand new">Computing</option>
                <option value="Used">Electronics</option>
                <option value="Refurbished">Fashion</option>
                <option value="Refurbished">Books</option>
                <option value="Refurbished">Furniture</option>
                <option value="Refurbished">Toys</option>
                <option value="Refurbished">Other/Miscellaneous</option>
            </select> 

            <label for="product-condition">Product Condition:</label>
            <select id="product-condition" name="product-condition" required>
                <option value="Brand new">New</option>
                <option value="Used">Used</option>
                <option value="Refurbished">Refurbished</option>
            </select> 

            <label for="product-description">Product Description:</label>
            <textarea id="product-description" name="product-description" required></textarea>

            <!-- This is the product image -->
            <label for="product-images">Photos:</label>
            <div id="drop-area" class="drop-area">
                <p>Drag and drop images here</p>
                <input type="file" id="product-images" name="product-images[]" accept="image/*" required multiple>
                
                <div id="preview"></div>
                <script> //This gives preview of the images before uploading
                    const fileInput = document.getElementById('product-images');
                    const preview = document.getElementById('preview');
                    let allFiles = [];

                    fileInput.addEventListener('change', function () {
                        // Convert FileList to Array
                        const newFiles = Array.from(this.files);
                        // Merge new files into allFiles
                        allFiles = [...allFiles, ...newFiles];
                        // Clear and update preview
                        preview.innerHTML = '';
                        allFiles.forEach(file => {
                            const img = document.createElement('img');
                            const objectUrl = URL.createObjectURL(file);
                            img.src = objectUrl;
                            img.style.height = '100px';
                            img.onload = () => URL.revokeObjectURL(objectUrl); // free memory
                            preview.appendChild(img);
                        });
                    });
                </script>
            </div>
            <!-- This is the product image end -->

            <label for="product-postage-way">Postage Delivery:</label>
            <select id="product-condition" name="product-condition" required>
                <option value="Standard Delivery">Standard Delivery</option>
                <option value="Economy Delivery">Economy Delivery</option>
            </select> 

            <label for="product-postage-price">Postage Price:</label>
            <input type="text" id="product-postage-price" name="product-postage-price" required>
                
            <button type="submit">Submit</button>
           
        </form>

        <div class="text-center mt-3"> <!-- Center the title and add margin below -->
        <a href="index.php">Back to Homepage</a> <!-- Link to Homepage page -->
    </div>
    
    <!-- This is checks for inputs -->
    <script> 
        document.querySelector("form").addEventListener("submit", function(e) {
        const price = document.getElementById("product-price").value.trim();
        const postage = document.getElementById("product-postage").value.trim();
        const images = document.getElementById("product-images").files;
        let errors = [];

        // Check that price and postage are valid floats
        if (isNaN(price) || parseFloat(price) <= 0) {
            errors.push("Product price must be a positive number.");
        }

        if (isNaN(postage) || parseFloat(postage) < 0) {
            errors.push("Product postage must be a number (0 or more).");
        }

        // Ensure at least one image is selected
        if (images.length === 0) {
            errors.push("Please upload at least one product image.");
        }

        // If any errors, prevent form submission and alert the user
        if (errors.length > 0) {
            e.preventDefault(); // Stop form from submitting
            alert(errors.join("\n"));
        }
        });
    </script>

    <!-- Drag and Drop Functionality -->
    <script>
        const dropArea = document.getElementById("drop-area");
        const fileInput = document.getElementById("product-images");

        // Highlight drop area when item is dragged over it
        ["dragenter", "dragover"].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropArea.classList.add("highlight");
            });
        });

        // Remove highlight when drag leaves or ends
        ["dragleave", "drop"].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropArea.classList.remove("highlight");
            });
        });

        // Handle dropped files
        dropArea.addEventListener("drop", (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files; // Assign dropped files to the hidden input
            }
        });

        // Trigger file cursor on click
        dropArea.addEventListener("click", () => {
            fileInput.click();
        });
    </script>

    <?php else: ?>
        <script type="text/javascript">
            window.location.href = "login.php";
        </script>

    <?php endif; ?>

</body>
</html>