<!DOCTYPE html>
<html lang="hu">

<?php
include(__DIR__ . '/components/head.php');
?>

<body>
<?php
$ACTIVE = 'Keresés';
include(__DIR__ . '/components/header.php');
?>
<main>
    <h1>Könyv keresése</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
    <label for="title">Cím:</label>
        <input type="text" name="title" id="title">

        <label for="author_first_name">Szerző keresztneve:</label>
        <input type="text" name="author_first_name" id="author_first_name">

        <label for="author_last_name">Szerző vezetékneve:</label>
        <input type="text" name="author_last_name" id="author_last_name">

        <label for="publisher">Kiadó:</label>
        <input type="text" name="publisher" id="publisher">

        <label for="genre">Műfaj:</label>
        <input type="text" name="genre" id="genre">

        <label for="min_year">Publikáció éve (minimum):</label>
        <input type="number" name="min_year" id="min_year">

        <label for="max_year">Publikáció éve (maximum):</label>
        <input type="number" name="max_year" id="max_year">

        <input type="submit" value="Keresés">
    </form>

    <?php
    $conn = oci_connect('system', 'oracle', 'localhost/XE');
    error_reporting(0);
    $title = $_GET['title'];
    $author_last_name = $_GET['author_last_name'];
    $author_first_name = $_GET['author_first_name'];
    $publisher = $_GET['publisher'];
    $genre = $_GET['genre'];
    $min_year = $_GET['min_year'];
    $max_year = $_GET['max_year'];

    $sql = "SELECT konyv.cim, szerzo.vezeteknev, szerzo.keresztnev, kiado.nev, konyv.mufaj, konyv.publikacio_eve, konyv.ar, konyv.darabszam
        FROM konyv
        LEFT JOIN szerzo ON konyv.isbn = szerzo.isbn
        LEFT JOIN kiado ON konyv.kiado_id = kiado.kiado_id
        WHERE 1=1";

    if (!empty($title)) {
        $sql .= " AND UPPER(konyv.cim) LIKE UPPER('%$title%')";
    }

    if (!empty($author_last_name)) {
        $sql .= " AND UPPER(szerzo.vezeteknev) LIKE UPPER('%$author_last_name%')";
    }

    if (!empty($author_first_name)) {
        $sql .= " AND UPPER(szerzo.keresztnev) LIKE UPPER('%$author_first_name%')";
    }

    if (!empty($publisher)) {
        $sql .= " AND UPPER(kiado.nev) LIKE UPPER('%$publisher%')";
    }

    if (!empty($genre)) {
        $sql .= " AND UPPER(konyv.mufaj) LIKE UPPER('%$genre%')";
    }

    if (!empty($min_year)) {
        $sql .= " AND konyv.publikacio_eve >= $min_year";
    }

    if (!empty($max_year)) {
        $sql .= " AND konyv.publikacio_eve <= $max_year";
    }

    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);

    echo "<table>
        <tr>
            <th>Cím</th>
            <th>Szerző</th>
            <th>Kiadó</th>
            <th>Műfaj</th>
            <th>Publikáció éve</th>
            <th>Ár</th>
            <th>Darabszám</th>
        </tr>";

    while ($row = oci_fetch_assoc($stmt)) {
        echo "<tr>
            <td>{$row['CIM']}</td>
            <td>{$row['VEZETEKNEV']} {$row['KERESZTNEV']}</td>
            <td>{$row['NEV']}</td>
            <td>{$row['MUFAJ']}</td>
            <td>{$row['PUBLIKACIO_EVE']}</td>
            <td>{$row['AR']}</td>
            <td>{$row['DARABSZAM']}</td>
        </tr>";
    }

    echo "</table>";

    oci_free_statement($stmt);
    oci_close($conn);
    ?>
</main>
<?php include(__DIR__ . '/components/footer.php'); ?>

</body>

</html>