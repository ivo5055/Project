<?php
session_start();
include 'includes/dbh.inc.php';
include 'elements/header.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$room_number = $_GET['room_number'];
$building = $_GET['building'];
$username = $_SESSION['username'];

// Retrieve user details from the database
$userId = $_SESSION['Id'];
$queryUserDetails = "SELECT full_name, fn, egn FROM users WHERE Id = ?";
$stmtUserDetails = $pdo->prepare($queryUserDetails);
$stmtUserDetails->execute([$userId]);
$userDetails = $stmtUserDetails->fetch(PDO::FETCH_ASSOC);

$fn = $userDetails['fn'];
$editable = $fn == 0 ? '' : 'readonly';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $fn = $_POST['fn'];
    $egn = $_POST['egn'];
    $payment_method = $_POST['payment_method'];

    // Verify if the fn and egn are registered to another account
    $checkFnEgnQuery = "SELECT COUNT(*) FROM users WHERE fn = :fn AND egn = :egn AND Id != :userId";
    $checkFnEgnStmt = $pdo->prepare($checkFnEgnQuery);
    $checkFnEgnStmt->execute(['fn' => $fn, 'egn' => $egn, 'userId' => $userId]);
    $isFnEgnRegistered = $checkFnEgnStmt->fetchColumn();

    if ($isFnEgnRegistered > 0) {
        $error = "Student Information registered for other account.";
    } else {
        // Verify the user's details
        $verifyQuery = "SELECT COUNT(*) FROM students_db WHERE fn = :fn AND egn = :egn";
        $verifyStmt = $pdo->prepare($verifyQuery);
        $verifyStmt->execute(['fn' => $fn, 'egn' => $egn]);
        $isStudentValid = $verifyStmt->fetchColumn();

        if ($isStudentValid) {
            // Update the user's record in the "users" table with the new details
            $updateUserQuery = "UPDATE users SET full_name = :fullname, fn = :fn, egn = :egn WHERE Id = :userId";
            $updateUserStmt = $pdo->prepare($updateUserQuery);
            $updateUserStmt->execute(['fullname' => $fullname, 'fn' => $fn, 'egn' => $egn, 'userId' => $userId]);
        
            // Check if the user already booked a room
            $checkBookingQuery = "SELECT COUNT(*) FROM bookings WHERE userN = :username";
            $checkBookingStmt = $pdo->prepare($checkBookingQuery);
            $checkBookingStmt->execute(['username' => $username]);
            $userBookingCount = $checkBookingStmt->fetchColumn();
        
            if ($userBookingCount > 0) {
                $error = "You have already booked a room. You can only book one room at a time.";
            } else {
                // Check current bookings for this room
                $checkRoomBookingQuery = "SELECT COUNT(*) FROM bookings WHERE room_number = :room_number AND building = :building";
                $checkRoomBookingStmt = $pdo->prepare($checkRoomBookingQuery);
                $checkRoomBookingStmt->execute(['room_number' => $room_number, 'building' => $building]);
                $currentRoomBookings = $checkRoomBookingStmt->fetchColumn();

                // Fetch room capacity
                $roomQuery = "SELECT room_capacity FROM room WHERE room_number = :room_number AND building = :building";
                $roomStmt = $pdo->prepare($roomQuery);
                $roomStmt->execute(['room_number' => $room_number, 'building' => $building]);
                $roomCapacity = $roomStmt->fetchColumn();

                if ($currentRoomBookings < $roomCapacity) {
                    // Book the room
                    $bookingQuery = "INSERT INTO bookings (userN, building, room_number) VALUES (:userN, :building, :room_number)";
                    $bookingStmt = $pdo->prepare($bookingQuery);
                    $bookingStmt->execute(['userN' => $username, 'building' => $building, 'room_number' => $room_number]);

                    $success = "Room booked successfully!";
                    
                    // Redirect to offers.php
                    header("Location: offers.php");
                    exit();
                } else {
                    $error = "This room is fully booked. Please choose another room.";
                }
            }
        } else {
            $error = "Invalid Data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Room</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>

<div class="booking-form">
    <h1>Enter your details</h1>
    <?php
    if (isset($error)) {
        echo '<p class="error">' . htmlspecialchars($error) . '</p>';
    }
    if (isset($success)) {
        echo '<p class="success">' . htmlspecialchars($success) . '</p>';
    }
    ?>
    <form method="post" action="">
        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($userDetails['full_name']); ?>" <?php echo $editable; ?> required>
        <p></p>

        <label for="fn">Faculty Number (FN):</label>
        <input type="text" id="fn" name="fn" value="<?php echo htmlspecialchars($userDetails['fn']); ?>" <?php echo $editable; ?> required>
        <p></p>

        <label for="egn">EGN:</label>
        <input type="text" id="egn" name="egn" value="" required>
        <p></p>

        <label for="payment_method">Payment Method:</label>
        <select id="payment_method" name="payment_method" required>
            <option value="credit_card">Credit Card</option>
            <option value="paypal">PayPal</option>
            <option value="bank_transfer">Bank Transfer</option>
        </select>
        <p></p>

        <button type="submit">Book</button>
    </form>
</div>

</body>
</html>
