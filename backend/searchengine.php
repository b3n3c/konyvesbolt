<?php
// Adatbázis kapcsolat létrehozása
$conn = oci_connect('system', 'oracle', 'localhost/XE');

// Űrlapról érkező adatok lekérdezése
$title = $_GET['title'];
$author_last_name = $_GET['author_last_name'];
$author_first_name = $_GET['author_first_name'];
$publisher = $_GET['publisher'];
$genre = $_GET['genre'];
$min_year = $_GET['min_year'];
$max_year = $_GET['max_year'];

// SQL lekérdezés összeállítása
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

// SQL lekérdezés végrehajtása
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);

// Lekérdezés eredményének feldolgozása és megjelenítése a táblázatban
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

// Adatbázis kapcsolat lezárása
oci_free_statement($stmt);
oci_close($conn);
?>
