<?php 
session_start();
require 'db.php';

$user = null;
if (isset($_SESSION['login']) && isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stm = $db->prepare('SELECT * FROM user WHERE email = :email');
    $stm->bindParam(':email', $email);
    $stm->execute();
    $user = $stm->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f8;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        .container {
            background: #fff;
            padding: 25px 40px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 320px;
            text-align: center;
        }
        .btn-logout {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-logout:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($user): ?>
            <p>Welkom <?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></p>
            <a href="users.php" class="btn-logout" style="margin-top: 20px; display: block;">User Management</a>
            <form action="logout.php" method="POST" style="margin-top: 20px;">
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        <?php else: ?>
            <p>U bent niet ingelogd.</p>
            <div style="margin-top: 20px;">
                <a href="login.php" class="btn-logout" style="margin-bottom: 10px; display: block;">Login</a>
                <a href="register.php" class="btn-logout" style="display: block;">Register</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
