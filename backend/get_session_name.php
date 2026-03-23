<?php
session_start();
echo json_encode(['name' => $_SESSION['first_name'] ?? 'User']);
?>