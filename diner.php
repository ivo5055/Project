<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Меню</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body class="hideS">
    
<?php 
session_start();
include 'elements/header.php'; 
?>

<div class="diner-container">
    <img src="img/menuBG.jpg" alt="Образ на меню" data-translate="true">

    <!-- Dropdown to select the day of the week -->
    <?php if (isset($_SESSION['account']) && $_SESSION['account'] == 'C'): ?>
    <div class="day-selector-container">
        <form method="GET">
            <label for="day-select" data-translate="true">Избери ден:</label>
            <select id="day-select" name="day" onchange="this.form.submit()">
                <option value="sel" data-translate="true">-------------</option>
                <option value="mon" <?= (isset($_GET['day']) && $_GET['day'] === 'mon') ? 'selected' : '' ?> data-translate="true">Понеделник</option>
                <option value="tue" <?= (isset($_GET['day']) && $_GET['day'] === 'tue') ? 'selected' : '' ?> data-translate="true">Вторник</option>
                <option value="wed" <?= (isset($_GET['day']) && $_GET['day'] === 'wed') ? 'selected' : '' ?> data-translate="true">Сряда</option>
                <option value="thu" <?= (isset($_GET['day']) && $_GET['day'] === 'thu') ? 'selected' : '' ?> data-translate="true">Четвъртък</option>
                <option value="fri" <?= (isset($_GET['day']) && $_GET['day'] === 'fri') ? 'selected' : '' ?> data-translate="true">Петък</option>
                <option value="sat" <?= (isset($_GET['day']) && $_GET['day'] === 'sat') ? 'selected' : '' ?> data-translate="true">Събота</option>
                <option value="sun" <?= (isset($_GET['day']) && $_GET['day'] === 'sun') ? 'selected' : '' ?> data-translate="true">Неделя</option>
            </select>
        </form>
    </div>
    <?php endif; ?>

    <div class="menu-items">
        <ul>
        <?php
        $currentDay = strtolower(date('D'));

        if (isset($pdo)) {
            try {
                $dayOfWeek = isset($_GET['day']) ? strtolower($_GET['day']) : $currentDay;

                $dayMapping = [
                    'mon' => ['name' => 'nameM', 'price' => 'priceM'],
                    'tue' => ['name' => 'nameTu', 'price' => 'priceTu'],
                    'wed' => ['name' => 'nameWe', 'price' => 'priceWe'],
                    'thu' => ['name' => 'nameTh', 'price' => 'priceTh'],
                    'fri' => ['name' => 'nameFr', 'price' => 'priceFr'],
                    'sat' => ['name' => 'nameSa', 'price' => 'priceSa'],
                    'sun' => ['name' => 'nameSu', 'price' => 'priceSu']
                ];

                if (array_key_exists($dayOfWeek, $dayMapping)) {
                    $nameField = $dayMapping[$dayOfWeek]['name'];
                    $priceField = $dayMapping[$dayOfWeek]['price'];

                    $sql = "SELECT Id, $nameField, $priceField FROM menu_items";
                    $stmt = $pdo->query($sql);

                    if ($stmt->rowCount() > 0) {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $itemId = $row['Id'];
                            $itemName = $row[$nameField];
                            $itemPrice = $row[$priceField];

                            if ($itemPrice != "0") {
                                echo "<li>";
                                echo "<span class='item-name' data-translate='true'>" . htmlspecialchars($itemName) . "</span>";
                                echo "<span class='item-price' data-translate='true'>" . number_format($itemPrice, 2) ."лв.". "</span>";

                                // Reservation form for users with account type 'A' or 'U'
                                if (isset($_SESSION['account']) && ($_SESSION['account'] == 'A' || $_SESSION['account'] == 'U')) {
                                    echo "<form method='POST' style='display:inline;'>";
                                    echo "<input type='hidden' name='item_id' value='$itemId'>";
                                    echo "<input type='hidden' name='user_name' value='" . htmlspecialchars($_SESSION["username"]) . "'>";
                                    echo "<button type='button' class='reserve-button' onclick='addToBasket(\"$itemId\", \"" . htmlspecialchars($itemName) . "\", \"" . number_format($itemPrice, 2) . "\")' data-translate='true'>Запази</button>";
                                    echo "</form>";
                                }

                                // Delete button only available for account type 'C'
                                if (isset($_SESSION['account']) && $_SESSION['account'] == 'C') {
                                    echo "<a href='includes/delete_item.php?Id=$itemId' class='delete-button' onclick=\"return confirm('Сигурен ли си, че искаш да изтриеш този елемент?');\" data-translate='true'>Изтрий</a>";
                                }

                                echo "</li>";
                            }
                        }
                    } else {
                        echo "<li data-translate='true'>Няма налични ястия за този ден.</li>";
                    }
                }
            } catch (PDOException $e) {
                echo "Error: " . htmlspecialchars($e->getMessage());
            }
        } else {
            echo "<li data-translate='true'>Не е установена връзка с базата данни.</li>";
        }
        ?>
        </ul>
    </div>

