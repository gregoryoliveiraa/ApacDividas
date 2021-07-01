
<?php


require('../conexao.php');

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

    $sql = "SELECT NOME FROM COLEGIO WHERE ID_COLEGIO = " . $rowAluno['ID_COLEGIO'];
    $stmt = sqlsrv_query($conn, $sql);
    $rowColegio = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    $dados['COLEGIO'] = $rowColegio;

    $sql = "SELECT * FROM PARCELA WHERE DIVIDA_ID_DIVIDA = " . $_GET['idDivida'];
    $stmt = sqlsrv_query($conn, $sql);
    while ($rowParcela = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $dados['PARCELAS'][] = $rowParcela;
    }

    sqlsrv_free_stmt($stmt);

    // var_export($dados);
    // die();


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
    //         'STATUS_DIV' => 'Em aprovaÃ§Ã£o',
    //     ),
    //     'ALUNO' =>
    //     array(
    //         'ID_ALUNO' => 1338,
    //         'NOME' => 'JÃºlia Santos Brolezzi',
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

require('../pdf/WriteTag.php');

$pdf=new PDF_WriteTag();
$pdf->SetMargins(15,10,15);
$pdf->SetFont('times','',12);
$pdf->AddPage();

// Stylesheet
$pdf->SetStyle("p","times","N",12,"0,0,0",0);
$pdf->SetStyle("t","times","B",12,"0,0,0",10);
$pdf->SetStyle("table","times","N",12,"255,0,0",0);
$pdf->SetStyle("pf","times","N",10,"60,60,60",0);
$pdf->SetStyle("h1","times","B",9,"60,60,60",0);
$pdf->SetStyle("h2","times","BI",9,"60,60,60",0);
$pdf->SetStyle("h3","times","N",9,"60,60,60",0);
$pdf->SetStyle("h4","times","N",6,"60,60,60",0);
$pdf->SetStyle("h5","times","BU",12,"0,0,0",0);
$pdf->SetStyle("h6","times","B",12,"0,0,0");
$pdf->SetStyle("hr","times","B",9,"0,0,0",1);
$pdf->SetStyle("img","times","BI",10,"0,0,0",0);
$pdf->SetStyle("a","times","BU",9,"0,0,255");
$pdf->SetStyle("pers","times","B",12,"0,0,0");
$pdf->SetStyle("dataB","times","B",12,"255,0,0");
$pdf->SetStyle("data","times","N",12,"255,0,0");
$pdf->SetStyle("place","times","U",0,"0,0,160");
$pdf->SetStyle("vb","times","B",0,"102,153,153");

// Title
$txt="<h1>INSTITUIÇÃO PAULISTA ADVENTISTA DE EDUCAÇÃO E ASSISTÊNCIA SOCIAL REGIONAL</h1>
<h2>ADMINISTRATIVA PAULISTA CENTRAL - EDUCAÇÃO CNPJ/MF 43.586.122/0156-50</h2>
<h4> _______________________________________________________________________________________________________________________________________</h4>
<h3>CÉRLEY JUNIO MARTINS DE AZEVEDO</h3>
<h3>ADVOGADO</h3>
<h4> _______________________________________________________________________________________________________________________________________</h4>";
$pdf->SetLineWidth(0);
$pdf->SetFillColor(255,255,255);
$pdf->Image('../img/Logo_Adventista-min.png', 15, 10, 20, 20);
$pdf->SetMargins(30,10,0);
$pdf->WriteTag(0,2.5,$txt,0,"C",0,2);

$pdf->SetMargins(15,10,20);
$pdf->Ln(4);
$txt="<h5>TERMO DE CONFISSÃO DE DÍVIDA E PAGAMENTO</h5>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"C",0,0);

$pdf->Ln(1);
$txt="<p>Fica estabelecido o presente termo de confissão de dívida e acordo para pagamento entre as partes abaixo qualificadas:<p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"L",0,0);

$pdf->Ln(4);
$txt="<h6>DAS PARTES</h6>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"L",0,0);

$pdf->Ln(4);
$txt="<h6>De um lado,</h6>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"L",0,0);

