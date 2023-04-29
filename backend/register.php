<?php
include('dbhelper.php');
$conn = getDb();

// kérés típusának ellenőrzése
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    http_response_code(405);
    exit();
}

session_start();

// ha be van jelentkezve, akkor átirányítás a főoldalra
if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
    header('Location: ../index.php');
    exit();
}

function redirectWithError($error, $extra = "") {
    header(
        "Location: ../bejelentkezes.php?error=$error&form=register".
        "&email=".urlencode($_POST['email'])
    );
    exit();
}

// tömb mezők ellenőrzése
foreach ($_POST as $key => $value) {
    if (isset($value) && !is_array($value)) {
        $_POST[$key] = trim($value);
    } else {
        unset($_POST[$key]);
    }
}

// mezők hosszának ellenőrzése
foreach ($_POST as $key => $value) {
    if ($key == 'introduction') continue;
    if (isset($value) && !empty($value)) {
        if (strlen($_POST[$key]) > 100) {
            redirectWithError("FieldTooLong", "&field=".urlencode($key));
        }
    } else {
        redirectWithError("EmptyField", "&field=".urlencode($key));
    }
}

// kötelező mezők ellenőrzése
$required = ["email", "password", "password1"];
foreach ($required as $key) {
    if (!isset($_POST[$key]) || empty($_POST[$key])) {
        redirectWithError("EmptyField", "&field=".urlencode($key));
    }
}

// email cím ellenőrzése
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    redirectWithError("InvalidEmail");
}

// jelszó hosszának ellenőrzése
if (strlen($_POST['password']) < 8) {
    redirectWithError("PasswordTooShort");
}
$email = $_POST['email'];

require(__DIR__ . '/conn.php');


// ismételt jelszó ellenőrzése
if ($_POST['password'] != $_POST['password1']) {
    redirectWithError("PasswordsDontMatch");
}


// email használatának ellenőrzése
$check = oci_parse ($conn, 'SELECT * FROM FELHASZNALO WHERE EMAIL = :email');
oci_bind_by_name($check, ":email", $_POST["email"]);
oci_execute($check);
if (oci_fetch($check))
    redirectWithError("EmailIsInUse");

$idstd = oci_parse ($conn, 'SELECT MAX(FELHASZNALO_ID) AS MAXX FROM FELHASZNALO');
oci_execute($idstd);
oci_fetch($idstd);
$id = ((int)oci_result($idstd, "MAXX"))+1;

$register = oci_parse ($conn, 'INSERT INTO felhasznalo VALUES(
                               :id, :knev, :vnev, :email, :jelszo, :admin, :hazszam, :utca, :irsz, :megye, :orszag, :tel, :varos)');
$admin = 'N';
oci_bind_by_name($register, ":admin",$admin);
oci_bind_by_name($register, ":id",$id);
oci_bind_by_name($register, ":knev",$_POST['k_name']);
oci_bind_by_name($register, ":vnev",$_POST['v_name']);
oci_bind_by_name($register, ":email",$_POST['email']);
oci_bind_by_name($register, ":jelszo",$_POST['password']);
oci_bind_by_name($register, ":hazszam",$_POST['h-number']);
oci_bind_by_name($register, ":utca",$_POST['street']);
oci_bind_by_name($register, ":irsz",$_POST['irsz']);
oci_bind_by_name($register, ":megye",$_POST['megye']);
oci_bind_by_name($register, ":orszag",$_POST['country']);
oci_bind_by_name($register, ":tel",$_POST['tel']);
oci_bind_by_name($register, ":varos",$_POST['city']);

if(oci_execute($register)){
    oci_commit($conn);
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['id'] = $id;
    header('Location: ../index.php');
    echo "Sikeres regisztráció!";
}else{
    redirectWithError("DatabaseError");
}


