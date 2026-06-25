<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 * @package		
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 */
 
//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

//Classes requerentes
require_once "./config.php";
require_once './define.php';
require_once "./controle/login.php";
require_once "./modelo/banco.php";
require_once "./modelo/funcoes.php";

Class Carrega{
	//Controle de acesso
	private $banco = null; //Banco
	private $login = null; //Login
	private $modulo = null; //Módulo base ativo
	//Inclusões
	private $css = null; //Array
	private $js = null; //Array
	//Visão
	private $title = ""; //String
	private $menu = ""; //String
	private $mensagem = ""; //String
	private $acoes = ""; //String
	private $breadcrumb = ""; //String
	private $conteudo = ""; //String
	private $funcao = null; //Funcao
	
	public function __construct(){
		//Inicia conexão com o banco de dados
		$this->banco();
		//Verifica login
		$this->login();

		$this->funcao = new Funcao();
	}
	
	public function permissao(){

		//Verifica permissão de acesso ao módulo informado
		if(isset($_GET['mod'])){
			//Guarda nome de arquivo do módulo
			$modulo = substr($this->funcao->limpaInput($_GET['mod']),0,20);
			//Verifica permissão de acesso ao módulo
			if(!is_array($this->login->permissao($modulo))){
				//Não pode acessar
				return false;
			}else{
				//Carrega o módulo permitido
				if($this->funcao->carrega_arquivo('controle',$modulo)){
					$modulo = strtoupper(substr($modulo,0,1)).substr($modulo,1).'Controle';
					$this->modulo = new $modulo($this->login, $this->banco);
					return true;
				}else{
					return false;
				}
			}
		}else{
			//Verifica se o usuário tem alguma permissão
			if(!is_array($this->login->permissao())) return false;
			
			//Informa que o usuário tem acesso ao sistema
			return true;
		}
	}
	
	public function template(){
		//Valida variável de login
		if(is_null($this->login) || !is_object($this->login) || get_class($this->login) != 'LoginControle') $this->login();
		//Verifica se o usuário está saindo do sistema
		if(isset($_GET['sair'])) $this->login->sair();
		//Verifica se está trocando de senha
		if(isset($_POST['form_name']) && $_POST['form_name'] == 'senha') $login_retorno = $this->login->trocar_senha();

		if(isset($_POST['form_name']) && $_POST['form_name'] == 'esqueci_senha') $login_retorno = $this->login->verefica_email();
		//Verifica se o usuário está logado
		//FORMULÁRIO DE LOGIN CASO NÃO CONSIGA LOGAR
		//FORMULÁRIO DE TROCA DE SENHA CASO SEJA PROVISÓRIA
		if(!isset($login_retorno)) $login_retorno = $this->login->logar();
		//Verifica se recebeu o ARRAY corretamente
		if(!is_array($login_retorno) || (isset($login_retorno[0]) && !is_string($login_retorno[0]))){
			//Informa a indisponibilidade
			$this->mensagem.= $this->funcao->mensagem('Não foi possível acessar o sistema',MENSAGEM_ERRO);
			//Monta retorno vazio para não passar adiante
			$login_retorno = array('<div></div>');
		}else{
			//Se existir, guarda mensagem recebida
			if(isset($login_retorno[1]) && is_array($login_retorno[1]))
				$this->mensagem.= $this->funcao->mensagem(current($login_retorno[1]),key($login_retorno[1]));
		}
		
		//Verifica se tem permissão para acessar o sistema
		//Se não tiver, carregará o formulário de login
		//Se saiu, o formulário carregará na tentativa de login acima
		if($this->permissao() == false) $this->conteudo = $this->logout(true);
		
		//Verifica se deve exibir os registros excluídos
		$this->excluidos();
		
		//Se está logado, carrega dados da estrutura
		$this->js = $this->js();
		$this->css = $this->css();
		$this->title = $this->title();
		$this->breadcrumb = $this->breadcrumb();
		
		//Verifica se está na tela de login
		if(strlen($login_retorno[0]) > 0 && $this->login->alterarsenha == false){
			//Verifica se o usuário saiu
			if(isset($_GET['sair']) && strlen($this->mensagem) == 0)
				$this->mensagem.= $this->funcao->mensagem('Você saiu do sistema');
			//Adiciona formulário de login à variável de conteúdo para exibir no corpo da página
			$this->conteudo = $login_retorno[0];
		}elseif(strlen($login_retorno[0]) == 0 && strlen($this->mensagem) > 0){
			//Se está acusando erro, mas não há conteúdo de login é porque não carregou algo do sistema
			//Limpa conteúdo, mostrando apenas o erro no template de abertura
			$this->conteudo = '';
		}elseif(is_string($this->conteudo) && strlen($this->conteudo) == 0){
			//Carrega o menu para todos as tela, mesmo que troca de senha
			$this->menu = $this->menu();
			//Se solicitada para trocar senha, carrega a tela correspondente
			if(strlen($login_retorno[0]) > 0 && $this->login->alterarsenha == true) $this->conteudo = $login_retorno[0];
			else{
				//Verifica se deve salvar algum registro
				$this->salvar();
				//Verifica se deve remover algum registro
				$this->excluir();
				//Carrega o conteúdo do módulo ou a tela inicial (boas vindas)
				$this->conteudo = $this->conteudo();
			}
			//Verifica se está em algum módulo
			if(!is_null($this->modulo) || (strlen($login_retorno[0]) > 0 && $this->login->alterarsenha == true)) $this->acoes = $this->acoes();
		}else $this->mensagem.= $this->funcao->mensagem('Não foi possível carregar o sistema',MENSAGEM_ERRO);
		
		//Carrega HTML do template
		$arquivo = './visao/template.php';
		if(!file_exists($arquivo)) return $this->logout();
		ob_start();
		include_once $arquivo;
		$template = ob_get_clean();
		
		return $template;
	}
	
	private function banco(){
		//Verifica se já possui banco iniciado
		if(!is_null($this->banco) && is_object($this->banco) && get_class($this->banco) == 'Banco') return true;
			
		//Verifica se existe a classe de banco
		if(!class_exists('Banco')) return false;
		
		//Verifica se existe a classe de configuração
		if(!class_exists('Config')) return false;
		
		//Inicia conexão com o banco de dados
		$this->banco = new Banco(new Config());
		
		//Verifica se iniciou corretamente
		if(is_object($this->banco) && get_class($this->banco) == 'Banco') return true;
		else return false;
	}
	
	private function login(){
		//Verifica se já possui login iniciado
		if(!is_null($this->login) && is_object($this->login) && get_class($this->login) == 'LoginControle') return true;
		
		//Verifica se existe a classe de login
		if(!class_exists('LoginControle')) return false;
		
		//Verifica se já possui banco iniciado
		if(is_null($this->banco) || !is_object($this->banco) || get_class($this->banco) != 'Banco')
			//Tenta conectar
			if(!$this->banco()) return false;
		
		//Inicia objeto de login
		$this->login = new LoginControle($this->banco);
		
		//Verifica se iniciou corretamente
		if(is_object($this->login) && get_class($this->login) == 'LoginControle') return true;
		else return false;
	}
	
	private function acoes(){
		//Carrega HTML da barra de acoes
		if((!is_null($this->modulo) && isset($_GET['formulario'])) || $this->login->alterarsenha == true || (property_exists($this->modulo, 'registro') && is_object($this->modulo->registro))){
			if(isset($_GET['mod']) && is_null($this->login->permissao($_GET['mod'],CADASTRAR)) && is_null($this->login->permissao($_GET['mod'],EDITAR)) && $this->login->alterarsenha == false) return '';
			//Template da barra de ações para salvar
			$arquivo = './visao/adm_acoes_salvar.php';
		}else{
			//Verifica se o usuário tem alguma permissão de ação
			if(is_null($this->login->permissao($_GET['mod'],CADASTRAR)) && is_null($this->login->permissao($_GET['mod'],EXCLUIR)) && $this->login->permissao($_GET['mod'],LISTAR) < EXCLUIDOS) return '';
			//Template da barra de ações para adicionar / remover / listar todos
			$arquivo = './visao/adm_acoes_adicionar.php';
		}
		
		if(!file_exists($arquivo)) return $this->logout();
		ob_start();
		include_once $arquivo;
		$barra = ob_get_clean();
		
		return $barra;
	}
	
	private function excluir(){
		//Verifica se está em um módulo
		if(is_null($this->modulo)) return false;
		if(!is_object($this->modulo)) return false;
		//Verifica se recebeu o(s) ID(s) à excluir
		if(!isset($_GET['id']) || (!is_numeric($_GET['id']) && !is_array($_GET['id']))) return false;
		//Verifica se recebeu o parâmetro de exclusão
		if(!isset($_GET['excluir'])) return false;
		
		//Verifica a existência da função no módulo
		if(!method_exists($this->modulo, 'excluir')) return false;
		
		//Executa a exclusão do(s) registro(s)
		$retorno = $this->modulo->excluir();
		
		//Verifica a execução da exclusão
		if($retorno === true) return $this->mensagem.= $this->funcao->mensagem('Registro(s) excluído(s) com sucesso', MENSAGEM_SUCESSO);
		if($retorno === false) return $this->mensagem.= $this->funcao->mensagem('Não foi possível excluir o(s) registro(s)', MENSAGEM_ERRO);
		if(is_array($retorno)) return $this->mensagem.= $this->funcao->mensagem(current($retorno),key($retorno));
		
		return false;
	}
	
	private function salvar(){
		//Verifica se está em um módulo
		if(is_null($this->modulo)) return false;
		if(!is_object($this->modulo)) return false;
		//Verifica se recebeu algum formulário
		if(!isset($_POST) || count($_POST) == 0) return false;
		
		//Verifica a existência da função no módulo
		if(!method_exists($this->modulo, 'salvar')) return false;
		
		//Executa a exclusão do(s) registro(s)
		$retorno = $this->modulo->salvar();
		
		//Verifica a execução da exclusão
		if($retorno !== false && is_numeric($retorno)){
			$this->mensagem.= $this->funcao->mensagem('Registro salvo com sucesso', MENSAGEM_SUCESSO);
			//Se, ao adicionar um novo registro, apenas clicar em salvar, o parâmetro ID existirá, mas será 0 (ZERO)
			if(isset($_GET['id']) && $_GET['id'] == 0) $_GET['id'] = $retorno;
		}elseif($retorno === false) $this->mensagem.= $this->funcao->mensagem('Não foi possível salvar o registro', MENSAGEM_ERRO);
		elseif(!is_array($retorno) || count($retorno) != 2) $this->mensagem.= $this->funcao->mensagem('Não foi possível salvar o registro', MENSAGEM_ERRO);
		else{
			//Verifique se há campos incorretos
			if(is_array($retorno[0])) $GLOBALS['campo_erro'] = $retorno[0];
			//Verifica se há mensagem de retorno
			if(is_array($retorno[1])) $this->mensagem.= $this->funcao->mensagem(current($retorno[1]),key($retorno[1]));
		}
		
		return true;
	}
	
	private function conteudo(){
		//Inicia conteúdo a ser exibido
		$conteudo = '';
		
		//Verifica se está em algum módulo
		if(is_null($this->modulo)){
			//Carrega HTML do conteúdo
			$arquivo = './visao/inicio.php';
			if(!file_exists($arquivo)) return false;
			ob_start();
			require_once $arquivo;
			$conteudo = ob_get_clean();
		}else{
			//Carrega dados
			if(isset($_GET['formulario']) || (property_exists($this->modulo, 'registro') && is_object($this->modulo->registro))) $retorno = $this->modulo->formulario(isset($_GET['id'])?(is_numeric($_GET['id'])?$_GET['id']:null):null);
			else $retorno = $this->modulo->lista(isset($_GET['pagina'])?(is_numeric($_GET['pagina'])?$_GET['pagina']:1):1);
			
			//Verifica se recebeu o retorno correto
			if(!is_array($retorno)) $this->mensagem.= $funcao->mensagem('Não foi possível carregar o módulo');
			elseif(count($retorno) >= 1){
				//Carrega conteúdo
				$conteudo = $retorno[0];
				
				//Verifica se há erro à ser exibido
				if(count($retorno) == 2){
					if(is_array($retorno[1])) $this->mensagem.= $this->funcao->mensagem(current($retorno[1]),key($retorno[1]));
					elseif(is_string($retorno[1])) $this->mensagem.= $this->funcao->mensagem($retorno[1]);
				}
			}
		}
		
		return $conteudo;
	}
	
	private function menu(){
		//Carregar itens de menu conforme permissões do usuário
		
		//Carrega classe do Menu
		if(!$this->funcao->carrega_arquivo('controle', 'menu')) return '';
		if(!class_exists('MenuControle')) return '';
		
		//Inicia objeto do menu
		$menu = new MenuControle($this->login, $this->banco);
		
		//Carrega itens de menu
		$itens = $menu->listar_itens();
		
		//Verifica se causou erro
		if(!is_array($itens)){
			$this->mensagem.= $this->funcao->mensagem('Não foi possível carregar os itens de menu',MENSAGEM_ERRO);
			return '';
		}else{
			//Verifica se há erro à ser exibido
			if(count($itens) == 2){
				if(is_array($itens[1])) $this->mensagem.= $this->funcao->mensagem(current($itens[1]),key($itens[1]));
				elseif(is_string($itens[1])) $this->mensagem.= $this->funcao->mensagem($itens[1]);
				return '';
			}
		}
		
		//Carrega HTML do menu
		$arquivo = './visao/menu.php';
		if(!file_exists($arquivo)) return $this->logout();
		ob_start();
		include_once $arquivo;
		$menu = ob_get_clean();
		
		return $menu;
	}
	
	private function js(){
		//Lista os arquivos do sistema que devem ser carregados
		$arquivo = Array();
		$arquivo[] = 'jquery.min';
		$arquivo[] = 'bootstrap.min';
		$arquivo[] = 'html5shiv.min';
		$arquivo[] = 'respond.min';
		$arquivo[] = 'sigem';
                $arquivo[] = 'select2.min';
		
		//Lista os arquivos do sistema que devem ser carregados
		if(isset(Config::$sistema_js) && is_array(Config::$sistema_js)) $arquivo = array_merge($arquivo, Config::$sistema_js);
		
		//Lista os arquivos do módulo que devem ser carregados
		if(!is_null($this->modulo) && method_exists($this->modulo,'js') && $this->login->alterarsenha == false) $arquivo = array_merge($arquivo, $this->modulo->js());
		
		//Remove arquivos duplicados
		$arquivo = array_unique($arquivo);
		
		//Monta TAGs
		$tag = '';
		foreach($arquivo as $a){
			//Monta caminho completo do arquivo
			$a = './js/'.$a.'.js';
			//Verifica se o arquivo existe
			if(file_exists($a)){
				if(strlen($tag) > 0) $tag.= PHP_EOL;
				$tag.= '<script src="'.$a.'" type="text/javascript"></script>';
			}
		}
		
		return $tag;
	}
	
	private function css(){
		//Lista os arquivos do sistema que devem ser carregados
		$arquivo = Array();
		$arquivo[] = 'bootstrap.min';
		$arquivo[] = 'sigem';
                $arquivo[] = 'select2.min';
		
		//Lista os arquivos do sistema que devem ser carregados
		if(isset(Config::$sistema_css) && is_array(Config::$sistema_css)) $arquivo = array_merge($arquivo, Config::$sistema_css);
		
		//Lista os arquivos do módulo que devem ser carregados
		if(!is_null($this->modulo) && method_exists($this->modulo,'css') && $this->login->alterarsenha == false) $arquivo = array_merge($arquivo, $this->modulo->css());
		
		//Adiciona CSS de sobrescrita para a tela de login
		if($this->permissao() == false) $arquivo[] = 'login';
		
		//Remove arquivos duplicados
		$arquivo = array_unique($arquivo);
		
		//Monta TAGs
		$tag = '';
		foreach($arquivo as $a){
			//Monta caminho completo do arquivo
			$a = './css/'.$a.'.css';
			//Verifica se o arquivo existe
			if(file_exists($a)){
				if(strlen($tag) > 0) $tag.= PHP_EOL;
				$tag.= '<link href="'.$a.'" rel="stylesheet">';
			}
		}
		
		return $tag;
	}
	
	private function title(){
		//Guarda nome do sistema para montar título a ser exibido
		$title = Config::$sistema_nome;
		
		//Se estiver em um módulo e não estiver com senha provisória, adiciona seu título
		if(!is_null($this->modulo) && $this->login->alterarsenha == false) $title = $title.' - '.$this->modulo->title();
		
		return $title;
	}
	
	private function breadcrumb(){
		//Se não está em um módulo ou tem senha provisória, não há breadcrumb
		if(is_null($this->modulo) || $this->login->alterarsenha == true) return '';
		
		//Cria campo que informa onde o usuário está
		$breadcrumb = '';
		$breadcrumb.= '<div class="row" id="breadcrumb">';
		$breadcrumb.= '<div class="col-xs-12">';
		
		$breadcrumb.= '<h3>';
		$breadcrumb.= $this->modulo->title();
		//Verifica se está adicionando ou editando
		if(isset($_GET['formulario']) || (property_exists($this->modulo, 'registro') && is_object($this->modulo->registro))){
			$breadcrumb.= '<span class="text-muted"> > ';
			if(isset($_GET['id'])) $breadcrumb.= 'Editando';
			else $breadcrumb.= 'Adicionando';
			$breadcrumb.= '</span>';
		}
		$breadcrumb.= '</h3>';
		
		$breadcrumb.= '</div>';
		$breadcrumb.= '</div>';
		
		return $breadcrumb;
	}
	
	private function excluidos(){
		//Verifica se deve listar todos ou não
		if(isset($_GET['todos'])){
			if(isset($_SESSION[$_GET['mod'].'_excluidos'])) unset($_SESSION[$_GET['mod'].'_excluidos']);
			else $_SESSION[$_GET['mod'].'_excluidos'] = true;
		}
	}
	
	private function logout($permissao = false){
		//Desloga
		$this->login->sair();
		
		//Carrega tela de login
		$login_retorno = $this->login->logar();
		//Verifica se recebeu o ARRAY corretamente
		if(!is_array($login_retorno) || (isset($login_retorno[0]) && !is_string($login_retorno[0]))){
			//Informa a indisponibilidade
			$this->mensagem.= $this->funcao->mensagem('Não foi possível acessar o sistema',MENSAGEM_ERRO);
			//Monta retorno vazio para não passar adiante
			$login_retorno = array('<div></div>');
		}else{
			//Se existir, guarda mensagem recebida
			//Guarda apenas se não estiver saindo por falta de permissão, já que a mensagem de login já foi exibida anteriormente
			if(isset($login_retorno[1]) && is_array($login_retorno[1]) && $permissao == false)
				$this->mensagem = $this->funcao->mensagem(current($login_retorno[1]),key($login_retorno[1]));
		}
		
		//Retorna tela de login
		return $login_retorno[0];
	}
}
?>