$pdf->Ln(0);
$txt="<p><pers>A INSTITUIÇÃO PAULISTA ADVENTISTA DE EDUCAÇÃO E ASSISTÊNCIA SOCIAL</pers>, com sua <pers>REGIÃO ADMINISTRATIVA CENTRAL</pers>, 
inscrita no <pers>CNPJ/MF sob nº 43.586.122/0156-50</pers>,  localizada na Rua Júlio Ribeiro, nº 188, Bonfim, Campinas, S.P. - 
CEP: 13.070-712, representado pelo <pers>Sr. Delizeu Coutinho Fernandes</pers>, brasileiro, casado, administrador, portador 
do RG n° 18.168.223-0 SSP/SP, e inscrito sob CPF n° 105.070.408-80, domiciliado em Campinas/SP, nos mesmos 
limites, decorrentes da procuração outorgada por Instrumento Público lavrada no Livro 251, às fls., 384/385/386, 
do Cartório de Notas de Artur Nogueira/SP, doravante denominada simplesmente <pers>Credora;</pers><p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);

$pdf->Ln(4);
$txt="<h6>E, de outro,</h6>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"L",0,0);

$pdf->Ln(0);
$txt="<p><dataB>" . utf8_decode($dados["RESPONSAVEL"]["NOME"]) . "</dataB>, portador da carteira de identidade, RG sob nº <data>"
. utf8_decode($dados["RESPONSAVEL"]["RG"]). "</data> e inscrito no <data>CPF/MF "
. utf8_decode($dados["RESPONSAVEL"]["CPF"]). "</data>, residente e domiciliado na <data>Rua:" . utf8_decode($dados["RESPONSAVEL"]["RUA"]). ","
. utf8_decode($dados["RESPONSAVEL"]["NUMERO"]) . ", " . utf8_decode($dados["RESPONSAVEL"]["BAIRRO"]) . ", "
. utf8_decode($dados["RESPONSAVEL"]["CIDADE"]) . " / ". utf8_decode($dados["RESPONSAVEL"]["ESTADO"]) . " CEP: "
. utf8_decode($dados["RESPONSAVEL"]["CEP"]) . ", cel. " . utf8_decode($dados["RESPONSAVEL"]["CELULAR"]) . "</data>, doravante denominado, simplesmente <pers>devedor;</pers><p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);

$pdf->Ln(4);
$txt="<h6>DA CONFISSÃO DA DÍVIDA</h6>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"L",0,0);

    $count = 1;
    $textoFinal = "";
        foreach($dados['PARCELAS'] as $parcela){
            $mesTexto = selecionaMes($parcela['DATA_VENCIMENTO']->format('m'));
            $ano = $parcela['DATA_VENCIMENTO']->format('Y');
            $textoFinal .= $mesTexto . "/" . $ano . ", ";
        }


$pdf->Ln(0);
$txt="<p>A devedora qualifica-se como responsável pelo pagamento de obrigações; mensalidades escolares de 
<data>". utf8_decode($textoFinal)."</data> referente a aluna: <dataB>" . utf8_decode($dados["ALUNO"]["NOME"]) . " (RA " . $dados["ALUNO"]["RA"] . ")</dataB>, 
da unidade <data>" . utf8_decode($dados["COLEGIO"]["NOME"]) . "</data>.<p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);

$pdf->Ln(4);
$txt="<p>O referido débito, acrescido dos honorários advocatícios, atualização monetária, multa e juros, 
com cálculo atualizado até 26 de outubro de 2020, conforme planilha discriminadora dos débitos (Doc. 01), que é 
parte integrante do presente instrumento para todos os efeitos legais, totaliza-se em: <dataB>R$ " . utf8_decode(number_format($dados["DIVIDA"]["TOTAL"], 2, ",", ".")) . "</dataB>
, sendo composto das seguintes verbas:<p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);

$total = $dados["DIVIDA"]["TOTAL"] * 1.20;

$pdf->Ln(4);
$txt="<p>a) Principal + Atualização, Multa e Juros no valor de: <dataB>R$ " . utf8_decode(number_format($dados["DIVIDA"]["TOTAL"], 2, ",", ".")) . "</dataB>;</p>
<p>b) Honorários advocatícios 20% no valor de: <dataB>R$ " . utf8_decode(number_format($dados["DIVIDA"]["TOTAL"] * 0.2 , 2, ",", ".")) . "</dataB>;</p>
<p>c) Total devido no valor de:  <dataB>R$ " . utf8_decode(number_format($total , 2, ",", ".")) . "</dataB>;</p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);

$pdf->Ln(4);
$txt="<p>Assim sendo, a devedora confessa dever a Credora a quantia total de <dataB>R$ " . utf8_decode(number_format($total , 2, ",", ".")) . "</dataB>;</p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);


