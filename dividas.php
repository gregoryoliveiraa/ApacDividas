<!DOCTYPE html>
<html>

<head>

    <!--Importando Script Jquery-->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script type="text/javascript">
        function successCadastro() {
            Swal.fire(
                'Cadastro',
                'realizado com sucesso',
                'success'
            )
        };

        function successEdicao() {
            Swal.fire(
                'Edição',
                'realizada com sucesso',
                'success'
            )
        };

        function successDeletar() {
            Swal.fire(
                'Remoção',
                'realizada com sucesso',
                'success'
            )
        };

        function deletar(id) {
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
                    window.location.href = "dividas.php?deletar=1&idDeletar=" + id
                }
            })
        };
    </script>

    <?php
    function dateEmSQL($dateSql)
    {
        $ano = substr($dateSql, 6);
        $mes = substr($dateSql, 3, -5);
        $dia = substr($dateSql, 0, -8);
        return $ano . "-" . $mes . "-" . $dia;
    }

    if (!isset($_POST['btEdicao']) && !isset($_POST['btNovo'])) {
        include("valida.php");
        $retornoProtegepagina = protegePagina();
    } else {
        $retornoProtegepagina = 1;
    }

    require('menu.php');
    require('conexao.php');

    $acessoUsuario = 0;
    if(isset($_SESSION['usuarioAcesso']) && isset($_SESSION['usuarioIdColegio'])){
        $acessoUsuario = $_SESSION['usuarioAcesso'];
        if($_SESSION['usuarioAcesso'] == 1 && $_SESSION['usuarioIdColegio'] > 0){
            $_GET['btFiltrarDivida'] = 1;
            $sql = "SELECT * FROM COLEGIO WHERE ID_COLEGIO = {$_SESSION['usuarioIdColegio']}";
            $stmt = sqlsrv_query($conn, $sql);
            $rowColegioUsuario = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $_GET['departamento'] = $rowColegioUsuario['DEPARTAMENTO'];
            sqlsrv_free_stmt($stmt);
        }
    }


    $liberaExtratoPesquisa = false;

    if (isset($_POST['btEdicao'])) {

        $total = $_POST['total'];
        $entrada = empty($_POST['entrada']) ? NULL : $_POST['entrada'];
        $parcelas = empty($_POST['parcelas']) ? NULL : $_POST['parcelas'];
        $juros = empty($_POST['juros']) ? NULL : $_POST['juros'];
        $data = dateEmSQL($_POST['data']);
        $status = empty($_POST['status']) ? NULL : $_POST['status'];

        if (isset($_POST['iddivida'])) {

            $iddivida = $_POST['iddivida'];

            $tsql = "UPDATE DIVIDA SET
                    TOTAL = ?,
                    ENTRADA = ?,
                    QTD_PARCELAS = ?,
                    JUROS = ?,
                    DATA_INICIAL = ?,
                    STATUS_DIV = ? WHERE ID_DIVIDA = ?";

            $var = [$total, $entrada, $parcelas, $juros, $data, $status, $iddivida];

            if (!sqlsrv_query($conn, $tsql, $var)) {
                die('Erro: ' . sqlsrv_errors());
            }
            echo "<script>successEdicao();</script>";
            echo "<meta http-equiv='refresh' content='1;url=dividas.php?id=" . $iddivida . "'>";
        }
        //else{

        // $tsql= "INSERT INTO ALUNO (
        //     NOME,
        //     RA,
        //     ID_COLEGIO,
        //     ID_RESPONSAVEL) 
        //     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        // $var = array($total, $entrada, $forma_pagamento, $parcelas, $juros, $data, $status, $RAaluno);

        // if (!sqlsrv_query($conn, $tsql, $var))
        // {
        //     die('Erro: ' . sqlsrv_errors());
        // }
        // echo "<script>successCadastro();</script>";
        // echo "<meta http-equiv='refresh' content='1;url=dividas.php'>"; 
        // }

    }

    if (isset($_GET['deletar'])) {

        $tsql = "DELETE FROM PARCELA WHERE DIVIDA_ID_DIVIDA = " . $_GET['idDeletar'];

        if (!sqlsrv_query($conn, $tsql)) {
            die('Erro: ' . sqlsrv_errors());
        }

        $tsql = "DELETE FROM DIVIDA WHERE ID_DIVIDA = " . $_GET['idDeletar'];

        if (!sqlsrv_query($conn, $tsql)) {
            die('Erro: ' . sqlsrv_errors());
        }
        echo "<script>successDeletar();</script>";
        echo "<meta http-equiv='refresh' content='1;url=dividas.php'>";
    }


    if (isset($_GET['id']) && !isset($_POST['btNovo'])) {

        $sql = "SELECT * FROM DIVIDA WHERE ID_DIVIDA = " . $_GET['id'];
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        sqlsrv_free_stmt($stmt);
    }

    if (!isset($row['ID_DIVIDA'])) {
        $_GET['id'] = null;
    }

    $queryWhere = "";

    if (isset($_GET['btFiltrarDivida'])) {

        $queryWhere = "Where ";
        $cont = 0;

        if (!empty($_GET['dataInicial'])) {
            $dataInicial = $_GET['dataInicial'];
            $queryWhere = $queryWhere . "D.DATA_INICIAL >= '$dataInicial' AND ";
        }

        if (!empty($_GET['dataFinal'])) {
            $dataFinal = $_GET['dataFinal'];
            $queryWhere = $queryWhere . "D.DATA_INICIAL <= '$dataFinal' AND ";
        }

        if (!empty($_GET['idDivida'])) {
            $idDivida = $_GET['idDivida'];
            $queryWhere = $queryWhere . "D.ID_DIVIDA = $idDivida AND ";
        }

        if (!empty($_GET['idDivida'])) {
            $idDivida = $_GET['idDivida'];
            $queryWhere = $queryWhere . "D.ID_DIVIDA = $idDivida AND ";
        }

        if (!empty($_GET['raAluno'])) {
            $raAluno = $_GET['raAluno'];
            $queryWhere = $queryWhere . "A.RA = $raAluno AND ";
        }

        if (!empty($_GET['nomeAluno'])) {
            $nomeAluno = $_GET['nomeAluno'];
            $queryWhere = $queryWhere . "A.NOME LIKE '%$nomeAluno%' AND ";
        }

        if (!empty($_GET['nomeResponsavel'])) {
            $nomeResponsavel = $_GET['nomeResponsavel'];
            $queryWhere = $queryWhere . "R.NOME LIKE '%$nomeResponsavel%' AND ";
        }

        if (!empty($_GET['departamento']) && is_numeric($_GET['departamento'])) {
            $departamento = $_GET['departamento'];
            $queryWhere = $queryWhere . "C.DEPARTAMENTO = '$departamento' AND ";
        }

        if (!empty($_GET['status']) && $_GET['status'] != "Status") {
            $status = $_GET['status'];
            $queryWhere = $queryWhere . "D.STATUS_DIV LIKE '$status' AND ";
        }

        $liberaExtratoPesquisa = true;

        $queryWhere = substr($queryWhere, 0, -4);

        if ($queryWhere == "Wh") {
            $queryWhere = "";
        }
    }

    ?>

    </div>
    <br>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Dívidas</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Pagamentos</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Dívida</strong>
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
                if (!isset($row['ID_DIVIDA'])) {
                    $_GET['id'] = null;
                    $collapsed = "collapsed";
                }
                ?>
                <div class="ibox <?php echo $collapsed; ?>">
                    <div class="ibox-title">
                        <h5>Edição <small> e controle</small></h5>
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
                        <form method="POST" action="">
                            <div class="form-group row">
                                <div class="col-sm-4"><input type="text" name="total" <?php if (isset($_GET['id']) && !isset($_POST['btNovo'])) {
                                                                                            echo "value='" . $row['TOTAL'] . "'";
                                                                                        } ?> placeholder="Total" class="form-control" required></div>
                                <div class="col-sm-4"><input type="text" name="entrada" <?php if (isset($_GET['id']) && !isset($_POST['btNovo'])) {
                                                                                            echo "value='" . $row['ENTRADA'] . "'";
                                                                                        } ?> placeholder="Entrada" class="form-control"></div>
                                <div class="col-sm-4"><input name="data" <?php if (isset($_GET['id']) && !isset($_POST['btNovo'])) {
                                                                                echo "value='" . $row['DATA_INICIAL']->format('d/m/Y') . "'";
                                                                            } else {
                                                                                echo "type='date'";
                                                                            } ?> placeholder="Data inicial" class="form-control" required></div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <select class="form-control m-b" name="parcelas">
                                        <?php if (isset($_GET['id']) && !isset($_POST['btNovo'])) {
                                            echo "<option value='" . $row['QTD_PARCELAS'] . "'>" . $row['QTD_PARCELAS'] . " </option>";
                                        } ?>
                                        <option>Quantidade de parcelas</option>
                                        <?php for ($i = 1; $i <= 30; $i++) {
                                            echo "<option value='$i'>$i X</option>";
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-sm-4"><input type="text" name="juros" <?php if (isset($_GET['id']) && !isset($_POST['btNovo'])) {
                                                                                            echo "value='" . $row['JUROS'] . "'";
                                                                                        } ?> placeholder="Juros" class="form-control"></div>
                                <div class="col-sm-4"><select class="form-control m-b" name="status">
                                        <?php if (isset($_GET['id']) && !isset($_POST['btNovo']) && isset($row['STATUS_DIV'])) {
                                            echo "<option value='" . $row['STATUS_DIV'] . "'>" . $row['STATUS_DIV'] . " </option>";
                                        } ?>
                                        <option>Status</option>
                                        <option>Em Aberto</option>
                                        <option>Em aprovação</option>
                                        <option>Pago</option>
                                        <option>Atrasado</option>
                                        <option>Negociado</option>
                                    </select>
                                </div>
                            </div>

                            <?php
                            if (isset($_GET['id']) && !isset($_POST['btNovo'])) {
                                echo ' <input type="hidden" name="iddivida" value="' . $row['ID_DIVIDA'] . '"> ';
                            }
                            ?>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group row">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <?php if (isset($_GET['id']) && !isset($_POST['btNovo'])) { ?><button name="btEdicao" class="btn btn-success btn-lg" type="submit"><?php echo "Salvar edição"; ?> </button><?php } ?>
                                    <?php if (!isset($_GET['id']) || isset($_POST['btNovo'])) { ?>
                                        <button class="btn btn-white btn-lg" type="reset">Limpar</button>
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
                <div class="ibox collapsed">
                    <div class="ibox-title">
                        <h5>Pesquisar <small> dívida</small></h5>
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
                        <form action="" method="get">
                            <div class="form-group  row">
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4"><input type="text" name="idDivida" placeholder="ID Divida" class="form-control"></div>
                            <?php if(!$acessoUsuario){ ?>
                                <div class="col-sm-4">
                                    <select class="form-control m-b" name="departamento">
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
                            <? } ?>
                                <div class="col-sm-4"><select class="form-control m-b" name="status">
                                        <option>Status</option>
                                        <option>Em Aberto</option>
                                        <option>Em aprovação</option>
                                        <option>Pago</option>
                                        <option>Atrasado</option>
                                        <option>Negociado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-4"><input type="text" name="raAluno" placeholder="RA  Aluno" class="form-control"></div>
                                <div class="col-sm-4"><input type="text" name="nomeAluno" placeholder="Nome Aluno" class="form-control"></div>
                                <div class="col-sm-4"><input type="text" name="nomeResponsavel" placeholder="Nome Responsável" class="form-control"></div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-2" style="text-align:right;">Dívidas entre -> Data Inicial</div>
                                <div class="col-sm-4"><input name="dataInicial" type='date' placeholder="Data inicial" class="form-control"></div>
                                <div class="col-sm-1" style="text-align:right;">Data Final</div>
                                <div class="col-sm-5"><input name="dataFinal" type='date' placeholder="Data inicial" class="form-control"></div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group row">
                                <div class="col-sm-12 col-sm-offset-2">
                                    <button class="btn btn-white btn-lg" type="reset">Limpar</button>
                                    <button class="btn btn-success btn-lg" name='btFiltrarDivida' type="submit">Filtrar</button>
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
                        <h5>Listando dívidas cadastrados</h5>
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
                            <table class="table table-striped table-bordered table-hover dataTables-example">
                                <thead>
                                    <tr>
                                        <th>ID/Gerenciar</th>
                                        <th>Aluno</th>
                                        <th>Colégio</th>
                                        <th>Qtd Parcelas</th>
                                        <th>Data Inicial</th>
                                        <th>Total (R$)</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $top = null;
                                    if (empty($queryWhere)) {
                                        $top = "TOP 200";
                                        $queryWhere = "ORDER BY D.ID_DIVIDA DESC";
                                    }
                                    $sql = "SELECT {$top} A.NOME NOMEA, C.NOME NOMEC, * FROM DIVIDA D
                                    INNER JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO
                                    INNER JOIN RESPONSAVEL R ON R.ID_RESPONSAVEL = A.ID_RESPONSAVEL
                                    INNER JOIN COLEGIO C ON C.ID_COLEGIO = A.ID_COLEGIO {$queryWhere} ";
                                    $stmt = sqlsrv_query($conn, $sql);
                                    if ($stmt === false) {
                                        die(print_r(sqlsrv_errors(), true));
                                    }

                                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                        if ($row['STATUS_DIV'] == 'Pago') {
                                            $button = "class='btn btn-primary btn-rounded' type='button'> <i class='fa fa-money'></i>";
                                        }
                                        if ($row['STATUS_DIV'] == 'Em Aberto') {
                                            $button = "class='btn btn-info btn-rounded' type='button'> <i class='fa fa-handshake-o'></i>";
                                        }
                                        if ($row['STATUS_DIV'] == 'Atrasado') {
                                            $button = "class='btn btn-danger btn-rounded' type='button'> <i class='fa fa-minus-circle'></i>";
                                        }
                                        if ($row['STATUS_DIV'] == 'Negociado') {
                                            $button = "class='btn btn-info btn-rounded' type='button'> <i class='fa fa-handshake-o'></i>";
                                        }
                                        if ($row['STATUS_DIV'] == 'Em aprovação') {
                                            $button = "class='btn btn-warning btn-rounded' type='button'> <i class='fa fa-warning'></i>";
                                        }
                                        if (empty($row['STATUS_DIV'])) {
                                            $button = "class='btn btn-secondary btn-rounded'";
                                            $row['STATUS_DIV'] = "Sem status";
                                        }
                                        echo "<tr class='gradeA'>
                                        <td><a href='parcelas.php?idDivida=" . $row['ID_DIVIDA'] . "' title='Gerenciar parcelas desta dívida'><button class='btn btn-success btn-rounded' type='button'>" . $row['ID_DIVIDA'] . " <i class='fa fa-bar-chart'></i></button></a></td>
                                        <td> <a href='aluno.php?id=" . $row['ID_ALUNO'] . "' target='new_blank'>" . $row['RA'] . "-" . $row['NOMEA'] . "</td>
                                        <td>";
                                        if(!$acessoUsuario){ echo "<a href='colegio.php?id=" . $row['ID_COLEGIO'] . "' target='new_blank'>"; } echo $row['DEPARTAMENTO'] . "-" . $row['NOMEC'] . "</td>
                                        <td>" . $row['QTD_PARCELAS'] . "</td>
                                        <td>" . $row['DATA_INICIAL']->format('d/m/Y') . "</td>
                                        <td>" . number_format($row['TOTAL'], 2, ",", ".") . "</td>
                                        <td> <button " . $button . " " . $row['STATUS_DIV'] . "</button></td>
                                        ";
                                        echo "<td>
                                                <a href='relatorios/dividaContrato.php?idDivida=" . $row['ID_DIVIDA'] . "' title='Imprimir contrato' target='new_blank'><button class='btn btn-success btn-circle' type='button'><i class='fa fa-print'></i></button></a>";
                                        if(!$acessoUsuario){
                                            echo "
                                            <a href='dividas.php?id=" . $row['ID_DIVIDA'] . "' title='Editar'><button class='btn btn-info btn-circle' type='button'><i class='fa fa-edit'></i></button></a>
                                            <a href='javascript:deletar(" . $row['ID_DIVIDA'] . ");' title='Deletar'><button class='btn btn-danger btn-circle' type='button'><i class='fa fa-trash'></i></button>";
                                        }
                                    echo "</td></tr>";
                                    }

                                    sqlsrv_free_stmt($stmt);
                                    ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>ID/Gerenciar</th>
                                        <th>Aluno</th>
                                        <th>Colégio</th>
                                        <th>Qtd Parcelas</th>
                                        <th>Data Inicial</th>
                                        <th>Total (R$)</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <?php if ($liberaExtratoPesquisa && !empty($_GET['departamento']) && is_numeric($_GET['departamento'])) { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="wrapper wrapper-content animated fadeInRight">
                        <div class="ibox-content p-xl">
                            <div class="row">
                                <div class="col-sm-6">
                                    <?
                                    $sql = "SELECT * FROM COLEGIO WHERE DEPARTAMENTO = {$_GET['departamento']}";
                                    $stmt = sqlsrv_query($conn, $sql);
                                    $rowDados = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                    sqlsrv_free_stmt($stmt);
                                    ?>
                                    <h5>Extrato</h5>
                                    <address>
                                        Colégio: <? echo $rowDados['NOME']; ?><br>
                                        Sigla: <strong><? echo $rowDados['SIGLA']; ?> </strong><br><br>
                                    </address>

                                </div>

                                <div class="col-sm-6 text-right">
                                    <h4>Departamento</h4>
                                    <h4 class="text-success"><?php echo $rowDados['DEPARTAMENTO']; ?></h4>
                                </div>

                            </div>

                            <div class="table-responsive m-t">
                                <table class="table invoice-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Aluno</th>
                                            <th>Qtd Parcelas</th>
                                            <th>Data Inicial</th>
                                            <th>Crédito</th>
                                            <th>Débito</th>
                                            <th>Valor Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php

                                        $valorTotalEmAbertoResult = 0;
                                        $valorTotalPagoResult = 0;
                                        $valorTotalDividas = 0;

                                        $sql = "SELECT A.NOME NOMEA, C.NOME NOMEC, * FROM DIVIDA D
                                        INNER JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO
                                        INNER JOIN RESPONSAVEL R ON R.ID_RESPONSAVEL = A.ID_RESPONSAVEL
                                        INNER JOIN COLEGIO C ON C.ID_COLEGIO = A.ID_COLEGIO {$queryWhere} ORDER BY ID_DIVIDA ASC";
                                        $stmt = sqlsrv_query($conn, $sql);
                                        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

                                            $sqlParcela = "SELECT * FROM PARCELA P
                                            INNER JOIN DIVIDA D ON D.ID_DIVIDA = P.DIVIDA_ID_DIVIDA 
                                            WHERE P.DIVIDA_ID_DIVIDA = {$row['ID_DIVIDA']}";

                                            $stmtP = sqlsrv_query($conn, $sqlParcela);
                                            $valorTotalEmAberto = 0;
                                            $valorTotalPago = 0;
                                            while ($rowParcela = sqlsrv_fetch_array($stmtP, SQLSRV_FETCH_ASSOC)) {
                                                $valorEmAberto = "";
                                                $valorPago = "";

                                                if (
                                                    $rowParcela['STATUS_PARCELA'] == 'Em Aberto' ||
                                                    $rowParcela['STATUS_PARCELA'] == 'Negociado' ||
                                                    $rowParcela['STATUS_PARCELA'] == 'Atrasado'
                                                ) {
                                                    $valorTotalEmAberto += $rowParcela['VALOR'];
                                                }
                                                if ($rowParcela['STATUS_PARCELA'] == 'Pago') {
                                                    $valorTotalPago += $rowParcela['VALOR'];
                                                }
                                            }

                                            $valorTotalEmAbertoResult += $valorTotalEmAberto;
                                            $valorTotalPagoResult += $valorTotalPago;
                                            $valorTotalDividas += $row['TOTAL'];

                                            echo "<tr>
                                                        <td>" . $row['ID_DIVIDA'] . "</td>
                                                        <td>" . $row['RA'] . "-" . $row['NOMEA'] . "</td>
                                                        <td>" . $row['QTD_PARCELAS'] . "</td>
                                                        <td>" . $row['DATA_INICIAL']->format('d/m/Y') . "</td>
                                                        <td>" . number_format($valorTotalPago, 2, ",", ".") . "</td>
                                                        <td>" . number_format($valorTotalEmAberto, 2, ",", ".") . "</td>
                                                        <td>" . number_format($row['TOTAL'], 2, ",", ".") . "</td>
                                                        <td>" . $row['STATUS_DIV'] . "</td>
                                            </tr>";
                                        }

                                        ?>
                                    </tbody>
                                </table>
                            </div><!-- /table-responsive -->

                            <div class="col-sm-12 text-right">
                                <br></br>
                                <address>
                                    <h3>
                                    <b>Total Dívidas:</b>
                                        <?php echo "R$ " . number_format($valorTotalDividas, 2, ",", "."); ?><br><br>

                                    <b>Total créditos:</b>
                                        <span style="color:red;"><?php echo "R$ " . number_format($valorTotalPagoResult, 2, ",", "."); ?></span><br><br>
                                    
                                    <b>Total débitos:</b>
                                        <?php echo "R$ " . number_format($valorTotalEmAbertoResult, 2, ",", "."); ?><br>
                                    <p style="font-size: 10px;">(A receber)</p><br>
                                    </h3>
                                </address>
                            </div>

                            <div class="col-sm-12 text-center">
                                <br></br>
                                <address>
                                    <strong>Associação Paulista Central - Educação</strong><br>
                                    Rua Julio Ribeiro, 188 • Bonfim, <br>
                                    Campinas/SP - 13070-712 <br>
                                    <abbr title="Phone"></abbr> (19) 2117.2900
                                </address>

                            </div>

                            <br></br>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="title-action">
                                    <h3>Imprimir extrato dívidas</h3>
                                        <a href="relatorios/dividasColegio.php?departamento=<? echo $_GET['departamento']; ?>&where=<? echo $queryWhere; ?>" target="_blank" class="btn btn-success"><i class="fa fa-file-pdf-o"></i> PDF </a>
                                        <a href="relatorios/dividasColegioExcel.php?departamento=<? echo $_GET['departamento']; ?>&where=<? echo $queryWhere; ?>" target="_blank" class="btn btn-success"><i class="fa fa-file-excel-o"></i> EXCEL </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php } ?>
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
        $(document).ready(function() {
            $('.dataTables-example').DataTable({
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [{
                        extend: 'copy'
                    },
                    {
                        extend: 'csv'
                    },
                    {
                        extend: 'excel',
                        title: 'listaDividas'
                    },
                    {
                        extend: 'pdf',
                        title: 'listaDividas'
                    },

                    {
                        extend: 'print',
                        customize: function(win) {
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