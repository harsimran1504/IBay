<?php
session_start();

$servername = 'sci-project.lboro.ac.uk';
$dbname = '295group5';
$username = '295group5';
$password = 'becvUgUxpXMijnWviR7h';

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = $_GET['query'] ?? '';
$query = trim($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Search Results for: <em><?php echo htmlspecialchars($query); ?></em></h2>

        <?php
        if ($query === '') {
            echo "<p>Please enter a search term.</p>";
        } else {
            $searchTerm = "%" . mysqli_real_escape_string($conn, $query) . "%";
            $sql = "SELECT title, description FROM iBayItems WHERE title LIKE ? OR description LIKE ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $searchTerm, $searchTerm);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='mb-4 p-3 border rounded'>";
                    echo "<h4>" . htmlspecialchars($row["title"]) . "</h4>";
                    #echo "<p>" . htmlspecialchars($row["description"]) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No items found.</p>";
            }
        }
        mysqli_close($conn);
        ?>
    </div>
</body>
</html>