<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'elderly_carenew');
if ($conn->connect_error) {
  echo json_encode(["status" => "error", "message" => "DB connection failed"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$med_id = $data['id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if (!$user_id || $role !== 'elder' || !$med_id) {
  echo json_encode(["status" => "error", "message" => "Unauthorized or missing ID"]);
  exit;
}

$stmt = $conn->prepare("UPDATE medications SET status = 'taken' WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $med_id, $user_id);
if ($stmt->execute()) {
  echo json_encode(["status" => "success"]);
} else {
  echo json_encode(["status" => "error", "message" => "Update failed"]);
}
?>