<?php

$conn = oci_connect('system', 'oracle', 'localhost/XE');

$cim = $_POST['cim'];
$ISBN = $_POST['ISBN'];
$mufaj = $_POST['mufaj'];
$tipus = $_POST['tipus'];
$ev = (int)$_POST['ev'];
$ar = (int)$_POST['ar'];
$db = (int)$_POST['db'];
$kiadoid = (int)$_POST['kiadoid'];


$query = oci_parse($conn, "INSERT INTO KONYV (CIM, ISBN, MUFAJ, TIPUS, PUBLIKACIO_EVE, AR, DARABSZAM, KIADO_ID)
VALUES ('$cim', '$ISBN', '$mufaj', '$tipus', $ev, $ar, $db, $kiadoid)");

$result = oci_execute($query, OCI_DEFAULT);
if ($result) {
    oci_commit($conn);
    header("Location: ../ujKonyv.php?success=$result#success");
    exit();
}
else{
    echo "Hiba !";
    exit();
}
?>