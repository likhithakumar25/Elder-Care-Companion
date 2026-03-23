<?php
session_start();
header('Content-Type: application/json');

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

$result = $conn->query("SELECT id, name, time, status FROM medications WHERE user_id = $user_id ORDER BY time");

$medications = [];
while ($row = $result->fetch_assoc()) {
  $medications[] = $row;
}
echo json_encode($medications);
?>