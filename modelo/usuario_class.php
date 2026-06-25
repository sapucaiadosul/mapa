<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class Usuario{
	private $id = 0;
	private $nome = '';
	private $email = '';
	private $usuario = '';
	private $senha = '';
	private $perfil = null;
	private $perfil_id = 0;
	private $provisoria = false;
	private $criado = '';
	private $criador_id = 0;
	private $modificado = '';
	private $modificador_id = 0;
	private $desativado = false;
	private $funcao = null; //Funcao
	
	function __construct($id, $nome, $usuario, $senha = null, $perfil = null, $provisoria = false, $controle = false, $criado = '', $criador_id = 0, $modificado = '', $modificador_id = 0, $desativado = false, $email = ''){
		$this->funcao = new Funcao();
		//Valida campos
		if(is_numeric($id) && $id > 0 && strlen($id) <= 5) $this->id = $id;
		if(strlen($nome) > 0 && strlen($nome) <= 40) $this->nome = $nome;
		if(strlen($email) > 0 && strlen($email) <= 120 && ($email = filter_var($email, FILTER_VALIDATE_EMAIL)) !== false) $this->email = $email;
		if(strlen($usuario) > 0 && strlen($usuario) <= 20) $this->usuario = $usuario;
		if(strlen((string)$senha) > 0) $this->senha = $senha;
		if(gettype($perfil) == 'object' && get_class($perfil) == 'Perfil') $this->perfil = $perfil;
		if(is_numeric($perfil)) $this->perfil_id = $perfil;
		if(is_bool($provisoria) || is_numeric($provisoria)) $this->provisoria = ($provisoria == true || $provisoria == 1 ? true : false);
		$this->criado = $this->funcao->limpar_data($criado);
		if(is_numeric($criador_id) && $criador_id > 0 && strlen($criador_id) <= 5) $this->criador_id = $criador_id;
		$this->modificado = $this->funcao->limpar_data($modificado);
		if(is_numeric($modificador_id) && $modificador_id > 0 && strlen($modificador_id) <= 5) $this->modificador_id = $modificador_id;
		if(!is_null($desativado) && $desativado !== false) $this->desativado = true;
		
		//Atualiza sessão, senha NULL é usuário listado e não logado
		//Adicionada validação '$controle', pois no formulário é digitada uma senha (provisória ou não) para o usuário
		if(!is_null($senha) && $controle == false) $this->atualizar_session();
	}
	
	public function __get( $key )
    {
        return (isset($this->$key)?$this->$key:'');
    }
	
	public function trocar_senha($senha_atual, $nova_senha){
		//Valida campos
		if(strlen($senha_atual) == 0) return 'Senha atual não informada';
		if(strlen($nova_senha) == 0) return 'Nova senha não informada';
		if($senha_atual != $this->senha) return 'Senha atual não confere';
		
		//Atualiza senha
		$this->senha = $nova_senha;
		
		//Atualiza sessão
		$this->atualizar_session();
		
		return true;
	}
	
	private function atualizar_session(){
		//Atualiza sessão, se existir
		//Apenas usuário e senha para forçar login a cada página,
		// para garantir que não tenha sido alterado o ID por PHP Injection
		if(strlen(session_id()) > 0){
			$_SESSION[(class_exists('Config') && isset(Config::$sistema_sessao)?Config::$sistema_sessao:'SIGEM').'_LOGIN_USUARIO'] = $this->usuario;
			$_SESSION[(class_exists('Config') && isset(Config::$sistema_sessao)?Config::$sistema_sessao:'SIGEM').'_LOGIN_SENHA'] = $this->senha;
		}
	}
}
?>