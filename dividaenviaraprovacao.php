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
        include("loadingPage.php");
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


        if (isset($_POST['btEdicao'])){

            $nome = $_POST['nome'];
            $rg = $_POST['rg'];
            $cpf = $_POST['cpf'];
            $telefone = $_POST['telefone'];
            $celular = $_POST['celular'];
            $email = $_POST['email'];
            $rua = $_POST['rua'];
            $numero = $_POST['numero'];
            $bairro = $_POST['bairro'];
            $cidade = $_POST['cidade'];
            $cep = $_POST['cep'];
            $estado = $_POST['estado'];
            $complemento = $_POST['complemento'];

            if(isset($_POST['idresponsavel'])){

                $idresponsavel = $_POST['idresponsavel'];

                $tsql= "UPDATE RESPONSAVEL SET
                    NOME = ?,
                    RG = ?,
                    CPF = ?,
                    TELEFONE = ?,
                    CELULAR = ?,
                    EMAIL = ?,
                    RUA = ?,
                    NUMERO = ?,
                    BAIRRO = ?,
                    CIDADE = ?,
                    CEP = ?,
                    ESTADO = ?,
                    COMPLEMENTO = ?
                    WHERE ID_RESPONSAVEL = ?";

                $var = array($nome, $rg, $cpf, $telefone, $celular, $email, $rua, $numero, $bairro, $cidade, $cep, $estado, $complemento, $idresponsavel);
    
                if (!sqlsrv_query($conn, $tsql, $var))
                {
                    die('Erro: ' . sqlsrv_errors());
                }
                echo "<script>successEdicao();</script>";
                echo "<meta http-equiv='refresh' content='1;url=responsavel.php?id=" . $idresponsavel . "'>"; 
                
            }else{
        
                $tsql= "INSERT INTO RESPONSAVEL (
                    NOME,
                    RG,
                    CPF,
                    TELEFONE,
                    CELULAR,
                    EMAIL,
                    RUA,
                    NUMERO,
                    BAIRRO,
                    CIDADE,
                    CEP,
                    ESTADO,
                    COMPLEMENTO)
                    VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $var = array($nome, $rg, $cpf, $telefone, $celular, $email, $rua, $numero, $bairro, $cidade, $cep, $estado, $complemento);
                
                if (!sqlsrv_query($conn, $tsql, $var))
                {
                    die('Erro: ' . sqlsrv_errors());
                }
                echo "<script>successCadastro();</script>";
                echo "<meta http-equiv='refresh' content='1;url=responsavel.php'>"; 
            }

        }

        if (isset($_GET['deletar'])){
            
            $tsql= "DELETE FROM RESPONSAVEL WHERE ID_RESPONSAVEL = " . $_GET['idDeletar'];

            if (!sqlsrv_query($conn, $tsql))
            {
                die('Erro: ' . sqlsrv_errors());
            }
            echo "<script>successDeletar();</script>";
            echo "<meta http-equiv='refresh' content='1;url=responsavel.php'>"; 
        }
        

    if(isset($_GET['id']) && !isset($_POST['btNovo'])){

        $sql = "SELECT * FROM RESPONSAVEL WHERE ID_RESPONSAVEL = " . $_GET['id'];
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        sqlsrv_free_stmt($stmt);
    }

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

    <?php if(!$acessoUsuario){ ?>
        <div class="row">
            <div class="col-lg-12">
            <?php
                $collapsed = "";
                if (!isset($row['ID_RESPONSAVEL'])) {
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
                                <div class="col-sm-8"><input type="text" name="nome" placeholder="Nome" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['NOME']. "'"; }?> class="form-control" required></div>
                                <div class="col-sm-4"><input type="text" name="rg" placeholder="RG" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['RG']. "'"; }?> class="form-control"></div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4"><input type="text" name="cpf" placeholder="CPF" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['CPF']. "'"; }?> class="form-control"></div>
                                <div class="col-sm-8"><input type="text" name="email" placeholder="Email" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['EMAIL']. "'"; }?> class="form-control"></div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6"><input type="text" name="telefone" placeholder="Telefone" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['TELEFONE']. "'"; }?> class="form-control"></div>
                                <div class="col-sm-6"><input type="text" name="celular" placeholder="Celular" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['CELULAR']. "'"; }?> class="form-control"></div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group row">
                                <div class="col-sm-4"><input type="text" id="cep" name="cep" placeholder="CEP" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['CEP']. "'"; }?> class="form-control" required>
                            </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-8"><input type="text" id="logradouro" name="rua" placeholder="Rua" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['RUA']. "'"; }?> class="form-control"></div>
                                <div class="col-sm-4"><input type="text" id="numero" name="numero" placeholder="Numero" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['NUMERO']. "'"; }?> class="form-control"></div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6"><input type="text" id="complemento" name="complemento" placeholder="Complemento" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['COMPLEMENTO']. "'"; }?> class="form-control"></div>
                                <div class="col-sm-6"><input type="text" id="bairro" name="bairro" placeholder="Bairro" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['BAIRRO']. "'"; }?> class="form-control"></div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6"><input type="text" id="cidade" name="cidade" placeholder="Cidade" <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "value='".$row['CIDADE']. "'"; }?> class="form-control"></div>
                                <div class="col-sm-6">
                                    <select class="form-control m-b" name="estado" id="uf">
                                    <?php if(isset($_GET['id']) && !isset($_POST['btNovo'])){ echo "<option value='". $row['ESTADO']."'>" . $row['ESTADO']. " </option>"; }?>
                                        <option>Selecione o Estado</option>
                                        <option value="AC">Acre</option>
                                        <option value="AL">Alagoas</option>
                                        <option value="AP">Amapá</option>
                                        <option value="AM">Amazonas</option>
                                        <option value="BA">Bahia</option>
                                        <option value="CE">Ceará</option>
                                        <option value="DF">Distrito Federal</option>
                                        <option value="ES">Espírito Santo</option>
                                        <option value="GO">Goiás</option>
                                        <option value="MA">Maranhão</option>
                                        <option value="MT">Mato Grosso</option>
                                        <option value="MS">Mato Grosso do Sul</option>
                                        <option value="MG">Minas Gerais</option>
                                        <option value="PA">Pará</option>
                                        <option value="PB">Paraíba</option>
                                        <option value="PR">Paraná</option>
                                        <option value="PE">Pernambuco</option>
                                        <option value="PI">Piauí</option>
                                        <option value="RJ">Rio de Janeiro</option>
                                        <option value="RN">Rio Grande do Norte</option>
                                        <option value="RS">Rio Grande do Sul</option>
                                        <option value="RO">Rondônia</option>
                                        <option value="RR">Roraima</option>
                                        <option value="SC">Santa Catarina</option>
                                        <option value="SP">São Paulo</option>
                                        <option value="SE">Sergipe</option>
                                        <option value="TO">Tocantins</option>
                                    </select>
                                </div>
                            </div>

                            <?php 
                            if(isset($_GET['id']) && !isset($_POST['btNovo'])){ 
                                echo ' <input type="hidden" name="idresponsavel" value="' .$row['ID_RESPONSAVEL']. '"> ';
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


           
            </div>
        </div>
    <?php } require("footer.php"); ?>

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