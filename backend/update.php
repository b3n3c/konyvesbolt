<?php

// Check if form is submitted


    // Connect to database
    $conn = oci_connect('system', 'oracle', 'localhost/XE');



    // Get the values submitted in the form
    $rendeles_id = $_POST['rendeles_id'];
    $teljesitve = $_POST['teljesitve'];
    var_dump($rendeles_id,$teljesitve);

    $query = oci_parse($conn, "UPDATE rendeles SET teljesitve = :allapot WHERE rendeles_id =  :rendid ");
    oci_bind_by_name($query,':allapot',$teljesitve);
    oci_bind_by_name($query,':rendid',$rendeles_id);
    $result = oci_execute($query, OCI_DEFAULT);
    if ($result) {
        oci_commit($conn);
        echo "Sikeresen frissitve !";
        header('Location: ../rendelesek.php');
    }
    else{
        echo "Hiba !";
        
    }
    


