<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class ModuloControle{
	private $login = null;
	private $banco = null;
	private $dao = null;
	private $modulo_dependente = array('perfil');
	private $modulo_arquivo = 'modulo';
	private $funcao = null; //Funcao
	
	function __construct($login, $banco){
		$this->funcao = new Funcao();
		//Carrega classe DAO
		if(!$this->funcao->carrega_arquivo('modelo', 'modulo_dao')) return false;
		
		//Valida objeto de configuração
		if(get_class($login) != 'LoginControle') return false;
		if(get_class($banco) != 'Banco') return false;
		
		//Guarda objetos para uso futuro
		$this->login = $login;
		$this->banco = $banco;
	}
	
	public function lista_perfil($perfil = null){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return false;
		//Valida parâmetro
		if(is_object($perfil) && get_class($perfil) != 'Perfil') $perfil = null;
		
		//Verifica se o usuário tem permissão de acesso
		if(!(in_array($_GET['mod'],$this->modulo_dependente) && is_numeric($this->login->permissao($_GET['mod'],LISTAR)))) return array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo');
		
		//Carrega tabela
		$table = $this->table_permissao($perfil);
		
		return $table;
	}
	
	private function table_permissao($perfil = null){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return false;
		
		//Valida parâmetro
		if(is_object($perfil) && get_class($perfil) != 'Perfil') $perfil = null;
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new ModuloDao($this->banco);
		
		//Seleciona registros para montar a tag select
		$registros = $this->dao->selecionar();
		
		//Verifica se encontrou algum retistro
		if(gettype($registros) == 'string') return array(MENSAGEM_ERRO=>$registros);
		
		//Monta opções do select
		$linha = '';
		foreach($registros as $registro){
			//Carrega ou limpa permissões do módulo
			if(!is_null($perfil)){
				$permissao = $perfil->permissao($registro->arquivo);
				//Se não tiver retorno é porque está salvando o perfil, guardando apenas o ID do módulo
				if(!is_array($permissao) && !$permissao) $permissao = $perfil->permissao($registro->id);
			}else{
				$permissao = null;
			}
			
			$linha.= '<input name="modulo[]" type="hidden" value="'.$registro->id.'">';
			
			$linha.= '<tr>';
			$linha.= '<td><label class="control-label">'.$registro->nome.'</label></td>';
			$linha.= '<td>';
			$linha.= '<select class="form-control" name="listar_'.$registro->id.'">';
			$linha.= '<option value="0">Nenhum</option>';
			$linha.= '<option'.(isset($permissao[LISTAR])?($permissao[LISTAR]==PROPRIOS?' selected':''):'').' value="'.PROPRIOS.'">Próprios</option>';
			$linha.= '<option'.(isset($permissao[LISTAR])?($permissao[LISTAR]==TODOS?' selected':''):'').' value="'.TODOS.'">Todos</option>';
			$linha.= '<option'.(isset($permissao[LISTAR])?($permissao[LISTAR]==EXCLUIDOS?' selected':''):'').' value="'.EXCLUIDOS.'">Até excluídos</option>';
			$linha.= '</select>';
			$linha.= '</td>';
			$linha.= '<td>';
			$linha.= '<select class="form-control" name="cadastrar_'.$registro->id.'">';
			$linha.= '<option value="0">Não</option>';
			$linha.= '<option'.(isset($permissao[CADASTRAR])?($permissao[CADASTRAR]==true?' selected':''):'').' value="1">Sim</option>';
			$linha.= '</select>';
			$linha.= '</td>';
			$linha.= '<td>';
			$linha.= '<select class="form-control" name="editar_'.$registro->id.'">';
			$linha.= '<option value="0">Nenhum</option>';
			$linha.= '<option'.(isset($permissao[EDITAR])?($permissao[EDITAR]==PROPRIOS?' selected':''):'').' value="'.PROPRIOS.'">Próprios</option>';
			$linha.= '<option'.(isset($permissao[EDITAR])?($permissao[EDITAR]==TODOS?' selected':''):'').' value="'.TODOS.'">Todos</option>';
			$linha.= '</select>';
			$linha.= '</td>';
			$linha.= '<td>';
			$linha.= '<select class="form-control" name="excluir_'.$registro->id.'">';
			$linha.= '<option value="0">Nenhum</option>';
			$linha.= '<option'.(isset($permissao[EXCLUIR])?($permissao[EXCLUIR]==PROPRIOS?' selected':''):'').' value="'.PROPRIOS.'">Próprios</option>';
			$linha.= '<option'.(isset($permissao[EXCLUIR])?($permissao[EXCLUIR]==TODOS?' selected':''):'').' value="'.TODOS.'">Todos</option>';
			$linha.= '</select>';
			$linha.= '</td>';
			$linha.= '</tr>';
		}
		
		return $linha;
	}
}
?>