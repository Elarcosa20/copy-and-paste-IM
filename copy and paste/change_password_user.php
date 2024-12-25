<?php
session_start();
if ($_SESSION['role'] != 'user') { 
    header('Location: ../login.php'); 
    exit; 
}

include 'db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif ($new_password != $confirm_password) {
        $error_message = "New password and confirmation do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if ($user && password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->execute([$hashed_password, $_SESSION['user_id']]);
            $success_message = "Password updated successfully.";
        } else {
            $error_message = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Change Password</title>

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
        .change-password-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 90%;
            max-width: 500px;
        }
        h1 {
            margin-bottom: 20px;
        }
        input {
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
        }
        button:hover {
            background-color: #218838;
        }
        .error {
            color: #dc3545;
        }
        .success {
            color: #28a745;
        }
        .back-btn {
            position: absolute;
            top: 10px;
            left: 10px;
            background: transparent;
            border: none;
            font-size: 24px;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
<button class="back-btn" onclick="window.location.href='dashboard_user.php'">
        <i class="fas fa-arrow-left"></i>
    </button>
    <div class="change-password-container">
        <h1>Change Password</h1>

        <?php if (isset($error_message)): ?>
            <p class="error"><?= $error_message ?></p>
        <?php elseif (isset($success_message)): ?>
            <p class="success"><?= $success_message ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="password" name="current_password" placeholder="Current Password" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit" name="change_password">Change Password</button>
        </form>
    </div>
</body>
</html>

