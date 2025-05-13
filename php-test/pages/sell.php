<?php
// TEST LOGIN INFO, test@test.com, Test123.

session_start(); // Always required at the top
#var_dump($_SESSION); // Debugging line to check session variables

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = 'sci-project.lboro.ac.uk';
$dbname = '295group5';
$username = '295group5';
$password = 'becvUgUxpXMijnWviR7h';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check session user
    if (!isset($_SESSION['userId'])) {
        $errorMessage = "Error: User not logged in.";
    }

    // Validate required fields
    $requiredFields = ['product-title', 'product-description', 'product-category', 'product-condition', 'product-price', 'product-postage-price', 'product-postage-way'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errorMessage = "Error: Missing or empty field: $field";
            break;
        }
    }

    // Proceed only if no errors so far
    if (empty($errorMessage)) {
        // Assign and sanitize inputs
        $productTitle = trim($_POST['product-title']);
        $productDescription = trim($_POST['product-description']);
        $productCategory = trim($_POST['product-category']);
        $productCondition = trim($_POST['product-condition']);
        $productPostageWay = trim($_POST['product-postage-way']);

        // Validate numeric fields
        if (!is_numeric($_POST['product-price']) || $_POST['product-price'] <= 0) {
            $errorMessage = "Error: Invalid product price.";
        } elseif (!is_numeric($_POST['product-postage-price']) || $_POST['product-postage-price'] < 0) {
            $errorMessage = "Error: Invalid postage price.";
        } else {
            $productPrice = floatval($_POST['product-price']);
            $productPostagePrice = floatval($_POST['product-postage-price']);

            // Compose the full description and postage
            $productPostage = $productPostagePrice . " - " . $productPostageWay;
            $productFullDescription = $productCondition . " : " . $productDescription;
            $userId = $_SESSION['userId'];

            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                $errorMessage = "Connection failed: " . $conn->connect_error;
            } else {
                $sqlItem = "INSERT INTO iBayItems (`userId`, `title`, `category`, `description`, `price`, `postage`) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sqlItem);

                if ($stmt) {
                    $stmt->bind_param("ssssds", $userId, $productTitle, $productCategory, $productFullDescription, $productPrice, $productPostage);

                    if ($stmt->execute()) {
                        $itemId = $stmt->insert_id; // Get the last inserted item ID
                        if (isset($_FILES['product-images'])) {
                            $images = $_FILES['product-images'];

                            
                            $sqlItemImages = "INSERT INTO iBayImages2 (`image`, `mimeType`, `imageSize`, `itemId`) VALUES (?, ?, ?, ?)";
                            $stmtImages = $conn->prepare($sqlItemImages);
                            if (!$stmtImages) {
                                $errorMessage = "Prepare failed: " . $conn->error;
                            } 
                            else {
                                for ($key = 0; $key < count($images['tmp_name']); $key++) {
                                    if ($images['error'][$key] === UPLOAD_ERR_OK) {
                                        $tmpName = $images['tmp_name'][$key];
                                        $mimeType = mime_content_type($tmpName);
                                        $imageSize = filesize($tmpName);

                                        echo "<p>Image $key - Type: $mimeType, Size: $imageSize bytes</p>";

                                        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                                        if (!in_array($mimeType, $allowedTypes)) {
                                            error_log("Image $key skipped: invalid MIME type $mimeType");
                                            continue;
                                        }

                                        if ($imageSize > 5 * 1024 * 1024) {
                                            $errorMessage = "Error: File size exceeds 5MB.";
                                            continue;
                                        }

                                        $imageData = file_get_contents($tmpName);
                                        if ($imageData === false) {
                                            error_log("Failed to read image data from file: $tmpName");
                                            continue;
                                        }

                                        $null = NULL;
                                        $stmtImages->bind_param("bssi", $null, $mimeType, $imageSize, $itemId);
                                        $stmtImages->send_long_data(0, $imageData);
                                        $stmtImages->execute();
                                    }
                                }
                                $stmtImages->close();
                                $_SESSION['image_added'] = true;
                            }
                        }
                        $_SESSION['item_added'] = true;
                        header("Location: index.php");

                        echo "<pre>";
                        print_r($_FILES['product-images']);
                        echo "</pre>";
                        
                        exit();
                    } else {
                        $errorMessage = "Error executing query: " . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    $errorMessage = "Prepare failed: " . $conn->error;
                }

                $conn->close();
            }
        }
    }
}

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
            <select id="product-category" name="product-category" required>
                <option value="Computing">Computing</option>
                <option value="Electronics">Electronics</option>
                <option value="Fashion">Fashion</option>
                <option value="Books">Books</option>
                <option value="Furniture">Furniture</option>
                <option value="Toys">Toys</option>
                <option value="Other/Miscellaneous">Other/Miscellaneous</option>
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
            <select id="product-postage-way" name="product-postage-way" required>
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
        const fileInput1 = document.getElementById("product-images");

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