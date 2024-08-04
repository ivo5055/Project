
    <div id="filterForm" class="dropdown-content filter-form-container">
        <form method="GET">
            <input type="hidden" name="building" id="building" value="<?php echo isset($_GET['building']) ? $_GET['building'] : '1'; ?>">
            <div>
                <label for="room_number">Room Number:</label>
                <input type="text" name="room_number" id="room_number" class="filter-field" value="<?php echo isset($_GET['room_number']) ? $_GET['room_number'] : ''; ?>">
            </div>
            <div>
                <label for="min_capacity">Min Room Capacity:</label>
                <input type="text" name="min_capacity" id="min_capacity" class="filter-field" value="<?php echo isset($_GET['min_capacity']) ? $_GET['min_capacity'] : ''; ?>">
            </div>
            <div>
                <label for="max_capacity">Max Room Capacity:</label>
                <input type="text" name="max_capacity" id="max_capacity" class="filter-field" value="<?php echo isset($_GET['max_capacity']) ? $_GET['max_capacity'] : ''; ?>">
            </div>
            <div>
                <label for="min_rating">Min Rating:</label>
                <input type="text" name="min_rating" id="min_rating" class="filter-field" value="<?php echo isset($_GET['min_rating']) ? $_GET['min_rating'] : ''; ?>">
            </div>
            <div>
                <label for="max_rating">Max Rating:</label>
                <input type="text" name="max_rating" id="max_rating" class="filter-field" value="<?php echo isset($_GET['max_rating']) ? $_GET['max_rating'] : ''; ?>">
            </div>
            <div>
                <label for="min_price">Min Price:</label>
                <input type="text" name="min_price" id="min_price" class="filter-field" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>">
            </div>
            <div>
                <label for="max_price">Max Price:</label>
                <input type="text" name="max_price" id="max_price" class="filter-field" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>">
            </div>
            <div>
                <label for="sort_order">Sort by Price:</label>
                <select name="sort_order" id="sort_order" class="filter-field">
                    <option value="asc" <?php echo (isset($_GET['sort_order']) && $_GET['sort_order'] == 'asc') ? 'selected' : ''; ?>>Ascending</option>
                    <option value="desc" <?php echo (isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc') ? 'selected' : ''; ?>>Descending</option>
                </select>
            </div>
            <button type="submit" id="applyFilterButton">Filter</button>
            <button type="button" id="clearFilter">Clear Filter</button>
        </form>
    </div>
</div>


<?php
// PHP code to process the form submission
// Prepare the base query
$query = "SELECT * FROM room WHERE 1=1";

// Filter prototype
$params = [];
if (!empty($_GET['room_number'])) {
    $query .= " AND room_number = :room_number";
    $params['room_number'] = $_GET['room_number'];
}
if (!empty($_GET['min_capacity'])) {
    $query .= " AND room_capacity >= :min_capacity";
    $params['min_capacity'] = $_GET['min_capacity'];
}
if (!empty($_GET['max_capacity'])) {
    $query .= " AND room_capacity <= :max_capacity";
    $params['max_capacity'] = $_GET['max_capacity'];
}
if (!empty($_GET['min_rating'])) {
    $query .= " AND (total_rating / number_of_reviews) >= :min_rating";
    $params['min_rating'] = $_GET['min_rating'];
}
if (!empty($_GET['max_rating'])) {
    $query .= " AND (total_rating / number_of_reviews) <= :max_rating";
    $params['max_rating'] = $_GET['max_rating'];
}
if (!empty($_GET['min_price'])) {
    $query .= " AND price >= :min_price";
    $params['min_price'] = $_GET['min_price'];
}
if (!empty($_GET['max_price'])) {
    $query .= " AND price <= :max_price";
    $params['max_price'] = $_GET['max_price'];
}

// Handle building filter
if (!empty($_GET['building'])) {
    $query .= " AND building = :building";
    $params['building'] = $_GET['building'];
}

// Sorting
$sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'asc';
$query .= " ORDER BY price " . ($sortOrder === 'desc' ? 'DESC' : 'ASC');

// Prepare and execute the query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
?>
