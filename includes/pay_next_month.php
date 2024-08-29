<?php
session_start(); // Ensure session is started
include 'dbh.inc.php'; // Include your database connection file

// Read POST data
$data = json_decode(file_get_contents('php://input'), true);
$orderID = $data['orderID'];
$username = $_SESSION['username']; // Ensure you have session started

try {
    // PayPal credentials
    $paypalClientId = 'AWMHbixiflAFoFCPWHvEpXz8hcqJvwMxwEnObijMCUif6Q3csc-LSJ7am9BhxoGGYkBrlaOstBTuhlqd';
    $paypalSecret = 'EGZLY4npKs0G9vbN31cQlOLrbB7JnvoirHsD2EDu-37GaAixe1ls9UlMVrcCDhBziOdOYDxpXneHZR66';
    $paypalApiUrl = 'https://api-m.sandbox.paypal.com'; // Use sandbox URL for testing

    // Verify payment details with PayPal API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$paypalApiUrl/v2/checkout/orders/$orderID");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic " . base64_encode("$paypalClientId:$paypalSecret"),
        "Content-Type: application/json"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $order = json_decode($response, true);

    if ($order['status'] === 'COMPLETED') {
        // Update booking in the database
        $updateBookingQuery = "UPDATE bookings SET booking_date = DATE_ADD(booking_date, INTERVAL 1 MONTH) WHERE userN = :username";
        $updateBookingStmt = $pdo->prepare($updateBookingQuery);
        $updateBookingStmt->execute(['username' => $username]);

        if ($updateBookingStmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update booking']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Payment verification failed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
