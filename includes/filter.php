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
