<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class MapaDAO{
	private $login = null;
	private $banco = null;
	private $modulo_dependente = 'meta';
	private $funcao = null; //Funcao
	
	function __construct($login, $banco){
		$this->funcao = new Funcao();
		//Carrega classe Mapa
		Funcao::carrega_arquivo('modelo', 'mapa_class');
		
		//Guarda objetos para uso futuro
		$this->login = $login;
		$this->banco = $banco;
	}
	
	public function selecionar($id = null, $ativos = true, $pagina = 1, $num_registros = 0){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return 'Não foi identificada conexão com o banco de dados';
		//Valida parâmetros
		if(!is_numeric($id)) $id = null;
		if(!is_bool($ativos) && !is_null($ativos)) $ativos = true;
		if(!is_numeric($pagina)) $pagina = 1;
		if(!is_numeric($num_registros) || $num_registros == 0) $num_registros = Config::$db_limite;
		
		//Verifica se o usuário tem permissão de acesso
		if(!is_numeric($this->login->permissao($this->modulo_dependente,LISTAR)) && is_null($id)) return 'Você não tem permissão para acessar o conteúdo';
		
		//Inicia lista de parâmetros
		$parametros = array();
		
		//Monta requisição
		$requisicao = 'select m.id, m.nome from mapa m';
		if(is_numeric($id)){
			$requisicao.= ' where m.id=?';
			//Adiciona parâmetros da requisição
			$parametros[] = $id;
			//Verifica as permissões
			if($this->login->permissao($this->modulo_dependente,LISTAR) != EXCLUIDOS){
				$requisicao.= ' and m.desativado is null';
			}
		}else{
			//Verifica as permissões
			if($this->login->permissao($this->modulo_dependente,LISTAR) != EXCLUIDOS || $ativos == true){
				$requisicao.= ' where m.desativado is null';
			}
		}
		$requisicao.= ' order by m.id desc';
		//Verifica se deve fazer paginação
		if(is_null($id) && $num_registros > 0){
			$requisicao.= ' limit '.(($pagina*$num_registros)-$num_registros).','.$num_registros;
		}
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(is_string($resultado)) return $resultado;
		if(!is_array($resultado)) return 'Não foi possível carregar a informação';
		if(count($resultado) == 0) return 'Nenhum mapa encontrado';
		
		//Retorna um ARRAY, agora de objetos e não mais de ARRAYs
		return $this->array_to_object($resultado,is_numeric($id));
	}
	
	private function array_to_object($array = null, $completo = false){
		//Verifica existência da classe necessária
		if(!class_exists('Mapa')) return 'Não foi identificada a classe necessária';
		
		//Valida parâmetro
		if(!is_array($array)) return false;
		
		//Inicia o array de objeto
		$object = array();
		
		//Converte arrays em objects
		foreach($array as $registro){
			//Cria objeto e adiciona à lista
			$object[] = new Mapa($registro['id'], $registro['nome']);
		}
		
		return $object;
	}
}
?>