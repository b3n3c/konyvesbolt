<?php
include(__DIR__ . '/backend/validator.php');
include(__DIR__ . '/backend/dbhelper.php');

session_start();

if(isset($_POST["addToCart"]) && $_POST["quantity"] > 0){
    if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
        header('Location: bejelentkezes.php');
        exit();
    }
    if(isset($_SESSION["cart"][$_POST["isbn"]])){
        $_SESSION["cart"][$_POST["isbn"]] += $_POST["quantity"];
    }else{
        $_SESSION["cart"][$_POST["isbn"]] = $_POST["quantity"];
    }
    header("Location: reszletek.php?isbn={$_POST["isbn"]}"."&success=1#success");
}

$conn = getDb();

$konyv = getBookByISBN($_GET["isbn"]);

$k_cim = $konyv["CIM"];
$k_mufaj = $konyv["MUFAJ"];
$k_tipus = $konyv["TIPUS"];
$k_publikacio_eve = $konyv["PUBLIKACIO_EVE"];
$k_ar = $konyv["AR"];
$kiado_id = $konyv["KIADO_ID"];
$k_kiado = $konyv["KIADO"];
$k_szerzok = getSzerzokString($_GET["isbn"]);

$mas_konyvek = oci_parse($conn, "SELECT RESZE.ISBN, KONYV.CIM FROM RESZE, KONYV WHERE RENDELES_ID IN(
SELECT RENDELES_ID FROM RESZE WHERE ISBN = :value GROUP BY RENDELES_ID) AND RESZE.ISBN != :value AND RESZE.ISBN=KONYV.ISBN");
oci_bind_by_name($mas_konyvek, ':value', $_GET['isbn']);
oci_execute($mas_konyvek, OCI_DEFAULT);


