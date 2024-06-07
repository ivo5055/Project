<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "elements/header.php"; ?>
    <title>Add Notification</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>
    <h2>Add Notification</h2>
    <?php
    

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
        // Validate and sanitize input
        $notificationMessage = htmlspecialchars($_POST['notification_message']);
        $notificationDuration = htmlspecialchars($_POST['notification_duration']);

        // Insert the notification into the database
        $sql = "INSERT INTO notification (message, duration) VALUES (:message, :duration)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['message' => $notificationMessage, 'duration' => $notificationDuration]);

        // Redirect to a page after successful submission (you can change the URL)
        header("Location: addNotification.php?success=true");
        exit();
    }

    // Delete notification if requested
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
        $notificationId = intval($_POST['delete_notification']);
        $sql = "DELETE FROM notification WHERE Id = :id"; // Change to use Id
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $notificationId]);
        // Redirect to prevent resubmission
        header("Location: addNotification.php");
        exit();
    }

    // Fetch all non-expired notifications
    $currentDateTime = date('Y-m-d H:i:s');
    $sql = "SELECT * FROM notification WHERE duration > :currentDateTime";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['currentDateTime' => $currentDateTime]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="notification_message">Notification Message:</label><br>
        <textarea id="notification_message" name="notification_message" rows="4" cols="50" required></textarea><br>
        <label for="notification_duration">Notification Duration:</label><br>
        <input type="datetime-local" id="notification_duration" name="notification_duration" required><br>
        <input type="hidden" name="action" value="add">
        <input type="submit" value="Submit">
    </form>
    
    <br>
    <h2>Notifications</h2>
    <?php if ($notifications): ?>
        <?php foreach ($notifications as $notification): ?>
            <div class="notification">
                <span><?php echo $notification['message']; ?></span>
                <span> - Until: <?php echo $notification['duration']; ?></span>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="delete_notification" value="<?php echo $notification['Id']; ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No notifications available.</p>
    <?php endif; ?>
</body>
</html>
