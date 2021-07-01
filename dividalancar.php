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

        function erroAluno(){
            Swal.fire(
            'Aluno não localizado',
            'Este aluno não pertence ao seu colégio ou não existe.',
            'warning'
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
    
        if($retornoProtegepagina){
        require('menu.php'); 
        require('conexao.php');
        
        $acessoUsuario = 0;
        $idColegioSessao = 0;
        if(isset($_SESSION['usuarioAcesso']) && isset($_SESSION['usuarioIdColegio'])){
            if($_SESSION['usuarioAcesso'] == 1 && $_SESSION['usuarioIdColegio'] > 0){
                $acessoUsuario = $_SESSION['usuarioAcesso'];
                $idColegioSessao = $_SESSION['usuarioIdColegio'];
            }
        }

        if ((isset($_GET['buscarDados']) || isset($_GET['raPesquisa'])) && !isset($_POST['btNovo'])){

            $raAluno = $_GET['raPesquisa'];
            $idColegio = isset($_GET['id_colegio']) ? $_GET['id_colegio'] : $_SESSION['usuarioIdColegio'];

            $sql = "SELECT A.NOME AS NOMEALUNO, R.NOME AS NOMER, C.NOME AS NOMEC, * FROM ALUNO AS A
            LEFT JOIN COLEGIO AS C ON A.ID_COLEGIO = C.ID_COLEGIO
            LEFT JOIN RESPONSAVEL AS R ON A.ID_RESPONSAVEL = R.ID_RESPONSAVEL
            WHERE A.RA = " . $raAluno . " AND A.ID_COLEGIO = $idColegio";
            $stmt = sqlsrv_query($conn, $sql);
            $rowAluno = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            sqlsrv_free_stmt($stmt);

            var_export($rowAluno);

            if(empty($rowAluno)){

                $sql = "SELECT NOME, DEPARTAMENTO FROM COLEGIO WHERE ID_COLEGIO = $idColegio";
                $stmt = sqlsrv_query($conn, $sql);
                $rowColegio = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                sqlsrv_free_stmt($stmt);

                if(!empty($rowColegio['DEPARTAMENTO'])){
                    $departamentoDiv = $rowColegio['DEPARTAMENTO'] == 1010 ? 1001 : str_replace('10', '01', $rowColegio['DEPARTAMENTO']);
                    $sql = "SELECT *
                    FROM APAC_DIVIDAS.dbo.v_web_escola_dados_alunos WHERE Cod_Aluno = $raAluno AND Cod_Escola = $departamentoDiv";
                    $stmt = sqlsrv_query($conn, $sql);
                    $rowAlunoASSI = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                    sqlsrv_free_stmt($stmt);

                    if(!empty($rowAlunoASSI['Cod_Aluno'])){
                        //Aluno
                        $rowAluno['RA'] = $rowAlunoASSI['Cod_Aluno'];
                        $rowAluno['NOMEALUNO'] = $rowAlunoASSI['Aluno'];
                        $rowAluno['ID_COLEGIO'] = $idColegio;
                        $rowAluno['DEPARTAMENTO'] = $rowColegio['DEPARTAMENTO'];
                        $rowAluno['NOMEC'] = $rowColegio['NOME'];
                        $rowAluno['ID_RESPONSAVEL'] = "";

                        //Responsável
                        $rowAluno['NOMER'] = $rowAlunoASSI['ResponsavelFinanceiro'];
                        $rowAluno['RG'] = $rowAlunoASSI['RG_Resp_Financeiro'];
                        $rowAluno['CPF'] = $rowAlunoASSI['CPF_Resp_Financeiro'];
                        $rowAluno['CELULAR'] = $rowAlunoASSI['Celular_Resp_Financeiro'];
                        $rowAluno['EMAIL'] = $rowAlunoASSI['Email_Resp_Financeiro'];
                        $rowAluno['RUA'] = $rowAlunoASSI['Endereco_Resp_Financeiro'];
                        $rowAluno['NUMERO'] = 0;
                        $rowAluno['BAIRRO'] = $rowAlunoASSI['Bairro_Resp_Financeiro'];
                        $rowAluno['CIDADE'] = $rowAlunoASSI['Cidade_Resp_Financeiro'];
                        $rowAluno['CEP'] = $rowAlunoASSI['CEP_Resp_Financeiro'];
                        $rowAluno['ESTADO'] = $rowAlunoASSI['UF_Resp_Financeiro'];
                        $rowAluno['COMPLEMENTO'] = "";
                        $rowAluno['CADASTRAR'] = true;
                    }

                }
            }else{
                $rowAluno['CADASTRAR'] = false;
            }
        }


        function dataFormatada($ano, $mes){

            if($mes < 10){
                $zero = 0;
            }else{
                $zero = '';
            }
            $data = $ano . "-" . $mes . "-10";

            return $data;
        }

        function dateEmSQL($dateSql)
        {
            $ano = substr($dateSql, 6);
            $mes = substr($dateSql, 3, -5);
            $dia = substr($dateSql, 0, -8);
            return $ano . "-" . $mes . "-" . $dia;
        }


        if(isset($_POST['btNovo']))
        {
            if(!isset($_POST['ID_ALUNO']))
            {

                $tsql= "INSERT INTO RESPONSAVEL (NOME, RG, CPF, CELULAR, EMAIL, RUA, BAIRRO, CIDADE, CEP, ESTADO) VALUES (
                    '{$_POST['NOMER']}', 
                    '{$_POST['RG']}', 
                    '{$_POST['CPF']}', 
                    '{$_POST['CELULAR']}', 
                    '{$_POST['EMAIL']}', 
                    '{$_POST['RUA']}',
                    '{$_POST['BAIRRO']}', 
                    '{$_POST['CIDADE']}', 
                    '{$_POST['CEP']}', 
                    '{$_POST['ESTADO']}')";
                if (!sqlsrv_query($conn, $tsql, $var))
                {
                    die('Erro ao cadastrar Responsavel: ' . sqlsrv_errors());
                }

                $sql = "SELECT ID_RESPONSAVEL FROM RESPONSAVEL WHERE NOME = '{$_POST['NOMER']}' AND EMAIL = '{$_POST['EMAIL']}'";
                $stmt = sqlsrv_query($conn, $sql);
                $rowResponsavel = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                sqlsrv_free_stmt($stmt);

                if($rowResponsavel['ID_RESPONSAVEL'])
                {
                    $tsql= "INSERT INTO ALUNO (NOME, RA, ID_COLEGIO, ID_RESPONSAVEL) VALUES (
                            '{$_POST['NOMEALUNO']}', '{$_POST['RA']}', '{$_POST['ID_COLEGIO']}', '{$rowResponsavel['ID_RESPONSAVEL']}')";
                    if (!sqlsrv_query($conn, $tsql))
                    {
                        die('Erro ao cadastrar aluno: ' . sqlsrv_errors());
                    }

                    $sql = "SELECT ID_ALUNO FROM ALUNO WHERE RA = {$_POST['RA']}";
                    $stmt = sqlsrv_query($conn, $sql);
                    $rowAluno = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                    sqlsrv_free_stmt($stmt);
                }
            }

            $_POST['ID_ALUNO'] = isset($rowAluno['ID_ALUNO']) ? $rowAluno['ID_ALUNO'] : $_POST['ID_ALUNO'];
            $totalDivida = preg_replace('/[^0-9]/', '', $_POST['TOTAL']);
            $entradaDivida = preg_replace('/[^0-9]/', '', $_POST['ENTRADA']);
            $quantidadeParcelasDivida = preg_replace('/[^0-9]/', '', $_POST['QTD_PARCELAS']);
            $ano = substr($_POST['DATA_INICIAL'], 0, 4);
            $mes = substr($_POST['DATA_INICIAL'], 5, 2);
            $dataDivida = $_POST['DATA_INICIAL'];

            if(isset($_POST['ID_ALUNO']))
            {
                $tsql= "INSERT INTO DIVIDA (ALUNO_ID_ALUNO, TOTAL, ENTRADA, QTD_PARCELAS, VALOR_PARCELA, DATA_INICIAL, STATUS_DIV) VALUES(
                    '{$_POST['ID_ALUNO']}', 
                    '{$totalDivida}', 
                    '{$entradaDivida}', 
                    '{$quantidadeParcelasDivida}', 
                    '1', 
                    '{$dataDivida}',
                    'Em aprovação')";
                if (!sqlsrv_query($conn, $tsql))
                {
                    die('Erro em cadastrar divida: ' . sqlsrv_errors());
                }

                $sql = "SELECT ID_DIVIDA FROM DIVIDA WHERE ALUNO_ID_ALUNO = {$_POST['ID_ALUNO']} AND TOTAL = {$totalDivida} AND QTD_PARCELAS = {$quantidadeParcelasDivida}";
                $stmt = sqlsrv_query($conn, $sql);
                $rowDivida= sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                sqlsrv_free_stmt($stmt);

                if(isset($rowDivida['ID_DIVIDA']))
                {
                    $valorParcela = ( $totalDivida - $entradaDivida ) / $quantidadeParcelasDivida;
                    
                    if($entradaDivida){
                        $dataParcela = dataFormatada($ano, $mes);
                        $mes++;
                        if($mes >= 13)
                        {
                            $ano++;
                            $mes = 1;
                        }

                        $tsql= "INSERT INTO PARCELA ( DIVIDA_ID_DIVIDA, NUMERO, VALOR, STATUS_PARCELA, FORMA_PAGAMENTO, DATA_VENCIMENTO) VALUES (
                        '{$rowDivida['ID_DIVIDA']}', 
                        '1', 
                        '{$entradaDivida}', 
                        'Em Aberto', 
                        'Boleto', 
                        '{$dataParcela}')";
                        if (!sqlsrv_query($conn, $tsql))
                        {
                            die('Erro em cadastrar entrada: ' . sqlsrv_errors());
                        }
                    }
                    for($i = 1; $i <= $quantidadeParcelasDivida; $i++)
                    {
                        $dataParcela = dataFormatada($ano, $mes);
                        $mes++;
                        if($mes >= 13)
                        {
                            $ano++;
                            $mes = 1;
                        }
                        $tsql= "INSERT INTO PARCELA ( DIVIDA_ID_DIVIDA, NUMERO, VALOR, STATUS_PARCELA, FORMA_PAGAMENTO, DATA_VENCIMENTO) VALUES (
                        '{$rowDivida['ID_DIVIDA']}', 
                        '{$i}', 
                        '{$valorParcela}', 
                        'Em Aberto', 
                        'Boleto', 
                        '{$dataParcela}')";
                        if (!sqlsrv_query($conn, $tsql))
                        {
                            die('Erro em cadastrar parcela: ' . sqlsrv_errors());
                        }
                    }
                }
            }
            echo "<script>successCadastro();</script>";
            echo "<meta http-equiv='refresh' content='1;url=dividas.php?btFiltrarDivida=1&idDivida={$rowDivida['ID_DIVIDA']}'>"; 
        }

        if(isset($_GET['buscarDados']) && empty($rowAluno['NOMEALUNO']) && !isset($_POST['btNovo'])){
            echo "<script>erroAluno();</script>";
            echo "<meta http-equiv='refresh' content='3;url=dividalancar.php'>";
            die();
        }
        else
        {
?>


    </div>
    <br>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Dívida</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Lançar</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Cadastro</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Dados</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">


    <? if (!isset($rowAluno)) { ?>
        <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Iniciar 
                            <small>criação de dívida</small>
                        </h5>
                    </div>
                    <div class="ibox-content">
                        <form method="GET" action="">
                            <div class="form-group  row">

                                <?php if(empty($_SESSION['usuarioIdColegio'])){ ?>
                                    <div class="col-sm-4">
                                        <select class="form-control m-b" name="id_colegio" required>
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
                                <? }?>

                                <div class="col-sm-4"><input type="text" name="raPesquisa" placeholder="(RA) Registro Academico " <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['RA']. "'"; }?> class="form-control" required></div>

                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-success btn-lg" type="submit" name="buscarDados">  <i class="fa fa-search"></i> Iniciar criação de dívida</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
        
        <?php

        }else{

        ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Cadastro <small> confissão de dívida</small></h5>
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

                            <div class="panel-body">
                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-success">
                                        <div class="panel-heading">
                                            <h5 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><i class="fa fa-arrow-circle-down"></i>  Verificar dados do aluno <b><?php echo $rowAluno['NOMEALUNO'] . " - " . $rowAluno['RA'];?></b> </a>
                                            </h5>
                                        </div>
                                        <div id="collapseOne" class="panel-collapse collapse in">
                                            <div class="panel-body">
                                                <div class="form-group  row">
                                                    <div class="col-sm-6">
                                                        <ul>
                                                            <h2>Aluno:</h2>
                                                            <?php
                                                                echo "<li><h3>RA</h3> " . $rowAluno['RA'] . "</li>";
                                                                echo "<li><h3>Nome</h3> " . $rowAluno['NOMEALUNO'] . "</li>";
                                                                echo "<li><h3>Departamento</h3> " . $rowAluno['DEPARTAMENTO'] . " - "  . $rowAluno['NOMEC'] . " </li>";
                                                            ?>
                                                        </ul>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <ul>
                                                            <h2>Responsável:</h2>
                                                            <?php
                                                                echo "<li><b>Nome</b>: " . $rowAluno['NOMER'] . "</li>";
                                                                echo "<li><b>RG</b>: " . $rowAluno['RG'] . "</li>";
                                                                echo "<li><b>CPF</b>: " . $rowAluno['CPF'] . "</li>";
                                                                echo "<li><b>Celular</b>: " . $rowAluno['CELULAR'] . "</li>";
                                                                echo "<li><b>E-mail</b>: " . $rowAluno['EMAIL'] . "</li>";
                                                                echo "<li><b>Rua</b>: " . $rowAluno['RUA'] . "</li>";
                                                                echo "<li><b>Bairro</b>: " . $rowAluno['BAIRRO'] . "</li>";
                                                                echo "<li><b>Cidade</b>: " . $rowAluno['CIDADE'] . "</li>";
                                                                echo "<li><b>Estado</b>: " . $rowAluno['ESTADO'] . "</li>";
                                                                echo "<li><b>CEP</b>: " . $rowAluno['CEP'] . "</li>";
                                                            ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <br></br>

                        <form action="" method="post">

                            <div class="form-group  row">
                                <div class="col-sm-6"><input name="DATA_INICIAL" 
                                    <?php if (isset($_GET['id']) && !isset($_POST['btNovo'])) {
                                            echo "value='" . $row['DATA_INICIAL']->format('d/m/Y') . "'";
                                        } else {
                                            echo "type='date'";
                                        } 
                                    ?> placeholder="Data inicial" class="form-control" required></div>
                                <div class="col-sm-6"><input type="text" name="QTD_PARCELAS" placeholder="Quantidade de Parcelas" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['RG']. "'"; }?> required class="form-control"></div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6"><input type="text" name="TOTAL" placeholder="Valor Total" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['CPF']. "'"; }?> required class="form-control"></div>
                                <div class="col-sm-6"><input type="text" name="ENTRADA" placeholder="Entrada (Opcional)" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['EMAIL']. "'"; }?> class="form-control"></div>
                            </div>

                            <?php 
                                if($rowAluno['CADASTRAR']){
                                    echo "
                                        <input type='hidden' name='RA' value='{$rowAluno['RA']}'>
                                        <input type='hidden' name='NOMEALUNO' value='{$rowAluno['NOMEALUNO']}'>
                                        <input type='hidden' name='ID_COLEGIO' value='{$rowAluno['ID_COLEGIO']}'>

                                        <input type='hidden' name='NOMER' value='{$rowAluno['NOMER']}'>
                                        <input type='hidden' name='RG' value='{$rowAluno['RG']}'>
                                        <input type='hidden' name='CPF' value='{$rowAluno['CPF']}'>
                                        <input type='hidden' name='CELULAR' value='{$rowAluno['CELULAR']}'>
                                        <input type='hidden' name='EMAIL' value='{$rowAluno['EMAIL']}'>
                                        <input type='hidden' name='RUA' value='{$rowAluno['RUA']}'>
                                        <input type='hidden' name='NUMERO' value='{$rowAluno['NUMERO']}'>
                                        <input type='hidden' name='BAIRRO' value='{$rowAluno['BAIRRO']}'>
                                        <input type='hidden' name='CIDADE' value='{$rowAluno['CIDADE']}'>
                                        <input type='hidden' name='CEP' value='{$rowAluno['CEP']}'>
                                        <input type='hidden' name='ESTADO' value='{$rowAluno['ESTADO']}'>
                                        <input type='hidden' name='COMPLEMENTO' value='{$rowAluno['COMPLEMENTO']}'>
                                        ";
                                }else{
                                    echo "
                                        <input type='hidden' name='ID_ALUNO' value='{$rowAluno['ID_ALUNO']}'>
                                        ";
                                }
                            ?>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group row">
                                <div class="col-sm-4 col-sm-offset-2">
                                <button name="btNovo" class="btn btn-success btn-lg" type="submit"><i class="fa fa-arrow-circle-down"></i> Lançar confissão de dívida para aprovação</button>
                                <?php if(!isset($_GET['id']) || isset($_POST['btNovo'])){ ?>
                                    <button class="btn btn-white btn-lg" type="reset" >Limpar</button>
                                <? } ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <? } ?>


           
            </div>
        </div>
    <?php } 

        }require("footer.php"); ?>

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