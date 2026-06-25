<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class UsuarioDAO{
	private $login = null;
	private $banco = null;
	private $modulo_arquivo = 'usuario';
	private $funcao = null; //Funcao
	
	function __construct($login, $banco){
		$this->funcao = new Funcao();
		//Carrega classe Usuario
		if(!($this->funcao->carrega_arquivo('modelo', 'usuario_class'))) return false;
		
		//Valida objeto de configuração
		if(get_class($banco) != 'Banco') return false;
		if(get_class($login) != 'LoginControle') return false;
		
		//Guarda objeto do banco para conexão
		$this->login = $login;
		$this->banco = $banco;
	}


	public function selecionarPorTexto($texto){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return 'Não foi identificada conexão com o banco de dados';

		//Verifica se o usuário tem permissão de acesso
		if((!is_numeric($this->login->permissao($this->modulo_arquivo,LISTAR)) && is_null($id)) && (!is_numeric($this->login->permissao($this->modulo_arquivo,EDITAR)) && !is_null($id))) return 'Você não tem permissão para acessar o conteúdo';
		
		//Inicia lista de parâmetros
		$parametros = array();

		if(isset($texto) && strlen($texto) > 0) {
			$palavras = explode(' ', $texto);
			$p = ' (u.nome like "%' . $palavras[0] . '%" or ';
			$p .= ' u.email like "%' . $palavras[0] . '%" or ';
			$p .= ' p.nome like "%' . $palavras[0] . '%" or ';
			$p .= ' u.usuario like "%' . $palavras[0] . '%")  ';
			array_shift($palavras);
			foreach($palavras as $plv) {
				$p .= ' and (u.nome like "%' . $plv . '%" or ';
				$p .= ' u.email like "%' . $plv . '%" or ';
				$p .= ' p.nome like "%' . $plv . '%" or ';
				$p .= ' u.usuario like "%' . $plv . '%") ';
			}
			$p .= '';
		}
		$requisicao = 'select u.id, u.nome, u.email, u.usuario, u.provisoria, u.perfil_id, p.nome as perfil, u.criado, u.criador_id, u.modificado, u.modificador_id, u.desativado from usuario u left join perfil p on p.id = u.perfil_id where ' . $p;

		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);

		//Verifica o resultado obtido
		if(is_string($resultado)) return $resultado;
		if(!is_array($resultado)) return 'Não foi possível carregar a informação';
		if(count($resultado) == 0) return 'Nenhum usuário encontrado';
		
		//Retorna um ARRAY, agora de objetos e não mais de ARRAYs
		return $this->array_to_object($resultado);
	}
	
	public function selecionar($id = null, $ativos = true, $pagina = 1, $num_registros = 0){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return 'Não foi identificada conexão com o banco de dados';
		//Valida parâmetro
		if(!is_numeric($id)) $id = null;
		if(!is_bool($ativos) && !is_null($ativos)) $ativos = true;
		if(!is_numeric($pagina)) $pagina = 1;
		if(!is_numeric($num_registros) || $num_registros == 0) $num_registros = Config::$db_limite;
		
		//Verifica se o usuário tem permissão de acesso
		if((!is_numeric($this->login->permissao($this->modulo_arquivo,LISTAR)) && is_null($id)) && (!is_numeric($this->login->permissao($this->modulo_arquivo,EDITAR)) && !is_null($id))) return 'Você não tem permissão para acessar o conteúdo';
		
		//Inicia lista de parâmetros
		$parametros = array();
		
		//Monta requisição
		$requisicao = 'select u.id, u.nome, u.email, u.usuario, u.provisoria, u.perfil_id, p.nome as perfil, u.criado, u.criador_id, u.modificado, u.modificador_id, u.desativado from usuario u left join perfil p on p.id = u.perfil_id';
		if(is_numeric($id)){
			$requisicao.= ' where u.id=?';
			//Adiciona parâmetros da requisição
			$parametros[] = $id;
			//Verifica as permissões
			if($this->login->permissao($this->modulo_arquivo,LISTAR) != EXCLUIDOS){
				$requisicao.= ' and u.desativado is null';
				if($this->login->permissao($this->modulo_arquivo,LISTAR) == PROPRIOS){
					$requisicao.= ' and u.criador_id=?';
					//Adiciona parâmetros da requisição
					$parametros[] = $this->login->id;
				}
			}
		}else{
			//Verifica as permissões
			if($this->login->permissao($this->modulo_arquivo,LISTAR) != EXCLUIDOS || $ativos == true){
				$requisicao.= ' where u.desativado is null';
				if($this->login->permissao($this->modulo_arquivo,LISTAR) == PROPRIOS){
					$requisicao.= ' and u.criador_id=?';
					//Adiciona parâmetros da requisição
					$parametros[] = $this->login->id;
				}
			}
		}
		$requisicao.= ' order by u.nome';
		//Verifica se deve fazer paginação
		if(is_bool($ativos)){
			$requisicao.= ' limit '.(($pagina*$num_registros)-$num_registros).','.$num_registros;
		}
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(is_string($resultado)) return $resultado;
		if(!is_array($resultado)) return 'Não foi possível carregar a informação';
		if(count($resultado) == 0) return 'Nenhum usuário encontrado';
		
		//Retorna um ARRAY, agora de objetos e não mais de ARRAYs
		return $this->array_to_object($resultado);
	}
	
	public function numero_registros($ativos = true){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return 'Não foi identificada conexão com o banco de dados';
		//Verifica parâmetros
		if(!is_bool($ativos)) $ativos = true;
		
		//Monta requisição
		$requisicao = 'select count(*) as registros from usuario u';
		//Verifica as permissões
		if($this->login->permissao($this->modulo_arquivo,LISTAR) != EXCLUIDOS || $ativos == true){
			$requisicao.= ' where u.desativado is null';
			if($this->login->permissao($this->modulo_arquivo,LISTAR) == PROPRIOS){
				$requisicao.= ' and u.criador_id=?';
				//Adiciona parâmetros da requisição
				$parametros[] = $this->login->id;
			}
		}
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao);
		
		//Verifica o resultado obtido
		if(is_string($resultado)) return $resultado;
		if(!is_array($resultado)) return 'Não foi possível carregar a informação';
		if(count($resultado) != 1) return 'Não foi possível carregar a informação';
		
		//Retorna a quantidade de registros
		return $resultado[0]['registros'];
	}
	
	public function salvar($registro, $login){
		//Valida parâmetros
		if(gettype($registro) != 'object' || (gettype($registro) == 'object' && get_class($registro) != 'Usuario')) return array(MENSAGEM_ERRO=>'Dados inválidos. Tente novamente.');
		if(gettype($login) != 'object' || (gettype($login) != 'object' && get_class($login) != 'LoginControle')) return array(MENSAGEM_ERRO=>'Login inválido');
		
		//Verifica se o usuário tem permissão de acesso
		if(is_numeric($registro->id) && $registro->id > 0){
			//Verifica permissão para editar
			if(!is_numeric($this->login->permissao($this->modulo_arquivo,EDITAR))) return array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo');
		}else{
			//Verifica permissão para cadastrar
			if(!is_numeric($this->login->permissao($this->modulo_arquivo,CADASTRAR))) return array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo');
		}
		
		//Tendo ID atualiza dados
		if(is_numeric($registro->id) && $registro->id > 0){
			//Verifica permissão para editar
			if(!is_numeric($this->login->permissao($this->modulo_arquivo,EDITAR))) return array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo');
			//Requisições diferentes para com e sem senha
			if(strlen($registro->senha) > 0){
				//Monta requisição
				$requisicao = 'update usuario set perfil_id=?, usuario=?, senha=md5(?), provisoria='.($registro->provisoria?true:'NULL').', nome=?, email=?, modificado=now(), modificador_id=? where id=?';
				//Monta parâmetros
				$parametros = array($registro->perfil_id, $registro->usuario, $registro->senha, $registro->nome, $registro->email, $login->id, $registro->id);
			}else{
				//Monta requisição
				$requisicao = 'update usuario set perfil_id=?, usuario=?, nome=?, email=?, modificado=now(), modificador_id=? where id=?';
				//Monta parâmetros
				$parametros = array($registro->perfil_id, $registro->usuario, $registro->nome, $registro->email, $login->id, $registro->id);
			}
		}else{
			//Verifica permissão para cadastrar
			if(!is_numeric($this->login->permissao($this->modulo_arquivo,CADASTRAR))) return array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo');
			
			//Monta requisição
			$requisicao = 'insert into usuario (perfil_id, usuario, senha, provisoria, nome, email, criado, criador_id) values(?, ?, md5(?), '.($registro->provisoria?true:'NULL').', ?, ?, now(), ?)';
			//Monta parâmetros
			$parametros = array($registro->perfil_id, $registro->usuario, $registro->senha, $registro->nome, $registro->email, $login->id);
		}
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(is_string($resultado) && !is_numeric($resultado)) return array(MENSAGEM_ERRO=>$resultado);
		//Se está atualizando, retorna o número de registros alterados, que deve ser apenas 1
		//Se está inserindo, retorna o ID do novo registro
		if((is_numeric($registro->id) && $registro->id > 0 && $resultado != 1) || !is_numeric($resultado)) return array(MENSAGEM_ERRO=>'Não foi possível salvar o registro');
		
		//Retorna o ID do registro
		return (is_numeric($registro->id) && $registro->id > 0 ? $registro->id : $resultado);
	}
	
	public function excluir($id, $criador_id){
		//Valida login
		if(!is_object($this->login) || get_class($this->login) != 'LoginControle') return 'Login inválido';
		
		//Valida parâmetros
		if(!is_numeric($id) || (is_numeric($id) && $id == 0)) return 'ID inválido';
		if(!is_numeric($criador_id) || (is_numeric($criador_id) && $criador_id == 0)) return 'Usuário de geração inválido';
		
		//Verifica o criador do registro
		if($criador_id == $this->login->id){
			//Verifica permissão para excluir
			if(!is_numeric($this->login->permissao($this->modulo_arquivo,EXCLUIR))) return 'Você não tem permissão para excluir o conteúdo';
		}else{
			//Verifica permissão para excluir qualquer arquivo
			if($this->login->permissao($this->modulo_arquivo,EXCLUIR) != TODOS) return 'Você não tem permissão para excluir o conteúdo';
		}
		
		//Monta requisição
		$requisicao = 'update usuario set desativado=IF(desativado is null, true, null), modificado=now(), modificador_id=? where id=?';
		
		//Monta parâmetros
		$parametros = array($this->login->id, $id);
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(is_string($resultado) && !is_numeric($resultado)) return $resultado;
		//Retorna o número de registros alterados, que deve ser apenas 1
		if($resultado != 1 || !is_numeric($resultado)) return 'Não foi possível excluir o registro';
		
		//Excluindo, retorna TRUE para informar êxito
		return true;
	}
	
	public function existe($id, $usuario){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return 'Não foi identificada conexão com o banco de dados';
		//Valida parâmetro
		if(!is_numeric($id)) return false;
		if(strlen($usuario) == 0 || strlen($usuario) > 20) return false;
		
		//Inicia lista de parâmetros
		$parametros = array();
		
		//Monta requisição
		$requisicao = 'select count(*) as qtd from usuario where usuario like ? and id != ?';
		//Adiciona parâmetros da requisição
		$parametros[] = $usuario;
		$parametros[] = $id;
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(is_string($resultado)) return $resultado;
		if(!is_array($resultado)) return 'Não foi possível carregar a informação';
		if(count($resultado) == 0) return false;
		if($resultado[0]['qtd'] == 0) return false;
		
		//Informa que encontrou o usuário
		return true;
	}
	
	private function array_to_object($array = null){
		//Verifica existência da classe necessária
		if(!class_exists('Usuario')) return 'Não foi identificada a classe necessária';
		if(!class_exists('Perfil')) return 'Não foi identificada a classe necessária';
		
		//Valida parâmetro
		if(!is_array($array)) return false;
		
		//Inicia o array de objeto
		$object = array();
		
		//Converte arrays em objects
		foreach($array as $registro){
			//Cria perfil com apenas as informações necessárias
			$perfil = new Perfil($registro['perfil_id'], $registro['perfil'], null);
			//Cria objeto e adiciona à lista
			$object[] = new Usuario($registro['id'], $registro['nome'], $registro['usuario'], null, $perfil, $registro['provisoria'], false, $registro['criado'], $registro['criador_id'], $registro['modificado'], $registro['modificador_id'], $registro['desativado'], $registro['email']);
		}
		
		return $object;
	}
}
?>
