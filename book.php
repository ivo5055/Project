<?php
session_start();
include 'includes/dbh.inc.php';
include 'elements/header.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$room_number = $_GET['room_number'];
$building = $_GET['building'];
$username = $_SESSION['username'];

// Retrieve user details from the database
$userId = $_SESSION['Id'];
$queryUserDetails = "SELECT full_name, fn, egn FROM users WHERE Id = ?";
$stmtUserDetails = $pdo->prepare($queryUserDetails);
$stmtUserDetails->execute([$userId]);
$userDetails = $stmtUserDetails->fetch(PDO::FETCH_ASSOC);

$fn = $userDetails['fn'];
$editable = $fn == 0 ? '' : 'readonly';

// Fetch room capacity
$queryCapacity = "SELECT room_capacity FROM room WHERE room_number = :room_number AND building = :building";
$stmtCapacity = $pdo->prepare($queryCapacity);
$stmtCapacity->execute(['room_number' => $room_number, 'building' => $building]);
$roomCapacity = $stmtCapacity->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $numParticipants = intval($_POST['num_participants']);
    $fullnames = $_POST['fullname'];
    $fns = $_POST['fn'];
    $egns = $_POST['egn'];
    $documents = $_FILES['document'];

    $errors = [];
    $uniqueCheckFn = [];
    $uniqueCheckEgn = [];

    for ($i = 0; $i < $numParticipants; $i++) {
        $fullname = $fullnames[$i];
        $fn = $fns[$i];
        $egn = $egns[$i];
        $documentTmp = $documents['tmp_name'][$i];
        $documentName = basename($documents['name'][$i]);

        // Check for unique FN and EGN
        if (in_array($fn, $uniqueCheckFn) || in_array($egn, $uniqueCheckEgn)) {
            $errors[] = "Данните на участник " . ($i + 1) . " вече са въведени.";
            continue;
        }
        $uniqueCheckFn[] = $fn;
        $uniqueCheckEgn[] = $egn;

        // Validate file upload
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($documents['type'][$i], $allowedTypes)) {
            $errors[] = "Невалиден формат на файл за участник " . ($i + 1) . ". Моля, качете PDF или DOC файл.";
            continue;
        }

        // Verify the FN and EGN are registered in the students_db
        $verifyQuery = "SELECT COUNT(*) FROM students_db WHERE fn = :fn AND egn = :egn";
        $verifyStmt = $pdo->prepare($verifyQuery);
        $verifyStmt->execute(['fn' => $fn, 'egn' => $egn]);
        $isStudentValid = $verifyStmt->fetchColumn();

        if (!$isStudentValid) {
            $errors[] = "Невалидни данни за участник " . ($i + 1) . ".";
            continue;
        }

        // Save the uploaded file
        $uploadDir = 'docS/docU/';
        $uploadFilePath = $uploadDir . $documentName;
        if (!move_uploaded_file($documentTmp, $uploadFilePath)) {
            $errors[] = "Възникна грешка при качването на файла за участник " . ($i + 1) . ".";
            continue;
        }

        // Insert booking request for the participant
        $bookingRequestQuery = "INSERT INTO booking_requests (userN, building, room_number, fullname, fn, document_path, status) 
                                VALUES (:userN, :building, :room_number, :fullname, :fn, :document_path, 'pending')";
        $bookingRequestStmt = $pdo->prepare($bookingRequestQuery);
        $bookingRequestStmt->execute([
            'userN' => $username,
            'building' => $building,
            'room_number' => $room_number,
            'fullname' => $fullname,
            'fn' => $fn,
            'document_path' => $uploadFilePath
        ]);
    }

    if (empty($errors)) {
        $success = "Заявката за резервация беше изпратена успешно!";
        header("Location: offers.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Резервиране на стая</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
    <script>
        function updateParticipants() {
            const numParticipants = parseInt(document.getElementById('num_participants').value);
            const participantsContainer = document.getElementById('participants_container');
            participantsContainer.innerHTML = '';

            for (let i = 0; i < numParticipants; i++) {
                const isFirstParticipant = i === 0;
                const fullnameValue = isFirstParticipant ? "<?php echo htmlspecialchars($userDetails['full_name']); ?>" : '';
                const fnValue = isFirstParticipant ? "<?php echo htmlspecialchars($userDetails['fn']); ?>" : '';
                const egnValue = isFirstParticipant ? '' : ''; // EGN should be provided by the user, not pre-filled.

                const participantDiv = document.createElement('div');
                participantDiv.innerHTML = `
                    <h3>Участник ${i + 1}</h3>
                    <label for="fullname${i}">Пълно име:</label>
                    <input type="text" id="fullname${i}" name="fullname[]" value="${fullnameValue}" ${isFirstParticipant ? 'readonly' : ''} required>
                    
                    <label for="fn${i}">Факултетен номер (FN):</label>
                    <input type="text" id="fn${i}" name="fn[]" value="${fnValue}" ${isFirstParticipant ? 'readonly' : ''} required>
                    
                    <label for="egn${i}">ЕГН:</label>
                    <input type="text" id="egn${i}" name="egn[]" value="${egnValue}" required>

                    <label for="document${i}">Качване на документ:</label>
                    <input type="file" id="document${i}" name="document[]" accept=".pdf,.doc,.docx" required>
                `;
                participantsContainer.appendChild(participantDiv);
            }
        }
    </script>
</head>
<body>

<div class="booking-form">
    <h1>Въведете данните за участниците</h1>
    <?php
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<p class="error">' . htmlspecialchars($error) . '</p>';
        }
    }
    if (isset($success)) {
        echo '<p class="success">' . htmlspecialchars($success) . '</p>';
    }
    ?>
    <form method="post" action="" enctype="multipart/form-data">
        <label for="num_participants">Брой участници:</label>
        <select id="num_participants" name="num_participants" onchange="updateParticipants()" required>
            <option value="0" selected>0</option> <!-- Default selection is 0 -->
            <?php for ($i = 1; $i <= $roomCapacity; $i++): ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php endfor; ?>
        </select>

        <div id="participants_container">
        </div>
        
        <button type="submit">Изпрати заявка за резервация</button>
    </form>
</div>

</body>
</html>
