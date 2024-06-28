<?php
// db.php
$mysqli = new mysqli('localhost', 'root', '123', 'my_php_project');

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
?>

