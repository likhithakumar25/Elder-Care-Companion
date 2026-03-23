<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli('localhost', 'root', '', 'elderly_carenew');
if ($conn->connect_error) {
  echo json_encode([]);
  exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if (!$user_id || $role !== 'elder') {
  echo json_encode([]);
  exit;
}

$result = $conn->query("SELECT title, date, time FROM appointments WHERE user_id = $user_id ORDER BY date, time");

$appointments = [];
while ($row = $result->fetch_assoc()) {
  $appointments[] = $row;
}
echo json_encode($appointments);
?>