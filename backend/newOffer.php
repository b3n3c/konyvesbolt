<?php

$conn = oci_connect('system', 'oracle', 'localhost/XE');

$queryCount = oci_parse($conn, "SELECT COUNT(*) FROM RENDELES");
oci_execute($queryCount);
$row = oci_fetch_array($queryCount, OCI_RETURN_NULLS+OCI_ASSOC);
$count = $row['COUNT(*)'];

$rendeles_id = $count+2;
$now = date("d-M-y");
$userId = $_POST['userId'];
$ar = (int)$_POST['ar'];

$query = oci_parse($conn, "INSERT INTO RENDELES (RENDELES_ID,TELJESITVE,AR,DATUM,FELHASZNALO_ID)
VALUES ('$rendeles_id', 'N', '$ar', '$now', $userId)");

$result = oci_execute($query, OCI_DEFAULT);
if ($result) {
    oci_commit($conn);
    echo "Ön sikeresen vásárolt!!";
    exit();
}
else{
    echo "Hiba !";
    exit();
}
?>