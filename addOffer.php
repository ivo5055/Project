<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Добавяне на оферта</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>
    
    <?php include 'elements/header.php'; ?>

    <div class="add-offer-container">
        <h1 data-translate="true">Добавяне на оферта</h1>
        <div class="add-offer-form">
            <form action="includes/addOfferI.php" method="post" enctype="multipart/form-data">

                <label for="image" data-translate="true">Качи изображение:</label>
                <input type="file" id="image" name="my_image" required>

                <label for="building" data-translate="true">Номер на сградата:</label>
                <select id="building" name="building" required>
                    <option value="1" data-translate="true">1</option>
                    <option value="2" data-translate="true">2</option>
                    <option value="3" data-translate="true">3</option>
                    <option value="4" data-translate="true">4</option>
                    <option value="5" data-translate="true">5</option>
                    <option value="6" data-translate="true">6</option>
                </select>

                <label for="room_number" data-translate="true">Номер на стаята:</label>
                <input type="text" id="room_number" name="room_number" required>

                <label for="room_capacity" data-translate="true">Капацитет на стаята:</label>
                <select id="room_capacity" name="room_capacity" required>
                    <option value="1" data-translate="true">1</option>
                    <option value="2" data-translate="true">2</option>
                    <option value="3" data-translate="true">3</option>
                    <option value="4" data-translate="true">4</option>
                </select>

                <label for="description" data-translate="true">Описание:</label>
                <input type="text" id="description" name="description" required>

                <label for="price" data-translate="true">Цена:</label>
                <input type="text" id="price" name="price" required>

                <div class="radio-group">
                    <label data-translate="true">За:</label>
                    <input type="radio" id="male" name="gender_R" value="male" checked>
                    <label for="male" data-translate="true">Мъж</label>
                    <input type="radio" id="female" name="gender_R" value="female">
                    <label for="female" data-translate="true">Жена</label>
                </div>

                <button type="submit" data-translate="true">Публикувай</button>
            </form>
        </div>
    </div>
</body>
</html>
