<?php include(__DIR__.'/backend/validator.php');

include(__DIR__ . '/backend/dbhelper.php');
session_start();
$conn = getDb();
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
$TITLE_SUFFIX = 'Kezdőlap';
include(__DIR__.'/components/head.php');
?>

<body>
<?php
$ACTIVE = 'Kezdőlap';
include(__DIR__.'/components/header.php');
?>

<main>
    <article>
        <h1>Üdvözlöm a Könyvesboltban!</h1>
        <p>Oldalunk a könyvek mellett, egy-egy kemény borítóba zárt történetet árul.
        </p>
        <p>A könyvek, a mozi és a televízió világában nélkülözhetetlen elem, amely segíti az olvasót a történet
            elképzelésében
            és a
            karakterek érzéseinek kifejezésében. Ennek köszönhetően az olvasás nagyon fontos
            szerepet
            játszik a szórakoztatásban, mint az ismeretterjesztésben.
        </p>
        <p>Ezen az oldalon számos kategóriát, beleértve a sci-fi, ismeretterjesztő, gyermekkönyvek,
            regények
            és
            mesekönyveket talál.
        </p>
        <p>Remélem, hogy ezen az oldalon megtalálja a kívánt könyvet, és ha kérdései vagy problémája
            lenne
            , kérjük, vegye fel a kapcsolatot az ügyfélszolgálattal.
        </p>
    </article>
    <section>
        <h2>A top 5 rendelt könyv:</h2>
        <?php
        $sql = "SELECT Konyv.ISBN, Konyv.cim,Szerzo.keresztnev,szerzo.vezeteknev, SUM(Resze.darab) AS osszesen_rendelt 
        FROM Konyv
        JOIN Resze ON Konyv.ISBN = Resze.ISBN
        JOIN Szerzo ON konyv.isbn = szerzo.isbn
        GROUP BY Konyv.ISBN, Konyv.cim,Szerzo.keresztnev,szerzo.vezeteknev
        ORDER BY osszesen_rendelt DESC";
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);
        $i = 0;
        while (($row = oci_fetch_array($stid, OCI_ASSOC)) && $i < 5) {
            $akt_isbn = (int)$row['ISBN'];
            print "<a href='reszletek.php?isbn={$akt_isbn}'><div class='card'>\n";
            print "<h1>" . ($row['CIM'] ?? "&nbsp;") . "</h1>\n";

            $szerzok = oci_parse($conn, "SELECT * FROM SZERZO WHERE ISBN = :value");
            oci_bind_by_name($szerzok, ':value', $akt_isbn);
            oci_execute($szerzok, OCI_DEFAULT);
            $j = 1;
            while (($szerzo = oci_fetch_array($szerzok, OCI_ASSOC))) {
                print "<h2>";
                if ($j != 1) {
                    print ", ";
                }
                print ($szerzo['VEZETEKNEV'] ?? "&nbsp;") . " " .
                    ($szerzo['KERESZTNEV'] ?? "&nbsp;") . "</h2>\n";
                $j++;
            }
            print "</div></a>\n";
            $i++;
        }

        oci_free_statement ($stid);
        oci_close($conn);
        ?>
    </section>
</main>
<?php include(__DIR__.'/components/footer.php'); ?>
</body>

</html>
