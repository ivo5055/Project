<?php
session_start();
include 'includes/dbh.inc.php';
include 'elements/header.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$room_number = $_GET['room_number'];
$building = $_GET['building'];
$username = $_SESSION['username'];

// Retrieve user details from the database
$userId = $_SESSION['Id'];
$queryUserDetails = "SELECT full_name, fn, egn, gender FROM users WHERE Id = ?";
$stmtUserDetails = $pdo->prepare($queryUserDetails);
$stmtUserDetails->execute([$userId]);
$userDetails = $stmtUserDetails->fetch(PDO::FETCH_ASSOC);

$fn = $userDetails['fn'];
$editable = $fn == 0 ? '' : 'readonly';
$userGender = $userDetails['gender']; // Retrieve the gender of the current user

// Fetch room capacity
$queryCapacity = "SELECT room_capacity FROM room WHERE room_number = :room_number AND building = :building";
$stmtCapacity = $pdo->prepare($queryCapacity);
$stmtCapacity->execute(['room_number' => $room_number, 'building' => $building]);
$roomCapacity = $stmtCapacity->fetchColumn();

// Get the number of current bookings
$bookingQuery = "SELECT COUNT(*) FROM bookings WHERE room_number = :room_number";
$bookingStmt = $pdo->prepare($bookingQuery);
$bookingStmt->execute(['room_number' => $room_number]);
$currentBookings = $bookingStmt->fetchColumn();

// Calculate available capacity
$availableCapacity = $roomCapacity - $currentBookings;

// Minimum grade requirement for each building
$buildingGrades = [
    '1' => 2,
    '2' => 2,
    '3' => 2,
    '4' => 3.5,
    '5' => 4,
    '6' => 5
];
$minGradeRequired = $buildingGrades[$building] ?? 0;

// Initialize variables for form values and error handling
$fullnames = $_POST['fullname'] ?? [];
$fns = $_POST['fn'] ?? [];
$egns = $_POST['egn'] ?? [];
$errors = [];
$success = '';

