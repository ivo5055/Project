<?php 
include "includes/dbh.inc.php"; 
if(!isset($_SESSION)){
    session_start();
}

if(isset($_SESSION['username'])){
    $username = $_SESSION['username'];

    // Adjust the SQL query to fetch notifications for the logged-in user or for all users
    $sql = "SELECT message FROM notification WHERE userN = :username OR userN IS NULL OR userN = ''";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);

    $notifications = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $notifications[] = $row['message'];
    }
}

?>
<header>
    <a href="MainPage.php" class="logo-circle"></a>
    <nav>
        <a href="offers.php" data-translate="true">Оферти</a>
        <a href="diner.php" data-translate="true">Столова</a>
        <a href="about.php" data-translate="true">За нас</a>

        <?php 
        if (isset($_SESSION['account']) && $_SESSION['account'] == 'A'): ?>
        <a href="addNotification.php" data-translate="true">Уведоми</a>
        <a href="addOffer.php" data-translate="true">Добавяне на оферти</a>
        <a href="manage_offers.php" data-translate="true">Оправление на оферти</a>
        <a href="requests.php" data-translate="true">Заявки</a>

        <?php endif; ?>

        <?php 
        if (isset($_SESSION['account']) && $_SESSION['account'] == 'U'): ?>
        <a href="public_chat.php" data-translate="true">Публичен чат</a>
        <?php endif; ?>
    </nav>
    <div class="search-box">
        <input type="text" placeholder="Search..." data-translate="true">
        <select class="language-select" id="language-select">
            <option value="bg">Български</option>
            <option value="en">English</option>
        </select>

        <div class="dropdown">
            <div class="dropbtnN" onclick="toggleDropdown('notificationDropdown')">
                <?php if (!empty($notifications)): ?>
                    <img src="img/notification1.png" alt="Notifications">
                <?php else: ?>
                    <img src="img/notification.png" alt="No Notifications">
                <?php endif; ?>
            </div>
            <div class="dropdown-content" id="notificationDropdown">
                <?php
                if (!empty($notifications)) {
                    foreach ($notifications as $notification) {
                        echo "<p data-translate='true'>$notification</p>";
                    }
                } else {
                    echo "<p data-translate='true'>No notifications</p>";
                }

                // Function to delete expired notifications
                function deleteExpiredNotifications($pdo) {
                    $currentDateTime = date('Y-m-d H:i:s');
                    $sql = "DELETE FROM notification WHERE duration <= :currentDateTime";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['currentDateTime' => $currentDateTime]);
                }

                // Delete expired notifications
                deleteExpiredNotifications($pdo);

                ?>
            </div>
        </div>

        <?php
        // Dropdown menu
        if (isset($_SESSION['username'])) {
            echo '<div class="dropdown">';
            echo '<button class="dropbtn" onclick="toggleDropdown(\'userDropdown\')" data-translate="true">' . $_SESSION['username'] . '</button>';
            echo '<div class="dropdown-content" id="userDropdown">';
            echo '<a href="profile.php" data-translate="true">Профил</a>';
            if (isset($_SESSION['account']) && $_SESSION['account'] == 'A'):
            echo '<a href="approve.php" data-translate="true">Потвърждаване на администратори</a>';
            endif;
            echo '<a href="bookmarkedRooms.php" data-translate="true">Отметки</a>';
            echo '<a href="includes/logout.php" data-translate="true">Изход</a>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<a href="Login.php" class="login-button" data-translate="true">Вход</a>';
        }
        ?>
    </div>
</header>
<script src="js/dropdown.js"></script>
<script src="js/translate.js"></script>