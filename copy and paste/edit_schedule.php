<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

include 'db/db.php';

if (!isset($_GET['id'])) {
    header('Location: manage_schedules.php');
    exit;
}

$schedule_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM schedules WHERE schedule_id = ?");
$stmt->execute([$schedule_id]);
$schedule = $stmt->fetch();

if (!$schedule) {
    header('Location: manage_schedules.php');
    exit;
}

$stmt = $pdo->prepare("SELECT student_id FROM schedules_students WHERE schedule_id = ?");
$stmt->execute([$schedule_id]);
$student_association = $stmt->fetch();

$rooms_stmt = $pdo->prepare("SELECT * FROM rooms");
$rooms_stmt->execute();
$rooms = $rooms_stmt->fetchAll();

$students_stmt = $pdo->prepare("SELECT * FROM students");
$students_stmt->execute();
$students = $students_stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_schedule'])) {
    $room_id = $_POST['room_id'];
    $date = $_POST['date'];
    $time_slot = $_POST['time_slot'];
    $student_id = $_POST['student_id'];

    if ($room_id && $date && $time_slot && $student_id) {
        // Update schedule
        $stmt = $pdo->prepare("UPDATE schedules SET room_id = ?, date = ?, time_slot = ? WHERE schedule_id = ?");
        $stmt->execute([$room_id, $date, $time_slot, $schedule_id]);

        $stmt = $pdo->prepare("UPDATE schedules_students SET student_id = ? WHERE schedule_id = ?");
        $stmt->execute([$student_id, $schedule_id]);

        header('Location: manage_schedules.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Schedule</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #0072ff, #00c6ff);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 700px;
            text-align: center;
        }
        input, select {
            padding: 10px;
            margin: 10px;
            border-radius: 5px;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            width: calc(100% - 22px);
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #218838;
        }
        .back-link {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 20px;
        }
        .back-link a {
            color: white;
            text-decoration: none;
            font-size: 18px;
        }
        .back-link i {
            margin-right: 8px;
        }
        .back-btn {
            background: transparent;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .back-btn {
            left: 20px;
        }
        </style>
</head>
<body>

<form method="GET" action="manage_schedules.php">
    <button type="submit" class="back-btn">
        <i class="fas fa-arrow-left"></i>
    </button>
</form>

<div class="container">
    <h1>Edit Schedule</h1>
    <form method="POST" action="">
        <select name="room_id" required>
            <option value="" disabled>Select Room</option>
            <?php foreach ($rooms as $room): ?>
                <option value="<?= $room['room_id'] ?>" <?= ($room['room_id'] == $schedule['room_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($room['room_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="date" name="date" value="<?= htmlspecialchars($schedule['date']) ?>" required>
        <input type="text" name="time_slot" placeholder="Enter Time Slot (e.g., 10:00 AM - 12:00 PM)" value="<?= htmlspecialchars($schedule['time_slot']) ?>" required>

        <select name="student_id" required>
            <option value="" disabled>Select Student</option>
            <?php foreach ($students as $student): ?>
                <option value="<?= $student['student_id'] ?>" <?= ($student['student_id'] == $student_association['student_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="update_schedule">Update Schedule</button>
    </form>
</div>

</body>
</html>