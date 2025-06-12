<?php
session_start();
require 'db.php';
global $db;

$inputs = [];
$errors = [];

const EMAIL_ERROR = 'Email is required.';
const PASSWORD_ERROR = 'Password is required.';
const LOGIN_ERROR = 'Je gegevens zijn niet juist';

if (empty($_POST['email'])) {
    $errors['email'] = EMAIL_ERROR;
} else {
    $inputs['email'] = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if ($inputs['email'] === false) {
        $errors['email'] = 'Invalid email format.';
    }
}

if (empty($_POST['password'])) {
    $errors['password'] = PASSWORD_ERROR;
} else {
    $inputs['password'] = $_POST['password'];
}

if (count($errors) === 0) {
    $stm = $db->prepare('SELECT * FROM user WHERE email = :email');
    $stm->bindParam(':email', $inputs['email']);
    $stm->execute();
    $user = $stm->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        if (password_verify($inputs['password'], $user['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['email'] = $user['email'];
            header('Location: index.php');
            exit;
        } else {
            $errors['login'] = LOGIN_ERROR;
        }
    } else {
        $errors['login'] = LOGIN_ERROR;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
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
        }
        h1 {
            color: #333;
            text-align: center;
        }
        form {
            background: #fff;
            padding: 25px 40px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 320px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #555;
        }
        input[type="email"],
        input[type="password"] {
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
        input[type="password"]:focus {
            border-color: #007BFF;
            outline: none;
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
        div {
            color: #d9534f;
            font-size: 13px;
            margin-top: -12px;
            margin-bottom: 12px;
            min-height: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <div><?php echo $errors['login'] ?? ''; ?></div>
        <form action="login.php" method="POST">
            <label for="email">Email:</label><br />
            <input type="email" id="email" name="email" value="<?php echo $inputs['email'] ?? ''; ?>">
            <div><?php echo $errors['email'] ?? ''; ?></div>

            <label for="password">Password:</label><br />
            <input type="password" id="password" name="password">
            <div><?php echo $errors['password'] ?? ''; ?></div>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
