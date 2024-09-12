<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Забравена парола</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>

<?php include 'elements/header.php'; ?>

<div class="forgot-password-container">
    <h1 data-translate="true">Забравена парола</h1>

    <?php if (isset($_GET['success']) && !empty($_GET['success'])): ?>
        <div class="messageF success">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['email']) && !empty($_GET['email'])): ?>
        <div class="messageF error">
            <?php echo htmlspecialchars($_GET['email']); ?>
        </div>
    <?php endif; ?>

    <form action="includes/forgot_pwd.php" method="post">
        <label for="email" data-translate="true">Моля, въведете вашия имейл адрес:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit" data-translate="true">Изпрати линк за възстановяване</button>
    </form>
</div>

</body>
</html>
