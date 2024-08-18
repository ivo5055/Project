<!-- Hidden input to store user ID -->
<input type="hidden" id="userId" value="<?php echo htmlspecialchars($userId); ?>">

<?php
// Fetch the user's bookmarked rooms
$userId = isset($_SESSION['Id']) ? $_SESSION['Id'] : null;
$booked_rooms = [];
if ($userId) {
    $bookmarks_query = "SELECT room_id FROM bookmarks WHERE user_id = :user_id";
    $bookmarks_stmt = $pdo->prepare($bookmarks_query);
    $bookmarks_stmt->execute([':user_id' => $userId]);
    $booked_rooms = $bookmarks_stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Room details
$bookingQuery = "SELECT COUNT(*) FROM bookings WHERE room_number = :room_number";
$bookingStmt = $pdo->prepare($bookingQuery);

echo '<div class="room-offer">';
echo '<a href="room_details.php?room_number=' . htmlspecialchars($row['room_number']) . '&building=' . htmlspecialchars($row['building']) . '" data-translate="true">';
echo '<img src="img/' . htmlspecialchars($row['image_url']) . '" alt="Room Image" data-translate="true">';
echo '</a>';
echo '<div class="offer-details">';
echo '<p data-translate="true">Номер на стая: ' . htmlspecialchars($row['room_number']) . '</p>';

// Show how many rooms are booked
$bookingStmt->execute(['room_number' => $row['room_number']]);
$currentBookings = $bookingStmt->fetchColumn();
echo '<p data-translate="true">Живущи: ' . htmlspecialchars($currentBookings) . ' / ' . htmlspecialchars($row['room_capacity']) . '</p>';

// Calculate and display the average rating and number of reviews
$averageRating = $row['number_of_reviews'] > 0 ? round($row['total_rating'] / $row['number_of_reviews'], 1) : 0;
echo '<p data-translate="true">Рейтинг: ' . htmlspecialchars($averageRating) . '/5 (' . htmlspecialchars($row['number_of_reviews']) . ' reviews)</p>';
echo '<p data-translate="true">Цена: ' . htmlspecialchars($row['price']) . ' лв. на месец</p>';

// Link to room details
echo '<p><a href="room_details.php?room_number=' . htmlspecialchars($row['room_number']) . '&building=' . htmlspecialchars($row['building']) . '" class="button" data-translate="true">Резервирай</a></p>';

// Check if the room is bookmarked
$isBookmarked = in_array($row['Id'], $booked_rooms) ? 'bookmarked' : '';

// Bookmark button
echo '<button class="bookmark-button ' . $isBookmarked . '" data-offer-id="' . htmlspecialchars($row['Id']) . '" onclick="bookmarkRoom(this)" data-translate="true">';
echo $isBookmarked ? 'Unbookmark' : 'Bookmark';
echo '</button>';

// Admin options
if (isset($_SESSION['account']) && $_SESSION['account'] == 'A') {
    echo '<form method="post" action="">';
    echo '<input type="hidden" name="Id" value="' . htmlspecialchars($row['Id']) . '">'; // Hidden input to send room ID
    
    // Edit button
    echo '<a href="editOffer.php?room_number=' . htmlspecialchars($row['room_number']) . '&building=' . htmlspecialchars($row['building']) . ' " class="button" data-translate="true">Редактирай</a> <a> </a>';
     
    // Delete button
    echo '<button type="submit" name="delete_room" class="button" data-translate="true">Изтрий</button>'; // Delete button
    echo '</form>';
}
echo '</div>';
echo '</div>';
?>
