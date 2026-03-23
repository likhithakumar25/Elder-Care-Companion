<?php
session_start();
header('Content-Type: application/json; charset=utf-8'); // ✅ Add charset for clean output

$conn = new mysqli('localhost', 'root', '', 'elderly_carenew');
if ($conn->connect_error) {
  echo json_encode([]);
  exit;
}

$conn->set_charset('utf8mb4'); // ✅ Ensures proper character encoding

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if (!$user_id || $role !== 'elder') {
  echo json_encode([]);
  exit;
}

$stmt = $conn->prepare("SELECT bp, sugar, heart_rate, recorded_at FROM vitals WHERE user_id = ? ORDER BY recorded_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$vitals = [];
while ($row = $result->fetch_assoc()) {
  $vitals[] = $row;
}

echo json_encode($vitals);
?>