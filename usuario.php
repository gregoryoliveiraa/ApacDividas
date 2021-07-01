
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
                    window.location.href = "usuario.php?deletar=1&idDeletar="+id
                }
            })  
        };

    </script>

    <?php
    if (!isset($_POST['btUsuario']) && !isset($_POST['btNovo'])){
        include("valida.php"); 
        $retornoProtegepagina = protegePagina();
    }else{
        $retornoProtegepagina = 1;
    }

    if($retornoProtegepagina){
    require('menu.php'); 
    require('conexao.php');
        
        if (isset($_POST['btUsuario'])){
    
            $nome  = $_POST['nome'];
            $email = $_POST['email'];
            $usuario = $_POST['usuario'];
            $senha  = base64_encode($_POST['senha']);
            $acesso = $_POST['acesso'];
            $id_colegio = $_POST['id_colegio'];

            if(isset($_POST['idusuario'])){

                $idusuario = $_POST['idusuario'];

                $tsql= "UPDATE USUARIO SET
                    NOME = ?,
                    EMAIL = ?,
                    USUARIO = ?,
                    SENHA = ?,
                    ACESSO = ?,
                    ID_COLEGIO = $id_colegio 
                    WHERE ID_USUARIO = ?";
    
                $var = array($nome, $email, $usuario, $senha, $acesso,  $idusuario);
    
                if (!sqlsrv_query($conn, $tsql, $var))
                {
                    die('Erro: ' . sqlsrv_errors());
                }
                echo "<script>successEdicao();</script>";
                echo "<meta http-equiv='refresh' content='1;url=usuario.php?id=" . $idusuario . "'>"; 
                
            }else{
        
            $tsql= "INSERT INTO USUARIO (
                NOME,
                EMAIL,
                USUARIO,
                SENHA,
                ACESSO,
                ID_COLEGIO) 
                VALUES (?, ?, ?, ?, ?, ?)";

            $var = [$nome, $email, $usuario, $senha, $acesso, $id_colegio];

            if (!sqlsrv_query($conn, $tsql, $var))
            {
                die('Erro: ' . sqlsrv_errors());
            }
            echo "<script>successCadastro();</script>";
            echo "<meta http-equiv='refresh' content='1;url=usuario.php'>"; 
            }

        }

        if (isset($_GET['deletar'])){
            
            $tsql= "DELETE FROM USUARIO WHERE ID_USUARIO = " . $_GET['idDeletar'];

            if (!sqlsrv_query($conn, $tsql))
            {
                die('Erro: ' . sqlsrv_errors());
            }
            echo "<script>successDeletar();</script>";
            echo "<meta http-equiv='refresh' content='1;url=usuario.php'>"; 
        }
        


if(isset($_GET['id']) && !isset($_POST['btNovo'])){

    $sql = "SELECT * FROM USUARIO WHERE ID_USUARIO = " . $_GET['id'];
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        switch ($row['ACESSO']) {
            case 1:
                $acesso = "Vizualização";
                break;
            case 2:
                $acesso = "Vizualização/Edição";
                break;
            case 3:
                $acesso = "Vizualização/Edição/Administração";
                break;
            default:
                $acesso = "Nível de acesso indefinido";
        }
    sqlsrv_free_stmt($stmt);
}

?>

