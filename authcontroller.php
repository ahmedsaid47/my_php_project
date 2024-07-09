<?php
session_start();
include 'db.php';

function registerUser($username, $password, $email) {
    global $mysqli;
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $mysqli->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $passwordHash, $email);
    return $stmt->execute();
}

function loginUser($username, $password) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        return true;
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        if (registerUser($username, $password, $email)) {
            header("Location: login.php");
        } else {
            // Kayıt işlemi başarısız olduğunda kullanıcıya mesaj göster
            $_SESSION['register_error'] = "Registration failed! Please try again.";
            header("Location: register.php");
        }
    }

    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if (loginUser($username, $password)) {
            header("Location: index.php");
        } else {
            // Giriş işlemi başarısız olduğunda kullanıcıya mesaj göster
            $_SESSION['login_error'] = "Login failed! Please check your username and password.";
            header("Location: login.php");
        }
    }
}
?>

