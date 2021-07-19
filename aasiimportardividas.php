

 <!DOCTYPE html>
<html>

<head>

    <!--Importando Script Jquery-->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script type="text/javascript">

        function successCadastro(){
            Swal.fire(
            'Cadastro',
            'realizado com sucesso',
            'success'
            )
        };

        function errorImportacaoDividas(){
            Swal.fire(
            'Oops...',
            'Nenhuma divida foi importada!',
            'warning'
            )
        };

        function successImportacaoDividas(dividas, parcelas){
            Swal.fire(
            'Importação de '+dividas+' dívidas e '+parcelas+' parcelas',
            'realizado com sucesso ',
            'success'
            )
        };

        function successEdicao(){
            Swal.fire(
            'Edição',
            'realizada com sucesso',
            'success'
            )
        };

        function successDeletar(){
            Swal.fire(
            'Remoção',
            'realizada com sucesso',
            'success'
            )
        };

        function deletar(id){
            Swal.fire({
            title: 'Tem certeza que deseja deletar?',
            text: "Você não conseguira reverter esta ação!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, deletar!',
            cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "responsavel.php?deletar=1&idDeletar="+id
                }
            })  
        };

    </script>

<?php

        if (!isset($_POST['btEdicao']) && !isset($_POST['btNovo'])){
            include("valida.php"); 
            $retornoProtegepagina = protegePagina();
        }else{
            $retornoProtegepagina = 1;
        }

        function encontrarAlunoDividas($raAluno, $departamento, $hitorico){

            require('conexao.php');
            $sql = "SELECT A.ID_ALUNO AS ID_ALUNO FROM ALUNO AS A
            LEFT JOIN COLEGIO AS C ON A.ID_COLEGIO = C.ID_COLEGIO
            WHERE A.RA = $raAluno AND C.DEPARTAMENTO = $departamento";
            $stmt = sqlsrv_query($conn, $sql);
            $rowAluno = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            sqlsrv_free_stmt($stmt);

            if(empty($rowAluno)){
                $departamentoDiv = $departamento == 1010 ? 1001 : str_replace('10', '01', $departamento);
                $sql = "SELECT Cod_Aluno, Aluno, Cod_Escola, ResponsavelFinanceiro, Celular_Resp_Financeiro, Email_Resp_Financeiro, Parcelas_em_Atraso, Valor_Pendencias_Resp_Fin 
                FROM APAC_DIVIDAS.dbo.v_web_escola_dados_alunos WHERE (Cod_Aluno = $raAluno) AND Cod_Escola = $departamentoDiv";
                $stmt = sqlsrv_query($conn, $sql);
                $rowAlunoASSI = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                sqlsrv_free_stmt($stmt);

                $sql = "SELECT ID_COLEGIO FROM COLEGIO WHERE DEPARTAMENTO = $departamento";
                $stmt = sqlsrv_query($conn, $sql);
                $rowColegio = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                sqlsrv_free_stmt($stmt);

                if(!empty($rowAlunoASSI['ResponsavelFinanceiro'])){

                    $responsavel = str_replace("'", "", $rowAlunoASSI['ResponsavelFinanceiro']);
                    $sql = "SELECT ID_RESPONSAVEL FROM RESPONSAVEL WHERE NOME LIKE '$responsavel'";
                    $stmt = sqlsrv_query($conn, $sql);
                    $rowResponsavel = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                    sqlsrv_free_stmt($stmt);

                    if(empty($rowResponsavel)){
                        try{
                            $tsql= "INSERT INTO RESPONSAVEL (NOME, CELULAR, EMAIL)
                            VALUES ('".$rowAlunoASSI['ResponsavelFinanceiro']."', '".$rowAlunoASSI['Celular_Resp_Financeiro']."', '".$rowAlunoASSI['Email_Resp_Financeiro']."' )";
                            sqlsrv_query($conn, $tsql);
                        }
                        catch(Exception $e)
                        {
                            echo $e->getMessage();
                        }
                        $sql = "SELECT ID_RESPONSAVEL FROM RESPONSAVEL WHERE NOME LIKE '$responsavel'";
                        $stmt = sqlsrv_query($conn, $sql);
                        $rowResponsavel = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                    }
                }
                $nome = empty($rowAlunoASSI['Aluno']) ? preg_replace("/[^a-zA-Z0-9\s]/", "", $hitorico) : $rowAlunoASSI['Aluno'];
                $ra = empty($rowAlunoASSI['Cod_Aluno']) ? $raAluno : $rowAlunoASSI['Cod_Aluno'];

                if(!empty($nome) && !empty($ra) && !empty($rowColegio['ID_COLEGIO'])){
                    $id_colegio = $rowColegio['ID_COLEGIO'];
                    $id_responsavel = empty($rowResponsavel['ID_RESPONSAVEL']) ? NULL : $rowResponsavel['ID_RESPONSAVEL'];

                    try{
                        $tsql= "INSERT INTO ALUNO (
                            NOME,
                            RA,
                            ID_COLEGIO,
                            ID_RESPONSAVEL)
                            VALUES (?, ?, ?, ?)";

                        $var = [$nome, $ra, $id_colegio, $id_responsavel];
                        sqlsrv_query($conn, $tsql, $var);
                    }
                    catch(Exception $e)
                    {
                        echo $e->getMessage();
                    }

                    $sql = "SELECT ID_ALUNO FROM ALUNO WHERE RA = $ra";
                    $stmt = sqlsrv_query($conn, $sql);
                    $rowAluno = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                    sqlsrv_free_stmt($stmt);

                    return $rowAluno['ID_ALUNO'];
                }
            }

            return $rowAluno['ID_ALUNO'];
        }


        function validarECriarDivida($idAluno, $valor, $quantidadeParcelas, $dataInicialDivida){
            require('conexao.php');
            $sql = "SELECT * FROM DIVIDA WHERE ALUNO_ID_ALUNO = $idAluno AND TOTAL = $valor AND QTD_PARCELAS = $quantidadeParcelas AND DATA_INICIAL = '$dataInicialDivida'";
            $stmt = sqlsrv_query($conn, $sql);
            $rowDivida = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            sqlsrv_free_stmt($stmt);

            $dataSync = new DateTime('now');
            $dataSync = $dataSync->format('Y-m-d');

            if(empty($rowDivida)){
                try{
                    $tsql= "INSERT INTO DIVIDA (
                        ALUNO_ID_ALUNO,
                        TOTAL,
                        QTD_PARCELAS,
                        DATA_INICIAL,
                        STATUS_DIV,
                        DATA_SYNC_ASSI)
                        VALUES (?, ?, ?, ?, ?, ?)";

                    $var = [$idAluno, $valor, $quantidadeParcelas, $dataInicialDivida, "Em Aberto", $dataSync];
                    sqlsrv_query($conn, $tsql, $var);
                }
                catch(Exception $e)
                {
                    echo $e->getMessage();
                }

                $sql = "SELECT * FROM DIVIDA WHERE ALUNO_ID_ALUNO = $idAluno AND TOTAL = $valor AND QTD_PARCELAS = $quantidadeParcelas AND DATA_INICIAL = '$dataInicialDivida'";
                $stmt = sqlsrv_query($conn, $sql);
                $rowDivida = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                sqlsrv_free_stmt($stmt);
                return $rowDivida;

            }
            return $rowDivida;
        }


        function validarECriarParcela($valor, $valorParcela, $quantidadeParcelas, $idDivida, $mes, $ano){
            require('conexao.php');
            $sql = "SELECT ID_PARCELA FROM PARCELA WHERE DIVIDA_ID_DIVIDA = $idDivida";
            $stmt = sqlsrv_query($conn, $sql);
            $cont = 0;
            while($row = sqlsrv_fetch_array($stmt)) {
                $cont++;
            }
            if(empty($cont)){
                $dataSync = new DateTime('now');
                $dataSync = $dataSync->format('Y-m-d');
                for($i = 1; $i <= $quantidadeParcelas; $i++){
                    $dataParcela = dataFormatada($ano, $mes);
                    $mes++;
                    if($mes >= 13){
                        $ano++;
                        $mes = 1;
                    }
                    try{
                        $tsql= "INSERT INTO PARCELA (
                            DIVIDA_ID_DIVIDA,
                            NUMERO,
                            VALOR,
                            STATUS_PARCELA,
                            FORMA_PAGAMENTO,
                            DATA_VENCIMENTO,
                            DATA_SYNC_ASSI)
                            VALUES (?, ?, ?, ?, ?, ?, ?)";

                        $var = [$idDivida, $i, $valorParcela, "Em Aberto", "Boleto", $dataParcela, $dataSync];
                        sqlsrv_query($conn, $tsql, $var);
                    }
                    catch(Exception $e)
                    {
                        echo $e->getMessage();
                    }
                }
                $sql = "SELECT ID_PARCELA FROM PARCELA WHERE DIVIDA_ID_DIVIDA = $idDivida";
                $stmt = sqlsrv_query($conn, $sql);
                $cont = 0;
                while($row = sqlsrv_fetch_array($stmt)) {
                    $cont++;
                }
            }
            return $cont;
        }

        function validaSegundoRA($historico){

            $historico = validarDepartamentoNoHistorico($historico);
            $historico = validarRegras($historico);
            $raAluno = substr($historico, 5, 6);
            $raAluno = preg_replace('/[^0-9]/', '', $raAluno);
            return $raAluno;
        }


        function validarHistoricoComColegio($historico){

            $historico = validarDepartamentoNoHistorico($historico);
            $historico = validarRegras($historico);
            $raAluno = substr($historico, 0, 6);
            $raAluno = preg_replace('/[^0-9]/', '', $raAluno);
            return $raAluno;
        }

        function validarDepartamentoNoHistorico($historico){

            if(substr($historico, 0, 3) == 110 ||
                substr($historico, 0, 3) == 310 ||
                substr($historico, 0, 3) == 710) {
                $historico = substr($historico, 5);
            }

            if(substr($historico, 0, 3) == 101 ||
                substr($historico, 0, 3) == 301 ||
                substr($historico, 0, 3) == 701) {
                $historico = substr($historico, 5);
            }
            if(substr($historico, 0, 4) == 2610 ||
                substr($historico, 0, 4) == 2601 ||
                substr($historico, 0, 4) == 1910 ||
                substr($historico, 0, 4) == 1901 ||
                substr($historico, 0, 4) == 2410 ||
                substr($historico, 0, 4) == 2401 ||
                substr($historico, 0, 4) == 1010 ||
                substr($historico, 0, 4) == 1001 ||
                substr($historico, 0, 4) == 2210 ||
                substr($historico, 0, 4) == 2201 ||
                substr($historico, 0, 4) == 2510 ||
                substr($historico, 0, 4) == 2501 ||
                substr($historico, 0, 4) == 2710 ||
                substr($historico, 0, 4) == 2701 ) {
                $historico = substr($historico, 6);
            }
            return $historico;
        }

        function validarRegras($historico){
            if(substr($historico, 0, 3) == 'RA ' || substr($historico, 0, 3) == 'RA:'){
                $historico = substr($historico, 3);
            }
            $pos = strripos(strtolower($historico), 'ra');
            $raNaPosicao = is_numeric(substr($historico, $pos + 3, 3));
            if($pos && $raNaPosicao){
                $historico = substr($historico, $pos + 2);
            }

            return $historico;
        }
        function validarQuantidadeDeParcelas($historico, $ano){

            $historico = substr($historico, -10);

            if(substr($ano, -2) == substr($historico, -2) || 
                substr($ano, -2) + 1 == substr($historico, -2))
            {
                $quantidadeParcelas = substr($historico, -5, 2);
            }
            if($ano == substr($historico, -4) || $ano + 1 == substr($historico, -4)){
                $quantidadeParcelas = substr($historico, -7, 2);
            }
            if(empty($quantidadeParcelas)){
                $quantidadeParcelas = preg_replace('/[^0-9]/', '', substr($historico, -3));
            }

            if(empty($quantidadeParcelas) || !is_numeric($quantidadeParcelas)){
                $quantidadeParcelas = 10;
            }

            return $quantidadeParcelas;
        }

        function dataFormatada($ano, $mes){

            if($mes < 10){
                $zero = 0;
            }else{
                $zero = '';
            }
            $data = $ano . "-" . $zero . $mes . "-10";

            return $data;
        }

        function ignorarDivida($historico){
            if(strpos(strtolower($historico), 'cancelado') !== false){
                return true;
            }
            if(strpos(strtolower($historico), 'amex') !== false){
                return true;
            }
            if(strpos(strtolower($historico), 'hipercard') !== false){
                return true;
            }
            return false;
        }

        if($retornoProtegepagina){
        require('menu.php');
        require('conexao.php');

        if(isset($_GET['btImportarDividas'])){

            $queryWhere = "Where ";
            $cont = 0;

            if(!empty($_GET['Entidade'])){
                $Entidade = $_GET['Entidade'];
                $queryWhere = $queryWhere . "Entidade = $Entidade AND ";
            }

            if(!empty($_GET['Ano']) && is_numeric($_GET['Ano'])){
                $Ano = $_GET['Ano'];
                $queryWhere = $queryWhere . "Ano = $Ano AND ";
            }

            if(!empty($_GET['Mes']) && is_numeric($_GET['Mes'])){
                $Mes = $_GET['Mes'];
                $queryWhere = $queryWhere . "Mes = $Mes AND ";
            }

            if(!empty($_GET['ContaContabil'])){
                $ContaContabil = $_GET['ContaContabil'];
                $queryWhere = $queryWhere . "Conta_contabil LIKE '%$ContaContabil%' AND ";
            }

            if(!empty($_GET['ContaCorrente'])){
                $ContaCorrente = $_GET['ContaCorrente'];
                $queryWhere = $queryWhere . "Conta_corrente LIKE '%$ContaCorrente%' AND ";
            }

            if(!empty($_GET['Departamento']) && is_numeric($_GET['Departamento'])){
                $Departamento = $_GET['Departamento'];
                $queryWhere = $queryWhere . "Departamento = '$Departamento' AND ";
            }

            if(!empty($_GET['Historico'])){
                $Historico = $_GET['Historico'];
                $queryWhere = $queryWhere . "Historico LIKE '%$Historico%' AND ";
            }

            $queryWhere = substr($queryWhere,0,-4);

        }



        /**
         *  IMPORTAR DIVIDAS
         *  */
        $resultDividas = [];
        if (isset($queryWhere) && isset($_GET['btImportarDividas'])) {

            $sql = "select * from v_aasi_acordo_confissao $queryWhere ORDER BY Ano ASC, Mes ASC, Valor ASC";
            $stmt = sqlsrv_query($conn, $sql);
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $resultDividas[] = $row;
            }
            sqlsrv_free_stmt($stmt);
            $contDividas = 0;
            $contParcelas = 0;

            foreach($resultDividas as $divida){
                if( substr($divida['Conta_contabil'], 0, 7) != '1139005'){
                    continue;
                }

                if(ignorarDivida($divida['Historico'])){
                    continue;
                }

                if($divida['Valor'] < 0){
                    $raAluno = validarHistoricoComColegio($divida['Historico']);
                    $raAluno2 = validaSegundoRA($divida['Historico']);

                    if(!empty($raAluno)){
                        $resultAluno = encontrarAlunoDividas($raAluno, $divida['Departamento'], $divida['Historico'] );

                        if(!empty($resultAluno)){

                            $valor = abs($divida['Valor']);
                            $ano = $divida['Ano'];
                            $mes = $divida['Mes'];
                            $quantidadeParcelas = validarQuantidadeDeParcelas($divida['Historico'], $ano);
                            $valorParcela = round($valor / $quantidadeParcelas, 2);
                            $dataInicialDivida = dataFormatada($ano, $mes);

                            $resultDivida = validarECriarDivida($resultAluno, $valor, $quantidadeParcelas, $dataInicialDivida);

                            if(!empty($resultDivida)){
                                $contDividas++;
                                $resultParcelas = validarECriarParcela($valor, $valorParcela, $quantidadeParcelas, $resultDivida['ID_DIVIDA'], $divida['Mes'], $divida['Ano']);
                                if($resultParcelas){
                                    $contParcelas += $resultParcelas;
                                }
                            }
                        }
                    }
                }
            }
            if($contDividas == 0 && $contParcelas == 0){
                echo "<script>errorImportacaoDividas();</script>";
            }else{
            echo "<script>successImportacaoDividas(".$contDividas.",".$contParcelas.");</script>";
            }
        }


