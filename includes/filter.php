<!-- Filter Form -->

<form method="GET" action="">
        <div>
            <label for="capacity">Room Capacity:</label>
            <select name="capacity" id="capacity">
                <option value="">Any</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        </div>
        <div>
            <label for="rating">Minimum Rating:</label>
            <select name="rating" id="rating">
                <option value="">Any</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <div>
            <label for="price">Maximum Price:</label>
            <input type="text" name="price" id="price">
        </div>
        <button type="submit">Filter</button>
</form>

<?php
// Prepare the base query
            $query = "SELECT * FROM room WHERE 1=1";

            // Filter prototype
            $params = [];
            if (!empty($_GET['capacity'])) {
                $query .= " AND room_capacity = :capacity";
                $params['capacity'] = $_GET['capacity'];
            }
            if (!empty($_GET['rating'])) {
                $query .= " AND (total_rating / number_of_reviews) >= :rating";
                $params['rating'] = $_GET['rating'];
            }
            if (!empty($_GET['price'])) {
                $query .= " AND price <= :price";
                $params['price'] = $_GET['price'];
            }


            // Prepare and execute the query
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            ?>