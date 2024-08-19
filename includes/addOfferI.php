<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in (assuming 'user_id' is set in session upon login)
    /*if (!isset($_SESSION["user_id"])) {
        // If user is not logged in, redirect to login page
        header("Location: ../Login.html");
        exit();
    }*/

    // Retrieve offer data from POST
    $building = $_POST["building"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $room_number = $_POST["room_number"];
    $room_capacity = $_POST["room_capacity"];
    $gender_R = $_POST["gender_R"];
    $amenities = isset($_POST['amenities']) ? implode(', ', $_POST['amenities']) : '';

    try {
        include "dbh.inc.php";

        echo "<pre>";
        print_r($_FILES['my_image']);
        echo "</pre>";

        $img_name = $_FILES['my_image']['name'];
        $img_size = $_FILES['my_image']['size'];
        $tmp_name = $_FILES['my_image']['tmp_name'];
        $error = $_FILES['my_image']['error'];

        if ($error === 0) {
            if ($img_size > 30000000) {
                $em = "Sorry, your file is too large.";
                header("Location: ../addOffer.php?error=$em");
                exit();
            } else {
                $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                $img_ex_lc = strtolower($img_ex);
                $allowed_exs = array("jpg", "jpeg", "png");

                if (in_array($img_ex_lc, $allowed_exs)) {
                    $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
                    $img_upload_path = '../img/' . $new_img_name;
                    move_uploaded_file($tmp_name, $img_upload_path);
                } else {
                    $em = "Не можете да качвате файлове от този тип";
                    header("Location: ../addOffer.php?error=$em");
                    exit();
                }
            }
        }

        // Check if room_num already exists for the building
        $queryRoom = "SELECT * FROM room WHERE room_number = ? AND building = ?";
        $stmtRoom = $pdo->prepare($queryRoom);
        $stmtRoom->execute([$room_number, $building]);
        if ($stmtRoom->rowCount() > 0) {
            $em = "Номерът на стаята е вече регистрирана за този блок.";
            header("Location: ../addOffer.php?errorN=$em");
            exit();
        }

        // Insert offer into database
        $queryInsert = "INSERT INTO room (building, room_number, description, price, image_url, room_capacity, gender_R, amenities) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $pdo->prepare($queryInsert);
        $stmtInsert->execute([$building, $room_number, $description, $price, $new_img_name, $room_capacity, $gender_R, $amenities]);

        header("Location: ../offers.php");
        exit();
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: ../MainPage.php");
    exit();
}
?>
