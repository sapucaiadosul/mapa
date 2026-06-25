<?php

/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class MetaDAO {

    private $login = null;
    private $banco = null;
    private $modulo_arquivo = 'meta';
    private $funcao = null; //Funcao

    function __construct($login, $banco) {
        $this->funcao = new Funcao();
        //Carrega classes necessárias
        if (!($this->funcao->carrega_arquivo('modelo', 'acao_class')))
            return false;
        if (!($this->funcao->carrega_arquivo('modelo', 'acompanhamento_class')))
            return false;
        if (!($this->funcao->carrega_arquivo('modelo', 'anexo_class')))
            return false;
        if (!($this->funcao->carrega_arquivo('modelo', 'meta_class')))
            return false;

        //Valida objetos de configuração
        if (get_class($banco) != 'Banco')
            return false;
        if (get_class($login) != 'LoginControle')
            return false;

        //Guarda objetos para uso futuro
        $this->login = $login;
        $this->banco = $banco;
    }

    public function excluir($id) {
        //Valida login
        if (!is_object($this->login) || get_class($this->login) != 'LoginControle')
            return 'Login inválido';

        //Valida parâmetros
        if (!is_numeric($id) || $id <= 0)
            return 'ID inválido';

        //Verifica o responsável pelo registro
        $responsavelId = $this->validarResponsavelId($id);
        if (!is_numeric($responsavelId))
            return 'Não foi possível identificar o responsável pelo registro';

        //Verifica o criador do registro
        if ($responsavelId == $this->login->id) {
            //Verifica permissão para excluir
            if (!is_numeric($this->login->permissao($this->modulo_arquivo, EXCLUIR)))
                return 'Você não tem permissão para excluir o conteúdo';
        } else {
            //Verifica permissão para excluir qualquer arquivo
            if ($this->login->permissao($this->modulo_arquivo, EXCLUIR) != TODOS)
                return 'Você não tem permissão para excluir o conteúdo';
        }

        //Monta requisição
        $requisicao = 'update meta set desativado=true, modificado=now(), modificador_id=? where id=? and desativado is null';

        //Monta parâmetros
        $parametros = array($this->login->id, $id);

        //Executa a requisição
        $resultado = $this->banco->query($requisicao, $parametros);

        //Verifica o resultado obtido
        if (gettype($resultado) == 'string')
            return $resultado;
        //Retorna o número de registros alterados, que deve ser apenas 1
        if ($resultado != 1 || !is_numeric($resultado))
            return 'Não foi possível excluir o registro';

        //Excluindo, retorna TRUE para informar êxito
        return true;
    }

    public function excluirAcao($id) {
        //Valida login
        if (!is_object($this->login) || get_class($this->login) != 'LoginControle')
            return 'Login inválido';

        //Valida parâmetros
        if (!is_numeric($id) || $id <= 0 || strlen($id) > 9)
            return 'ID de ação inválido';

        //Verifica se o usuário tem permissão para excluir todas as metas
        if ($this->login->permissao($this->modulo_arquivo, EXCLUIR) == TODOS) {
            //Monta requisição
            $requisicao = 'update acao set desativado=true, modificado=now(), modificador_id=? where id=? and desativado is null';

            //Monta parâmetros
            $parametros = array($this->login->id, $id);
        } elseif ($this->login->permissao($this->modulo_arquivo, EXCLUIR) == PROPRIOS) {
            //Monta requisição
            $requisicao = 'update acao a left join meta m on m.id = a.meta_id set a.desativado=true, a.modificado=now(), a.modificador_id=? where a.id=? and a.desativado is null and m.responsavel_id=?';

            //Monta parâmetros
            $parametros = array($this->login->id, $id, $this->login->perfil_id);
        } else
            return 'Você não tem permissão para excluir ações';

        //Executa a requisição
        $resultado = $this->banco->query($requisicao, $parametros);

        //Verifica o resultado obtido
        if (gettype($resultado) == 'string')
            return $resultado;
        //Retorna o número de registros alterados, que deve ser apenas 1
        if ($resultado != 1 || !is_numeric($resultado))
            return 'Não foi possível excluir o registro';

        //Excluindo, retorna TRUE para informar êxito
        return true;
    }

    public function excluirAnexo($id) {
        //Valida login
        if (!is_object($this->login) || get_class($this->login) != 'LoginControle')
            return 'Login inválido';

        //Valida parâmetros
        if (!is_numeric($id) || $id <= 0 || strlen($id) > 9)
            return 'ID de anexo inválido';

        //Verifica se o usuário tem permissão para excluir todas as metas
        if ($this->login->permissao($this->modulo_arquivo, EXCLUIR) == TODOS) {
            //Monta requisição
            $requisicao = 'update anexo set desativado=true, modificado=now(), modificador_id=? where id=? and desativado is null';

            //Monta parâmetros
            $parametros = array($this->login->id, $id);
        } elseif ($this->login->permissao($this->modulo_arquivo, EXCLUIR) == PROPRIOS) {
            //Monta requisição
            $requisicao = 'update anexo a left join meta m on m.id = a.meta_id set a.desativado=true, a.modificado=now(), a.modificador_id=? where a.id=? and a.desativado is null and m.responsavel_id=?';

            //Monta parâmetros
            $parametros = array($this->login->id, $id, $this->login->perfil_id);
        } else
            return 'Você não tem permissão para excluir anexos';

        //Executa a requisição
        $resultado = $this->banco->query($requisicao, $parametros);

        //Verifica o resultado obtido
        if (gettype($resultado) == 'string')
            return $resultado;
        //Retorna o número de registros alterados, que deve ser apenas 1
        if ($resultado != 1 || !is_numeric($resultado))
            return 'Não foi possível excluir o registro';

        //Excluindo, retorna TRUE para informar êxito
        return true;
    }

    public function salvar($registro) {
        //Valida parâmetros
        if (!is_object($registro) || get_class($registro) != 'Meta')
            return array(MENSAGEM_ERRO => 'Dados inválidos. Tente novamente.');
        if (!is_object($this->login) || get_class($this->login) != 'LoginControle')
            return array(MENSAGEM_ERRO => 'Login inválido');

        //Verifica se o usuário tem permissão de acesso
        if (is_numeric($registro->id) && $registro->id > 0) {
            //Valida permissão de edição
            if ($this->login->permissao($this->modulo_arquivo, EDITAR) < TODOS) {
                if ($this->login->permissao($this->modulo_arquivo, EDITAR) != PROPRIOS || ($this->login->permissao($this->modulo_arquivo, EDITAR) == PROPRIOS && $this->validarResponsavelId($registro->id, $this->login->perfil_id) === false))
                    return array('', array(MENSAGEM_ERRO => 'Você não tem permissão para acessar o conteúdo'));
            }
            //Atualiza o registro
            $retorno = $this->atualizarMeta($registro);
        } else {
            //Valida permissão de inclusão
            if (!is_numeric($this->login->permissao($this->modulo_arquivo, CADASTRAR)))
                return array('', array(MENSAGEM_ERRO => 'Você não tem permissão para cadastrar'));
            //Inserir o registro
            $retorno = $this->inserirMeta($registro);

            //Valida retorno
            if (is_numeric($retorno) && $retorno > 0 && strlen($retorno) <= 5) {
                $registro->id = $retorno;
                $atualizarMetaId = $retorno;
                $retorno = true;
            }
        }

        //Valida retorno
        if (is_array($retorno))
            return $retorno;
        if ($retorno !== true)
            return array(MENSAGEM_ERRO => 'Não foi possível salvar o registro');

        //Salva ação
        $acaoNaoSalva = false;
        if (!is_null($registro->acao) && is_array($registro->acao) && count($registro->acao) > 0) {
            foreach ($registro->acao as $acao) {
                //Atualiza ID da Meta caso estaja inserindo
                if (isset($atualizarMetaId))
                    $acao->metaId = $atualizarMetaId;
                //Executa o salvamento
                $retorno = $this->salvarAcao($acao, $registro->responsavelId);
                //Valida retorno
                if (!is_numeric($retorno)) {
                    if (!$acaoNaoSalva) {
                        $erro = 'a(s) acão(ões)';
                        $acaoNaoSalva = true;
                    }
                }
            }
        }

        //Salva acompanhamento
        if (!is_null($registro->acompanhamento) && is_array($registro->acompanhamento) && count($registro->acompanhamento) == 1) {
            //Separa objeto para não causar notificação ao atualizar metaId
            $acompanhamento = $registro->acompanhamento[0];
            //Atualiza ID da Meta caso estaja inserindo
            if (isset($atualizarMetaId))
                $acompanhamento->metaId = $atualizarMetaId;
            //Executa salvamento
            $retorno = $this->salvarAcompanhamento($acompanhamento);
            //Valida retorno
            if (!is_numeric($retorno)) {
                //Adiciona texto extra se já houver ocorrido erros
                if (isset($erro))
                    $erro .= ' nem o';
                else
                    $erro = 'o';
                $erro .= ' acompanhamento';
                //Remove acompanhamento para não carregar na tela
                $registro->acompanhamento = null;
            } else {
                //Tenta enviar e-mail aos usuários do perfil responsável, quando o GP adiciona acompanhamento
                //Não valida o envio, pois não é obrigatório
                $this->enviarEmailAcompanhamento($registro->responsavelId);
            }
        }

        //Salva anexos
        if (!is_null($registro->anexo) && is_array($registro->anexo) && count($registro->anexo) > 0) {
            foreach ($registro->anexo as $anexo) {
                //Atualiza ID da Meta caso estaja inserindo
                if (isset($atualizarMetaId))
                    $anexo->metaId = $atualizarMetaId;
                //Executa o salvamento
                $retorno = $this->salvarAnexo($anexo);
                //Valida retorno
                if (!is_numeric($retorno)) {
                    //Adiciona texto extra se já houver ocorrido erros
                    if (isset($erro))
                        $erro .= ' nem o(s)';
                    else
                        $erro = 'o(s)';
                    $erro .= ' anexo(s)';
                    //Remove anexos para não carregar na tela
                    $registro->anexo = null;
                    //Ao ocorrer um erro, para de salvar os anexos
                    break;
                }
            }
        }

        //Verifica se houve erro em algum dos adicionais ao registro
        if (isset($erro))
            return array(MENSAGEM_ERRO => 'O registro foi salvo, mas não foi possível salvar ' . $erro . '.');
        //Retorna ID do registro salvo
        else
            return $registro->id;
    }

    public function selecionarAnexo($id) {
        //Valida parâmetro
        if (!is_numeric($id) || $id <= 0 || strlen($id) > 9)
            return 'ID de anexo inválida';

        //Chama função privada informando o ID que será selecionado
        return $this->selecionarAnexos(null, $id);
    }

    public function selecionarMeta($id = null, $ativos = true, $pagina = 1, $numeroRegistros = 0) {
        //Verifica se tem acesso ao banco
        if (is_null($this->banco))
            return 'Não foi identificada conexão com o banco de dados';
        //Valida parâmetros
        if (!is_numeric($id))
            $id = null;
        if (!is_bool($ativos) && !is_null($ativos))
            $ativos = true;
        if (!is_numeric($pagina))
            $pagina = 1;
        if (!is_numeric($numeroRegistros) || $numeroRegistros == 0)
            $numeroRegistros = Config::$db_limite;

        //Verifica se o usuário tem permissão de acesso
        if ((!is_numeric($this->login->permissao($this->modulo_arquivo, LISTAR)) && is_null($id)) && (!is_numeric($this->login->permissao($this->modulo_arquivo, EDITAR)) && !is_null($id)))
            return 'Você não tem permissão para acessar o conteúdo';

        //Inicia lista de parâmetros
        $parametro = array();

        //Inicia cláusula WHERE, para validar campos de pesquisa
        $requisicaoWhere = '';

        //Monta requisição
        $requisicao = 'select distinct m.id, m.numero, m.titulo, m.responsavel_id, p.nome as responsavel_nome, m.responsavel_nome as responsavel_pessoa, m.data_inicial, m.data_final, m.data_conclusao, m.ind_objetivo, m.ind_ods, m.ind_titulo, m.ind_referencia, m.ind_indicador, m.ind_unidade, m.ind_sec_valor, m.ind_sec_datahora, m.ind_mon_valor, m.ind_mon_datahora, m.manter_monitoria, m.criado, m.criador_id, m.modificado, m.modificador_id, m.desativado';

        //Adicionar campos de contagem se estiver na lista de registros
        if (!is_numeric($id))
            $requisicao .= ',
			(select count(a.id) from anexo a where a.meta_id = m.id and a.desativado is null) as anexos,
			(select count(a.id) from acompanhamento a where a.meta_id = m.id and a.tipo = "0" and a.desativado is null) as tipo_0,
			(select count(a.id) from acompanhamento a where a.meta_id = m.id and a.tipo = "1" and a.desativado is null) as tipo_1,
			(select count(a.id) from acompanhamento a where a.meta_id = m.id and a.tipo = "2" and a.desativado is null) as tipo_2,
			(select count(a.id) from acompanhamento a where a.meta_id = m.id and a.tipo = "3" and a.desativado is null) as tipo_3,
			(select a.texto from acompanhamento a where a.meta_id = m.id and a.desativado is null and a.tipo = "0" order by a.criado desc limit 0,1) as observacao,
			(select a.texto from acompanhamento a where a.meta_id = m.id and a.desativado is null and a.tipo = "1" order by a.criado desc limit 0,1) as informacao,
			(select a.texto from acompanhamento a where a.meta_id = m.id and a.desativado is null and a.tipo = "2" order by a.criado desc limit 0,1) as problema,
			(select a.texto from acompanhamento a where a.meta_id = m.id and a.desativado is null and a.tipo = "3" order by a.criado desc limit 0,1) as monitoramento
		';

        $requisicao .= ' from meta m left join perfil p on m.responsavel_id = p.id ';

        //Verifica as permissões
        if ($this->login->permissao($this->modulo_arquivo, LISTAR) != EXCLUIDOS || $ativos == true) {
            $requisicaoWhere .= ' where m.desativado is null';
            if ($this->login->permissao($this->modulo_arquivo, LISTAR) < TODOS) {
                $requisicaoWhere .= ' and u.id=?';
                //Adiciona parâmetros da requisição
                $parametro[] = $this->login->id;
                //Adiciona validação para o usuário
                $requisicao .= ' left join usuario u on m.responsavel_id = u.perfil_id';
            }
        }

        //Verifica se está carregando um registro ou a lista
        if (is_numeric($id)) {
            if (strlen($requisicaoWhere) == 0)
                $requisicaoWhere .= ' where';
            else
                $requisicaoWhere .= ' and';
            $requisicaoWhere .= ' m.id=?';
            //Adiciona parâmetros da requisição
            $parametro[] = $id;
        } else {
            //Carrga filtro de pesquisa, conforme campos do formulário de pesquisa
            $filtroPesquisa = $this->carregarFiltroPesquisa($requisicao);
            if (is_array($filtroPesquisa) && count($filtroPesquisa) == 2) {
                if (is_string($filtroPesquisa[0]) && strlen($filtroPesquisa[0]) > 0 && is_array($filtroPesquisa[1])) {
                    if (strlen($requisicaoWhere) == 0)
                        $requisicaoWhere .= substr($filtroPesquisa[0], 4);
                    else
                        $requisicaoWhere .= $filtroPesquisa[0];
                    $parametro = array_merge($parametro, $filtroPesquisa[1]);
                }
            }
        }

        //Adiciona cláusula WHERE a requisição
        $requisicao .= $requisicaoWhere;

        //Informa por qual campo será ordenado
        //$requisicao.= ' order by if(m.modificado is null, m.criado, m.modificado) desc, p.nome asc, m.titulo asc';
        $requisicao .= ' order by  numero, m.titulo asc, p.nome asc ';

        //Verifica se deve fazer paginação
        if (is_null($id) && $numeroRegistros > 0) {
            $requisicao .= ' limit ' . (($pagina * $numeroRegistros) - $numeroRegistros) . ',' . $numeroRegistros;
        }

        //Executa a requisição
        $resultado = $this->banco->query($requisicao, $parametro);

        //Verifica o resultado obtido
        if (is_string($resultado))
            return $resultado;
        if (!is_array($resultado))
            return 'Não foi possível carregar a informação';
        if (count($resultado) == 0)
            return 0;

        //Retorna um ARRAY, agora de objetos e não mais de ARRAYs
        return $this->converterArrayMeta($resultado, is_numeric($id));
    }

    public function selecionarNumeroRegistros($ativos = true) {
        //Verifica se tem acesso ao banco
        if (is_null($this->banco))
            return 'Não foi identificada conexão com o banco de dados';
        //Verifica parâmetros
        if (!is_bool($ativos))
            $ativos = true;

        //Inicia variáveis da requisição
        $requisicaoWhere = '';
        $parametro = array();

        //Monta requisição
        $requisicao = 'select count(*) as registros from meta m';

        //Verifica as permissões
        if ($this->login->permissao($this->modulo_arquivo, LISTAR) != EXCLUIDOS || $ativos == true) {
            $requisicaoWhere .= ' where m.desativado is null';
            if ($this->login->permissao($this->modulo_arquivo, LISTAR) == PROPRIOS) {
                $requisicaoWhere .= ' and m.criador_id=?';
                //Adiciona parâmetros da requisição
                $parametro[] = $this->login->id;
            }
        }

        //Carrga filtro de pesquisa, conforme campos do formulário de pesquisa
        $filtroPesquisa = $this->carregarFiltroPesquisa($requisicao);
        if (is_array($filtroPesquisa) && count($filtroPesquisa) == 2) {
            if (is_string($filtroPesquisa[0]) && strlen($filtroPesquisa[0]) > 0 && is_array($filtroPesquisa[1])) {
                if (strlen($requisicaoWhere) == 0)
                    $requisicaoWhere .= substr($filtroPesquisa[0], 4);
                else
                    $requisicaoWhere .= $filtroPesquisa[0];
                $parametro = array_merge($parametro, $filtroPesquisa[1]);
            }
        }

        //Adiciona cláusula WHERE a requisição
        $requisicao .= $requisicaoWhere;

        //Executa a requisição
        $resultado = $this->banco->query($requisicao, $parametro);

        //Verifica o resultado obtido
        if (is_string($resultado))
            return $resultado;
        if (!is_array($resultado))
            return 'Não foi possível carregar a informação';
        if (count($resultado) != 1)
            return 'Não foi possível carregar a informação';

        //Retorna a quantidade de registros
        return $resultado[0]['registros'];
    }

    public function validarResponsavelId($registroId, $perfilId = null) {
        //Verifica se tem acesso ao banco
        if (is_null($this->banco))
            return false;

        //Valida parâmetros
        if (!is_numeric($registroId))
            return false;
        if (!is_numeric($perfilId))
            $perfilId = null;

        //Seleciona o registro
        $registros = $this->selecionarMeta($registroId, false); //ATIVOS = FALSE, para mostrar o responsável de qualquer registro
        //Verifica se encontrou algum registro
        if (is_numeric($registros) && $registros == 0)
            return false;
        if (is_string($registros))
            return array(MENSAGEM_ERRO => $registros);
        if (!is_array($registros) || count($registros) != 1)
            return array(MENSAGEM_ERRO => 'Não foi possível carregar o cadastro');

        //Separa o registro
        $registro = $registros[0];

        //Verifica o retorno esperado
        if (is_null($perfilId)) {
            //Retorno a ID do criador
            return $registro->responsavelId;
        } else {
            //Verifica se o usuário pertence ao perfil criador do registro
            if ($registro->responsavelId == $perfilId)
                return $registro;
            else
                return false;
        }
    }

    private function armazenarAnexo($registro) {
        //Valida parâmetros
        if (!is_object($registro) || get_class($registro) != 'Anexo')
            return 'Dados inválidos. Tente novamente.';
        if ($registro->id == 0)
            return 'ID inválido';
        if ($registro->metaId == 0)
            return 'Meta inválida';
        if (strlen($registro->nome) == 0)
            return 'Nome não informado';
        if (strlen($registro->extensao) == 0)
            return 'Extensão do arquivo não encontrada';
        if (strlen($registro->nomeTemporario) == 0)
            return 'Arquivo não encontrado';

        //Valida existência do arquivo temporário
        if (!file_exists($registro->nomeTemporario))
            return 'Arquivo não encontrado';

        //Monta nome da pasta
        $pasta = "./anexo/" . floor($registro->id / 1000);
        //Monta nome do arquivo
        $arquivo = $pasta . "/" . $registro->id . "." . $registro->extensao;

        //Verifica a existência da pasta, criando caso não exista
        if (!is_dir($pasta))
            if (!mkdir($pasta))
                return 'Não foi possível criar a pasta de anexos';

        //Verifica existência de arquivo com mesmo nome
        if (file_exists($arquivo))
            return 'Arquivo já existe';

        //Move arquivo
        return move_uploaded_file($registro->nomeTemporario, $arquivo);
    }

    private function atualizarAcao($registro, $responsavelId) {
        //Valida parâmetros
        if (!is_object($registro) || get_class($registro) != 'Acao')
            return array(MENSAGEM_ERRO => 'Dados inválidos. Tente novamente.');
        if (!is_object($this->login) || get_class($this->login) != 'LoginControle')
            return array(MENSAGEM_ERRO => 'Login inválido');
        if (!is_numeric($responsavelId) || $responsavelId == 0)
            return 'ID de responsável inválida';

        //Verifica se o usuário é o responsável da meta (GP pode realizar alterações acima, mas pode fazer alterações em suas próprias metas)
        if ($this->validarResponsavelId($registro->metaId) == $this->login->perfil_id) {
            //Verifica se é o responsável pela meta e já está concluindo a ação criada
            if ($registro->id > 0 && strlen($registro->concluida) > 0) {
                //Monta requisição
                //Só conclui meta que ainda não foi concluída ou que foi reprovada
                $requisicao = 'update acao set
					concluida = now(), concluida_id = ?, monitorada = null, monitor_id = null, aprovada = null, modificado = now(), modificador_id = ?
					where id = ? and (concluida is null or (concluida is not null and monitorada is not null and aprovada = 0))';
                //Monta parâmetros
                $parametros = array($this->login->id, $this->login->id, $registro->id);

                //Executa a requisição
                $resultadoConclusao = $this->banco->query($requisicao, $parametros);

                //Verifica o resultado obtido
                if (is_string($resultadoConclusao) && !is_numeric($resultadoConclusao))
                    return array(MENSAGEM_ERRO => $resultadoConclusao);
            }
        }

        //Verifica se o usuário tem permissão de edição total para atualizar o nome
        if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS) {
            //Monta requisição
            $requisicao = 'update acao set nome = ?, prazo = ?, modificado = now(), modificador_id = ? where id = ? and (aprovada = 0 or aprovada is null)';
            $parametros = array($registro->nome, $registro->prazo, $this->login->id, $registro->id);

            //Executa a requisição
            $resultadoEdicao = $this->banco->query($requisicao, $parametros);

            //Verifica o resultado obtido
            if (is_string($resultadoEdicao) && !is_numeric($resultadoEdicao))
                return array(MENSAGEM_ERRO => $resultadoEdicao);
        }

        //Verifica se o usuário tem permissão de edição total para atualizar aprovar ação
        if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS && strlen($registro->criado) > 0) {
            //Monta requisição
            $requisicao = 'update acao set monitorada = now(), monitor_id = ?, aprovada = ?, modificado = now(), modificador_id = ?
				where id = ? and (aprovada = 0 or aprovada is null) and concluida is not null';
            $parametros = array($this->login->id, $registro->aprovada, $this->login->id, $registro->id);

            //Executa a requisição
            $resultadoAprovacao = $this->banco->query($requisicao, $parametros);

            //Verifica o resultado obtido
            if (is_string($resultadoAprovacao) && !is_numeric($resultadoAprovacao))
                return array(MENSAGEM_ERRO => $resultadoAprovacao);
        }

        //Verifica se conseguiu atualizar
        return (isset($resultadoConclusao) ? $resultadoConclusao : 1) + (isset($resultadoEdicao) ? $resultadoEdicao : 1) + (isset($resultadoAprovacao) ? $resultadoAprovacao : 1);
    }

    private function atualizarMeta($registro) {
        //Valida parâmetros
        if (!is_object($registro) || get_class($registro) != 'Meta')
            return array(MENSAGEM_ERRO => 'Dados inválidos. Tente novamente.');
        if (!is_numeric($registro->id) || $registro->id == 0 || strlen($registro->id) > 5)
            return array(MENSAGEM_ERRO => 'ID inválido de registro');
        if (!is_object($this->login) || get_class($this->login) != 'LoginControle')
            return array(MENSAGEM_ERRO => 'Login inválido');

        //Inicia requisição
        $requisicao = 'update meta set ';

        //inicia parâmetros
        $parametros = array();

        //Só quem tiver permissão de edição total pode editar os dados básicos da meta
        if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS) {//, ind_mon_valor, ind_mon_datahora
            $requisicao .= ' numero=?, titulo=?, responsavel_id=?, responsavel_nome=?, data_inicial=?, data_final=?, ind_objetivo=?, ind_ods=?, ind_titulo=?, ind_referencia=?, ind_indicador=?, ind_unidade=?, manter_monitoria=?, ';
            $parametros[] = $registro->numero;
            $parametros[] = $registro->titulo;
            $parametros[] = $registro->responsavelId;
            $parametros[] = $registro->responsavelPessoa;
            $parametros[] = $registro->dataInicial;
            $parametros[] = $registro->dataFinal;
            $parametros[] = $registro->indObjetivo;
            $parametros[] = $registro->indOds;
            $parametros[] = $registro->indTitulo;
            $parametros[] = $registro->indReferencia;
            $parametros[] = $registro->indIndicador;
            $parametros[] = $registro->indUnidade;
            $parametros[] = is_bool($registro->manterMonitoria) ? $registro->manterMonitoria : null;

            //Verifica se alterou o valor alcançado
            if ($registro->indMonValor != $registro->indValorAnterior) {
                $requisicao .= ' ind_mon_valor=?, ind_mon_datahora=now(), ';
                $parametros[] = $registro->indMonValor;
            }
        } elseif ($registro->indSecValor != $registro->indValorAnterior) {//Verifica se alterou o valor alcançado
            $requisicao .= ' ind_sec_valor=?, ind_sec_datahora=now(), ';
            $parametros[] = $registro->indSecValor;
        }

        //A data de conclusão pode ser editada por todos que tem permissão de edição
        $requisicao .= ' data_conclusao=?, modificado=now(), modificador_id=? where id=? and desativado is null';
        $parametros[] = (strlen($registro->dataConclusao) == 0 ? null : $registro->dataConclusao);
        $parametros[] = $this->login->id;
        $parametros[] = $registro->id;

        //Adiciona validação de posse se não tiver permissão para editar
        if ($this->login->permissao($this->modulo_arquivo, EDITAR) < TODOS) {
            $requisicao .= ' and responsavel_id = ?';
            //Adiciona parâmetro
            $parametros[] = $this->login->perfil_id;
        }

        //Executa a requisição
        $resultado = $this->banco->query($requisicao, $parametros);

        //Verifica o resultado obtido
        if (is_string($resultado) && !is_numeric($resultado)) {
            if ($this->login->permissao($this->modulo_arquivo, EDITAR) < TODOS)
                return array(MENSAGEM_ERRO => 'Você não pode editar o registro, pois não faz parte do grupo responsável');
            else
                return array(MENSAGEM_ERRO => $resultado);
        }

        //Verifica se retornou 1, que é o número de registro alterados
        if ($resultado !== 1)
            return array(MENSAGEM_ERRO => 'Não foi possível salvar o registro');

        //Informa que atualizou o registro
        return true;
    }

    private function carregarFiltroPesquisa(&$requisicao) {
        //Carrega campos do formulário de pesquisa
        $numero = (isset($_SESSION['numero']) ? (is_numeric($_SESSION['numero']) ? $_SESSION['numero'] : '') : '');
        $ods = (isset($_SESSION['ods']) ? (is_numeric($_SESSION['ods']) ? $_SESSION['ods'] : '') : '');
        $pesquisa = (isset($_SESSION['pesquisa']) ? (strlen($_SESSION['pesquisa']) > 0 && strlen($_SESSION['pesquisa']) <= 200 ? '%' . str_replace(' ', '%', addslashes($_SESSION['pesquisa'])) . '%' : '') : '');
        $campo = (isset($_SESSION['campo']) ? (is_numeric($_SESSION['campo']) && $_SESSION['campo'] >= 0 && $_SESSION['campo'] <= 2 ? $_SESSION['campo'] : 0) : 0);
        $responsavel = (isset($_SESSION['responsavel']) ? (is_numeric($_SESSION['responsavel']) && $_SESSION['responsavel'] > 0 && strlen($_SESSION['responsavel']) <= 3 ? $_SESSION['responsavel'] : '') : '');
        $responsavelPessoa = (isset($_SESSION['responsavelPessoa']) ? (strlen($_SESSION['responsavelPessoa']) > 0 && strlen($_SESSION['responsavelPessoa']) <= 40 ? '%' . str_replace(' ', '%', addslashes($_SESSION['responsavelPessoa'])) . '%' : '') : '');
        $situacao = (isset($_SESSION['situacao']) ? (is_numeric($_SESSION['situacao']) && $_SESSION['situacao'] > 0 && $_SESSION['situacao'] <= 5 ? $_SESSION['situacao'] : '') : '');
        $mapa = (isset($_SESSION['mapa']) ? (is_numeric($_SESSION['mapa']) ? $_SESSION['mapa'] : '') : '');
        $metaContinuada = isset($_SESSION['metas_continuadas']) && is_bool($_SESSION['metas_continuadas']) ? $_SESSION['metas_continuadas'] : true;

        $requisicaoPesquisa = '';
        $parametro = array();

        if (is_numeric($numero)) {
            $requisicaoPesquisa .= ' and m.numero = ?';
            $parametro[] = $numero;
        }

        if (is_numeric($ods)) {
            $requisicaoPesquisa .= ' and m.ind_ods = ?';
            $parametro[] = $ods;
        }

        if (strlen($pesquisa) > 0) {
            //Lista de opções para campo de pesquisa
            $campos = array(' and m.titulo like ?', ' and a.texto like ?', ' and (m.titulo like ? or a.texto like ?)');
            //Seleciona onde deve pesquisar
            $requisicaoPesquisa .= $campos[$campo];
            $parametro[] = $pesquisa;
            //Se deve pesquisar em dois campos, é preciso carregar o valor duas vezes
            if ($campo == 2)
                $parametro[] = $pesquisa;
            //Se estiver pesquisando nos comentários, adiciona tabela para utilizar em relacionamentos de pesquisa
            if ($campo == 1 || $campo == 2)
                $requisicao .= ' left join acompanhamento a on m.id = a.meta_id';
        }
        if (is_numeric($responsavel)) {
            $requisicaoPesquisa .= ' and m.responsavel_id = ?';
            $parametro[] = $responsavel;
        }
        if (strlen($responsavelPessoa) > 0) {
            $requisicaoPesquisa .= ' and m.responsavel_nome like ?';
            $parametro[] = $responsavelPessoa;
        }
        if (is_numeric($mapa) && $mapa > 0) {
            if ($metaContinuada)
                $requisicaoPesquisa .= ' and (m.mapa_id = ? or manter_monitoria = true)';
            else
                $requisicaoPesquisa .= ' and m.mapa_id = ?';
            $parametro[] = $mapa;
        }
        if (is_numeric($situacao)) {
            switch ($situacao) {
                case 1:
                    $requisicaoPesquisa .= ' and datediff(m.data_final, now()) >= 15 and m.data_conclusao is null';
                    break;
                case 2:
                    $requisicaoPesquisa .= ' and datediff(m.data_final, now()) < 15 and datediff(m.data_final, now()) >= 0 and m.data_conclusao is null';
                    break;
                case 3:
                    $requisicaoPesquisa .= ' and datediff(m.data_final, now()) < 0 and m.data_conclusao is null';
                    break;
                case 4:
                    $requisicaoPesquisa .= ' and m.data_conclusao is not null';
                    break;
                case 5:
                    $requisicaoPesquisa .= ' and manter_monitoria = true';
                    break;
            }
        }

        return array($requisicaoPesquisa, $parametro);
    }

    private function carregarUltimosAcompanhamentos($observacao, $informacao, $problema, $monitoramento) {
        //Inicia array de acompanhamentos
        $acompanhamento = array();

        //Cria objetos se houver conteúdo
        if (strlen($observacao) > 0)
            $acompanhamento[] = new Acompanhamento(0, 0, $observacao, 0);
        if (strlen($informacao) > 0)
            $acompanhamento[] = new Acompanhamento(0, 0, $informacao, 1);
        if (strlen($problema) > 0)
            $acompanhamento[] = new Acompanhamento(0, 0, $problema, 2);
        if (strlen($monitoramento) > 0)
            $acompanhamento[] = new Acompanhamento(0, 0, $monitoramento, 3);

        return $acompanhamento;
    }

    private function converterArrayAcao($array, $metaId) {
        //Valida parâmetros
        if (!is_array($array))
            return 'Lista inválida de registros';
        if (!is_numeric($metaId) || $metaId <= 0 || strlen($metaId) > 5)
            return 'ID de meta inválida';

        //Verifica existência da classe necessária
        if (!class_exists('Acao'))
            return 'Não foi identificada a classe necessária';

        //Inicia o array de objeto
        $object = array();

        //Converte arrays em objects
        foreach ($array as $registro) {
            //Cria objeto e adiciona à lista
            $object[] = new Acao($registro['id'], $metaId, $registro['nome'], $registro['prazo'], $registro['concluida'], $registro['concluida_id'], $registro['monitorada'], $registro['monitor_id'], $registro['aprovada'], $registro['criado'], $registro['criador_id'], $registro['modificado'], $registro['modificador_id'], $registro['desativado']);
        }

        return $object;
    }

    private function converterArrayAcompanhamento($array, $metaId) {
        //Valida parâmetros
        if (!is_array($array))
            return 'Lista inválida de registros';
        if (!is_numeric($metaId) || $metaId <= 0 || strlen($metaId) > 5)
            return 'ID de meta inválida';

        //Verifica existência da classe necessária
        if (!class_exists('Acompanhamento'))
            return 'Não foi identificada a classe necessária';

        //Inicia o array de objeto
        $object = array();

        //Converte arrays em objects
        foreach ($array as $registro) {
            //Cria objeto e adiciona à lista
            $object[] = new Acompanhamento($registro['id'], $metaId, $registro['texto'], $registro['tipo'], $registro['criado'], $registro['criador_id'], $registro['modificado'], $registro['modificador_id'], $registro['usuario_nome'], $registro['usuario_perfil'], $registro['desativado'], $registro['modificador_nome']);
        }

        return $object;
    }

    private function converterArrayAnexo($array, $metaId) {
        //Valida parâmetros
        if (!is_array($array))
            return 'Lista inválida de registros';
        if (!is_numeric($metaId) || $metaId <= 0 || strlen($metaId) > 5)
            return 'ID de meta inválida';

        //Verifica existência da classe necessária
        if (!class_exists('Anexo'))
            return 'Não foi identificada a classe necessária';

        //Inicia o array de objeto
        $object = array();

        //Converte arrays em objects
        foreach ($array as $registro) {
            //Cria objeto e adiciona à lista
            $object[] = new Anexo($registro['id'], $metaId, $registro['nome'], $registro['extensao'], $registro['criado'], $registro['criador_id'], $registro['modificado'], $registro['modificador_id'], $registro['desativado']);
        }

        return $object;
    }

    private function converterArrayMeta($array, $completo = false) {
        //Valida parâmetros
        if (!is_array($array))
            return 'Lista inválida de registros';
        if (!is_bool($completo))
            $completo = false;

        //Verifica existência da classe necessária
        if (!class_exists('Meta'))
            return 'Não foi identificada a classe necessária';

        //Inicia o array de objeto
        $object = array();

        //Converte arrays em objects
        foreach ($array as $registro) {
            //Se está carregando a meta completa, seleciona os anexos e comentários da meta
            if ($completo) {
                //Carrega as ações do registro
                $acoes = $this->selecionarAcoes($registro['id']);
                //Valida retorno
                if (is_string($acoes))
                    return $acoes;
                if (!is_array($acoes))
                    return 'Não foi possível carregar as ações';

                //Carrega os comentários do registro
                $acompanhamentos = $this->selecionarAcompanhamentos($registro['id']);
                //Valida retorno
                if (is_string($acompanhamentos))
                    return $acompanhamentos;
                if (!is_array($acompanhamentos))
                    return 'Não foi possível carregar os acompanhamentos';

                //Carrega os anexos do registro
                $anexos = $this->selecionarAnexos($registro['id']);
                //Valida retorno
                if (is_string($anexos))
                    return $anexos;
                if (!is_array($anexos))
                    return 'Não foi possível carregar os anexos';
            } else {
                //Passa os dados selecionados no banco para a criação de lista de objetvos Acompanhamento
                //Cada parâmetro representa o texto de cada tipo de acompanhamento
                $acompanhamentos = $this->carregarUltimosAcompanhamentos(
                        isset($registro['observacao']) ? $registro['observacao'] : '',
                        isset($registro['informacao']) ? $registro['informacao'] : '',
                        isset($registro['problema']) ? $registro['problema'] : '',
                        isset($registro['monitoramento']) ? $registro['monitoramento'] : ''
                );
                //Valida retorno
                if (!is_array($acompanhamentos))
                    unset($acompanhamentos);
            }
            //Cria objeto e adiciona à lista
            $object[] = new Meta(
                    $registro['id'],
                    $registro['numero'],
                    $registro['titulo'],
                    $registro['responsavel_id'],
                    $registro['responsavel_nome'],
                    $registro['responsavel_pessoa'],
                    $registro['criado'],
                    $registro['criador_id'],
                    $registro['modificado'],
                    $registro['modificador_id'],
                    $registro['desativado'],
                    isset($acompanhamentos) ? $acompanhamentos : null,
                    isset($anexos) ? $anexos : null,
                    isset($registro['anexos']) ? $registro['anexos'] : 0,
                    isset($registro['tipo_0']) ? $registro['tipo_0'] : 0,
                    isset($registro['tipo_1']) ? $registro['tipo_1'] : 0,
                    isset($registro['tipo_2']) ? $registro['tipo_2'] : 0,
                    isset($registro['tipo_3']) ? $registro['tipo_3'] : 0,
                    isset($registro['data_inicial']) ? $registro['data_inicial'] : '',
                    isset($registro['data_final']) ? $registro['data_final'] : '',
                    isset($registro['data_conclusao']) ? $registro['data_conclusao'] : '',
                    $this->selecionarTotaisAcoes($registro['id']),
                    isset($acoes) ? $acoes : null,
                    (isset($registro['ind_objetivo']) ? $registro['ind_objetivo'] : ''),
                    (isset($registro['ind_ods']) ? $registro['ind_ods'] : ''),
                    (isset($registro['ind_titulo']) ? $registro['ind_titulo'] : ''),
                    (isset($registro['ind_referencia']) ? $registro['ind_referencia'] : 0),
                    (isset($registro['ind_indicador']) ? $registro['ind_indicador'] : 0),
                    (isset($registro['ind_unidade']) ? $registro['ind_unidade'] : ''),
                    (isset($registro['ind_sec_valor']) ? $registro['ind_sec_valor'] : 0),
                    (isset($registro['ind_sec_datahora']) ? $registro['ind_sec_datahora'] : ''),
                    (isset($registro['ind_mon_valor']) ? $registro['ind_mon_valor'] : 0),
                    (isset($registro['ind_mon_datahora']) ? $registro['ind_mon_datahora'] : ''),
                    null,
                    (isset($registro['manter_monitoria']) ? $registro['manter_monitoria'] : '')
            );
        }

        return $object;
    }

    private function enviarEmailAcompanhamento($responsavelId) {
        //Valida parâmetro
        if (!is_numeric($responsavelId) || $responsavelId <= 0 || strlen($responsavelId) > 3)
            return 'Responsável inválido';

        //Verifica se é do perfil GP e se está editando uma meta que não pertence ao próprio perfil
        if ($this->login->perfil_id == $responsavelId || $this->login->perfil_id != PERFIL_ENVIA_EMAIL)
            return false;

        //Adiciona classe de envio de email, com ARROBA para impedir mensagem de erro
        @include_once './modelo/PHPMailerAutoload.php';

        //Verifica se conseguiu carregar a classe
        if (!class_exists('PHPMailer'))
            return 'Não foi possível carregar a classe de envio de e-mail';

        //Seleciona e-mails dos usuários do perfil responsável
        $usuarios = $this->selecionarEmails($responsavelId);

        //Verifica se encontrou algum e-mail
        if (!is_array($usuarios) || count($usuarios) == 0)
            return 'Nenhum e-mail encontrado';

        //Carrega informação a ser enviada
        $assunto = 'Mapa Estratégico - Novo acompanhamento do GP';
        $mensagem = 'O Gabinete do Prefeito adicionou um acompanhamento em uma de suas metas.';
        $mensagem .= '<br>Acesse o sistema para visualizar: <a href="www.esteio.rs.gov.br/mapa">www.esteio.rs.gov.br/mapa</a>';

        //Carrega dados no objeto de envio de e-mail
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Charset = 'utf8';
        $mail->Host = '10.0.0.5';
        $mail->Port = '25';
        $mail->Username = 'nao-responda@esteio.rs.gov.br';
        $mail->Password = 'n4o@r3sponda';
        $mail->From = 'nao-responda@esteio.rs.gov.br';
        $mail->FromName = 'Mapa';
        $mail->IsHTML(true);
        $mail->Subject = utf8_decode($assunto);
        $mail->Body = utf8_decode($mensagem);

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        //Carrega e-mails para envio
        foreach ($usuarios as $usuario) {
            if (isset($usuario['nome']) && isset($usuario['email']))
                $mail->addBCC($usuario['email'], $usuario['nome']);
        }

        //Envia e-mail
        if (!$mail->Send())
            return 'Não foi possível enviar o e-mail';

        return true;
    }

    private function inserirAcao($registro) {
        //Valida parâmetros
        if (!is_object($registro) || get_class($registro) != 'Acao')
            return array(MENSAGEM_ERRO => 'Dados inválidos. Tente novamente.');
        if (!is_object($this->login) || get_class($this->login) != 'LoginControle')
            return array(MENSAGEM_ERRO => 'Login inválido');

        //Verifica se é o responsável pela meta e já está concluindo a ação criada
        if (strlen($registro->concluida) > 0 && $this->validarResponsavelId($registro->metaId) == $this->login->perfil_id) {
            $concluidaCampos = ' concluida, concluida_id,';
            $concluidaValores = ' now(), ?,';
        }

        //Monta requisição
        $requisicao = 'insert into acao (meta_id, nome, prazo,' . (isset($concluidaCampos) ? $concluidaCampos : '') . ' criado, criador_id) values (?, ?, ?,' . (isset($concluidaValores) ? $concluidaValores : '') . ' now(), ?)';
        //Monta parâmetros
        $parametros = array($registro->metaId, $registro->nome, $registro->prazo, $this->login->id);
        if (isset($concluidaValores))
            $parametros [] = $this->login->id;

        //Executa a requisição
        $resultado = $this->banco->query($requisicao, $parametros);

        //Verifica o resultado obtido
        if (is_string($resultado) && !is_numeric($resultado))
            return array(MENSAGEM_ERRO => $resultado);

        //Retorna o ID do registro adicionado
        return $resultado;
    }

    private function inserirMeta($registro) {
        //Valida parâmetros
        if (!is_object($registro) || get_class($registro) != 'Meta')
            return array(MENSAGEM_ERRO => 'Dados inválidos. Tente novamente.');
        if (!is_object($this->login) || get_class($this->login) != 'LoginControle')
            return array(MENSAGEM_ERRO => 'Login inválido');

        //Monta requisição
        $requisicao = 'insert into meta (mapa_id, numero, titulo, responsavel_id, responsavel_nome, data_inicial, data_final, ind_objetivo, ind_ods, ind_titulo, ind_referencia, ind_indicador, ind_unidade, ind_mon_valor, ind_mon_datahora, manter_monitoria, criado, criador_id) values ((select max(id) from mapa where desativado is null), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(), ?, now(), ?)';

        //Monta parâmetros
        $parametros = array(
            $registro->numero,
            $registro->titulo,
            $registro->responsavelId,
            $registro->responsavelPessoa,
            $registro->dataInicial,
            $registro->dataFinal,
            $registro->indObjetivo,
            $registro->indOds ? $registro->indOds : null,
            (strlen($registro->ind_titulo) ? $registro->ind_titulo : null),
            (is_numeric($registro->ind_referencia) ? $registro->ind_referencia : null),
            (is_numeric($registro->ind_indicador) ? $registro->ind_indicador : null),
            (strlen($registro->ind_unidade) ? $registro->ind_unidade : null),
            (is_numeric($registro->ind_mon_valor) ? $registro->ind_mon_valor : null),
            (is_bool($registro->manterMonitoria) ? $registro->manterMonitoria : null), $this->login->id);

        //Executa a requisição
        $resultado = $this->banco->query($requisicao, $parametros);

        //Verifica o resultado obtido
        if (is_string($resultado) && !is_numeric($resultado))
            return array(MENSAGEM_ERRO => $resultado);

        //Retorna o ID do registro adicionado
        return $resultado;
    }

    private function salvarAcao($registro, $responsavelId) {
        //Valida parâmetros
        if (!is_object($registro) || get_class($registro) != 'Acao')
            return 'Dados inválidos. Tente novamente.';
        if ($registro->metaId == 0)
            return 'Meta inválida';
        if (strlen($registro->nome) == 0)
            return 'Nome não informado';
        if (!is_numeric($responsavelId) || $responsavelId == 0)
            return 'ID de responsável inválida';

        //Se o registro não possuir ID, cadastra a ação
        if ($registro->id == 0)
            return $this->inserirAcao($registro);
        else
            return $this->atualizarAcao($registro, $responsavelId);
    }

    private function salvarAcompanhamento($registro) {
        //Valida parâmetros
        if (!is_object($registro) || get_class($registro) != 'Acompanhamento')
            return 'Dados inválidos. Tente novamente.';
        if ($registro->metaId == 0)
            return 'Meta inválida';
        if (strlen($registro->texto) == 0)
            return 'Texto não informado';
        if (!is_object($this->login) || get_class($this->login) != 'LoginControle')
            return 'Login inválido';


        $impF5 = md5($registro->texto);
        $sesTxt = isset($_SESSION['acom_f5_no']) ? $_SESSION['acom_f5_no'] : '';

        if ($impF5 == md5($sesTxt)) {
            return 1;
        } else {
            $_SESSION['acom_f5_no'] = $registro->texto;
        }

        //Monta requisição
        $requisicao = 'insert into acompanhamento (meta_id, texto, tipo, criado, criador_id) values (?, ?, ?, now(), ?)';

        //Monta parâmetros
        $parametros = array($registro->metaId, $registro->texto, $registro->tipo, $this->login->id);

        //Executa a requisição
        $resultado = $this->banco->query($requisicao, $parametros);

        //Retorna o ID do registro adicionado ou mensagem de erro
        //Se não for numérico, o receptor entenderá que não salvou
        return $resultado;
    }

    private function salvarAnexo($registro) {
        //Valida parâmetros
        if (!is_object($registro) || get_class($registro) != 'Anexo')
            return 'Dados inválidos. Tente novamente.';
        if ($registro->metaId == 0)
            return 'Meta inválida';
        if (strlen($registro->nome) == 0)
            return 'Nome não informado';
        if (strlen($registro->extensao) == 0)
            return 'Extensão do arquivo não encontrada';
        if (strlen($registro->nomeTemporario) == 0)
            return 'Arquivo não encontrado';
        if (!is_object($this->login) || get_class($this->login) != 'LoginControle')
            return 'Login inválido';

        //Monta requisição
        $requisicao = 'insert into anexo (meta_id, nome, extensao, criado, criador_id) values (?, ?, ?, now(), ?)';

        //Monta parâmetros
        $parametros = array($registro->metaId, $registro->nome, $registro->extensao, $this->login->id);

        //Executa a requisição
        $resultado = $this->banco->query($requisicao, $parametros);

        //Tenta guardar o ID gerado
        $registro->id = $resultado;

        //Se salvar corretamente, move arquivo da pasta provisória para a permanente
        if ($registro->id > 0) {
            $this->armazenarAnexo($registro);
        }

        //Retorna o ID do registro adicionado ou mensagem de erro
        //Se não for numérico, o receptor entenderá que não salvou
        return $resultado;
    }

    private function selecionarAcoes($metaId) {
        //Valida parâmetro
        if (!is_numeric($metaId) || $metaId <= 0 || strlen($metaId) > 5)
            return 'ID de meta inválida';

        //Inicia lista de parâmetros
        $parametro = array();

        //Monta requisição
        $requisicao = 'select a.id, a.meta_id, a.nome, a.prazo, a.concluida, a.concluida_id, a.monitorada, a.monitor_id, a.aprovada, a.criado, a.criador_id, a.modificado, a.modificador_id, a.desativado from acao a ';

        //Adiciona condições
        $requisicaoWhere = 'where a.meta_id = ? and a.desativado is null order by a.aprovada desc, a.monitorada is null, a.concluida is null, a.prazo, a.nome';
        $parametro[] = $metaId;

        //Executa a requisição
        $resultado = $this->banco->query($requisicao . $requisicaoWhere, $parametro);

        //Verifica o resultado obtido
        if (is_string($resultado))
            return $resultado;
        if (!is_array($resultado))
            return 'Não foi possível carregar a informação';

        //Retorna um ARRAY, agora de objetos e não mais de ARRAYs
        return $this->converterArrayAcao($resultado, $metaId);
    }

    public function visibilidadeAcompanhamento($restaurar = null, $idAcompanhemnto = 0, $idUsuario = 0) {
        //Monta requisição
        $requisicao = 'update acompanhamento set desativado=?, modificador_id = (select id from usuario where usuario = ? limit 1), modificado = now() where id = ?';

        //Monta parâmetros
        $parametros = array($restaurar, $idUsuario, $idAcompanhemnto);

        //Executa a requisição
        $resultado = $this->banco->query($requisicao, $parametros);
    }

    private function selecionarAcompanhamentos($id) {
        //Valida parâmetro
        if (!is_numeric($id) || $id <= 0 || strlen($id) > 5)
            return 'ID de meta inválida';

        //Inicia lista de parâmetros
        $parametro = array();

        //Monta requisição
        //$requisicao = 'select a.id, a.texto, a.tipo, a.criado, a.criador_id, a.modificado, a.modificador_id, u.nome as usuario_nome, p.nome as usuario_perfil, a.desativado from acompanhamento a left join usuario u on u.id = if(a.modificador_id is not null,a.modificador_id,a.criador_id) left join perfil p on p.id = u.perfil_id where a.meta_id = ? and a.desativado is null order by if(a.modificado is not null,a.modificado,a.criado) desc';
        $requisicao = 'select a.id, a.texto, a.tipo, a.criado, a.criador_id, a.modificado, a.modificador_id, u.nome as usuario_nome, p.nome as usuario_perfil, a.desativado, (select nome from usuario where a.modificador_id = id) as modificador_nome  from acompanhamento a left join usuario u on u.id = a.criador_id left join perfil p on p.id = u.perfil_id where a.meta_id = ? order by a.criado desc';
        $parametro[] = $id;

        //Executa a requisição
        $resultado = $this->banco->query($requisicao, $parametro);

        //Verifica o resultado obtido
        if (is_string($resultado))
            return $resultado;
        if (!is_array($resultado))
            return 'Não foi possível carregar a informação';

        //Retorna um ARRAY, agora de objetos e não mais de ARRAYs
        return $this->converterArrayAcompanhamento($resultado, $id);
    }

    private function selecionarAnexos($metaId, $id = 0) {
        //Valida parâmetro
        if (!is_numeric($id) || $id <= 0 || strlen($id) > 9)
            $id = 0;
        if ((!is_numeric($metaId) || $metaId <= 0 || strlen($metaId) > 5) && (!is_null($metaId) && $id == 0))
            return 'ID de meta inválida';

        //Inicia lista de parâmetros
        $parametro = array();

        //Monta requisição
        $requisicao = 'select a.id, a.meta_id, a.nome, a.extensao, a.criado, a.criador_id, a.modificado, a.modificador_id, a.desativado from anexo a ';
        if ($id == 0) {
            //Adiciona condições
            //$requisicaoWhere = 'where a.meta_id = ? and a.desativado is null order by a.nome';
            $requisicaoWhere = 'where a.meta_id = ? and a.desativado is null order by a.criado desc ';
            $parametro[] = $metaId;
        } else {
            //Adiciona condições
            $requisicaoWhere = 'where a.id = ? and a.desativado is null';
            $parametro[] = $id;

            //Verifica as permissões
            if ($this->login->permissao($this->modulo_arquivo, LISTAR) < TODOS) {
                //Adiciona validação para o usuário
                $requisicao .= 'left join meta m on m.id = a.meta_id left join usuario u on m.responsavel_id = u.perfil_id ';
                $requisicaoWhere .= ' and u.id=?';
                //Adiciona parâmetros da requisição
                $parametro[] = $this->login->id;
            }
        }

        //Executa a requisição
        $resultado = $this->banco->query($requisicao . $requisicaoWhere, $parametro);

        //Verifica o resultado obtido
        if (is_string($resultado))
            return $resultado;
        if (!is_array($resultado))
            return 'Não foi possível carregar a informação';

        //Se estiver carregando o anexo para baixar, atualizar a metaId
        if (is_null($metaId) && isset($resultado[0]['meta_id']) && is_numeric($resultado[0]['meta_id']))
            $metaId = $resultado[0]['meta_id'];

        //Retorna um ARRAY, agora de objetos e não mais de ARRAYs
        return $this->converterArrayAnexo($resultado, $metaId);
    }

    private function selecionarEmails($responsavelId) {
        //Valida parâmetro
        if (!is_numeric($responsavelId) || $responsavelId <= 0 || strlen($responsavelId) > 3)
            return 'Responsável inválido';

        //Inicia lista de parâmetros
        $parametro = array();

        //Monta requisição
        $requisicao = 'select nome, email from usuario where email is not null and perfil_id=?';
        $parametro[] = $responsavelId;

        //Executa a requisição
        $resultado = $this->banco->query($requisicao, $parametro);

        //Verifica o resultado obtido
        if (is_string($resultado) || is_array($resultado))
            return $resultado;
        if (!is_array($resultado))
            return 'Não foi possível carregar a informação';
    }

    private function selecionarTotaisAcoes($metaId) {
        //Valida parâmetro
        if (!is_numeric($metaId) || $metaId <= 0 || strlen($metaId) > 5)
            return 'ID de meta inválida';

        //Inicia lista de parâmetros
        $parametro = array();

        //Monta requisição
        $requisicao = 'select count(*) as total, concluida is not null as concluida, monitorada is not null as monitorada, aprovada = true and aprovada is not null as aprovada from acao where meta_id = ? and desativado is null group by concluida is null, monitorada is null, aprovada = true and aprovada is not null';
        $parametro[] = $metaId;

        //Executa a requisição
        $resultado = $this->banco->query($requisicao, $parametro);

        //Verifica o resultado obtido
        if (is_string($resultado))
            return $resultado;
        if (!is_array($resultado))
            return 'Não foi possível carregar a informação';

        //Inicia ARRAY com o total de cada situação
        $situacao = array(
            'aprovada' => 0,
            'reprovada' => 0,
            'aguardando' => 0,
            'aberta' => 0,
            'total' => 0
        );

        //Verifica se a meta possui ações
        if (count($resultado) == 0)
            return $situacao;

        //Monta ARRAY com o total de cada situação
        while (!is_null($status = array_shift($resultado))) {
            //Monta ARRAY com o total de cada situação
            $situacao['total'] += $status['total'];
            if ($status['aprovada'] == true)
                $situacao['aprovada'] = $status['total'];
            if ($status['monitorada'] == true && $status['aprovada'] == false)
                $situacao['reprovada'] = $status['total'];
            if ($status['concluida'] == true && $status['monitorada'] == false)
                $situacao['aguardando'] = $status['total'];
            if ($status['concluida'] == false)
                $situacao['aberta'] = $status['total'];
        }

        //Retorna um ARRAY com o total de cada situação
        return $situacao;
    }

}

?>
