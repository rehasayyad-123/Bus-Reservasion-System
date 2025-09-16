<?php
// search_results.php
include 'db_connect.php';

$origin = $_GET['origin'] ?? '';
$destination = $_GET['destination'] ?? '';
$travel_date = $_GET['travel_date'] ?? '';

$sql = "SELECT
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
            r.origin LIKE ? AND r.destination LIKE ?
        GROUP BY
            s.schedule_id, b.bus_name, b.bus_number, r.origin, r.destination, s.departure_time, s.arrival_time, s.fare
        HAVING
            available_seats > 0"; // Only show buses with available seats

$stmt = $conn->prepare($sql);
$search_origin = '%' . $origin . '%';
$search_destination = '%' . $destination . '%';
$stmt->bind_param("sss", $travel_date, $search_origin, $search_destination);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Available Buses from <?php echo htmlspecialchars($origin); ?> to <?php echo htmlspecialchars($destination); ?> on <?php echo htmlspecialchars($travel_date); ?></h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="bus-table">
                <thead>
                    <tr>
                        <th>Bus Name</th>
                        <th>Bus Number</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                        <th>Fare</th>
                        <th>Available Seats</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['bus_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['bus_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['departure_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['arrival_time']); ?></td>
                            <td>₹<?php echo htmlspecialchars($row['fare']); ?></td>
                            <td><?php echo htmlspecialchars($row['available_seats']); ?></td>
                            <td>
                                <form action="book_bus.php" method="POST">
                                    <input type="hidden" name="schedule_id" value="<?php echo htmlspecialchars($row['schedule_id']); ?>">
                                    <input type="hidden" name="travel_date" value="<?php echo htmlspecialchars($travel_date); ?>">
                                    <button type="submit">Book Now</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="error-message">No buses found for your search criteria.</p>
        <?php endif; ?>

        <p><a href="index.php">Go Back to Search</a></p>
    </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>