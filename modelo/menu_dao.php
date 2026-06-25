<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class MenuDAO{
	private $banco = null;
	
	function __construct($banco){
		//Verifica se tem acesso a classe DAO
		$dao_arquivo = './modelo/menu_dao.php';
		if(!file_exists($dao_arquivo)) return false;
		require_once $dao_arquivo;
		
		//Valida objeto de configuração
		if(get_class($banco) != 'Banco') return false;
		
		//Guarda objeto do banco para conexão
		$this->banco = $banco;
	}
	
	//string ERRO
	//array LISTA DE ITENS DE MENU
	public function selecionar($usuario_id){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return 'Não foi identificada conexão com o banco de dados';
		//Valida parâmetro
		if(!is_numeric($usuario_id)) return 'Usuário não identificado';
		
		//Monta requisição
		$requisicao = 'select m.nome, m.arquivo from modulo m left join permissao p on p.modulo_id = m.id left join usuario u on p.perfil_id = u.perfil_id where u.id = ? and m.desativado is null and p.desativado is null and u.desativado is null group by m.id order by m.nome';
		
		//Adiciona parâmetros da requisição
		$parametros = array($usuario_id);
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(is_string($resultado)) return $resultado;
		if(!is_array($resultado)) return 'Não foi possível carregar a informação';
		
		//Retorna um ARRAY
		return $resultado;
	}
}
?>