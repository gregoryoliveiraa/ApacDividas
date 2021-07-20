<!DOCTYPE html>
<html>

<head>

    <?php
    include("loadingPage.php");
    include("valida.php");
    $retornoProtegepagina = protegePagina();
    require('menu.php');
    require('conexao.php');

    $acessoUsuario = 0;
    if(isset($_SESSION['usuarioAcesso']) && isset($_SESSION['usuarioIdColegio'])){
        $acessoUsuario = $_SESSION['usuarioAcesso'];
        $_GET['id_colegio'] = $_SESSION['usuarioIdColegio'];
        if(empty($_GET['Ano']) && empty($_GET['Mes'])){
            $_GET['Ano'] = "Selecione o ano";
            $_GET['Mes'] ="Selecione o mes";
        }
    }
    if($_SESSION['usuarioAcesso'] == 5) {
        $acessoUsuario = $_SESSION['usuarioAcesso'];
    }

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

        $desabilitarGrafico = 1;
        $desabilitarPesquisa = 1;


        if(!empty($_GET['id_colegio']) && $_GET['id_colegio'] != "Selecione o Colégio"){
            if($_GET['Ano'] == "Selecione o ano" && $_GET['Mes'] == "Selecione o mes"){
                $dataInicio = "2017-01-01";
                $dataFim = date('Y-m')."-28";
                $desabilitarGrafico = 0;
            }
            $idColegio = $_GET['id_colegio'];
            $queryDivida = " LEFT JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO 
                LEFT JOIN COLEGIO C ON C.ID_COLEGIO = A.ID_COLEGIO 
                WHERE D.DATA_INICIAL >= '$dataInicio' AND D.DATA_INICIAL <= '$dataFim' AND C.ID_COLEGIO = $idColegio";

            $queryParcela = " LEFT JOIN DIVIDA D ON D.ID_DIVIDA = P.DIVIDA_ID_DIVIDA
            LEFT JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO 
            LEFT JOIN COLEGIO C ON C.ID_COLEGIO = A.ID_COLEGIO 
            WHERE P.DATA_VENCIMENTO >= '$dataInicio' AND P.DATA_VENCIMENTO <= '$dataFim' AND C.ID_COLEGIO = $idColegio";

            $sql = "SELECT * FROM COLEGIO WHERE ID_COLEGIO = $idColegio";
            $stmt = sqlsrv_query($conn, $sql);
            $rowColegio = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            sqlsrv_free_stmt($stmt);

        }else{
            $queryDivida = " WHERE D.DATA_INICIAL >= '$dataInicio' AND D.DATA_INICIAL <= '$dataFim'";
            $queryParcela = " WHERE P.DATA_VENCIMENTO >= '$dataInicio' AND P.DATA_VENCIMENTO <= '$dataFim'";
            $rowColegio['NOME'] = "";
        }

        if($_SESSION['usuarioAcesso'] == 5){
            $alunos = "";
            foreach($_SESSION['ALUNOS'] as $aluno){
                if(!empty($aluno)){
                    $alunos .= $aluno . ", ";
                }
            }
            $alunos = substr($alunos, 0, -2);

            $queryDivida = "WHERE ALUNO_ID_ALUNO IN (" . $alunos . ")";

            $queryParcela = " LEFT JOIN DIVIDA D ON D.ID_DIVIDA = P.DIVIDA_ID_DIVIDA
            LEFT JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO
            WHERE D.ALUNO_ID_ALUNO IN (" . $alunos . ")" ;

            $desabilitarGrafico = 0;
            $desabilitarPesquisa = 0;
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
                                $sql = "SELECT ABS(SUM(D.TOTAL)) AS Valor FROM DIVIDA D $queryDivida";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowDividas = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "R$ <h1 class='no-margins'>" . number_format($rowDividas['Valor'], 2, ",", ".") . "</h1>";
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(D.ID_DIVIDA) AS Total FROM DIVIDA D $queryDivida";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowDividaCount = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "<br>Total: " . $rowDividaCount['Total'];
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(P.ID_PARCELA) AS Total FROM PARCELA P $queryParcela";
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
                $sql = "SELECT ABS(SUM(P.VALOR)) AS Valor FROM PARCELA P $queryParcela AND P.STATUS_PARCELA = 'Pago'";
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

                                $sql = "SELECT COUNT(D.ID_DIVIDA) AS Total FROM DIVIDA D $queryDivida AND D.STATUS_DIV = 'Pago'";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowDividaCount = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "<br>Dívidas: " . $rowDividaCount['Total'];
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(P.ID_PARCELA) AS Total FROM PARCELA P $queryParcela AND P.STATUS_PARCELA = 'Pago'";
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
                $sql = "SELECT ABS(SUM(P.VALOR)) AS Valor FROM PARCELA P $queryParcela AND P.STATUS_PARCELA = 'Em Aberto'";
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
                            <h5><? if($_SESSION['usuarioAcesso'] != 5) { ?> À receber <? }else{ ?> À pagar <? } ?></h5>
                        </div>
                        <a href="dividas.php?status='emAberto'" style="text-decoration:none; color: inherit;">
                            <div class="ibox-content">
                                <?
                                echo "R$ <h1 class='no-margins'>" . number_format($rowAReceber['Valor'], 2, ",", ".") . "</h1>";
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(D.ID_DIVIDA) AS Total FROM DIVIDA D $queryDivida AND STATUS_DIV = 'Em Aberto'";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                $rowDividaCount = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                echo "<br>Dívidas: " . $rowDividaCount['Total'];
                                sqlsrv_free_stmt($stmt);

                                $sql = "SELECT COUNT(P.ID_PARCELA) AS Total FROM PARCELA P $queryParcela AND P.STATUS_PARCELA = 'Em Aberto'";
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

        <? if($desabilitarGrafico){ ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5><? echo $ano . " " . $rowColegio['NOME'];?> 
                            <small>- Dívidas x pagamentos (<? echo $dataInicio . " a " . $dataFim;?> )</small>
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
        <? } ?>


        <? if($desabilitarPesquisa){ ?>
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
                                <div class="col-sm-3">
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
                                <div class="col-sm-3">
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

                                <?php if(empty($_SESSION['usuarioIdColegio'])){ ?>
                                    <div class="col-sm-3">
                                        <select class="form-control m-b" name="id_colegio">
                                        <?php if(!isset($_GET['id'])){?>
                                            <option>Selecione o Colégio</option>
                                        <?php }

                                                $sql = "SELECT * FROM COLEGIO ORDER BY NOME ASC";
                                                $stmt = sqlsrv_query($conn, $sql);
                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }

                                                while ($rowColegio = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                    echo "<option value='" . $rowColegio['ID_COLEGIO'] . "'>" . $rowColegio['DEPARTAMENTO'] . " - " .$rowColegio['NOME'] . "</option>";
                                                }

                                                sqlsrv_free_stmt($stmt);
                                            ?>
                                        </select>
                                    </div>
                                <? } ?>
                                <div class="col-sm-3 col-sm-offset-2">
                                    <button class="btn btn-success btn-lg" type="submit">  <i class="fa fa-bar-chart"></i> Vizualizar dados</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
            <? } ?>

            <div class="row">
                <div class="col-lg-6">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Pagamentos de parcela por status </h5>

                        </div>
                        <div class="ibox-content">
                            <div>
                                <canvas id="doughnutChart" height="140"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Pagamentos de divida por status </h5>

                        </div>
                        <div class="ibox-content">
                            <div>
                                <canvas id="doughnutChartDiv" height="140"></canvas>
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
$data = "";
$mes = "";
$sql = "SELECT ABS(SUM(P.VALOR)) AS Valor, P.DATA_VENCIMENTO FROM PARCELA P 
                    $queryParcela AND P.STATUS_PARCELA = 'Pago' GROUP BY P.DATA_VENCIMENTO";
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
                    $sql = "SELECT ABS(SUM(D.TOTAL)) AS Valor, D.DATA_INICIAL FROM DIVIDA D $queryDivida GROUP BY D.DATA_INICIAL";
                    $stmt = sqlsrv_query($conn, $sql);
                    while ($rowsss = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $data .= $rowsss['Valor'] . ", ";
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


            $(function() {

                var doughnutData = {
                    labels: [<?php
                                $rows = [];
                                $data = '';
                                $sql = "SELECT P.STATUS_PARCELA FROM PARCELA P $queryParcela GROUP BY P.STATUS_PARCELA ORDER BY P.STATUS_PARCELA DESC";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    $rows[] = $row;
                                }
                                foreach ($rows as $row) {
                                    $data .= '"' . $row['STATUS_PARCELA'] . '", ';
                                }
                                sqlsrv_free_stmt($stmt);
                                echo $data;
                                ?>],
                    datasets: [{
                        data: [<?php
                                $rows = [];
                                $data = '';
                                $sql = "SELECT COUNT(P.ID_PARCELA) AS QTD FROM PARCELA P $queryParcela GROUP BY P.STATUS_PARCELA ORDER BY P.STATUS_PARCELA DESC";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    $rows[] = $row;
                                }
                                foreach ($rows as $row) {
                                    $data .= $row['QTD'] . ", ";
                                }
                                sqlsrv_free_stmt($stmt);
                                echo $data;
                                ?>],
                        backgroundColor: ["#1c84c6"]
                    }]
                };


                var doughnutOptions = {
                    responsive: true
                };


                var ctx4 = document.getElementById("doughnutChart").getContext("2d");
                new Chart(ctx4, {
                    type: 'doughnut',
                    data: doughnutData,
                    options: doughnutOptions
                });

            });


            $(function() {

                var doughnutData = {
                    labels: [<?php
                                $rows = [];
                                $data = '';
                                $sql = "SELECT D.STATUS_DIV FROM DIVIDA D $queryDivida GROUP BY D.STATUS_DIV ORDER BY D.STATUS_DIV DESC";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    $rows[] = $row;
                                }
                                foreach ($rows as $row) {
                                    $data .= '"' . $row['STATUS_DIV'] . '", ';
                                }
                                sqlsrv_free_stmt($stmt);
                                echo $data;
                                ?>],
                    datasets: [{
                        data: [<?php
                                $rows = [];
                                $data = '';
                                $sql = "SELECT COUNT(D.STATUS_DIV) AS QTD FROM DIVIDA D $queryDivida GROUP BY D.STATUS_DIV ORDER BY D.STATUS_DIV DESC";
                                $stmt = sqlsrv_query($conn, $sql);
                                if ($stmt === false) {
                                    die(print_r(sqlsrv_errors(), true));
                                }
                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    $rows[] = $row;
                                }
                                foreach ($rows as $row) {
                                    $data .= $row['QTD'] . ", ";
                                }
                                sqlsrv_free_stmt($stmt);
                                echo $data;
                                ?>],
                        backgroundColor: ["#1c84c6"]
                    }]
                };


                var doughnutOptions = {
                    responsive: true
                };


                var ctx4 = document.getElementById("doughnutChartDiv").getContext("2d");
                new Chart(ctx4, {
                    type: 'doughnut',
                    data: doughnutData,
                    options: doughnutOptions
                });

            });
        </script>
        </body>
    <? } ?>

</html>