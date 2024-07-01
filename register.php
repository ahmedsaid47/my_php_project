<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Bootstrap CSS dosyasını ekliyoruz -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="my-4 text-center">Register</h2>
        <?php
        session_start();
        if (isset($_SESSION['register_error'])) {
            echo "<div class='alert alert-danger' role='alert'>" . htmlspecialchars($_SESSION['register_error']) . "</div>";
            unset($_SESSION['register_error']);
        }
        ?>
        <form action="authcontroller.php" method="post" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" id="username" placeholder="Username" required>
                <div class="invalid-feedback">Please enter your username.</div>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
                <div class="invalid-feedback">Please enter a valid email address.</div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                <div class="invalid-feedback">Please enter your password.</div>
            </div>
            <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
        </form>
        <p class="mt-3 text-center">Already have an account? <a href="login.php">Login here</a>.</p>
    </div>

    <!-- Bootstrap JS ve bağımlılıkları (jQuery ve Popper.js) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Bootstrap form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>

