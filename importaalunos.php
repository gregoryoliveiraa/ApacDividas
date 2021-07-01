<?php


require('conexao.php');

    $result = [];
    $count = 0;
    $countADD = 0;

    $sql = "SELECT A.ID_ALUNO, A.RA, A.ID_RESPONSAVEL, C.DEPARTAMENTO FROM ALUNO AS A
    LEFT JOIN COLEGIO AS C ON A.ID_COLEGIO = C.ID_COLEGIO WHERE ID_RESPONSAVEL IS NULL AND RA != 81585";
    $stmt = sqlsrv_query($conn, $sql);

    while ($rowAluno = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $alunos[] = $rowAluno;
    }
    sqlsrv_free_stmt($stmt);

    foreach($alunos as $aluno){

        if(isset($aluno['ID_ALUNO']) && isset($aluno['RA']) && isset($aluno['DEPARTAMENTO'])){

            $departamentoDiv = $aluno['DEPARTAMENTO'] == 1010 ? 1001 : str_replace('10', '01', $aluno['DEPARTAMENTO']);
            $sql = "SELECT * FROM APAC_DIVIDAS.dbo.v_web_escola_dados_alunos WHERE (Cod_Aluno = {$aluno['RA']}) AND Cod_Escola = $departamentoDiv";
            $stmt = sqlsrv_query($conn, $sql);
            $rowAlunoASSI = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

            if(isset($rowAlunoASSI)){

                if(!empty($rowAlunoASSI['ResponsavelFinanceiro']) && !empty($aluno['ID_RESPONSAVEL'])){

                $tsql= "UPDATE RESPONSAVEL
                SET NOME='{$rowAlunoASSI['ResponsavelFinanceiro']}', 
                RG='{$rowAlunoASSI['RG_Resp_Financeiro']}', 
                CPF='{$rowAlunoASSI['CPF_Resp_Financeiro']}', 
                CELULAR='{$rowAlunoASSI['Celular_Resp_Financeiro']}', 
                EMAIL='{$rowAlunoASSI['Email_Resp_Financeiro']}', 
                RUA='{$rowAlunoASSI['Endereco_Resp_Financeiro']}',
                BAIRRO='{$rowAlunoASSI['Bairro_Resp_Financeiro']}', 
                CIDADE='{$rowAlunoASSI['Cidade_Resp_Financeiro']}', 
                CEP='{$rowAlunoASSI['CEP_Resp_Financeiro']}', 
                ESTADO='{$rowAlunoASSI['UF_Resp_Financeiro']}'
                WHERE ID_RESPONSAVEL = {$aluno['ID_RESPONSAVEL']}";
                sqlsrv_query($conn, $tsql);

                }

                $count++;

                $result[] = ["Count" => $count, "Aluno" => $aluno['ID_ALUNO'], "Resp" => $aluno['ID_RESPONSAVEL']];

            }

            if(!empty($rowAlunoASSI['ResponsavelFinanceiro']) && empty($aluno['ID_RESPONSAVEL'])){

                
                $rowAlunoASSI['ResponsavelFinanceiro'] = substr($rowAlunoASSI['ResponsavelFinanceiro'], 0, 14);

                $tsql= "INSERT INTO RESPONSAVEL (NOME, RG, CPF, CELULAR, EMAIL, RUA, BAIRRO, CIDADE, CEP, ESTADO) 
                VALUES ('{$rowAlunoASSI['ResponsavelFinanceiro']}', 
                '{$rowAlunoASSI['RG_Resp_Financeiro']}', 
                '{$rowAlunoASSI['CPF_Resp_Financeiro']}', 
                '{$rowAlunoASSI['Celular_Resp_Financeiro']}', 
                '{$rowAlunoASSI['Email_Resp_Financeiro']}', 
                '{$rowAlunoASSI['Endereco_Resp_Financeiro']}',
                '{$rowAlunoASSI['Bairro_Resp_Financeiro']}', 
                '{$rowAlunoASSI['Cidade_Resp_Financeiro']}', 
                '{$rowAlunoASSI['CEP_Resp_Financeiro']}', 
                '{$rowAlunoASSI['UF_Resp_Financeiro']}')";
                sqlsrv_query($conn, $tsql);

                try{
                    $sql = "SELECT ID_RESPONSAVEL FROM RESPONSAVEL WHERE NOME LIKE '{$rowAlunoASSI['ResponsavelFinanceiro']}'";
                    $stmt = sqlsrv_query($conn, $sql);
                    $rowResponsavel = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

                }catch(Exception $ex){

                }

                if($rowResponsavel['ID_RESPONSAVEL']){

                    $tsql= "UPDATE ALUNO SET ID_RESPONSAVEL = '{$rowResponsavel['ID_RESPONSAVEL']}' WHERE ID_ALUNO = {$aluno['ID_ALUNO']}";
                    sqlsrv_query($conn, $tsql);
                    $countADD ++;

                }
            }

                $count++;

                $result[] = ["CountADD" => $countADD, "Aluno" => $aluno['ID_ALUNO'], "Resp" => $rowResponsavel['ID_RESPONSAVEL']];
        }
    }

    var_export($result);

    sqlsrv_free_stmt($stmt);