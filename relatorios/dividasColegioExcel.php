<meta charset="utf-8">
<?php
require('../conexao.php');
if (isset($_GET['departamento']) && isset($_GET['where'])) {

    $sql = "SELECT * FROM COLEGIO WHERE DEPARTAMENTO = {$_GET['departamento']}";
    $stmt = sqlsrv_query($conn, $sql);
    $rowDados = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);

    // Definimos o nome do arquivo que será exportado
    $arquivo = $rowDados['SIGLA'] ."-". $rowDados['NOME']. ".xls";

    //declaramos uma variavel para monstarmos a tabela
    $dadosXls  = "";
    $dadosXls .= "  <table border='1' >";
    $dadosXls .= "          <tr>";
    $dadosXls .= "          <th>ID</th>
                            <th>Aluno</th>
                            <th>Qtd Parcelas</th>
                            <th>Data Inicial</th>
                            <th>Crédito</th>
                            <th>Débito</th>
                            <th>Valor Total</th>
                            <th>Status</th>";
    $dadosXls .= "      </tr>";
    //incluimos nossa conexão
    $valorTotalEmAbertoResult = 0;
    $valorTotalPagoResult = 0;
    $valorTotalDividas = 0;

    $sql = "SELECT A.NOME NOMEA, C.NOME NOMEC, * FROM DIVIDA D
                INNER JOIN ALUNO A ON A.ID_ALUNO = D.ALUNO_ID_ALUNO
                INNER JOIN RESPONSAVEL R ON R.ID_RESPONSAVEL = A.ID_RESPONSAVEL
                INNER JOIN COLEGIO C ON C.ID_COLEGIO = A.ID_COLEGIO {$_GET['where']} ORDER BY ID_DIVIDA ASC";
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

        $dadosXls .= "<tr>
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
    $dadosXls .= "  </table>";

    // Configurações header para forçar o download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$arquivo.'"');
    header('Cache-Control: max-age=0');
    // Se for o IE9, isso talvez seja necessário
    header('Cache-Control: max-age=1');

    // Envia o conteúdo do arquivo
    echo $dadosXls;
    exit;
}
?>