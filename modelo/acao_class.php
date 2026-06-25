<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class Acao{
	private $id = 0;
	private $metaId = 0;
	private $nome = '';
	private $prazo = '';
	private $concluida = '';
	private $concluidaId = 0;
	private $monitorada = '';
	private $monitorId = 0;
	private $aprovada = false;
	private $criado = '';
	private $criadorId = 0;
	private $modificado = '';
	private $modificadorId = 0;
	private $desativado = false;
	
	function __construct($id, $metaId, $nome, $prazo ='', $concluida = '', $concluidaId = 0, $monitorada = '', $monitorId = 0, $aprovada = false, $criado = '', $criadorId = 0, $modificado = '', $modificadorId = 0, $desativado = false){
		//Valida campos
		$this->__set('id', $id);
		$this->__set('metaId', $metaId);
		if(strlen($nome) > 0 && mb_strlen($nome) <= 200) $this->nome = $nome;
		$this->prazo = $this->funcao->limpar_data($prazo);
		$this->concluida = $this->funcao->limpar_data($concluida);
		if(is_numeric($concluidaId) && $concluidaId > 0 && strlen($concluidaId) <= 5) $this->concluidaId = $concluidaId;
		$this->monitorada = $this->funcao->limpar_data($monitorada);
		if(is_numeric($monitorId) && $monitorId > 0 && strlen($monitorId) <= 5) $this->monitorId = $monitorId;
		if(!is_null($aprovada) && $aprovada != false) $this->aprovada = true;
		$this->criado = $this->funcao->limpar_data($criado);
		if(is_numeric($criadorId) && $criadorId > 0 && strlen($criadorId) <= 5) $this->criadorId = $criadorId;
		$this->modificado = $this->funcao->limpar_data($modificado);
		if(is_numeric($modificadorId) && $modificadorId > 0 && strlen($modificadorId) <= 5) $this->modificadorId = $modificadorId;
		if(!is_null($desativado) && $desativado !== false) $this->desativado = true;
	}
	
	public function __get($key){
		if($key == 'idString') return ($this->id == 0 ? '' : $this->id);
		if($key == 'prazoFormatada' && strlen($this->prazo) == 10) return explode('-',$this->prazo)[2].'/'.explode('-',$this->prazo)[1].'/'.explode('-',$this->prazo)[0];
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
