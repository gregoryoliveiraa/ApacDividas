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
                    window.location.href = "aluno.php?deletar=1&idDeletar="+id
                }
            })
        };

    </script>

    <?php
        include("loadingPage.php");
        if (!isset($_POST['btEdicao']) && !isset($_POST['btNovo'])){
            include("valida.php"); 
            $retornoProtegepagina = protegePagina();
        }else{
            $retornoProtegepagina = 1;
        }
        
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
        
        if (isset($_POST['btEdicao'])){

            $nome  = $_POST['nome'];
            $ra = $_POST['ra'];
            $id_colegio = $_POST['id_colegio'];
            $id_responsavel = $_POST['id_responsavel'];

            if(isset($_POST['idaluno'])){

                $idaluno = $_POST['idaluno'];

                $tsql= "UPDATE ALUNO SET
                    NOME = ?,
                    RA = ?,
                    ID_COLEGIO = ?,
                    ID_RESPONSAVEL = ?
                    WHERE ID_ALUNO = ?";
    
                $var = array($nome, $ra, $id_colegio, $id_responsavel, $idaluno);
    
                if (!sqlsrv_query($conn, $tsql, $var))
                {
                    die('Erro: ' . sqlsrv_errors());
                }
                echo "<script>successEdicao();</script>";
                echo "<meta http-equiv='refresh' content='1;url=aluno.php?id=" . $idaluno . "'>"; 
                
            }else{
        
            $tsql= "INSERT INTO ALUNO (
                NOME,
                RA,
                ID_COLEGIO,
                ID_RESPONSAVEL) 
                VALUES (?, ?, ?, ?)";

            $var = [$nome, $ra, $id_colegio, $id_responsavel];

            if (!sqlsrv_query($conn, $tsql, $var))
            {
                die('Erro: ' . sqlsrv_errors());
            }
            echo "<script>successCadastro();</script>";
            echo "<meta http-equiv='refresh' content='1;url=aluno.php'>"; 
            }

        }

        if (isset($_GET['deletar'])){
            
            $tsql= "DELETE FROM ALUNO WHERE ID_ALUNO = " . $_GET['idDeletar'];

            if (!sqlsrv_query($conn, $tsql))
            {
                die('Erro: ' . sqlsrv_errors());
            }
            echo "<script>successDeletar();</script>";
            echo "<meta http-equiv='refresh' content='1;url=aluno.php'>"; 
        }
        


if(isset($_GET['id']) && !isset($_POST['btNovo'])){

    $sql = "SELECT * FROM ALUNO WHERE ID_ALUNO = " . $_GET['id'];
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
}

if(!isset($row['ID_ALUNO'])){
    $_GET['id'] = null;
}

