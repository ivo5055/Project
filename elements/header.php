<?php 
include "includes/dbh.inc.php"; 
if(!isset($_SESSION['Id'])){
    session_start();
}

// Fetch notifications
$sql = "SELECT message FROM notification";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$notifications = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $notifications[] = $row['message'];
}

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

<script>
    function toggleDropdown(dropdownId) {
        var dropdownContent = document.getElementById(dropdownId);
        var dropdowns = document.getElementsByClassName("dropdown-content");

        // Close all dropdowns except the one being toggled
        for (var i = 0; i < dropdowns.length; i++) {
            if (dropdowns[i].id !== dropdownId) {
                dropdowns[i].style.display = 'none';
            }
        }

        // Toggle the current dropdown
        if (dropdownContent.style.display === 'block') {
            dropdownContent.style.display = 'none';
        } else {
            dropdownContent.style.display = 'block';
        }
    }

    // Close the dropdown if the user clicks outside of it
    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn') && !event.target.matches('.dropbtnN img')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.style.display === 'block') {
                    openDropdown.style.display = 'none';
                }
            }
        }
    }
</script>
