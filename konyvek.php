<?php
include(__DIR__ . '/backend/validator.php');
include(__DIR__ . '/backend/conn.php');

session_start();

require(__DIR__ . '/backend/conn.php');

$stid = oci_parse ($conn, 'SELECT * FROM KONYV');
if (!$stid) {
    $e = oci_error($conn);
    trigger_error (htmlentities ($e ['message'], ENT_QUOTES), E_USER_ERROR);
}

$r = oci_execute($stid);
if (!$r) {
    $e = oci_error($stid);
    trigger_error (htmlentities ($e ['message'], ENT_QUOTES), E_USER_ERROR);
}
?>

<!DOCTYPE html>
<html lang="hu">

<?php
include(__DIR__ . '/components/head.php');
?>

<body>
<?php
$ACTIVE = 'Könyvek';
include(__DIR__ . '/components/header.php');
?>
<main>
    <?php
    while (oci_fetch($stid)) {
        $akt_isbn = (int)oci_result($stid, 'ISBN');
        print "<a href='reszletek.php?isbn={$akt_isbn}'><div class='card'>\n";
        print "<h1>" . ($stid !== null ? oci_result($stid, 'CIM') : "&nbsp;") . "</h1>\n";

        $szerzok = oci_parse($conn, "SELECT * FROM SZERZO WHERE ISBN = :value");
        oci_bind_by_name($szerzok, ':value', $akt_isbn);
        oci_execute($szerzok, OCI_DEFAULT);
        $i = 1;
        while (oci_fetch($szerzok)){
            print "<h2>";
            if ($i != 1)
                print ", ";
            print ($szerzok !== null ? oci_result($szerzok, 'VEZETEKNEV') : "&nbsp;") . " " .
                ($szerzok !== null ? oci_result($szerzok, 'KERESZTNEV') : "&nbsp;") . "</h2>\n";
        }
        print "</div></a>\n";
    }
    oci_free_statement ($stid);
    oci_close($conn);
    ?>
</main>
<?php include(__DIR__ . '/components/footer.php'); ?>

</body>

</html>


