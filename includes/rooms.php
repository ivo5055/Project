<?php
            // Include database connection file
            include "dbh.inc.php";

            // Fetch room offers from database
            $query = "SELECT * FROM room";
            $result = $pdo->query($query);

            // Loop through fetched room offers and generate HTML dynamically
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="room-offer">';
                echo '<a href="room_details.php">';
                echo '<img src="' . $row['image'] . '" alt="' . $row['title'] . '">';
                echo '</a>';
                echo '<div class="offer-details">';
                echo '<h2>' . $row['title'] . '</h2>';
                echo '<p>' . $row['description'] . '</p>';
                echo '<p>Rating: ' . $row['rating'] . '/5 (' . $row['reviews'] . ' reviews)</p>';
                echo '<p>Price: $' . $row['price'] . ' per night</p>';
                echo '<a href="room_details.php" class="button">Book Now</a>';
                echo '</div>';
                echo '</div>';
            }
            ?>