?>


    </div>
    <br>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Importar Dívidas</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Dividas</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Importar</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Filtro para  <small> validação de dados</small></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                    <form action="" method="get">
                            <div class="form-group  row">
                            </div>
                            <div class="form-group row">

                            <div class="col-sm-4">
                                    <select class="form-control m-b" name="Departamento">
                                        <option>Selecione o Colegio</option>
                                        <?php
                                            $sql = "SELECT * FROM COLEGIO ORDER BY DEPARTAMENTO ASC";
                                            $stmt = sqlsrv_query($conn, $sql);
                                            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                echo "<option value='" . $row['DEPARTAMENTO'] . "'>" . $row['DEPARTAMENTO'] . " - " . $row['NOME'] . "</option>";
                                            }

                                            sqlsrv_free_stmt($stmt);

                                        ?>
                                    </select>
                                    </div>
                                <div class="col-sm-4">
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
                                <div class="col-sm-4">
                                    <select class="form-control m-b" name="Mes">
                                        <option>Selecione o mês</option>
                                        <option value='1'>Janeiro</option>
                                        <option value='2'>Fevereiro</option>
                                        <option value='3'>Março</option>
                                        <option value='4'>Abril</option>
                                        <option value='5'>Maio</option>
                                        <option value='6'>Junho</option>
                                        <option value='7'>Julho</option>
                                        <option value='8'>Agosto</option>
                                        <option value='9'>Setembro</option>
                                        <option value='10'>Outubro</option>
                                        <option value='11'>Novembro</option>
                                        <option value='12'>Dezembro</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-3"><input type="text" name="Historico" placeholder="Historico" class="form-control" ></div>
                                <div class="col-sm-3"><input type="text" name="Entidade" placeholder="Entidade" class="form-control" ></div>
                                <div class="col-sm-3"><input type="text" name="ContaContabil" placeholder="Conta Contabil" class="form-control" ></div>
                                <div class="col-sm-3"><input type="text" name="ContaCorrente" placeholder="Conta Corrente" class="form-control" ></div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group row">
                                <div class="col-sm-12 col-sm-offset-2">
                                    <button class="btn btn-white btn-lg" type="reset" >Limpar</button>
                                    <button class="btn btn-success btn-lg" name='btImportarDividas' type="submit">Importar dívidas</button>
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
                        <h5>Listando Responsávels cadastrados</h5>
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

                        <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-example" >
                    <thead>
                    <tr>
                        <th>Entidade</th>
                        <th>Período</th>
                        <th>Conta contabil</th>
                        <th>Conta corrente</th>
                        <th>Departamento</th>
                        <th>Historico</th>
                        <th>Recibo</th>
                        <th>Valor</th>
                    </tr>
                    </thead>
                    <tbody>


                    <?php
                        if (isset($queryWhere)) {
                            $sql = "select * from v_aasi_acordo_confissao $queryWhere ORDER BY Ano ASC, Mes ASC, Valor ASC";
                            $stmt = sqlsrv_query($conn, $sql);
                            if ($stmt === false) {
                                die(print_r(sqlsrv_errors(), true));
                            }
                            $vermelho = '';
                            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                if ($row['Valor'] < 0) {
                                    $vermelho = "style='color:red;'";
                                echo "<tr class='gradeA'>
                                    <td>" . $row['Entidade'] . "</td>
                                    <td>" . $row['Mes'] . '/' . $row['Ano'] . "</td>
                                    <td>" . $row['Conta_contabil'] . "</td>
                                    <td>" . $row['Conta_corrente'] . "</td>
                                    <td>" . $row['Departamento'] . "</td>
                                    <td>" . $row['Historico'] . "</td>
                                    <td>" . $row['Recibo'] . "</td>
                                    <td " . $vermelho . ">R$ " . number_format($row['Valor'], 2) .  "</td>
                                </tr>";
                            }
                                $vermelho = '';
                            }

                            sqlsrv_free_stmt($stmt);
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>Entidade</th>
                        <th>Período</th>
                        <th>Conta contabil</th>
                        <th>Conta corrente</th>
                        <th>Departamento</th>
                        <th>Historico</th>
                        <th>Recibo</th>
                        <th>Valor</th>
                    </tr>
                    </tfoot>
                    </table>
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

    <script src="js/plugins/dataTables/datatables.min.js"></script>
    <script src="js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>

    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function(){
            $('.dataTables-example').DataTable({
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    { extend: 'copy'},
                    {extend: 'csv'},
                    {extend: 'excel', title: 'ExampleFile'},
                    {extend: 'pdf', title: 'ExampleFile'},

                    {extend: 'print',
                     customize: function (win){
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');

                            $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                    }
                    }
                ]

            });

        });

    </script>

    <!-- iCheck -->
    <script src="js/plugins/iCheck/icheck.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        });
    </script>

<script type="text/javascript">
        $("#cep").focusout(function() {
            //Início do Comando AJAX
            $.ajax({
                //O campo URL diz o caminho de onde virá os dados
                //É importante concatenar o valor digitado no CEP
                url: 'https://viacep.com.br/ws/' + $(this).val() + '/json/unicode/',
                //Aqui você deve preencher o tipo de dados que será lido,
                //no caso, estamos lendo JSON.
                dataType: 'json',
                //SUCESS é referente a função que será executada caso
                //ele consiga ler a fonte de dados com sucesso.
                //O parâmetro dentro da função se refere ao nome da variável
                //que você vai dar para ler esse objeto.
                success: function(resposta) {
                    //Agora basta definir os valores que você deseja preencher
                    //automaticamente nos campos acima.
                    $("#logradouro").val(resposta.logradouro);
                    $("#complemento").val(resposta.complemento);
                    $("#bairro").val(resposta.bairro);
                    $("#cidade").val(resposta.localidade);
                    $("#uf").val(resposta.uf);
                    //Vamos incluir para que o Número seja focado automaticamente
                    //melhorando a experiência do usuário
                    $("#numero").focus();
                }
            });
        });
    </script>
    </body>

</html>