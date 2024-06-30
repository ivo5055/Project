<?php
session_start();
include 'includes/dbh.inc.php';
include 'elements/header.php';

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

    if ($action == 'approve') {
        // Approve the request
        $checkRoomBookingQuery = "SELECT COUNT(*) FROM bookings WHERE room_number = :room_number AND building = :building";
        $checkRoomBookingStmt = $pdo->prepare($checkRoomBookingQuery);
        $checkRoomBookingStmt->execute(['room_number' => $room_number, 'building' => $building]);
        $currentRoomBookings = $checkRoomBookingStmt->fetchColumn();

        $roomQuery = "SELECT room_capacity FROM room WHERE room_number = :room_number AND building = :building";
        $roomStmt = $pdo->prepare($roomQuery);
        $roomStmt->execute(['room_number' => $room_number, 'building' => $building]);
        $roomCapacity = $roomStmt->fetchColumn();

        if ($currentRoomBookings < $roomCapacity) {
            // Book the room
            $bookingQuery = "INSERT INTO bookings (userN, building, room_number) VALUES (:userN, :building, :room_number)";
            $bookingStmt = $pdo->prepare($bookingQuery);
            $bookingStmt->execute(['userN' => $username, 'building' => $building, 'room_number' => $room_number]);

            // Update the request status
            $updateRequestQuery = "UPDATE booking_requests SET status = 'approved' WHERE id = :request_id";
            $updateRequestStmt = $pdo->prepare($updateRequestQuery);
            $updateRequestStmt->execute(['request_id' => $requestId]);

            // Add notification for the user
            $notificationMessage = "Your room request has been approved"; // for room $room_number building $building"
            $notificationDuration = date('Y-m-d H:i:s', strtotime('+1 day')); // Notification lasts for one week
            $notificationQuery = "INSERT INTO notification (message, duration, userN) VALUES (:message, :duration, :userN)";
            $notificationStmt = $pdo->prepare($notificationQuery);
            $notificationStmt->execute([
                'message' => $notificationMessage,
                'duration' => $notificationDuration,
                'userN' => $username
            ]);
        } else {
            $error = "Room is fully booked.";
        }
    } else if ($action == 'reject') {
        // Reject the request
        $updateRequestQuery = "UPDATE booking_requests SET status = 'rejected' WHERE id = :request_id";
        $updateRequestStmt = $pdo->prepare($updateRequestQuery);
        $updateRequestStmt->execute(['request_id' => $requestId]);

        // Add notification for the user
        $notificationMessage = "Your room request has been rejected"; // for room $room_number building $building
        $notificationDuration = date('Y-m-d H:i:s', strtotime('+1 day')); // Notification lasts for one week
        $notificationQuery = "INSERT INTO notification (message, duration, userN) VALUES (:message, :duration, :userN)";
        $notificationStmt = $pdo->prepare($notificationQuery);
        $notificationStmt->execute([
            'message' => $notificationMessage,
            'duration' => $notificationDuration,
            'userN' => $username
        ]);
    }
}

// Fetch pending requests with student grades
$query = "SELECT br.*, s.grade 
          FROM booking_requests br 
          JOIN students_db s ON br.fn = s.fn 
          WHERE br.status = 'pending' 
          ORDER BY $sortColumn $sortOrder";
$stmt = $pdo->prepare($query);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Booking Requests</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>

<div class="requests-container">
    <h1>Manage Booking Requests</h1>
    <?php
    if (isset($error)) {
        echo '<p class="error">' . htmlspecialchars($error) . '</p>';
    }
    ?>
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th><a href="?sort_column=building&sort_order=<?php echo $newSortOrder; ?>">Building</a></th>
                <th><a href="?sort_column=room_number&sort_order=<?php echo $newSortOrder; ?>">Room Number</a></th>
                <th>Full Name</th>
                <th>FN</th>
                <th><a href="?sort_column=grade&sort_order=<?php echo $newSortOrder; ?>">Grade</a></th>
                <th>Payment Method</th>
                <th>Action</th>
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
                    <td><?php echo htmlspecialchars($request['payment_method']); ?></td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <input type="hidden" name="username" value="<?php echo $request['userN']; ?>">
                            <input type="hidden" name="room_number" value="<?php echo $request['room_number']; ?>">
                            <input type="hidden" name="building" value="<?php echo $request['building']; ?>">
                            <button type="submit" name="action" value="approve">Approve</button>
                            <button type="submit" name="action" value="reject">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
