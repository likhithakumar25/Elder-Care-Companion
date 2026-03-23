<?php
session_start();
header('Content-Type: application/json; charset=utf-8'); // ✅ Fix encoding

$conn = new mysqli('localhost', 'root', '', 'elderly_carenew');
$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';
if (!$user_id || !in_array($role, ['caregiver', 'family'])) {
  echo json_encode([]);
  exit;
}

$sql = "SELECT elder_id FROM relationships WHERE related_user_id = ? AND role = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $role);
$stmt->execute();
$result = $stmt->get_result();

$elder_ids = [];
while ($row = $result->fetch_assoc()) {
  $elder_ids[] = $row['elder_id'];
}

if (empty($elder_ids)) {
  echo json_encode([]);
  exit;
}

$id_list = implode(',', array_map('intval', $elder_ids));
$query = "SELECT user_id, name, time, status FROM medications WHERE user_id IN ($id_list)";
$res = $conn->query($query);

$medications = [];
while ($row = $res->fetch_assoc()) {
  $medications[] = [
    'elder_id' => $row['user_id'], // ✅ rename for frontend
    'name' => $row['name'],
    'time' => $row['time'],
    'status' => $row['status']
  ];
}

echo json_encode($medications);
?>