<?php
// Handle form submission for adding menu items
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['reserve']) && !isset($_POST['confirm_reserve'])) {
    $dayOfWeek = $_POST['day'] ?? '';
    $itemName = $_POST['name'] ?? '';
    $itemPrice = $_POST['price'] ?? '';

    $dayOfWeek = htmlspecialchars($dayOfWeek);
    $itemName = htmlspecialchars($itemName);
    $itemPrice = htmlspecialchars($itemPrice);

    // Map abbreviated days to corresponding database fields
    $dayMapping = [
        'mon' => ['name' => 'nameM', 'price' => 'priceM'],
        'tue' => ['name' => 'nameTu', 'price' => 'priceTu'],
        'wed' => ['name' => 'nameWe', 'price' => 'priceWe'],
        'thu' => ['name' => 'nameTh', 'price' => 'priceTh'],
        'fri' => ['name' => 'nameFr', 'price' => 'priceFr'],
        'sat' => ['name' => 'nameSa', 'price' => 'priceSa'],
        'sun' => ['name' => 'nameSu', 'price' => 'priceSu']
    ];

    if (array_key_exists($dayOfWeek, $dayMapping)) {
        $nameField = $dayMapping[$dayOfWeek]['name'];
        $priceField = $dayMapping[$dayOfWeek]['price'];

        // Prepare and execute SQL query for insertion
        $sql = "INSERT INTO menu_items ($nameField, $priceField) VALUES (:name, :price)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $itemName,
            ':price' => $itemPrice
        ]);

        echo "<p data-translate='true'>Продуктът беше добавен успешно!</p>";
    } else {
        echo "<p data-translate='true'>Невалиден ден от седмицата.</p>";
    }
}

// Handle confirmation of reserved items
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_reserve'])) {
    $selectedItems = isset($_POST['selected_items']) ? json_decode($_POST['selected_items'], true) : [];

    foreach ($selectedItems as $item) {
        $itemId = $item['Id'];
        $userName = $_SESSION['username'];

        $sql = "INSERT INTO reserved_items (item_id, user_name) VALUES (:item_id, :user_name)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':item_id' => $itemId,
            ':user_name' => $userName
        ]);
    }

    echo "<p data-translate='true'>Продуктът беше добавен успешно!</p>";
}   
?>

<?php if (isset($_SESSION['account']) && $_SESSION['account'] != 'C'): ?>

<!-- Basket Container -->
<div class="reserved-items-container">
    <h2 data-translate="true">Твоята Кошница</h2>
    <ul id="basket-items">
        
    </ul>
    <button id="confirm-basket" class="reserve-button" onclick="confirmBasket()" data-translate="true">Потвърди</button>
    <button id="clear-basket" class="delete-button" onclick="clearBasket()" data-translate="true">Изчисти</button>
    <script src="js/reserve_list.js"></script>
</div>

<?php endif; ?>

