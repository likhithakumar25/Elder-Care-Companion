<?php
session_start();
header('Content-Type: application/json');
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
$query = "SELECT user_id, bp, sugar, heart_rate, recorded_at FROM vitals WHERE user_id IN ($id_list)";
$res = $conn->query($query);

$vitals = [];
while ($row = $res->fetch_assoc()) {
  $vitals[] = [
    'elder_id' => $row['user_id'], // ✅ rename for frontend
    'bp' => $row['bp'],
    'sugar' => $row['sugar'],
    'heart_rate' => $row['heart_rate'],
    'recorded_at' => $row['recorded_at']
  ];
}

echo json_encode($vitals);
?>