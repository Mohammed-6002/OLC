<?php
session_start();
require 'db.php';

// Check if user is logged in, else redirect to login
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header('Location: login.php');
    exit;
}

$errors = [];
$inputs = [];

// Get user ID from query parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: users.php');
    exit;
}

$id = (int)$_GET['id'];

// Fetch user data
$stm = $db->prepare('SELECT * FROM user WHERE id = :id');
$stm->bindParam(':id', $id, PDO::PARAM_INT);
$stm->execute();
$user = $stm->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: users.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $inputs['email'] = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$inputs['email']) {
        $errors['email'] = 'Invalid email format.';
    }

    $inputs['firstname'] = trim($_POST['firstname']);
    if (empty($inputs['firstname'])) {
        $errors['firstname'] = 'First name is required.';
    }

    $inputs['lastname'] = trim($_POST['lastname']);
    if (empty($inputs['lastname'])) {
        $errors['lastname'] = 'Last name is required.';
    }

    // Password is optional; if provided, hash it
    $password = $_POST['password'] ?? '';

    if (count($errors) === 0) {
        // Check if email is used by another user
        $stm = $db->prepare('SELECT id FROM user WHERE email = :email AND id != :id');
        $stm->bindParam(':email', $inputs['email']);
        $stm->bindParam(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        if ($stm->fetch()) {
            $errors['email'] = 'Email is already in use by another user.';
        } else {
            // Update user
            if (!empty($password)) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $updateSql = 'UPDATE user SET email = :email, first_name = :first_name, last_name = :last_name, password = :password WHERE id = :id';
            } else {
                $updateSql = 'UPDATE user SET email = :email, first_name = :first_name, last_name = :last_name WHERE id = :id';
            }
            $stm = $db->prepare($updateSql);
            $stm->bindParam(':email', $inputs['email']);
            $stm->bindParam(':first_name', $inputs['firstname']);
            $stm->bindParam(':last_name', $inputs['lastname']);
            $stm->bindParam(':id', $id, PDO::PARAM_INT);
            if (!empty($password)) {
                $stm->bindParam(':password', $passwordHash);
            }
            $stm->execute();

            header('Location: users.php');
            exit;
        }
    }
} else {
    // Pre-fill form with existing data
    $inputs['email'] = $user['email'];
    $inputs['firstname'] = $user['first_name'];
    $inputs['lastname'] = $user['last_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f8;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            padding: 25px 40px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #555;
        }
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="text"]:focus {
            border-color: #007BFF;
            outline: none;
        }
        .error {
            color: #d9534f;
            font-size: 13px;
            margin-top: -12px;
            margin-bottom: 12px;
            min-height: 18px;
        }
        button {
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
        button:hover {
            background-color: #0056b3;
        }
        a.back-link {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #007BFF;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        <form action="edit_user.php?id=<?php echo $id; ?>" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($inputs['email'] ?? ''); ?>" required>
            <div class="error"><?php echo $errors['email'] ?? ''; ?></div>

            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($inputs['firstname'] ?? ''); ?>" required>
            <div class="error"><?php echo $errors['firstname'] ?? ''; ?></div>

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($inputs['lastname'] ?? ''); ?>" required>
            <div class="error"><?php echo $errors['lastname'] ?? ''; ?></div>

            <label for="password">Password (leave blank to keep current):</label>
            <input type="password" id="password" name="password">
            <div class="error"><?php echo $errors['password'] ?? ''; ?></div>

            <button type="submit">Save Changes</button>
        </form>
        <a href="users.php" class="back-link">Back to User List</a>
    </div>
</body>
</html>
