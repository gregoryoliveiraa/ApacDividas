<!DOCTYPE html>
<html>

<head>

    <?php
    include("valida.php");
    $retornoProtegepagina = protegePagina();
    require('menu.php');
    require('conexao.php');

    if ($retornoProtegepagina) {

        if (isset($_GET['Ano']) && $_GET['Ano'] != "Selecione o ano") {
            $ano = $_GET['Ano'];
            if(isset($_GET['Mes']) && $_GET['Mes'] != "Selecione o mes"){
                $dataInicio = $ano."-".$_GET['Mes']."-01";
            }else{
                $dataInicio = $ano."-01-01";
            }
            if(date('Y') == $_GET['Ano'] && $_GET['Mes'] != "Selecione o mes"){
                $dataFim = $ano."-".date('m')."-28";
            }else{
                if(isset($_GET['Mes']) && $_GET['Mes'] != "Selecione o mes"){
                    $dataFim = $ano."-".$_GET['Mes']."-28";
                }else{
                    $dataFim = $ano."-12-28";
                }
            }
        } else {
            $ano = date('Y');
            $query = "Ano = $ano ";
            $dataInicio = $ano."-01-01";
            if(isset($_GET['Mes']) && $_GET['Mes'] != "Selecione o mes"){
                $dataFim = $ano."-".$_GET['Mes']."-28";
            }else{
                $dataFim = $ano."-".date('m')."-28";
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
                                $sql = "SELECT ABS(SUM(TOTAL)) AS Valor FROM DIVIDA WHERE DATA_INICIAL >= '$dataInicio' AND DATA_INICIAL <= '$dataFim'";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowDividas = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "R$ <h1 class='no-margins'>" . number_format($rowDividas['Valor'], 2, ",", ".") . "</h1>";
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(ID_DIVIDA) AS Total FROM DIVIDA WHERE DATA_INICIAL >= '$dataInicio' AND DATA_INICIAL <= '$dataFim'";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowDividaCount = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "<br>Total: " . $rowDividaCount['Total'];
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(ID_PARCELA) AS Total FROM PARCELA WHERE DATA_VENCIMENTO >= '$dataInicio' AND DATA_VENCIMENTO <= '$dataFim'";
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
                $sql = "SELECT ABS(SUM(VALOR)) AS Valor FROM PARCELA WHERE STATUS_PARCELA = 'Pago' AND DATA_VENCIMENTO >= '$dataInicio' AND DATA_VENCIMENTO <= '$dataFim' ";
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

                                $sql = "SELECT COUNT(ID_DIVIDA) AS Total FROM DIVIDA WHERE STATUS_DIV = 'Pago' AND DATA_INICIAL >= '$dataInicio' AND DATA_INICIAL <= '$dataFim' ";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowDividaCount = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "<br>Dívidas: " . $rowDividaCount['Total'];
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(ID_PARCELA) AS Total FROM PARCELA WHERE STATUS_PARCELA = 'Pago'  AND DATA_VENCIMENTO >= '$dataInicio' AND DATA_VENCIMENTO <= '$dataFim' ";
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
                $sql = "SELECT ABS(SUM(VALOR)) AS Valor FROM PARCELA WHERE STATUS_PARCELA = 'Em Aberto' AND DATA_VENCIMENTO >= '$dataInicio' AND DATA_VENCIMENTO <= '$dataFim' ";
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

                                $sql = "SELECT COUNT(ID_DIVIDA) AS Total FROM DIVIDA WHERE STATUS_DIV = 'Em Aberto' AND DATA_INICIAL >= '$dataInicio' AND DATA_INICIAL <= '$dataFim'";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowDividaCount = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "<br>Dívidas: " . $rowDividaCount['Total'];
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(ID_PARCELA) AS Total FROM PARCELA WHERE STATUS_PARCELA = 'Em Aberto'  AND DATA_VENCIMENTO >= '$dataInicio' AND DATA_VENCIMENTO <= '$dataFim' ";
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
                        <h5><? echo $ano; ?> - Total por colégio</h5>
                    </div>
                    <div class="ibox-content">
                        <div>
                            <canvas id="radarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Pesquisar
                            <small>e vizualizar nos gráficos </small>
                        </h5>
                    </div>
                    <div class="ibox-content">
                        <form method="get" action="">
                            <div class="form-group  row">
                                <div class="col-sm-4">
                                    <select class="form-control m-b" name="Ano">
                                        <option>Selecione o ano</option>
                                        <?php
                                        $date = date('Y');
                                        while ($date > 2016) {
                                            echo  "<option value='$date'>$date</option>";
                                            $date--;
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <select class="form-control m-b" name="Mes">
                                        <option>Selecione o mes</option>
                                        <option value="01">Janeiro</option>
                                        <option value="02">Fevereiro</option>
                                        <option value="03">Marco</option>
                                        <option value="04">Abril</option>
                                        <option value="05">Maio</option>
                                        <option value="06">Junho</option>
                                        <option value="07">Julho</option>
                                        <option value="08">Agosto</option>
                                        <option value="09">Setembro</option>
                                        <option value="10">Outubro</option>
                                        <option value="11">Novembro</option>
                                        <option value="12">Dezembro</option>
                                    </select>
                                </div>
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-success btn-lg" type="submit">  <i class="fa fa-bar-chart"></i> Vizualizar dados nos graficos</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
            
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5><? echo $ano; ?> - <small>Total de dividas por colegio</small></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="flot-chart">
                            <div class="flot-chart-content" id="flot-bar-chart"></div>
                        </div>
                        <div id="legendContainer"></div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5><? echo $ano; ?> - <small>Total de parcelas pagas por colegio</small></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="flot-chart">
                            <div class="flot-chart-content" id="flot-bar-parcelas-chart"></div>
                        </div>
                        <div id="legendContainer-parcelas"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5><? echo $ano; ?> - <small>Média de dividas requisitadas por colegio</small></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="flot-chart">
                            <div class="flot-chart-content" id="flot-bar-divida-media-chart"></div>
                        </div>
                        <div id="legendContainer-divida-media"></div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5><? echo $ano; ?> - <small>Média de parcelas pagas por colegio</small></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="flot-chart">
                            <div class="flot-chart-content" id="flot-bar-media-chart"></div>
                        </div>
                        <div id="legendContainer-media"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5><? echo $ano; ?> - <small>Quantidade de dividas por colegio</small></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="flot-chart">
                            <div class="flot-chart-content" id="flot-bar-quantidade"></div>
                        </div>
                        <div id="legendContainer-quantidade"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5><? echo $ano; ?> - <small>Quantidade de parcelas pagas por colegio</small></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="flot-chart">
                            <div class="flot-chart-content" id="flot-bar-parcelas-quantidade"></div>
                        </div>
                        <div id="legendContainer-parcelas-quantidade"></div>
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

        <!-- Custom AND plugin javascript -->
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
                $nome = '';
                $data = '';
                $sql = "SELECT ABS(SUM(P.VALOR)) AS valor, C.NOME AS NOMEC FROM PARCELA P 
                    LEFT JOIN DIVIDA D ON D.ID_DIVIDA = P.DIVIDA_ID_DIVIDA
                    LEFT JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO 
                    LEFT JOIN COLEGIO C ON C.ID_COLEGIO = A.ID_COLEGIO 
                    WHERE P.DATA_VENCIMENTO >= '$dataInicio' AND P.DATA_VENCIMENTO <= '$dataFim' 
                    AND P.STATUS_PARCELA = 'Pago' GROUP BY C.NOME ORDER BY C.NOME ASC";
                $stmt = sqlsrv_query($conn, $sql);
                if ($stmt === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $nome .= '"' . str_replace("Colégio Adventista de ", "", $row['NOMEC']) . '", ';
                    $data .= $row['valor'] . " , ";
                } 
                sqlsrv_free_stmt($stmt);
                
                ?>

                var radarData = {
                    labels: [<?php echo $nome; ?>],
                    datasets: [{
                            label: "Pagamentos realizados por colégio",
                            backgroundColor: 'rgba(0,89,167,0.5)',
                            borderColor: "rgba(0,59,111,0.7)",
                            data: [<?php echo $data; ?>]
                        },
                        {
                            label: "Dívidas concebidas",
                            backgroundColor: "rgba(220,220,220,0.2)",
                            borderColor: "rgba(220,220,220,1)",
                            data: [<?php
                                    $nome = '';
                                    $data = '';
                                    $sql = "SELECT ABS(SUM(D.TOTAL)) AS valor FROM DIVIDA D
                                        LEFT JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO 
                                        LEFT JOIN COLEGIO C ON C.ID_COLEGIO = A.ID_COLEGIO 
                                        WHERE D.DATA_INICIAL >= '$dataInicio' AND D.DATA_INICIAL <= '$dataFim' GROUP BY C.NOME ORDER BY C.NOME ASC";
                                    $stmt = sqlsrv_query($conn, $sql);
                                    if ($stmt === false) {
                                        die(print_r(sqlsrv_errors(), true));
                                    }
                                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                        $data .= $row['valor'] . " , ";
                                    } 
                                    echo $data;
                                    sqlsrv_free_stmt($stmt);
                                    
                                    ?>]
                                            }
                    ]
                };

                var radarOptions = {
                    responsive: true
                };

                var ctx5 = document.getElementById("radarChart").getContext("2d");
                new Chart(ctx5, {
                    type: 'radar',
                    data: radarData,
                    options: radarOptions
                });


            });


            $(function() {
                var barOptions = {
                    series: {
                        bars: {
                            show: true,
                            barWidth: 0.6,
                            fill: true,
                            fillColor: {
                                colors: [{
                                    opacity: 0.8
                                }, {
                                    opacity: 0.8
                                }]
                            }
                        }
                    },
                    xaxis: {
                        tickDecimals: 0
                    },
                    colors: ["#003B6F"],
                    grid: {
                        color: "#999999",
                        hoverable: true,
                        clickable: true,
                        tickColor: "#D4D4D4",
                        borderWidth: 0
                    },
                    legend: {
                        show: true,
                        container: $("#legendContainer"),
                        noColumns: 0
                    },
                    tooltip: true,
                    tooltipOpts: {
                        content: "x: %x, y: %y"
                    },
                    responsive: true
                };

                <?php
                $rows = [];
                $data = '';
                $cont = 1;
                $sql = "SELECT ABS(SUM(D.TOTAL)) AS valor, C.NOME AS NOMEC FROM DIVIDA D
                LEFT JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO 
                LEFT JOIN COLEGIO C ON C.ID_COLEGIO = A.ID_COLEGIO 
                WHERE D.DATA_INICIAL >= '$dataInicio' AND D.DATA_INICIAL <= '$dataFim' GROUP BY C.NOME ORDER BY C.NOME ASC";
                $stmt = sqlsrv_query($conn, $sql);
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $data .= '{label:"' . $cont . '-' . str_replace("Colégio Adventista de ", "", $row['NOMEC']) . '  - R$ <b>' . number_format($row['valor'], 2, ",", ".") . '</b>", data:[' . $cont . ', [' . $cont . ',' . $row['valor'] . ']]},';
                    $cont++;
                }
                sqlsrv_free_stmt($stmt);
                ?>
                var barData = [<?php echo $data; ?>];
                $.plot($("#flot-bar-chart"), barData, barOptions);

            });


            $(function() {
                var barOptions = {
                    series: {
                        bars: {
                            show: true,
                            barWidth: 0.6,
                            fill: true,
                            fillColor: {
                                colors: [{
                                    opacity: 0.8
                                }, {
                                    opacity: 0.8
                                }]
                            }
                        }
                    },
                    xaxis: {
                        tickDecimals: 0
                    },
                    colors: ["#003B6F"],
                    grid: {
                        color: "#999999",
                        hoverable: true,
                        clickable: true,
                        tickColor: "#D4D4D4",
                        borderWidth: 0
                    },
                    legend: {
                        show: true,
                        container: $("#legendContainer-parcelas"),
                        noColumns: 0
                    },
                    tooltip: true,
                    tooltipOpts: {
                        content: "x: %x, y: %y"
                    },
                    responsive: true
                };

                <?php
                $rows = [];
                $data = '';
                $cont = 1;
                $sql = "SELECT ABS(SUM(P.VALOR)) AS valor, C.NOME AS NOMEC FROM PARCELA P 
                    LEFT JOIN DIVIDA D ON D.ID_DIVIDA = P.DIVIDA_ID_DIVIDA
                    LEFT JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO 
                    LEFT JOIN COLEGIO C ON C.ID_COLEGIO = A.ID_COLEGIO 
                    WHERE P.DATA_VENCIMENTO >= '$dataInicio' AND P.DATA_VENCIMENTO <= '$dataFim' 
                    AND P.STATUS_PARCELA = 'Pago' GROUP BY C.NOME ORDER BY C.NOME ASC";
                $stmt = sqlsrv_query($conn, $sql);
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $data .= '{label:"' . $cont . '-' . str_replace("Colégio Adventista de ", "", $row['NOMEC']) . '  - R$ <b>' . number_format($row['valor'], 2, ",", ".") . '</b>", data:[' . $cont . ', [' . $cont . ',' . $row['valor'] . ']]},';
                    $cont++;
                }
                sqlsrv_free_stmt($stmt);
                ?>
                var barData = [<?php echo $data; ?>];
                $.plot($("#flot-bar-parcelas-chart"), barData, barOptions);

            });



            $(function() {
                var barOptions = {
                    series: {
                        bars: {
                            show: true,
                            barWidth: 0.6,
                            fill: true,
                            fillColor: {
                                colors: [{
                                    opacity: 0.8
                                }, {
                                    opacity: 0.8
                                }]
                            }
                        }
                    },
                    xaxis: {
                        tickDecimals: 0
                    },
                    colors: ["#003B6F"],
                    grid: {
                        color: "#999999",
                        hoverable: true,
                        clickable: true,
                        tickColor: "#D4D4D4",
                        borderWidth: 0
                    },
                    legend: {
                        show: true,
                        container: $("#legendContainer-divida-media"),
                        noColumns: 0
                    },
                    tooltip: true,
                    tooltipOpts: {
                        content: "x: %x, y: %y"
                    },
                    responsive: true
                };

                <?php
                $rows = [];
                $data = '';
                $cont = 1;
                $sql = "SELECT AVG(ABS(D.TOTAL)) AS valor, C.NOME AS NOMEC FROM DIVIDA D
                LEFT JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO 
                LEFT JOIN COLEGIO C ON C.ID_COLEGIO = A.ID_COLEGIO 
                WHERE D.DATA_INICIAL >= '$dataInicio' AND D.DATA_INICIAL <= '$dataFim' GROUP BY C.NOME ORDER BY C.NOME ASC";
                $stmt = sqlsrv_query($conn, $sql);
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $data .= '{label:"' . $cont . '-' . str_replace("Colégio Adventista de ", "", $row['NOMEC']) . '  - R$ <b>' . number_format($row['valor'], 2, ",", ".") . '</b>", data:[' . $cont . ', [' . $cont . ',' . $row['valor'] . ']]},';
                    $cont++;
                }
                sqlsrv_free_stmt($stmt);
                ?>
                var barData = [<?php echo $data; ?>];
                $.plot($("#flot-bar-divida-media-chart"), barData, barOptions);

            });



            $(function() {
                var barOptions = {
                    series: {
                        bars: {
                            show: true,
                            barWidth: 0.6,
                            fill: true,
                            fillColor: {
                                colors: [{
                                    opacity: 0.8
                                }, {
                                    opacity: 0.8
                                }]
                            }
                        }
                    },
                    xaxis: {
                        tickDecimals: 0
                    },
                    colors: ["#003B6F"],
                    grid: {
                        color: "#999999",
                        hoverable: true,
                        clickable: true,
                        tickColor: "#D4D4D4",
                        borderWidth: 0
                    },
                    legend: {
                        show: true,
                        container: $("#legendContainer-media"),
                        noColumns: 0
                    },
                    tooltip: true,
                    tooltipOpts: {
                        content: "x: %x, y: %y"
                    },
                    responsive: true
                };

                <?php
                $rows = [];
                $data = '';
                $cont = 1;
                $sql = "SELECT AVG(ABS(P.VALOR)) AS valor, C.NOME AS NOMEC FROM PARCELA P 
                    LEFT JOIN DIVIDA D ON D.ID_DIVIDA = P.DIVIDA_ID_DIVIDA
                    LEFT JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO 
                    LEFT JOIN COLEGIO C ON C.ID_COLEGIO = A.ID_COLEGIO 
                    WHERE P.DATA_VENCIMENTO >= '$dataInicio' AND P.DATA_VENCIMENTO <= '$dataFim' 
                    AND P.STATUS_PARCELA = 'Pago' GROUP BY C.NOME ORDER BY C.NOME ASC";
                $stmt = sqlsrv_query($conn, $sql);
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $data .= '{label:"' . $cont . '-' . str_replace("Colégio Adventista de ", "", $row['NOMEC']) . ' - R$ <b>' . number_format($row['valor'], 2, ",", ".") . '</b>", data:[' . $cont . ', [' . $cont . ',' . $row['valor'] . ']]},';
                    $cont++;
                }
                sqlsrv_free_stmt($stmt);
                ?>
                var barData = [<?php echo $data; ?>];
                $.plot($("#flot-bar-media-chart"), barData, barOptions);

            });


            $(function() {
                var barOptions = {
                    series: {
                        bars: {
                            show: true,
                            barWidth: 0.6,
                            fill: true,
                            fillColor: {
                                colors: [{
                                    opacity: 0.8
                                }, {
                                    opacity: 0.8
                                }]
                            }
                        }
                    },
                    xaxis: {
                        tickDecimals: 0
                    },
                    colors: ["#003B6F"],
                    grid: {
                        color: "#999999",
                        hoverable: true,
                        clickable: true,
                        tickColor: "#D4D4D4",
                        borderWidth: 0
                    },
                    legend: {
                        show: true,
                        container: $("#legendContainer-quantidade"),
                        noColumns: 0
                    },
                    tooltip: true,
                    tooltipOpts: {
                        content: "x: %x, y: %y"
                    },
                    responsive: true
                };

                <?php
                $rows = [];
                $data = '';
                $cont = 1;
                $sql = "SELECT COUNT(D.TOTAL) AS valor, C.NOME AS NOMEC FROM DIVIDA D
                LEFT JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO 
                LEFT JOIN COLEGIO C ON C.ID_COLEGIO = A.ID_COLEGIO 
                WHERE D.DATA_INICIAL >= '$dataInicio' AND D.DATA_INICIAL <= '$dataFim' GROUP BY C.NOME ORDER BY C.NOME ASC";
                $stmt = sqlsrv_query($conn, $sql);
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $data .= '{label:"' . $cont . '-' . str_replace("Colégio Adventista de ", "", $row['NOMEC']) . ' - <b>' . $row['valor'] . '</b>", data:[' . $cont . ', [' . $cont . ',' . $row['valor'] . ']]},';
                    $cont++;
                }
                sqlsrv_free_stmt($stmt);
                ?>
                var barData = [<?php echo $data; ?>];
                $.plot($("#flot-bar-quantidade"), barData, barOptions);

            });


            $(function() {
                var barOptions = {
                    series: {
                        bars: {
                            show: true,
                            barWidth: 0.6,
                            fill: true,
                            fillColor: {
                                colors: [{
                                    opacity: 0.8
                                }, {
                                    opacity: 0.8
                                }]
                            }
                        }
                    },
                    xaxis: {
                        tickDecimals: 0
                    },
                    colors: ["#003B6F"],
                    grid: {
                        color: "#999999",
                        hoverable: true,
                        clickable: true,
                        tickColor: "#D4D4D4",
                        borderWidth: 0
                    },
                    legend: {
                        show: true,
                        container: $("#legendContainer-parcelas-quantidade"),
                        noColumns: 0
                    },
                    tooltip: true,
                    tooltipOpts: {
                        content: "x: %x, y: %y"
                    },
                    responsive: true
                };

                <?php
                $rows = [];
                $data = '';
                $cont = 1;
                $sql = "SELECT COUNT(P.VALOR) AS valor, C.NOME AS NOMEC FROM PARCELA P 
                    LEFT JOIN DIVIDA D ON D.ID_DIVIDA = P.DIVIDA_ID_DIVIDA
                    LEFT JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO 
                    LEFT JOIN COLEGIO C ON C.ID_COLEGIO = A.ID_COLEGIO 
                    WHERE P.DATA_VENCIMENTO >= '$dataInicio' AND P.DATA_VENCIMENTO <= '$dataFim' 
                    AND P.STATUS_PARCELA = 'Pago' GROUP BY C.NOME ORDER BY C.NOME ASC";
                $stmt = sqlsrv_query($conn, $sql);
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $data .= '{label:"' . $cont . '-' . str_replace("Colégio Adventista de ", "", $row['NOMEC']) . ' - <b>' . $row['valor'] . '</b>", data:[' . $cont . ', [' . $cont . ',' . $row['valor'] . ']]},';
                    $cont++;
                }
                sqlsrv_free_stmt($stmt);
                ?>
                var barData = [<?php echo $data; ?>];
                $.plot($("#flot-bar-parcelas-quantidade"), barData, barOptions);

            });
        </script>
        </body>
    <? } ?>

</html>