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
$rendeles = oci_parse ($conn, 'SELECT * FROM RENDELES ORDER BY RENDELES.DATUM desc');
oci_execute($rendeles, OCI_DEFAULT);

$resze = oci_parse($conn, 'SELECT * FROM RESZE WHERE RENDELES_ID = :value');
oci_bind_by_name($resze,':value',$_SESSION['id']);
oci_execute($resze,OCI_DEFAULT);

$user_stmt = oci_parse ($conn, 'SELECT * FROM FELHASZNALO WHERE FELHASZNALO_ID = :value');
oci_bind_by_name($user_stmt, ':value', $_SESSION['id']);
oci_execute($user_stmt, OCI_DEFAULT);
$user = oci_fetch($user_stmt);

?>
<!DOCTYPE html>
<html lang="hu">

<?php
$TITLE_SUFFIX = 'Rendelések';
include(__DIR__ . '/components/head.php');
?>
<body>
    <?php
    if (!$conn) {
        $error = oci_error();
        echo "Failed to connect to database: " . $error['message'];
        exit();
    }
    $ACTIVE = 'Rendelések';
    include(__DIR__ . '/components/header.php');
    ?>
    <main>
        <h1>Rendelések:</h1>
        <table>
            <thead>
                <tr>
                    <th>Felhasználó_ID</th>
                    <th>Rendelés Dátuma</th>
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
                    print "<td>" . oci_result($rendeles,"FELHASZNALO_ID") . "</td>";
                    print "<td>" . oci_result($rendeles, "DATUM") . "</td>\n";
                    print "<td> <form method='post' action='backend/update.php'>";
                    print "<input type='hidden' name='rendeles_id' value='" . oci_result($rendeles,"RENDELES_ID") . "'/>";
                    print "<select name='teljesitve' onchange='this.form.submit()'>";
                    print "<option value='Y'" . (oci_result($rendeles,"TELJESITVE") == 'Y' ? " selected='selected'" : "") . ">Teljesítve</option>";
                    print "<option value='N'" . (oci_result($rendeles,"TELJESITVE") == 'N' ? " selected='selected'" : "") . ">Feldolgozás alatt</option>";
                    print "</select>";
                    print "</form>";
                    print "</td>";
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

    <?php include(__DIR__ . "/components/footer.php"); ?>
</body>

</html>