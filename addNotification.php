<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "elements/header.php"; ?>
    <title data-translate="true">Добавяне на уведомление</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body class="add-notification-page">
    <h2 data-translate="true">Добавяне на уведомление</h2>
    <?php
    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
        // Validate and sanitize input
        $notificationMessage = htmlspecialchars($_POST['notification_message']);
        $notificationDuration = htmlspecialchars($_POST['notification_duration']);
        $notificationUser = htmlspecialchars($_POST['notification_user']);
        
        $sql = "INSERT INTO notification (message, duration, userN) VALUES (:message, :duration, :userN)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'message' => $notificationMessage,
            'duration' => $notificationDuration,
            'userN' => $notificationUser
        ]);
        
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

    // Fetch all user names for the dropdown
    try {
        $sqlUsers = "SELECT user FROM users";
        $stmtUsers = $pdo->prepare($sqlUsers);
        $stmtUsers->execute();
        $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Error fetching users: ' . $e->getMessage();
    }

    // Fetch all non-expired notifications
    $currentDateTime = date('Y-m-d H:i:s');
    $sql = "SELECT * FROM notification WHERE duration > :currentDateTime";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['currentDateTime' => $currentDateTime]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="notification_message" data-translate="true">Съобщение за уведомление:</label><br>
        <textarea id="notification_message" name="notification_message" rows="4" cols="50" required></textarea><br>
        
        <div class="form-group">
            <div class="form-group-item">
                <div class="form-label-container">
                    <label for="notification_user" data-translate="true">Потребител:</label>
                </div>
                <select class="selectU" id="notification_user" name="notification_user">
                    <option value="" disabled selected>Изберете потребител</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['user']); ?>">
                            <?php echo htmlspecialchars($user['user']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group-item">
                <div class="form-label-container">
                    <label for="notification_duration" data-translate="true">Продължителност на уведомлението:</label>
                </div>
                <input type="datetime-local" id="notification_duration" name="notification_duration" required>
            </div>
        </div><br>
        
        <input type="hidden" name="action" value="add">
        <input type="submit" data-translate="true" value="Изпрати">
    </form>
    
    <br>
    <h2 data-translate="true">Уведомления</h2>
    <div class="notification-container">
        <?php if ($notifications): ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification">
                    <div class="userN"><?php echo htmlspecialchars($notification['userN']); ?></div>
                    <div class="message"><?php echo htmlspecialchars($notification['message']); ?></div>
                    <div class="duration">До: <?php echo htmlspecialchars($notification['duration']); ?></div>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" name="delete_notification" value="<?php echo htmlspecialchars($notification['Id']); ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" data-translate="true">Изтрий</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p data-translate="true">Няма налични уведомления.</p>
        <?php endif; ?>
    </div>
</body>
</html>
