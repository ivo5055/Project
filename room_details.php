<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Детайли за стаята</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="room_details.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>
<?php include 'elements/header.php'; ?>

<h1 class="room-details-header" data-translate="true">Детайли за стаята</h1>
<?php
    // Include database connection file
    include "includes/dbh.inc.php";

    // Check if room_number and building are provided in the URL
    if (isset($_GET['room_number']) && isset($_GET['building'])) {
        $room_number = $_GET['room_number'];
        $building = $_GET['building'];
        
        // Fetch room details from database
        $query = "SELECT * FROM room WHERE room_number = :room_number AND building = :building";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['room_number' => $room_number, 'building' => $building]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($room) {
            $averageRating = $room['number_of_reviews'] > 0 ? round($room['total_rating'] / $room['number_of_reviews'], 1) : 0;
            echo '<div class="room-details">';
            echo '<div class="room-image">';
            echo '<img src="img/' . htmlspecialchars($room['image_url']) . '" alt="Снимка на стаята" onclick="openModal(this.src)" data-translate="true">';
            echo '</div>';
            echo '<div class="room-info">';
            echo '<p><strong data-translate="true">Номер на стаята:</strong> ' . htmlspecialchars($room['room_number']) . '</p>';
            echo '<p><strong data-translate="true">Сграда:</strong> ' . htmlspecialchars($room['building']) . '</p>';
            echo '<p><strong data-translate="true">Капацитет:</strong> ' . htmlspecialchars($room['room_capacity']) . '</p>';
            echo '<p><strong data-translate="true">Описание:</strong> ' . htmlspecialchars($room['description']) . '</p>';
            echo '<p><strong data-translate="true">Оценка:</strong> ' . htmlspecialchars($averageRating) . '/5 (' . htmlspecialchars($room['number_of_reviews']) . ' отзива)</p>';
            echo '<p><strong data-translate="true">Цена:</strong> $' . htmlspecialchars($room['price']) . ' на месец</p>';
            
            // Check if user is logged in
            if (isset($_SESSION['username'])) {
                $username = $_SESSION['username'];

                // Check if user already booked a room
                $checkBookingQuery = "SELECT COUNT(*) FROM bookings WHERE userN = :username";
                $checkBookingStmt = $pdo->prepare($checkBookingQuery);
                $checkBookingStmt->execute(['username' => $username]);
                $userBookingCount = $checkBookingStmt->fetchColumn();

                if ($userBookingCount > 0) {
                    echo '<p data-translate="true">Вие вече сте резервирали стая. Можете да резервирате само една стая наведнъж.</p>';
                } else {
                    
                    // Check current bookings for this room
                    $checkRoomBookingQuery = "SELECT COUNT(*) FROM bookings WHERE room_number = :room_number AND building = :building";
                    $checkRoomBookingStmt = $pdo->prepare($checkRoomBookingQuery);
                    $checkRoomBookingStmt->execute(['room_number' => $room_number, 'building' => $building]);
                    $currentRoomBookings = $checkRoomBookingStmt->fetchColumn();

                    if ($currentRoomBookings < $room['room_capacity']) {
                        echo '<form method="get" action="book.php">';
                        echo '<input type="hidden" name="room_number" value="' . htmlspecialchars($room_number) . '">';
                        echo '<input type="hidden" name="building" value="' . htmlspecialchars($building) . '">';
                        echo '<button type="submit" class="button" data-translate="true">Резервирай сега</button>';
                        echo '</form>';
                    } else {
                        echo '<p data-translate="true">Тази стая е напълно резервирана. Моля, изберете друга стая.</p>';
                    }
                }
            } else {
                echo '<p data-translate="true">Моля, <a href="login.php" data-translate="true">влезте</a>, за да резервирате тази стая.</p>';
            }

            echo '</div>'; // Close .room-info
            echo '</div>'; // Close .room-details
        } else {
            echo '<p data-translate="true">Стая не е намерена.</p>';
        }
    } else {
        echo '<p data-translate="true">Не е предоставен номер на стаята или сграда.</p>';
    }
?>

<!-- Modal for image zoom -->
<div id="imageModal" class="modal">
    <span class="modal-close" onclick="closeModal()" data-translate="true">&times;</span>
    <img class="modal-content" id="modalImage" data-translate="true">
</div>

<script>
// Function to open the modal and show the clicked image
function openModal(src) {
    var modal = document.getElementById('imageModal');
    var modalImg = document.getElementById('modalImage');
    modal.style.opacity = '0'; // Ensure fade-in effect
    modal.classList.add('show');
    setTimeout(() => { // Delay setting image source to allow fade-in
        modalImg.src = src;
        modalImg.classList.remove('zoomed'); // Ensure zoom is not applied initially
        modal.style.opacity = '1'; // Fade-in effect
    }, 300); // Match with modal opacity transition duration
}

// Function to close the modal
function closeModal() {
    var modal = document.getElementById('imageModal');
    var modalImg = document.getElementById('modalImage');
    modal.style.opacity = '0'; // Fade out effect
    modalImg.classList.remove('zoomed'); // Remove zoom effect
    setTimeout(() => { // Delay hiding modal to allow fade-out
        modal.classList.remove('show');
    }, 300); // Match with modal opacity transition duration
}

// Close the modal when clicking outside of the image
window.onclick = function(event) {
    var modal = document.getElementById('imageModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Handle mouse movement over the image to zoom in
document.getElementById('modalImage').addEventListener('mousemove', function(event) {
    var rect = this.getBoundingClientRect();
    var x = event.clientX - rect.left;
    var y = event.clientY - rect.top;

    // Apply zoom effect and set transform-origin based on mouse position
    this.classList.add('zoomed');
    this.style.transformOrigin = `${x}px ${y}px`;
});

// Remove zoom effect when mouse leaves the image
document.getElementById('modalImage').addEventListener('mouseleave', function() {
    this.classList.remove('zoomed');
    this.style.transformOrigin = 'center'; // Reset transform origin
});
</script>

</body>
</html>
