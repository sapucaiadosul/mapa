<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 *
 * @package		
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class Banco{
	private $conexao = null;
	
	function __construct($config, $banco = ''){
		//Valida objeto de configuração
		if(get_class($config) != 'Config') return false;
		
		//Valida nome do banco
		if(strlen($banco) > 0) $banco = '_'.$banco;
		if(!isset($config->{'db_banco'.$banco})) $banco = '';
		
		try{
			//Conecta ao banco de dados
			$this->conexao = new PDO($config->{'db_banco'.$banco}, $config->{'db_usuario'.$banco}, $config->{'db_senha'.$banco});
		}catch(PDOException $Exception){
			die('Não foi possível acessar o banco de dado. Por favor, tente mais tarde.');
		}
	}
	
	//return string ERRO
	//return int ID ou QUANTIDADE ALTERADA
	//return array REGISTROS ENCONTRADOS
	public function query($requisicao, $parametros = null){
		//Verifica se a conexão foi iniciada
		if($this->conexao == null) return 'A conexão não foi iniciada';
		
		//Verifica se é uma requisição permitida
		$tipo = strtolower(substr($requisicao,0,strpos($requisicao,' ')));
		if(!in_array($tipo,array('select','update','insert'))) return 'Tipo inválido de requisição';
		
		//Prepara a requisicao
		$requisicao_preparada = $this->conexao->prepare($requisicao);
		//Verifica se há parâmetros na requisição
		if(substr_count($requisicao,'?') > 0){
			//Verifica se recebeu parâmetros
			if(!is_array($parametros)) return 'Formato inválido de parâmetros';
			//Verifica se o número de parâmetros é o mesmo do esperado na requisição
			if(substr_count($requisicao,'?') != count($parametros)) return 'Quantidade inválida de parâmetros';
			//Executa a requisição
			$executou = $requisicao_preparada->execute($parametros);
		}else{
			//Executa a requisição
			$executou = $requisicao_preparada->execute();
		}
		
		//Verifica se houve erro de execução
		if(!$executou) return 'Não foi possível executar a requisição';
		
		//Retorna conforme tipo de requisição
		switch($tipo){
			case 'select':
				//Retorna array com dados selecionados
				return $requisicao_preparada->fetchAll(PDO::FETCH_ASSOC);
				break;
			case 'update':
				//Retorna o número de registros alterados
				return $requisicao_preparada->rowCount();
				break;
			case 'insert':
				//Retorna ID do registro inserido
				return $this->conexao->lastInsertId();
				break;
			default:
				return 'Tipo de requisição não identificada';
		}
	}
}
?>