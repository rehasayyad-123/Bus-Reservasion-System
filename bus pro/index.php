<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Reservation System</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>

    <div class="container">

    <header class="navbar">
        <a href="./Home.html">Home</a> 
       <style>
       .navbar{
  text-align: center;
  background-color: aqua;
  font-weight: 600;
  font-size: x-large;
}
</style>
    </header>

        <h1>Welcome to Bus Reservation</h1>
        <form action="./search_results.php" method="GET">
            <div class="form-group">
                <label for="origin">Origin:</label>
                <input type="text" id="origin" name="origin" required>
            </div>
            <div class="form-group">
                <label for="destination">Destination:</label>
                <input type="text" id="destination" name="destination" required>
            </div>
            <div class="form-group">
                <label for="date">Date of Travel:</label>
                <input type="date" id="date" name="travel_date" required>
            </div>
            <button type="submit">Search Buses</button>
        </form>
    </div>
</body>
</html>