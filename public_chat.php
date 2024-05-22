<?php
// Include database connection
include_once 'includes/dbh.inc.php';
include "elements/header.php";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    // Sanitize user input
    $message = htmlspecialchars($_POST['message']);

    // Insert message into the database
    $stmt = $pdo->prepare("INSERT INTO messages (user_id, message, timestamp) VALUES (?, ?, NOW())");
    $stmt->execute([$_SESSION['Id'], $message]);

    // Redirect the user back to the same page after form submission
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fetch archived messages from the database, ordered by timestamp ascending
$stmt = $pdo->query("SELECT messages.*, users.user FROM messages JOIN users ON messages.user_id = users.Id ORDER BY messages.timestamp ASC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offers</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="message.css">
    <link rel="stylesheet" href="dropdown.css">
    
</head>
<body>


<div class="container">

        <!-- display archived messages -->
        <div class="message-container" id="message-container">
            <?php foreach ($messages as $message): ?>
                <div class="message">
                <strong><?php echo $message['user'] . '  ' //. $message['timestamp']; ?></strong>
                    <p><?php echo $message['message']; ?></p>
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
