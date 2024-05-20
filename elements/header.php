<?php session_start();

?>

<header>
        <a href="MainPage.php" class="logo-circle"></a>
    <nav>
        <a href="offers.php">Offers</a>
        <a href="diner.php">Diner</a>
        <a href="about.php">About Us</a>

        <?php 
        if (isset($_SESSION['account']) && $_SESSION['account'] == 'A'): ?>
        <a href="addOffer.php">Add Offer</a>
        <a href="approve.php">Approve Admins</a>
        <?php endif; ?>

    </nav>
    <div class="search-box">
        <input type="text" placeholder="Search...">
        <select class="language-selector">
            <option value="en">English</option>
            <option value="bg">Bulgarian</option>
        </select>

        <?php
        

        //Dropdown menu
        if (isset($_SESSION['username'])) {
            echo '<div class="dropdown">';
            echo '<button class="dropbtn" onclick="toggleDropdown()">' . $_SESSION['username'] . '</button>';
            echo '<div class="dropdown-content" id="myDropdown">';
            echo '<a href="profile.php">Profile</a>';
            echo '<a href="includes/logout.php">Logout</a>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<a href="Login.php" class="login-button">Login</a>';
        }
        ?>

    </div>
</header>
