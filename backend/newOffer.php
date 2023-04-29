<?php
include("dbhelper.php");
session_start();
$conn = getDb();
$now = date("d-M-y");
$userId = $_SESSION["id"];
$ar = (int)$_POST['ar'];

$sql = 'BEGIN :r := UjRendeles(:param1, :param2, :param3); END;';
$stmt_id = oci_parse($conn, $sql);
oci_bind_by_name($stmt_id, ':param1', $userId);
oci_bind_by_name($stmt_id, ':param2', $now);
oci_bind_by_name($stmt_id, ':param3', $ar);
oci_bind_by_name($stmt_id, ':r', $rendeles_id, 200);
oci_execute($stmt_id);
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
header("Location: ../kosar.php?success=1#success");
exit();