// Initialize the number of participants
$numParticipants = isset($_POST['num_participants']) ? intval($_POST['num_participants']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $documents = $_FILES['document'];

    $uniqueCheckFn = [];
    $uniqueCheckEgn = [];
    $bookingRequests = []; // To store booking requests before inserting them into the database

    // Check if all participants have the same gender
    $participantGender = null;
    $allSameGender = true;

    // Check if all participants meet the grade requirement
    $gradeCheckPassed = true;

    for ($i = 0; $i < $numParticipants; $i++) {
        $fullname = $fullnames[$i];
        $fn = $fns[$i];
        $egn = $egns[$i];
        $documentTmp = $documents['tmp_name'][$i];
        $documentName = basename($documents['name'][$i]);

        // Check for unique FN and EGN
        if (in_array($fn, $uniqueCheckFn) || in_array($egn, $uniqueCheckEgn)) {
            $errors[] = "Данните на студент " . ($i + 1) . " вече са въведени.";
            continue;
        }
        $uniqueCheckFn[] = $fn;
        $uniqueCheckEgn[] = $egn;

        // Validate file upload
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($documents['type'][$i], $allowedTypes)) {
            $errors[] = "Невалиден формат на файл " . ($i + 1) . ". Моля, качете PDF или DOC файл.";
            continue;
        }

        // Verify the FN and EGN are registered in the students_db
        $verifyQuery = "SELECT COUNT(*) FROM students_db WHERE fn = :fn AND egn = :egn";
        $verifyStmt = $pdo->prepare($verifyQuery);
        $verifyStmt->execute(['fn' => $fn, 'egn' => $egn]);
        $isStudentValid = $verifyStmt->fetchColumn();

        if (!$isStudentValid) {
            $errors[] = "Невалидни данни за студент " . ($i + 1) . ".";
            continue;
        }

        // Retrieve grade of the participant
        $queryParticipantGrade = "SELECT grade FROM students_db WHERE fn = :fn";
        $stmtParticipantGrade = $pdo->prepare($queryParticipantGrade);
        $stmtParticipantGrade->execute(['fn' => $fn]);
        $participantGrade = $stmtParticipantGrade->fetchColumn();

        // Check if the participant meets the minimum grade requirement
        if ($participantGrade < $minGradeRequired) {
            $errors[] = "Студент " . ($i + 1) . " не отговаря на изискванията за минимална оценка от " . $minGradeRequired . ".";
            $gradeCheckPassed = false;
            continue;
        }

        // Retrieve gender of the participant
        $queryParticipantGender = "SELECT gender FROM users WHERE fn = :fn";
        $stmtParticipantGender = $pdo->prepare($queryParticipantGender);
        $stmtParticipantGender->execute(['fn' => $fn]);
        $participantGender = $stmtParticipantGender->fetchColumn();

        // Check if all participants have the same gender
        if ($participantGender !== $userGender) {
            $errors[] = "Студент " . ($i + 1) . " не съвпада с пола на текущия потребител.";
            $allSameGender = false;
            break; // Exit the loop if the genders do not match
        }

        // Save the uploaded file
        $uploadDir = 'docS/docU/';
        $uploadFilePath = $uploadDir . $documentName;
        if (!move_uploaded_file($documentTmp, $uploadFilePath)) {
            $errors[] = "Възникна грешка при качването на файла за студент " . ($i + 1) . ".";
            continue;
        }

        // Collect booking requests if there are no errors
        $bookingRequests[] = [
            'userN' => $username,
            'building' => $building,
            'room_number' => $room_number,
            'fullname' => $fullname,
            'fn' => $fn,
            'document_path' => $uploadFilePath
        ];
    }

    // Only insert into the database if there are no errors, all participants are of the same gender, and all meet the grade requirement
    if (empty($errors) && $allSameGender && $gradeCheckPassed) {
        foreach ($bookingRequests as $request) {
            $bookingRequestQuery = "INSERT INTO booking_requests (userN, building, room_number, fullname, fn, document_path, status) 
                                    VALUES (:userN, :building, :room_number, :fullname, :fn, :document_path, 'pending')";
            $bookingRequestStmt = $pdo->prepare($bookingRequestQuery);
            $bookingRequestStmt->execute($request);
        }

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
    <title data-translate="true">Резервиране на стая</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
    
    <script>
        function updateParticipants() {
            const numParticipants = parseInt(document.getElementById('num_participants').value) || 0;
            const participantsContainer = document.getElementById('participants_container');
            participantsContainer.innerHTML = '';

            for (let i = 0; i < numParticipants; i++) {
                const isFirstParticipant = i === 0;
                const fullnameValue = isFirstParticipant ? "<?php echo htmlspecialchars($userDetails['full_name']); ?>" : (document.getElementById(`fullname${i}`)?.value || '');
                const fnValue = isFirstParticipant ? "<?php echo htmlspecialchars($userDetails['fn']); ?>" : (document.getElementById(`fn${i}`)?.value || '');
                const egnValue = document.getElementById(`egn${i}`)?.value || '';

                const participantDiv = document.createElement('div');
                participantDiv.innerHTML = `
                    <h3 data-translate="true">Студент ${i + 1}</h3>
                    <label for="fullname${i}" data-translate="true">Пълно име:</label>
                    <input type="text" id="fullname${i}" name="fullname[]" value="${fullnameValue}" ${isFirstParticipant ? 'readonly' : ''} required>
                    
                    <label for="fn${i}" data-translate="true">Факултетен номер (FN):</label>
                    <input type="text" id="fn${i}" name="fn[]" value="${fnValue}" ${isFirstParticipant ? 'readonly' : ''} required>
                    
                    <label for="egn${i}" data-translate="true">ЕГН:</label>
                    <input type="text" id="egn${i}" name="egn[]" value="${egnValue}" required>

                    <label for="document${i}" data-translate="true">Качване на документ:</label>
                    <input type="file" id="document${i}" name="document[]" accept=".pdf,.doc,.docx" required>
                `;
                participantsContainer.appendChild(participantDiv);
            }
        }

        window.onload = function() {
            updateParticipants(); // Ensure the participants are correctly updated on page load
        };
    </script>
</head>
<body>

<div class="booking-form" data-translate="true">
    <h1 data-translate="true">Въведете данните за студентите</h1>
    <?php
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<p class="error" data-translate="true">' . htmlspecialchars($error) . '</p>';
        }
    }
    if ($success) {
        echo '<p class="success" data-translate="true">' . htmlspecialchars($success) . '</p>';
    }
    ?>
    <form method="post" action="" enctype="multipart/form-data">
        <label for="num_participants" data-translate="true">Брой студенти:</label>
        <select id="num_participants" name="num_participants" onchange="updateParticipants()" required>
            <option value="1" <?php echo $numParticipants == 1 ? 'selected' : ''; ?>>1</option>
            <?php for ($i = 2; $i <= $availableCapacity; $i++): ?>
                <option value="<?php echo $i; ?>" <?php echo $numParticipants == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
            <?php endfor; ?>
        </select>
        <br><br>
        <div id="participants_container">
            <?php
            // Re-populate the participants form fields if there were validation errors
            for ($i = 0; $i < count($fullnames); $i++):
                $fullnameValue = htmlspecialchars($fullnames[$i]);
                $fnValue = htmlspecialchars($fns[$i]);
                $egnValue = htmlspecialchars($egns[$i]);
            ?>
                <div>
                    <h3 data-translate="true">Студент <?php echo ($i + 1); ?></h3>
                    <label for="fullname<?php echo $i; ?>" data-translate="true">Пълно име:</label>
                    <input type="text" id="fullname<?php echo $i; ?>" name="fullname[]" value="<?php echo $fullnameValue; ?>" required>
                    
                    <label for="fn<?php echo $i; ?>" data-translate="true">Факултетен номер (FN):</label>
                    <input type="text" id="fn<?php echo $i; ?>" name="fn[]" value="<?php echo $fnValue; ?>" required>
                    
                    <label for="egn<?php echo $i; ?>" data-translate="true">ЕГН:</label>
                    <input type="text" id="egn<?php echo $i; ?>" name="egn[]" value="<?php echo $egnValue; ?>" required>

                    <label for="document<?php echo $i; ?>" data-translate="true">Качване на документ:</label>
                    <input type="file" id="document<?php echo $i; ?>" name="document[]" accept=".pdf,.doc,.docx">
                </div>
            <?php endfor; ?>
        </div>
        
        <button type="submit" data-translate="true">Изпрати заявка за резервация</button>
    </form>
</div>

</body>
</html>
