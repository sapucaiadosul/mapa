<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class PerfilControle{
	private $login = null;
	private $banco = null;
	private $dao = null;
	private $registro = null;
	private $modulo_dependente = array('usuario', 'meta');
	private $modulo_arquivo = 'perfil';
	private $funcao = null;
	
	function __construct($login, $banco){
		$this->funcao = new Funcao();
		//Carrega classe DAO
		if(!$this->funcao->carrega_arquivo('modelo', 'perfil_dao')) return false;
		
		//Carrega classe de módulos
		if(!$this->funcao->carrega_arquivo('controle', 'modulo')) return false;
		
		//Valida objeto de configuração
		if(get_class($login) != 'LoginControle') return false;
		if(get_class($banco) != 'Banco') return false;
		
		//Guarda objetos para uso futuro
		$this->login = $login;
		$this->banco = $banco;
	}
	
	public function __get( $key ){
		if($key == 'registro') return $this->registro;
	}
	
	public function lista($pagina = 1, $id = null){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return false;
		//Valida parâmetro
		if(!is_numeric($pagina) || $pagina < 1) $pagina = 1;
		if(!is_numeric($id)) $id = null;
		
		//Verifica se o usuário tem permissão de acesso
		if(!is_numeric($this->login->permissao($this->modulo_arquivo,LISTAR))) return array('',array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo'));
		
		//Carrega tabela
		$retorno = $this->table($pagina);
		
		//Verifica se recebeu erro
		if(is_array($retorno)) return array('',$retorno);
		if(!is_string($retorno)) return array('',array(MENSAGEM_ERRO=>'Não foi possível carregar os registros'));
		
		//Carrega conteúdo gerado
		$conteudo = $retorno;
		
		//Paginação
		$conteudo.= $this->funcao->pagina_criar_links($this->modulo_arquivo,$this->numero_paginas(), $pagina);
		
		return array($conteudo);
	}
	
	public function formulario($id = null){
		//Valida parâmetros
		if(!is_numeric($id) || $id < 1) $id = null;
		
		//Verifica se o usuário tem permissão de acesso
		if((!is_numeric($this->login->permissao($this->modulo_arquivo,EDITAR)) && !is_null($id)) || (!is_numeric($this->login->permissao($this->modulo_arquivo,CADASTRAR)) && is_null($id))) return array('',array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo'));
		
		//Carrega formulário de cadastro/alteração
		$retorno = $this->form($id);
		
		//Verifica se recebeu erro
		if(is_array($retorno)) return array('',$retorno);
		if(!is_string($retorno)) return array('',array(MENSAGEM_ERRO=>'Não foi possível carregar o formulário'));
		
		return array($retorno);
	}
	
	public function title(){
		return 'Perfil';
	}
	
	public function css(){
		//Lista de arquivos CSS necessários para o módulo
		return array();
	}
	
	public function js(){
		//Lista de arquivos JS necessários para o módulo
		return array('perfil');
	}
	
	public function numero_paginas($registros = 0){
		//Valida parâmetro
		if(!is_numeric($registros) || $registros == 0) $registros = Config::$db_limite;
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new PerfilDao($this->login, $this->banco);
		
		//Recebe o número de registros para calcular o número de páginas
		$numero_registros = $this->dao->numero_registros(isset($_SESSION[$this->modulo_arquivo.'_excluidos'])?false:true);
		
		//Valida retorno da seleção de registros
		if(!is_numeric($numero_registros)) return array(MENSAGEM_ERRO=>$numero_registros);
		
		//Calcula o número de páginas
		$numero_paginas = ceil($numero_registros / $registros);
		
		return $numero_paginas;
	}
	
	public function salvar(){
		//Limpa registro local
		$this->registro = null;
		
		//Verifica se recebeu o formulário
		if(!isset($_POST)) return array('');
		
		//Verifica se o usuário tem permissão de acesso
		if(!is_numeric($this->login->permissao($this->modulo_arquivo,CADASTRAR)) && !is_numeric($this->login->permissao($this->modulo_arquivo,EDITAR))) return array('',array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo'));
		
		//Inicia lista de campos inválidos
		$campo_erro = array();
		
		//Inicia lista de permissões
		$permissao = array();
			
		//Verifica se há módulos ativos para montar permissões
		if(isset($_POST['modulo']) && count($_POST['modulo']) > 0){
			//Percorre todos os módulos para montar lista de permissões
			foreach($_POST['modulo'] as $modulo){
				//Verifica se há permissões e monta lista
				if(isset($_POST['listar_'.$modulo]) && is_numeric($_POST['listar_'.$modulo]) && $_POST['listar_'.$modulo] > 0) $permissao[$modulo][LISTAR] = $_POST['listar_'.$modulo];
				if(isset($_POST['cadastrar_'.$modulo]) && is_numeric($_POST['cadastrar_'.$modulo]) && $_POST['cadastrar_'.$modulo] > 0) $permissao[$modulo][CADASTRAR] = $_POST['cadastrar_'.$modulo];
				if(isset($_POST['editar_'.$modulo]) && is_numeric($_POST['editar_'.$modulo]) && $_POST['editar_'.$modulo] > 0) $permissao[$modulo][EDITAR] = $_POST['editar_'.$modulo];
				if(isset($_POST['excluir_'.$modulo]) && is_numeric($_POST['excluir_'.$modulo]) && $_POST['excluir_'.$modulo] > 0) $permissao[$modulo][EXCLUIR] = $_POST['excluir_'.$modulo];
			}
		}
		
		//Carrega todos os campos do formulário validando-os
		$this->registro = new Perfil((isset($_POST['id'])?$_POST['id']:0), (isset($_POST['nome'])?$_POST['nome']:''), $permissao);
		
		//Marca os campos inválidos
		
		//DADOS
		if(strlen($this->registro->nome) == 0) $campo_erro[] = 'nome';
		
		//Se encontrar erros, retorna a lista de erros encontrados
		if(count($campo_erro) > 0) return array($campo_erro, array(MENSAGEM_PADRAO=>'Preencha os campos destacados'));
		
		//Se tem ID, verifica se tem permissão para editar todos. Senão, verifica se pode editar os próprios e se tem permissão para tal
		if(is_numeric($this->registro->id) && $this->registro->id > 0){
			if($this->login->permissao($this->modulo_arquivo,EDITAR) < TODOS){
				if($this->login->permissao($this->modulo_arquivo,EDITAR) != PROPRIOS || ($this->login->permissao($this->modulo_arquivo,EDITAR) == PROPRIOS && $this->criador_id($this->registro->id, $this->login->id) === false))
					return array('',array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo'));
			}
		}
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new PerfilDao($this->login, $this->banco);
		
		//Salva os dados
		$retorno = $this->dao->salvar($this->registro, $this->login);
		
		//Não houvendo erros, limpa variável para carregar os dados salvos
		if(is_numeric($retorno)){
			$this->registro = null;
			return $retorno;
		}elseif(is_array($retorno)) return array('',$retorno);
		elseif(is_string($retorno)) return array($retorno);
		else return false;
	}
	
	public function excluir(){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return array(MENSAGEM_ERRO=>'Não foi possível acessar o banco de dados');
		
		//Verifica se o usuário tem permissão de acesso
		if(!is_numeric($this->login->permissao($this->modulo_arquivo,EXCLUIR))) return array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo');
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new PerfilDao($this->login, $this->banco);
		
		//Verifica o que deve ser excluído e se realmente deve
		if(isset($_GET['excluir'])){
			//Pega lista de IDs ou gera uma com o único ID recebido, senão é NULL
			$ids = (isset($_GET['id'])?(is_numeric($_GET['id'])?array($_GET['id']):(is_array($_GET['id'])?$_GET['id']:null)):null);
			//Verifica se recebeu algum ID para excluir
			if(is_null($ids)) return false;
			
			//Percorre lista excluindo, se tiver permissão
			$apagou = 0;
			foreach($ids as $id){
				if(is_numeric($id)){
					if(!is_string($retorno = $this->dao->excluir($id, $this->criador_id($id)))){
						$apagou++;
					}else{
						//Monta mensagem de retorno
						$apagou = ($apagou > 0 ? $apagou.' registro(s) excluído(s). ':'').$retorno.', ID '.$id;
						break;
					}
				}
			}
			
			//Retorna mensagem de sucesso ou mensagens de erro
			return (is_numeric($apagou)?(count($ids)==$apagou?true:array(MENSAGEM_ERRO=>count($ids)-$apagou.' registros não foram excluídos')):array(MENSAGEM_ERRO=>$apagou));
		}
	}
	
	public function organizar_permissoes($array){
		//Inicia o array de objeto
		$permissao = array();
		
		//Valida parâmetro
		if(is_array($array)){
			//Converte arrays em objects
			foreach($array as $registro){
				$permissao[$registro['arquivo']][$registro['acao']] = $registro['tipo'];
			}
		}
		
		return $permissao;
	}
	
	public function select($id = null, $ocultarAdministrador = false){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return false;
		
		//Valida parâmetro
		if(!is_numeric($id)) $id = null;
		if(!is_bool($ocultarAdministrador)) $ocultarAdministrador = false;
		
		//Verifica se o usuário tem permissão de acesso
		if(!(in_array($_GET['mod'], $this->modulo_dependente) && (is_numeric($this->login->permissao($_GET['mod'],LISTAR)) || is_numeric($this->login->permissao($_GET['mod'],CADASTRAR)) || is_numeric($this->login->permissao($_GET['mod'],EDITAR))))) return array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo');
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new PerfilDao($this->login, $this->banco);
		
		//Seleciona registros ATIVOS para montar a tag select
		$registros = $this->dao->selecionar(null, true, 1, -1);
		
		//Verifica se encontrou algum retistro
		if(is_string($registros)) return array(MENSAGEM_ERRO=>$registros);
		if(!is_array($registros)) return array(MENSAGEM_ERRO=>'Não foi possível carregar os perfils');
		
		//Monta options da tabela
		$options = '';
		foreach($registros as $registro){
			//Pula registro do Administrador, se solicitado
			if($ocultarAdministrador == true && $registro->id == 1) continue;
			//Monta linha do select
			$options.= '<option'.(!is_null($id)?($registro->id == $id?' selected':''):'').' value="'.$registro->id.'">';
			$options.= $registro->nome;
			$options.= '</option>';
		}
		
		//Verifica existência da tela
		$visao_arquivo = './visao/perfil_visao_select.php';
		if(!file_exists($visao_arquivo)) return false;
		
		//Carrega HTML
		ob_start();
		include_once $visao_arquivo;
		$select = ob_get_clean();
		
		return $select;
	}
	
	public function criador_id($registro_id, $criador_id = null){
		//Valida parâmetros
		if(!is_numeric($registro_id)) return false;
		if(!is_numeric($criador_id)) $criador_id = null;
		
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return false;
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new PerfilDao($this->login, $this->banco);
		
		//Seleciona o registro
		$registros = $this->dao->selecionar($registro_id, false); //ATIVOS = FALSE, para mostrar o criador de qualquer registro
		
		//Verifica se encontrou algum registro
		if(is_string($registros)) return array(MENSAGEM_ERRO=>$registros);
		if(!is_array($registros) || count($registros) != 1) return array(MENSAGEM_ERRO=>'Não foi possível carregar o cadastro');
		
		//Separa o registro
		$registro = $registros[0];
		
		//Verifica o retorno esperado
		if(is_null($criador_id)){
			//Retorno a ID do criador
			return $registro->criador_id;
		}else{
			//Verifica se o usuário é o criador do registro
			if($registro->criador_id == $criador_id) return $registro;
			else return false;
		}
	}
	
	private function form($id = null){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return array(MENSAGEM_ERRO=>'Não foi possível conectar ao banco de dados');
		
		//Valida parâmetro
		if(!is_numeric($id)) $id = null;
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new PerfilDao($this->login, $this->banco);
		
		//Chama controlador extra para gerar lista de módulos
		$modulo = new ModuloControle($this->login, $this->banco);
		
		//Verifica existência da tela
		$visao_arquivo = './visao/perfil_visao_form.php';
		if(!file_exists($visao_arquivo)) return array(MENSAGEM_ERRO=>'Não foi possível carregar o formulário');
		
		//Se não salvou, carrega os dados informados, ignorando os dados do banco
		if(!is_null($this->registro)){
			//Dados
			$registro = $this->registro;
			
			//Desativa para ignorar dados do banco
			$id = null;
		}
		
		//Verifica se há ID válido para procurar
		if(is_numeric($id) && $id > 0){
			//Verifica se o usuário tem permissão de edição total
			if($this->login->permissao($this->modulo_arquivo,EDITAR) < TODOS){
				//Verifica se o usuário tem permissão para editar os próprios
				if($this->login->permissao($this->modulo_arquivo,EDITAR) < PROPRIOS) return array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo');
				else{
					//Recebe registro ou erro ou false (se não tiver permissão)
					$registro = $this->criador_id($id, $this->login->id);
					
					//Valida retorno
					if($registro === false) return array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo');
					if(is_array($registro)) return $registro;
					
					//Guarda registro para usar fora do objeto
					$this->registro = $registro;
				}
			}else{
				//Seleciona o registro
				$registros = $this->dao->selecionar($id, false); //ATIVOS = FALSE, para mostrar o criador de qualquer registro
				
				//Verifica se encontrou algum retistro
				if(is_string($registros)) return array(MENSAGEM_ERRO=>$registros);
				if(!is_array($registros) || count($registros) != 1) return array(MENSAGEM_ERRO=>'Não foi possível carregar o cadastro');
				
				//Separa o registro
				$registro = $registros[0];
				
				//Guarda registro para usar fora do objeto
				$this->registro = $registro;
			}
		}
		
		//Seleciona os módulos e, se tiver ID, as permissões do perfil
		$modulos = $modulo->lista_perfil(isset($registro)?$registro:null);
			
		//Carrega HTML
		ob_start();
		include_once $visao_arquivo;
		$form = ob_get_clean();
		
		return $form;
	}
	
	private function table($pagina = 1, $num_registros = 0){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return false;
		//Valida parâmetros
		if(!is_numeric($pagina)) $pagina = 1;
		if(!is_numeric($num_registros)) $num_registros = 0;
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new PerfilDao($this->login, $this->banco);
		
		//Seleciona registros para montar a tabela
		$registros = $this->dao->selecionar(null, isset($_SESSION[$this->modulo_arquivo.'_excluidos'])?false:true, $pagina, $num_registros);
		
		//Verifica se encontrou algum retistro
		if(is_string($registros)) return array(MENSAGEM_ERRO=>$registros);
		if(!is_array($registros)) return array(MENSAGEM_ERRO=>'Não foi possível carregar os registros');
		
		//Verifica se o usuário tem permissão de editar
		$permissao_editar = $this->login->permissao($this->modulo_arquivo,EDITAR);
		$permissao_excluir = $this->login->permissao($this->modulo_arquivo,EXCLUIR);
		
		//Monta linhas da tabela
		$linhas = '';
		foreach($registros as $registro){
			//Template criado no código devido ao alto número de validações
			$linhas.= '<tr'.($registro->desativado?' class="danger"':'').'>';
			if($permissao_excluir >= PROPRIOS){
				$pemissao_excluir_proprio = true;
				$linhas.= '<td class="text-center">';
				if(($permissao_excluir >= TODOS || ($permissao_excluir >= PROPRIOS && $registro->criador_id == $this->login->id)) && !$registro->desativado)
					$linhas.= '<input type="checkbox" value="'.$registro->id.'">';
				$linhas.= '</td>';
			}
			$linhas.= '<td class="text-center">'.($permissao_editar >= TODOS || ($permissao_editar >= PROPRIOS && $registro->criador_id == $this->login->id) ? '<a href="index.php?mod='.$this->modulo_arquivo.'&formulario&id='.$registro->id.'">' : '').$registro->id.($permissao_editar >= TODOS || ($permissao_editar >= PROPRIOS && $registro->criador_id == $this->login->id) ? '</a>' : '').'</td>';
			$linhas.= '<td>'.($permissao_editar >= TODOS || ($permissao_editar >= PROPRIOS && $registro->criador_id == $this->login->id) ? '<a href="index.php?mod='.$this->modulo_arquivo.'&formulario&id='.$registro->id.'">' : '').$registro->nome.($permissao_editar >= TODOS || ($permissao_editar >= PROPRIOS && $registro->criador_id == $this->login->id) ? '</a>' : '').'</td>';
			if($permissao_excluir >= PROPRIOS){
				$linhas.= '<td class="text-center">';
				if($permissao_excluir >= TODOS || ($permissao_excluir >= PROPRIOS && $registro->criador_id == $this->login->id))
					$linhas.= ($registro->desativado?'':'<button class="btn btn-danger" onclick="registro_del(\''.(isset($_GET['mod'])?$_GET['mod']:'').'\','.$registro->id.',\''.$registro->nome.'\');"><span class="glyphicon glyphicon-remove"></span></button>');
				$linhas.= '</td>';
			}
			$linhas.= '</tr>';
		}
		
		//Verifica existência da tela
		$visao_arquivo = './visao/perfil_visao_table.php';
		if(!file_exists($visao_arquivo)) return array(MENSAGEM_ERRO=>'Não foi possível carregar a tabela de perfis');
		
		//Carrega HTML
		ob_start();
		include_once $visao_arquivo;
		$table = ob_get_clean();
		
		return $table;
	}
}
?>