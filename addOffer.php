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
                <?php if (isset($_GET['error'])) {echo "<div style='color: red;'>" . htmlspecialchars($_GET['error']) . "</div>";}?>  
                <input type="file" id="image" name="my_image" required>
                
                <div class="form-grid">
                    <div class="form-grid-item">
                        <label for="building" data-translate="true">Номер на сградата:</label>
                        <select id="building" name="building" required>
                            <option value="1" data-translate="true">1</option>
                            <option value="2" data-translate="true">2</option>
                            <option value="3" data-translate="true">3</option>
                            <option value="4" data-translate="true">4</option>
                            <option value="5" data-translate="true">5</option>
                            <option value="6" data-translate="true">6</option>
                        </select>
                    </div>

                    <div class="form-grid-item">
                        <label for="room_number" data-translate="true">Номер на стаята:</label>
                        <?php if (isset($_GET['errorN'])) {echo "<div style='color: red;'>" . htmlspecialchars($_GET['errorN']) . "</div>";}?>
                        <input type="text" id="room_number" name="room_number" required>
                    </div>

                    <div class="form-grid-item">
                        <label for="room_capacity" data-translate="true">Капацитет на стаята:</label>
                        <select id="room_capacity" name="room_capacity" required>
                            <option value="1" data-translate="true">1</option>
                            <option value="2" data-translate="true">2</option>
                            <option value="3" data-translate="true">3</option>
                            <option value="4" data-translate="true">4</option>
                        </select>
                    </div>

                    <div class="form-grid-item">
                        <label for="price" data-translate="true">Цена:</label>
                        <input type="text" id="price" name="price" required>
                    </div>
                </div>

                <label for="description" data-translate="true">Описание:</label>
                <textarea id="description" name="description" rows="4" required></textarea>

                <div class="radio-group">
                    <label data-translate="true">За:</label>
                    <input type="radio" id="male" name="gender_R" value="male" checked>
                    <label for="male" data-translate="true">Мъж</label>
                    <input type="radio" id="female" name="gender_R" value="female">
                    <label for="female" data-translate="true">Жена</label>
                </div>

                <div class="amenities-group">
                    <label data-translate="true">Удобства:</label>
                    <div class="amenity">
                        <input type="checkbox" id="wifi" name="amenities[]" value="WiFi">
                        <label for="wifi" title="WiFi">
                            <img src="img/wifi.png" alt="WiFi">
                        </label>
                    </div>
                    <div class="amenity">
                        <input type="checkbox" id="fridge" name="amenities[]" value="fridge">
                        <label for="fridge" title="Хладилник">
                            <img src="img/fridge.png" alt="Хладилник">
                        </label>
                    </div>
                    <div class="amenity">
                        <input type="checkbox" id="bathroom" name="amenities[]" value="bathroom">
                        <label for="bathroom" title="Самостоятелна баня">
                            <img src="img/bathroom.png" alt="Самостоятелна баня">
                        </label>
                    </div>
                    <div class="amenity">
                        <input type="checkbox" id="air_conditioning" name="amenities[]" value="air_conditioning">
                        <label for="air_conditioning" title="Климатик">
                            <img src="img/air_conditioning.png" alt="Климатик">
                        </label>
                    </div>
                    <div class="amenity">
                        <input type="checkbox" id="washing_machine" name="amenities[]" value="washing_machine">
                        <label for="washing_machine" title="Пералня">
                            <img src="img/washing_machine.png" alt="Пералня">
                        </label>
                    </div>
                    <div class="amenity">
                        <input type="checkbox" id="dryer" name="amenities[]" value="dryer">
                        <label for="dryer" title="Сушилня">
                            <img src="img/dryer.png" alt="Сушилня">
                        </label>
                    </div>
                </div>

                <button type="submit" data-translate="true">Публикувай</button>
            </form>
        </div>
    </div>
</body>
</html>
