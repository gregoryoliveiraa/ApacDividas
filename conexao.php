<?php
    $serverName = "10.105.111.2"; 
    $connectionInfo = array( "Database"=>"APAC_DIVIDAS", "UID"=>"acesso.divida", "PWD"=>"*******");
    $conn = sqlsrv_connect( $serverName, $connectionInfo);
?>
