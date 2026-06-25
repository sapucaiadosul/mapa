<?php
if (version_compare(PHP_VERSION, '5.3.10', '<')) mostrarErro('O servidor precisa ter PHP 5.3.10 ou superior para executar esta versão do SiGeM!');

//Informa que está dentro do sistema
define('SIGEM_EXEC', true);

//Inicia sessão
session_start();

//Classe que carrega todos os elementos
require_once "./controle/carrega.php";
$carrega = new Carrega();

//Carrega dados para encontrar o arquivo
$anexoId = isset($_SESSION['anexo_id']) && is_numeric($_SESSION['anexo_id']) && $_SESSION['anexo_id'] >= 0 && strlen($_SESSION['anexo_id']) <= 9 ? $_SESSION['anexo_id'] : 0;
$anexoNome = isset($_SESSION['anexo_nome']) && strlen($_SESSION['anexo_nome']) <= 80 ? $_SESSION['anexo_nome'] : '';
$anexoExtensao = isset($_SESSION['anexo_extensao']) && strlen($_SESSION['anexo_extensao']) <= 4 ? $_SESSION['anexo_extensao'] : '';
$modulo = isset($_SESSION['modulo']) && strlen($_SESSION['modulo']) <= 20 ? $_SESSION['modulo'] : '';

//Limpa dados da sessão
limparSessao();

//Valida existência de todos os dados
if($anexoId == 0 || strlen($anexoNome) == 0 || strlen($anexoExtensao) == 0 || strlen($modulo) == 0) mostrarErro("N&atilde;o foi poss&iacute;vel baixar o anexo");

//Valida permissão do usuário para baixar arquivo
if(!validarPermissao($modulo)) mostrarErro('Você não tem permiss&atilde;o para baixar o arquivo');

//Monta caminho do arquivo
$arquivo = "anexo/".floor($anexoId/1000)."/".$anexoId.".".$anexoExtensao;

//Verifica se o arquivo existe
if(!file_exists($arquivo)) mostrarErro("N&atilde;o foi poss&iacute;vel baixar o anexo");

header('Pragma: public'); 	// required
header('Expires: 0');		// no cache
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Disposition: attachment; filename="'.basename($anexoNome.".".$anexoExtensao).'"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: '.filesize($arquivo));//provide file size
header('Connection: close');
readfile($arquivo);//push it out
die();
exit();

function acessarBanco(){
	$bancoArquivo = "modelo/banco.php";
	if(!file_exists($bancoArquivo)) return false;
	require_once($bancoArquivo);
	
	$configArquivo = "config.php";
	if(!file_exists($configArquivo)) return false;
	require_once($configArquivo);
	
	//Verifica se existe a classe de banco
	if(!class_exists('Banco')) return false;
	
	//Verifica se existe a classe de configuração
	if(!class_exists('Config')) return false;
	
	//Inicia conexão com o banco de dados
	$banco = new Banco(new Config());
	
	//Verifica se iniciou corretamente
	if(is_object($banco) && get_class($banco) == 'Banco') return $banco;
	else return false;
}

function carregarLogin(){
	$loginArquivo = "controle/login.php";
	if(!file_exists($loginArquivo)) return false;
	require_once($loginArquivo);
	
	//Verifica se existe a classe de login
	if(!class_exists('LoginControle')) return false;
	
	//Acessa banco, recebendo-o como retorno
	if(!($banco = acessarBanco())) return false;
	
	//Inicia objeto de login
	$login = new LoginControle($banco);
	
	//Verifica se iniciou corretamente
	if(is_object($login) && get_class($login) == 'LoginControle') return $login;
	else return false;
}

function limparSessao(){
	if(!session_id()) return false;
	
	unset($_SESSION['anexo_id']);
	unset($_SESSION['anexo_nome']);
	unset($_SESSION['anexo_extensao']);
	unset($_SESSION['modulo']);
}

function mostrarErro($mensagem){
	//Limpa dados da sessão
	limparSessao();
	
	//Exibe mensagem e finaliza execução da página
	die($mensagem);
	exit();
}

function validarPermissao($modulo){
	//Valida parâmetro
	if(strlen($modulo) == 0 || strlen($modulo) > 20) return false;
	
	//Carrega objeto de login
	if(!($login = carregarLogin())) return false;
	
	//Loga no sistema
	$login->logar();
	
	//Valida permissão de acesso
	if($login->permissao($modulo,LISTAR) < TODOS) return false;
	
	return true;
}
?>