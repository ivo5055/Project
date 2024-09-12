<?php
// Include database connection
include_once 'includes/dbh.inc.php';
include "elements/header.php";

if (!isset($_SESSION['account']) || $_SESSION['account'] !== 'U') {
    header("Location: login.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    // Sanitize user input
    $message = htmlspecialchars(trim($_POST['message']));

    // Check if the message is not empty
    if (!empty($message)) {
        // Insert message into the database
        $stmt = $pdo->prepare("INSERT INTO messages (user_id, message, timestamp) VALUES (?, ?, NOW())");
        $stmt->execute([$_SESSION['Id'], $message]);
    }

    // Redirect the user back to the same page after form submission
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Check if delete request is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message_id'])) {
    $message_id = intval($_POST['delete_message_id']);
    // Delete message from the database
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$message_id]);

    // Redirect the user back to the same page after deletion
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fetch archived messages from the database, ordered by timestamp ascending
$stmt = $pdo->query("SELECT messages.*, users.full_name FROM messages JOIN users ON messages.user_id = users.Id ORDER BY messages.timestamp ASC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Студентски Чат</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="message.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>

<div class="container">

    <!-- display archived messages -->
    <div class="message-container" id="message-container">
    <?php foreach ($messages as $message): ?>
        <div class="message <?php echo $_SESSION['Id'] == $message['user_id'] ? 'my-message' : 'other-message'; ?>">
    <?php if ($_SESSION['Id'] == $message['user_id']): ?>
        
        <div class="message-content">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="delete-form">
                <input type="hidden" name="delete_message_id" value="<?php echo htmlspecialchars($message['Id']); ?>">
                <button type="submit" class="delete-button-chat">Delete</button>
            </form>
            <p><?php echo htmlspecialchars($message['message']); ?></p>
        </div>
    <?php else: ?>
        <strong><?php echo htmlspecialchars($message['full_name']); ?></strong>
        <p><?php echo htmlspecialchars($message['message']); ?></p>
    <?php endif; ?>
    <span class="timestamp"><?php echo htmlspecialchars($message['timestamp']); ?></span>
</div>

    <?php endforeach; ?>

    </div>

    <!-- submitting messages -->
    <div class="bottom-form">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="text" name="message" placeholder="Type your message here...">
            <input type="submit" value="Send">
        </form>
    </div>

    <script>
        // Scroll the message container to the bottom
        var messageContainer = document.getElementById('message-container');
        messageContainer.scrollTop = messageContainer.scrollHeight;
    </script>

</body>
</html>
