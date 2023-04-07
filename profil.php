<?php
include(__DIR__ . '/backend/validator.php');
include(__DIR__ . '/backend/dbhelper.php');
$conn = getDb();
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    header('Location: bejelentkezés.php');
    exit();
}

if (isset($_GET['error'])) {
    $row = $_GET;
} else {
    require(__DIR__ . '/backend/conn.php');

    $rendeles = oci_parse ($conn, 'SELECT * FROM RENDELES WHERE FELHASZNALO_ID = :value');
    oci_bind_by_name($rendeles, ':value', $_SESSION['id']);
    oci_execute($rendeles, OCI_DEFAULT);

    $user_stmt = oci_parse ($conn, 'SELECT * FROM FELHASZNALO WHERE FELHASZNALO_ID = :value');
    oci_bind_by_name($user_stmt, ':value', $_SESSION['id']);
    oci_execute($user_stmt, OCI_DEFAULT);
    $user = oci_fetch($user_stmt);
}

?>
<!DOCTYPE html>
<html>
<?php
$TITLE_SUFFIX = "Profilom";
include(__DIR__ . '/components/head.php');
?>

<body>
<?php
$ACTIVE = 'Profilom';
include(__DIR__ . '/components/header.php');
?>
<main>
    <h1>Profilom</h1>
    <?php
    print "<p>Név: " . oci_result($user_stmt, "VEZETEKNEV") . " " . oci_result($user_stmt, "KERESZTNEV") . "</p>\n";
    print "<p>Rendelések: </p>\n";
    ?>
    <table>
        <thead>
        <tr>
            <th>Dátum</th>
            <th>Állapot</th>
            <th>Rendelt termékek</th>
            <th>Végösszeg</th>
        </tr>
        </thead>
        <tbody>
        <?php
        while (oci_fetch($rendeles)){
            $rendeles_id = oci_result($rendeles, "RENDELES_ID");
            print "<tr>\n";
            print "<td>" . oci_result($rendeles, "DATUM") . "</td>\n";
            if(oci_result($rendeles, "TELJESITVE") == "Y")
                print "<td>Teljesítve</td>\n";
            else
                print "<td>Kézbesítés alatt</td>\n";
            print "<td><ul>";
            $resze = oci_parse ($conn, 'SELECT KONYV.CIM, RESZE.DARAB FROM RESZE, KONYV WHERE RENDELES_ID = :value AND RESZE.ISBN=KONYV.ISBN');
            oci_bind_by_name($resze, ':value', $rendeles_id);
            oci_execute($resze, OCI_DEFAULT);
            while (oci_fetch($resze)){
                print "<li>" . oci_result($resze, "CIM") . " x" . oci_result($resze, "DARAB") . "</li>";
            }
            print "</ul></td>";
            print "<td>" . oci_result($rendeles, "AR") . " Ft.</td>\n";
            print "</tr>\n";
        }
        ?>
        </tbody>
    </table>

</main>

<?php include(__DIR__ . '/components/footer.php'); ?>
</body>

</html>