?>

    </div>
    <br>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Alunos</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Cadastros</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Aluno</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">

    <?php if(!$acessoUsuario){ ?>
        <div class="row">
            <div class="col-lg-12">
            <?php
                $collapsed = "";
                if (!isset($row['ID_ALUNO'])) {
                    $_GET['id'] = null;
                    $collapsed = "collapsed";
                }
                ?>
                <div class="ibox <?php echo $collapsed; ?>">
                    <div class="ibox-title">
                        <h5>Cadastro <small> e controle</small></h5>
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
                        <form action="" method="post">
                            <div class="form-group  row">
                                
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4"><input type="text" name="ra" placeholder="(RA) Registro Academico " <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['RA']. "'"; }?> class="form-control" required></div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-8"><input type="text" name="nome" placeholder="Nome" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['NOME']. "'"; }?>class="form-control" required></div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-8">
                                    <select class="form-control m-b" name="id_responsavel">
                                        <?php if(!isset($_GET['id'])){?>
                                            <option>Selecione o Responsável</option>
                                        <?php }
                                            $pesquisaAluno = '';
                                            if(isset($_GET['id'])){
                                                $pesquisaAluno = "WHERE ID_RESPONSAVEL = ".$row['ID_RESPONSAVEL'];
                                                $sql = "SELECT * FROM RESPONSAVEL $pesquisaAluno ORDER BY NOME ASC";
                                                $stmt = sqlsrv_query($conn, $sql);
                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }

                                                while ($rowResponsavel = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                    echo "<option value='" . $rowResponsavel['ID_RESPONSAVEL'] . "'>" . $rowResponsavel['NOME'] . "</option>";
                                                }
                                                sqlsrv_free_stmt($stmt);
                                            }

                                            $sql = "SELECT * FROM RESPONSAVEL ORDER BY NOME ASC";
                                            $stmt = sqlsrv_query($conn, $sql);
                                            if ($stmt === false) {
                                                die(print_r(sqlsrv_errors(), true));
                                            }

                                            while ($rowResponsavel = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                echo "<option value='" . $rowResponsavel['ID_RESPONSAVEL'] . "'>" . $rowResponsavel['NOME'] . "</option>";
                                            }

                                            sqlsrv_free_stmt($stmt);
                                        ?>
                                    </select>
                                        </div>
                                    <div class="col-sm-4">
                                    <a href='responsavel.php' title='Cadastre o responsável'><button class='btn btn-success btn-circle' type='button'><i class='fa fa-address-book'></i></button></a>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-8">
                                    <select class="form-control m-b" name="id_colegio">
                                    <?php if(!isset($_GET['id'])){?>
                                        <option>Selecione o Colégio</option>
                                    <?php }
                                            $pesquisaColegio = '';
                                            if(isset($_GET['id'])){
                                                $pesquisaColegio = "WHERE ID_COLEGIO = ".$row['ID_COLEGIO'];
                                                echo $pesquisaColegio;
                                                $sql = "SELECT * FROM COLEGIO $pesquisaColegio ORDER BY NOME ASC";
                                                $stmt = sqlsrv_query($conn, $sql);
                                                if ($stmt === false) {
                                                    die(print_r(sqlsrv_errors(), true));
                                                }

                                                while ($rowColegio = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                                    echo "<option value='" . $rowColegio['ID_COLEGIO'] . "'>" . $rowColegio['DEPARTAMENTO'] . " - " .$rowColegio['NOME'] . "</option>";
                                                }
                                                sqlsrv_free_stmt($stmt);
                                            }

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
                                    <div class="col-sm-4">
                                    <a href='colegio.php' title='Cadastre o colégio'><button class='btn btn-success btn-circle' type='button'><i class='fa fa-building'></i></button></a>
                                </div>
                            </div>

                            <?php
                            if(isset($_GET['id']) && !isset($_POST['btNovo'])){
                                echo ' <input type="hidden" name="idaluno" value="' .$row['ID_ALUNO']. '"> ';
                            }
                            ?>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group row">
                                <div class="col-sm-4 col-sm-offset-2">
                                <button name="btEdicao" class="btn btn-success btn-lg" type="submit"><?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "Salvar edição"; } else { echo "Cadastrar"; } ?></button>
                                <?php if(!isset($_GET['id']) || isset($_POST['btNovo'])){ ?>
                                    <button class="btn btn-white btn-lg" type="reset" >Limpar</button>
                                <? } else if(!isset($_POST['btNovo'])){ ?>
                                    <button class="btn btn-white btn-lg" name="btNovo">Cadastrar NOVO</button>
                                <? } ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <? } ?>
            <div class="row">
                <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Listando Alunos cadastrados</h5>
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
                        <th>ID</th>
                        <th>Nome</th>
                        <th>RA</th>
                        <th>Colegio</th>
                        <th>Responsavel</th>
                        <?php if(!$acessoUsuario){ ?><th>Ações</th><? } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        $whereClause = "";
                        if($idColegioSessao > 0){
                            $whereClause = "WHERE C. ID_COLEGIO = {$idColegioSessao} ORDER BY A.NOME ASC";
                        } 

                        $sql = "SELECT A.ID_ALUNO AS ID_ALUNO, A.NOME AS NOMEALUNO, A.RA AS RA, C.NOME AS NOMEC, C.ID_COLEGIO AS ID_COLEGIO, R.ID_RESPONSAVEL AS ID_RESPONSAVEL, R.NOME AS NOMER FROM ALUNO AS A
                        LEFT JOIN COLEGIO AS C ON A.ID_COLEGIO = C.ID_COLEGIO
                        LEFT JOIN RESPONSAVEL AS R ON A.ID_RESPONSAVEL = R.ID_RESPONSAVEL {$whereClause}";

                        $stmt = sqlsrv_query($conn, $sql);
                        if ($stmt === false) {
                            die(print_r(sqlsrv_errors(), true));
                        }

                        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                            echo "<tr class='gradeA'>
                                        <td>" . $row['ID_ALUNO'] . "</td>
                                        <td>" . $row['NOMEALUNO'] . "</td>
                                        <td>" . $row['RA'] . "</td>
                                        <td>";
                                        if(!$acessoUsuario){ echo "<a href='colegio.php?id=".$row['ID_COLEGIO']."' target='new_blank'>"; } echo $row['NOMEC'] . "</a></td>
                                        <td> <a href='responsavel.php?id=".$row['ID_RESPONSAVEL']."' target='new_blank'>" . $row['NOMER'] . "</a></td>
                                        ";
                                        if(!$acessoUsuario){
                                            echo "<td class='center'>
                                            <a href='aluno.php?id=".$row['ID_ALUNO']."' title='Editar'><button class='btn btn-success btn-circle' type='button'><i class='fa fa-edit'></i></button></a>&ensp;
                                            <a href='javascript:deletar(".$row['ID_ALUNO'].");' title='Deletar'><button class='btn btn-danger btn-circle' type='button'><i class='fa fa-trash'></i></button></a>
                                            </td>";
                                        }
                                    echo "</tr>";
                                    }
                                    sqlsrv_free_stmt($stmt);
                                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>RA</th>
                        <th>Colegio</th>
                        <th>Responsavel</th>
                        <?php if(!$acessoUsuario){ ?><th>Ações</th><? } ?>
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
                    {extend: 'excel', title: 'ListaAlunos'},
                    {extend: 'pdf', title: 'ListaAlunos'},

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