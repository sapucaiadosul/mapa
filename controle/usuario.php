<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class UsuarioControle{
	private $login = null;
	private $banco = null;
	private $dao = null;
	private $registro = null;
	private $modulo_dependente = array();
	private $modulo_arquivo = 'usuario';
	private $funcao = null; //Funcao
	
	function __construct($login, $banco){
		$this->funcao = new Funcao();
		//Carrega classe DAO
		if(!$this->funcao->carrega_arquivo('modelo', 'usuario_dao')) return false;
		
		//Carrega classe de controle do Perfil
		if(!$this->funcao->carrega_arquivo('controle', 'perfil')) return false;
		
		//Valida objeto de configuração
		if(get_class($login) != 'LoginControle') return false;
		if(get_class($banco) != 'Banco') return false;
		
		//Guarda objetos para uso futuro
		$this->login = $login;
		$this->banco = $banco;
	}
	
	public function __get( $key )
    {
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
		return 'Usuário';
	}
	
	public function css(){
		//Lista de arquivos CSS necessários para o módulo
		return array();
	}
	
	public function js(){
		//Lista de arquivos JS necessários para o módulo
		return array('usuario');
	}
	
	public function numero_paginas($registros = 0){
		//Valida parâmetro
		if(!is_numeric($registros) || $registros == 0) $registros = Config::$db_limite;
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new UsuarioDao($this->login, $this->banco);
		
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
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new UsuarioDao($this->login, $this->banco);
		
		//Inicia lista de campos inválidos
		$campo_erro = array();
		
		//Valida senha
		if(isset($_POST['nova_senha']) && strlen($_POST['nova_senha']) > 0){
			if(!isset($_POST['repete_senha']) || strlen($_POST['repete_senha']) == 0 || $_POST['nova_senha'] != $_POST['repete_senha']){
				$campo_erro[] = 'repete_senha';
				$campo_erro[] = 'nova_senha';
			}
		}elseif(isset($_POST['repete_senha']) && strlen($_POST['repete_senha']) > 0 && (!isset($_POST['nova_senha']) || strlen($_POST['nova_senha']) == 0)){
			$campo_erro[] = 'repete_senha';
			$campo_erro[] = 'nova_senha';
		}
		if(count($campo_erro) == 0 && isset($_POST['nova_senha']) && strlen($_POST['nova_senha']) > 0 && isset($_POST['repete_senha']) && strlen($_POST['repete_senha']) > 0 && $_POST['nova_senha'] == $_POST['repete_senha']) $senha = $_POST['nova_senha'];
		
		//Carrega todos os campos do formulário validando-os
		$this->registro = new Usuario((isset($_POST['id'])?$_POST['id']:0), (isset($_POST['nome'])?$_POST['nome']:''), (isset($_POST['usuario'])?$_POST['usuario']:''), (isset($senha)?$senha:''), (isset($_POST['perfil'])?$_POST['perfil']:''), (isset($_POST['senha_provisoria'])?($_POST['senha_provisoria']?true:false):''), true, '', 0, '', 0 , false, (isset($_POST['email'])?$_POST['email']:''));
		
		//Marca os campos inválidos
		
		//DADOS
		if($this->registro->id == 0 && strlen($this->registro->senha) == 0){
			$campo_erro[] = 'nova_senha';
			$campo_erro[] = 'repete_senha';
		}
		if(strlen($this->registro->nome) == 0) $campo_erro[] = 'nome';
		if(strlen($this->registro->usuario) == 0) $campo_erro[] = 'usuario';
		if($this->dao->existe($this->registro->id, $this->registro->usuario)) $campo_erro[] = 'usuario_existe';
		if($this->registro->perfil_id == 0) $campo_erro[] = 'perfil';
		if(strlen($this->registro->email) == 0 && isset($_POST['email']) && strlen($_POST['email']) > 0) $campo_erro[] = 'email';
		
		//Se encontrar erros, retorna a lista de erros encontrados
		if(count($campo_erro) > 0) return array($campo_erro, array(MENSAGEM_PADRAO=>'Preencha os campos destacados'));
		
		//Se tem ID, verifica se tem permissão para editar todos. Senão, verifica se pode editar os próprios e se tem permissão para tal
		if(is_numeric($this->registro->id) && $this->registro->id > 0){
			if($this->login->permissao($this->modulo_arquivo,EDITAR) < TODOS){
				if($this->login->permissao($this->modulo_arquivo,EDITAR) != PROPRIOS || ($this->login->permissao($this->modulo_arquivo,EDITAR) == PROPRIOS && $this->criador_id($this->registro->id, $this->login->id) === false))
					return array('',array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo'));
			}
		}
		
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
		if(is_null($this->dao)) $this->dao = new UsuarioDao($this->login, $this->banco);
		
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
	
	public function criador_id($registro_id, $criador_id = null){
		//Valida parâmetros
		if(!is_numeric($registro_id)) return false;
		if(!is_numeric($criador_id)) $criador_id = null;
		
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return false;
		
		//Carrega o DAO, se ainda não carregou-o no objeto
		if(is_null($this->dao)) $this->dao = new UsuarioDao($this->login, $this->banco);
		
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
		if(is_null($this->dao)) $this->dao = new UsuarioDao($this->login, $this->banco);
		
		//Carrega classe modelo de Perfil
		if(!$this->funcao->carrega_arquivo('controle', 'perfil')) return array(MENSAGEM_ERRO=>'Não foi possível carregar o perfil');
		
		//Inicia objeto para gerar select de perfils
		$perfil = new PerfilControle($this->login, $this->banco);
		
		//Verifica existência da tela
		$visao_arquivo = './visao/usuario_visao_form.php';
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
		
		//Carrega tag SELECT
		$select = $perfil->select(isset($registro)?(!is_object($registro->perfil)?$registro->perfil_id:$registro->perfil->id):null);
		
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
		if(is_null($this->dao)) $this->dao = new UsuarioDao($this->login, $this->banco);
		
		if(isset($_GET['src']) && strlen($_GET['src']) > 0){		
			//Seleciona registros para montar a tabela
			$registros = $this->dao->selecionarPorTexto($_GET['src']);
		} else
		//Seleciona registros para montar a tabela
		$registros = $this->dao->selecionar(null, isset($_SESSION[$this->modulo_arquivo.'_excluidos'])?false:true, $pagina, $num_registros);
		
		//Verifica se encontrou algum retistro
		if(is_string($registros)) return array(MENSAGEM_ERRO=>$registros);
		if(!is_array($registros)) return array(MENSAGEM_ERRO=>'Não foi possível carregar os registros');
		
		//Verifica se o usuário tem permissão de editar
		$permissao_editar = $this->login->permissao($this->modulo_arquivo,EDITAR);
		$permissao_excluir = $this->login->permissao($this->modulo_arquivo,EXCLUIR);
		
		//Monta linhas da tabela
		$linhas = '<form method="get" action="index.php" ><div class="form-group col-md-6">
			<input autofocus class="form-control  mb-2" id="src" type="text" name="src" placeholder="Pesquisar por nome, e-mail, usuário ou a sigla da secretária" />
			<input type="hidden" name="mod" value="usuario" />
			<input style="display:none" type="submit" />
		</div></form>';
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
			$linhas.= '<td>'.($permissao_editar >= TODOS || ($permissao_editar >= PROPRIOS && $registro->criador_id == $this->login->id) ? '<a href="index.php?mod='.$this->modulo_arquivo.'&formulario&id='.$registro->id.'">' : '').$registro->email.($permissao_editar >= TODOS || ($permissao_editar >= PROPRIOS && $registro->criador_id == $this->login->id) ? '</a>' : '').'</td>';
			$linhas.= '<td>'.($permissao_editar >= TODOS || ($permissao_editar >= PROPRIOS && $registro->criador_id == $this->login->id) ? '<a href="index.php?mod='.$this->modulo_arquivo.'&formulario&id='.$registro->id.'">' : '').$registro->usuario.($permissao_editar >= TODOS || ($permissao_editar >= PROPRIOS && $registro->criador_id == $this->login->id) ? '</a>' : '').'</td>';
			$linhas.= '<td>'.($permissao_editar >= TODOS || ($permissao_editar >= PROPRIOS && $registro->criador_id == $this->login->id) ? '<a href="index.php?mod='.$this->modulo_arquivo.'&formulario&id='.$registro->id.'">' : '').$registro->perfil->nome.($permissao_editar >= TODOS || ($permissao_editar >= PROPRIOS && $registro->criador_id == $this->login->id) ? '</a>' : '').'</td>';
			if($permissao_excluir >= PROPRIOS){
				$linhas.= '<td class="text-center">';
				if($permissao_excluir >= TODOS || ($permissao_excluir >= PROPRIOS && $registro->criador_id == $this->login->id))
					$linhas.= ($registro->desativado?'<button class="btn btn-primary" onclick="registro_del(\''.(isset($_GET['mod'])?$_GET['mod']:'').'\','.$registro->id.',\''.$registro->nome.'\');"><span class="glyphicon glyphicon-ok"></span></button>':'<button class="btn btn-danger" onclick="registro_del(\''.(isset($_GET['mod'])?$_GET['mod']:'').'\','.$registro->id.',\''.$registro->nome.'\');"><span class="glyphicon glyphicon-remove"></span></button>');
				$linhas.= '</td>';
			}
			$linhas.= '</tr>';
		}
		
		//Verifica existência da tela
		$visao_arquivo = './visao/usuario_visao_table.php';
		if(!file_exists($visao_arquivo)) return array(MENSAGEM_ERRO=>'Não foi possível carregar a tabela de usuários');
		
		//Carrega HTML
		ob_start();
		include_once $visao_arquivo;
		$table = ob_get_clean();
		
		return $table;
	}
}
?>
