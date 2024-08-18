<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Одобряване на потребители</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">

    <?php include 'elements/header.php';?>
</head>
<body>

   <h1 data-translate="true">Одобряване на потребители</h1>
<form method="post" action="">
    <label for="user" data-translate="true">Търсене на потребителско име:</label>
    <input type="text" id="user" name="user" required>
    <button type="submit" name="search" data-translate="true">Търсене</button>
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
        <h2 data-translate="true">Резултати от търсенето:</h2>
        <table>
            <thead>
                <tr>
                    <th data-translate="true">Потребителско име</th>
                    <th data-translate="true">Статус на акаунта</th>
                    <th data-translate="true">Действие</th>
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
                                    <button type="submit" name="approve_user" data-translate="true">Одобри</button>
                                <?php else: ?>
                                    <?php if ($_SESSION['username'] != $row['user']): ?>
                                        <button type="submit" name="disapprove_user" data-translate="true">Задай като не одобрен</button>
                                    <?php else: ?>
                                        <button type="button" disabled data-translate="true">Задай като не одобрен</button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p data-translate="true">Няма намерени потребители.</p>
    <?php endif;
}

if (isset($_POST['approve_user'])) {
    $user_id = $_POST['user_id'];

    // Prepare and execute the query to update the user's account status to 'A'
    $stmt = $pdo->prepare("UPDATE users SET account = 'A' WHERE id = ?");
    $stmt->execute([$user_id]);

    if ($stmt->rowCount() > 0) {
        echo "<p data-translate=\"true\">Потребителят беше одобрен успешно.</p>";
    } else {
        echo "<p data-translate=\"true\">Неуспешно одобрение на потребителя.</p>";
    }
}

if (isset($_POST['disapprove_user'])) {
    $user_id = $_POST['user_id'];

    // Prepare and execute the query to update the user's account status to 'U'
    $stmt = $pdo->prepare("UPDATE users SET account = 'U' WHERE id = ?");
    $stmt->execute([$user_id]);

    if ($stmt->rowCount() > 0) {
        echo "<p data-translate=\"true\">Статусът на потребителя е зададен на не одобрен.</p>";
    } else {
        echo "<p data-translate=\"true\">Неуспешно задаване на статус на потребителя като не одобрен.</p>";
    }
}
?>


</body>
</html>
