<?php
function getDb(){
    $_username = "system";
    $_password = "oracle";
    $_connection_string = "localhost/XE";

    $conn = oci_connect($_username, $_password, $_connection_string, 'AL32UTF8');
    if (!$conn) {
        $e = oci_error();
        trigger_error (htmlentities ($e ['message'], ENT_QUOTES), E_USER_ERROR);
    }
    return $conn;
}

function getBookByISBN($isbn){
    $conn = getDb();
    $konyv = oci_parse ($conn, 'SELECT KONYV.*, KIADO.nev AS kiado FROM KONYV, KIADO WHERE konyv.kiado_id = kiado.kiado_id AND KONYV.ISBN = :value');
    oci_bind_by_name($konyv, ':value', $isbn);
    oci_execute($konyv, OCI_DEFAULT);
    $res = oci_fetch_assoc($konyv);
    oci_free_statement($konyv);
    oci_close($conn);
    return $res;
}

function getSzerzokString($isbn){
    $conn = getDb();
    $szerzok = oci_parse($conn, "SELECT * FROM SZERZO WHERE ISBN = :value");
    oci_bind_by_name($szerzok, ':value', $isbn);
    oci_execute($szerzok, OCI_DEFAULT);
    $i = 1;
    $k_szerzok = "";
    while (oci_fetch($szerzok)){
        if ($i != 1)
            $k_szerzok .= ", ";
        $k_szerzok .= oci_result($szerzok, 'VEZETEKNEV'). " " . oci_result($szerzok, 'KERESZTNEV');
        $i++;
    }
    oci_free_statement($szerzok);
    oci_close($conn);
    return $k_szerzok;
}
