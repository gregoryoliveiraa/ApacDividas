<?php

echo validarECriarParcela();
function validarECriarParcela($idDivida = 169, $idParcela = 1421, $valorParcela = 1312.08, $valorDivida = 13120.80){
            require('conexao.php');
            $resultParcelas = array (
                0 => 
                array (
                  'ID_PARCELA' => 1421,
                  'DIVIDA_ID_DIVIDA' => 169,
                  'NUMERO' => 1,
                  'VALOR' => '1312.08',
                  'STATUS_PARCELA' => 'Em Aberto',
                  'FORMA_PAGAMENTO' => 'Boleto',
                  'DATA_VENCIMENTO' => 
                  DateTime::__set_state(array(
                     'date' => '2021-03-01 00:00:00.000000',
                     'timezone_type' => 3,
                     'timezone' => 'UTC',
                  )),
                ),
                1 => 
                array (
                  'ID_PARCELA' => 1422,
                  'DIVIDA_ID_DIVIDA' => 169,
                  'NUMERO' => 2,
                  'VALOR' => '1312.08',
                  'STATUS_PARCELA' => 'Em Aberto',
                  'FORMA_PAGAMENTO' => 'Boleto',
                  'DATA_VENCIMENTO' => 
                  DateTime::__set_state(array(
                     'date' => '2021-04-01 00:00:00.000000',
                     'timezone_type' => 3,
                     'timezone' => 'UTC',
                  )),
                ),
                2 => 
                array (
                  'ID_PARCELA' => 1423,
                  'DIVIDA_ID_DIVIDA' => 169,
                  'NUMERO' => 3,
                  'VALOR' => '1312.08',
                  'STATUS_PARCELA' => 'Em Aberto',
                  'FORMA_PAGAMENTO' => 'Boleto',
                  'DATA_VENCIMENTO' => 
                  DateTime::__set_state(array(
                     'date' => '2021-05-01 00:00:00.000000',
                     'timezone_type' => 3,
                     'timezone' => 'UTC',
                  )),
                ),
                3 => 
                array (
                  'ID_PARCELA' => 1424,
                  'DIVIDA_ID_DIVIDA' => 169,
                  'NUMERO' => 4,
                  'VALOR' => '1312.08',
                  'STATUS_PARCELA' => 'Em Aberto',
                  'FORMA_PAGAMENTO' => 'Boleto',
                  'DATA_VENCIMENTO' => 
                  DateTime::__set_state(array(
                     'date' => '2021-06-01 00:00:00.000000',
                     'timezone_type' => 3,
                     'timezone' => 'UTC',
                  )),
                ),
                4 => 
                array (
                  'ID_PARCELA' => 1425,
                  'DIVIDA_ID_DIVIDA' => 169,
                  'NUMERO' => 5,
                  'VALOR' => '1312.08',
                  'STATUS_PARCELA' => 'Em Aberto',
                  'FORMA_PAGAMENTO' => 'Boleto',
                  'DATA_VENCIMENTO' => 
                  DateTime::__set_state(array(
                     'date' => '2021-07-01 00:00:00.000000',
                     'timezone_type' => 3,
                     'timezone' => 'UTC',
                  )),
                ),
                5 => 
                array (
                  'ID_PARCELA' => 1426,
                  'DIVIDA_ID_DIVIDA' => 169,
                  'NUMERO' => 6,
                  'VALOR' => '1312.08',
                  'STATUS_PARCELA' => 'Em Aberto',
                  'FORMA_PAGAMENTO' => 'Boleto',
                  'DATA_VENCIMENTO' => 
                  DateTime::__set_state(array(
                     'date' => '2021-08-01 00:00:00.000000',
                     'timezone_type' => 3,
                     'timezone' => 'UTC',
                  )),
                ),
                6 => 
                array (
                  'ID_PARCELA' => 1427,
                  'DIVIDA_ID_DIVIDA' => 169,
                  'NUMERO' => 7,
                  'VALOR' => '1312.08',
                  'STATUS_PARCELA' => 'Em Aberto',
                  'FORMA_PAGAMENTO' => 'Boleto',
                  'DATA_VENCIMENTO' => 
                  DateTime::__set_state(array(
                     'date' => '2021-09-01 00:00:00.000000',
                     'timezone_type' => 3,
                     'timezone' => 'UTC',
                  )),
                ),
                7 => 
                array (
                  'ID_PARCELA' => 1428,
                  'DIVIDA_ID_DIVIDA' => 169,
                  'NUMERO' => 8,
                  'VALOR' => '1312.08',
                  'STATUS_PARCELA' => 'Em Aberto',
                  'FORMA_PAGAMENTO' => 'Boleto',
                  'DATA_VENCIMENTO' => 
                  DateTime::__set_state(array(
                     'date' => '2021-10-01 00:00:00.000000',
                     'timezone_type' => 3,
                     'timezone' => 'UTC',
                  )),
                ),
                8 => 
                array (
                  'ID_PARCELA' => 1429,
                  'DIVIDA_ID_DIVIDA' => 169,
                  'NUMERO' => 9,
                  'VALOR' => '1312.08',
                  'STATUS_PARCELA' => 'Em Aberto',
                  'FORMA_PAGAMENTO' => 'Boleto',
                  'DATA_VENCIMENTO' => 
                  DateTime::__set_state(array(
                     'date' => '2021-11-01 00:00:00.000000',
                     'timezone_type' => 3,
                     'timezone' => 'UTC',
                  )),
                ),
                9 => 
                array (
                  'ID_PARCELA' => 1430,
                  'DIVIDA_ID_DIVIDA' => 169,
                  'NUMERO' => 10,
                  'VALOR' => '1312.08',
                  'STATUS_PARCELA' => 'Em Aberto',
                  'FORMA_PAGAMENTO' => 'Boleto',
                  'DATA_VENCIMENTO' => 
                  DateTime::__set_state(array(
                     'date' => '2021-12-01 00:00:00.000000',
                     'timezone_type' => 3,
                     'timezone' => 'UTC',
                  )),
                ),
              );

            //setarParcela como PAGO
            $resultParcelas[0]['STATUS_PARCELA'] = "PAGO";

            foreach($resultParcelas as $parcela){
                //$parcela[]
                
            }
            
            
            return $cont;
        }
