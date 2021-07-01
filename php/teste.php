<?php
$serverName = "10.105.111.2"; //serverName\instanceName
$connectionInfo = array( "Database"=>"APAC_DIVIDAS", "UID"=>"acesso.divida", "PWD"=>"D1viD@s21Db@");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}

$sql = "SELECT * FROM USUARIO";
$stmt = sqlsrv_query( $conn, $sql );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}

while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
      echo $row['NOME'].", ".$row['EMAIL']."<br />";
}

sqlsrv_free_stmt( $stmt);
?>