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
                    window.location.href = "parcelas.php?deletar=1&idDeletar=" + id
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
        if($_SESSION['usuarioAcesso'] == 1 && $_SESSION['usuarioIdColegio'] > 0){
            $acessoUsuario = $_SESSION['usuarioAcesso'];
        }
    }

    if (isset($_POST['btEdicao'])) {


        $numero = $_POST['numero'];
        $valor = empty($_POST['valor']) ? NULL : $_POST['valor'];
        $data = dateEmSQL($_POST['data']);
        $dataSync = empty($_POST['dataSync']) ? NULL : "'".dateEmSQL($_POST['dataSync'])."'";
        $forma_pagamento = empty($_POST['forma_pagamento']) ? NULL : $_POST['forma_pagamento'];
        $status = empty($_POST['status']) ? NULL : $_POST['status'];

        // var_export([$_POST['dataSync'], $dataSync]);
        // die();

        if (isset($_POST['idparcela'])) {

            $idParcela = $_POST['idparcela'];
            $idDivida = $_POST['iddivida'];

            $tsql = "UPDATE PARCELA SET VALOR = '$valor',
                STATUS_PARCELA = '$status',
                FORMA_PAGAMENTO = '$forma_pagamento',
                NUMERO = '$numero',
                DATA_VENCIMENTO = '$data',
                DATA_SYNC_ASSI = $dataSync
                WHERE ID_PARCELA = $idParcela";
            sqlsrv_query($conn, $tsql);

            $resultParcelas = [];
            $somaParcelasPagas = 0;
            $contParcelasEmAberto = 0;
            $sql = "SELECT * FROM PARCELA WHERE DIVIDA_ID_DIVIDA = $idDivida";
            $stmt = sqlsrv_query($conn, $sql);
            $cont = 0;
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $resultParcelas[] = $row;
            }

            $sql = "SELECT TOTAL FROM DIVIDA WHERE ID_DIVIDA = $idDivida";
            $stmt = sqlsrv_query($conn, $sql);
            $cont = 0;
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $valorDivida = $row['TOTAL'];

            if (!empty($resultParcelas)) {
                foreach ($resultParcelas as $parcela) {
                    if ($parcela['STATUS_PARCELA'] == 'Pago') {
                        $somaParcelasPagas += $parcela['VALOR'];
                    }
                    if ($parcela['STATUS_PARCELA'] == 'Em Aberto') {
                        $contParcelasEmAberto++;
                    }
                }
                if($status == 'Em Aberto'){
                    $contParcelasEmAberto--;
                    $valorDivida -= $valor;
                }
                $totalAPagar = $valorDivida - $somaParcelasPagas;
                $totalPorParcelaEmAberto = 0;
                if ($totalAPagar <= 1 && $valorDivida > 0 && $somaParcelasPagas > 0) {
                    $tsql = "UPDATE DIVIDA SET STATUS_DIV = 'Pago' WHERE ID_DIVIDA = " . $idDivida;
                    sqlsrv_query($conn, $tsql);

                    $tsql = "DELETE FROM PARCELA WHERE (STATUS_PARCELA = 'Em Aberto' OR STATUS_PARCELA =  'Atrasado') AND DIVIDA_ID_DIVIDA = " . $idDivida;
                    sqlsrv_query($conn, $tsql);
                } else {
                    if ($contParcelasEmAberto) {
                        $totalPorParcelaEmAberto = $totalAPagar / $contParcelasEmAberto;
                    }
                }

                foreach ($resultParcelas as $parcela) {
                    if ($parcela['STATUS_PARCELA'] == 'Em Aberto' && $idParcela != $parcela['ID_PARCELA']) {
                        $tsql = "UPDATE PARCELA SET VALOR = $totalPorParcelaEmAberto WHERE ID_PARCELA = " . $parcela['ID_PARCELA'];
                        sqlsrv_query($conn, $tsql);
                    }
                }
            }
            echo "<script>successEdicao();</script>";
            echo "<meta http-equiv='refresh' content='1;url=parcelas.php?id={$idParcela}&idDivida={$idDivida}'>";
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
        // echo "<meta http-equiv='refresh' content='1;url=parcelas.php'>"; 
        // }

    }

    if (isset($_GET['deletar'])) {

        $tsql = "DELETE FROM PARCELA WHERE ID_PARCELA = " . $_GET['idDeletar'];

        if (!sqlsrv_query($conn, $tsql)) {
            die('Erro: ' . sqlsrv_errors());
        }
        echo "<script>successDeletar();</script>";
        echo "<meta http-equiv='refresh' content='1;url=parcelas.php'>";
    }


    if (isset($_GET['id']) && !isset($_POST['btNovo'])) {

        $sql = "SELECT * FROM PARCELA WHERE ID_PARCELA = " . $_GET['id'];
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        sqlsrv_free_stmt($stmt);
    }

    if (!isset($row['ID_PARCELA'])) {
        $_GET['id'] = null;
    }

    ?>

    </div>
    <br>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Parcelas</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.html">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Pagamentos</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Parcela</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">

    <?php if(!$acessoUsuario){ ?>
        <div class="row">
            <div class="col-lg-12">
                <?php
                $collapsed = "";
                if (!isset($row['ID_PARCELA'])) {
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
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form method="POST" action="">
                            <div class="form-group row">
                                <div class="col-sm-3"><label>Numero</label><input type="text" name="numero" <?php if (isset($_GET['id']) && !isset($_POST['btNovo'])) {
                                                                                            echo "value='" . $row['NUMERO'] . "'";
                                                                                        } ?> placeholder="Total" class="form-control" required></div>
                                <div class="col-sm-3"><label>Valor</label><input type="text" name="valor" <?php if (isset($_GET['id']) && !isset($_POST['btNovo'])) {
                                                                                            echo "value='" . $row['VALOR'] . "'";
                                                                                        } ?> placeholder="Entrada" class="form-control"></div>
                                <div class="col-sm-3"><label>Data Vencimento</label><input name="data" <?php if (isset($_GET['id']) && !isset($_POST['btNovo'])) {
                                                                                echo "value='" . $row['DATA_VENCIMENTO']->format('d/m/Y') . "'";
                                                                            } else {
                                                                                echo "type='date'";
                                                                            } ?> placeholder="Data inicial" class="form-control" required></div>
                                <div class="col-sm-3"><label>Data sincronização</label><input name="dataSync" <?php if (isset($_GET['id']) && !isset($_POST['btNovo']) && $row['DATA_SYNC_ASSI']) {
                                                                                echo "value='" . $row['DATA_SYNC_ASSI']->format('d/m/Y') . "'";
                                                                            } else {
                                                                                echo "type='date'";
                                                                            } ?> placeholder="Data inicial" class="form-control"></div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6"><label>Forma de pagamento</label>
                                    <select class="form-control m-b" name="forma_pagamento">
                                        <?php if (isset($_GET['id']) && !isset($_POST['btNovo']) && isset($row['FORMA_PAGAMENTO'])) {
                                            echo "<option value='" . $row['FORMA_PAGAMENTO'] . "'>" . $row['FORMA_PAGAMENTO'] . " </option>";
                                        } ?>
                                        <option>Forma de pagamento</option>
                                        <option>PIX</option>
                                        <option>Boleto</option>
                                        <option>Depósito</option>
                                        <option>Cartão de crédito</option>
                                    </select>
                                </div>
                                <div class="col-sm-6"><label>Status</label>
                                    <select class="form-control m-b" name="status">
                                        <?php if (isset($_GET['id']) && !isset($_POST['btNovo']) && isset($row['STATUS_PARCELA'])) {
                                            echo "<option value='" . $row['STATUS_PARCELA'] . "'>" . $row['STATUS_PARCELA'] . " </option>";
                                        } ?>
                                        <option>Status</option>
                                        <option>Em Aberto</option>
                                        <option>Pago</option>
                                        <option>Atrasado</option>
                                        <option>Negociado</option>
                                    </select>
                                </div>
                            </div>

                            <?php
                            if (isset($_GET['id']) && !isset($_POST['btNovo'])) {
                                echo ' <input type="hidden" name="idparcela" value="' . $row['ID_PARCELA'] . '"> ';
                                echo ' <input type="hidden" name="iddivida" value="' . $row['DIVIDA_ID_DIVIDA'] . '"> ';
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
        <? }  ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Listando parcelas cadastrados</h5>
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
                                        <th>ID</th>
                                        <th>Divida</th>
                                        <th>Numero</th>
                                        <th>Data Venc</th>
                                        <th>Forma Pag</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                        <?php if(!$acessoUsuario){ ?><th>Ações</th><? } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $whereClause = '';
                                    if($acessoUsuario){
                                        $whereClause = "WHERE P.DIVIDA_ID_DIVIDA = 0";
                                    }
                                    if (isset($_GET['idDivida'])) {
                                        $idDivida = $_GET['idDivida'];
                                        $whereClause = "WHERE P.DIVIDA_ID_DIVIDA = " . $idDivida;
                                    } else if (isset($_GET['id'])) {
                                        $idParcela = $_GET['id'];
                                        $whereClause = "WHERE P.DIVIDA_ID_DIVIDA = " . $idParcela;
                                    }
                                    $sql = "SELECT TOP 200 P.DATA_SYNC_ASSI AS DATASYNC, * FROM PARCELA P
                                    INNER JOIN DIVIDA D ON D.ID_DIVIDA = P.DIVIDA_ID_DIVIDA " . $whereClause;
                                    $stmt = sqlsrv_query($conn, $sql);
                                    if ($stmt === false) {
                                        die(print_r(sqlsrv_errors(), true));
                                    }


                                    while ($rowParcela = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                        if ($rowParcela['STATUS_PARCELA'] == 'Pago') {
                                            $button = "class='btn btn-primary btn-rounded' type='button'> <i class='fa fa-money'></i>";
                                        }
                                        if ($rowParcela['STATUS_PARCELA'] == 'Em Aberto') {
                                            $button = "class='btn btn-warning btn-rounded' type='button'> <i class='fa fa-warning'></i>";
                                        }
                                        if ($rowParcela['STATUS_PARCELA'] == 'Atrasado') {
                                            $button = "class='btn btn-danger btn-rounded' type='button'> <i class='fa fa-minus-circle'></i>";
                                        }
                                        if ($rowParcela['STATUS_PARCELA'] == 'Negociado') {
                                            $button = "class='btn btn-info btn-rounded' type='button'> <i class='fa fa-handshake'></i>";
                                        }
                                        if (empty($rowParcela['STATUS_PARCELA'])) {
                                            $button = "class='btn btn-secondary btn-rounded'";
                                            $rowParcela['STATUS_PARCELA'] = "Sem status";
                                        }

                                        $buttonStatusASSI = !empty($rowParcela['DATASYNC']) ? "<i class='fa fa-refresh' title='Sicronizado com ASSI: " . $rowParcela['DATASYNC']->format('d/m/Y') . "'></i>" : "<i class='fa fa-exclamation-circle' title='Divida não sincronizada com ASSI'> ";

                                        $idDivida = $rowParcela['ID_DIVIDA'];
                                        echo "<tr class='gradeA'>
                                        <td>" . $rowParcela['ID_PARCELA'] . "</td>
                                        <td> <a href='dividas.php?id=" . $rowParcela['ID_DIVIDA'] . "' target='new_blank'>" . $rowParcela['ID_DIVIDA'] . "</td>
                                        <td>" . $rowParcela['NUMERO'] . "</td>
                                        <td>" . $rowParcela['DATA_VENCIMENTO']->format('d/m/Y') . "</td>
                                        <td>" . $rowParcela['FORMA_PAGAMENTO'] . "</td>
                                        <td>R$ " . number_format($rowParcela['VALOR'], 2, ",", ".") . "</td>
                                        <td> <button " . $button . " " . $rowParcela['STATUS_PARCELA'] . "</button> $buttonStatusASSI </td>
                                        ";
                                        if(!$acessoUsuario){
                                            echo "<td class='center'>
                                            <a href='parcelas.php?id=" . $rowParcela['ID_PARCELA'] . "&idDivida=" . $idDivida . "' title='Editar'><button class='btn btn-success btn-circle' type='button'><i class='fa fa-edit'></i></button></a>
                                            <a href='javascript:deletar(" . $rowParcela['ID_PARCELA'] . ");' title='Deletar'><button class='btn btn-danger btn-circle' type='button'><i class='fa fa-trash'></i></button></a>
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
                                        <th>Divida</th>
                                        <th>Numero</th>
                                        <th>Data Venc</th>
                                        <th>Forma Pag</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                        <?php if(!$acessoUsuario){ ?><th>Ações</th><? } ?>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['idDivida'])) { ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="wrapper wrapper-content animated fadeInRight">
                        <div class="ibox-content p-xl">

                        <div class="row">
                                <div class="col-lg-12">
                                    <div class="title-action">
                                        <a href="relatorios/dividas.php?idDivida=<? echo $_GET['idDivida']; ?>" target="_blank" class="btn btn-info"><i class="fa fa-print"></i> Imprimir extrato dívida </a>
                                        <a href="relatorios/dividaContrato.php?idDivida=<? echo $_GET['idDivida']; ?>" target="_blank" class="btn btn-success"><i class="fa fa-print"></i> Imprimir contrato dívida </a>
                                    </div>
                                </div>

                            </div>
                            <br></br>
                            <div class="row">
                                
                                <div class="col-sm-6">
                                    <?
                                    $sql = "SELECT A.NOME NOMEA, R.NOME NOMER, R.CELULAR, R.EMAIL, A.RA, D.TOTAL, D.DATA_INICIAL FROM DIVIDA D 
                                        LEFT JOIN ALUNO A ON D.ALUNO_ID_ALUNO = A.ID_ALUNO
                                        LEFT JOIN RESPONSAVEL R ON A.ID_RESPONSAVEL = R.ID_RESPONSAVEL
                                        WHERE D.ID_DIVIDA = " . $_GET['idDivida'];
                                    $stmt = sqlsrv_query($conn, $sql);
                                    if ($stmt === false) {
                                        die(print_r(sqlsrv_errors(), true));
                                    }
                                    $valorTotalEmAberto = 0;
                                    $valorTotalPago = 0;
                                    $rowDados = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                                    sqlsrv_free_stmt($stmt);
                                    ?>


                                    <h5>Extrato</h5>
                                    <address>
                                        RA: <? echo $rowDados['RA']; ?><br>
                                        Nome: <strong><? echo $rowDados['NOMEA']; ?> </strong><br><br>
                                        <? echo isset($rowDados['NOMER']) ? "Responsavel: " . $rowDados['NOMER'] . "<br>" : "";
                                        echo isset($rowDados['CELULAR']) ? "Contato: " . $rowDados['CELULAR'] . "<br>" : "";
                                        echo isset($rowDados['EMAIL']) ? "E-mail: " . $rowDados['EMAIL'] . "<br>" : ""; ?>
                                    </address>

                                </div>

                                <div class="col-sm-6 text-right">
                                    <h4>Divida Nº</h4>
                                    <h4 class="text-success"><?php echo $_GET['idDivida']; ?></h4>

                                    <p>
                                        <span><strong>Início:</strong> <? echo $rowDados['DATA_INICIAL']->format('d/m/Y'); ?></span><br />
                                    </p>
                                </div>

                            </div>

                            <div class="table-responsive m-t">
                                <table class="table invoice-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Parcela</th>
                                            <th>Data Vencimento</th>
                                            <th>Forma de Pagamento</th>
                                            <th>Debito</th>
                                            <th>Crédito</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php

                                        $whereClause = '';
                                        if (isset($_GET['idDivida'])) {
                                            $idDivida = $_GET['idDivida'];
                                            $whereClause = "WHERE P.DIVIDA_ID_DIVIDA = " . $idDivida;
                                        }
                                        $sql = "SELECT * FROM PARCELA P
                                        INNER JOIN DIVIDA D ON D.ID_DIVIDA = P.DIVIDA_ID_DIVIDA " . $whereClause;
                                        $stmt = sqlsrv_query($conn, $sql);
                                        if ($stmt === false) {
                                            die(print_r(sqlsrv_errors(), true));
                                        }
                                        $valorTotalEmAberto = 0;
                                        $valorTotalPago = 0;
                                        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                            $valorEmAberto = "";
                                            $valorPago = "";

                                            if (
                                                $row['STATUS_PARCELA'] == 'Em Aberto' ||
                                                $row['STATUS_PARCELA'] == 'Negociado' ||
                                                $row['STATUS_PARCELA'] == 'Atrasado'
                                            ) {
                                                $valorEmAberto = "R$ " . number_format($row['VALOR'], 2, ",", ".");
                                                $valorTotalEmAberto += $row['VALOR'];
                                            }
                                            if ($row['STATUS_PARCELA'] == 'Pago') {
                                                $valorPago = "R$ " . number_format($row['VALOR'], 2, ",", ".");
                                                $valorTotalPago += $row['VALOR'];
                                            }
                                            if (empty($row['STATUS_PARCELA'])) {
                                                $button = "class='btn btn-secondary btn-rounded'";
                                                $row['STATUS_PARCELA'] = "Sem status";
                                            }
                                            echo "<tr>
                                                        <td>" . $row['ID_PARCELA'] . "</td>
                                                        <td>" . $row['NUMERO'] . "</td>
                                                        <td>" . $row['DATA_VENCIMENTO']->format('d/m/Y') . "</td>
                                                        <td>" . $row['FORMA_PAGAMENTO'] . "</td>
                                                        <td>" . $valorEmAberto . "</td>
                                                        <td style='color:red;'>" . $valorPago . "</td>
                                            </tr>";
                                        }

                                        sqlsrv_free_stmt($stmt);
                                        ?>
                                    </tbody>
                                </table>
                            </div><!-- /table-responsive -->

                            <div class="col-sm-12 text-right">
                                <br></br>
                                <address>
                                    <h3><b>Total débitos:</b>
                                        <?php echo "R$ " . number_format($rowDados['TOTAL'], 2, ",", "."); ?><br><br>

                                        <b>Total créditos:</b>
                                        <span style="color:red;"><?php echo "R$ " . number_format($valorTotalPago, 2, ",", "."); ?></span><br><br>

                                        <b>Total Dívidas:</b>
                                        <?php $valorTotalEmAberto = $rowDados['TOTAL'] - $valorTotalPago;
                                        echo "R$ " . number_format($valorTotalEmAberto, 2, ",", "."); ?><br>
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
                                        <a href="relatorios/dividas.php?idDivida=<? echo $_GET['idDivida']; ?>" target="_blank" class="btn btn-info"><i class="fa fa-print"></i> Imprimir extrato dívida </a>
                                        <a href="relatorios/dividaContrato.php?idDivida=<? echo $_GET['idDivida']; ?>" target="_blank" class="btn btn-success"><i class="fa fa-print"></i> Imprimir contrato dívida </a>
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
                        title: 'ExampleFile'
                    },
                    {
                        extend: 'pdf',
                        title: 'ExampleFile'
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