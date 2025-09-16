//<?php
// db_connect.php

//$servername = "localhost"; // Usually localhost
//$username = "root";        // Your MySQL username
//$password = "";            // Your MySQL password (often empty for XAMPP/WAMP)
//$dbname = "bus_reservation"; // The database you created

// Create connection
//$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
//if ($conn->connect_error) {
//    die("Connection failed: " . $conn->connect_error);
//}
// echo "Connected successfully"; // Uncomment for testing connection
//?>

<?php
$host="localhost";
$username="root";
$password=null;
$database="bus_reservation";

$conn = new mysqli($host,$username,$password,$database);
if($conn->connect_error){
    die("some error".$conn->connect_error);
}
echo"connection success";
echo"<br>";
