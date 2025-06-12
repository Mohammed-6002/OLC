<?php
session_start();
require 'db.php';

if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header('Location: login.php');
    exit;
}


$stm = $db->prepare('SELECT id, email, first_name, last_name FROM user');
$stm->execute();
$users = $stm->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f8;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a.button {
            padding: 6px 12px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            margin-right: 5px;
        }
        a.button.delete {
            background-color: #d9534f;
        }
        a.button:hover {
            opacity: 0.9;
        }
        .add-user {
            display: block;
            width: 150px;
            margin: 20px auto;
            text-align: center;
            padding: 10px 0;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .add-user:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h1>User Management</h1>
    <a href="register.php" class="add-user">Add New User</a>
    <table>
        <thead>
            <tr>
                <th>Email</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="button">Edit</a>
                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="button delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (count($users) === 0): ?>
            <tr>
                <td colspan="4" style="text-align:center;">No users found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
