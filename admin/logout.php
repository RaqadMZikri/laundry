<?php
    session_start();
    session_unset();
    $_SESSION['login'] = false;
    session_destroy();
    header('location:../index.php');
    exit();
?>  