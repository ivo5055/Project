<?php
if (isset($_POST['delete_room'])) {
    include 'dbh.inc.php';

    $Id = $_POST['Id']; // Get the room ID to be deleted

    // Delete room image
    $imageQuery = "SELECT image_url FROM room WHERE id = :id";
    $imageStmt = $pdo->prepare($imageQuery);
    $imageStmt->execute(['id' => $Id]);
    $image = $imageStmt->fetch(PDO::FETCH_ASSOC);

    if ($image) {
        $imagePath = 'img/' . $image['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // Delete the image file
        }
    }

    // Delete the room from the database
    $deleteQuery = "DELETE FROM room WHERE id = :id";
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->execute(['id' => $Id]);

    // Redirect to the same page after deletion
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
