<?php
session_start();

if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

include 'db/db.php';

$stmt = $pdo->prepare("
    SELECT schedules.*, rooms.room_name, students.first_name, students.last_name
    FROM schedules 
    JOIN rooms ON schedules.room_id = rooms.room_id
    LEFT JOIN schedules_students ON schedules.schedule_id = schedules_students.schedule_id
    LEFT JOIN students ON schedules_students.student_id = students.student_id
");
$stmt->execute();
$schedules = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schedules</title>
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

<form method="GET" action="dashboard_user.php">
    <button type="submit" class="back-btn">
        <i class="fas fa-arrow-left"></i>
    </button>
</form>

<div class="container">
    <h1>View Schedules</h1>

    <ul class="schedule-list">
        <?php if (empty($schedules)): ?>
            <li>No schedules available.</li>
        <?php else: ?>
            <?php foreach ($schedules as $schedule): ?>
                <li class="schedule-item">
                    <span>
                        <?= htmlspecialchars($schedule['room_name']) ?> - <?= htmlspecialchars($schedule['date']) ?> - <?= htmlspecialchars($schedule['time_slot']) ?>
                        <?php if ($schedule['first_name'] && $schedule['last_name']): ?>
                            - <strong></strong> <?= htmlspecialchars($schedule['first_name'] . ' ' . $schedule['last_name']) ?>
                        <?php else: ?>
                            
                        <?php endif; ?>
                    </span>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

</body>
</html>
