<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>APAC-ExtratoDivida_<?php echo $_GET['idDivida']; ?></title>

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
if (isset($_GET['idDivida'])) { ?>

    <body class="white-bg">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="ibox-content p-xl">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <img alt="image" class="rounded-circle" src="../img/Logo_Adventista.png" width="10%" />
                    </div>
                </div>
                <br></br>
                <div class="row">
                    <div class="col-sm-6">
                        <?
                        $sql = "SELECT A.NOME NOMEA, R.NOME NOMER, A.RA, D.TOTAL, D.DATA_INICIAL FROM DIVIDA D 
                            LEFT JOIN ALUNO A ON D.ALUNO_ID_ALUNO = A.ID_ALUNO
                            LEFT JOIN RESPONSAVEL R ON A.ID_RESPONSAVEL = R.ID_RESPONSAVEL
                            WHERE D.ID_DIVIDA = " . $_GET['idDivida'];
                        $stmt = sqlsrv_query($conn, $sql);
                        if ($stmt === false) {
                            die(print_r(sqlsrv_errors(), true));
                        }
                        $valorTotalEmAberto = 0;
                        $valorTotalPago = 0;
                        $rowDados = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                        sqlsrv_free_stmt($stmt);
                        ?>

                        <h5>Extrato</h5>
                        <address>
                            RA: <? echo $rowDados['RA']; ?><br>
                            Nome: <strong><? echo $rowDados['NOMEA']; ?> </strong><br>
                            <? echo isset($rowDados['NOMER']) ? "Responsavel: " . $rowDados['NOMER'] : ""; ?>
                        </address>

                    </div>

                    <div class="col-sm-6 text-right">
                        <h4>Divida Nº</h4>
                        <h4 class="text-success"><?php echo $_GET['idDivida']; ?></h4>

                        <p>
                            <span><strong>Início:</strong> <? echo $rowDados['DATA_INICIAL']->format('d/m/Y'); ?></span><br />
                        </p>
                    </div>

                </div>

                <div class="table-responsive m-t">
                    <table class="table invoice-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Parcela</th>
                                <th>Data Vencimento</th>
                                <th>Forma de Pagamento</th>
                                <th>Debito</th>
                                <th>Crédito</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php

                            $whereClause = '';
                            if (isset($_GET['idDivida'])) {
                                $idDivida = $_GET['idDivida'];
                                $whereClause = "WHERE P.DIVIDA_ID_DIVIDA = " . $idDivida;
                            }
                            $sql = "SELECT * FROM PARCELA P
                            INNER JOIN DIVIDA D ON D.ID_DIVIDA = P.DIVIDA_ID_DIVIDA " . $whereClause;
                            $stmt = sqlsrv_query($conn, $sql);
                            if ($stmt === false) {
                                die(print_r(sqlsrv_errors(), true));
                            }
                            $valorTotalEmAberto = 0;
                            $valorTotalPago = 0;
                            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                $valorEmAberto = "";
                                $valorPago = "";

                                if (
                                    $row['STATUS_PARCELA'] == 'Em Aberto' ||
                                    $row['STATUS_PARCELA'] == 'Negociado' ||
                                    $row['STATUS_PARCELA'] == 'Atrasado'
                                ) {
                                    $valorEmAberto = "R$ " . number_format($row['VALOR'], 2, ",", ".");
                                    $valorTotalEmAberto += $row['VALOR'];
                                }
                                if ($row['STATUS_PARCELA'] == 'Pago') {
                                    $valorPago = "R$ " . number_format($row['VALOR'], 2, ",", ".");
                                    $valorTotalPago += $row['VALOR'];
                                }
                                if (empty($row['STATUS_PARCELA'])) {
                                    $button = "class='btn btn-secondary btn-rounded'";
                                    $row['STATUS_PARCELA'] = "Sem status";
                                }
                                echo "<tr>
                                            <td>" . $row['ID_PARCELA'] . "</td>
                                            <td>" . $row['NUMERO'] . "</td>
                                            <td>" . $row['DATA_VENCIMENTO']->format('d/m/Y') . "</td>
                                            <td>" . $row['FORMA_PAGAMENTO'] . "</td>
                                            <td>" . $valorEmAberto . "</td>
                                            <td style='color:red;'>" . $valorPago . "</td>
                                </tr>";
                            }

                            sqlsrv_free_stmt($stmt);
                            ?>
                        </tbody>
                    </table>
                </div><!-- /table-responsive -->

                <div class="col-sm-12 text-right">
                    <br></br>
                    <address>
                        <h3>
                        <b>Total Dívidas:</b>
                        <?php $valorTotalEmAberto = $rowDados['TOTAL'] - $valorTotalPago;
                            echo "R$ " . number_format($valorTotalEmAberto, 2, ",", "."); ?><br><br>

                                    <b>Total créditos:</b>
                                    <span style="color:red;"><?php echo "R$ " . number_format($valorTotalPago, 2, ",", "."); ?></span><br><br>
                                    
                                    <b>Total débitos:</b>
                                    <?php echo "R$ " . number_format($rowDados['TOTAL'], 2, ",", "."); ?><br>
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