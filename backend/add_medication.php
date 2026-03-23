<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'elderly_carenew');
if ($conn->connect_error) {
  echo json_encode(['status' => 'error', 'message' => 'DB connection failed']);
  exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';
if (!$user_id || !in_array($role, ['elder'])) {
  echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
  exit;
}

// ✅ Decode JSON input
$input = json_decode(file_get_contents('php://input'), true);
$name = $input['name'] ?? '';
$timeLabel = $input['time'] ?? '';
$timeMap = [
  'Morning' => '08:00:00',
  'Afternoon' => '13:00:00',
  'Evening' => '19:00:00'
];
$time = $timeMap[$timeLabel] ?? '00:00:00'; // fallback if invalid
$status = $input['status'] ?? '';

if (!$name || !$time || !$status) {
  echo json_encode(['status' => 'error', 'message' => 'Missing or invalid fields']);
  exit;
}

// ✅ Insert into medications table
$stmt = $conn->prepare("INSERT INTO medications (user_id, name, time, status) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $user_id, $name, $time, $status);
if ($stmt->execute()) {
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Insert failed']);
}
?>