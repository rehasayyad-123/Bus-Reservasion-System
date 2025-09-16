<?php
// process_booking.php
include './db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $schedule_id = $_POST['schedule_id'] ?? '';
    $travel_date = $_POST['travel_date'] ?? '';
    $passenger_name = $_POST['passenger_name'] ?? '';
    $passenger_email = $_POST['passenger_email'] ?? '';
    $num_seats = $_POST['num_seats'] ?? '';
    $fare_per_seat = $_POST['fare_per_seat'] ?? '';

    // Basic validation
    if (empty($schedule_id) || empty($travel_date) || empty($passenger_name) || empty($passenger_email) || empty($num_seats) || !is_numeric($num_seats) || $num_seats <= 0) {
        die("Invalid input. Please go back and try again.");
    }

    // Check available seats again to prevent overbooking (important for concurrent bookings)
    $sql_check_seats = "SELECT
                            b.total_seats - COALESCE(SUM(CASE WHEN bo.booking_date = ? THEN bo.num_seats ELSE 0 END), 0) AS available_seats
                        FROM
                            schedules s
                        JOIN
                            buses b ON s.bus_id = b.bus_id
                        LEFT JOIN
                            bookings bo ON s.schedule_id = bo.schedule_id AND bo.booking_status = 'confirmed'
                        WHERE
                            s.schedule_id = ?
                        GROUP BY
                            b.total_seats";
    $stmt_check = $conn->prepare($sql_check_seats);
    $stmt_check->bind_param("si", $travel_date, $schedule_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();
    $available_seats = $row_check['available_seats'] ?? 0;
    $stmt_check->close();

    if ($num_seats > $available_seats) {
        echo "<p class='error-message'>Sorry, only " . htmlspecialchars($available_seats) . " seats are available for this bus on " . htmlspecialchars($travel_date) . ".</p>";
        echo "<p><a href='index.php'>Go Back to Search</a></p>";
        $conn->close();
        exit();
    }

    // Insert booking into database
    $sql_insert = "INSERT INTO bookings (schedule_id, booking_date, num_seats, passenger_name, passenger_email, booking_status)
                   VALUES (?, ?, ?, ?, ?, 'confirmed')";

    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("isiss", $schedule_id, $travel_date, $num_seats, $passenger_name, $passenger_email);

    if ($stmt_insert->execute()) {
        $booking_id = $conn->insert_id;
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Booking Confirmation</title>
            <link rel="stylesheet" href="./style.css">
        </head>
        <body>
            <div class="container">
                <h2>Booking Confirmed!</h2>
                <p class="success-message">Your booking has been successfully confirmed.</p>
                <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($booking_id); ?></p>
                <p><strong>Passenger Name:</strong> <?php echo htmlspecialchars($passenger_name); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($passenger_email); ?></p>
                <p><strong>Number of Seats:</strong> <?php echo htmlspecialchars($num_seats); ?></p>
                <p><strong>Total Fare:</strong> ₹<?php echo htmlspecialchars($fare_per_seat * $num_seats); ?></p>
                <p><a href="pyment/pyment.html">Pyment</a></p>
                <p>You will receive a confirmation email shortly.</p>
                <p><a href="./index.php">Book Another Ticket</a></p>
            </div>
        </body>
        </html>
        <?php
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Booking Error</title>
            <link rel="stylesheet" href="./style.css">
        </head>
        <body>
            <div class="container">
                <h2 class="error-message">Booking Failed!</h2>
                <p class="error-message">There was an error processing your booking. Please try again.</p>
               
             <p class="error-message">Error: <?php echo $stmt_insert->error; ?></p>
             
             <p><a href="./index.php">Go Back to Search</a></p>
            </div>

            
}
        </body>
        </html>
        <?php
    }

    $stmt_insert->close();
    $conn->close();
} else {
    header('Location: index.php'); // Redirect if accessed directly
    exit();
}

?>