$pdf->Ln(10);
$txt="<pf>Rua Júlio Ribeiro, nº 188, Bonfim, Campinas, S.P. - CEP: 13070-712 Tel.: 2117-2900</pf>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"C",0,0);


$pdf->AddPage();

$txt="<h1>INSTITUIÇÃO PAULISTA ADVENTISTA DE EDUCAÇÃO E ASSISTÊNCIA SOCIAL REGIONAL</h1>
<h2>ADMINISTRATIVA PAULISTA CENTRAL - EDUCAÇÃO CNPJ/MF 43.586.122/0156-50</h2>
<h4> _______________________________________________________________________________________________________________________________________</h4>
<h3>CÉRLEY JUNIO MARTINS DE AZEVEDO</h3>
<h3>ADVOGADO</h3>
<h4> _______________________________________________________________________________________________________________________________________</h4>";
$pdf->SetLineWidth(0);
$pdf->SetFillColor(255,255,255);
$pdf->Image('../img/Logo_Adventista-min.png', 15, 10, 20, 20);
$pdf->SetMargins(30,10,0);
$pdf->WriteTag(0,2.5,$txt,0,"C",0,2);

$pdf->SetMargins(15,10,20);

$pdf->Ln(8);
$txt="<h6>DO PAGAMENTO</h6>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"L",0,0);

$pdf->Ln(0);
$txt="<p>Para o pagamento a prazo, sob a condição de não haver qualquer atraso nas respectivas datas de vencimento em nenhuma parcela, a Credora concede o desconto <dataB> de R$ " . utf8_decode(number_format($dados["DIVIDA"]["TOTAL"] * 0.2 , 2, ",", ".")) . "</dataB> 
a Devedora, restando o saldo de  <dataB>R$ " . utf8_decode(number_format($dados["DIVIDA"]["TOTAL"], 2, ",", "."))  . "</dataB> 
a ser pago de forma parcelada em " . $dados["DIVIDA"]["QTD_PARCELAS"] . " vezes nas condições a seguir discriminadas:</p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);


$pdf->Ln(6);
$txt="<t>       Parcela                                           Vencimento                                                      Valor</t>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,1,"J",0,0);

    $count = 1;
    foreach($dados['PARCELAS'] as $parcela){
        $numero = (!empty($dados['DIVIDA']['ENTRADA']) && $parcela['NUMERO'] == 1 && $count == 1) ? "Entrada" : $parcela['NUMERO'];
        $pdf->Ln(0);
        $txt="<table>{$numero}                                                 " . $parcela['DATA_VENCIMENTO']->format('d/m/Y') . "                                                    R$ " . number_format($parcela['VALOR'], 2, ",", ".") . "</table>";
        $pdf->SetLineWidth(0.1);
        $pdf->WriteTag(0,6,$txt,1,"C",0,0);
        $count++;
    }

$pdf->Ln(4);
$txt="<p>O pagamento deverá ser realizado através de depósito bancário no valor correspondente as parcelas 
mensais supra descriminadas, no <h6>Banco Bradesco, Ag: 3389-8, C/C: 25.392-8, em nome de INST PTA ADV DE EDUC 
E ASSIST SOCIAL, CNPJ: 43.586.122/0046-16.</h6> </p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);

$pdf->Ln(4);
$txt="<p>A Devedora deverá informar a Credora do referido pagamento, através da cópia do comprovante de 
deposito da respectiva parcela enviando para o e-mail; <place>cfinancas.apac@ucb.org.br</place>, 'aos cuidados 
do Departamento de Cobrança'.</p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);

$pdf->Ln(4);
$txt="<p>A aceitação de qualquer parcela além do prazo previsto não se constituirá em novação ou alteração de 
qualquer das cláusulas deste Termo, <h6>A quitação das parcelas pagas com cheque só ocorrerá após a compensação 
das cártulas.</h6></p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);

$pdf->Ln(4);
$txt="<p>Em razão do presente acordo, depois de efetivado o último pagamento ora convencionado a Credora dará a
 Devedora inteira quitação do débito em questão, após a comprovação do último pagamento.</p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);

$pdf->Ln(10);
$txt="<pf>Rua Júlio Ribeiro, nº 188, Bonfim, Campinas, S.P. - CEP: 13070-712 Tel.: 2117-2900</pf>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"C",0,0);


$pdf->AddPage();

