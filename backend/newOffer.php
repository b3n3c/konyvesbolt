<?php
include("dbhelper.php");
session_start();
$conn = getDb();

$queryCount = oci_parse($conn, "SELECT COUNT(*) FROM RENDELES");
oci_execute($queryCount);
$row = oci_fetch_array($queryCount, OCI_RETURN_NULLS+OCI_ASSOC);
$count = $row['COUNT(*)'];
$rendeles_id = $count+1;
$now = date("d-M-y");
$userId = $_SESSION["id"];
$ar = (int)$_POST['ar'];

$query = oci_parse($conn, "INSERT INTO RENDELES (RENDELES_ID,TELJESITVE,AR,DATUM,FELHASZNALO_ID)
VALUES ('$rendeles_id', 'N', '$ar', '$now', $userId)");
$result = oci_execute($query, OCI_DEFAULT);
if ($result) {
    oci_commit($conn);
    foreach (array_keys($_SESSION["cart"]) as $isbn){
        $resze_query = oci_parse($conn, "INSERT INTO RESZE(rendeles_id, isbn, darab) VALUES (:rendeles, :isbn, :darab)");
        oci_bind_by_name($resze_query, ":rendeles", $rendeles_id);
        oci_bind_by_name($resze_query, ":isbn", $isbn);
        oci_bind_by_name($resze_query, ":darab", $_SESSION["cart"][$isbn]);

        if(oci_execute($resze_query, OCI_DEFAULT)){
            oci_commit($conn);
            oci_free_statement($resze_query);
        }else{
            print "Valami hiba történt";
            exit();
        }
    }

    unset($_SESSION["cart"]);
    header("Location: ../kosar.php?success=$result#success");
    exit();
} else{
    echo "Hiba !";
    exit();
}