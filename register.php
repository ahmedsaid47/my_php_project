<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <?php
    session_start();
    if (isset($_SESSION['register_error'])) {
        echo "<p style='color:red'>" . $_SESSION['register_error'] . "</p>";
        unset($_SESSION['register_error']);
    }
    ?>
    <form action="authcontroller.php" method="post">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="register">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</body>
</html>

