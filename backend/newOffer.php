<?php

$conn = oci_connect('system', 'oracle', 'localhost/XE');

$queryCount = oci_parse($conn, "SELECT COUNT(*) FROM RENDELES");
oci_execute($queryCount);
$row = oci_fetch_array($queryCount, OCI_RETURN_NULLS+OCI_ASSOC);
$count = $row['COUNT(*)'];
$ISBN=$_POST['isbn'];
$rendeles_id = $count+2;
$now = date("d-M-y");
$userId = $_POST['userId'];
$ar = (int)$_POST['ar'];

$query = oci_parse($conn, "INSERT INTO RENDELES (RENDELES_ID,TELJESITVE,AR,DATUM,FELHASZNALO_ID)
VALUES ('$rendeles_id', 'N', '$ar', '$now', $userId)");

$result = oci_execute($query, OCI_DEFAULT);
if ($result) {
        oci_commit($conn);
        header("Location: ../reszletek.php?isbn=$ISBN"."&success=$result#success");
        exit();
}
else{
    echo "Hiba !";
    exit();
}
?>