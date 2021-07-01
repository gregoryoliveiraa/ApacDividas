

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

            $queryWhere = "WHERE ";
            $cont = 0;
            $podeRemover = false;

            if(!empty($_GET['Cod_Aluno'])){
                $Cod_Aluno = $_GET['Cod_Aluno'];
                $queryWhere = $queryWhere . "Cod_Aluno = '$Cod_Aluno' AND ";
                $podeRemover = true;
            }

            if(!empty($_GET['Aluno'])){
                $Aluno = $_GET['Aluno'];
                $queryWhere = $queryWhere . "Aluno LIKE '%$Aluno%' AND ";
                $podeRemover = true;
            }

            if(!empty($_GET['ResponsavelFinanceiro'])){
                $ResponsavelFinanceiro = $_GET['ResponsavelFinanceiro'];
                $queryWhere = $queryWhere . "ResponsavelFinanceiro LIKE '%$ResponsavelFinanceiro%' AND ";
                $podeRemover = true;
            }

            if(!empty($_GET['Departamento']) && is_numeric($_GET['Departamento'])){
                $Departamento = $_GET['Departamento'] == 1010 ? 1001 : str_replace('10', '01', $_GET['Departamento']);
                $queryWhere = $queryWhere . "Cod_Escola = $Departamento AND ";
                $podeRemover = true;
            }

            if($podeRemover){
                $queryWhere = substr($queryWhere,0,-4);
            }else{
                $queryWhere = substr($queryWhere,0,-6);
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
                            <div class="col-sm-6"><input type="text" name="Cod_Aluno" placeholder="RA" class="form-control" ></div>
                            <div class="col-sm-6">
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
                            </div>

                            <div class="form-group row">
                                    <div class="col-sm-6"><input type="text" name="Aluno" placeholder="Nome aluno" class="form-control" ></div>
                                    <div class="col-sm-6"><input type="text" name="ResponsavelFinanceiro" placeholder="Nome Responsavel" class="form-control" ></div>
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
                        <th>RA Aluno</th>
                        <th>Nome Aluno</th>
                        <th>Departamento</th>
                        <th>Resp. Financeiro</th>
                        <th>Celular</th>
                        <th>Email</th>
                        <th>Parcelas em atraso/Valor</th>
                    </tr>
                    </thead>
                    <tbody>


<?php
    if (isset($queryWhere)) {
        $sql = "SELECT Cod_Aluno, Aluno, Cod_Escola, ResponsavelFinanceiro, Celular_Resp_Financeiro, Email_Resp_Financeiro, Parcelas_em_Atraso, Valor_Pendencias_Resp_Fin 
        FROM APAC_DIVIDAS.dbo.v_web_escola_dados_alunos $queryWhere";
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        while ($row = sqlsrv_fetch_array($stmt)) {
            echo "<tr class='gradeA'>
                <td>" . $row['Cod_Aluno'] . "</td>
                <td>" . $row['Aluno'] . "</td>
                <td>" . $row['Cod_Escola'] . "</td>
                <td>" . $row['ResponsavelFinanceiro'] . "</td>";
                if(empty($row['Celular_Resp_Financeiro'])){ echo "<td> --</td>";} else{ echo "<td><a href='tel:" . $row['Celular_Resp_Financeiro'] . "'>" . $row['Celular_Resp_Financeiro'] . "</a></td>";}
                if(empty($row['Email_Resp_Financeiro'])){ echo "<td> --</td>";} else{ echo "<td><a href='email:" . $row['Email_Resp_Financeiro'] . "'>" . $row['Email_Resp_Financeiro'] . "</a></td>";}
                if(empty($row['Valor_Pendencias_Resp_Fin'])){ echo "<td> --</td>";} else{ echo "<td>" . $row['Parcelas_em_Atraso'] . " / " . number_format($row['Valor_Pendencias_Resp_Fin'], 2,",",".") . " </td>";}
             echo "</tr>";
        }

        sqlsrv_free_stmt($stmt);
    }
}
?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>RA Aluno</th>
                        <th>Nome Aluno</th>
                        <th>Departamento</th>
                        <th>Resp. Financeiro</th>
                        <th>Celular</th>
                        <th>Email</th>
                        <th>Parcelas em atraso/Valor</th>
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