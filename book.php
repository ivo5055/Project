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
        $error = "Информацията за студента е регистрирана на друг акаунт.";
    } else {
        // Verify the user's details
        $verifyQuery = "SELECT COUNT(*) FROM students_db WHERE fn = :fn AND egn = :egn";
        $verifyStmt = $pdo->prepare($verifyQuery);
        $verifyStmt->execute(['fn' => $fn, 'egn' => $egn]);
        $isStudentValid = $verifyStmt->fetchColumn();

        if ($isStudentValid) {
            // Check if the user already requested a booking
            $checkBookingRequestQuery = "SELECT COUNT(*) FROM booking_requests WHERE userN = :username AND status = 'pending'";
            $checkBookingRequestStmt = $pdo->prepare($checkBookingRequestQuery);
            $checkBookingRequestStmt->execute(['username' => $username]);
            $userBookingRequestCount = $checkBookingRequestStmt->fetchColumn();

            if ($userBookingRequestCount > 0) {
                $error = "Вие вече сте подали заявка за стая. Можете да подадете само една заявка наведнъж.";
            } else {
                // Insert booking request
                $bookingRequestQuery = "INSERT INTO booking_requests (userN, building, room_number, fullname, fn, payment_method, status) 
                                        VALUES (:userN, :building, :room_number, :fullname, :fn, :payment_method, 'pending')";
                $bookingRequestStmt = $pdo->prepare($bookingRequestQuery);
                $bookingRequestStmt->execute([
                    'userN' => $username,
                    'building' => $building,
                    'room_number' => $room_number,
                    'fullname' => $fullname,
                    'fn' => $fn,
                    'payment_method' => $payment_method
                ]);

                $success = "Заявката за резервация беше изпратена успешно!";
                header("Location: offers.php");
                exit();
            }
        } else {
            $error = "Невалидни данни.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Резервиране на стая</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>

<div class="booking-form">
    <h1 data-translate="true">Въведете вашите данни</h1>
    <?php
    if (isset($error)) {
        echo '<p class="error" data-translate="true">' . htmlspecialchars($error) . '</p>';
    }
    if (isset($success)) {
        echo '<p class="success" data-translate="true">' . htmlspecialchars($success) . '</p>';
    }
    ?>
    <form method="post" action="">
        <label for="fullname" data-translate="true">Пълно име:</label>
        <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($userDetails['full_name']); ?>" <?php echo $editable; ?> required>
        <p></p>

        <label for="fn" data-translate="true">Факултетен номер (FN):</label>
        <input type="text" id="fn" name="fn" value="<?php echo htmlspecialchars($userDetails['fn']); ?>" <?php echo $editable; ?> required>
        <p></p>

        <label for="egn" data-translate="true">ЕГН:</label>
        <input type="text" id="egn" name="egn" value="" required>
        <p></p>

        <label for="payment_method" data-translate="true">Метод на плащане:</label>
        <select id="payment_method" name="payment_method" required>
            <option value="credit_card" data-translate="true">Кредитна карта</option>
            <option value="paypal" data-translate="true">PayPal</option>
            <option value="bank_transfer" data-translate="true">Банков превод</option>
        </select>
        <p></p>

        <button type="submit" data-translate="true">Изпрати заявка за резервация</button>
    </form>
</div>

</body>
</html>
