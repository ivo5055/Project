<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Offer</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">

    <?php include 'elements/header.php';?>
</head>
<body>

   <h1>Approve Users</h1>
<form method="post" action="">
    <label for="user">Search Username:</label>
    <input type="text" id="user" name="user" required>
    <button type="submit" name="search">Search</button>
</form>

<?php

include "includes/dbh.inc.php";

// Check if the search form is submitted
if (isset($_POST['search'])) {
    $user = $_POST['user'];

    // Prepare and execute the query to search for users
    $stmt = $pdo->prepare("SELECT id, user, account FROM users WHERE user LIKE ?");
    $searchTerm = "%".$user."%";
    $stmt->execute([$searchTerm]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result): ?>
        <h2>Search Results:</h2>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Account</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['user'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['account'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php if ($row['account'] == 'U'): ?>
                                    <button type="submit" name="approve_user">Approve</button>
                                <?php else: ?>
                                    <?php if ($_SESSION['username'] != $row['user']): ?>
                                        <button type="submit" name="disapprove_user">Set to Unapproved</button>
                                    <?php else: ?>
                                        <button type="button" disabled>Set to Unapproved</button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif;
}

if (isset($_POST['approve_user'])) {
    $user_id = $_POST['user_id'];

    // Prepare and execute the query to update the user's account status to 'A'
    $stmt = $pdo->prepare("UPDATE users SET account = 'A' WHERE id = ?");
    $stmt->execute([$user_id]);

    if ($stmt->rowCount() > 0) {
        echo "<p>User approved successfully.</p>";
    } else {
        echo "<p>Failed to approve user.</p>";
    }
}

if (isset($_POST['disapprove_user'])) {
    $user_id = $_POST['user_id'];

    // Prepare and execute the query to update the user's account status to 'U'
    $stmt = $pdo->prepare("UPDATE users SET account = 'U' WHERE id = ?");
    $stmt->execute([$user_id]);

    if ($stmt->rowCount() > 0) {
        echo "<p>User status set to unapproved successfully.</p>";
    } else {
        echo "<p>Failed to set user status to unapproved.</p>";
    }
}
?>


</body>
</html>



</body>
</html>
