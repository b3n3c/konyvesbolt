<?php
include(__DIR__ . '/backend/validator.php');

session_start();
require(__DIR__ . '/backend/conn.php');?>

<?php
$TITLE_SUFFIX = 'Felhasználók';
include(__DIR__ . '/components/head.php');
?>
<body>
<?php
$ACTIVE = 'Felhasználók';
include(__DIR__ . '/components/header.php');
?>
    <!-- HTML űrlap -->
    <form method="post">
        <label for="vezeteknev">Vezetéknév:</label>
        <input type="text" id="vezeteknev" name="vezeteknev">
        <label for="keresztnev">Keresztnév:</label>
        <input type="text" id="keresztnev" name="keresztnev">
        <input type="submit" value="Keresés">
    </form>
<?php
// Content-type és karakterkódolás beállítása
header('Content-type: text/html; charset=utf-8');

// Adatbázis kapcsolat
$conn = oci_connect('system', 'oracle', 'localhost/XE');
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vezeteknev = $_POST['vezeteknev'];
    $keresztnev = $_POST['keresztnev'];

    // Lekérdezés a felhasználók táblájából vezetéknév és keresztnév alapján
    $sql = "SELECT * FROM felhasznalo WHERE vezeteknev LIKE '%$vezeteknev%' AND keresztnev LIKE '%$keresztnev%'";
    $stmt = oci_parse($conn, $sql);
    if (!$stmt) {
        $e = oci_error($conn);
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }

    // Lekérdezés végrehajtása
    if (!oci_execute($stmt)) {
        $e = oci_error($stmt);
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }

    // Eredmények megjelenítése
    echo "<h2>Keresés eredménye:</h2>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Keresztnév</th><th>Vezetéknév</th><th>E-mail</th><th>Admin</th></tr>";
    while (($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
        echo "<tr>";
        echo "<td>" . $row['FELHASZNALO_ID'] . "</td>";
        echo "<td>" . $row['KERESZTNEV'] . "</td>";
        echo "<td>" . $row['VEZETEKNEV'] . "</td>";
        echo "<td>" . $row['EMAIL'] . "</td>";
        echo "<td>" . ($row['ADMIN']=='Y' ? 'Igen' : 'Nem') . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Kapcsolat lezárása
    oci_free_statement($stmt);
}

?>




<?php include(__DIR__ . "/components/footer.php"); ?>