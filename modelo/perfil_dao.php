<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class PerfilDAO{
	private $login = null;
	private $banco = null;
	private $modulo_arquivo = 'perfil';
	private $funcao = null; //Funcao
	private $perfilControle = null; //Permissao
	
	function __construct($login, $banco){
		
		$this->funcao = new Funcao();
		//Carrega classe Perfil
		if(!($this->funcao->carrega_arquivo('modelo', 'perfil_class'))) return false;
		
		//Valida objetos de configuração
		if(get_class($banco) != 'Banco') return false;
		if(get_class($login) != 'LoginControle') return false;
		
		//Guarda objetos para uso futuro
		$this->login = $login;
		$this->banco = $banco;
		$this->perfilControle = new PerfilControle($this->login, $this->banco);
	}
	
	public function selecionar($id = null, $ativos = true, $pagina = 1, $num_registros = 0){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return 'Não foi identificada conexão com o banco de dados';
		//Valida parâmetros
		if(!is_numeric($id)) $id = null;
		if(!is_bool($ativos) && !is_null($ativos)) $ativos = true;
		if(!is_numeric($pagina)) $pagina = 1;
		if(!is_numeric($num_registros) || $num_registros == 0) $num_registros = Config::$db_limite;
		
		//Verifica se o usuário tem permissão de acesso
		if((!is_numeric($this->login->permissao($this->modulo_arquivo,LISTAR)) && is_null($id)) && (!is_numeric($this->login->permissao($this->modulo_arquivo,EDITAR)) && !is_null($id))) return 'Você não tem permissão para acessar o conteúdo';
		
		//Inicia lista de parâmetros
		$parametros = array();
		
		//Monta requisição
		$requisicao = 'select p.id, p.nome, p.criado, p.criador_id, p.modificado, p.modificador_id, p.desativado from perfil p';
		if(is_numeric($id)){
			$requisicao.= ' where p.id=?';
			//Adiciona parâmetros da requisição
			$parametros[] = $id;
			//Verifica as permissões
			if($this->login->permissao($this->modulo_arquivo,LISTAR) != EXCLUIDOS){
				$requisicao.= ' and p.desativado is null';
				if($this->login->permissao($this->modulo_arquivo,LISTAR) == PROPRIOS){
					$requisicao.= ' and p.criador_id=?';
					//Adiciona parâmetros da requisição
					$parametros[] = $this->login->id;
				}
			}
		}else{
			//Verifica as permissões
			if($this->login->permissao($this->modulo_arquivo,LISTAR) != EXCLUIDOS || $ativos == true){
				$requisicao.= ' where p.desativado is null';
				if($this->login->permissao($this->modulo_arquivo,LISTAR) == PROPRIOS){
					$requisicao.= ' and p.criador_id=?';
					//Adiciona parâmetros da requisição
					$parametros[] = $this->login->id;
				}
			}
		}
		$requisicao.= ' order by p.nome';
		//Verifica se deve fazer paginação
		if(is_null($id) && $num_registros > 0){
			$requisicao.= ' limit '.(($pagina*$num_registros)-$num_registros).','.$num_registros;
		}
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(is_string($resultado)) return $resultado;
		if(!is_array($resultado)) return 'Não foi possível carregar a informação';
		if(count($resultado) == 0) return 'Nenhum perfil encontrado';
		
		//Retorna um ARRAY, agora de objetos e não mais de ARRAYs
		return $this->array_to_object($resultado,is_numeric($id));
	}
	
	public function numero_registros($ativos = true){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return 'Não foi identificada conexão com o banco de dados';
		//Verifica parâmetros
		if(!is_bool($ativos)) $ativos = true;
		
		//Monta requisição
		$requisicao = 'select count(*) as registros from perfil p';
		//Verifica as permissões
		if($this->login->permissao($this->modulo_arquivo,LISTAR) != EXCLUIDOS || $ativos == true){
			$requisicao.= ' where p.desativado is null';
			if($this->login->permissao($this->modulo_arquivo,LISTAR) == PROPRIOS){
				$requisicao.= ' and p.criador_id=?';
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
		//Valida dados
		if(gettype($registro) != 'object' || (gettype($registro) == 'object' && get_class($registro) != 'Perfil')) return array(MENSAGEM_ERRO=>'Dados inválidos. Tente novamente.');
		//Valida parâmetros
		if(gettype($login) != 'object' || (gettype($login) != 'object' && get_class($login) != 'LoginControle')) return array(MENSAGEM_ERRO=>'Login inválido');
		
		//Verifica se o usuário tem permissão de acesso
		if(is_numeric($registro->id) && $registro->id > 0){
			//Verifica permissão para editar
			if(!is_numeric($this->login->permissao($this->modulo_arquivo,EDITAR))) return array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo');
		}else{
			//Verifica permissão para cadastrar
			if(!is_numeric($this->login->permissao($this->modulo_arquivo,CADASTRAR))) return array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo');
		}
		
		//Insere novo perfil ou atualiza-o, caso tenha ID
		//Retorna ID existente ou criado
		$id = $this->salva_registro($registro, $login);
		if(is_string($id) && !is_numeric($id)) return array(MENSAGEM_ERRO=>$id);
		if(is_array($id)) return $id;
		//Verifica se conseguiu inserir / atualizar
		if(!is_numeric($id) || $id == 0) return array(MENSAGEM_ERRO=>'Não foi possível inserir/atualizar o perfil');
		//Atualiza ID do perfil
		$registro->id = $id;
		
		//Retorna ID do registro salvo
		return $registro->id;
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
		
		//Verifica se há usuários ativos com o perfil a ser excluído
		
		//Monta requisição
		$requisicao = 'select count(*) as ativos from usuario where perfil_id=? and desativado is null';
		
		//Monta parâmetros
		$parametros = array($id);
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(gettype($resultado) == 'string') return $resultado;
		//Como está buscando uma contagem, deve retornar apenas 1 registro
		if(!is_array($resultado) && count($resultado) != 1 && !is_array($resultado[0]) && count($resultado[0]) != 1) return 'Não foi possível validar os usuários ativos no perfil';
		//Verifica se há usuários ativos
		if($resultado[0]['ativos'] > 0) return 'Não é possível excluir o perfil, pois há usuários ativos utilizando-o';
		
		//Monta requisição
		$requisicao = 'update perfil set desativado=true, modificado=now(), modificador_id=? where id=?';
		
		//Monta parâmetros
		$parametros = array($this->login->id, $id);
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(gettype($resultado) == 'string') return $resultado;
		//Retorna o número de registros alterados, que deve ser apenas 1
		if($resultado != 1 || !is_numeric($resultado)) return 'Não foi possível excluir o registro';
		
		//Excluindo, retorna TRUE para informar êxito
		return true;
	}
	
	private function carregar_permissoes($id){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return 'Não foi identificada conexão com o banco de dados';
		//Valida parâmetro
		if(!is_numeric($id)) return 'Perfil não identificado';
		
		//Monta requisição
		$requisicao = 'select m.arquivo, p.acao, p.tipo from permissao p left join modulo m on m.id = p.modulo_id where p.desativado is null and p.perfil_id = ?';
		
		//Adiciona parâmetros da requisição
		$parametros = array($id);
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(gettype($resultado) == 'string') return $resultado;
		if(!is_array($resultado)) return 'Não foi possível carregar a informação';
		
		//Organiza as permissões para um array $permissao[módulo][ação]=tipo;
		$resultado = $this->perfilControle->organizar_permissoes($resultado);
		
		//Retorna os dados obtidos
		return $resultado;
	}
	
	private function array_to_object($array = null, $completo = false){
		//Verifica existência da classe necessária
		if(!class_exists('Perfil')) return 'Não foi identificada a classe necessária';
		
		//Valida parâmetro
		if(!is_array($array)) return false;
		
		//Inicia o array de objeto
		$object = array();
		
		//Converte arrays em objects
		foreach($array as $registro){
			//Se está carregando o perfil completo, seleciona permissões
			if($completo) $permissoes = $this->carregar_permissoes($registro['id']);
			//Cria objeto e adiciona à lista
			$object[] = new Perfil($registro['id'], $registro['nome'], (isset($permissoes)?$permissoes:null), $registro['criado'], $registro['criador_id'], $registro['modificado'], $registro['modificador_id'], $registro['desativado']);
		}
		
		return $object;
	}
	
	private function salva_registro($registro, $login){
		//Valida parâmetros
		if(gettype($registro) != 'object' || (gettype($registro) == 'object' && get_class($registro) != 'Perfil')) return array(MENSAGEM_ERRO=>'Dados inválidos. Tente novamente.');
		if(gettype($login) != 'object' || (gettype($login) != 'object' && get_class($login) != 'LoginControle')) return array(MENSAGEM_ERRO=>'Login inválido');
		
		//Tendo ID atualiza dados
		if(is_numeric($registro->id) && $registro->id > 0){
			//Verifica permissão para editar
			if(!is_numeric($login->permissao($this->modulo_arquivo,EDITAR))) return array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo');
			
			//Monta requisição
			$requisicao = 'update perfil set nome=?, modificado=now(), modificador_id=? where id=?';
			
			//Monta parâmetros
			$parametros = array($registro->nome, $login->id, $registro->id);
			
			//Adiciona validação de posse se não tiver permissão para editar
			if($login->permissao($this->modulo_arquivo,EDITAR) < TODOS){
				$requisicao.= ' and criador_id = ?';
				//Adiciona parâmetro
				$parametros[] = $login->id;
			}
		}else{
			//Verifica permissão para cadastrar
			if(!is_numeric($this->login->permissao($this->modulo_arquivo,CADASTRAR))) return array(MENSAGEM_ERRO=>'Você não tem permissão para acessar o conteúdo');
			
			//Monta requisição
			$requisicao = 'insert into perfil (nome, criado, criador_id) values(?, now(), ?)';
			//Monta parâmetros
			$parametros = array($registro->nome, $login->id);
		}
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(is_string($resultado) && !is_numeric($resultado)){
			if($login->permissao($this->modulo_arquivo,EDITAR) < TODOS) return array(MENSAGEM_ERRO=>'Você não pode editar o registro, pois não é o criador');
			else return $resultado;
		}
		//Se está atualizando, retorna o número de registros alterados, que deve ser apenas 1
		//Se está inserindo, retorna o ID do novo registro
		if((is_numeric($registro->id) && $registro->id > 0 && $resultado != 1) || !is_numeric($resultado)) return array(MENSAGEM_ERRO=>'Não foi possível salvar o registro');
		
		//Guarda ID para inserir as permissões
		if(!is_numeric($registro->id) || $registro->id == 0) $registro->id = $resultado;
		
		//Desativa todas as permissões do perfil para após inserir ou atualizar as que foram atribuídas
		$retorno = $this->desativar_permissoes($registro, $login);
		if(is_string($resultado) && !is_numeric($resultado) && strlen($retorno) > 0) return array(MENSAGEM_ERRO=>$retorno);
		
		//Insere / atualiza as permissões
		$retorno = $this->inserir_permissoes($registro, $login);
		if(is_string($resultado) && !is_numeric($resultado) && strlen($retorno) > 0) return array(MENSAGEM_ERRO=>$retorno);
		
		//Retorna o ID do registro
		return $registro->id;
	}
	
	private function desativar_permissoes($registro, $login){
		//Valida parâmetros
		if(gettype($registro) != 'object' || (gettype($registro) == 'object' && get_class($registro) != 'Perfil')) return 'Dados inválidos. Tente novamente.';
		if(gettype($login) != 'object' || (gettype($login) != 'object' && get_class($login) != 'LoginControle')) return 'Login inválido';
		if(!is_numeric($registro->id) || $registro->id == 0) return 'Perfil não encontrado';
		
		//Monta requisição
		$requisicao = 'update permissao set modificado=now(), modificador_id=?, desativado=? where perfil_id=?';
		
		//Monta parâmetros
		$parametros = array($login->id, true, $registro->id);
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(is_string($resultado)) return $resultado;
		
		return true;
	}
	
	private function inserir_permissoes($registro, $login){
		//Valida parâmetros
		if(gettype($registro) != 'object' || (gettype($registro) == 'object' && get_class($registro) != 'Perfil')) return 'Dados inválidos. Tente novamente.';
		if(gettype($login) != 'object' || (gettype($login) != 'object' && get_class($login) != 'LoginControle')) return 'Login inválido';
		if(!is_numeric($registro->id) || $registro->id == 0) return 'Perfil não encontrado';
		
		//Verifica se há permissões à serem inseridas
		if(!is_array($registro->permissao_array)) return false;
		
		//Inicia mensagem de retorno
		$retorno = '';
		
		//Percorre lista de módulos
		foreach($registro->permissao_array as $modulo=>$permissao){
			//Percorre lista de permissões
			foreach($permissao as $acao=>$tipo){
				//Monta requisição
				$requisicao = 'update permissao set tipo=?, modificado=now(), modificador_id=?, desativado = ? where perfil_id=? and modulo_id=? and acao=?';
				
				//Monta parâmetros
				$parametros = array($tipo, $login->id, NULL, $registro->id, $modulo, $acao);
				
				//Adiciona validação de posse se não tiver permissão para editar
				if($login->permissao($this->modulo_arquivo,EDITAR) < TODOS){
					$requisicao.= ' and criador_id = ?';
					//Adiciona parâmetro
					$parametros[] = $login->id;
				}
				
				//Executa a requisição
				$resultado = $this->banco->query($requisicao, $parametros);
				
				//Verifica o resultado obtido
				if(is_string($resultado))	$retorno.= (strlen($retorno)>0?'<br>':'').$resultado;
				if(is_numeric($resultado) && $resultado == 0){
					//Se não existe e o usuário tem permissão para cadastrar, cadastra a permissão
					if(is_numeric($login->permissao($this->modulo_arquivo,CADASTRAR))){
						//Monta requisição
						$requisicao = 'insert permissao (perfil_id, modulo_id, acao, tipo, criado, criador_id) values (?, ?, ?, ?, now(), ?)';
						
						//Monta parâmetros
						$parametros = array($registro->id, $modulo, $acao, $tipo, $login->id);
						
						//Executa a requisição
						$resultado = $this->banco->query($requisicao, $parametros);
						
						//Verifica o resultado obtido
						//Não valida retorno, pois não tem um ID e sim uma chave composta, retornando sempre ZERO (0) ao inserir
						if(is_string($resultado) && !is_numeric($resultado)) $retorno.= (strlen($retorno)>0?'<br>':'').$resultado;
					}else{
						//Informa a falta de permissão do usuário para novas permissões
						$retorno.= (strlen($retorno)>0?'<br>':'').'Você não tem permissão para cadastrar permissões';
					}
				}
			}
		}
		return $retorno;
	}
}
?>