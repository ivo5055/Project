<?php
session_start();
include 'includes/dbh.inc.php';
include 'elements/header.php';
if (!isset($_SESSION['account']) || $_SESSION['account'] !== 'A') {
    header("Location: MainPage.php");
    exit();
}
// Handle booking deletion
if (isset($_POST['delete_room'])) {
    $bookingId = $_POST['Id'];
    $deleteQuery = "DELETE FROM bookings WHERE Id = :id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute(['id' => $bookingId]);

    // Redirect to the same page to avoid form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch bookings with user details, including search functionality
$searchQuery = '';
$params = [];

if (isset($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%';
    $searchQuery = "AND (u.full_name LIKE :search OR u.fn LIKE :search OR b.room_number LIKE :search OR b.userN LIKE :search)";
    $params = ['search' => $search];
}

$query = "SELECT b.*, u.full_name, u.fn, u.gender 
          FROM bookings b
          JOIN users u ON b.userN = u.user
          WHERE 1=1 $searchQuery
          ORDER BY b.building, b.room_number, b.userN";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Управление на офертите</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>

<div class="requests-container">
    <h1 class="centered-title" data-translate="true">Управление на офертите</h1>
    
    <form method="get" action="" class="search-form">
        <input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" data-translate="true" placeholder="Търсене">
        <button type="submit" data-translate="true">Търси</button>
    </form>

    <table>
        <thead>
            <tr>
                <th data-translate="true">Блок</th>
                <th data-translate="true">Номер на стаята</th>
                <th data-translate="true">Потребител</th>
                <th data-translate="true">Пълно име</th>
                <th data-translate="true">Факултетен номер</th>
                <th data-translate="true">Пол</th>
                <th data-translate="true">Действие</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['building']); ?></td>
                    <td><?php echo htmlspecialchars($booking['room_number']); ?></td>
                    <td><?php echo htmlspecialchars($booking['userN']); ?></td>
                    <td><?php echo htmlspecialchars($booking['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['fn']); ?></td>
                    <td><?php echo htmlspecialchars($booking['gender']); ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="Id" value="<?php echo $booking['Id']; ?>">
                            <button type="submit" name="delete_room" data-translate="true">Изтрий</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
