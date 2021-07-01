<!DOCTYPE html>
<html>

<head>

        <?php   
            include("valida.php"); 
            $retornoProtegepagina = protegePagina();
            require('menu.php'); 
            require('conexao.php'); 

            if($retornoProtegepagina){

            if(empty($_GET['Ano'])){
                $ano = date('Y');
            }
            else{
                $ano = $_GET['Ano'];
            }
            $mes = intval(date('m'));

            $nomesLegenda = '';
        ?>

        </div>


    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
                    <div class="col-lg-3">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <span class="label label-info float-right">Anual</span>
                                <h5>Colegios</h5>
                            </div>
                            <div class="ibox-content">
                                <?php
                                    $sql = "SELECT COUNT(NOME) AS QTD FROM COLEGIO";
                                    $stmt = sqlsrv_query($conn, $sql);
                                    if ($stmt === false) {
                                        die(print_r(sqlsrv_errors(), true));
                                    }
                                    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                        echo "<h1 class='no-margins'>".$row['QTD']."</h1>";
                                    sqlsrv_free_stmt($stmt);
                                
                                ?>
                                <small>Cadastrados</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <span class="label label-success float-right">Anual</span>
                                <h5>Alunos</h5>
                            </div>
                            <div class="ibox-content">
                                <?php
                                    $sql = "SELECT COUNT(NOME) AS QTD FROM ALUNO";
                                    $stmt = sqlsrv_query($conn, $sql);
                                    if ($stmt === false) {
                                        die(print_r(sqlsrv_errors(), true));
                                    }
                                    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                        echo "<h1 class='no-margins'>".$row['QTD']."</h1>";
                                    sqlsrv_free_stmt($stmt);
                                
                                ?>
                                <small>Total</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <span class="label label-primary float-right">Anual</span>
                                <h5>Dívidas</h5>
                            </div>
                            <div class="ibox-content">
                            <?php 
                                                        $sql = "select count(Historico) as valor from v_aasi_acordo_confissao WHERE Valor <= 0 AND Ano = $ano";
                                                        $stmt = sqlsrv_query($conn, $sql);
                                                        if ($stmt === false) {
                                                            die(print_r(sqlsrv_errors(), true));
                                                        }
                                                        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                                        echo "<h1 class='no-margins'>".$row['valor']."</h1>"; 
                                                        sqlsrv_free_stmt($stmt);
                                                ?>
                                <small>Cedidas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <span class="label label-danger float-right">Anual</span>
                                <h5>Parcelas</h5>
                            </div>
                            <div class="ibox-content">
                                <?php
                                    $sql = "SELECT COUNT(ID_Parcela) AS QTD FROM PARCELA";
                                    $stmt = sqlsrv_query($conn, $sql);
                                    if ($stmt === false) {
                                        die(print_r(sqlsrv_errors(), true));
                                    }
                                    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                        echo "<h1 class='no-margins'>".$row['QTD']."</h1>";
                                    sqlsrv_free_stmt($stmt);
                                
                                ?>
                                <small>Neste momento</small>
                            </div>
                        </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5><? echo $ano;?> - Total por colégio</h5>
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
                        <h5><? echo $ano;?> - <small>Total de dividapor colegio</small></h5>
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
                    <div class="ibox-content">
                         <form method="get" action="">
                            <div class="form-group  row">
                                <div class="col-sm-6">
                                    <select class="form-control m-b" name="Ano">
                                        <option>Selecione o ano</option>
                                        <?php
                                        $date = date('Y');
                                        while($date >2016){
                                            echo  "<option value='$date'>$date</option>";
                                            $date--;
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-success btn-lg" type="submit">Vizualizar no grafico</button>
                                </div>
                            </div>
                        </form>
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
       
        $(function () {
    <?php
        $rows = [];
        $nome = '';
        $data = '';
        $sql = "select Departamento, ABS(Sum(Valor)) as valor from v_aasi_acordo_confissao WHERE Valor > 0 AND Ano = $ano And Departamento != 110 group by Departamento order by Departamento asc";
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
           $rows[] = $row;
        }
        sqlsrv_free_stmt($stmt);
        foreach($rows as $key => $row){
            $sql = "SELECT NOME FROM COLEGIO where Departamento = " . $row['Departamento'];
            $stmt = sqlsrv_query($conn, $sql);
            $roww = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $rows[$key]['NOME'] = $roww['NOME'];
            $rows[$key]['valor'] = $row['valor'];

        }
        foreach($rows as $r){
            $nome .= '"'. $r['NOME'] .'", ';
            $data .= $r['valor']." , ";
        }
        sqlsrv_free_stmt($stmt);
        ?>

    var radarData = {
        labels: [<?php echo $nome; ?>],
        datasets: [
            {
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
                    $rows = [];
                    $data = '';
                    $sql = "select Departamento, ABS(Sum(Valor)) as Valor from v_aasi_acordo_confissao WHERE Valor < 0 AND Ano = $ano And Departamento != 110 group by Departamento order by Departamento asc";
                    $stmt = sqlsrv_query($conn, $sql);
                    if ($stmt === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }
                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $rows[] = $row;
                    }
                    foreach($rows as $row){
                        $data .= $row['Valor'].", ";
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
    new Chart(ctx5, {type: 'radar', data: radarData, options:radarOptions});


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
        colors: ["#1ab394"],
        grid: {
            color: "#999999",
            hoverable: true,
            clickable: true,
            tickColor: "#D4D4D4",
            borderWidth:0
        },
        legend: {
            show: true,
            container:$("#legendContainer"),            
            noColumns: 0
        },
        tooltip: true,
        tooltipOpts: {
            content: "x: %x, y: %y"
        }
    };

    <?php
        $rows = [];
        $data = '';
        $cont = 1;
        $sql = "select Departamento, ABS(Sum(Valor)) as valor from v_aasi_acordo_confissao WHERE Valor > 0 AND Ano = $ano And Departamento != 110 group by Departamento order by Departamento asc";
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
           $rows[] = $row;
        }
        sqlsrv_free_stmt($stmt);
        foreach($rows as $key => $row){
            $sql = "SELECT NOME FROM COLEGIO where Departamento = " . $row['Departamento'];
            $stmt = sqlsrv_query($conn, $sql);
            $roww = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $rows[$key]['NOME'] = $roww['NOME'];
            $rows[$key]['valor'] = $row['valor'];

        }
        foreach($rows as $r){
            $data .= '{label:"' . $r['NOME'] . '", data:['. $r['valor'] .']},';
            $nomesLegenda .= $cont . " - " . $r['NOME'] . "<br>";
            $cont++;
        }
        sqlsrv_free_stmt($stmt);
        ?>
        <?php //echo $data; ?>
    var barData = [
        {label:"teste", data:[200]}
    ];
    $.plot($("#flot-bar-chart"), barData, barOptions);

});
    </script>
</body>
<? } ?>
</html>
