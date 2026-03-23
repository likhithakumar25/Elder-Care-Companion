<?php
$host = 'localhost';
$user = 'username';
$pass = 'password';
$dbname = 'database_name';

$conn = new mysqli($host, $user, $pass, $dbname, 3306);

if ($conn->connect_error) {
  die("Database connection failed");
}
?>