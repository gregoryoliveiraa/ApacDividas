<?php
require('../conexao.php');
require('../pdf/fpdf.php');

if (isset($_GET['idDivida'])) {

    $sql = "SELECT * FROM DIVIDA WHERE ID_DIVIDA = " . $_GET['idDivida'];
    $stmt = sqlsrv_query($conn, $sql);
    $rowDivida = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    $dados['DIVIDA'] = $rowDivida;

    $sql = "SELECT * FROM ALUNO WHERE ID_ALUNO = " . $rowDivida['ALUNO_ID_ALUNO'];
    $stmt = sqlsrv_query($conn, $sql);
    $rowAluno = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    $dados['ALUNO'] = $rowAluno;

    $sql = "SELECT * FROM RESPONSAVEL WHERE ID_RESPONSAVEL = " . $rowAluno['ID_RESPONSAVEL'];
    $stmt = sqlsrv_query($conn, $sql);
    $rowResponsavel = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    $dados['RESPONSAVEL'] = $rowResponsavel;

    $sql = "SELECT * FROM PARCELA WHERE DIVIDA_ID_DIVIDA = " . $_GET['idDivida'];
    $stmt = sqlsrv_query($conn, $sql);
    while ($rowParcela = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $dados['PARCELAS'][] = $rowParcela;
    }
    sqlsrv_free_stmt($stmt);


    // array(
    //     'DIVIDA' =>
    //     array(
    //         'ID_DIVIDA' => 2407,
    //         'ALUNO_ID_ALUNO' => 1338,
    //         'TOTAL' => '9999.00',
    //         'ENTRADA' => '1000',
    //         'QTD_PARCELAS' => '12',
    //         'VALOR_PARCELA' => '1',
    //         'DATA_INICIAL' =>
    //         DateTime::__set_state(array(
    //             'date' => '2021-05-12 00:00:00.000000',
    //             'timezone_type' => 3,
    //             'timezone' => 'UTC',
    //         )),
    //         'JUROS' => NULL,
    //         'DESCONTO' => NULL,
    //         'STATUS_DIV' => 'Em aprovação',
    //     ),
    //     'ALUNO' =>
    //     array(
    //         'ID_ALUNO' => 1338,
    //         'NOME' => 'Júlia Santos Brolezzi',
    //         'RA' => 205,
    //         'ID_COLEGIO' => 7,
    //         'ID_RESPONSAVEL' => 927,
    //     ),
    //     'RESPONSAVEL' =>
    //     array(
    //         'ID_RESPONSAVEL' => 927,
    //         'NOME' => 'Schirley Costa Santos Brolezzi',
    //         'RG' => '57.094.228-7   ',
    //         'CPF' => '029518376-40',
    //         'TELEFONE' => NULL,
    //         'CELULAR' => '19981908789',
    //         'EMAIL' => 'schirleycsantos@yahoo.com.br',
    //         'RUA' => '',
    //         'NUMERO' => NULL,
    //         'BAIRRO' => 'Vila Santa Isabel',
    //         'CIDADE' => 'Campinas',
    //         'CEP' => '13084-633',
    //         'ESTADO' => 'SP',
    //         'COMPLEMENTO' => NULL,
    //     ),
    //     'PARCELAS' =>
    //     array(
    //         0 =>
    //         array(
    //             'ID_PARCELA' => 25926,
    //             'DIVIDA_ID_DIVIDA' => 2407,
    //             'NUMERO' => 1,
    //             'VALOR' => '1000.00',
    //             'STATUS_PARCELA' => 'Em Aberto',
    //             'FORMA_PAGAMENTO' => 'Boleto',
    //             'DATA_VENCIMENTO' =>
    //             DateTime::__set_state(array(
    //                 'date' => '2021-05-10 00:00:00.000000',
    //                 'timezone_type' => 3,
    //                 'timezone' => 'UTC',
    //             )),
    //         ),
    //         1 =>
    //         array(
    //             'ID_PARCELA' => 25927,
    //             'DIVIDA_ID_DIVIDA' => 2407,
    //             'NUMERO' => 1,
    //             'VALOR' => '749.92',
    //             'STATUS_PARCELA' => 'Em Aberto',
    //             'FORMA_PAGAMENTO' => 'Boleto',
    //             'DATA_VENCIMENTO' =>
    //             DateTime::__set_state(array(
    //                 'date' => '2021-06-10 00:00:00.000000',
    //                 'timezone_type' => 3,
    //                 'timezone' => 'UTC',
    //             )),
    //         ),
    //         2 =>
    //         array(
    //             'ID_PARCELA' => 25928,
    //             'DIVIDA_ID_DIVIDA' => 2407,
    //             'NUMERO' => 2,
    //             'VALOR' => '749.92',
    //             'STATUS_PARCELA' => 'Em Aberto',
    //             'FORMA_PAGAMENTO' => 'Boleto',
    //             'DATA_VENCIMENTO' =>
    //             DateTime::__set_state(array(
    //                 'date' => '2021-07-10 00:00:00.000000',
    //                 'timezone_type' => 3,
    //                 'timezone' => 'UTC',
    //             )),
    //         ),
    //         3 =>
    //         array(
    //             'ID_PARCELA' => 25929,
    //             'DIVIDA_ID_DIVIDA' => 2407,
    //             'NUMERO' => 3,
    //             'VALOR' => '749.92',
    //             'STATUS_PARCELA' => 'Em Aberto',
    //             'FORMA_PAGAMENTO' => 'Boleto',
    //             'DATA_VENCIMENTO' =>
    //             DateTime::__set_state(array(
    //                 'date' => '2021-08-10 00:00:00.000000',
    //                 'timezone_type' => 3,
    //                 'timezone' => 'UTC',
    //             )),
    //         ),
    //         4 =>
    //         array(
    //             'ID_PARCELA' => 25930,
    //             'DIVIDA_ID_DIVIDA' => 2407,
    //             'NUMERO' => 4,
    //             'VALOR' => '749.92',
    //             'STATUS_PARCELA' => 'Em Aberto',
    //             'FORMA_PAGAMENTO' => 'Boleto',
    //             'DATA_VENCIMENTO' =>
    //             DateTime::__set_state(array(
    //                 'date' => '2021-09-10 00:00:00.000000',
    //                 'timezone_type' => 3,
    //                 'timezone' => 'UTC',
    //             )),
    //         ),
    //         5 =>
    //         array(
    //             'ID_PARCELA' => 25931,
    //             'DIVIDA_ID_DIVIDA' => 2407,
    //             'NUMERO' => 5,
    //             'VALOR' => '749.92',
    //             'STATUS_PARCELA' => 'Em Aberto',
    //             'FORMA_PAGAMENTO' => 'Boleto',
    //             'DATA_VENCIMENTO' =>
    //             DateTime::__set_state(array(
    //                 'date' => '2021-10-10 00:00:00.000000',
    //                 'timezone_type' => 3,
    //                 'timezone' => 'UTC',
    //             )),
    //         ),
    //         6 =>
    //         array(
    //             'ID_PARCELA' => 25932,
    //             'DIVIDA_ID_DIVIDA' => 2407,
    //             'NUMERO' => 6,
    //             'VALOR' => '749.92',
    //             'STATUS_PARCELA' => 'Em Aberto',
    //             'FORMA_PAGAMENTO' => 'Boleto',
    //             'DATA_VENCIMENTO' =>
    //             DateTime::__set_state(array(
    //                 'date' => '2021-11-10 00:00:00.000000',
    //                 'timezone_type' => 3,
    //                 'timezone' => 'UTC',
    //             )),
    //         ),
    //         7 =>
    //         array(
    //             'ID_PARCELA' => 25933,
    //             'DIVIDA_ID_DIVIDA' => 2407,
    //             'NUMERO' => 7,
    //             'VALOR' => '749.92',
    //             'STATUS_PARCELA' => 'Em Aberto',
    //             'FORMA_PAGAMENTO' => 'Boleto',
    //             'DATA_VENCIMENTO' =>
    //             DateTime::__set_state(array(
    //                 'date' => '2021-12-10 00:00:00.000000',
    //                 'timezone_type' => 3,
    //                 'timezone' => 'UTC',
    //             )),
    //         ),
    //         8 =>
    //         array(
    //             'ID_PARCELA' => 25934,
    //             'DIVIDA_ID_DIVIDA' => 2407,
    //             'NUMERO' => 8,
    //             'VALOR' => '749.92',
    //             'STATUS_PARCELA' => 'Em Aberto',
    //             'FORMA_PAGAMENTO' => 'Boleto',
    //             'DATA_VENCIMENTO' =>
    //             DateTime::__set_state(array(
    //                 'date' => '2022-01-10 00:00:00.000000',
    //                 'timezone_type' => 3,
    //                 'timezone' => 'UTC',
    //             )),
    //         ),
    //         9 =>
    //         array(
    //             'ID_PARCELA' => 25935,
    //             'DIVIDA_ID_DIVIDA' => 2407,
    //             'NUMERO' => 9,
    //             'VALOR' => '749.92',
    //             'STATUS_PARCELA' => 'Em Aberto',
    //             'FORMA_PAGAMENTO' => 'Boleto',
    //             'DATA_VENCIMENTO' =>
    //             DateTime::__set_state(array(
    //                 'date' => '2022-02-10 00:00:00.000000',
    //                 'timezone_type' => 3,
    //                 'timezone' => 'UTC',
    //             )),
    //         ),
    //         10 =>
    //         array(
    //             'ID_PARCELA' => 25936,
    //             'DIVIDA_ID_DIVIDA' => 2407,
    //             'NUMERO' => 10,
    //             'VALOR' => '749.92',
    //             'STATUS_PARCELA' => 'Em Aberto',
    //             'FORMA_PAGAMENTO' => 'Boleto',
    //             'DATA_VENCIMENTO' =>
    //             DateTime::__set_state(array(
    //                 'date' => '2022-03-10 00:00:00.000000',
    //                 'timezone_type' => 3,
    //                 'timezone' => 'UTC',
    //             )),
    //         ),
    //         11 =>
    //         array(
    //             'ID_PARCELA' => 25937,
    //             'DIVIDA_ID_DIVIDA' => 2407,
    //             'NUMERO' => 11,
    //             'VALOR' => '749.92',
    //             'STATUS_PARCELA' => 'Em Aberto',
    //             'FORMA_PAGAMENTO' => 'Boleto',
    //             'DATA_VENCIMENTO' =>
    //             DateTime::__set_state(array(
    //                 'date' => '2022-04-10 00:00:00.000000',
    //                 'timezone_type' => 3,
    //                 'timezone' => 'UTC',
    //             )),
    //         ),
    //         12 =>
    //         array(
    //             'ID_PARCELA' => 25938,
    //             'DIVIDA_ID_DIVIDA' => 2407,
    //             'NUMERO' => 12,
    //             'VALOR' => '749.92',
    //             'STATUS_PARCELA' => 'Em Aberto',
    //             'FORMA_PAGAMENTO' => 'Boleto',
    //             'DATA_VENCIMENTO' =>
    //             DateTime::__set_state(array(
    //                 'date' => '2022-05-10 00:00:00.000000',
    //                 'timezone_type' => 3,
    //                 'timezone' => 'UTC',
    //             )),
    //         ),
    //     ),
    // )

    // $count = 1;
    // foreach($dados['PARCELAS'] as $parcela){
    //     $numero = (!empty($dados['DIVIDA']['ENTRADA']) && $parcela['NUMERO'] == 1 && $count == 1) ? "Entrada" : $parcela['NUMERO'];
    //     echo "<tr>
    //         <td>" . $numero . "</td>
    //         <td>" . $parcela['DATA_VENCIMENTO']->format('d/m/Y') . "</td>
    //         <td>" . number_format($parcela['VALOR'], 2, ",", ".") . "</td>
    //     </tr>";
    //     $count++;
    // }

}


class PDF extends FPDF
{
// Page header
function Header()
{
	// Logo
	$this->Image('../pdf/tutoriallogo.png',10,6,30);
	// Arial bold 15
	$this->SetFont('Arial','B',15);
	// Move to the right
	$this->Cell(80);
	// Title
	$this->Cell(30,10,'Title',1,0,'C');
	// Line break
	$this->Ln(20);
}

// Page footer
function Footer()
{
	// Position at 1.5 cm from bottom
	$this->SetY(-15);
	// Arial italic 8
	$this->SetFont('Arial','I',8);
	// Page number
	$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
for($i=1;$i<=40;$i++)
	$pdf->Cell(0,10,'Printing line number '.$i,0,1);
$pdf->Output();

?>