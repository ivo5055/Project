<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offers</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">    
</head>
<body>
    
<?php 
include "elements/header.php";
include "includes/dbh.inc.php"; 
?>

<div class="header-container">
    <?php include "includes/show_booked.php"; ?>

    <h1><p></p>Room Offers<p></p></h1>
    
        <!-- Building Filter -->
        <div id="buildingFilterForm" class="radio-button-form">
            <button class="building-button" data-building="1">Building 1</button>
            <button class="building-button" data-building="2">Building 2</button>
            <button class="building-button" data-building="3">Building 3</button>
            <button class="building-button" data-building="4">Building 4</button>
            <button class="building-button" data-building="5">Building 5</button>
            <button class="building-button" data-building="6">Building 6</button>
            <button id="filterButton" class="Filter_B">Filter</button>
            
        </div>

    <?php include "includes/filter.php";?>

    <div class="room-offers">
        <?php
        // Delete
        include "includes/deleteOffer.php";

        // Loop through fetched room offers and generate HTML dynamically
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            // Count current bookings for this room
            $bookingQuery = "SELECT COUNT(*) FROM bookings WHERE room_number = :room_number";
            $bookingStmt = $pdo->prepare($bookingQuery);
            $bookingStmt->execute(['room_number' => $row['room_number']]);
            $currentBookings = $bookingStmt->fetchColumn();

            // Check if the building filter is set
            if (isset($_GET['building'])) {
                // Check if the room belongs to the selected building
                if ($_GET['building'] == $row['building']) {
                    // Check if the room is available or the user is an admin
                    if ($currentBookings < $row['room_capacity'] || (isset($_SESSION['account']) && $_SESSION['account'] == "A")) {
                        // Include room display
                        if (isset($_SESSION['gender']) && $_SESSION['account'] == "U") {
                            if ($row['gender_R'] == $_SESSION['gender']) {
                                include "includes/offersDisplay.php";
                            }
                        } else if (!isset($_SESSION['gender']) || $_SESSION['account'] == "A") {
                            include "includes/offersDisplay.php";
                        }
                    }
                }
            } else {
                // If building filter is not set, show all rooms
                // Check if the room is available or the user is an admin
                if ($currentBookings < $row['room_capacity'] || (isset($_SESSION['account']) && $_SESSION['account'] == "A")) {
                    // Include room display
                    if (isset($_SESSION['gender']) && $_SESSION['account'] == "U") {
                        if ($row['gender_R'] == $_SESSION['gender']) {
                            include "includes/offersDisplay.php";
                        }
                    } else if (!isset($_SESSION['gender']) || $_SESSION['account'] == "A") {
                        include "includes/offersDisplay.php";
                    }
                }
            }
        }
        ?>
    </div>
</div>

<script>
    const buildingButtons = document.querySelectorAll('.building-button');

    // Add event listener to building buttons
    buildingButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get the value of the building from the data attribute
            const building = button.getAttribute('data-building');
            // Redirect to the page with the selected building as a parameter
            window.location.href = `offers.php?building=${building}`;
        });
    });
</script>

<script>
    // Get references to filter button and form
    const filterButton = document.getElementById('filterButton');
    const filterForm = document.getElementById('filterForm');

    // Add event listener to filter button
    filterButton.addEventListener('click', function() {
        // Toggle visibility of filter form
        filterForm.classList.toggle('show');
    });

    // Add event listener to clear button
    const clearFilterButton = document.getElementById('clearFilter');
    clearFilterButton.addEventListener('click', function() {
        // Reset filter form
        filterForm.reset();
        // Clear input values manually
        const inputs = filterForm.getElementsByTagName('input');
        for (let i = 0; i < inputs.length    ; i++) {
        inputs[i].value = '';
    }
    // Clear select value manually
    const select = filterForm.getElementsByTagName('select')[0];
    select.selectedIndex = 0;
});
</script>
</body>
</html>
