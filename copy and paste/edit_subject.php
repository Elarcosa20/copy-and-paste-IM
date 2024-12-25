<?php
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

// Include database configuration
include 'db/db.php';

// Check if subject_id is passed in the URL
if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];

    // Fetch the subject from the database
    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE subject_id = ?");
    $stmt->execute([$subject_id]);
    $subject = $stmt->fetch();

    // If subject not found, redirect to the subject list
    if (!$subject) {
        header('Location: manage_subjects.php');
        exit;
    }
} else {
    // Redirect if no subject_id is provided
    header('Location: manage_subjects.php');
    exit;
}

// Handle form submission to update subject
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_subject'])) {
    $subject_name = $_POST['subject_name'];

    // Validate the input
    if ($subject_name) {
        // Update the subject in the database
        $stmt = $pdo->prepare("UPDATE subjects SET subject_name = ? WHERE subject_id = ?");
        $stmt->execute([$subject_name, $subject_id]);

        // Redirect to the subject list page after update
        header('Location: manage_subjects.php');
        exit;
    } else {
        $error_message = "Subject name cannot be empty.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject</title>
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
    </style>
</head>
<body>

<form method="GET" action="manage_subjects.php">
    <button type="submit" class="back-btn">
        <i class="fas fa-arrow-left"></i>
    </button>
</form>

<div class="container">
    <h1>Edit Subject</h1>

    <!-- Display error message if any -->
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <!-- Edit Subject Form -->
    <form method="POST" action="">
        <input type="text" name="subject_name" value="<?= htmlspecialchars($subject['subject_name']) ?>" required>
        <button type="submit" name="edit_subject">Update Subject</button>
    </form>
</div>

</body>
</html>
