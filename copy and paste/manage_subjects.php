<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

include 'db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_subject'])) {
    $subject_name = $_POST['subject_name'];

    if ($subject_name) {
        $stmt = $pdo->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
        $stmt->execute([$subject_name]);

        header('Location: manage_subjectS.php');
        exit;
    } else {
        $error_message = "Please enter the subject name.";
    }
}

if (isset($_GET['delete_subject_id'])) {
    $subject_id = $_GET['delete_subject_id'];

    $stmt = $pdo->prepare("DELETE FROM subjects WHERE subject_id = ?");
    $stmt->execute([$subject_id]);

    header('Location: manage_subjects.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM subjects");
$stmt->execute();
$subjects = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subject</title>
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
        input, button {
            padding: 10px;
            margin: 10px;
            border-radius: 5px;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            width: 100%;
        }
        button {
            background-color: #28a745;
            cursor: pointer;
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
            display: flex;
            align-items: center;
        }
        .back-btn i {
            margin-right: 8px;
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
            display: flex;
            justify-content: space-between;
        }
        .actions a {
            color: white;
            text-decoration: none;
        }
        .actions a:hover {
            color: #00c6ff;
        }
        .add{
            background-color: #28a745;
            cursor: pointer
        }
        .add :hover{
         color: #00c6ff;
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
    <h1>Manage Subject</h1>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <form method="POST" action="manage_subject.php">
        <input type="text" name="subject_name" placeholder="Enter Subject Name" required>
        <button type="submit" name="add_subject" class="add">Add Subject</button>
    </form>

    <h2>Subject List</h2>
    <ul class="subject-list">
        <?php if (empty($subjects)): ?>
            <li>No subjects available.</li>
        <?php else: ?>
            <?php foreach ($subjects as $subject): ?>
                <li class="subject-item">
                    <span><?= htmlspecialchars($subject['subject_name']) ?></span>

                    <div class="actions">
                        <a href="edit_subject.php?subject_id=<?= $subject['subject_id'] ?>" style="margin-right: 15px;">
                            <i class="fas fa-edit" style="color: #0072ff;"></i>
                        </a>
                        <a href="?delete_subject_id=<?= $subject['subject_id'] ?>" onclick="return confirm('Are you sure you want to delete this subject?');">
                            <i class="fas fa-trash" style="color: red;"></i>
                        </a>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

</body>
</html>
