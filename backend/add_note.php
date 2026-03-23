<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli('localhost', 'root', '', 'elderly_carenew');
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
  echo json_encode(["status" => "error", "message" => "DB connection failed"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$caregiver_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if (!$caregiver_id || $role !== 'caregiver') {
  echo json_encode(["status" => "error", "message" => "Unauthorized"]);
  exit;
}

$elder_id = $data['elder_id'] ?? null;
$note = $data['note'] ?? '';

if ($elder_id && $note) {
  $stmt = $conn->prepare("INSERT INTO caregiver_notes (caregiver_id, elder_id, note, created_at) VALUES (?, ?, ?, NOW())");
  $stmt->bind_param("iis", $caregiver_id, $elder_id, $note);
  if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Insert failed"]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Missing fields"]);
}
?>