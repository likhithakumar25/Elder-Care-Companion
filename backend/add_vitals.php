<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'elderly_carenew');
if ($conn->connect_error) {
  echo json_encode(["status" => "error", "message" => "DB connection failed"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if (!$user_id || $role !== 'elder') {
  echo json_encode(["status" => "error", "message" => "Unauthorized"]);
  exit;
}

$bp = $data['bp'] ?? '';
$sugar = $data['sugar'] ?? '';
$heart_rate = $data['heart_rate'] ?? '';

if ($bp && $sugar && $heart_rate) {
  $stmt = $conn->prepare("INSERT INTO vitals (user_id, bp, sugar, heart_rate, recorded_at) VALUES (?, ?, ?, ?, NOW())");
  $stmt->bind_param("isss", $user_id, $bp, $sugar, $heart_rate);
  if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Insert failed"]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Missing fields"]);
}
?>