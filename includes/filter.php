<div id="filterForm" class="dropdown-content filter-form-container" data-translate="true">
    <form method="GET">
        <input type="hidden" name="building" id="building" value="<?php echo isset($_GET['building']) ? $_GET['building'] : '1'; ?>">
        <div>
            <label for="room_number" data-translate="true">Номер на стая</label>
            <input type="text" name="room_number" id="room_number" class="filter-field" value="<?php echo isset($_GET['room_number']) ? $_GET['room_number'] : ''; ?>">
        </div>
        <div>
            <label for="min_capacity" data-translate="true">< Живущи</label>
            <input type="text" name="min_capacity" id="min_capacity" class="filter-field" value="<?php echo isset($_GET['min_capacity']) ? $_GET['min_capacity'] : ''; ?>">
        </div>
        <div>
            <label for="max_capacity" data-translate="true">> Живущи</label>
            <input type="text" name="max_capacity" id="max_capacity" class="filter-field" value="<?php echo isset($_GET['max_capacity']) ? $_GET['max_capacity'] : ''; ?>">
        </div>
        <div>
            <label for="min_rating" data-translate="true">< Рейтинг</label>
            <input type="text" name="min_rating" id="min_rating" class="filter-field" value="<?php echo isset($_GET['min_rating']) ? $_GET['min_rating'] : ''; ?>">
        </div>
        <div>
            <label for="max_rating" data-translate="true">> Рейтинг</label>
            <input type="text" name="max_rating" id="max_rating" class="filter-field" value="<?php echo isset($_GET['max_rating']) ? $_GET['max_rating'] : ''; ?>">
        </div>
        <div>
            <label for="min_price" data-translate="true">< Цена</label>
            <input type="text" name="min_price" id="min_price" class="filter-field" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>">
        </div>
        <div>
            <label for="max_price" data-translate="true">> Цена</label>
            <input type="text" name="max_price" id="max_price" class="filter-field" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>">
        </div>
        <div>
            <label for="sort_order" data-translate="true">Сортиране по цена</label>
            <select name="sort_order" id="sort_order" class="filter-field">
                <option value="asc" <?php echo (isset($_GET['sort_order']) && $_GET['sort_order'] == 'asc') ? 'selected' : ''; ?> >Ascending</option>
                <option value="desc" <?php echo (isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc') ? 'selected' : ''; ?> >Descending</option>
            </select>
        </div>
        <button type="submit" id="applyFilterButton" data-translate="true">Filter</button>
        <button type="button" id="clearFilter" data-translate="true">Clear</button>
    </form>
</div>
