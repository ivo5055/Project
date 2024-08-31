<?php
session_start();
include "includes/dbh.inc.php";
include "includes/deleteOffer.php";

// Fetch the user's grade from the database
$grade = null;
if (isset($_SESSION['Id'])) {
    $userId = $_SESSION['Id'];
    $stmt = $pdo->prepare("
        SELECT sd.grade 
        FROM students_db sd
        JOIN users u ON u.fn = sd.fn
        WHERE u.Id = ?
    ");
    $stmt->execute([$userId]);
    $grade = $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Оферти</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">    
</head>
<body>

<?php
include "elements/header.php";
?>

<div class="header-container">
    <?php include "includes/show_booked.php"; ?>
    <h1 data-translate="true"><p></p>Налични Оферти<p></p></h1>

    <!-- Building Filter -->
    <div id="buildingFilterForm" class="radio-button-form">
        <button class="building-button active" data-building="1" data-translate="true">Блок 1</button>
        <button class="building-button" data-building="2" data-translate="true">Блок 2</button>
        <button class="building-button" data-building="3" data-translate="true">Блок 3</button>
        <button class="building-button" data-building="4" <?php if ($grade !== null && $grade < 3.5) echo 'disabled title="Недостатъчен успех."'; ?> data-translate="true">Блок 4</button>
        <button class="building-button" data-building="5" <?php if ($grade !== null && $grade < 4) echo 'disabled title="Недостатъчен успех."'; ?> data-translate="true">Блок 5</button>
        <button class="building-button" data-building="6" <?php if ($grade !== null && $grade < 5) echo 'disabled title="Недостатъчен успех."'; ?> data-translate="true">Блок 6</button>
        
        <button id="filterButton" class="Filter_B">Filter</button>
        <?php include "includes/filter.php"; ?>
    </div>
    
    <div class="room-offers">
        <?php
        $query = "SELECT * FROM room WHERE 1=1";
        $params = [];

        if (!empty($_GET['room_number'])) {
            $query .= " AND room_number = :room_number";
            $params['room_number'] = $_GET['room_number'];
        }
        if (!empty($_GET['min_capacity'])) {
            $query .= " AND room_capacity >= :min_capacity";
            $params['min_capacity'] = $_GET['min_capacity'];
        }
        if (!empty($_GET['max_capacity'])) {
            $query .= " AND room_capacity <= :max_capacity";
            $params['max_capacity'] = $_GET['max_capacity'];
        }
        if (!empty($_GET['min_rating'])) {
            $query .= " AND (total_rating / number_of_reviews) >= :min_rating";
            $params['min_rating'] = $_GET['min_rating'];
        }
        if (!empty($_GET['max_rating'])) {
            $query .= " AND (total_rating / number_of_reviews) <= :max_rating";
            $params['max_rating'] = $_GET['max_rating'];
        }
        if (!empty($_GET['min_price'])) {
            $query .= " AND price >= :min_price";
            $params['min_price'] = $_GET['min_price'];
        }
        if (!empty($_GET['max_price'])) {
            $query .= " AND price <= :max_price";
            $params['max_price'] = $_GET['max_price'];
        }
        if (!empty($_GET['building'])) {
            $query .= " AND building = :building";
            $params['building'] = $_GET['building'];
        }

        $sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'asc';
        $query .= " ORDER BY price " . ($sortOrder === 'desc' ? 'DESC' : 'ASC');

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        // Loop through fetched room offers and generate HTML dynamically
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bookingQuery = "SELECT COUNT(*) FROM bookings WHERE room_number = :room_number";
            $bookingStmt = $pdo->prepare($bookingQuery);
            $bookingStmt->execute(['room_number' => $row['room_number']]);
            $currentBookings = $bookingStmt->fetchColumn();

            if (isset($_GET['building'])) {
                if ($_GET['building'] == $row['building']) {
                    if ($currentBookings < $row['room_capacity'] || (isset($_SESSION['account']) && $_SESSION['account'] == "A")) {
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
                if ($row['building'] == 1) {
                    if ($currentBookings < $row['room_capacity'] || (isset($_SESSION['account']) && $_SESSION['account'] == "A")) {
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
        }
        ?>
    </div>
</div>

<script src="js/building_filter.js"></script>
<script src="js/filter.js"></script>
<script src="js/bookmark.js"></script>

</body>
</html>
