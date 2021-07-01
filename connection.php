<?php


$serverName = "10.105.111.2";
$connectionOptions = array(
"Database" => "APAC-DIVIDAS",
"Uid" => "dbo",
"PWD" => "D1viD@s21Db@"
);
//Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);
if( $conn === false ) {
die( FormatErrors( sqlsrv_errors()));
}
//Select Query
$tsql= "SELECT @@Version as SQL_VERSION";
//Executes the query
$getResults= sqlsrv_query($conn, $tsql);
//Error handling
if ($getResults == FALSE)
die(FormatErrors(sqlsrv_errors()));
?>
<h1> Results : </h1>
<?php
while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
echo ($row['SQL_VERSION']);
echo ("<br/>");
}
sqlsrv_free_stmt($getResults);
function FormatErrors( $errors )
{
/* Display errors. */
echo "Error information: <br/>";
foreach ( $errors as $error )
{
echo "SQLSTATE: ".$error['SQLSTATE']."<br/>";
echo "Code: ".$error['code']."<br/>";
echo "Message: ".$error['message']."<br/>";
}
}
?>