$legjobb_a_mufajban = oci_parse($conn, "SELECT konyv.cim, resze.ISBN, SUM(resze.darab) AS peldany FROM konyv, resze WHERE
                                                                                konyv.isbn = resze.isbn AND konyv.mufaj=:mufaj AND resze.ISBN != :isbn GROUP BY resze.ISBN, konyv.cim ORDER BY SUM(resze.darab) DESC FETCH NEXT 3 ROWS ONLY");
oci_bind_by_name($legjobb_a_mufajban, ':isbn', $_GET['isbn']);
oci_bind_by_name($legjobb_a_mufajban, ':mufaj', $k_mufaj);
oci_execute($legjobb_a_mufajban, OCI_DEFAULT);

$legjobb_a_kiadotol = oci_parse($conn, "SELECT konyv.cim, resze.ISBN, SUM(resze.darab) AS peldany FROM konyv, resze WHERE konyv.isbn = resze.isbn AND konyv.kiado_id=:kiado AND resze.ISBN != :isbn GROUP BY resze.ISBN, konyv.cim ORDER BY SUM(resze.darab) DESC FETCH NEXT 3 ROWS ONLY");
oci_bind_by_name($legjobb_a_kiadotol, ':isbn', $_GET['isbn']);
oci_bind_by_name($legjobb_a_kiadotol, ':kiado', $kiado_id);
oci_execute($legjobb_a_kiadotol, OCI_DEFAULT);
?>

<!DOCTYPE html>
<html lang="hu">

<?php
$CSS = ["css/rating.css"];
include(__DIR__ . '/components/head.php');
?>

<body>
<?php
$ACTIVE = 'Könyvek';
include(__DIR__ . '/components/header.php');
?>
<main>
    <?php
        print "<h1>{$k_cim}</h1>";
        print "<h2>{$k_szerzok}</h2>";
        print "<h3>Kiadó: {$k_kiado}</h3>";
        print "<p>Műfaj: {$k_mufaj}</p>";
        print "<p>Ár: {$k_ar} Ft.</p><br>";
    ?>

    <?php

    if (isset($_SESSION["admin"])) {

        $konyv = oci_parse ($conn, 'SELECT * FROM KONYV WHERE ISBN = :value');
        oci_bind_by_name($konyv, ':value', $_GET['isbn']);
        oci_execute($konyv, OCI_DEFAULT);
        oci_fetch($konyv);

        $k_cim = oci_result($konyv, "CIM");
        $k_mufaj = oci_result($konyv, "MUFAJ");
        $k_isbn = oci_result($konyv, "ISBN");
        $k_tipus = oci_result($konyv, "TIPUS");
        $k_publikacio_eve = oci_result($konyv, "PUBLIKACIO_EVE");
        $k_ar = oci_result($konyv, "AR");
        $k_darab = oci_result($konyv, "DARABSZAM");
        $kiado_id = oci_result($konyv, "KIADO_ID");

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

    <form method="post" action="backend/updateBook.php">
        <fieldset>
            <legend>Jelenlegi könyv módositása</legend>

            <label for="title">Könyv cime:</label>
            <input required id="title" maxlength="49" type="text" name="cim" value="<?php echo $k_cim?>">

            <label for="ISBN">ISBN szám:</label>
            <input required id="ISBN" name="ISBN" type="text" value="<?php echo $k_isbn?>">
            <br />

            <label for="mufaj">Műfaj:</label>
            <input type="text" required id="mufaj" name="mufaj" value="<?php echo $k_mufaj?>">
            <br />

            <label for="tipus">Tipus (puha, vagy keményfedeles):</label>
            <input type="text" required id="tipus" name="tipus" value="<?php echo $k_tipus?>">
            <br />

            <label for="ev">Publikáció éve:</label>
            <input type="number" max="3000" min="0" required id="ev" name="ev" value="<?php echo $k_publikacio_eve?>">
            <br />

            <label for="ar">Könyv ára (Ft):</label>
            <input type="number" min="0" required id="ar" name="ar" value="<?php echo $k_ar?>">
            <br />

            <label for="db">Hány darab van raktáron?</label>
            <input type="number"  min="0" required id="db" name="db" value="<?php echo $k_darab?>">
            <br />

            <select name="kiadoid">
                <option>Válassz egy kiadót:</option>
                <?php
                foreach($kiadok as $kiado){
                    if ($kiado[0] == $kiado_id) {
                        echo "<option value='$kiado[0]' selected>$kiado[1]</option>";
                    } else {
                        echo "<option value='$kiado[0]'>$kiado[1]</option>";
                    }
                }
                ?>
            </select>
            <br /><br /><br />
            <input type="submit" value="Frissites" />
            <?php if (isset($_GET["success1"]) && $_GET["success1"] == "1") { ?>
                <span id="success" class='ok'>A könyv sikeresen frissitve!</span>
            <?php } ?>
        </fieldset>
    </form>

        <?php
            }
        ?>

    <?php

        $user = oci_parse ($conn, 'SELECT * FROM FELHASZNALO WHERE FELHASZNALO_ID = :value');
        oci_bind_by_name($user, ':value', $_SESSION['id']);
        oci_execute($user, OCI_DEFAULT);
        oci_fetch($user);

        $userId = oci_result($user, "FELHASZNALO_ID");

        oci_close($conn);


        ?>

        <form method="post" action="">
            <fieldset>
                <legend>Könyv rendelése:</legend>
                <input type="hidden" id="custId" name="isbn" value="<?php echo $_GET["isbn"] ?>">
                <br /><br /><br />
                <label for="quantity">Mennyiség: </label>
                <input type="number" value="1" id="quantity" name="quantity" min="1">
                <input type="submit" value="Kosárhoz adás" name="addToCart"/>
                <?php if (isset($_GET["success"]) && $_GET["success"] == "1") { ?>
                    <span id="success" class='ok'>A könyv sikeresen hozzáadva a kosárhoz!</span>
                <?php } ?>
            </fieldset>
        </form>

    <?php
    //Legnépszerűbb a műfajban
    $i = 0;
    while (oci_fetch($legjobb_a_mufajban)) {
        if($i == 0){
            print "<br><p>A legnépszerűbb könyvek a műfajban: </p><br>";
        }
        $akt_isbn = (int)oci_result($legjobb_a_mufajban, 'ISBN');
        print "<a href='reszletek.php?isbn={$akt_isbn}'><div class='card'>\n";
        print "<h1>" . oci_result($legjobb_a_mufajban, 'CIM') . "</h1>\n";

        $szerzok = oci_parse($conn, "SELECT * FROM SZERZO WHERE ISBN = :value");
        oci_bind_by_name($szerzok, ':value', $akt_isbn);
        oci_execute($szerzok, OCI_DEFAULT);
        $k = 1;
        while (oci_fetch($szerzok)){
            print "<h2>";
            if ($k != 1)
                print ", ";
            print ($szerzok !== null ? oci_result($szerzok, 'VEZETEKNEV') : "&nbsp;") . " " .
                ($szerzok !== null ? oci_result($szerzok, 'KERESZTNEV') : "&nbsp;") . "</h2>\n";
            $k += 1;
        }
        print "</div></a>\n";
        $i += 1;
    }
    ?>

    <?php
    $i = 0;
    while (oci_fetch($mas_konyvek)) {
        if($i == 0){
            print "<br><p>Akik megvásárolták ezt a könyvet, ezeket is olvasták már: </p><br>";
        }
        $akt_isbn = (int)oci_result($mas_konyvek, 'ISBN');
        print "<a href='reszletek.php?isbn={$akt_isbn}'><div class='card'>\n";
        print "<h1>" . oci_result($mas_konyvek, 'CIM') . "</h1>\n";

        $szerzok = oci_parse($conn, "SELECT * FROM SZERZO WHERE ISBN = :value");
        oci_bind_by_name($szerzok, ':value', $akt_isbn);
        oci_execute($szerzok, OCI_DEFAULT);
        $k = 1;
        while (oci_fetch($szerzok)){
            print "<h2>";
            if ($k != 1)
                print ", ";
            print ($szerzok !== null ? oci_result($szerzok, 'VEZETEKNEV') : "&nbsp;") . " " .
                ($szerzok !== null ? oci_result($szerzok, 'KERESZTNEV') : "&nbsp;") . "</h2>\n";
            $k += 1;
        }
        print "</div></a>\n";
        $i += 1;
    }
    ?>

    <?php
    //Legnépszerűbb a a kiadótól
    $i = 0;
    while (oci_fetch($legjobb_a_kiadotol)) {
        if($i == 0){
            print "<br><p>A legnépszerűbb könyvek a kiadótól: </p><br>";
        }
        $akt_isbn = (int)oci_result($legjobb_a_kiadotol, 'ISBN');
        print "<a href='reszletek.php?isbn={$akt_isbn}'><div class='card'>\n";
        print "<h1>" . oci_result($legjobb_a_kiadotol, 'CIM') . "</h1>\n";

        $szerzok = oci_parse($conn, "SELECT * FROM SZERZO WHERE ISBN = :value");
        oci_bind_by_name($szerzok, ':value', $akt_isbn);
        oci_execute($szerzok, OCI_DEFAULT);
        $k = 1;
        while (oci_fetch($szerzok)){
            print "<h2>";
            if ($k != 1)
                print ", ";
            print ($szerzok !== null ? oci_result($szerzok, 'VEZETEKNEV') : "&nbsp;") . " " .
                ($szerzok !== null ? oci_result($szerzok, 'KERESZTNEV') : "&nbsp;") . "</h2>\n";
            $k += 1;
        }
        print "</div></a>\n";
        $i += 1;
    }
    ?>

</main>
<?php include(__DIR__ . '/components/footer.php'); ?>

</body>

</html>
