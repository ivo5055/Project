<?php
// Fetch the room IDs from bookmarks table
$bookmarks_query = "SELECT DISTINCT room_id FROM bookmarks";
$bookmarks_result = $pdo->query($bookmarks_query);

// Store booked room IDs in an array
$booked_rooms = [];
while ($bookmark_row = $bookmarks_result->fetch(PDO::FETCH_ASSOC)) {
    $booked_rooms[] = $bookmark_row['room_id'];
}


$bookingQuery = "SELECT COUNT(*) FROM bookings WHERE room_number = :room_number";
$bookingStmt = $pdo->prepare($bookingQuery);
$bookingStmt->execute(['room_number' => $row['room_number']]);
$currentBookings = $bookingStmt->fetchColumn();


echo '<div class="room-offer">';
echo '<a href="room_details.php?room_number=' . htmlspecialchars($row['room_number']) . '&building=' . htmlspecialchars($row['building']) . '">';
echo '<img src="img/' . htmlspecialchars($row['image_url']) . '" alt="Room Image">';
echo '</a>';
echo '<div class="offer-details">';
echo '<p>Room number: ' . htmlspecialchars($row['room_number']) . '</p>';

// Show how many rooms are booked
echo '<p>Booked: ' . htmlspecialchars($currentBookings) . ' / ' .htmlspecialchars($row['room_capacity']) . '</p>';

// Calculate and display the average rating and number of reviews
$averageRating = $row['number_of_reviews'] > 0 ? round($row['total_rating'] / $row['number_of_reviews'], 1) : 0;
echo '<p>Rating: ' . htmlspecialchars($averageRating) . '/5 (' . htmlspecialchars($row['number_of_reviews']) . ' reviews)</p>';
echo '<p>Price: $' . htmlspecialchars($row['price']) . ' per month</p>';

// Link to room details
echo '<p><a href="room_details.php?room_number=' . htmlspecialchars($row['room_number']) . '&building=' . htmlspecialchars($row['building']) . '" class="button">Book Now</a></p>';

// Check if the room is bookmarked
$isBookmarked = in_array($row['Id'], $booked_rooms) ? 'bookmarked' : '';

// Bookmark button
echo '<button class="bookmark-button ' . $isBookmarked . '" data-offer-id="' . htmlspecialchars($row['Id']) . '" onclick="bookmarkRoom(this, ' . htmlspecialchars($row['Id']) . ')"></button>';

// Admin options
if (isset($_SESSION['account']) && $_SESSION['account'] == 'A') {
    echo '<form method="post" action="">';
    echo '<input type="hidden" name="Id" value="' . htmlspecialchars($row['Id']) . '">'; // Hidden input to send room ID
    
    // Edit button
    echo '<a href="editOffer.php?room_number=' . htmlspecialchars($row['room_number']) . '&building=' . htmlspecialchars($row['building']) . ' " class="button">Edit</a> <a> </a>'; 
     
    // Delete button
    echo '<button type="submit" name="delete_room" class="button">Delete</button>'; // Delete button
    echo '</form>';
}
echo '</div>';
echo '</div>';
?>

