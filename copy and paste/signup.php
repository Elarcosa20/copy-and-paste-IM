<?php
session_start();

include 'db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    $email = htmlspecialchars(trim($_POST['email']));
    $contact_number = htmlspecialchars(trim($_POST['contact_number']));
    $role = $_POST['role'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, contact_number, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $hashedPassword, $email, $contact_number, $role]);

            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #0072ff, #00c6ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        h1 {
            margin-bottom: 30px;
            font-size: 30px;
            color: #333;
            font-weight: 600;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        input, select {
            padding: 12px 15px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input:focus, select:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 8px rgba(106, 17, 203, 0.3);
            outline: none;
        }

        .submit-btn {
            padding: 12px 15px;
            background: linear-gradient(to right, #0072ff, #00c6ff);
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .submit-btn:hover {
            background-color: #2575fc;
            transform: translateY(-2px);
        }

        .submit-btn:active {
            background-color: #1a4ed5;
        }

        p {
            margin-top: 20px;
            font-size: 14px;
            color: #333;
        }

        a {
            color: #6a11cb;
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        @media screen and (max-width: 500px) {
            .container {
                padding: 30px;
            }

            h1 {
                font-size: 24px;
            }

            input, select, .submit-btn {
                padding: 10px;
            }
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Create an Account</h1>
        <form method="POST" class="form-container">
            <input type="text" name="username" placeholder="Enter your username" required><br>
            <input type="password" name="password" placeholder="Enter your password" required><br>
            <input type="email" name="email" placeholder="Enter your email" required><br>
            <input type="text" name="contact_number" placeholder="Enter your contact number" required><br>

            <select name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select><br>

            <button type="submit" class="submit-btn">Sign Up</button>
        </form>

        <?php if (isset($error)) : ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