$txt="<h1>INSTITUIÇÃO PAULISTA ADVENTISTA DE EDUCAÇÃO E ASSISTÊNCIA SOCIAL REGIONAL</h1>
<h2>ADMINISTRATIVA PAULISTA CENTRAL - EDUCAÇÃO CNPJ/MF 43.586.122/0156-50</h2>
<h4> _______________________________________________________________________________________________________________________________________</h4>
<h3>CÉRLEY JUNIO MARTINS DE AZEVEDO</h3>
<h3>ADVOGADO</h3>
<h4> _______________________________________________________________________________________________________________________________________</h4>";
$pdf->SetLineWidth(0);
$pdf->SetFillColor(255,255,255);
$pdf->Image('../img/Logo_Adventista-min.png', 15, 10, 20, 20);
$pdf->SetMargins(30,10,0);
$pdf->WriteTag(0,2.5,$txt,0,"C",0,2);

$pdf->SetMargins(15,10,20);

$pdf->Ln(4);
$txt="<h6>DO INADIMPLEMENTO</h6>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"L",0,0);

$pdf->Ln(0);
$txt="<p>O inadimplemento de qualquer das parcelas importa no vencimento antecipado das demais, bem como, 
fica convencionado que a Credora poderá exigir o pagamento da totalidade da dívida, por meio de <h6>Ação de 
Execução de Título Extrajudicial</h6>, assim sendo serão deduzidas as parcelas pagas.</p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);

$pdf->Ln(4);
$txt="<p>Em caso de inadimplemento, incidirá sobre o total, no ato da execução; atualização monetária pelo INPC,
 multa de 2% (dois por cento), juros de mora 1% (um por cento) ao mês, mais honorários advocatícios na monta de 
 20% (vinte por cento).</p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);

$pdf->Ln(4);
$txt="<p>As partes elegem o foro de Campinas, para dirimir qualquer controvérsia à cerca do presente instrumento, 
com exclusão de qualquer outro, por mais privilegiado que seja.</p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);

$pdf->Ln(4);
$txt="<p>E, por estarem, assim justos e contratados, assinam o presente termo de acordo, bem como as testemunhas 
abaixo, em 02 (duas) vias de igual teor e forma.</p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"J",0,0);

$pdf->Ln(6);
$txt="<h6>Campinas_______ de _____________________ de " . date("Y") . " </6>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,8,$txt,0,"C",0,0);

$pdf->Ln(3);
$txt="<p>DEVEDORA: </p>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"L",0,0);

// $pdf->Ln(17);
// $txt="<p>____________________________________</p>
// <table>" . utf8_decode($dados["RESPONSAVEL"]["NOME"]) . "</table>";
// $pdf->SetLineWidth(0.1);
// $pdf->WriteTag(0,4,$txt,0,"L",0,0);

$pdf->Ln(8);
$txt="<h6>CREDORA: </h6>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"L",0,0);

$pdf->Ln(17);
$txt="<p>____________________________________                   ____________________________________</p>
<h6>INST. P. ADV.de ED. e ASS. SOC.                                  Cérley Junio Martins de Azevedo</h6>
<h6>p/p Delizeu Coutinho Fernandes        		                                         OAB/SP 340690   </h6>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,4,$txt,0,"L",0,0);

$pdf->Ln(8);
$txt="<h6>TESTEMUNHAS: </h6>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"L",0,0);

$pdf->Ln(17);
$txt="<p>____________________________________                   ____________________________________</p>
<table>Testemunha 01                                                                        Testemunha 02</table>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,4,$txt,0,"L",0,0);

$pdf->Ln(10);
$txt="<pf>Rua Júlio Ribeiro, nº 188, Bonfim, Campinas, S.P. - CEP: 13070-712 Tel.: 2117-2900</pf>";
$pdf->SetLineWidth(0.1);
$pdf->WriteTag(0,6,$txt,0,"C",0,0);


$pdf->Output();


function selecionaMes($mes){
    switch($mes){
        case 1:
            $mesTexto = "jan";
            break;
        case 2:
            $mesTexto = "fev";
            break;
        case 3:
            $mesTexto = "mar";
            break;
        case 4:
            $mesTexto = "abr";
            break;
        case 5:
            $mesTexto = "mai";
            break;
        case 6:
            $mesTexto = "jun";
            break;
        case 7:
            $mesTexto = "jul";
            break;
        case 8:
            $mesTexto = "ago";
            break;
        case 9:
            $mesTexto = "set";
            break;
        case 10:
            $mesTexto = "out";
            break;
        case 11:
            $mesTexto = "nov";
            break;
        case 12:
            $mesTexto = "dez";
            break;
    }
    return $mesTexto;
}
?>