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
        ?>

        </div>


    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
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
                                <h5>Usuários</h5>
                            </div>
                            <div class="ibox-content">
                                <?php
                                    $sql = "SELECT COUNT(ID_USUARIO) AS QTD FROM USUARIO";
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
                            <h5><? echo $ano;?> - Comparação de pagamento
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
                                        while($date >2000){
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
                    $mes = '';
                    $data = '';
                    $sql = "select Mes, ABS(Sum(Valor)) as valor from v_aasi_acordo_confissao WHERE Valor > 0 AND Ano = $ano group by Mes";
                    $stmt = sqlsrv_query($conn, $sql);
                    if ($stmt === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }
                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $rows[] = $row;
                    }
                    foreach($rows as $row){
                        switch ($row['Mes']){
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
                        $mes .= '"'. $mesTexto .'", ';
                        $data .= $row['valor'].", ";
                    }
                      sqlsrv_free_stmt($stmt);
                ?>
var lineData = {
    labels: [<?php echo $mes; ?>],
    datasets: [

        {
            label: "Pagamentos realizados",
            backgroundColor: 'rgba(0,89,167,0.5)',
            borderColor: "rgba(0,59,111,0.7)",
            pointBackgroundColor: "rgba(26,179,148,1)",
            pointBorderColor: "#fff",
            data: [<?php echo $data; ?>]
        },{
            label: "Confissão de dívida cedida",
            backgroundColor: 'rgba(220, 220, 220, 0.5)',
            pointBorderColor: "#fff",
            data: [<?php
                    $rows = [];
                    $mes = '';
                    $data = '';
                    $sql = "select Mes, ABS(Sum(Valor)) as valor from v_aasi_acordo_confissao WHERE Valor < 0 AND Ano = $ano group by Mes";
                    $stmt = sqlsrv_query($conn, $sql);
                    if ($stmt === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }
                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $rows[] = $row;
                    }
                    foreach($rows as $row){
                        $data .= $row['valor'].", ";
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
new Chart(ctx, {type: 'line', data: lineData, options:lineOptions});


});
    </script>
</body>
<? } ?>
</html>
