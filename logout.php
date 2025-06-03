<?php 
    session_start();
    unset($_SESSION ['login']);
    unset($_SESSION ['register']);
    header('Location: index.php');

?>