<?php
/**
* Sistema de segurança com acesso restrito
*
* Usado para restringir o acesso de certas páginas do seu site
*
* @author Thiago Belem <contato@thiagobelem.net>
* @link http://thiagobelem.net/
*
* @version 1.0
* @package SistemaSeguranca
*/



//  Configurações do Script
// ==============================
$_SG['abreSessao'] = true;         // Inicia a sessão com um session_start()?

$_SG['caseSensitive'] = false;     // Usar case-sensitive? Onde 'thiago' é diferente de 'THIAGO'

$_SG['validaSempre'] = true;       // Deseja validar o usuário e a senha a cada carregamento de página?
// Evita que, ao mudar os dados do usuário no banco de dado o mesmo contiue logado.
// ==============================
// ==============================

// ======================================
//   ~ Não edite a partir deste ponto ~
// ======================================


// Verifica se precisa iniciar a sessão
if ($_SG['abreSessao'] == true) {
    session_start();
}

/**
 * Função que valida um usuário e senha
 *
 * @param string $usuario - O usuário a ser validado
 * @param string $senha - A senha a ser validada
 *
 * @return bool - Se o usuário foi validado ou não (true/false)
 */
function validaUsuario($usuario, $senha)
{
    global $_SG;

    $cS = ($_SG['caseSensitive']) ? 'BINARY' : '';

    // Usa a função addslashes para escapar as aspas
    $nusuario = addslashes($usuario);
    $nsenha = addslashes($senha);
    
    require('conexao.php');
    // Monta uma consulta SQL (query) para procurar um usuário
    $sql = "SELECT * FROM USUARIO WHERE USUARIO = '" . $nusuario . "' AND SENHA = '"  . base64_encode($nsenha) . "' ";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Nenhum registro foi encontrado => o usuário é inválido
    if (empty($row['ID_USUARIO'])) {
        return false;
    } else {
        // O registro foi encontrado => o usuário é valido

        // Definimos dois valores na sessão com os dados do usuário
        $_SESSION['usuarioID'] = $row['ID_USUARIO']; // Pega o valor da coluna 'id do registro encontrado no MySQL
        $_SESSION['usuarioNome'] = $row['NOME']; // Pega o valor da coluna 'nome' do registro encontrado no MySQL
        $_SESSION['usuarioAcesso'] = $row['ACESSO'];
        $_SESSION['usuarioEmail'] = $row['EMAIL'];
        $_SESSION['usuarioIdColegio'] = $row['ID_COLEGIO'];

        // Verifica a opção se sempre validar o login
        if ($_SG['validaSempre'] == true) {
            // Definimos dois valores na sessão com os dados do login
            $_SESSION['usuarioLogin'] = $usuario;
            $_SESSION['usuarioSenha'] = base64_encode($senha);
        }

        return true;
    }
}

/**
* Função que protege uma página
*/
function protegePagina() {
global $_SG;

if (!isset($_SESSION['usuarioID']) OR !isset($_SESSION['usuarioNome'])) {
// Não há usuário logado, manda pra página de login
expulsaVisitante('SemAlert');
return false;
} else if (!isset($_SESSION['usuarioID']) OR !isset($_SESSION['usuarioNome'])) {
// Há usuário logado, verifica se precisa validar o login novamente
if ($_SG['validaSempre'] == true) {
// Verifica se os dados salvos na sessão batem com os dados do banco de dados
if (!validaUsuario($_SESSION['usuarioLogin'], $_SESSION['usuarioSenha'])) {
// Os dados não batem, manda pra tela de login
expulsaVisitante('Usuário não logado!');
return false;
}
}
}
return true;
}

/**
* Função para expulsar um visitante
*/
function expulsaVisitante(string $textoAlert) {
global $_SG;

// Remove as variáveis da sessão (caso elas existam)
unset($_SESSION['usuarioID'], $_SESSION['usuarioNome'], $_SESSION['usuarioLogin'], $_SESSION['usuarioSenha']);


if($textoAlert != 'SemAlert'){
echo ' 
<script type="text/javascript">
alert("'. $textoAlert . '");
</script>
';
}
echo ' 
<meta http-equiv="refresh" content="0;url=login.html">
';

}
?>