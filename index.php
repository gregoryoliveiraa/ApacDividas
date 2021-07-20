<!DOCTYPE html>
<html>

<head>

    <?php
    
    include("loadingPage.php");
    include("valida.php");
    
    $retornoProtegepagina = protegePagina();
    if(isset($_SESSION['usuarioAcesso']) && isset($_SESSION['usuarioIdColegio'])){
        if($_SESSION['usuarioAcesso'] == 1 && $_SESSION['usuarioIdColegio'] > 0){
            echo "<meta http-equiv='refresh' content='0;url=dashpagamentos.php'>";
            die();
        }
    }

    if($_SESSION['usuarioAcesso'] == 5){
        echo "<meta http-equiv='refresh' content='0;url=dashpagamentos.php'>"; 
        die();
    }
    
    require('menu.php');
    require('conexao.php');

   

    if ($retornoProtegepagina) {

        if (isset($_GET['btnFiltrar'])) {

            $query = "";
            $cont = 0;

            if (!empty($_GET['dataInicio']) && !empty($_GET['dataFim'])) {
                $data = explode("-", $_GET['dataInicio']);
                $ano = $data[0];
                $mes = str_replace("0", "", $data[1]);
                $data = explode("-", $_GET['dataFim']);
                $ano2 = $data[0];
                $mes2 = str_replace("0", "", $data[1]);
                $query .= " (Ano BETWEEN $ano AND $ano2) AND (Mes BETWEEN $mes AND $mes2) ";
            } else if (!empty($_GET['dataInicio'])) {
                $data = explode("-", $_GET['dataInicio']);
                $ano = $data[0];
                $mes = str_replace("0", "", $data[1]);
                $query .= " Ano >= $ano AND Mes >= $mes";
            } else {
                if (empty($_GET['Ano'])) {
                    $ano = date('Y');
                    $query .= "Ano = $ano";
                } else {
                    $ano = $_GET['Ano'];
                    $query .= "Ano = $ano";
                }
            }
        } else {
            if (empty($_GET['Ano'])) {
                $ano = date('Y');
                $query = "Ano = $ano ";
            } else {
                $ano = $_GET['Ano'];
                $query = "Ano = $ano ";
            }
        }

    ?>

        </div>


        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-4">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <span class="label label-success float-right">100%</span>
                            <h5>Dívidas</h5>
                        </div>
                        <a href="dividas.php" style="text-decoration:none; color: inherit;">
                            <div class="ibox-content">
                                <?php
                                $sql = "SELECT ABS(SUM(TOTAL)) AS Valor FROM DIVIDA";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowDividas = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "R$ <h1 class='no-margins'>" . number_format($rowDividas['Valor'], 2, ",", ".") . "</h1>";
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(ID_DIVIDA) AS Total FROM DIVIDA ";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowDividaCount = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "<br>Total: " . $rowDividaCount['Total'];
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(ID_PARCELA) AS Total FROM PARCELA";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowParcelaCount = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "<br>Parcelas: " . $rowParcelaCount['Total'];
                                sqlsrv_free_stmt($stmt);
                                ?>
                            </div>
                        </a>
                    </div>
                </div>

                <?php
                $sql = "SELECT ABS(SUM(VALOR)) AS Valor FROM PARCELA WHERE STATUS_PARCELA = 'Pago' ";
                $stmt = sqlsrv_query($conn, $sql);
                if ($stmt === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
                $rowAReceber = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                $total = ($rowAReceber['Valor'] * 100) / $rowDividas['Valor'];
                ?>
                <div class="col-lg-4">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <span class="label label-info float-right"><? echo intval($total); ?>%</span>
                            <h5>Pago</h5>
                        </div>
                        <a href="dividas.php?status='Pago'" style="text-decoration:none; color: inherit;">
                            <div class="ibox-content">
                                <?
                                echo "R$ <h1 class='no-margins'>" . number_format($rowAReceber['Valor'], 2, ",", ".") . "</h1>";
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(ID_DIVIDA) AS Total FROM DIVIDA WHERE STATUS_DIV = 'Pago' ";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowDividaCount = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "<br>Dívidas: " . $rowDividaCount['Total'];
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(ID_PARCELA) AS Total FROM PARCELA WHERE STATUS_PARCELA = 'Pago' ";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowParcelaCount = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "<br>Parcelas: " . $rowParcelaCount['Total'];
                                sqlsrv_free_stmt($stmt);
                                ?>
                            </div>
                        </a>
                    </div>
                </div>

                <?php
                $sql = "SELECT ABS(SUM(VALOR)) AS Valor FROM PARCELA WHERE STATUS_PARCELA = 'Em Aberto' ";
                $stmt = sqlsrv_query($conn, $sql);
                if ($stmt === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
                $rowAReceber = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                $total = ($rowAReceber['Valor'] * 100) / $rowDividas['Valor'];
                ?>
                <div class="col-lg-4">

                    <div class="ibox ">
                        <div class="ibox-title">
                            <span class="label label-danger float-right"><? echo intval($total); ?>%</span>
                            <h5>À receber</h5>
                        </div>
                        <a href="dividas.php?status='emAberto'" style="text-decoration:none; color: inherit;">
                            <div class="ibox-content">
                                <?
                                echo "R$ <h1 class='no-margins'>" . number_format($rowAReceber['Valor'], 2, ",", ".") . "</h1>";
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(ID_DIVIDA) AS Total FROM DIVIDA WHERE STATUS_DIV = 'Em Aberto' ";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowDividaCount = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "<br>Dívidas: " . $rowDividaCount['Total'];
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(ID_PARCELA) AS Total FROM PARCELA WHERE STATUS_PARCELA = 'Em Aberto' ";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowParcelaCount = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "<br>Parcelas: " . $rowParcelaCount['Total'];
                                sqlsrv_free_stmt($stmt);
                                ?>
                            </div>
                        </a>
                    </div>

                </div>

        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5><? echo $ano; ?> - Comparação de pagamento
                            <small>com dividas criadas </small>
                        </h5>
                    </div>
                    <div class="ibox-content">
                        <div>
                            <canvas id="lineChart" height="140"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        </div>


        <?php require("footer.php"); ?>
        </div>



        </div>

        <!-- Mainly scripts -->
        <script src="js/jquery-3.1.1.min.js"></script>
        <script src="js/popper.min.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
        <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

        <!-- ChartJS-->
        <script src="js/plugins/chartJs/Chart.min.js"></script>

        <!-- Flot -->
        <script src="js/plugins/flot/jquery.flot.js"></script>
        <script src="js/plugins/flot/jquery.flot.tooltip.min.js"></script>
        <script src="js/plugins/flot/jquery.flot.spline.js"></script>
        <script src="js/plugins/flot/jquery.flot.resize.js"></script>
        <script src="js/plugins/flot/jquery.flot.pie.js"></script>
        <script src="js/plugins/flot/jquery.flot.symbol.js"></script>
        <script src="js/plugins/flot/jquery.flot.time.js"></script>

        <!-- Peity -->
        <script src="js/plugins/peity/jquery.peity.min.js"></script>
        <script src="js/demo/peity-demo.js"></script>

        <!-- Custom and plugin javascript -->
        <script src="js/inspinia.js"></script>
        <script src="js/plugins/pace/pace.min.js"></script>

        <!-- jQuery UI -->
        <script src="js/plugins/jquery-ui/jquery-ui.min.js"></script>

        <!-- Jvectormap -->
        <script src="js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
        <script src="js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

        <!-- EayPIE -->
        <script src="js/plugins/easypiechart/jquery.easypiechart.js"></script>

        <!-- Sparkline -->
        <script src="js/plugins/sparkline/jquery.sparkline.min.js"></script>

        <!-- Sparkline demo data  -->
        <script src="js/demo/sparkline-demo.js"></script>

        <script>
            $(function() {

                <?php
                $rows = [];
                $dataAtual = date('Y-m-d');
                $mes = '';
                $data = '';
                $sql = "SELECT ABS(SUM(VALOR)) AS Valor, DATA_VENCIMENTO FROM PARCELA 
                                    WHERE DATA_VENCIMENTO >= '2021-01-01' AND DATA_VENCIMENTO <= '" . $dataAtual . "' 
                                    AND STATUS_PARCELA = 'Pago' GROUP BY DATA_VENCIMENTO";
                $stmt = sqlsrv_query($conn, $sql);
                if ($stmt === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $rows[] = $row;
                }
                foreach ($rows as $row) {
                    switch (intval($row['DATA_VENCIMENTO']->format('m'))) {
                        case 1:
                            $mesTexto = "Janeiro";
                            break;
                        case 2:
                            $mesTexto = "Fevereiro";
                            break;
                        case 3:
                            $mesTexto = "Março";
                            break;
                        case 4:
                            $mesTexto = "Abril";
                            break;
                        case 5:
                            $mesTexto = "Maio";
                            break;
                        case 6:
                            $mesTexto = "Junho";
                            break;
                        case 7:
                            $mesTexto = "Julho";
                            break;
                        case 8:
                            $mesTexto = "Agosto";
                            break;
                        case 9:
                            $mesTexto = "Setembro";
                            break;
                        case 10:
                            $mesTexto = "Outubro";
                            break;
                        case 11:
                            $mesTexto = "Novembro";
                            break;
                        case 12:
                            $mesTexto = "Dezembro";
                            break;
                    }
                    $mes .= '"' . $mesTexto . '", ';
                    $data .= $row['Valor'] . ", ";
                }
                sqlsrv_free_stmt($stmt);
                ?>
                var lineData = {
                    labels: [<?php echo $mes; ?>],
                    datasets: [

                        {
                            label: "Confissão de dívida cedida",
                            backgroundColor: 'rgba(0,89,167,0.5)',
                            borderColor: "rgba(0,59,111,0.7)",
                            pointBackgroundColor: "rgba(26,179,148,1)",
                            pointBorderColor: "#fff",
                            data: [<?php echo $data; ?>]
                        }, {
                            label: "Pagamentos realizados",
                            backgroundColor: 'rgba(220, 220, 220, 0.5)',
                            pointBorderColor: "#fff",
                            data: [<?php
                                    
                                    $rows = [];
                                    $mes = '';
                                    $data = '';
                                    $sql = "SELECT ABS(SUM(TOTAL)) AS Valor, DATA_INICIAL FROM DIVIDA WHERE DATA_INICIAL >= '01-01-2021' GROUP BY DATA_INICIAL";
                                    $stmt = sqlsrv_query($conn, $sql);
                                    if ($stmt === false) {
                                        die(print_r(sqlsrv_errors(), true));
                                    }
                                    while ($rowsss = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                        $rowss[] = $rowsss;
                                    }
                                    foreach ($rowss as $row) {
                                        $data .= $row['Valor'] . ", ";
                                    }
                                    sqlsrv_free_stmt($stmt);
                                    echo $data;
                                    ?>]
                        }
                    ]
                };

                var lineOptions = {
                    responsive: true
                };


                var ctx = document.getElementById("lineChart").getContext("2d");
                new Chart(ctx, {
                    type: 'line',
                    data: lineData,
                    options: lineOptions
                });


            });
        </script>
        </body>
    <? } ?>

</html>