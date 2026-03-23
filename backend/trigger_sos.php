<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$conn = new mysqli('localhost', 'root', '', 'elderly_carenew');
if ($conn->connect_error) {
  echo json_encode(["status" => "error", "message" => "DB connection failed"]);
  exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if (!$user_id || $role !== 'elder') {
  echo json_encode(["status" => "error", "message" => "Unauthorized"]);
  exit;
}

$result = $conn->query("SELECT contact_name, phone FROM sos_contacts WHERE user_id = $user_id");

$contacts = [];
while ($row = $result->fetch_assoc()) {
  $contacts[] = $row;
}

echo json_encode([
  "status" => "success",
  "message" => "SOS triggered",
  "contacts" => $contacts
]);
?>