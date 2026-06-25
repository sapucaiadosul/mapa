<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class MapaControle{
	private $login = null;
	private $banco = null;
	private $dao = null;
	private $registro = null;
	private $modulo_dependente = array('meta');
	private $modulo_arquivo = 'mapa';
	private $funcao = null; //Funcao
	
	function __construct($login, $banco){
		$this->funcao = new Funcao();

		//Carrega classe DAO
		if(!$this->funcao->carrega_arquivo('modelo', 'mapa_dao')) return false;
		
		//Carrega classe base
		if(!$this->funcao->carrega_arquivo('modelo', 'mapa_class')) return false;
		
		//Valida objeto de configuração
		if(get_class($login) != 'LoginControle') return false;
		if(get_class($banco) != 'Banco') return false;
		
		//Guarda objetos para uso futuro
		$this->login = $login;
		$this->banco = $banco;
	}
	
	public function css(){
		//Lista de arquivos CSS necessários para o módulo
		return array('');
	}
	
	public function excluir(){
		
	}
	
	public function formulario(){
		return array('');
	}
	
	public function js(){
		//Lista de arquivos JS necessários para o módulo
		return array('');
	}
	
	public function lista(){
		return array('');
	}
	
	public function salvar(){
		
	}

	public function select($id = null){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return false;
		
		//Valida parâmetro
		if(!is_numeric($id)) $id = null;
		
		//Verifica se o usuário tem permissão de acesso
		if(!(in_array($_GET['mod'], $this->modulo_dependente) && (is_numeric($this->login->permissao($_GET['mod'],LISTAR)) ))) return array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo');
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new MapaDao($this->login, $this->banco);
		
		//Seleciona registros ATIVOS para montar a tag select
		$registros = $this->dao->selecionar(null, true, 1, -1);
		
		//Verifica se encontrou algum retistro
		if(is_string($registros)) return array(MENSAGEM_ERRO=>$registros);
		if(!is_array($registros)) return array(MENSAGEM_ERRO=>'Não foi possível carregar os mapas');
		
		//Monta options da tabela
		$options = '';
		foreach($registros as $registro){
			//Monta linha do select
			$options.= '<option'.(is_null($id) || $registro->id == $id ? ' selected' : '').' value="'.$registro->id.'">';
			$options.= $registro->nome;
			$options.= '</option>';
			//Se não tem ID selecionada, seleciona o primeiro, que é o mais novo, depois passa para ZERO para não selecionar outro
			if(is_null($id)){
				$id = 0;
				//Se não tiver iniciada sessão do mapa, inicia passando a ID selecionada
				if(!isset($_SESSION['mapa']) || !is_numeric($_SESSION['mapa']))
					$_SESSION['mapa'] = $registro->id;
			}
		}
		
		//Verifica existência da tela
		$visao_arquivo = './visao/mapa_visao_select.php';
		if(!file_exists($visao_arquivo)) return false;
		
		//Carrega HTML
		ob_start();
		include_once $visao_arquivo;
		$select = ob_get_clean();
		
		return $select;
	}
	
	public function title(){
		return 'Mapa';
	}
	
	
}
?>