<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class Anexo{
	private $id = 0;
	private $metaId = 0;
	private $nome = '';
	private $nomeTemporario = '';
	private $extensao = '';
	private $criado = '';
	private $criadorId = 0;
	private $modificado = '';
	private $modificadorId = 0;
	private $desativado = false;
	
	function __construct($id, $metaId, $nome, $extensao, $criado = '', $criadorId = 0, $modificado = '', $modificadorId = 0, $desativado = false, $nomeTemporario = ''){
		//Valida campos
		$this->__set('id', $id);
		$this->__set('metaId', $metaId);
		if(strlen($nome) > 0 && strlen($nome) <= 80) $this->nome = $nome;
		if(strlen($extensao) > 0 && strlen($extensao) <= 4) $this->extensao = $extensao;
		$this->criado = $this->funcao->limpar_data($criado);
		if(is_numeric($criadorId) && $criadorId > 0 && strlen($criadorId) <= 5) $this->criadorId = $criadorId;
		$this->modificado = $this->funcao->limpar_data($modificado);
		if(is_numeric($modificadorId) && $modificadorId > 0 && strlen($modificadorId) <= 5) $this->modificadorId = $modificadorId;
		if(!is_null($desativado) && $desativado !== false) $this->desativado = true;
		if(strlen($nomeTemporario) > 0 && file_exists($nomeTemporario)) $this->nomeTemporario = $nomeTemporario;
	}
	
	public function __get($key){
		return (isset($this->$key)?$this->$key:'');
    }
	
	public function __set($key, $value){
		if($key == 'id')
			if(is_numeric($value) && $value > 0 && strlen($value) <= 9) $this->id = $value;
		if($key == 'metaId')
			if(is_numeric($value) && $value > 0 && strlen($value) <= 5) $this->metaId = $value;
	}
}
?>