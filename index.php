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
</head>
<body>
    <?php if ($user): ?>
        <p>Welkom <?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></p>
    <?php else: ?>
        <p>U bent niet ingelogd.</p>
    <?php endif; ?>
</body>
</html>
