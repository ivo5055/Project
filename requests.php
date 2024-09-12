<?php
session_start();
include 'includes/dbh.inc.php';
include 'elements/header.php';

if (!isset($_SESSION['account']) || $_SESSION['account'] !== 'A') {
    header("Location: login.php");
    exit();
}

// Sorting logic
$sortColumn = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'building';
$sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'asc';
$newSortOrder = ($sortOrder === 'asc') ? 'desc' : 'asc';

// Handle request approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requestId = $_POST['request_id'];
    $action = $_POST['action'];
    $username = $_POST['username'];
    $room_number = $_POST['room_number'];
    $building = $_POST['building'];
    $fn = $_POST['fn'];  // FN from POST request

    if ($action == 'approve') {
        // Get the correct username from the users table based on FN
        $queryUser = "SELECT user FROM users WHERE fn = :fn";
        $stmtUser = $pdo->prepare($queryUser);
        $stmtUser->execute(['fn' => $fn]);
        $correctUsername = $stmtUser->fetchColumn();

        // Check room availability
        $checkRoomBookingQuery = "SELECT COUNT(*) FROM bookings WHERE room_number = :room_number AND building = :building";
        $checkRoomBookingStmt = $pdo->prepare($checkRoomBookingQuery);
        $checkRoomBookingStmt->execute(['room_number' => $room_number, 'building' => $building]);
        $currentRoomBookings = $checkRoomBookingStmt->fetchColumn();

        $roomQuery = "SELECT room_capacity FROM room WHERE room_number = :room_number AND building = :building";
        $roomStmt = $pdo->prepare($roomQuery);
        $roomStmt->execute(['room_number' => $room_number, 'building' => $building]);
        $roomCapacity = $roomStmt->fetchColumn();

        if ($currentRoomBookings < $roomCapacity) {
            // Check if there's already any booking for the same user (regardless of room)
            $checkExistingBookingQuery = "SELECT Id FROM bookings WHERE userN = :userN";
            $checkExistingBookingStmt = $pdo->prepare($checkExistingBookingQuery);
            $checkExistingBookingStmt->execute(['userN' => $correctUsername]);
            $existingBookings = $checkExistingBookingStmt->fetchAll(PDO::FETCH_COLUMN);

            if ($existingBookings) {
                // Delete old bookings for the user
                $deleteBookingsQuery = "DELETE FROM bookings WHERE Id IN (" . implode(',', array_fill(0, count($existingBookings), '?')) . ")";
                $deleteBookingsStmt = $pdo->prepare($deleteBookingsQuery);
                $deleteBookingsStmt->execute($existingBookings);
            }

            // Book the room
            $bookingQuery = "INSERT INTO bookings (userN, building, room_number) VALUES (:userN, :building, :room_number)";
            $bookingStmt = $pdo->prepare($bookingQuery);
            $bookingStmt->execute(['userN' => $correctUsername, 'building' => $building, 'room_number' => $room_number]);

            // Update the request status
            $updateRequestQuery = "UPDATE booking_requests SET status = 'approved' WHERE id = :request_id";
            $updateRequestStmt = $pdo->prepare($updateRequestQuery);
            $updateRequestStmt->execute(['request_id' => $requestId]);

            // Add notification for the user
            $notificationMessage = "Вашето искане за стая е одобрено";
            $notificationDuration = date('Y-m-d H:i:s', strtotime('+1 day'));
            $notificationQuery = "INSERT INTO notification (message, duration, userN) VALUES (:message, :duration, :userN)";
            $notificationStmt = $pdo->prepare($notificationQuery);
            $notificationStmt->execute([
                'message' => $notificationMessage,
                'duration' => $notificationDuration,
                'userN' => $correctUsername
            ]);
        } else {
            $error = "Стаята е напълно резервирана.";
        }
    } else if ($action == 'reject') {
        // Reject the request
        $updateRequestQuery = "UPDATE booking_requests SET status = 'rejected' WHERE id = :request_id";
        $updateRequestStmt = $pdo->prepare($updateRequestQuery);
        $updateRequestStmt->execute(['request_id' => $requestId]);

        // Add notification for the user
        $notificationMessage = "Вашето искане за стая е отхвърлено";
        $notificationDuration = date('Y-m-d H:i:s', strtotime('+1 day'));
        $notificationQuery = "INSERT INTO notification (message, duration, userN) VALUES (:message, :duration, :userN)";
        $notificationStmt = $pdo->prepare($notificationQuery);
        $notificationStmt->execute([
            'message' => $notificationMessage,
            'duration' => $notificationDuration,
            'userN' => $username
        ]);
    }
}

// Fetch pending requests with student grades and documents
$query = "SELECT br.*, s.grade, br.document_path 
          FROM booking_requests br 
          JOIN students_db s ON br.fn = s.fn 
          WHERE br.status = 'pending' 
          ORDER BY $sortColumn $sortOrder";
$stmt = $pdo->prepare($query);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Управление на заявките за резервация</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>

<div class="requests-container">
    <h1 data-translate="true">Управление на заявките за резервация</h1>
    <?php
    if (isset($error)) {
        echo '<p class="error" data-translate="true">' . htmlspecialchars($error) . '</p>';
    }
    ?>
    <table>
        <thead>
            <tr>
                <th data-translate="true">Потребител</th>
                <th><a href="?sort_column=building&sort_order=<?php echo $newSortOrder; ?>" data-translate="true">Блок</a></th>
                <th><a href="?sort_column=room_number&sort_order=<?php echo $newSortOrder; ?>" data-translate="true">Номер на стаята</a></th>
                <th data-translate="true">Пълно име</th>
                <th data-translate="true">ФН</th>
                <th><a href="?sort_column=grade&sort_order=<?php echo $newSortOrder; ?>" data-translate="true">Оценка</a></th>
                <th data-translate="true">Документи</th>
                <th data-translate="true">Действие</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['userN']); ?></td>
                    <td><?php echo htmlspecialchars($request['building']); ?></td>
                    <td><?php echo htmlspecialchars($request['room_number']); ?></td>
                    <td><?php echo htmlspecialchars($request['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($request['fn']); ?></td>
                    <td><?php echo htmlspecialchars($request['grade']); ?></td>
                    <td>
                        <?php 
                        $documents = explode(',', $request['document_path']);
                        foreach ($documents as $document): 
                        ?>
                            <a href="/Project/<?php echo htmlspecialchars($document); ?>" download data-translate="true">Изтегли</a><br>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <input type="hidden" name="username" value="<?php echo $request['userN']; ?>">
                            <input type="hidden" name="room_number" value="<?php echo $request['room_number']; ?>">
                            <input type="hidden" name="building" value="<?php echo $request['building']; ?>">
                            <input type="hidden" name="fn" value="<?php echo $request['fn']; ?>">
                            <button type="submit" name="action" value="approve" data-translate="true">Одобри</button>
                            <button type="submit" name="action" value="reject" data-translate="true">Отхвърли</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
