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
    $konyv = oci_parse ($conn, 'SELECT * FROM KONYV WHERE ISBN = :value');
    oci_bind_by_name($konyv, ':value', $isbn);
    oci_execute($konyv, OCI_DEFAULT);
    $res = oci_fetch_assoc($konyv);
    oci_free_statement($konyv);
    oci_close($conn);
    return $res;
}
