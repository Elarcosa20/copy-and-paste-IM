<?php
session_start();

    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: login.php');
        exit;
    }
    
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include 'db/db.php';

if (isset($_GET['delete_id'])) {
    $room_id = $_GET['delete_id'];

    $stmt = $pdo->prepare("DELETE FROM rooms WHERE room_id = ?");
    $stmt->execute([$room_id]);

    header('Location: manage_room.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_room'])) {
    $room_name = $_POST['room_name'];

    $stmt = $pdo->prepare("INSERT INTO rooms (room_name) VALUES (?)");
    $stmt->execute([$room_name]);
    
    header('Location: manage_room.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_room'])) {
    $room_id = $_POST['room_id'];
    $room_name = $_POST['room_name'];

    $stmt = $pdo->prepare("UPDATE rooms SET room_name = ? WHERE room_id = ?");
    $stmt->execute([$room_name, $room_id]);

    header('Location: manage_room.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM rooms");
$stmt->execute();
$rooms = $stmt->fetchAll();

$edit_room = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE room_id = ?");
    $stmt->execute([$edit_id]);
    $edit_room = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms</title>
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
        .room-list {
            list-style-type: none;
            padding: 0;
        }
        .room-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 10px;
            border-radius: 5px;
        }
        .room-item .name {
            color: white;
            font-size: 1.1em;
        }
        .room-item .actions {
            color: #fff;
        }
        .room-item .actions i {
            margin-left: 10px;
            cursor: pointer;
        }
        .room-item .actions i:hover {
            color: #007bff;
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
            margin-bottom: 30px;
        }
        .form-container input {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            width: 100%;
            max-width: 400px;
            border: 1px solid #ccc;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }
        .form-container button {
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-container button:hover {
            background-color: #0056b3;
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
        <h1>Manage Rooms</h1>

        <div class="form-container">
            <?php if ($edit_room): ?>
                <form method="POST" action="manage_room.php">
                    <input type="hidden" name="room_id" value="<?= htmlspecialchars($edit_room['room_id']) ?>">
                    <input type="text" name="room_name" value="<?= htmlspecialchars($edit_room['room_name']) ?>" required>
                    <button type="submit" name="update_room">Update Room</button>
                </form>
            <?php else: ?>
                <form method="POST" action="manage_room.php">
                    <input type="text" name="room_name" placeholder="Enter Room Name" required>
                    <button type="submit" name="add_room">Add Room</button>
                </form>
            <?php endif; ?>
        </div>

        <ul class="room-list">
            <?php foreach ($rooms as $room): ?>
                <li class="room-item">
                    <span class="name"><?= htmlspecialchars($room['room_name']) ?></span>
                    <div class="actions">
                        <a href="manage_room.php?edit_id=<?= $room['room_id'] ?>"><i class="fas fa-edit"></i></a>
                        <a href="manage_room.php?delete_id=<?= $room['room_id'] ?>" onclick="return confirm('Are you sure you want to delete this room?')"><i class="fas fa-trash"></i></a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <form method="POST">
        <button type="submit" name="logout" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </form>

</body>
</html>
