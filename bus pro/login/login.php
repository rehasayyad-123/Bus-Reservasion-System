<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        echo "<script>alert('Login successful'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Invalid credentials'); window.history.back();</script>";
    }
}
?>


