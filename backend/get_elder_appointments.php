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

if (!$user_id || !in_array($role, ['caregiver', 'family'])) {
  echo json_encode([]);
  exit;
}

$linked = $conn->query("SELECT elder_id FROM relationships WHERE related_user_id = $user_id AND role = '$role'");
$elder_ids = [];
while ($row = $linked->fetch_assoc()) {
  $elder_ids[] = $row['elder_id'];
}

if (empty($elder_ids)) {
  echo json_encode([]);
  exit;
}

$id_list = implode(',', array_map('intval', $elder_ids));
$result = $conn->query("SELECT user_id AS elder_id, title, date, time FROM appointments WHERE user_id IN ($id_list) ORDER BY date, time");

$appointments = [];
while ($row = $result->fetch_assoc()) {
  $appointments[] = $row;
}
echo json_encode($appointments);
?>