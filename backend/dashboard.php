<?php
session_start();
$role = $_SESSION['role'] ?? '';
if (!$role) {
  header("Location: ../frontend/login.html");
  exit;
}
switch ($role) {
  case 'elder':
    header("Location: ../frontend/dashboard_elder.html");
    break;
  case 'caregiver':
    header("Location: ../frontend/dashboard_caregiver.html");
    break;
  case 'family':
    header("Location: ../frontend/dashboard_family.html");
    break;
  default:
    header("Location: ../frontend/login.html");
}
exit;
?>