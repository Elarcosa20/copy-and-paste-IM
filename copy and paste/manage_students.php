<?php
session_start();

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
if ($_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include 'db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];

    if (strlen($contact_number) != 11) {
        echo "Contact number must be exactly 11 characters.";
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, email, contact_number) VALUES (?, ?, ?, ?)");
    $stmt->execute([$first_name, $last_name, $email, $contact_number]);

    header('Location: manage_students.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_student'])) {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];

    if (strlen($contact_number) != 11) {
        echo "Contact number must be exactly 11 characters.";
        exit;
    }

    $stmt = $pdo->prepare("UPDATE students SET first_name = ?, last_name = ?, email = ?, contact_number = ? WHERE student_id = ?");
    $stmt->execute([$first_name, $last_name, $email, $contact_number, $student_id]);

    header('Location: manage_students.php');
    exit;
}

if (isset($_GET['delete_id'])) {
    $student_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->execute([$student_id]);

    header('Location: manage_students.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM students");
$stmt->execute();
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #2575fc, #6a11cb);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            width: 80%;
            max-width: 1000px;
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #fff;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
        }
        td {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .action-btn {
            color: white;
            background-color: transparent;
            border: none;
            cursor: pointer;
        }
        .edit-btn {
            color: #28a745;
        }
        .delete-btn {
            color: #dc3545;
        }
        .logout-btn {
            background: transparent;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            position: absolute;
            top: 20px;
            right: -100px;
        }
        .back-btn {
            background: transparent;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            position: absolute;
            top: 20px;
            left: -100px;
        }
        form input,
        form button {
            margin: 10px 0;
            padding: 10px;
            width: 100%;
            max-width: 300px;
            border-radius: 5px;
            border: none;
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
        <h1>Manage Students</h1>

        <form method="POST" action="manage_students.php">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="contact_number" placeholder="Contact Number" maxlength="11" required>
            <button type="submit" name="add_student">Add Student</button>
        </form>

        <table>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= $student['student_id'] ?></td>
                    <td><?= htmlspecialchars($student['first_name']) . ' ' . htmlspecialchars($student['last_name']) ?></td>
                    <td><?= htmlspecialchars($student['email']) ?></td>
                    <td><?= htmlspecialchars($student['contact_number']) ?></td>
                    <td>
                        <a href="edit_students.php?edit_id=<?= $student['student_id'] ?>" class="action-btn edit-btn">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="manage_students.php?delete_id=<?= $student['student_id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <form method="POST">
        <button type="submit" name="logout" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </form>

</body>
</html>
