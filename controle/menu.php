<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class MenuControle{
	private $login = null;
	private $banco = null;
	private $dao = null;
	private $funcao = null; //Funcao
	
	function __construct($login, $banco){
		$this->funcao = new Funcao();
		//Carrega classe DAO
		if(!$this->funcao->carrega_arquivo('modelo', 'menu_dao')) return false;
				
		//Valida objeto de configuração
		if(get_class($login) != 'LoginControle') return false;
		if(get_class($banco) != 'Banco') return false;
		
		//Guarda objetos para uso futuro
		$this->login = $login;
		$this->banco = $banco;
	}
	
	//string FORMULÁRIO DE LOGIN CASO NÃO CONSIGA LOGAR
	//string FORMULÁRIO DE TROCA DE SENHA CASO SEJA PROVISÓRIA
	//vazio SE LOGOU
	public function listar_itens(){
		//Verifica se tem acesso ao banco e se recebeu o objeto de login
		if(is_null($this->banco)) return array('', array(MENSAGEM_ERRO=>'Sem acesso ao banco de dados'));
		if(is_null($this->login)) return array('', array(MENSAGEM_ERRO=>'Usuário não recebido'));
		
		//Valida usuário recebido
		if(!is_numeric($this->login->id) || $this->login->id == 0) return array('', array(MENSAGEM_ERRO=>'Usuário não identificado'));
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new MenuDao($this->banco);
		
		//Seleciona registros para montar a tag select
		$registros = $this->dao->selecionar($this->login->id);
		
		//Verifica se não encontrou algum retistro
		if(is_string($registros)) return array('', array(MENSAGEM_ERRO=>$registros));
		if(!is_array($registros)) return array('', array(MENSAGEM_ERRO=>'Não foi possível carregar o menu'));
		
		//Monta lista
		$itens = '';
		foreach($registros as $registro){
			$itens.= '<li'.(isset($_GET['mod'])?($registro['arquivo'] == $_GET['mod']?' class="active"':''):'').'><a href="index.php?mod='.$registro['arquivo'].'">'.$registro['nome'].'</a></li>';
		}
		
		return array($itens);
	}
}
?>