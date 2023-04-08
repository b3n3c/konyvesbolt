<?php
include("dbhelper.php");
$conn = getDb();

$cim = $_POST['cim'];
$ISBN = $_POST['ISBN'];
$mufaj = $_POST['mufaj'];
$tipus = $_POST['tipus'];
$ev = (int)$_POST['ev'];
$ar = (int)$_POST['ar'];
$db = (int)$_POST['db'];
$kiadoid = 0;

if(isset($_POST["kiado"])){
    $id_stmt = oci_parse($conn, "SELECT MAX(kiado_id) AS KID FROM KIADO");
    oci_execute($id_stmt);
    oci_fetch($id_stmt);
    $id = ((int)oci_result($id_stmt, "KID")) + 1;
    $kiado_query = oci_parse($conn, "INSERT INTO Kiado (kiado_id, orszag, nev) VALUES (:id, :orszag, :nev)");
    oci_bind_by_name($kiado_query, ":id", $id);
    oci_bind_by_name($kiado_query, ":orszag", $_POST["uj-kiado-orszag"]);
    oci_bind_by_name($kiado_query, ":nev", $_POST["uj-kiado"]);
    if (oci_execute($kiado_query)){
        oci_commit($conn);
        oci_free_statement($kiado_query);
        $kiadoid = $id;
    }else{
        echo "Hiba a kiadó beszúrásakor";
        exit();
    }
}else{
    $kiadoid = (int)$_POST['kiadoid'];
}


$query = oci_parse($conn, "INSERT INTO KONYV (CIM, ISBN, MUFAJ, TIPUS, PUBLIKACIO_EVE, AR, DARABSZAM, KIADO_ID)
VALUES ('$cim', '$ISBN', '$mufaj', '$tipus', $ev, $ar, $db, $kiadoid)");

$result = oci_execute($query, OCI_DEFAULT);
if ($result) {
    oci_commit($conn);
    $i = 1;
    while(isset($_POST["vez-nev-{$i}"])){
        $szerzo_query = oci_parse($conn, "INSERT INTO Szerzo (isbn, keresztnev, vezeteknev) VALUES
        (:isbn, :kernev, :vnev)");
        oci_bind_by_name($szerzo_query, ":isbn", $ISBN);
        oci_bind_by_name($szerzo_query, "kernev", $_POST["ker-nev-{$i}"]);
        oci_bind_by_name($szerzo_query, "vnev", $_POST["vez-nev-{$i}"]);
        if (oci_execute($szerzo_query)){
            oci_commit($conn);
            oci_free_statement($szerzo_query);
            $i++;
        }else{
            echo "Hiba!";
            break;
        }
    }
    header("Location: ../ujKonyv.php?success=$result#success");
    exit();
}
else{
    echo "Hiba !";
    exit();
}