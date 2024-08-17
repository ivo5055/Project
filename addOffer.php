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

    <div class="add-offer-container">
        <h1>Add Offer</h1>
        <div class="add-offer-form">
            <form action="includes/addOfferI.php" method="post" enctype="multipart/form-data">

                <label for="image">Upload Image:</label>
                <input type="file" id="image" name="my_image" required>

                <label for="building">Building Number:</label>
                <select id="building" name="building" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>

                <label for="room_number">Room Number:</label>
                <input type="text" id="room_number" name="room_number" required>

                <label for="room_capacity">Room Capacity:</label>
                <select id="room_capacity" name="room_capacity" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>

                <label for="description">Description:</label>
                <input type="text" id="description" name="description" required>

                <label for="price">Price:</label>
                <input type="text" id="price" name="price" required>

                <div class="radio-group">
                    <label>For:</label>
                    <input type="radio" id="male" name="gender_R" value="male" checked>
                    <label for="male">Male</label>
                    <input type="radio" id="female" name="gender_R" value="female">
                    <label for="female">Female</label>
                </div>


                <button type="submit">Post</button>
            </form>
        </div>
    </div>
</body>
</html>
