<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class Acompanhamento{
	private $id = 0;
	private $metaId = 0;
	private $texto = '';
	private $tipo = 0;
	private $criado = '';
	private $criadorId = 0;
	private $modificado = '';
	private $modificadorId = 0;
	private $dataHora = '';
	private $usuarioNome = '';
	private $usuarioPerfil = '';
	private $desativado = false;
        private $modificador_nome = '';
	
	function __construct($id, $metaId, $texto, $tipo = 0, $criado = '', $criadorId = 0, $modificado = '', $modificadorId = 0, $usuarioNome = '', $usuarioPerfil = '', $desativado = false, $modificador_nome = ''){
		//Valida campos
		$this->__set('metaId', $metaId);
		if(is_numeric($id) && $id > 0 && strlen($id) <= 9) $this->id = $id;
		if(strlen($texto) > 0) $this->texto = $texto;
		if(is_numeric($tipo) && $tipo >= 0 && $tipo <= 3) $this->tipo = $tipo;
		$this->criado = $this->funcao->limpar_data($criado);
		if(is_numeric($criadorId) && $criadorId > 0 && strlen($criadorId) <= 5) $this->criadorId = $criadorId;
		$this->modificado = $this->funcao->limpar_data($modificado);
		if(is_numeric($modificadorId) && $modificadorId > 0 && strlen($modificadorId) <= 5) $this->modificadorId = $modificadorId;
		if(strlen($usuarioNome) > 0 && strlen($usuarioNome) <= 40) $this->usuarioNome = $usuarioNome;
		if(strlen($usuarioPerfil) > 0 && strlen($usuarioPerfil) <= 20) $this->usuarioPerfil = $usuarioPerfil;
		if(!is_null($desativado) && $desativado !== false) $this->desativado = true;
                $this->modificador_nome = $modificador_nome;
		
		//Guarda data e hora para exibição na tela

                 /*
		if(strlen($this->modificado) > 0){
			$data = explode('-', $this->modificado);
			preg_match('/[0-9]{2}:[0-9]{2}/', $modificado, $hora);
		}else{
			$data = explode('-', $this->criado);
			preg_match('/[0-9]{2}:[0-9]{2}/', $criado, $hora);
		}
		*/
		$data = explode('-', $this->criado);
		preg_match('/[0-9]{2}:[0-9]{2}/', $criado, $hora);



		if(count($data) == 3) $this->dataHora = $data[2].'/'.$data[1].'/'.$data[0].(count($hora) == 1 ? ' - '.$hora[0] : '');
	}
	
	public function __get($key){
		return (isset($this->$key)?$this->$key:'');
    }
	
	public function __set($key, $value){
		if($key == 'metaId')
			if(is_numeric($value) && $value > 0 && strlen($value) <= 5) $this->metaId = $value;
	}
}
?>