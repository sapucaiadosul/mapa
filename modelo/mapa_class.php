<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class Mapa{
	private $id = 0;
	private $nome = '';
	
	function __construct($id, $nome){
		//Valida campos
		if(is_numeric($id) && $id > 0 && strlen($id) <= 3) $this->id = $id;
		if(strlen($nome) > 0 && strlen($nome) <= 40) $this->nome = $nome;
	}
	
	public function __get($key){
		return (isset($this->$key)?$this->$key:'');
    }
}
?>