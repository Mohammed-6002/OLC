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
</head>
<body>
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
</body>
</html>