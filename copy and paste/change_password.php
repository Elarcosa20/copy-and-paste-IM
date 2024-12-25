<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}
include 'db/db.php';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user || !isset($user['username'])) {
        header('Location: dashboard_admin.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_password = htmlspecialchars(trim($_POST['old_password']));
    $new_password = htmlspecialchars(trim($_POST['new_password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));

    if (!password_verify($old_password, $user['password'])) {
        $error = "The old password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $error = "The new passwords do not match.";
    } elseif ($old_password === $new_password) {
        $error = "The new password cannot be the same as the old one.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE password = ?");
        $stmt->execute([password_hash($new_password, PASSWORD_BCRYPT)]);
        $existing_password = $stmt->fetch();

        if ($existing_password) {
            $error = "This new password has already been used.";
        } else {
            $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);

            try {
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $stmt->execute([$hashedPassword, $user_id]);

                header('Location: change_password.php');
                exit;
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #0072ff, #00c6ff);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            position: relative;
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

        .back-btn:hover {
            text-decoration: underline;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input:focus {
            border-color: #007bff;
        }

        button {
            padding: 12px;
            background: #007bff;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
        }

        .modal button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <button class="back-btn" onclick="window.location.href='dashboard_admin.php'">
        <i class="fas fa-arrow-left"></i>
    </button>
    
    <div class="container">
        <h1>Change Password</h1>

        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="form-container">
            <input type="password" name="old_password" placeholder="Old Password" required><br>
            
            <input type="password" name="new_password" placeholder="New Password" required><br>

            <input type="password" name="confirm_password" placeholder="Confirm New Password" required><br>

            <button type="submit">Update Password</button>
        </form>
    </div>
    
    <div class="modal" id="errorModal">
        <div class="modal-content">
            <h3>Error</h3>
            <p id="modalMessage"></p>
            <button onclick="closeModal()">Close</button>
        </div>
    </div>
</body>
</html>
