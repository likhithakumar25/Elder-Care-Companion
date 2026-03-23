<?php
session_start();
echo json_encode([
  'user_id' => $_SESSION['user_id'] ?? null,
  'role' => $_SESSION['role'] ?? null
]);
?>