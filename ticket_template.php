 <?php
$booking = [
  'id' => $id ?? 'N/A',
  'username' => $username ?? 'Guest',
  'email' => $email ?? 'Not provided',
  'origin' => $origin ?? '',
  'destination' => $destination ?? '',
  'travel_date' => $travel_date ?? '',
  'travel_time' => $travel_time ?? '',
  'seats' => $seats ?? 1
];
?> 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BlazeBus Ticket</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      padding: 20px;
      color: #333;
    }
    .ticket-container {
      max-width: 700px;
      margin: auto;
       border: 2px solid black; 
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0px 0px 8px rgb(119, 118, 118);
    }
    .header {
      text-align: center;
      background-color: #FFD700;
      padding: 15px;
      color: white;
      margin-bottom: 20px;
      border-radius: 15px;
    }
    .header img {
      width: 120px;
    }
    .header h1 {
      margin: 10px 0 0;
      color: white;
    }
    .header h2 {
      margin: 10px 0 0;
      color: white;
    }
    .section-title {
      font-weight: bold;
      font-size: 18px;
      margin-top: 25px;
      margin-bottom: 10px;
      color: #333;
      border-bottom: 1px solid #ddd;
      padding-bottom: 5px;
    }
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    .info-table td {
      padding: 10px;
      border: 1px solid #ccc;
      vertical-align: top;
    }
    .info-table th {
      background: #f8f8f8;
      padding: 10px;
      text-align: left;
      border: 1px solid #ccc;
    }
    .footer {
      text-align: center;
      margin-top: 30px;
      font-size: 12px;
      color: #777;
    }
  </style>
</head>
<body>
  <div class="ticket-container">
    <div class="header">
    <h2>BlazeBus</h2>
      <h1>Ticket Confirmation</h1>
    </div>

    <div class="section-title">Booking Details</div>
    <table class="info-table">
      <tr>
        <th>Ticket ID</th>
        <td><?= $booking['id'] ?></td>
      </tr>
      <tr>
        <th>Name</th>
        <td><?= $booking['username'] ?></td>
      </tr>
      <tr>
        <th>Email</th>
        <td><?= $booking['email'] ?></td>
      </tr>
      <tr>
        <th>From</th>
        <td><?= $booking['origin'] ?></td>
      </tr>
      <tr>
        <th>To</th>
        <td><?= $booking['destination'] ?></td>
      </tr>
      <tr>
        <th>Travel Date</th>
        <td><?= $booking['travel_date'] ?></td>
      </tr>
      <tr>
        <th>Time</th>
        <td><?= $booking['travel_time'] ?></td>
      </tr>
      <tr>
        <th>Seats</th>
        <td><?= $booking['seats'] ?></td>
      </tr>
    </table>

    <div class="footer">
      Thank you for booking with <span style="color: #ffc711; border-bottom: #333 solid 1px;">BlazeBus.</span> Wishing you a safe and comfortable journey!
    </div>
  </div>
</body>
</html>
