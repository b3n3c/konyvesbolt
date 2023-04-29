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
            <br /><br />
            <fieldset>
                <legend>Kiadó</legend>
                <label for="kiado">Új kiadó hozzáadása</label>
                <input type="checkbox" id="kiado" name="kiado" value="ujkiado">
                <br /><br />
                <label for="kiadoid" id="kiadoid-label">Válassz egy kiadót: </label>
                <select name="kiadoid" id="kiadoid">
                    <option selected="selected"> </option>
                    <?php
                    foreach($kiadok as $kiado){
                        echo "<option value='$kiado[0]'>$kiado[1]</option>";
                    }
                    ?>
                </select>
                <br>
                <label for="uj-kiado" class="disabled" id="uj-kiado-label">Új kiadó neve:</label>
                <input required id="uj-kiado" maxlength="100" type="text" name="uj-kiado" disabled>
                <label for="uj-kiado-orszag" class="disabled" id="uj-kiado-orszag-label">Ország:</label>
                <input required id="uj-kiado-orszag" maxlength="100" type="text" name="uj-kiado-orszag" disabled>

            </fieldset>
            <br /><br />
            <fieldset>
                <legend>Szerző(k)</legend>
                <div id="szerzok">
                    <div id="szerzo1">
                        <label for="vez-nev-1">Vezetéknév:</label>
                        <input required id="vez-nev-1" maxlength="49" type="text" name="vez-nev-1">
                        <label for="ker-nev-1">Keresztnév:</label>
                        <input required id="ker-nev-1" maxlength="49" type="text" name="ker-nev-1">
                    </div>
                </div>
                <button type="button" name="add" id="add">Új szerző hozzáadása</button>
            </fieldset>

            <br /><br /><br />

            <input type="submit" value="Hozzáadás" />
            <input type="reset" value="Vissza mindent" />
            <?php if (isset($_GET["success"]) && $_GET["success"] == "1") { ?>
                <span id="success" class='ok'>A könyv sikeresen hozzáadva!</span><br>
            <?php } ?>
            <?php if (isset($_GET["warning"]) && $_GET["warning"] == "szerzok") {
                $s = substr($_SESSION["szerzok"], 0, -3);
                print "<br><span id='success' class='warning'>{$s} nevű szerző(k) már szerepeltek az adatbázisban.</span>";
            } ?>

        </fieldset>
    </form>
    </main>

    <?php include(__DIR__ . "/components/footer.php"); ?>
</body>

</html>

<script>
    jQuery(document).ready(function($){
        var i=1;
        $('#add').click(function(){
            i++;
            $('#szerzok').append('<div id=szerzo'+i+'><button type="button" name="remove" class="remove" id='+i+'>X</button><hr/><label for="vez-nev-'+i+'">Vezetéknév:</label><input required id="vez-nev-'+i+'" maxlength="49" type="text" name="vez-nev-'+i+'"> <label for="ker-nev-'+i+'">Keresztnév:</label> <input required id="ker-nev-'+i+'" maxlength="49" type="text" name="ker-nev-'+i+'"></div>');
        });
        $(document).on('click', '.remove', function(){
            var button_id = $(this).attr("id");
            $('#pizza'+button_id).remove();
        });
        $(document).on('click', '.remove', function(){
            var button_id = $(this).attr("id");
            $('#szerzo'+button_id).remove();
        });
    });

    $('#kiado').click(function() {
        if ($(this).is(':checked')) {
            $("#kiadoid").prop('disabled', true);
            $("#kiadoid-label").addClass("disabled");
            $("#uj-kiado").prop("disabled", false);
            $("#uj-kiado-label").removeClass("disabled");
            $("#uj-kiado-orszag").prop("disabled", false);
            $("#uj-kiado-orszag-label").removeClass("disabled");
        }else{
            $("#kiadoid").prop('disabled', false);
            $("#kiadoid-label").removeClass("disabled");
            $("#uj-kiado").prop("disabled", true);
            $("#uj-kiado-label").addClass("disabled");
            $("#uj-kiado-orszag").prop("disabled", true);
            $("#uj-kiado-orszag-label").addClass("disabled");
        }
    });
</script>


