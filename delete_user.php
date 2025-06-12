<?php
session_start();
require 'db.php';

// Check if user is logged in, else redirect to login
if (!isset($_SESSION['login']) || !$_SESSION['login']) {
    header('Location: login.php');
    exit;
}

// Get user ID from query parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: users.php');
    exit;
}

$id = (int)$_GET['id'];

// Delete user
$stm = $db->prepare('DELETE FROM user WHERE id = :id');
$stm->bindParam(':id', $id, PDO::PARAM_INT);
$stm->execute();

header('Location: users.php');
exit;
?>
