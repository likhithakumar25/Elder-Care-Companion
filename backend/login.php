<?php
session_start();
header("Content-Type: application/json");

$conn = new mysqli('localhost', 'root', '', 'elderly_carenew');
if ($conn->connect_error) {
    echo json_encode(['status'=>'error','message'=>'Database connection failed']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$role = $data['role'] ?? '';

if(empty($email) || empty($password) || empty($role)){
    echo json_encode(['status'=>'error','message'=>'All fields are required']);
    exit;
}

// Fetch user by email
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()){
    if(password_verify($password, $row['password'])){
        // Check role
        if($row['role'] !== $role){
            echo json_encode(['status'=>'error','message'=>'Role mismatch. Select correct role.']);
            exit;
        }

        // Set session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['first_name'] = $row['first_name'];

        echo json_encode([
            'status' => 'success',
            'first_name' => $row['first_name'],
            'role' => $row['role']
        ]);
        exit;
    }
}

echo json_encode(['status'=>'error','message'=>'Invalid credentials']);
?>
