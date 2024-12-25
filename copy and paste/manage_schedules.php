<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

include 'db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_schedule'])) {
    $room_id = $_POST['room_id'];
    $date = $_POST['date'];
    $time_slot = $_POST['time_slot'];
    $student_id = $_POST['student_id'];

    if ($room_id && $date && $time_slot && $student_id) {
        $stmt = $pdo->prepare("INSERT INTO schedules (room_id, date, time_slot) VALUES (?, ?, ?)");
        $stmt->execute([$room_id, $date, $time_slot]);

        $schedule_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO schedules_students (schedule_id, student_id) VALUES (?, ?)");
        $stmt->execute([$schedule_id, $student_id]);
    }

    header('Location: manage_schedules.php');
    exit;
}

if (isset($_GET['delete_id'])) {
    $schedule_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM schedules WHERE schedule_id = ?");
    $stmt->execute([$schedule_id]);

    header('Location: manage_schedules.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT schedules.*, rooms.room_name, students.first_name, students.last_name 
    FROM schedules 
    JOIN rooms ON schedules.room_id = rooms.room_id
    LEFT JOIN schedules_students ON schedules.schedule_id = schedules_students.schedule_id
    LEFT JOIN students ON schedules_students.student_id = students.student_id
");
$stmt->execute();
$schedules = $stmt->fetchAll();

$rooms_stmt = $pdo->prepare("SELECT * FROM rooms");
$rooms_stmt->execute();
$rooms = $rooms_stmt->fetchAll();

$students_stmt = $pdo->prepare("SELECT * FROM students");
$students_stmt->execute();
$students = $students_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule</title>
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
        .schedule-list {
            list-style-type: none;
            padding: 0;
        }
        .schedule-item {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }
        .actions i {
            cursor: pointer;
            margin: 0 5px;
        }
        a { color: white; text-decoration: none; }
        a:hover { color: #00c6ff; }
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
    </style>
</head>
<body>

<form method="GET" action="dashboard_admin.php">
    <button type="submit" class="back-btn">
        <i class="fas fa-arrow-left"></i>
    </button>
</form>

<div class="container">
    <h1>Manage Schedules</h1>
    <form method="POST" action="">
        <select name="room_id" required>
            <option value="" disabled selected>Select Room</option>
            <?php foreach ($rooms as $room): ?>
                <option value="<?= $room['room_id'] ?>"><?= htmlspecialchars($room['room_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="date" name="date" required>
        <input type="text" name="time_slot" placeholder="Enter Time Slot (e.g., 10:00 AM - 12:00 PM)" required>

        <select name="student_id" required>
            <option value="" disabled selected>Select Student</option>
            <?php foreach ($students as $student): ?>
                <option value="<?= $student['student_id'] ?>"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="add_schedule">Add Schedule</button>
    </form>

    <ul class="schedule-list">
        <?php foreach ($schedules as $schedule): ?>
            <li class="schedule-item">
                <span><?= htmlspecialchars($schedule['room_name']) ?> - <?= htmlspecialchars($schedule['date']) ?> - <?= htmlspecialchars($schedule['time_slot']) ?></span>
                <?php if ($schedule['first_name'] && $schedule['last_name']): ?>
                    <span> <?= htmlspecialchars($schedule['first_name'] . ' ' . $schedule['last_name']) ?></span>
                <?php else: ?>
                <?php endif; ?>
                <div class="actions">
                    <a href="edit_schedule.php?id=<?= $schedule['schedule_id'] ?>"><i class="fas fa-edit"></i></a>
                    <a href="?delete_id=<?= $schedule['schedule_id'] ?>" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>
