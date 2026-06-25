<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 * @package		
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class LoginControle{
	private $banco = null;
	private $dao = null;
	private $usuario = null;
	private $alterarsenha = false;
	private $funcao = null; //Funcao
	
	function __construct($banco){

		$this->funcao = new Funcao();
		//Carrega classe DAO
		$this->funcao->carrega_arquivo('modelo', 'login_dao');
		
		//Valida objeto de configuração
		if(get_class($banco) != 'Banco') return false;
		
		//Guarda objeto do banco para conexão
		$this->banco = $banco;
	}
	
	public function __get($key){
		//Trata o retorno conforme variável solicitada
        switch($key){
			case 'id':
				if(get_class($this->usuario) == 'Usuario'){
						return $this->usuario->id;
				}
				break;
			case 'perfil_id':
				if(get_class($this->usuario) == 'Usuario'){
					if(get_class($this->usuario->perfil) == 'Perfil'){
						return $this->usuario->perfil->id;
					}
				}
				break;
			case 'usuario_nome':
				if(get_class($this->usuario) == 'Usuario')
					return $this->usuario->nome;
				break;
			case 'alterarsenha':
				return $this->alterarsenha;
				break;
		}
    }
	
	//string FORMULÁRIO DE LOGIN CASO NÃO CONSIGA LOGAR
	//string FORMULÁRIO DE TROCA DE SENHA CASO SEJA PROVISÓRIA
	//vazio SE LOGOU
	public function logar($trocando_senha = false){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return array('',array(MENSAGEM_ERRO=>'Sem acesso ao banco de dados'));
		
		//Verifica se já está logado
		if($this->usuario instanceof Usuario && $this->usuario->id > 0) return $this->usuario->id;
		else{
			//Verifica se está fazendo novo login
			if(isset($_POST['form_name']) && $_POST['form_name'] == 'login'){
				$usuario = (isset($_POST['usuario']) && strlen($_POST['usuario']) > 0 ? $_POST['usuario'] : '');
				$senha = (isset($_POST['senha']) && strlen($_POST['senha']) > 0 ? $_POST['senha'] : '');
			}
			//Procura dados na sessão
			//Se faltar um dos dados, vai direto para novo login
			elseif(strlen(session_id()) > 0){
				if(isset($_SESSION[(class_exists('Config') && isset(Config::$sistema_sessao)?Config::$sistema_sessao:'SIGEM').'_LOGIN_USUARIO']) && strlen($_SESSION[(class_exists('Config') && isset(Config::$sistema_sessao)?Config::$sistema_sessao:'SIGEM').'_LOGIN_USUARIO']) > 0 && isset($_SESSION[(class_exists('Config') && isset(Config::$sistema_sessao)?Config::$sistema_sessao:'SIGEM').'_LOGIN_SENHA']) && strlen(($_SESSION[(class_exists('Config') && isset(Config::$sistema_sessao)?Config::$sistema_sessao:'SIGEM').'_LOGIN_SENHA'])) > 0){
					$usuario = $_SESSION[(class_exists('Config') && isset(Config::$sistema_sessao)?Config::$sistema_sessao:'SIGEM').'_LOGIN_USUARIO'];
					$senha = $_SESSION[(class_exists('Config') && isset(Config::$sistema_sessao)?Config::$sistema_sessao:'SIGEM').'_LOGIN_SENHA'];
				}
			}
		}
		
		//Verifica se há dados para logar
		if(!isset($usuario) || !isset($senha)) return array($this->formulario());
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new LoginDao($this->banco);
		
		//Seleciona ID do usuário, perfil e verifica se a senha é provisória
		$login_valido = $this->dao->validar_login($usuario, $senha);
		
		//Verifica login
		if(!is_array($login_valido)) return array($this->formulario(),array(MENSAGEM_ERRO=>'Não foi possível validar o login'));
		if(count($login_valido) ==  1) return array($this->formulario(),$login_valido);
		
		//Recebe informações do usuário
		$id = $login_valido['id'];
		$usuario_nome = $login_valido['usuario_nome'];
		$provisoria = $login_valido['provisoria'];
		$perfil_id = $login_valido['perfil_id'];
		$perfil_nome = $login_valido['perfil_nome'];
		
		//Seleciona lista de permissões para todos os módulos, recebendo o maior tipo de permissão por ação
		$permissao = $this->dao->permissao($usuario, $senha);
		
		//Verifica permissão
		if(!is_array($permissao)) $permissao = null;
		
		//Carrega classe modelo de Perfil
		if(!$this->funcao->carrega_arquivo('modelo', 'perfil_class')) return array('',array(MENSAGEM_ERRO=>'Não foi possível carregar o perfil'));
		
		//Cria objeto do perfil para inserir no objeto do usuário
		$perfil = new Perfil($perfil_id, $perfil_nome, $permissao);
		
		//Carrega classe modelo de Usuário
		if(!$this->funcao->carrega_arquivo('modelo', 'usuario_class')) return array('',array(MENSAGEM_ERRO=>'Não foi possível carregar o usuário'));
		
		//Cria objeto do usuário, se não existir
		$this->usuario = new Usuario($id, $usuario_nome, $usuario, $senha, $perfil);
		
		//Se a senha for provisória ou o usuário pedir, força troca de senha
		if((!is_null($provisoria) || isset($_GET['alterarsenha'])) && !$trocando_senha) return array($this->provisoria());
		
		//Retorna vazio, pois não há nada para ser exibido
		return array('');
	}
	
	public function trocar_senha(){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return array('',array(MENSAGEM_ERRO=>'Sem acesso ao banco de dados'));
		
		//Faz login para validar dados
		$this->logar(true);
		
		//Recebe dados do formulário
		$senha_atual = (isset($_POST['senha_atual'])?$_POST['senha_atual']:'');
		$nova_senha = (isset($_POST['nova_senha'])?$_POST['nova_senha']:'');
		$repete_senha = (isset($_POST['repita_senha'])?$_POST['repita_senha']:'');
		
		//Verifica se recebeu todos os campos
		if(strlen($senha_atual) == 0) return array($this->provisoria(),array(MENSAGEM_PADRAO=>'Informe a senha atual'));
		if(strlen($nova_senha) == 0) return array($this->provisoria(),array(MENSAGEM_PADRAO=>'Informe a nova senha'));
		if(strlen($repete_senha) == 0) return array($this->provisoria(),array(MENSAGEM_PADRAO=>'Repita a nova senha'));
		
		//Verifica se tem o usuário do solicitante
		if(get_class($this->usuario) != 'Usuario') return array($this->provisoria(),array(MENSAGEM_ERRO=>'Usuário não identificado'));
		
		//Valida senha atual e atualiza sessão
		if(!is_bool($retorno = $this->usuario->trocar_senha($senha_atual, $nova_senha))) return array($this->provisoria(),array(MENSAGEM_ERRO=>$retorno));
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new LoginDao($this->banco);
		
		//Altera senha do usuário
		$troca_senha = $this->dao->trocar_senha($this->usuario->usuario, $senha_atual, $nova_senha);
		
		//Verifica se trocou a senha
		if(gettype($troca_senha) == 'string') return array($this->provisoria(),$troca_senha);
		
		return array($this->provisoria(),array(MENSAGEM_SUCESSO=>'Senha alterada com sucesso'));
	}
	
	//boolean false O USUÁRIO NÃO TEM PERMISSÃO PARA ACESSAR UMA OU MAIS AÇÕES DO MÓDULO
	//number INFORMA O TIPO DE PERMISSAO QUE USUÁRIO TEM NA AÇÃO DO MÓDULO
	//array INFORMA O TIPO DE PERMISSAO QUE USUÁRIO TEM EM TODAS AS AÇÕES DO MÓDULO
	//array INFORMA O TIPO DE PERMISSAO QUE USUÁRIO TEM EM TODAS AS AÇÕES DE TODOS OS MÓDULOS
	public function permissao($modulo_arquivo = null, $acao = null){
		//Valida parâmetros
		if(!is_string($modulo_arquivo) || strlen($modulo_arquivo) == 0) $modulo_arquivo = null;
		if(!is_numeric($acao)) $acao = null;
		
		//Valida caminho até a lista de permissões
		if(is_null($this->usuario) || gettype($this->usuario) != 'object') return false;
		if(get_class($this->usuario) != 'Usuario') return false;
		if(is_null($this->usuario->perfil) || gettype($this->usuario->perfil) != 'object') return false;
		if(get_class($this->usuario->perfil) != 'Perfil') return false;
		
		//Permissões
		if(is_null($modulo_arquivo)) $permissao = $this->usuario->perfil->permissao_array;
		else $permissao = $this->usuario->perfil->permissao($modulo_arquivo, $acao);
		
		//Retorna as permissões encontradas
		return $permissao;
	}
	
	public function sair(){
		//Limpa dados do objeto
		$this->usuario = null;
		
		//Verifica se há sessão ativa
		while(strlen(session_id()) > 0){
			//Remove dados da sessão
			unset($_SESSION[(class_exists('Config') && isset(Config::$sistema_sessao)?Config::$sistema_sessao:'SIGEM').'_LOGIN_USUARIO']);
			unset($_SESSION[(class_exists('Config') && isset(Config::$sistema_sessao)?Config::$sistema_sessao:'SIGEM').'_LOGIN_SENHA']);
			session_destroy();
		}
	}
	
	private function formulario(){
		//Carrega HTML
		ob_start();
		$this->funcao->carrega_arquivo('visao', 'login_form');
		$form = ob_get_clean();
		
		return $form;
	}
	
	private function provisoria(){
		//Informa que está na tela de troca de senha e não na tela de login
		$this->alterarsenha = true;
		
		//Carrega HTML
		ob_start();
		$this->funcao->carrega_arquivo('visao', 'login_senha');
		$form = ob_get_clean();
		
		return $form;
	}



	public function verefica_token() {
		$token = (isset($_POST['token'])?$_POST['token']:'');

		if(strlen($token) > 0 ) {} else return array($this->formulario(), array(MENSAGEM_ERRO=>'É necessário prencher o campo do token'));  

		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return array('',array(MENSAGEM_ERRO=>'Sem acesso ao banco de dados'));
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new LoginDao($this->banco);
		
		//Altera senha do usuário
		$str = $this->dao->verificar_token($token);

		return array($this->formulario(),array(MENSAGEM_SUCESSO=>$str));
	}

	public function verefica_email () {
		$email = (isset($_POST['esqueci_senha_email'])?$_POST['esqueci_senha_email']:'');
		$cpf = (isset($_POST['esqueci_senha_cpf'])?$_POST['esqueci_senha_cpf']:'');

		if(strlen($email) > 0 && strlen($cpf) > 0) {} else return array($this->formulario(), array(MENSAGEM_ERRO=>'É necessário prencher o CPF e o E-mail'));  
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return array('',array(MENSAGEM_ERRO=>'Sem acesso ao banco de dados'));
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new LoginDao($this->banco);
		
		//Altera senha do usuário
		$esqueci_senha = $this->dao->verificar_email($email, $cpf);

		return array($this->formulario(),array(MENSAGEM_SUCESSO=>$esqueci_senha));
		
	}


}
?>