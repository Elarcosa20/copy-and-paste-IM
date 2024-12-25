<?php
session_start();

if ($_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include 'db/db.php';

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            margin: 0;
            padding: 20px;
        }
        .navbar {
            background: #6a11cb;
            padding: 10px;
            color: white;
            text-align: center;
            border-radius: 5px;
            position: relative;
        }
        .logout-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            background: transparent;
            border: none;
            font-size: 24px;
            color: white;
            cursor: pointer;
        }
        .logout-btn:hover {
            color: #f1f1f1;
        }
        .dashboard-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        a {
            color: #6a11cb;
            text-decoration: none;
            margin-right: 15px;
        }
        button {
            background-color: #2575fc;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #2575fc;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .section-heading {
            margin-top: 30px;
            font-size: 1.5em;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></p>
        <button class="logout-btn" onclick="window.location.href='?logout=true'">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </div>

    <div class="dashboard-container">
        <div>
            <h3>User List</h3>
            <?php
            if ($users) {
                echo "<table>";
                echo "<thead><tr><th>Username</th><th>Role</th><th>Action</th></tr></thead>";
                echo "<tbody>";
                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                    echo "<td><a href='change_password.php?user_id=" . htmlspecialchars($user['user_id']) . "'>Change Password</a></td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<p>No users available.</p>";
            }
            ?>
        </div>

        <div class="section-heading">
            <a href="manage_room.php">Manage Rooms</a>
        </div>

        <div class="section-heading">
            <a href="manage_students.php">Manage Students</a>
        </div>

        <div class="section-heading">
            <a href="manage_schedules.php">Manage Schedules</a>
        </div>

        <div class="section-heading">
            <a href="manage_subjects.php">Manage Subjects</a>
        </div>
    </div>
</body>
</html>
