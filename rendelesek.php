<?php 
include(__DIR__ . '/backend/validator.php'); 

session_start();
require(__DIR__ . '/backend/conn.php');

if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    header('Location: bejelentkezés.php');
    exit();
}

if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    header('Location: profil.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="hu">

<?php
$TITLE_SUFFIX = 'Rendelések';
include(__DIR__ . '/components/head.php');
?>
<body>
    <?php
    $ACTIVE = 'Rendelések';
    include(__DIR__ . '/components/header.php');
    ?>
    <main>
        <p>Fejlesztés alatt</p>>
    </main>
    <?php include(__DIR__ . "/components/footer.php"); ?>
</body>

</html>