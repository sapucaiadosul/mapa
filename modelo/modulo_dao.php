<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class ModuloDAO{
	private $banco = null;
	private $funcao = null; //Funcao
	
	function __construct($banco){
		$this->funcao = new Funcao();
		//Carrega classe Módulo
		if(!($this->funcao->carrega_arquivo('modelo', 'modulo_class'))) return false;
		
		//Valida objeto de configuração
		if(get_class($banco) != 'Banco') return false;
		
		//Guarda objeto do banco para conexão
		$this->banco = $banco;
	}
	
	public function selecionar($id = null){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return 'Não foi identificada conexão com o banco de dados';
		//Valida parâmetro
		if(!is_numeric($id)) $id = null;
		
		//Inicia lista de parâmetros
		$parametros = array();
		
		//Monta requisição
		$requisicao = 'select m.id, m.nome, m.arquivo, m.criado, m.criador_id, m.modificado, m.modificador_id, m.desativado from modulo m';
		if(is_numeric($id)){
			$requisicao.= ' where id=?';
			//Adiciona parâmetros da requisição
			$parametros[] = $id;
		}
		$requisicao.= ' order by nome';
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(is_string($resultado)) return $resultado;
		if(!is_array($resultado)) return 'Não foi possível carregar a informação';
		if(count($resultado) == 0) return 'Nenhum modulo encontrado';
		
		//Retorna um ARRAY, agora de objetos e não mais de ARRAYs
		return $this->array_to_object($resultado);
	}
	
	private function array_to_object($array = null){
		//Verifica existência da classe necessária
		if(!class_exists('Modulo')) return 'Não foi identificada a classe necessária';
		
		//Valida parâmetro
		if(!is_array($array)) return false;
		
		//Inicia o array de objeto
		$object = array();
		
		//Converte arrays em objects
		foreach($array as $registro){
			$object[] = new Modulo($registro['id'], $registro['nome'], $registro['arquivo'], $registro['criado'], $registro['criador_id'], $registro['modificado'], $registro['modificador_id'], $registro['desativado']);
		}
		
		return $object;
	}
}
?>