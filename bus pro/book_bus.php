<?php
// book_bus.php
include './db_connect.php';

$schedule_id = $_POST['schedule_id'] ?? '';
$travel_date = $_POST['travel_date'] ?? '';

if (empty($schedule_id) || empty($travel_date)) {
    header('Location: index.php');
    exit();
}

// Fetch bus details for display
$sql_details = "SELECT
                    s.schedule_id,
                    b.bus_name,
                    b.bus_number,
                    r.origin,
                    r.destination,
                    s.departure_time,
                    s.arrival_time,
                    s.fare,
                    b.total_seats - COALESCE(SUM(CASE WHEN bo.booking_date = ? THEN bo.num_seats ELSE 0 END), 0) AS available_seats
                FROM
                    schedules s
                JOIN
                    buses b ON s.bus_id = b.bus_id
                JOIN
                    routes r ON s.route_id = r.route_id
                LEFT JOIN
                    bookings bo ON s.schedule_id = bo.schedule_id AND bo.booking_status = 'confirmed'
                WHERE
                    s.schedule_id = ?
                GROUP BY
                    s.schedule_id, b.bus_name, b.bus_number, r.origin, r.destination, s.departure_time, s.arrival_time, s.fare";

$stmt_details = $conn->prepare($sql_details);
$stmt_details->bind_param("si", $travel_date, $schedule_id);
$stmt_details->execute();
$result_details = $stmt_details->get_result();
$bus_details = $result_details->fetch_assoc();

if (!$bus_details) {
    echo "<p class='error-message'>Bus details not found or no seats available.</p>";
    echo "<p><a href='index.php'>Go Back to Search</a></p>";
    exit();
}
$stmt_details->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Bus Ticket</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <div class="container">
        <h2>Book Your Ticket</h2>
        <h3>Bus Details:</h3>
        <p><strong>Bus Name:</strong> <?php echo htmlspecialchars($bus_details['bus_name']); ?></p>
        <p><strong>Bus Number:</strong> <?php echo htmlspecialchars($bus_details['bus_number']); ?></p>
        <p><strong>Route:</strong> <?php echo htmlspecialchars($bus_details['origin']); ?> to <?php echo htmlspecialchars($bus_details['destination']); ?></p>
        <p><strong>Departure:</strong> <?php echo htmlspecialchars($bus_details['departure_time']); ?></p>
        <p><strong>Arrival:</strong> <?php echo htmlspecialchars($bus_details['arrival_time']); ?></p>
        <p><strong>Fare per seat:</strong> ₹<?php echo htmlspecialchars($bus_details['fare']); ?></p>
        <p><strong>Available Seats:</strong> <?php echo htmlspecialchars($bus_details['available_seats']); ?></p>
        <p><strong>Date of Travel:</strong> <?php echo htmlspecialchars($travel_date); ?></p>

        <form action="./process_booking.php" method="POST">
            <input type="hidden" name="schedule_id" value="<?php echo htmlspecialchars($schedule_id); ?>">
            <input type="hidden" name="travel_date" value="<?php echo htmlspecialchars($travel_date); ?>">
            <input type="hidden" name="fare_per_seat" value="<?php echo htmlspecialchars($bus_details['fare']); ?>">

            <div class="form-group">
                <label for="passenger_name">Your Name:</label>
                <input type="text" id="passenger_name" name="passenger_name" required>
            </div>
            <div class="form-group">
                <label for="passenger_email">Your Email:</label>
                <input type="email" id="passenger_email" name="passenger_email" required>
            </div>
            <div class="form-group">
                <label for="num_seats">Number of Seats:</label>
                <input type="number" id="num_seats" name="num_seats" min="1" max="<?php echo htmlspecialchars($bus_details['available_seats']); ?>" required>
            </div>
            <button type="submit">Confirm Booking</button>
        </form>

        <p><a href="./search_results.php?php echo urlencode($bus_details['origin']); ?>&destination=<?php echo urlencode($bus_details['destination']); ?>&travel_date=<?php echo urlencode($travel_date); ?>">Go Back to Results</a></p>
    </div>
</body>
</html>
<?php
$conn->close();
?>