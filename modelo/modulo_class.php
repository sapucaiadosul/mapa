<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class Modulo{
	private $id = 0;
	private $nome = '';
	private $arquivo = '';
	private $criado = '';
	private $criador_id = 0;
	private $modificado = '';
	private $modificador_id = 0;
	private $desativado = false;
	private $funcao = null;
	
	function __construct($id, $nome, $arquivo, $criado = '', $criador_id = 0, $modificado = '', $modificador_id = 0, $desativado = false){
		$this->funcao = new Funcao();
		//Valida campos
		if(is_numeric($id) && $id > 0 && strlen($id) <= 2) $this->id = $id;
		if(strlen($nome) > 0 && strlen($nome) <= 20) $this->nome = $nome;
		if(strlen($arquivo) > 0 && strlen($arquivo) <= 20) $this->arquivo = $arquivo;
		$this->criado = $this->funcao->limpar_data($criado);
		if(is_numeric($criador_id) && $criador_id > 0 && strlen($criador_id) <= 5) $this->criador_id = $criador_id;
		$this->modificado = $this->funcao->limpar_data($modificado);
		if(is_numeric($modificador_id) && $modificador_id > 0 && strlen($modificador_id) <= 5) $this->modificador_id = $modificador_id;
		if(!is_null($desativado) && $desativado !== false) $this->desativado = true;
	}
	
	public function __get( $key )
    {
		return (isset($this->$key)?$this->$key:'');
    }
}
?>