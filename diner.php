<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offers</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body class="hideS">
    
<?php 
session_start(); // Ensure session is started
include 'elements/header.php'; 
?>

<div class="diner-container">
    <img src="img/menuBG.jpg" alt="Dorm">

    <!-- Dropdown to select the day of the week -->
    <div class="day-selector-container">
        <form method="GET">
            <label for="day-select">Select Day:</label>
            <select id="day-select" name="day" onchange="this.form.submit()">
                <option value="sel">-------------</option>
                <option value="mon" <?= (isset($_GET['day']) && $_GET['day'] === 'mon') ? 'selected' : '' ?>>Monday</option>
                <option value="tue" <?= (isset($_GET['day']) && $_GET['day'] === 'tue') ? 'selected' : '' ?>>Tuesday</option>
                <option value="wed" <?= (isset($_GET['day']) && $_GET['day'] === 'wed') ? 'selected' : '' ?>>Wednesday</option>
                <option value="thu" <?= (isset($_GET['day']) && $_GET['day'] === 'thu') ? 'selected' : '' ?>>Thursday</option>
                <option value="fri" <?= (isset($_GET['day']) && $_GET['day'] === 'fri') ? 'selected' : '' ?>>Friday</option>
                <option value="sat" <?= (isset($_GET['day']) && $_GET['day'] === 'sat') ? 'selected' : '' ?>>Saturday</option>
                <option value="sun" <?= (isset($_GET['day']) && $_GET['day'] === 'sun') ? 'selected' : '' ?>>Sunday</option>
            </select>
        </form>
    </div>

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
                                echo "<span class='item-name'>" . htmlspecialchars($itemName) . "</span>";
                                echo "<span class='item-price'>$" . number_format($itemPrice, 2) . "</span>";

                                // Reservation form for each item
                                echo "<form method='POST' style='display:inline;'>";
                                echo "<input type='hidden' name='item_id' value='$itemId'>";
                                echo "<input type='hidden' name='user_name' value='" . htmlspecialchars($_SESSION["username"]) . "'>";
                                echo "<input type='submit' name='reserve' value='Reserve' class='reserve-button'>";
                                echo "</form>";

                                echo "<a href='includes/delete_item.php?Id=$itemId' class='delete-button' onclick=\"return confirm('Are you sure you want to delete this item?');\">Delete</a>";
                                echo "</li>";
                            }
                        }
                    } else {
                        echo "<li>No menu items available.</li>";
                    }
                }
            } catch (PDOException $e) {
                echo "Error: " . htmlspecialchars($e->getMessage());
            }
        } else {
            echo "<li>Database connection not established.</li>";
        }
        ?>
        </ul>
    </div>

    <!-- Reserved Items Container -->
    <div class="reserved-items-container">
        <h2>Reserved Items</h2>
        <ul>
        <?php
        if (isset($pdo)) {
            try {
                $sql = "SELECT reserved_items.id, menu_items.nameSu, menu_items.priceSu, reserved_items.user_name
                        FROM reserved_items
                        JOIN menu_items ON reserved_items.item_id = menu_items.Id";
                $stmt = $pdo->query($sql);

                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $reservedItemName = htmlspecialchars($row['nameSu']);
                        $reservedItemPrice = htmlspecialchars($row['priceSu']);
                        $reservedUserName = htmlspecialchars($row['user_name']);

                        echo "<li>";
                        echo "<span class='reserved-user-name'>" . $reservedUserName . "</span>";
                        echo "<span class='reserved-item-name'>" . $reservedItemName . "</span>";
                        echo "<span class='reserved-item-price'>$" . number_format($reservedItemPrice, 2) . "</span>";
                        echo "</li>";
                    }
                } else {
                    echo "<li>No reserved items.</li>";
                }
            } catch (PDOException $e) {
                echo "Error: " . htmlspecialchars($e->getMessage());
            }
        } else {
            echo "<li>Database connection not established.</li>";
        }
        ?>
        </ul>
    </div>

    <!-- Form for adding menu items -->
    <div class="form-container">
        <h2>Add Menu Item</h2>
        <form method="POST">
            <label for="day">Day of the Week:</label>
            <select id="day" name="day" required>
                <option value="mon">Monday</option>
                <option value="tue">Tuesday</option>
                <option value="wed">Wednesday</option>
                <option value="thu">Thursday</option>
                <option value="fri">Friday</option>
                <option value="sat">Saturday</option>
                <option value="sun">Sunday</option>
            </select>
            <label for="name">Item Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>
            <input type="submit" value="Add Item">
        </form>
    </div>
</div>

<?php
// Handle reservation form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reserve'])) {
    $itemId = $_POST['item_id'] ?? '';
    $userName = $_POST['user_name'] ?? '';

    // Sanitize inputs
    $itemId = htmlspecialchars($itemId);
    $userName = htmlspecialchars($userName);

    // Prepare and execute SQL query for reservation
    $sql = "INSERT INTO reserved_items (item_id, user_name) VALUES (:item_id, :user_name)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':item_id' => $itemId,
        ':user_name' => $userName
    ]);

    echo "<p>Item reserved successfully!</p>";
}

// Handle form submission for adding menu items
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['reserve'])) {
    $dayOfWeek = $_POST['day'] ?? '';
    $itemName = $_POST['name'] ?? '';
    $itemPrice = $_POST['price'] ?? '';

    // Sanitize inputs
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

        echo "<p>Menu item added successfully!</p>";
    } else {
        echo "<p>Invalid day of the week.</p>";
    }
}
?>
</body>
</html>
