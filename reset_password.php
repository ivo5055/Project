<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Възстановяване на парола</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>

<?php include 'elements/header.php'; ?>

<div class="reset-password-container">
    <h1>Възстановяване на парола</h1>
    <form action="includes/reset_pwd.php" method="post">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>" required>
        
        <label for="password">Нова парола:</label>
        <input type="password" id="password" name="password" required>
        <?php if (isset($_GET['password'])): ?>
            <span style="color: red;"><?php echo htmlspecialchars($_GET['password']); ?></span>
        <?php endif; ?>

        <label for="confirm_password">Потвърдете паролата:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        
        <button type="submit">Възстановете паролата</button>
    </form>
</div>

</body>
</html>
