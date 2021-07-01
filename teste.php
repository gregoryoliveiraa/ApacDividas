<?php 

require('conexao.php');

        $sql = "select * from v_aasi_acordo_confissao  where Historico LIKE '%83219%' ";
                                            $stmt = sqlsrv_query($conn, $sql);
                                            if ($stmt === false) {
                                                die(print_r(sqlsrv_errors(), true));
                                            }
                                            $cont = 0;

                                            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                var_export($row);
                                                $cont++;
                                                if($cont > 50)die();
                                            }
                                            

                                            ?>