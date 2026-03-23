<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'elderly_carenew');
$data = json_decode(file_get_contents("php://input"), true);

// Extract fields
$role = $data['role'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$first_name = $data['first_name'];
$last_name = $data['last_name'];
$age = $data['age'];
$contact = $data['contact'];
$address = $data['address'];
$elder_email = $data['elder_email'] ?? null;

// Insert user
$stmt = $conn->prepare("INSERT INTO users (role, email, password, first_name, last_name, age, contact, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $role, $email, $password, $first_name, $last_name, $age, $contact, $address);
$stmt->execute();
$new_user_id = $stmt->insert_id;

// Link caregiver/family to elder using elder email
if (($role === 'caregiver' || $role === 'family') && $elder_email) {
  $find = $conn->prepare("SELECT id FROM users WHERE email = ? AND role = 'elder'");
  $find->bind_param("s", $elder_email);
  $find->execute();
  $result = $find->get_result();
  if ($row = $result->fetch_assoc()) {
    $elder_id = $row['id'];
    $link = $conn->prepare("INSERT INTO relationships (elder_id, related_user_id, role) VALUES (?, ?, ?)");
    $link->bind_param("iis", $elder_id, $new_user_id, $role);

    $link->execute();
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Elder email not found']);
    exit;
  }
}

// Success
echo json_encode(['status' => 'success']);
?>