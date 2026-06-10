<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is admin
if(!isset($_SESSION['login_id']) || $_SESSION['login_type'] != 1){
    header('location:login.php');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if(!$id){
    echo "Usage: reset_user.php?id=USER_ID";
    echo "<br><a href='index.php?page=user_list'>Back to User List</a>";
    exit;
}

// Verify the user exists
$user_check = $conn->query("SELECT id, email FROM users WHERE id = $id");
if($user_check->num_rows == 0){
    echo "User not found.";
    exit;
}
$user = $user_check->fetch_assoc();

$email = $user['email'];
$password_plain = 'password123';
$hash = md5($password_plain); // Note: Using MD5 is insecure, should use password_hash

$stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
$stmt->bind_param('si', $hash, $id);
if($stmt->execute()) {
    echo "User id=$id ($email) password reset to: $password_plain";
    echo "<br><a href='index.php?page=user_list'>Back to User List</a>";
} else {
    echo "Error: ".$conn->error;
}
?>