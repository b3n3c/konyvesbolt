<?php
$_username = "system";
$_password = "oracle";
$_connection_string = "localhost/XE";

$conn = oci_connect($_username, $_password, $_connection_string, 'AL32UTF8');
if (!$conn) {
    $e = oci_error();
    trigger_error (htmlentities ($e ['message'], ENT_QUOTES), E_USER_ERROR);
}