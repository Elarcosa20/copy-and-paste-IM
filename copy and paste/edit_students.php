<?php
session_start();

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

    $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, email, contact_number) VALUES (?, ?, ?, ?)");
    $stmt->execute([$first_name, $last_name, $email, $contact_number]);

    header('Location: manage_students.php');
    exit;
}

if (isset($_GET['edit_id'])) {
    $student_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_student'])) {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];

    $stmt = $pdo->prepare("UPDATE students SET first_name = ?, last_name = ?, email = ?, contact_number = ? WHERE student_id = ?");
    $stmt->execute([$first_name, $last_name, $email, $contact_number, $student_id]);

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
    <title>Edit Student</title>

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
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            width: 80%;
            max-width: 600px;
            text-align: center;
        }
        h1 {
            margin-bottom: 30px;
            font-size: 2em;
            color: #fff;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        label {
            font-weight: bold;
            display: block;
            color: #fff;
            margin-bottom: 8px;
        }
        input {
            padding: 10px;
            width: 100%;
            max-width: 400px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }
        input:focus {
            outline: none;
            border-color: #007bff;
            background-color: rgba(255, 255, 255, 0.3);
        }
        button {
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-btn, .logout-btn {
            background: transparent;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            position: absolute;
            top: 20px;
        }
        .back-btn {
            left: 20px;
        }
        .logout-btn {
            right: 20px;
        }
        .back-btn:hover, .logout-btn:hover {
            color: #dc3545;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
</head>
<body>
    <form method="GET" action="manage_students.php">
        <button type="submit" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </button>
    </form>

    <div class="container">
        <h1>Edit Student</h1>

        <?php if (isset($student)): ?>
            <form method="POST" action="manage_students.php" class="form-container">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($student['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number"  maxlength="11" required value="<?= htmlspecialchars($student['contact_number']) ?>" required>
                </div>

                <input type="hidden" name="student_id" value="<?= $student['student_id'] ?>">

                <button type="submit" name="edit_student">Update Student</button>
            </form>
        <?php else: ?>
            <p>Student not found.</p>
        <?php endif; ?>
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
