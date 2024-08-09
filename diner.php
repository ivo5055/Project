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
session_start();
include 'elements/header.php'; 
?>

<div class="diner-container">
    <img src="img/menuBG.jpg" alt="Dorm">

    <!-- Dropdown to select the day of the week -->
    <?php if (isset($_SESSION['account']) && $_SESSION['account'] == 'C'): ?>
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
                                echo "<span class='item-name'>" . htmlspecialchars($itemName) . "</span>";
                                echo "<span class='item-price'>$" . number_format($itemPrice, 2) . "</span>";

                                // Reservation form for users with account type 'A' or 'U'
                                if (isset($_SESSION['account']) && ($_SESSION['account'] == 'A' || $_SESSION['account'] == 'U')) {
                                    echo "<form method='POST' style='display:inline;'>";
                                    echo "<input type='hidden' name='item_id' value='$itemId'>";
                                    echo "<input type='hidden' name='user_name' value='" . htmlspecialchars($_SESSION["username"]) . "'>";
                                    echo "<input type='submit' name='reserve' value='Reserve' class='reserve-button'>";
                                    echo "</form>";
                                }

                                // Delete button only available for account type 'C'
                                if (isset($_SESSION['account']) && $_SESSION['account'] == 'C') {
                                    echo "<a href='includes/delete_item.php?Id=$itemId' class='delete-button' onclick=\"return confirm('Are you sure you want to delete this item?');\">Delete</a>";
                                }

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

    <!-- Reserved Items Container ChefA -->
    <?php if (isset($_SESSION['account']) && $_SESSION['account'] == 'C'): ?>
    <div class="reserved-items-container">
        <h2>Reserved Items</h2>

        <!-- Search Form for Reserved Items -->
        <form method="GET">
            <input type="text" id="search-user" name="search_user" value="<?= isset($_GET['search_user']) ? htmlspecialchars($_GET['search_user']) : '' ?>" placeholder="Enter username">
            <input type="submit" value="Search">
        </form>
        <br></br>
        <ul>
        <?php
        if (isset($pdo)) {
    try {
        $searchUser = isset($_GET['search_user']) ? trim($_GET['search_user']) : '';

        // Define column names for names and prices
        $nameColumns = ['nameM', 'nameTu', 'nameWe', 'nameTh', 'nameFr', 'nameSa', 'nameSu'];
        $priceColumns = ['priceM', 'priceTu', 'priceWe', 'priceTh', 'priceFr', 'priceSa', 'priceSu'];

        // Start building the SQL query
        $sql = "SELECT reserved_items.id, " . implode(', ', array_map(fn($col) => "menu_items.$col", $nameColumns)) . ", " .
                implode(', ', array_map(fn($col) => "menu_items.$col", $priceColumns)) . ", reserved_items.user_name
                FROM reserved_items
                JOIN menu_items ON reserved_items.item_id = menu_items.Id";

        // Add WHERE clause for the search term if provided
        if ($searchUser !== '') {
            $sql .= " WHERE reserved_items.user_name LIKE :search_user";
        }

        // Add condition to exclude items with a price of 0
        $priceConditions = array_map(fn($col) => "$col > 0", $priceColumns);
        if ($searchUser !== '') {
            $sql .= " AND " . implode(' OR ', $priceConditions);
        } else {
            $sql .= " WHERE " . implode(' OR ', $priceConditions);
        }

        // Prepare and execute the statement
        $stmt = $pdo->prepare($sql);

        // Bind search term if provided
        if ($searchUser !== '') {
            $stmt->bindValue(':search_user', "%$searchUser%", PDO::PARAM_STR);
        }

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reservedUserName = htmlspecialchars($row['user_name']);

                // Iterate through name and price columns for output
                foreach ($nameColumns as $i => $nameCol) {
                    $reservedItemName = htmlspecialchars($row[$nameCol]);
                    $reservedItemPrice = htmlspecialchars($row[$priceColumns[$i]]);

                    // Display item information if the price is greater than 0
                    if ($reservedItemPrice > 0) {
                        echo "<li>";
                        echo "<span class='reserved-user-name'>" . $reservedUserName . "</span>";
                        echo "<span class='reserved-item-name'>" . $reservedItemName . "</span>";
                        echo "<span class='reserved-item-price'>$" . number_format($reservedItemPrice, 2) . "</span>";
                        echo "</li>";
                    }
                }
            }
        } else {
            echo "<li>No reserved items found for the given username.</li>";
        }
    } catch (PDOException $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
}

         else {
            echo "<li>Database connection not established.</li>";
        }
        ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Form for adding menu items -->
    <?php if (isset($_SESSION['account']) && $_SESSION['account'] == 'C'): ?>
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
    <?php endif; ?>
</div>

<?php
// Handle reservation form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reserve'])) {
    $itemId = $_POST['item_id'] ?? '';
    $userName = $_POST['user_name'] ?? '';

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

<?php if (isset($_SESSION['account']) && $_SESSION['account'] != 'C'): ?>
<div class="reserved-items-container">
    <h2>Basket</h2>
    
    <!-- Container to hold selected items -->
    <ul id="selected-items">
        <!-- Items will be appended here dynamically -->
    </ul>

    <!-- Confirm and Clear buttons -->
    <form method="POST" id="confirm-form" style="display: none;">
        <input type="hidden" id="selected-items-data" name="selected_items">
        <input type="submit" name="confirm_reserve" value="Confirm" class="reserve-button">
        <button type="button" id="clear-selection" class="delete-button">Clear</button>
    </form>
</div>
<?php endif; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_reserve'])) {
    $selectedItems = isset($_POST['selected_items']) ? json_decode($_POST['selected_items'], true) : [];

    foreach ($selectedItems as $item) {
        $itemId = ''; 
        $userName = $_SESSION['username'];

        $sql = "INSERT INTO reserved_items (item_id, user_name) VALUES (:item_id, :user_name)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':item_id' => $itemId,
            ':user_name' => $userName
        ]);
    }

    echo "<p>Items reserved successfully!</p>";
}
?>


<script src="js/reserve_list.js"></script>
</body>
</html>
