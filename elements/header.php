<?php 
include "includes/dbh.inc.php"; 
if(!isset($_SESSION['Id'])){
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
}}

?>

<header>
    <a href="MainPage.php" class="logo-circle"></a>
    <nav>
        <a href="offers.php">Offers</a>
        <a href="diner.php">Diner</a>
        <a href="about.php">About Us</a>

        <?php 
        if (isset($_SESSION['account']) && $_SESSION['account'] == 'A'): ?>
        <a href="addNotification.php">Notify</a>
        <a href="addOffer.php">Add Offer</a>
        <a href="manage_offers.php">Manage Offers</a>
        <a href="approve.php">Approve Admins</a>
        <a href="requests.php">Requests</a>

        <?php endif; ?>

        <?php 
        if (isset($_SESSION['account']) && $_SESSION['account'] == 'U'): ?>
        <a href="public_chat.php">Public Chat</a>
        <?php endif; ?>
    </nav>
    <div class="search-box">
        <input type="text" placeholder="Search...">
        <select class="language-selector">
            <option value="en">English</option>
            <option value="bg">Bulgarian</option>
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
                        echo "<p>$notification</p>";
                    }
                } else {
                    echo "<p>No notifications</p>";
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
            echo '<button class="dropbtn" onclick="toggleDropdown(\'userDropdown\')">' . $_SESSION['username'] . '</button>';
            echo '<div class="dropdown-content" id="userDropdown">';
            echo '<a href="profile.php">Profile</a>';
            echo '<a href="bookmarkedRooms.php">Bookmark</a>';
            echo '<a href="includes/logout.php">Logout</a>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<a href="Login.php" class="login-button">Login</a>';
        }
        ?>
    </div>
</header>

<script src="js/dropdown.js"></script>