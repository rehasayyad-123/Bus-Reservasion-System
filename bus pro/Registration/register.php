<?php
header('Content-Type: application/json');


//$host="localhost";
//$username="root";
//$password=null;
//$database="bus_reservation";

//$conn = new mysqli($host,$username,$password,$database);
//if($conn->connect_error)


// Database connection details
$host = 'localhost';
$db   = 'user_registration';
$user = 'root';
$pass = 'null'; // Your MySQL password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

// Get the raw POST data from the front-end
$input = json_decode(file_get_contents('php://input'), true);

// Server-side validation
if (!isset($input['fullname'], $input['gender'], $input['dob'], $input['mobile'], $input['email'], $input['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

$fullname = $input['fullname'];
$gender = $input['gender'];
$dob = $input['dob'];
$mobile = $input['mobile'];
$email = $input['email'];
$password = $input['password'];

// Hash the password securely
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if the email already exists to prevent duplicate accounts
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already registered.']);
        exit();
    }

    // Insert the new user into the database
    $stmt = $pdo->prepare("INSERT INTO users (fullname, gender, dob, mobile, email, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fullname, $gender, $dob, $mobile, $email, $hashed_password]);

    echo json_encode(['success' => true, 'message' => 'Registration successful!']);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
    error_log($e->getMessage()); // Log the error for debugging
}