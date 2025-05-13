<?php
session_start();

$servername = 'sci-project.lboro.ac.uk';
$dbname = '295group5';
$username = '295group5';
$password = 'becvUgUxpXMijnWviR7h';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    header("Location: login.php");
    exit;
}

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    // echo "Connected successfully";
}

// HANDLE FORM SUBMISSION
$updated = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //grab & sanitize
    $firstName = mysqli_real_escape_string($conn, trim($_POST['firstName'] ?? ''));
    $lastName  = mysqli_real_escape_string($conn, trim($_POST['lastName']  ?? ''));
    $email     = mysqli_real_escape_string($conn, trim($_POST['email']     ?? ''));
    $address   = mysqli_real_escape_string($conn, trim($_POST['address']   ?? ''));
    $postcode  = mysqli_real_escape_string($conn, trim($_POST['postcode']  ?? ''));

    //validate
    if ($firstName === '' || $lastName === '' || $email === '' || $address === '' || $postcode === '') {
        $error = "All fields are required.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }
    else {
        //perform the UPDATE
        $fullName = $firstName . ' ' . $lastName;
        $sql = "
          UPDATE iBayMembers
             SET name     = '$fullName',
                 email    = '$email',
                 address  = '$address',
                 postcode = '$postcode'
           WHERE userId   = $userId
        ";
        if (mysqli_query($conn, $sql)) {
            $updated = true;
        } else {
            $error = "DB error: " . mysqli_error($conn);
        }
    }
}

//FETCH CURRENT PROFILE DATA
$sql    = "SELECT name, email, address, postcode FROM iBayMembers WHERE userId = $userId";
$result = mysqli_query($conn, $sql);
if (! $result || ! ($row = mysqli_fetch_assoc($result))) {
    die("Unable to load profile data.");
}

//split the full name
list($curFirst, $curLast) = array_pad(explode(' ', $row['name'], 2), 2, '');

//escape for HTML
$curFirst    = htmlspecialchars($curFirst);
$curLast     = htmlspecialchars($curLast);
$curEmail    = htmlspecialchars($row['email']);
$curAddress  = htmlspecialchars($row['address']);
$curPostcode = htmlspecialchars($row['postcode']);

mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
  
<head>
  <meta charset="UTF-8">
  <title>Edit Profile | iBay</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet">
  <link rel="stylesheet" href="../css/styles2.css">
  <style>
    .container { max-width:600px; margin:2rem auto; }
    .alert     { margin-bottom:1.5rem; }
  </style>
</head>

<body>
  <div class="container">

    <!--  Success / Error -->
    <?php if ($updated): ?>
      <div class="alert alert-success">Profile updated successfully.</div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <!--  Nav & Welcome -->
    <h2>Account Settings</h2>
    <p>
      <a href="index.php">Home</a> |
      <a href="../includes/logout.php">Sign Out</a>
    </p>
    <hr>

    <!--  Profile Edit Form -->
    <form method="POST" action="profile.php" novalidate>
      <div class="mb-3">
        <label class="form-label" for="firstName">First Name</label>
        <input class="form-control" id="firstName" name="firstName"
               type="text" value="<?=$curFirst?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label" for="lastName">Last Name</label>
        <input class="form-control" id="lastName" name="lastName"
               type="text" value="<?=$curLast?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label" for="email">Email address</label>
        <input class="form-control" id="email" name="email"
               type="email" value="<?=$curEmail?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label" for="address">Address</label>
        <input class="form-control" id="address" name="address"
               type="text" value="<?=$curAddress?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label" for="postcode">Postcode</label>
        <input class="form-control" id="postcode" name="postcode"
               type="text" value="<?=$curPostcode?>" required>
      </div>

      <button type="submit" class="btn btn-primary">Update Account</button>
    </form>

  </div>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
  </script>
</body>
</html>
