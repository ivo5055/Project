<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Редактиране на оферта за стая</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>
<?php include 'elements/header.php'; ?>

<?php include "includes/dbh.inc.php"; ?>

<div class="edit-room-offer">
    <h1 data-translate="true">Редактиране на оферта за стая</h1>
    <?php
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Fetch room details from the database
        $query = "SELECT * FROM room WHERE Id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($room) {
            if (isset($_POST['edit_room'])) {
                // Get updated room details from the form
                $building = $_POST['building'];
                $new_room_number = $_POST['room_number'];
                $room_capacity = $_POST['room_capacity'];
                $description = $_POST['description'];
                $price = $_POST['price'];
                $gender_R = $_POST['gender_R'];
                $amenities = isset($_POST['amenities']) ? implode(', ', $_POST['amenities']) : '';

                // Handle image upload if a new image is uploaded
                if (!empty($_FILES['my_image']['name'])) {
                    $img_name = $_FILES['my_image']['name'];
                    $img_size = $_FILES['my_image']['size'];
                    $tmp_name = $_FILES['my_image']['tmp_name'];
                    $error = $_FILES['my_image']['error'];

                    if ($error === 0) {
                        if ($img_size > 30000000) {
                            $em = "Sorry, your file is too large.";
                            exit();
                        } else {
                            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                            $img_ex_lc = strtolower($img_ex);
                            $allowed_exs = array("jpg", "jpeg", "png");

                            if (in_array($img_ex_lc, $allowed_exs)) {
                                $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
                                $img_upload_path = 'img/' . $new_img_name;
                                move_uploaded_file($tmp_name, $img_upload_path);
                            } else {
                                $em = "Не можете да качвате файлове от този тип";
                                exit();
                            }
                        }
                    }
                } else {
                    $new_img_name = $room['image_url']; // Retain old image if no new image is uploaded
                }

                // Update room details in the database
                $updateQuery = "UPDATE room SET building = :building, room_number = :new_room_number, room_capacity = :room_capacity, description = :description, price = :price, image_url = :image_url, gender_R = :gender_R, amenities = :amenities WHERE Id = :id";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->execute([
                    'building' => $building,
                    'new_room_number' => $new_room_number,
                    'room_capacity' => $room_capacity,
                    'description' => $description,
                    'price' => $price,
                    'image_url' => $new_img_name,
                    'gender_R' => $gender_R,
                    'amenities' => $amenities,
                    'id' => $id
                ]);

                echo '<p data-translate="true" style="color: red;">Детайлите на стаята бяха актуализирани успешно!</p>';
            }
            ?>
            <form method="post" action="" enctype="multipart/form-data" class="edit-room-offer-form">
                <label for="my_image" data-translate="true">Качи ново изображение (ако е необходимо):</label>
                <input type="file" name="my_image" id="my_image">

                <div class="form-grid">
                    <div class="form-grid-item">
                        <label for="building" data-translate="true">Блок:</label>
                        <select name="building" id="building" required>
                            <?php
                            // Populate building numbers
                            for ($i = 1; $i <= 6; $i++) {
                                $selected = ($room['building'] == $i) ? 'selected' : '';
                                echo "<option value=\"$i\" $selected>$i</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-grid-item">
                        <label for="room_number" data-translate="true">Номер на стаята:</label>
                        <input type="number" name="room_number" id="room_number" value="<?php echo htmlspecialchars($room['room_number']); ?>" required>
                    </div>

                    <div class="form-grid-item">
                        <label for="room_capacity" data-translate="true">Капацитет на стаята:</label>
                        <select name="room_capacity" id="room_capacity" required>
                            <?php
                            // Populate room capacities
                            for ($i = 1; $i <= 4; $i++) {
                                $selected = ($room['room_capacity'] == $i) ? 'selected' : '';
                                echo "<option value=\"$i\" $selected>$i</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-grid-item">
                        <label for="price" data-translate="true">Цена:</label>
                        <input type="text" name="price" id="price" value="<?php echo htmlspecialchars($room['price']); ?>" required>
                    </div>
                </div>

                <label for="description" data-translate="true">Описание:</label>
                <textarea name="description" id="description" required><?php echo htmlspecialchars($room['description']); ?></textarea>

                <div class="radio-group">
                    <label data-translate="true">За:</label>
                    <input type="radio" id="male" name="gender_R" value="male" <?php if ($room['gender_R'] == 'male') echo 'checked'; ?>>
                    <label for="male" data-translate="true">Мъж</label>
                    <input type="radio" id="female" name="gender_R" value="female" <?php if ($room['gender_R'] == 'female') echo 'checked'; ?>>
                    <label for="female" data-translate="true">Жена</label>
                </div>

                <div class="amenities-group">
                    <label data-translate="true">Удобства:</label>
                    <?php
                    $amenitiesList = [
                        'WiFi' => 'wifi',
                        'Хладилник' => 'fridge',
                        'Самостоятелна баня' => 'bathroom',
                        'Климатик' => 'air_conditioning',
                        'Пералня' => 'washing_machine',
                        'Сушилня' => 'dryer'
                    ];
                    foreach ($amenitiesList as $name => $value) {
                        $checked = in_array($value, explode(', ', $room['amenities'])) ? 'checked' : '';
                        echo "<div class=\"amenity\">
                                <input type=\"checkbox\" id=\"$value\" name=\"amenities[]\" value=\"$value\" $checked>
                                <label for=\"$value\" title=\"$name\">
                                    <img src=\"img/$value.png\" alt=\"$name\">
                                </label>
                              </div>";
                    }
                    ?>
                </div>

                <button type="submit" name="edit_room" class="button" data-translate="true">Запази промените</button>
            </form>
            <?php
        } else {
            echo '<p data-translate="true">Стаята не беше намерена.</p>';
        }
    } else {
        echo '<p data-translate="true">Не е предоставен номер на стая.</p>';
    }
    ?>
</div>
</body>
</html>
