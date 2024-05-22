<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room Offer</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>
<?php include 'elements/header.php'; ?>

<?php include "includes/dbh.inc.php"; ?>

<div class="edit-room-offer">
    <h1>Edit Room Offer</h1>
    <?php
    // Check if room_number is provided in the URL
    if (isset($_GET['room_number'])) {
        $room_number = $_GET['room_number'];

        // Fetch room details from the database
        $query = "SELECT * FROM room WHERE room_number = :room_number";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['room_number' => $room_number]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($room) {
            if (isset($_POST['edit_room'])) {
                // Get updated room details from the form
                $new_room_number = $_POST['room_number'];
                $room_capacity = $_POST['room_capacity'];
                $description = $_POST['description'];
                $price = $_POST['price'];

                // Update room details in the database
                $updateQuery = "UPDATE room SET room_number = :new_room_number, room_capacity = :room_capacity, description = :description, price = :price WHERE room_number = :room_number";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->execute([
                    'new_room_number' => $new_room_number,
                    'room_capacity' => $room_capacity,
                    'description' => $description,
                    'price' => $price,
                    'room_number' => $room_number
                ]);

                echo '<p>Room details updated successfully!</p>';
            }
            ?>
            <form method="post" action="">
                <label for="room_number">Room Number:</label>
                <input type="number" name="room_number" id="room_number" value="<?php echo htmlspecialchars($room['room_number']); ?>" required><br>

                <label for="room_capacity">Room Capacity:</label>
                <select name="room_capacity" id="room_capacity" required>
                    <option value="1" <?php if ($room['room_capacity'] == 1) echo 'selected'; ?>>1</option>
                    <option value="2" <?php if ($room['room_capacity'] == 2) echo 'selected'; ?>>2</option>
                    <option value="3" <?php if ($room['room_capacity'] == 3) echo 'selected'; ?>>3</option>
                    <option value="4" <?php if ($room['room_capacity'] == 4) echo 'selected'; ?>>4</option>
                </select><br>

                <label for="description">Description:</label>
                <textarea name="description" id="description" required><?php echo htmlspecialchars($room['description']); ?></textarea><br>

                <label for="price">Price:</label>
                <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($room['price']); ?>" required><br>

                <button type="submit" name="edit_room" class="button">Save Changes</button>
            </form>
            <?php
        } else {
            echo '<p>Room not found.</p>';
        }
    } else {
        echo '<p>No room number provided.</p>';
    }
    ?>
</div>

</body>
</html>
