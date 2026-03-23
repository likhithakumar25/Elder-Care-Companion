<?php
session_start();
header('Content-Type: application/json');

// DB connection
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

$title = $data['title'] ?? '';
$date = $data['date'] ?? '';
$time = $data['time'] ?? '';

if ($title && $date && $time) {
  $stmt = $conn->prepare("INSERT INTO appointments (user_id, title, date, time) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("isss", $user_id, $title, $date, $time);
  if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Insert failed"]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Missing fields"]);
}
?>