<!-- Reserved Items Container ChefA -->
<?php if (isset($_SESSION['account']) && $_SESSION['account'] == 'C'): ?>
    <div class="reserved-items-container">
        <h2 data-translate="true">Резервирани ястия</h2>

        <!-- Search Form for Reserved Items -->
        <form method="GET">
            <input type="text" id="search-user" name="search_user" value="<?= isset($_GET['search_user']) ? htmlspecialchars($_GET['search_user']) : '' ?>" placeholder="Въведете потребителско име" data-translate="true">
            <input type="submit" value="Търси" data-translate="true">
        </form>

        <br><br>

        <ul>
        <?php
        if (isset($pdo)) {
            try {
                $searchUser = isset($_GET['search_user']) ? trim($_GET['search_user']) : '';

                $nameColumns = ['nameM', 'nameTu', 'nameWe', 'nameTh', 'nameFr', 'nameSa', 'nameSu'];
                $priceColumns = ['priceM', 'priceTu', 'priceWe', 'priceTh', 'priceFr', 'priceSa', 'priceSu'];

                $sql = "SELECT reserved_items.id, reserved_items.user_name, " . 
                    implode(', ', array_map(fn($col) => "menu_items.$col", $nameColumns)) . ", " . 
                    implode(', ', array_map(fn($col) => "menu_items.$col", $priceColumns)) . "
                    FROM reserved_items
                    JOIN menu_items ON reserved_items.item_id = menu_items.Id";

                if ($searchUser !== '') {
                    $sql .= " WHERE reserved_items.user_name LIKE :search_user";
                }

                // Exclude items with a price of 0
                $priceConditions = array_map(fn($col) => "$col > 0", $priceColumns);
                if ($searchUser !== '') {
                    $sql .= " AND (" . implode(' OR ', $priceConditions) . ")";
                } else {
                    $sql .= " WHERE (" . implode(' OR ', $priceConditions) . ")";
                }

                $stmt = $pdo->prepare($sql);

                if ($searchUser !== '') {
                    $stmt->bindValue(':search_user', "%$searchUser%", PDO::PARAM_STR);
                }

                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $users = [];

                    // Fetch data and group items by username
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $reservedUserName = htmlspecialchars($row['user_name']);

                        // Iterate through name and price columns
                        foreach ($nameColumns as $i => $nameCol) {
                            $reservedItemName = htmlspecialchars($row[$nameCol]);
                            $reservedItemPrice = htmlspecialchars($row[$priceColumns[$i]]);

                            // Only add items with price > 0
                            if ($reservedItemPrice > 0) {
                                $users[$reservedUserName][] = [
                                    'name' => $reservedItemName,
                                    'price' => $reservedItemPrice
                                ];
                            }
                        }
                    }

                    // Display items grouped by user
                    foreach ($users as $userName => $items) {
                        $totalPrice = 0; // Initialize total price for the user
                        echo "<div class='user-items-container'>";
                        echo "<h3 data-translate='true'>" . htmlspecialchars($userName) . "</h3>";
                        echo "<ul>";

                        foreach ($items as $item) {
                            echo "<li>";
                            echo "<span class='reserved-item-name' data-translate='true'>" . $item['name'] . "</span>";
                            echo "<span class='reserved-item-price' data-translate='true'>" . number_format($item['price'], 2) . "лв."."</span>";
                            echo "</li>";
                            $totalPrice += $item['price']; // Accumulate the total price
                        }

                        // Display total price for the user
                        echo "<li class='total-price' data-translate='true'><strong>Общо: " . number_format($totalPrice, 2) . "лв. </strong></li>";

                        // Add Finish button with a form to delete all items for this user
                        echo "<form method='POST' style='display:inline; margin-top: 10px;'>";
                        echo "<input type='hidden' name='user_name' value='" . htmlspecialchars($userName) . "'>";
                        echo "<input type='submit' name='finish' value='Завърши' class='finish-button' onclick=\"return confirm('Сигурен ли си, че искаш да приключиш транзакцията?');\" data-translate='true'>";
                        echo "</form>";

                        echo "</div>";
                    }
                } else {
                    echo "<li data-translate='true'>Не са намерени резервирани ястия.</li>";
                }
            } catch (PDOException $e) {
                echo "Error: " . htmlspecialchars($e->getMessage());
            }
        } else {
            echo "<li data-translate='true'>Не е установена връзка с базата данни.</li>";
        }
        ?>
        </ul>
    </div>
<?php endif; ?>

<?php
// Handle Finish button
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finish'])) {
    $userName = $_POST['user_name'] ?? '';

    $userName = htmlspecialchars($userName);
    

    $sql = "DELETE FROM reserved_items WHERE user_name = :user_name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_name' => $userName]);
    


    // Refresh the page
    header("Location: " . $_SERVER['PHP_SELF']);
}
?>


<!-- Form for adding menu items -->
<?php if (isset($_SESSION['account']) && $_SESSION['account'] == 'C'): ?>
<div class="form-container">
    <h2 data-translate="true">Добави ястие в менюто</h2>
    <form method="POST">
        <label for="day" data-translate="true">Ден от седмицата:</label>
        <select id="day" name="day" required>
            <option value="mon" data-translate="true">Понеделник</option>
            <option value="tue" data-translate="true">Вторник</option>
            <option value="wed" data-translate="true">Сряда</option>
            <option value="thu" data-translate="true">Четвъртък</option>
            <option value="fri" data-translate="true">Петък</option>
            <option value="sat" data-translate="true">Събота</option>
            <option value="sun" data-translate="true">Неделя</option>
        </select>
        <label for="name" data-translate="true">Име на продукта:</label>
        <input type="text" id="name" name="name" required>
        <label for="price" data-translate="true">Цена:</label>
        <input type="number" id="price" name="price" step="0.01" required>
        <input type="submit" value="Добави ястие" data-translate="true">
    </form>
</div>
<?php endif; ?>
</div>

</body>
</html>
