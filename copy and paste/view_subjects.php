<?php
session_start();

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'user')) {
    header('Location: login.php');
    exit;
}

include 'db/db.php';

$stmt = $pdo->prepare("SELECT * FROM subjects");
$stmt->execute();
$subjects = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subjects</title>
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
        .subject-list {
            list-style-type: none;
            padding: 0;
            margin-top: 30px;
            color: white;
        }
        .subject-item {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
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
    <h1>Subjects</h1>

    <ul class="subject-list">
        <?php if (empty($subjects)): ?>
            <li>No subjects available.</li>
        <?php else: ?>
            <?php foreach ($subjects as $subject): ?>
                <li class="subject-item">
                    <span><?= htmlspecialchars($subject['subject_name']) ?></span>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

</body>
</html>
