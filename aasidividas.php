

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

        if(isset($_GET['btFiltrar'])){

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
                                            if ($stmt === false) {
                                                die(print_r(sqlsrv_errors(), true));
                                            }

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
                                    <button class="btn btn-success btn-lg" name='btFiltrar' type="submit">Filtrar</button>
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
                        <th>Período</th>
                        <th>Conta contabil</th>
                        <th>Departamento</th>
                        <th>Historico</th>
                        <th>Recibo</th>
                        <th style='width:15%;'>Valor</th>
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
            }
            echo "<tr class='gradeA'>
                <td>" . $row['Mes'] . '/' . $row['Ano'] . "</td>
                <td>" . substr($row['Conta_contabil'], 0, 7) . "</td>
                <td>" . $row['Departamento'] . "</td>
                <td>" . $row['Historico'] . "</td>
                <td>" . $row['Recibo'] . "</td>
                <td " . $vermelho . ">R$ " . number_format($row['Valor'], 2,",",".") .  "       </td>
            </tr>";
            $vermelho = '';
        }

        sqlsrv_free_stmt($stmt);
    }
}
?>
                
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>Período</th>
                        <th>Conta contabil</th>
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
                    {extend: 'excel', title: 'Confissão de dívidas'},
                    {extend: 'pdf', title: 'Confissão de dívidas'},

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