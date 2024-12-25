<?php
session_start();
if ($_SESSION['role'] != 'user') { 
    header('Location: ../login.php'); 
    exit; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #0072ff, #00c6ff);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
            position: relative;
        }
        .dashboard-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 80%;
            max-width: 500px;
        }
        h1 {
            margin-bottom: 20px;
        }
        .dashboard-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .dashboard-buttons a {
            text-decoration: none;
            background: #007bff;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
            display: block;
            text-align: center;
        }
        .dashboard-buttons a:hover {
            background-color: #0056b3;
        }
        .logout-btn {
            background: transparent;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .logout-btn:hover {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>User Dashboard</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></p>

        <div class="dashboard-buttons">
            <a href="view_students.php">View Students</a>
            <a href="view_schedules.php">View Schedules</a>
            <a href="view_subjects.php">View Subjects</a>
            <a href="change_password_user.php">Change Password</a>
        </div>
    </div>

    <form method="POST">
        <button type="submit" name="logout" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </form>

    <?php
    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: login.php');
        exit;
    }
    ?>
</body>
</html>
