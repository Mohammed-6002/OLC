<?php
require 'db.php';

$inputs = [];
$errors = [];

const EMAIL_ERROR = 'Email is required.';
const PASSWORD_ERROR = 'Password is required.';
const FIRSTNAME_ERROR = 'First name is required.';
const LASTNAME_ERROR = 'Last name is required.';

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

if (empty($errors)) {
    $passwordHash = password_hash($inputs['password'], PASSWORD_DEFAULT);

    $stmt = $db->prepare("INSERT INTO users (email, password, firstname, lastname) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$inputs['email'], $passwordHash, $inputs['firstname'], $inputs['lastname']]);

    if ($result) {
        echo "Registration successful.";
    } else {
        echo "Error during registration.";
    }
} else {
    foreach ($errors as $field => $error) {
        echo "<p>Error in $field: $error</p>";
    }
}
?>
