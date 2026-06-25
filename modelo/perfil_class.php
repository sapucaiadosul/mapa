<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class Perfil{
	private $id = 0;
	private $nome = '';
	private $permissao = null;
	private $criado = '';
	private $criador_id = 0;
	private $modificado = '';
	private $modificador_id = 0;
	private $desativado = false;
	private $funcao = null; //Funcao
	
	function __construct($id, $nome, $permissao, $criado = '', $criador_id = 0, $modificado = '', $modificador_id = 0, $desativado = false){
		$this->funcao = new Funcao();	
		//Valida campos
		if(is_numeric($id) && $id > 0 && strlen($id) <= 3) $this->id = $id;
		if(strlen($nome) > 0 && strlen($nome) <= 20) $this->nome = $nome;
		if(is_array($permissao) && count($permissao) > 0) $this->permissao = $permissao;
		$this->criado = $this->funcao->limpar_data($criado);
		if(is_numeric($criador_id) && $criador_id > 0 && strlen($criador_id) <= 5) $this->criador_id = $criador_id;
		$this->modificado = $this->funcao->limpar_data($modificado);
		if(is_numeric($modificador_id) && $modificador_id > 0 && strlen($modificador_id) <= 5) $this->modificador_id = $modificador_id;
		if(!is_null($desativado) && $desativado !== false) $this->desativado = true;
	}
	
	public function __get( $key )
    {
		//Para pegar a lista de permissões, usa-se a chave permissao_array
		if($key == 'permissao_array')
			return $this->permissao;
		
		//Chave permissao é uma função
        if($key != 'permissao')
			return (isset($this->$key)?$this->$key:'');
    }
	
	public function __set( $key, $value )
    {
		if($key == 'id'){
			if(is_numeric($value) && $value > 0 && strlen($value) <= 3) $this->id = $value;
		}
	}
	
	//boolean false O USUÁRIO NÃO TEM PERMISSÃO PARA ACESSAR UMA OU MAIS AÇÕES DO MÓDULO
	//number INFORMA O TIPO DE PERMISSAO QUE USUÁRIO TEM NA AÇÃO DO MÓDULO
	//array INFORMA O TIPO DE PERMISSAO QUE USUÁRIO TEM EM TODAS AS AÇÕES DO MÓDULO
	public function permissao($modulo, $acao = null){
		//Verifica se recebeu as permissões do usuário
		if(is_null($this->permissao))
			return false;
		//Verifica se o usuário tem permissão para a ação solicitada
		if(!is_null($acao)){
			//Retorna o tipo de permissão
			if(isset($this->permissao[$modulo][$acao])) return ($this->permissao[$modulo][$acao] == 0 ? false : $this->permissao[$modulo][$acao]);
			else return false;
		}else{
			//Retorna a lista de ações e seus tipor de permissão
			if(isset($this->permissao[$modulo])) return $this->permissao[$modulo];
			else return false;
		}
	}
}
?>