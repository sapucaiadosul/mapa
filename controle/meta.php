<?php

/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

//Define ID do perfil que envia e-mail de notificação
define('PERFIL_ENVIA_EMAIL', 2);

Class MetaControle {

    private $login = null;
    private $banco = null;
    private $dao = null;
    private $registro = null;
    private $modulo_dependente = array();
    private $modulo_arquivo = 'meta';
    private $funcao = null; //Funcao

    function __construct($login, $banco) {
        $this->funcao = new Funcao();
        //Carrega classe DAO
        Funcao::carrega_arquivo('modelo', 'meta_dao');

        //Carrega classes base, pois na validação de campos o DAO é chamado depois
        Funcao::carrega_arquivo('modelo', 'acao_class');
        Funcao::carrega_arquivo('modelo', 'acompanhamento_class');
        Funcao::carrega_arquivo('modelo', 'anexo_class');
        Funcao::carrega_arquivo('modelo', 'meta_class');

        //Guarda objetos para uso futuro
        $this->login = $login;
        $this->banco = $banco;
    }

    public function __get($key) {
        if ($key == 'registro')
            return $this->registro;
    }

    public function css() {
        //Lista de arquivos CSS necessários para o módulo
        return array('meta');
    }

    public function excluir() {
        //Verifica se tem acesso ao banco
        if (is_null($this->banco))
            return array(MENSAGEM_ERRO => 'Não foi possível acessar o banco de dados');

        //Verifica se o usuário tem permissão de acesso
        if (!is_numeric($this->login->permissao($this->modulo_arquivo, EXCLUIR)))
            return array(MENSAGEM_ERRO => 'Você não tem permissão para acessar o conteúdo');

        //Carrega o DAO, se ainda não carregou-o no objeto
        if (is_null($this->dao))
            $this->dao = new MetaDao($this->login, $this->banco);

        //Verifica o que deve ser excluído e se realmente deve
        if (isset($_GET['excluir'])) {
            //Pega lista de IDs ou gera uma com o único ID recebido, senão é NULL
            $ids = (isset($_GET['id']) ? (is_numeric($_GET['id']) ? array($_GET['id']) : (is_array($_GET['id']) ? $_GET['id'] : null)) : null);
            //Verifica se recebeu algum ID para excluir
            if (is_null($ids))
                return false;

            //Percorre lista excluindo, se tiver permissão
            $apagou = 0;
            foreach ($ids as $id) {
                if (is_numeric($id)) {
                    if (!is_string($retorno = $this->dao->excluir($id))) {
                        $apagou++;
                    } else {
                        //Monta mensagem de retorno
                        $apagou = ($apagou > 0 ? $apagou . ' registro(s) excluído(s). ' : '') . $retorno . ', ID ' . $id;
                        break;
                    }
                }
            }

            //Retorna mensagem de sucesso ou mensagens de erro
            return (is_numeric($apagou) ? (count($ids) == $apagou ? true : array(MENSAGEM_ERRO => count($ids) - $apagou . ' registros não foram excluídos')) : array(MENSAGEM_ERRO => $apagou));
        }
    }

    public function formulario($id = null) {
        //Valida parâmetros
        if (!is_numeric($id) || $id < 1)
            $id = null;

        //Verifica se o usuário tem permissão de acesso
        if ((!is_numeric($this->login->permissao($this->modulo_arquivo, EDITAR)) && !is_null($id)) || (!is_numeric($this->login->permissao($this->modulo_arquivo, CADASTRAR)) && is_null($id)))
            return array('', array(MENSAGEM_ERRO => 'Você não tem permissão para acessar o conteúdo'));

        //Verifica se deve excluir uma ação
        if (isset($_GET['acao'])) {
            $retorno = $this->excluirAcao();
            //Guarda retorno para exibir nas mensagens
            if (is_array($retorno))
                $mensagemExclusao = $retorno;
            if ($retorno === true)
                $mensagemExclusao = array(MENSAGEM_SUCESSO => 'Ação removida com sucesso');
        }

        //Verifica se deve excluir um anexo
        if (isset($_GET['anexo'])) {
            $retorno = $this->excluirAnexo();
            //Guarda retorno para exibir nas mensagens
            if (is_array($retorno))
                $mensagemExclusao = $retorno;
            if ($retorno === true)
                $mensagemExclusao = array(MENSAGEM_SUCESSO => 'Anexo removido com sucesso');
        }

        //Carrega formulário de cadastro/alteração
        $retorno = $this->carregarFormulario($id);

        //Verifica se recebeu erro
        if (is_array($retorno))
            return array('', $retorno);
        if (!is_string($retorno))
            return array('', array(MENSAGEM_ERRO => 'Não foi possível carregar o formulário'));

        if (isset($mensagemExclusao))
            return array($retorno, $mensagemExclusao);
        else
            return array($retorno);
    }

    public function js() {
        //Lista de arquivos JS necessários para o módulo
        return array('meta');
    }

    public function lista($pagina = 1) {
        //Verifica se tem acesso ao banco
        if (is_null($this->banco))
            return false;
        //Valida parâmetro
        if (!is_numeric($pagina) || $pagina < 1)
            $pagina = 1;
        //Carrega demais variáveis de controle
        $numeroRegistros = 0; //Registros por página
        $ativos = isset($_SESSION[$this->modulo_arquivo . '_excluidos']) ? false : true; //Exibir apenas ATIVOS ou até excluídos
        //Verifica se o usuário tem permissão de acesso
        if (!is_numeric($this->login->permissao($this->modulo_arquivo, LISTAR)))
            return array('', array(MENSAGEM_ERRO => 'Você não tem permissão para acessar o conteúdo'));

        //Verifica se deve baixar um anexo
        if (isset($_GET['anexo'])) {
            $retorno = $this->baixarAnexo();
            if (is_array($retorno))
                return array('', $retorno);
        }

        //Carrega formulário de pesquisa
        $retornoPesquisa = $this->carregarTabs();

        //Verifica se recebeu erro
        if (is_array($retornoPesquisa))
            return array('', $retornoPesquisa);
        if (!is_string($retornoPesquisa))
            return array('', array(MENSAGEM_ERRO => 'Não foi possível carregar a pesquisa'));

        //Carrega lista de registros
        $retorno = $this->carregarLista($pagina, $numeroRegistros, $ativos);

        //Verifica se recebeu erro
        if (is_array($retorno))
            return array($retornoPesquisa, $retorno);
        if (!is_string($retorno))
            return array('', array(MENSAGEM_ERRO => 'Não foi possível carregar os registros'));

        //Carrega conteúdo gerado
        $conteudo = $retornoPesquisa . $retorno;

        //Carrega o número de registros
        $registros = $this->dao->selecionarNumeroRegistros();
        if (!is_numeric($registros))
            $registros = 0;

        //Calcula número de páginas
        $paginas = $this->calcularNumeroPaginas($registros);

        //Número de registros encontrados
        $conteudo .= $this->funcao->exibirTotalRegistros($registros);

        //Paginação
        $conteudo .= $this->funcao->pagina_criar_links($this->modulo_arquivo, $paginas, $pagina);

        return array($conteudo);
    }

    public function permitirEdicao($id) {
        //Valida parâmetro
        if (!is_numeric($id) || $id == 0 || strlen($id) > 5)
            return false;

        //Verifica se o registro já carregou
        if (property_exists($this->registro, 'id') && $this->registro->id == $id) {
            //Verifica se o usuário pertence ao perfil responsável pelo registro
            return $this->registro->responsavelId == $this->login->perfil_id;
        }

        //Recebe registro ou erro ou false (se não tiver permissão)
        if (is_object($this->dao->validarResponsavelId($id, $this->login->perfil_id)))
            return true;
        else
            return false;
    }

    public function salvar() {
        //Limpa registro local
        $this->registro = null;

        //Verifica se recebeu o formulário
        if (!isset($_POST))
            return array('');

        //Valida os campos retornado lista de erros ou registro
        $retorno = $this->validarCampos();

        //Verifica se há erros
        if (is_bool($retorno) && $retorno === false)
            return array('');
        if (is_array($retorno))
            return array($retorno, array('Corrija os campos destacados'));
        if (!is_object($this->registro) || get_class($this->registro) != 'Meta')
            return array('', array(MENSAGEM_ERRO => 'Não foi possível carregar os dados para salvar'));

        //Carrega o DAO, se ainda não carregou-o no objeto
        if (is_null($this->dao))
            $this->dao = new MetaDao($this->login, $this->banco);

        //Verifica se o usuário tem permissão de acesso
        if ($this->registro->id == 0) {
            //Valida permissão de inclusão
            if (!is_numeric($this->login->permissao($this->modulo_arquivo, CADASTRAR)))
                return array('', array(MENSAGEM_ERRO => 'Você não tem permissão para cadastrar'));
        } else {
            //Valida permissão de edição
            if ($this->login->permissao($this->modulo_arquivo, EDITAR) < TODOS) {
                if ($this->login->permissao($this->modulo_arquivo, EDITAR) != PROPRIOS || ($this->login->permissao($this->modulo_arquivo, EDITAR) == PROPRIOS && $this->dao->validarResponsavelId($this->registro->id, $this->login->perfil_id) === false))
                    return array('', array(MENSAGEM_ERRO => 'Você não tem permissão para acessar o conteúdo'));
            }
        }

        //Salva o registro
        $retorno = $this->dao->salvar($this->registro, $this->login);

        //Não havendo erros, limpa variável para carregar os dados salvos
        if (is_numeric($retorno)) {
            $this->registro = null;
            return $retorno;
        } elseif (is_array($retorno))
            return array('', $retorno);
        elseif (is_string($retorno))
            return array($retorno);
        else
            return false;
    }

    public function title() {
        return 'Meta';
    }

    private function baixarAnexo() {
        //Verifica URL
        $id = (isset($_GET['anexo']) && is_numeric($_GET['anexo']) && $_GET['anexo'] > 0 && strlen($_GET['anexo']) <= 9 ? $_GET['anexo'] : 0);

        //Valida variável recebida
        if ($id == 0)
            return array(MENSAGEM_ERRO => 'Anexo inválido');

        //Carrega o DAO, se ainda não carregou-o no objeto
        if (is_null($this->dao))
            $this->dao = new MetaDao($this->login, $this->banco);

        //Seleciona anexo solicitado, validando a permissão de visualização
        $registros = $this->dao->selecionarAnexo($id);

        //Valida retorno
        if (is_string($registros))
            return array(MENSAGEM_ERRO => $registros);
        if (!is_array($registros) || count($registros) != 1)
            return array(MENSAGEM_ERRO => 'Não foi possível carregar o anexo');

        //Obtém o objeto selecionado
        $registro = $registros[0];

        //Verifica se recebeu o objeto corretamente
        if (!is_object($registro) || get_class($registro) != 'Anexo')
            return array(MENSAGEM_ERRO => 'Não foi possível carregar corretamente o anexo');

        //Guarda em sessão os dados necessários para baixar o arquivos
        $_SESSION['anexo_id'] = $registro->id;
        $_SESSION['anexo_nome'] = $registro->nome;
        $_SESSION['anexo_extensao'] = $registro->extensao;
        $_SESSION['modulo'] = $this->modulo_arquivo;

        //Estando com os dados carregados, move para página de download
        header('Location: baixar.php');
    }

    private function calcularNumeroPaginas($registros = 0, $limite = 0) {
        //Valida parâmetro
        if (!is_numeric($registros) || $registros < 0)
            $registros = 0;
        if (!is_numeric($limite) || $limite == 0)
            $limite = Config::$db_limite;

        //Calcula o número de páginas
        $paginas = ceil($registros / $limite);

        return $paginas;
    }

    private function carregarBarraAcao($acaoTotais, $exibirPorcentagem = false) {
        //Valida parâmetro
        if (!is_array($acaoTotais) || !isset($acaoTotais['total']) || !is_numeric($acaoTotais['total']) || $acaoTotais['total'] == 0)
            $acaoTotais = array();
        if (!is_bool($exibirPorcentagem))
            $exibirPorcentagem = false;

        //Calcula porcentagens
        $aprovada = isset($acaoTotais['aprovada']) && is_numeric($acaoTotais['aprovada']) && $acaoTotais['aprovada'] > 0 ? $acaoTotais['aprovada'] : 0;
        $aprovadaPorcento = $aprovada > 0 ? round(($aprovada / $acaoTotais['total']) * 100, 2) : 0;
        $reprovada = isset($acaoTotais['reprovada']) && is_numeric($acaoTotais['reprovada']) && $acaoTotais['reprovada'] > 0 ? $acaoTotais['reprovada'] : 0;
        $reprovadaPorcento = $reprovada > 0 ? round(($reprovada / $acaoTotais['total']) * 100, 2) : 0;
        $aguardando = isset($acaoTotais['aguardando']) && is_numeric($acaoTotais['aguardando']) && $acaoTotais['aguardando'] > 0 ? $acaoTotais['aguardando'] : 0;
        $aguardandoPorcento = $aguardando > 0 ? round(($aguardando / $acaoTotais['total']) * 100, 2) : 0;
        $aberta = isset($acaoTotais['aberta']) && is_numeric($acaoTotais['aberta']) && $acaoTotais['aberta'] > 0 ? $acaoTotais['aberta'] : 0;

        //Monta barra conforme informações recebidas
        $barra = '<div class="progress" id="acao_bar">';

        $barra .= '<input id="total_aprovada" type="hidden" value="' . $aprovada . '">';
        $barra .= '<div class="progress-bar progress-bar-success" style="width: ' . $aprovadaPorcento . '%" data-toggle="tooltip" data-placement="bottom" ' . ($exibirPorcentagem ? '' : 'title="' . $aprovadaPorcento . '%"') . '>' . ($exibirPorcentagem ? $aprovadaPorcento . '%' : '') . '</div>';

        $barra .= '<input id="total_reprovada" type="hidden" value="' . $reprovada . '">';
        $barra .= '<div class="progress-bar progress-bar-danger" style="width: ' . $reprovadaPorcento . '%" data-toggle="tooltip" data-placement="bottom" ' . ($exibirPorcentagem ? '' : 'title="' . $reprovadaPorcento . '%"') . '>' . ($exibirPorcentagem ? $reprovadaPorcento . '%' : '') . '</div>';

        $barra .= '<input id="total_aguardando" type="hidden" value="' . $aguardando . '">';
        $barra .= '<div class="progress-bar progress-bar-warning" style="width: ' . $aguardandoPorcento . '%" data-toggle="tooltip" data-placement="bottom" ' . ($exibirPorcentagem ? '' : 'title="' . $aguardandoPorcento . '%"') . '>' . ($exibirPorcentagem ? $aguardandoPorcento . '%' : '') . '</div>';

        $barra .= '<input id="total_aberta" type="hidden" value="' . $aberta . '">';

        $barra .= '</div>';

        return $barra;
    }

    private function carregarBarraIndicador($registro, $exibirPorcentagem = false) {
        //Valida parâmetro
        if (!is_object($registro) || get_class($registro) != 'Meta')
            $registro = $this->registro;
        if (!is_bool($exibirPorcentagem))
            $exibirPorcentagem = false;

        //Calcular valores
        $indicador = $registro->indIndicador - $registro->indReferencia;
        $aprovado = $registro->indMonValor - $registro->indReferencia;
        $reprovado = $registro->indSecDatahora <= $registro->indMonDatahora ? $registro->indSecValor - ($registro->indMonValor > 0 ? $registro->indMonValor : $registro->indReferencia) : 0;
        $aguardando = $registro->indSecDatahora > $registro->indMonDatahora ? $registro->indSecValor - ($registro->indMonValor > 0 ? $registro->indMonValor : $registro->indReferencia) : 0;

        //Se indicador decrescente, inverte valores
        if ($indicador < 0) {
            $indicador *= -1;
            $aprovado *= -1;
            $reprovado *= -1;
            $aguardando *= -1;
        }

        //Calcula porcentagens
        $aprovadoPorcento = $indicador > 0 ? round(($aprovado / $indicador) * 100, 2) : 0;
        $reprovadoPorcento = $indicador > 0 ? round(($reprovado / $indicador) * 100, 2) : 0;
        $aguardandoPorcento = $indicador > 0 ? round(($aguardando / $indicador) * 100, 2) : 0;

        //Monta barra conforme informações recebidas
        $barra = '<div class="progress" id="indicador_bar">';

        $barra .= '<input id="indicador_total_aprovado" type="hidden" value="' . $aprovado . '">';
        $barra .= '<div class="progress-bar progress-bar-success" style=" text-align:left; width: ' . $aprovadoPorcento . '%" data-toggle="tooltip" data-placement="bottom" ' . ($exibirPorcentagem ? '' : 'title="' . $aprovadoPorcento . '%"') . '>' . ($exibirPorcentagem ? $aprovadoPorcento . '%' : '') . '</div>';

        $barra .= '<input id="indicador_total_reprovado" type="hidden" value="' . $reprovado . '">';
        $barra .= '<div class="progress-bar progress-bar-danger" style="width: ' . $reprovadoPorcento . '%" data-toggle="tooltip" data-placement="bottom" ' . ($exibirPorcentagem ? '' : 'title="' . $reprovadoPorcento . '%"') . '>' . ($exibirPorcentagem ? $reprovadoPorcento . '%' : '') . '</div>';

        $barra .= '<input id="indicador_total_aguardando" type="hidden" value="' . $aguardando . '">';
        $barra .= '<div class="progress-bar progress-bar-warning" style="width: ' . $aguardandoPorcento . '%" data-toggle="tooltip" data-placement="bottom" ' . ($exibirPorcentagem ? '' : 'title="' . $aguardandoPorcento . '%"') . '>' . ($exibirPorcentagem ? $aguardandoPorcento . '%' : '') . '</div>';

        $barra .= '</div>';

        return $barra;
    }

    private function calcularIndicadores($valorInd, $valorRef, $valorAlc) {


        return $porcentagem;
    }

    private function carregarFormulario($id = null) {
        //Verifica se tem acesso ao banco
        if (is_null($this->banco))
            return array(MENSAGEM_ERRO => 'Não foi possível conectar ao banco de dados');

        //Valida parâmetro
        if (!is_numeric($id))
            $id = null;

        //Verifica existência da tela
        $visao_arquivo = './visao/meta_visao_form.php';
        if (!file_exists($visao_arquivo))
            return array(MENSAGEM_ERRO => 'Não foi possível carregar o formulário');

        //Carrega dados do formulário
        //Não havendo dados, cria registro em branco para criar novo registro
        $registro = $this->selecionarDadosFormulario($id);

        //Verifica se conseguiu carregar o registro
        if (is_array($registro))
            return $registro;
        elseif (!is_object($registro) || get_class($registro) != 'Meta')
            return array(MENSAGEM_ERRO => 'Não foi possível carregar o registro');

        //Se estiver inserindo um novo registro (id == 0 e responsavelId == 0), verifica se há responsável selecionado na pesquisa, para facilitar a inserção pelas secretarias
        if ($registro->id == 0 && $registro->responsavelId == 0 && isset($_SESSION['responsavel']) && is_numeric($_SESSION['responsavel']) && $_SESSION['responsavel'] > 1 && strlen($_SESSION['responsavel']) <= 3)
            $registro->responsavelId = $_SESSION['responsavel'];

        //Carrega select de perfis
        $responsaveis = $this->carregarPerfis($registro->responsavelId);
        if (is_array($responsaveis))
            return $responsaveis;

        //Estando os dados prontos para serem exibidos na tela, guarda registro no atributo do objetos
        $this->registro = $registro;

        //Carrega HTML
        ob_start();
        include_once $visao_arquivo;
        $form = ob_get_clean();

        return $form;
    }

    private function carregarLista($pagina = 1, $numeroRegistros = 0, $ativos = true) {
        //Verifica se tem acesso ao banco
        if (is_null($this->banco))
            return false;
        //Valida parâmetros
        if (!is_numeric($pagina))
            $pagina = 1;
        if (!is_numeric($numeroRegistros))
            $numeroRegistros = 0;
        if (!is_bool($ativos))
            $ativos = true;

        //Carrega o DAO, se ainda não carregou-o no objeto
        if (is_null($this->dao))
            $this->dao = new MetaDao($this->login, $this->banco);

        //Seleciona registros para montar a tabela
        $registros = $this->dao->selecionarMeta(null, $ativos, $pagina, $numeroRegistros);

        //Verifica se encontrou algum registro
        if (is_numeric($registros) && $registros == 0)
            return array(MENSAGEM_PADRAO => 'Nenhum registro encontrado');
        if (is_string($registros))
            return array(MENSAGEM_ERRO => $registros);
        if (!is_array($registros))
            return array(MENSAGEM_ERRO => 'Não foi possível carregar os registros');

        //Verifica se o usuário tem permissão de excluir
        $permissao_excluir = $this->login->permissao($this->modulo_arquivo, EXCLUIR);

        //Monta linhas da tabela
        $linhas = '';
        foreach ($registros as $registro) {
            //Carrega textos dos acompanhamentos
            $acompanhamentoTexto = array();
            if (is_array($registro->acompanhamento)) {
                foreach ($registro->acompanhamento as $acompanhamento) {
                    $acompanhamentoTexto[$acompanhamento->tipo] = strip_tags($acompanhamento->texto);
                }
            }

            //Calcula prazo da meta
            $diasFinal = (new DateTime(date('Y-m-d')))->diff(new DateTime($registro->dataFinal)); //Utilizado date() ao invés de now, pois now pega a hora junto, considerando em atraso o dia atual
            //Verifica a classe a ser atribuída ao registro
            if ($registro->desativado)
                $classe = 'info';
            elseif (strlen($registro->dataConclusao) > 0)
                $classe = 'success';
            elseif ($diasFinal->invert == 1)
                $classe = 'danger';
            elseif ($diasFinal->days < 15)
                $classe = 'warning';
            else
                $classe = ''; //Limpa para evitar sugeira de loop






                
//Template criado no código devido ao alto número de validações
            $linhas .= '<tr' . (strlen($classe) > 0 ? ' class="' . $classe . '"' : '') . '>';
            if ($permissao_excluir >= PROPRIOS) {
                $pemissao_excluir_proprio = true;
                $linhas .= '<td class="text-center hidden-xs">';
                if (($permissao_excluir >= TODOS || ($permissao_excluir >= PROPRIOS && $registro->responsavelId == $this->login->perfil_id)) && !$registro->desativado)
                    $linhas .= '<input type="checkbox" value="' . $registro->id . '">';
                $linhas .= '</td>';
            }

            $indOdsArray = json_decode($registro->indOds);
            $badges = '';

            if ($indOdsArray) {
                foreach ($indOdsArray as $numero) {
                    $badges .= '<span class="badge alert-default" data-toggle="tooltip" data-placement="bottom" title="' . $numero . '">' . $numero . '</span>';
                }
            }

            $linhas .= '<td class="text-center">' . ($registro->manterMonitoria ? '<a href="index.php?mod=' . $this->modulo_arquivo . '&formulario&id=' . $registro->id . '" title="Meta continuada">!</a>' : '') . '</td>';
            $linhas .= '<td class="text-center"><a href="index.php?mod=' . $this->modulo_arquivo . '&formulario&id=' . $registro->id . '">' . $registro->numero . '</a></td>';
            $linhas .= '<td><a href="index.php?mod=' . $this->modulo_arquivo . '&formulario&id=' . $registro->id . '">' . $registro->titulo . '</a></td>';
            $linhas .= '<td><a href="index.php?mod=' . $this->modulo_arquivo . '&formulario&id=' . $registro->id . '">' . $registro->responsavelPessoa . '</a></td>';
            $linhas .= '<td class="hidden-xs"><a href="index.php?mod=' . $this->modulo_arquivo . '&formulario&id=' . $registro->id . '">' . $registro->responsavelNome . '</a></td>';
            $linhas .= '<td class="text-center hidden-xs">' . $badges . '</td>';
            $linhas .= '<td class="text-center hidden-xs"><span class="badge alert-warning"' . (isset($acompanhamentoTexto[3]) ? ' data-toggle="tooltip" data-placement="bottom" title="' . $acompanhamentoTexto[3] . '"' : '') . '>' . $registro->acompanhamentoTipoMonitoramento . '</span><span class="badge alert-default"' . (isset($acompanhamentoTexto[0]) ? ' data-toggle="tooltip" data-placement="bottom" title="' . $acompanhamentoTexto[0] . '"' : '') . '>' . ($registro->acompanhamentoTipoObservacao + $registro->acompanhamentoTipoInformacao + $registro->acompanhamentoTipoProblema) . '</span></td>';
            $linhas .= '<td class="text-center hidden-xs"><a href="index.php?mod=' . $this->modulo_arquivo . '&formulario&id=' . $registro->id . '"><span class="badge">' . $registro->anexos . '</span></a></td>';
            //Se tiver indicador, exibe-o. Senão, exibe as ações
            if ($registro->indIndicador > 0)
                $linhas .= '<td>' . $this->carregarBarraIndicador($registro) . '</td>';
            else
                $linhas .= '<td>' . $this->carregarBarraAcao($registro->acaoTotais) . '</td>';
            if ($permissao_excluir >= PROPRIOS) {
                $linhas .= '<td class="text-center hidden-xs">';
                if ($permissao_excluir >= TODOS || ($permissao_excluir >= PROPRIOS && $registro->responsavelId == $this->login->perfil_id))
                    $linhas .= ($registro->desativado ? '' : '<button class="btn btn-danger" onclick="registro_del(\'' . (isset($_GET['mod']) ? $_GET['mod'] : '') . '\',' . $registro->id . ',\'' . $registro->nome . '\');"><span class="glyphicon glyphicon-remove"></span></button>');
                $linhas .= '</td>';
            }
            $linhas .= '</tr>';
        }

        //Verifica existência da tela
        $visao_arquivo = './visao/meta_visao_table.php';
        if (!file_exists($visao_arquivo))
            return array(MENSAGEM_ERRO => 'Não foi possível carregar a tabela de usuários');

        //Carrega HTML
        ob_start();
        include_once $visao_arquivo;
        $table = ob_get_clean();

        return $table;
    }

    private function carregarMapas($mapaId = null) {
        //Valida mapa
        if (!is_numeric($mapaId) || $mapaId < 0)
            $mapaId = null;

        //Carrega classe de controle
        $this->funcao->carrega_arquivo('controle', 'mapa');
        //Cria objeto para utilizar funções públicas
        $mapa = new MapaControle($this->login, $this->banco);
        //Carrega select de mapas
        $mapas = $mapa->select($mapaId);

        //Verifica se ocorreu algum erro
        if (is_array($mapas) || is_string($mapas))
            return $mapas;
        if (!is_string($mapas))
            return array(MENSAGEM_ERRO => 'Não foi possível carregar a lista de responsáveis');
    }

    private function carregarPerfis($responsavelId = null) {
        //Valida parametro
        if (!is_numeric($responsavelId) || $responsavelId <= 1)
            $responsavelId = null;

        //Carrega classe de controle
        $this->funcao->carrega_arquivo('controle', 'perfil');
        //Cria objeto para utilizar funções públicas
        $perfil = new PerfilControle($this->login, $this->banco);
        //Carrega select de perfis, elimiando o ID 1, que é o administrador, através do segundo parâmetro que é TRUE
        $responsaveis = $perfil->select($responsavelId, true);

        //Verifica se ocorreu algum erro
        if (is_array($responsaveis) || is_string($responsaveis))
            return $responsaveis;
        if (!is_string($responsaveis))
            return array(MENSAGEM_ERRO => 'Não foi possível carregar a lista de responsáveis');
    }

    private function carregarTabs() {
        //Verifica se o usuário tem permissão de acesso
        if (!is_numeric($this->login->permissao($this->modulo_arquivo, LISTAR)))
            return array(MENSAGEM_ERRO => 'Você não tem permissão para acessar o conteúdo.');

        $_SESSION['numero'] = (isset($_GET['numero']) ? (is_numeric($_GET['numero']) ? $_GET['numero'] : '') : (isset($_SESSION['numero']) && is_numeric($_SESSION['numero']) ? $_SESSION['numero'] : ''));
        $_SESSION['ods'] = (isset($_GET['ods']) ? (is_numeric($_GET['ods']) ? $_GET['ods'] : '') : (isset($_SESSION['ods']) && is_numeric($_SESSION['ods']) ? $_SESSION['ods'] : ''));
        $_SESSION['pesquisa'] = (isset($_GET['pesquisa']) ? $_GET['pesquisa'] : (isset($_SESSION['pesquisa']) ? $_SESSION['pesquisa'] : ''));
        $_SESSION['campo'] = (isset($_GET['campo']) ? $_GET['campo'] : (isset($_SESSION['campo']) ? $_SESSION['campo'] : ''));
        $_SESSION['responsavel'] = (isset($_GET['responsavel']) ? $_GET['responsavel'] : (isset($_SESSION['responsavel']) ? $_SESSION['responsavel'] : ''));
        $_SESSION['responsavelPessoa'] = (isset($_GET['responsavel_pessoa']) ? $_GET['responsavel_pessoa'] : (isset($_SESSION['responsavelPessoa']) ? $_SESSION['responsavelPessoa'] : ''));
        $_SESSION['situacao'] = (isset($_GET['situacao']) && is_numeric($_GET['situacao']) ? $_GET['situacao'] : (isset($_SESSION['situacao']) && is_numeric($_SESSION['situacao']) ? $_SESSION['situacao'] : ''));
        $_SESSION['mapa'] = (isset($_GET['mapa']) && is_numeric($_GET['mapa']) ? $_GET['mapa'] : (isset($_SESSION['mapa']) && is_numeric($_SESSION['mapa']) ? $_SESSION['mapa'] : ''));
        $_SESSION['metas_continuadas'] = (isset($_GET['metas_continuadas']) ? true : (!isset($_GET['formulario']) && count($_GET) > 1 ? false : (isset($_SESSION['metas_continuadas']) && is_bool($_SESSION['metas_continuadas']) ? $_SESSION['metas_continuadas'] : true)));

        //Verifica existência da tela
        $visao_arquivo = './visao/meta_visao_tabs.php';
        if (!file_exists($visao_arquivo))
            return array(MENSAGEM_ERRO => 'Não foi possível carregar as tabs');

        //Carrega select de mapas
        $mapas = $this->carregarMapas(is_numeric($_SESSION['mapa']) && $_SESSION['mapa'] >= 0 && strlen($_SESSION['mapa']) <= 3 ? $_SESSION['mapa'] : null);
        if (is_array($mapas))
            return $mapas;

        //Carrega select de perfis
        $responsaveis = $this->carregarPerfis(is_numeric($_SESSION['responsavel']) && $_SESSION['responsavel'] > 1 && strlen($_SESSION['responsavel']) <= 3 ? $_SESSION['responsavel'] : null);
        if (is_array($responsaveis))
            return $responsaveis;

        //Carrega HTML
        ob_start();
        include_once $visao_arquivo;
        return ob_get_clean();
    }

    private function excluirAcao() {
        if (!isset($_GET['acao']))
            return false;

        //Verifica se o usuário tem alguma permissão de exclusão
        if (!is_numeric($this->login->permissao($this->modulo_arquivo, EXCLUIR)))
            return array(MENSAGEM_ERRO => 'Você não tem permissão para excluir');

        //Verifica URL
        $id = (is_numeric($_GET['acao']) && $_GET['acao'] > 0 && strlen($_GET['acao']) <= 9 ? $_GET['acao'] : 0);

        //Valida variável recebida
        if ($id == 0)
            return array(MENSAGEM_ERRO => 'Ação inválida');

        //Carrega o DAO, se ainda não carregou-o no objeto
        if (is_null($this->dao))
            $this->dao = new MetaDao($this->login, $this->banco);

        //Desativa a ação no banco de dados
        $retorno = $this->dao->excluirAcao($id);

        //Valida retorno
        if (is_string($retorno))
            return array(MENSAGEM_ERRO => $retorno);
        if ($retorno !== true)
            return array(MENSAGEM_ERRO => 'Não foi possível excluir a ação');

        return true;
    }

    private function excluirAnexo() {
        if (!isset($_GET['anexo']))
            return false;

        //Verifica se o usuário tem alguma permissão de exclusão
        if (!is_numeric($this->login->permissao($this->modulo_arquivo, EXCLUIR)))
            return array(MENSAGEM_ERRO => 'Você não tem permissão para excluir');

        //Verifica URL
        $id = (is_numeric($_GET['anexo']) && $_GET['anexo'] > 0 && strlen($_GET['anexo']) <= 9 ? $_GET['anexo'] : 0);

        //Valida variável recebida
        if ($id == 0)
            return array(MENSAGEM_ERRO => 'Anexo inválido');

        //Carrega o DAO, se ainda não carregou-o no objeto
        if (is_null($this->dao))
            $this->dao = new MetaDao($this->login, $this->banco);

        //Desativa o anexo no banco de dados
        $retorno = $this->dao->excluirAnexo($id);

        //Valida retorno
        if (is_string($retorno))
            return array(MENSAGEM_ERRO => $retorno);
        if ($retorno !== true)
            return array(MENSAGEM_ERRO => 'Não foi possível excluir o anexo');

        return true;
    }

    private function selecionarDadosFormulario($id = null) {
        //Se não salvou, carrega os dados informados, ignorando os dados do banco
        if (!is_null($this->registro) && is_object($this->registro) && get_class($this->registro) == "Meta")
            return $this->registro;

        //Carrega o DAO, se ainda não carregou-o no objeto
        if (is_null($this->dao))
            $this->dao = new MetaDao($this->login, $this->banco);

        if (isset($_GET['acompanhamentoRemover'])) {
            $this->dao->visibilidadeAcompanhamento(true, $_GET['acompanhamentoRemover'], $_SESSION['MAPA_LOGIN_USUARIO']);
        } else if (isset($_GET['acompanhamentoRestaurar'])) {
            $this->dao->visibilidadeAcompanhamento(null, $_GET['acompanhamentoRestaurar'], $_SESSION['MAPA_LOGIN_USUARIO']);
        }

        //Não havendo ID nem registro, cria objeto em branco para criar novo registro
        if (!is_numeric($id) || $id == 0 || strlen($id) > 5) {
            $registro = new Meta(0, 0, '', 0, '', '');
            if (is_object($registro) && get_class($registro) == 'Meta')
                return $registro;
            else
                return array(MENSAGEM_ERRO => 'Não foi possível carregar o registro');
        }

        //Verifica se o usuário tem permissão de visualização total, LISTAR pois o GP decidiu que todos podem ver todos
        if ($this->login->permissao($this->modulo_arquivo, LISTAR) < TODOS) {
            //Verifica se o usuário tem permissão para editar os próprios
            if ($this->login->permissao($this->modulo_arquivo, LISTAR) < PROPRIOS)
                return array(MENSAGEM_ERRO => 'Você não tem permissão para acessar o conteúdo');
            else {
                //Recebe registro ou erro ou false (se não tiver permissão)
                $registro = $this->dao->validarResponsavelId($id, $this->login->perfil_id);

                //Valida retorno
                if ($registro === false)
                    return array(MENSAGEM_ERRO => 'Você não tem permissão para acessar o conteúdo');
                //Se vier uma mensagem de erro (array) ou o objeto, ambos serão retornados direto para validação posterior
                return $registro;
            }
        } else {
            //Seleciona o registro
            $registros = $this->dao->selecionarMeta($id, false); //ATIVOS = FALSE, para mostrar o criador de qualquer registro
            //Verifica se encontrou algum retistro
            if (is_string($registros))
                return array(MENSAGEM_ERRO => $registros);
            if (!is_array($registros) || count($registros) != 1)
                return array(MENSAGEM_ERRO => 'Não foi possível carregar o registro');

            //Separa o registro
            $registro = $registros[0];

            return $registro;
        }
    }

    private function validarCampos() {
        //Verifica se recebeu o formulário
        if (!isset($_POST))
            return false;

        //Inicia lista de campos inválidos
        $campo_erro = array();

        //Inicia lista de ações
        $acoes = array();
        $manter = false; //Verifica se deve manter a ação, independente de ter sido preenchida (utilizada quando é administrador e deu erro em algum campo, pois não salva ação sem nome). Se não houver erro, não salvará igual as ações sem nome, mesmo mantidas
        //Verifica se o registro é novo
        $metaId = (isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 && strlen($_POST['id']) <= 5 ? $_POST['id'] : 0);

        //Percorre as ações criando objetos que são adicionados ao array $acoes
        if (isset($_POST['acao_id'])) {
            foreach ($_POST['acao_id'] as $key => $value) {
                //Campos para gerente
                if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS) {
                    //Verifica se alguma das metas já existentes está sem conteúdo
                    if (strlen($value) > 0 && is_numeric($value) && (!isset($_POST['acao_nome'][$key]) || strlen($_POST['acao_nome'][$key]) == 0) && !isset($_POST['acao_aprovada'][$key])
                    ) {
                        $campo_erro[] = 'acao';
                        $manter = true;
                    } else
                        $manter = false;
                }

                //Adiciona ação a lista de salvamento/re-carregamento
                if ((isset($_POST['acao_nome'][$key]) && strlen($_POST['acao_nome'][$key]) > 0) || $manter == true)
                    $acoes[] = new Acao(
                            $value,
                            $metaId,
                            isset($_POST['acao_nome'][$key]) ? $_POST['acao_nome'][$key] : '',
                            isset($_POST['acao_prazo'][$key]) ? $_POST['acao_prazo'][$key] : '',
                            isset($_POST['acao_concluida'][$key]) ? ($_POST['acao_concluida'][$key] == 'on' ? date('Y-m-d') : $_POST['acao_concluida'][$key]) : '',
                            isset($_POST['acao_concluida'][$key]) && $_POST['acao_concluida'][$key] == 'on' ? $this->login->id : 0,
                            isset($_POST['acao_monitorada'][$key]) && strlen($_POST['acao_monitorada'][$key]) > 0 ? $_POST['acao_monitorada'][$key] : (isset($_POST['acao_aprova'][$key]) ? date('Y-m-d') : ''),
                            0,
                            isset($_POST['acao_aprova'][$key]) ? $_POST['acao_aprova'][$key] : (isset($_POST['acao_aprovada'][$key]) ? $_POST['acao_aprovada'][$key] : false),
                            isset($_POST['acao_aprova'][$key]) ? date('Y-m-d') : '');
            }
        }

        //Carrega os campos de acompanhamento
        if (isset($_POST['acompanhamento-textarea']) && strlen($_POST['acompanhamento-textarea']) > 0) {
            //Carrega dados no objeto
            $acompanhamento = new Acompanhamento(0, $metaId, $_POST['acompanhamento-textarea'], isset($_POST['tipo']) && is_numeric($_POST['tipo']) && $_POST['tipo'] >= 0 && $_POST['tipo'] <= 3 ? $_POST['tipo'] : 0);
            //Conseguindo criar, passa objeto para dentro de um array, pois é assim que a classe Meta espera
            if (is_object($acompanhamento) && strlen($acompanhamento->texto) > 0)
                $acompanhamentos = array($acompanhamento);
            else
                $campo_erro[] = 'acompanhamento';
        }

        //Carrega os campos de anexo
        if (isset($_FILES['arquivo']) && is_array($_FILES['arquivo'])) {
            //Percorre lista de arquivos criando objeto para cada arquivo encontrado
            for ($a = 0; $a < count($_FILES['arquivo']['tmp_name']); $a++) {
                //Verifica se foi carregado algum arquivo
                if (strlen($_FILES['arquivo']['tmp_name'][$a]) == 0 && strlen($_FILES['arquivo']['name'][0]) == 0)
                    break;

                //Verifica erros
                if ($_FILES['arquivo']['error'][$a] != 0)
                    $campo_erro[] = 'anexo';
                if ($_FILES['arquivo']['size'][$a] > 20 * 1000 * 1000)
                    $campo_erro[] = 'anexo'; //20Mb





                    
//Se houver erros, remove arquivo para não guardar arquivo
                if (in_array('anexo', $campo_erro)) {
                    unset($_FILES['arquivo']);
                    unset($anexos);
                    break;
                } else {
                    //Carrega dados no objeto
                    $anexo = new Anexo(0, $metaId, substr($_FILES['arquivo']['name'][$a], 0, (strrpos($_FILES['arquivo']['name'][$a], '.'))), explode('.', $_FILES['arquivo']['name'][$a])[count(explode('.', $_FILES['arquivo']['name'][$a])) - 1], '', 0, '', 0, false, $_FILES['arquivo']['tmp_name'][$a]);
                    //Conseguindo criar, adiciona objeto a lista de anexos
                    if (is_object($anexo) && strlen($anexo->nome) > 0 && strlen($anexo->extensao) > 0)
                        $anexos[] = $anexo;
                    else {
                        //Se houver erros, remove arquivo para não guardar arquivo
                        $campo_erro[] = 'anexo';
                        unset($_FILES['arquivo']);
                        unset($anexos);
                        break;
                    }
                }
            }
        }

        //Carrega todos os campos do formulário validando-os
        $this->registro = new Meta(
                (isset($_POST['id']) ? $_POST['id'] : 0),
                (isset($_POST['numero']) ? $_POST['numero'] : 0),
                (isset($_POST['titulo']) ? $_POST['titulo'] : ''),
                (isset($_POST['responsavel']) ? $_POST['responsavel'] : ''),
                '',
                (isset($_POST['responsavel_pessoa']) ? $_POST['responsavel_pessoa'] : ''),
                '', 0, '', 0, false,
                isset($acompanhamentos) ? $acompanhamentos : null,
                isset($anexos) ? $anexos : null,
                0, 0, 0, 0, 0,
                (isset($_POST['data_inicial']) ? $_POST['data_inicial'] : ''),
                (isset($_POST['data_final']) ? $_POST['data_final'] : ''),
                (isset($_POST['data_conclusao']) ? $_POST['data_conclusao'] : ''),
                null,
                isset($acoes) ? $acoes : null,
                (isset($_POST['indicador_objetivo']) ? $_POST['indicador_objetivo'] : ''),
                (isset($_POST['indicador_ods']) ? $_POST['indicador_ods'] : ''),
                (isset($_POST['indicador_titulo']) ? $_POST['indicador_titulo'] : ''),
                (isset($_POST['indicador_referencia']) && strlen($_POST['indicador_referencia']) > 0 ? $_POST['indicador_referencia'] : 0),
                (isset($_POST['indicador_indicador']) && strlen($_POST['indicador_indicador']) > 0 ? $_POST['indicador_indicador'] : 0),
                (isset($_POST['indicador_unidade']) ? $_POST['indicador_unidade'] : ''),
                ($this->login->permissao($this->modulo_arquivo, EDITAR) < TODOS && isset($_POST['indicador_alcancado']) && strlen($_POST['indicador_alcancado']) > 0 ? $_POST['indicador_alcancado'] : 0),
                '',
                ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS && isset($_POST['indicador_alcancado']) && strlen($_POST['indicador_alcancado']) > 0 ? $_POST['indicador_alcancado'] : 0),
                '',
                (isset($_POST['indicador_alcancado_anterior']) ? $_POST['indicador_alcancado_anterior'] : 0),
                (isset($_POST['manter_monitoria']) ? $_POST['manter_monitoria'] : 0));

        //Verifica se os campos obrigatórios foram preenchidos
        //Não tendo permissão de cadastro nem de edição total, estes campos se tornam opcionais
        if ((is_numeric($this->login->permissao($this->modulo_arquivo, CADASTRAR)) && $this->registro->id == 0) || ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS && $this->registro->id != 0)) {
            if (strlen($this->registro->titulo) == 0)
                $campo_erro[] = 'titulo';
            if ($this->registro->responsavelId == 0)
                $campo_erro[] = 'responsavel';
            if ($this->registro->dataInicial == 0 || $this->registro->dataFinal == 0)
                $campo_erro[] = 'prazo';
            if (!in_array('prazo', $campo_erro)) {
                //Verifica se a data inicial é maior que a data final
                $diff = (new DateTime($this->registro->dataInicial))->diff(new DateTime($this->registro->dataFinal));
                if ($diff->invert == 1)
                    $campo_erro[] = 'prazo';
            }
        }

        //Se houve erros, envia a lista de erros
        //Senão, retorna TRUE para informar que não há erros, o registro é atualizado diretamente no atributo do objeto
        if (count($campo_erro) > 0)
            return $campo_erro;
        else
            return true;
    }

}

?>
