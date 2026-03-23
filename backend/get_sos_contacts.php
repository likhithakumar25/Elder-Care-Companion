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

if (!$user_id) {
  echo json_encode([]);
  exit;
}

$elder_ids = [];

if ($role === 'elder') {
  $elder_ids[] = $user_id;
} elseif (in_array($role, ['caregiver', 'family'])) {
  $linked = $conn->query("SELECT elder_id FROM relationships WHERE related_user_id = $user_id AND role = '$role'");
  while ($row = $linked->fetch_assoc()) {
    $elder_ids[] = $row['elder_id'];
  }
}

if (empty($elder_ids)) {
  echo json_encode([]);
  exit;
}

$id_list = implode(',', array_map('intval', $elder_ids));
$result = $conn->query("SELECT user_id AS elder_id, contact_name, phone FROM sos_contacts WHERE user_id IN ($id_list)");

$contacts = [];
while ($row = $result->fetch_assoc()) {
  $contacts[] = $row;
}
echo json_encode($contacts);
?>