</div>
    <br>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Usuários</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Controle</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Usuário</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>

    
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
            <?php
                $collapsed = "";
                if (!isset($row['ID_USUARIO'])) {
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
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form action="" method="post">
                            <div class="form-group  row">
                                <div class="col-sm-12"><input type="text" name="nome" placeholder="Nome" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['NOME']. "'"; }?> class="form-control" required></div>
                            </div>
                            <div class="form-group  row">
                                <div class="col-sm-12"><input type="email" name="email" placeholder="Email" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['EMAIL']. "'"; }?> class="form-control" required></div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6"><input type="text" name="usuario" placeholder="Usuário" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['USUARIO']. "'"; }?> class="form-control" required></div>
                                <div class="col-sm-6"><input type="text" name="senha" placeholder="Senha" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".base64_decode($row['SENHA']). "'"; }?> class="form-control" required></div>
                            </div>

                            <?php 
                            if(isset($_GET['id']) && !isset($_POST['btNovo'])){ 
                                echo ' <input type="hidden" name="idusuario" value="' .$row['ID_USUARIO']. '"> ';
                            }
                            ?>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <select class="form-control m-b" name="acesso" id="uf">
                                        <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "<option value='". $row['ACESSO']."'>" . $acesso. " </option>"; }?>
                                        <option>Selecione o nivel de acesso</option>
                                        <option value="1">Vizualização</option>
                                        <option value="2">Vizualização/Edição</option>
                                        <option value="3">Vizualização/Edição/Administração</option>
                                    </select>
                                </div>
                           
                                <div class="col-sm-6">
                                    <select class="form-control m-b" name="id_colegio">
                                    <?php if(!isset($row['ID_COLEGIO'])){?>
                                        <option>Selecione o Colégio</option>
                                    <?php }
                                            $pesquisaColegio = '';
                                            if(isset($row['ID_COLEGIO'])){
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
                                        <option value='NULL'>Remover colégio deste usuário</option>
                                    </select>
                                    </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group row">
                                <div class="col-sm-4 col-sm-offset-2">
                                <button name="btUsuario" class="btn btn-success btn-lg" type="submit"><?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "Salvar edição"; } else { echo "Cadastrar"; } ?></button>
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
        
<?php
 
?>

            <div class="row">
                <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Listando Usuários cadastrados</h5>
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
                        <th>Usuário</th>
                        <th>Email</th>
                        <th>Colégio</th>
                        <th>Nível de acesso</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                        $sql = "SELECT C.NOME AS NOMEC, U.NOME AS NOMEU, * FROM USUARIO U
                        LEFT JOIN COLEGIO AS C ON U.ID_COLEGIO = C.ID_COLEGIO";
                        $stmt = sqlsrv_query($conn, $sql);
                        if ($stmt === false) {
                            die(print_r(sqlsrv_errors(), true));
                        }

                        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                            switch ($row['ACESSO']) {
                                case 1:
                                    $acesso = "Vizualização";
                                    break;
                                case 2:
                                    $acesso = "Vizualização/Edição";
                                    break;
                                case 3:
                                    $acesso = "Vizualização/Edição/Administração";
                                    break;
                                default:
                                    $acesso = "Nível de acesso indefinido";
                            }
                            echo "<tr class='gradeA'>
                                        <td>" . $row['ID_USUARIO'] . "</td>
                                        <td>" . $row['NOMEU'] . "</td>
                                        <td>" . $row['USUARIO'] . "</td>
                                        <td>" . $row['EMAIL'] . "</td>
                                        <td>"; 
                                        echo empty($row['NOMEC']) ? "Colégio não definido" : "<a href='colegio.php?id=".$row['ID_COLEGIO']."' target='new_blank'>".$row['NOMEC'];
                                        echo "</a></td>
                                        <td>" . $acesso . "</td>
                                        <td class='center'>
                                            <a href='usuario.php?id=".$row['ID_USUARIO']."' title='Editar'><button class='btn btn-success btn-circle' type='button'><i class='fa fa-edit'></i></button></a>&ensp;
                                            <a href='javascript:deletar(".$row['ID_USUARIO'].");' title='Deletar'><button class='btn btn-danger btn-circle' type='button'><i class='fa fa-trash'></i></button></a>
                                        </td>
                                    </tr>";
                        }

                        sqlsrv_free_stmt($stmt);


                            }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Usuário</th>
                        <th>Email</th>
                        <th>Colégio</th>
                        <th>Nível de acesso</th>
                        <th>Ações</th>
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