<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Offer</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>
    
    <?php include 'elements/header.php'; ?>

    <div class="">
        <h1>Add Offer</h1>
        <div class="add-offer-form">
            <form action="includes/addOfferI.php" method="post" enctype="multipart/form-data">

                
                <input type="file" name="my_image" required>
                <br></br>

                <label for="room_number">Room Number:</label>
                <input type="text" id="room_number" name="room_number" required><br><br>

                <label for="room_capacity">Room Capacity:</label>
                <input type="text" id="room_capacity" name="room_capacity" required><br><br>

                <label for="description">Description:</label>
                <input type="text" id="description" name="description" required><br><br>

                <label for="price">Price:</label>
                <input type="text" id="price" name="price" required><br><br>

                <button type="submit">Post</button>
            </form>
        </div>
    </div>
</body>
</html>
