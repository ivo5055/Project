<?php

if (isset($_SESSION['username']) && isset($_POST['rating']) && isset($_POST['room_number'])) {
    $username = $_SESSION['username'];
    $rating = $_POST['rating'];
    $room_number = $_POST['room_number'];

    // Check if the user has already rated this room
    $checkRatedQuery = "SELECT has_rated FROM bookings WHERE userN = :username AND room_number = :room_number";
    $checkRatedStmt = $pdo->prepare($checkRatedQuery);
    $checkRatedStmt->execute(['username' => $username, 'room_number' => $room_number]);
    $hasRated = $checkRatedStmt->fetchColumn();

    if (!$hasRated) {
        // Update total_rating and number_of_reviews in room table
        $updateRoomQuery = "UPDATE room
                            SET total_rating = total_rating + :rating, 
                                number_of_reviews = number_of_reviews + 1
                            WHERE room_number = :room_number";
        $updateRoomStmt = $pdo->prepare($updateRoomQuery);
        $updateRoomStmt->execute(['rating' => $rating, 'room_number' => $room_number]);

        // Update the booking to set has_rated to true
        $updateBookingQuery = "UPDATE bookings SET has_rated = 1 WHERE userN = :username AND room_number = :room_number";
        $updateBookingStmt = $pdo->prepare($updateBookingQuery);
        $updateBookingStmt->execute(['username' => $username, 'room_number' => $room_number]);

        echo "Rating submitted!";
    } else {
        echo "You have already rated this room.";
    }
} else {
    echo "Invalid request.";
}
?>
