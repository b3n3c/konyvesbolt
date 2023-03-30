<?php

session_start();

if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
    session_destroy();
    header('Location: ../index.php');
    exit();
} else {
    header('Location: ../index.php');
    exit();
}

?>