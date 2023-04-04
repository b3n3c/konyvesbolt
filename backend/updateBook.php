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


$query = oci_parse($conn, "UPDATE KONYV SET CIM = '$cim', MUFAJ = '$mufaj',
                 TIPUS = '$tipus', PUBLIKACIO_EVE = $ev,
                 AR = $ar, DARABSZAM = $db, KIADO_ID = $kiadoid WHERE ISBN = '$ISBN'");

$result = oci_execute($query, OCI_DEFAULT);
if ($result) {
    oci_commit($conn);
    header("Location: ../reszletek.php?isbn=$ISBN"."&success1=$result#success1");
    exit();
}
else{
    echo "Hiba !";
    exit();
}
?>