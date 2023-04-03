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

<?php

$kiadok = [];

$conn = oci_connect('system', 'oracle', 'localhost/XE');
if (!$conn) {
    $e = oci_error();
    trigger_error (htmlentities ($e ['message'], ENT_QUOTES), E_USER_ERROR);
}

$stid = oci_parse ($conn, 'SELECT * FROM KIADO');
if (!$stid) {
    $e = oci_error($conn);
    trigger_error (htmlentities ($e ['message'], ENT_QUOTES), E_USER_ERROR);
}

$r = oci_execute($stid);
if (!$r) {
    $e = oci_error($stid);
    trigger_error (htmlentities ($e ['message'], ENT_QUOTES), E_USER_ERROR);
}
$iteration = 0;
while (oci_fetch($stid)) {
    $kiadok[$iteration][0] = (int)oci_result($stid, 'KIADO_ID');
    $kiadok[$iteration][1] = oci_result($stid, 'NEV');
    $iteration++;
}
oci_free_statement ($stid);
oci_close($conn);

?>



<!DOCTYPE html>
<html lang="hu">

<?php
$TITLE_SUFFIX = 'Új könyv hozzáadása';
include(__DIR__ . '/components/head.php');
?>
<body>
    <?php
    $ACTIVE = 'Új könyv hozzáadása';
    include(__DIR__ . '/components/header.php');
    ?>

    <form method="post" action="backend/newBookAction.php">
        <fieldset>
            <legend>Új könyv hozzáadása a könyvtárhoz</legend>

            <label for="title">Könyv cime:</label>
            <input required id="title" maxlength="49" type="text" name="cim">

            <label for="ISBN">ISBN szám:</label>
            <input required id="ISBN" name="ISBN" type="text">
            <br />

            <label for="mufaj">Műfaj:</label>
            <input type="text" required id="mufaj" name="mufaj">
            <br />

            <label for="tipus">Tipus (puha, vagy keményfedeles):</label>
            <input type="text" required id="tipus" name="tipus">
            <br />

            <label for="ev">Publikáció éve:</label>
            <input type="number" max="3000" min="0" required id="ev" name="ev">
            <br />

            <label for="ar">Könyv ára (Ft):</label>
            <input type="number" min="0" required id="ar" name="ar">
            <br />

            <label for="db">Hány darab van raktáron?</label>
            <input type="number"  min="0" required id="db" name="db">
            <br />

            <select name="kiadoid">
                <option selected="selected">Válassz egy kiadót:</option>
                <?php
                foreach($kiadok as $kiado){
                    echo "<option value='$kiado[0]'>$kiado[1]</option>";
                }
                ?>
            </select>

            <br /><br /><br />





            <input type="submit" value="Hozzáadás" />
            <input type="reset" value="Vissza mindent" />
            <?php if (isset($_GET["success"]) && $_GET["success"] == "1") { ?>
                <span id="success" class='ok'>A könyv sikeresen hozzáadva!</span>
            <?php } ?>
        </fieldset>
    </form>
    </main>







    <?php include(__DIR__ . "/components/footer.php"); ?>
</body>

</html>


