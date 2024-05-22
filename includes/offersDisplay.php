<?php
echo '<div class="room-offer">';
echo '<a href="room_details.php?room_number=' . htmlspecialchars($row['room_number']) . '">';
echo '<img src="img/' . htmlspecialchars($row['image_url']) . '" alt="Room Image">';
echo '</a>';
echo '<div class="offer-details">';
echo '<p>Room number: ' . htmlspecialchars($row['room_number']) . '</p>';

// Show how many rooms are booked
echo '<p>Booked: ' . htmlspecialchars($currentBookings) . ' / ' . htmlspecialchars($row['room_capacity']) . '</p>';

// Calculate and display the average rating and number of reviews
$averageRating = $row['number_of_reviews'] > 0 ? round($row['total_rating'] / $row['number_of_reviews'], 1) : 0;
echo '<p>Rating: ' . htmlspecialchars($averageRating) . '/5 (' . htmlspecialchars($row['number_of_reviews']) . ' reviews)</p>';
echo '<p>Price: $' . htmlspecialchars($row['price']) . ' per month</p>';

// Link to room details
echo '<p><a href="room_details.php?room_number=' . htmlspecialchars($row['room_number']) . '" class="button">Book Now</a></p>';

// Admin delete button
if (isset($_SESSION['account']) && $_SESSION['account'] == 'A') {
    echo '<form method="post" action="">';
    echo '<input type="hidden" name="Id" value="' . htmlspecialchars($row['Id']) . '">'; // Hidden input to send room ID
    echo '<button type="submit" name="delete_room" class="button">Delete</button>'; // Delete button
    echo '</form>';
}
echo '</div>';
echo '</div>';
?>
