<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>APAC-ExtratoDividas_<?php echo $_GET['departamento']; ?></title>

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="../css/animate.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">

    <style>
        @media print {

            .no-print,
            .no-print * {
                display: none !important;
            }
        }
    </style>


</head>
<?php
require('../conexao.php');
if (isset($_GET['departamento']) && isset($_GET['where'])) { ?>

    <body class="white-bg">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="ibox-content p-xl">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <img alt="image" class="rounded-circle" src="../img/Logo_Adventista.png" width="10%" />
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-6">
                        <?
                        $sql = "SELECT * FROM COLEGIO WHERE DEPARTAMENTO = {$_GET['departamento']}";
                        $stmt = sqlsrv_query($conn, $sql);
                        $rowDados = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                        sqlsrv_free_stmt($stmt);
                        ?>
                        <h5>Extrato</h5>
                        <address>
                            Colégio: <? echo $rowDados['NOME']; ?><br>
                            Sigla: <strong><? echo $rowDados['SIGLA']; ?> </strong><br><br>
                        </address>

                    </div>

                    <div class="col-sm-6 text-right">
                        <h4>Departamento</h4>
                        <h4 class="text-success"><?php echo $rowDados['DEPARTAMENTO']; ?></h4>
                    </div>

                </div>

                <div class="table-responsive m-t">
                    <table class="table invoice-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Aluno</th>
                                <th>Qtd Parcelas</th>
                                <th>Data Inicial</th>
                                <th>Crédito</th>
                                <th>Débito</th>
                                <th>Valor Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php

                            $valorTotalEmAbertoResult = 0;
                            $valorTotalPagoResult = 0;
                            $valorTotalDividas = 0;

                            $sql = "SELECT A.NOME NOMEA, C.NOME NOMEC, * FROM DIVIDA D
                                        INNER JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO
                                        INNER JOIN RESPONSAVEL R ON R.ID_RESPONSAVEL = A.ID_RESPONSAVEL
                                        INNER JOIN COLEGIO C ON C.ID_COLEGIO = A.ID_COLEGIO {$_GET['where']} ORDER BY ID_DIVIDA ASC";
                            $stmt = sqlsrv_query($conn, $sql);
                            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                                $sqlParcela = "SELECT * FROM PARCELA P
                                            INNER JOIN DIVIDA D ON D.ID_DIVIDA = P.DIVIDA_ID_DIVIDA 
                                            WHERE P.DIVIDA_ID_DIVIDA = {$row['ID_DIVIDA']}";

                                $stmtP = sqlsrv_query($conn, $sqlParcela);
                                $valorTotalEmAberto = 0;
                                $valorTotalPago = 0;
                                while ($rowParcela = sqlsrv_fetch_array($stmtP, SQLSRV_FETCH_ASSOC)) {
                                    $valorEmAberto = "";
                                    $valorPago = "";

                                    if (
                                        $rowParcela['STATUS_PARCELA'] == 'Em Aberto' ||
                                        $rowParcela['STATUS_PARCELA'] == 'Negociado' ||
                                        $rowParcela['STATUS_PARCELA'] == 'Atrasado'
                                    ) {
                                        $valorTotalEmAberto += $rowParcela['VALOR'];
                                    }
                                    if ($rowParcela['STATUS_PARCELA'] == 'Pago') {
                                        $valorTotalPago += $rowParcela['VALOR'];
                                    }
                                }

                                $valorTotalEmAbertoResult += $valorTotalEmAberto;
                                $valorTotalPagoResult += $valorTotalPago;
                                $valorTotalDividas += $row['TOTAL'];

                                echo "<tr>
                                                        <td>" . $row['ID_DIVIDA'] . "</td>
                                                        <td>" . $row['RA'] . "-" . $row['NOMEA'] . "</td>
                                                        <td>" . $row['QTD_PARCELAS'] . "</td>
                                                        <td>" . $row['DATA_INICIAL']->format('d/m/Y') . "</td>
                                                        <td>" . number_format($valorTotalPago, 2, ",", ".") . "</td>
                                                        <td>" . number_format($valorTotalEmAberto, 2, ",", ".") . "</td>
                                                        <td>" . number_format($row['TOTAL'], 2, ",", ".") . "</td>
                                                        <td>" . $row['STATUS_DIV'] . "</td>
                                            </tr>";
                            }

                            ?>
                        </tbody>
                    </table>
                </div><!-- /table-responsive -->

                <div class="col-sm-12 text-right">
                    <br></br>
                    <address>
                        <h3><b>Total Dívidas:</b>
                                        <?php echo "R$ " . number_format($valorTotalDividas, 2, ",", "."); ?><br><br>

                                    <b>Total créditos:</b>
                                        <span style="color:red;"><?php echo "R$ " . number_format($valorTotalPagoResult, 2, ",", "."); ?></span><br><br>
                                    
                                    <b>Total débitos:</b>
                                        <?php echo "R$ " . number_format($valorTotalEmAbertoResult, 2, ",", "."); ?><br>
                                    <p style="font-size: 10px;">(A receber)</p><br>
                                    </h3>
          
                    </address>
                </div>

                <div class="col-sm-12 text-center">
                    <br></br>
                    <address>
                        <strong>Associação Paulista Central - Educação</strong><br>
                        Rua Julio Ribeiro, 188 • Bonfim, <br>
                        Campinas/SP - 13070-712 <br>
                        <abbr title="Phone"></abbr> (19) 2117.2900
                    </address>

                </div>

            </div>
        </div>

        </div>
    <? } ?>

    <!-- Mainly scripts -->
    <script src="../js/jquery-3.1.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/plugins/metisMenu/jquery.metisMenu.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="../js/inspinia.js"></script>

    <script type="text/javascript">
        setTimeout(() => {
            window.print();
        }, 2000);
    </script>

    </body>

</html>