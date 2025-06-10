<?php
use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// DB config
$host = "localhost";
$db = "bus_booking";
$user = "root";
$pass = "";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Form data
$origin       = $_POST['origin'] ?? '';
$destination  = $_POST['destination'] ?? '';
$travel_date  = $_POST['travel_date'] ?? '';
$travel_time  = $_POST['travel_time'] ?? '';
$seats        = intval($_POST['seats'] ?? 1);
$username     = $_POST['username'] ?? '';
$email        = $_POST['email'] ?? '';

// Validate required fields
if (!$origin || !$destination || !$travel_date || !$travel_time || !$seats || !$username || !$email) {
    die("All fields are required.");
}

// Debug: Log user email
file_put_contents("email_log.txt", "User: $username | Email: $email\n", FILE_APPEND);

// Route key
$route_key = $origin . '_' . $destination . '_' . $travel_date;

// Seat limit check
$limit_sql = "SELECT total_seats FROM seat_limits WHERE route_key = ?";
$stmt = $conn->prepare($limit_sql);
$stmt->bind_param("s", $route_key);
$stmt->execute();
$limit_result = $stmt->get_result();
$limit_data = $limit_result->fetch_assoc();
$total_seats = $limit_data['total_seats'] ?? 40;

$booked_sql = "SELECT SUM(seats) as booked FROM bookings WHERE origin=? AND destination=? AND travel_date=?";
$stmt2 = $conn->prepare($booked_sql);
$stmt2->bind_param("sss", $origin, $destination, $travel_date);
$stmt2->execute();
$result2 = $stmt2->get_result();
$row2 = $result2->fetch_assoc();
$already_booked = $row2['booked'] ?? 0;

if ($already_booked + $seats > $total_seats) {
    die("Sorry, only " . ($total_seats - $already_booked) . " seats are available.");
}

// Insert booking
$insert_sql = "INSERT INTO bookings (origin, destination, travel_date, travel_time, seats, username, email)
               VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt3 = $conn->prepare($insert_sql);
$stmt3->bind_param("ssssiss", $origin, $destination, $travel_date, $travel_time, $seats, $username, $email);

if ($stmt3->execute()) {
    $id = $stmt3->insert_id;

    // ✅ Generate PDF Ticket
    ob_start();
    include 'ticket_template.php';
    $html = ob_get_clean();

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Save PDF
    if (!is_dir('tickets')) {
        mkdir('tickets', 0755, true);
    }
    $pdfOutput = $dompdf->output();
    $pdfPath = "tickets/ticket_$id.pdf";
    file_put_contents($pdfPath, $pdfOutput);

    // ✅ Send Email to User with PDF
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'blazebus787@gmail.com';     // your Gmail
        $mail->Password   = 'naqp rcfr fyjp gyhl';        // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('blazebus787@gmail.com', 'BlazeBus');
        $mail->addAddress($email, $username); // User's email
        $mail->addAttachment($pdfPath, "BlazeBus_Ticket_$id.pdf");

        $mail->isHTML(true);
        $mail->Subject = 'Your BlazeBus Ticket';
        $mail->Body    = "Hi <strong>$username</strong>,<br><br>Your ticket is confirmed!<br>Please find your BlazeBus ticket PDF attached.<br><br>Thank you for choosing BlazeBus.";

        $mail->send();
    } catch (Exception $e) {
        echo "❌ Failed to send ticket to user email. Error: {$mail->ErrorInfo}";
    }

    // ✅ Send Booking Alert to Admin
    try {
        $adminMail = new PHPMailer(true);
        $adminMail->isSMTP();
        $adminMail->Host       = 'smtp.gmail.com';
        $adminMail->SMTPAuth   = true;
        $adminMail->Username   = 'blazebus787@gmail.com';  // same Gmail
        $adminMail->Password   = 'naqp rcfr fyjp gyhl';
        $adminMail->SMTPSecure = 'tls';
        $adminMail->Port       = 587;

        $adminMail->setFrom('blazebus787@gmail.com', 'BlazeBus');
        $adminMail->addAddress('blazebus787@gmail.com', 'Admin'); // Admin email

        $adminMail->isHTML(true);
        $adminMail->Subject = 'New Booking Received';
        $adminMail->Body = "
            <strong>New Booking Details:</strong><br><br>
            <strong>Name:</strong> $username<br>
            <strong>Email:</strong> $email<br>
            <strong>Route:</strong> $origin → $destination<br>
            <strong>Date:</strong> $travel_date<br>
            <strong>Time:</strong> $travel_time<br>
            <strong>Seats:</strong> $seats<br>
            <strong>Ticket ID:</strong> $id
        ";

        $adminMail->send();
    } catch (Exception $e) {
        echo "❌ Failed to send admin email. Error: {$adminMail->ErrorInfo}";
    }

    echo "<script>alert('Booking successful! Ticket has been emailed.'); window.location.href='index.html';</script>";
} else {
    echo "Booking failed: " . $stmt3->error;
}

$conn->close();

header("Location: thankyou.html");
exit();
?>
