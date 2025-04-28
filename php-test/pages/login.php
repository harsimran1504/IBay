<?php
session_start();
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

    <h2 class="text-center">Login</h2>
    <form action="/login" method="POST" class="mt-4">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>

        <p class="text-center mt-3">
            Don't have an account? 
            <a href="register.php">Register here</a>
        </p>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
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
