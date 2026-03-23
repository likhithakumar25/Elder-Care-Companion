<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

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

$name = $data['contact_name'] ?? '';
$phone = $data['phone'] ?? '';

if ($name && $phone) {
  $stmt = $conn->prepare("INSERT INTO sos_contacts (user_id, contact_name, phone) VALUES (?, ?, ?)");
  $stmt->bind_param("iss", $user_id, $name, $phone);
  if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Insert failed"]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Missing fields"]);
}
?>