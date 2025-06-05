<?php
require 'db.php';

$inputs = [];
$errors = [];

const EMAIL_ERROR = 'Email is required.';
const PASSWORD_ERROR = 'Password is required.';
const FIRSTNAME_ERROR = 'First name is required.';
const LASTNAME_ERROR = 'Last name is required.';
const USER_EXIST_ERROR = 'Het emailadres is al gebruikt';

if (empty($_POST['email'])) {
    $errors['email'] = EMAIL_ERROR;
} else {
$inputs['email'] = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if ($inputs['email'] === false) {
        $errors['email'] = 'Invalid email format.';
    }
}

if (empty($_POST['password'])) {
    $errors['password'] = PASSWORD_ERROR;
} else {
    $inputs['password'] = $_POST['password'];
}

if (empty($_POST['firstname'])) {
    $errors['firstname'] = FIRSTNAME_ERROR;
} else {
    $inputs['firstname'] = trim($_POST['firstname']);
}

if (empty($_POST['lastname'])) {
    $errors['lastname'] = LASTNAME_ERROR;
} else {
    $inputs['lastname'] = trim($_POST['lastname']);
}

if (count($errors) === 0) {
    $stm = $db->prepare('SELECT email FROM user WHERE email = :email');
    $stm->bindParam(':email', $inputs['email']);
    $stm->execute();
    $user = $stm->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $stm = $db->prepare('INSERT INTO user (email, password, first_name, last_name) VALUES (:email, :password, :first_name, :last_name)');
        $passwordHash = password_hash($inputs['password'], PASSWORD_DEFAULT);
        $stm->bindParam(':email', $inputs['email']);
        $stm->bindParam(':password', $passwordHash);
        $stm->bindParam(':first_name', $inputs['firstname']);
        $stm->bindParam(':last_name', $inputs['lastname']);
        $stm->execute();
        echo "Registration successful.";
    } else {
        $errors['user'] = USER_EXIST_ERROR;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <form action="register.php" method="POST">
        <label for="email">Email:</label><br />
        <input type="email" id="email" name="email" value="<?php echo $inputs['email'] ?? ''; ?>">
        <div><?php echo $errors['email'] ?? ''; ?></div>

        <label for="password">Password:</label><br />
        <input type="password" id="password" name="password">
        <div><?php echo $errors['password'] ?? ''; ?></div>

        <label for="firstname">First Name:</label><br />
        <input type="text" id="firstname" name="firstname"  value="<?php echo $inputs['firstname'] ?? ''; ?>">
        <div><?php echo $errors['firstname'] ?? ''; ?></div>

        <label for="lastname">Last Name:</label><br />
        <input type="text" id="lastname" name="lastname" value="<?php echo $inputs['lastname'] ?? ''; ?>">
        <div><?php echo $errors['lastname'] ?? ''; ?></div>

        <button type="submit">Register</button>
    </form>
</body>
</html>
