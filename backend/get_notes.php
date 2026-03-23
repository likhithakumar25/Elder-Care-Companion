<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli('localhost', 'root', '', 'elderly_carenew');
$conn->set_charset('utf8mb4');

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if (!$user_id || !in_array($role, ['caregiver', 'family', 'elder'])) {
  echo json_encode([]);
  exit;
}

$elder_ids = [];

if ($role === 'elder') {
  // Elder sees their own notes
  $elder_ids[] = $user_id;
} else {
  // Caregiver or family sees notes for related elders
  $sql = "SELECT elder_id FROM relationships WHERE related_user_id = ? AND role = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("is", $user_id, $role);
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $elder_ids[] = $row['elder_id'];
  }
}

if (empty($elder_ids)) {
  echo json_encode([]);
  exit;
}

$id_list = implode(',', array_map('intval', $elder_ids));
$query = "SELECT elder_id, note, created_at FROM caregiver_notes WHERE elder_id IN ($id_list) ORDER BY created_at DESC";
$res = $conn->query($query);

$notes = [];
while ($row = $res->fetch_assoc()) {
  $notes[] = $row;
}

echo json_encode($notes);
?>