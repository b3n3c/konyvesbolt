<?php

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    http_response_code(405);
    exit();
}

session_start();

if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
    header('Location: ../profil.php');
    exit();
}

$email = $_POST['email'];
$password = $_POST['password'];


if (isset($password) && !empty($password)) {
    $password = trim($password);
} else {
    header("Location: ../bejelentkezés.php?form=login&error=EmptyPassword&email=" . urlencode($email));
    echo "A jelszó mező nem lehet üres!";
    exit();
}


if (isset($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $email = trim($email);
} else {
    header("Location: ../bejelentkezés.php?form=login&error=InvalidEmail&email=" . urlencode($email));
    echo "Az e-mail cím formátuma nem megfelelő.";
    exit();
}

require(__DIR__ . '/conn.php');

$sql = "SELECT * FROM users WHERE email = ?";

$login = oci_parse ($conn, 'SELECT * FROM FELHASZNALO WHERE EMAIL = :email AND JELSZO = :password');
oci_bind_by_name($login, ':email', $email);
oci_bind_by_name($login, ':password', $password);
oci_execute($login, OCI_DEFAULT);
if(oci_fetch($login)){
    $_SESSION['email'] = $email;
    $_SESSION['id'] = oci_result($login, "FELHASZNALO_ID");
    $_SESSION['admin'] = (oci_result($login, "ADMIN")=="Y");
    header("Location: ../profil.php");
    echo "Sikeres bejelentkezés!";
}else{
    header("Location: ../bejelentkezés.php?form=login&error=UserDoesntExist&email=" . urlencode($email));
    echo "A megadott adataok hibásak";
    exit();
}