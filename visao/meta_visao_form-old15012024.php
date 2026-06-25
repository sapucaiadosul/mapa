<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

//Verifica existência de variável necessária para carregamento do formulário
if (isset($registro) && get_class($registro) == 'Meta') {

    $objetivos = [
        1 => [
            'nome' => 'Erradicação da Pobreza',
            'descricao' => 'Erradicar a pobreza em todas as formas e em todos os lugares'
        ],
        2 => [
            'nome' => 'Fome Zero e Agricultura Sustentável',
            'descricao' => 'Erradicar a fome, alcançar a segurança alimentar, melhorar a nutrição e promover a agricultura sustentável'
        ],
        3 => [
            'nome' => 'Saúde e Bem-Estar',
            'descricao' => 'Garantir o acesso à saúde de qualidade e promover o bem-estar para todos, em todas as idades'
        ],
        4 => [
            'nome' => 'Educação de Qualidade',
            'descricao' => 'Garantir o acesso à educação inclusiva, de qualidade e equitativa, e promover oportunidades de aprendizagem ao longo da vida para todos'
        ],
        5 => [
            'nome' => 'Igualdade de Gênero',
            'descricao' => 'Alcançar a igualdade de gênero e empoderar todas as mulheres e meninas'
        ],
        6 => [
            'nome' => 'Água Potável e Saneamento',
            'descricao' => 'Garantir a disponibilidade e a gestão sustentável da água potável e do saneamento para todos'
        ],
        7 => [
            'nome' => 'Energia Limpa e Acessível',
            'descricao' => 'Garantir o acesso a fontes de energia fiáveis, sustentáveis e modernas para todos'
        ],
        8 => [
            'nome' => 'Trabalho Decente e Crescimento Econômico',
            'descricao' => 'Promover o crescimento econômico inclusivo e sustentável, o emprego pleno e produtivo e o trabalho digno para todos'
        ],
        9 => [
            'nome' => 'Indústria, Inovação e Infraestrutura',
            'descricao' => 'Construir infraestruturas resilientes, promover a industrialização inclusiva e sustentável e fomentar a inovação'
        ],
        10 => [
            'nome' => 'Redução das Desigualdades',
            'descricao' => 'Reduzir as desigualdades no interior dos países e entre países'
        ],
        11 => [
            'nome' => 'Cidades e Comunidades Sustentáveis',
            'descricao' => 'Tornar as cidades e comunidades mais inclusivas, seguras, resilientes e sustentáveis'
        ],
        12 => [
            'nome' => 'Consumo e Produção Responsáveis',
            'descricao' => 'Garantir padrões de consumo e de produção sustentáveis'
        ],
        13 => [
            'nome' => 'Ação Contra a Mudança Global do Clima',
            'descricao' => 'Adotar medidas urgentes para combater as alterações climáticas e os seus impactos'
        ],
        14 => [
            'nome' => 'Vida na Água',
            'descricao' => 'Conservar e usar de forma sustentável os oceanos, mares e os recursos marinhos para o desenvolvimento sustentável'
        ],
        15 => [
            'nome' => 'Vida Terrestre',
            'descricao' => 'Proteger, restaurar e promover o uso sustentável dos ecossistemas terrestres, gerir de forma sustentável as florestas, combater a desertificação, travar e reverter a degradação dos solos e travar a perda da biodiversidade'
        ],
        16 => [
            'nome' => 'Paz, Justiça e Instituições Eficazes',
            'descricao' => 'Promover sociedades pacíficas e inclusivas para o desenvolvimento sustentável, proporcionar o acesso à justiça para todos e construir instituições eficazes, responsáveis e inclusivas a todos os níveis'
        ],
        17 => [
            'nome' => 'Parcerias e Meios de Implementação',
            'descricao' => 'Reforçar os meios de implementação e revitalizar a parceria global para o desenvolvimento sustentável'
        ]
    ];

    $valorSelecionado = $registro->indOds;
    ?>
    <div class="col-xs-12">
        <form class="meta" enctype="multipart/form-data" id="f_form" method="post" onsubmit="<?php echo (($this->login->permissao($this->modulo_arquivo, EDITAR) < TODOS && !$this->permitirEdicao(isset($_GET['id']) ? $_GET['id'] : 0)) || $this->registro->desativado == true ? "return false" : "formulario_enviar();"); ?>">
            <input id="id" name="id" type="hidden" value="<?php echo $registro->id; ?>">
            <fieldset class="col-xs-12 col-md-6 col-lg-7">
                <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS && $this->registro->desativado == false) { ?>
                    <div class="form-group">
                        <label class="control-label" for="numero">Número da meta</label>
                        <input class="form-control" id="numero" maxlength="4" name="numero" onkeypress="return apenasNumeros()" type="text" value="<?php echo $this->registro->numero; ?>">
                    </div>
                <?php } ?>
                <div class="form-group<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && in_array('titulo', $GLOBALS['campo_erro']) ? ' has-error' : ''); ?>">
                    <label class="control-label" for="titulo">Título da meta</label>
                    <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS && $this->registro->desativado == false) { ?>
                        <input autofocus class="form-control" id="titulo" maxlength="200" name="titulo" onkeypress="input_destaque_remover(this)" type="text" value="<?php echo $registro->titulo; ?>">
                    <?php } else { ?>
                        <p><?php echo $registro->numero . ' ' . $registro->titulo; ?></p>
                    <?php } ?>
                </div>
                <div class="form-group<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && in_array('titulo', $GLOBALS['campo_erro']) ? ' has-error' : ''); ?>">
                    <label class="control-label" for="indicador_objetivo">Objetivo do indicador</label>
                    <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS && $this->registro->desativado == false) { ?>
                        <input class="form-control" id="indicador_objetivo" maxlength="300" name="indicador_objetivo" onkeypress="input_destaque_remover(this)" type="text" value="<?php echo $registro->indObjetivo; ?>">
                    <?php } else { ?>
                        <p><?php echo $registro->indObjetivo; ?></p>
                    <?php } ?>
                </div>
                <?php if (($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS || $this->permitirEdicao(isset($_GET['id']) ? $_GET['id'] : 0)) && $this->registro->desativado == false) { ?>
                    <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS && $this->registro->desativado == false) { ?>
                        <div class="form-group col-xs-12 col-md-6 col-lg-4<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && in_array('acompanhamento', $GLOBALS['campo_erro']) ? ' has-error' : ''); ?>" style="padding: 0px;">
                            <label class="control-label" for="tipo">Tipo de acompanhamento</label>
                            <select class="form-control" id="tipo" name="tipo" onchange="selecionarTipo(this.value)">
                                <option value="0">Observação</option>
                                <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS): ?>
                                    <option value="3">Monitoramento</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-md-7 col-lg-6" style="padding-left: 20px;">
                            <label class="control-label" for="indicador_ods">Indicador ODS</label>
                            <select class="form-control" id="indicador_ods" name="indicador_ods">
                                <option value="0">Selecione o Objetivo</option>
                                <?php foreach ($objetivos as $valor => $objetivo): ?>
                                    <?php
                                    $selecionado = ($valor == $valorSelecionado) ? 'selected' : '';
                                    ?>
                                    <option value="<?= $valor ?>" title="<?= $objetivo['descricao'] ?>" <?= $selecionado ?>>
                                        <?= $valor ?>: <?= $objetivo['nome'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php } ?>
                    <div style="clear: both;">
                        <div class="text-toolbar alert-default" role="toolbar">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default" title="Alinhar à esquerda" onclick="formatar('justifyLeft');">
                                    <span class="glyphicon glyphicon-align-left"></span>
                                </button>
                                <button type="button" class="btn btn-default" title="Centralizar" onclick="formatar('justifyCenter');">
                                    <span class="glyphicon glyphicon-align-center"></span>
                                </button>
                                <button type="button" class="btn btn-default" title="Alinhar à direita" onclick="formatar('justifyRight');">
                                    <span class="glyphicon glyphicon-align-right"></span>
                                </button>
                                <button type="button" class="btn btn-default" title="Justificar" onclick="formatar('justifyFull');">
                                    <span class="glyphicon glyphicon-align-justify"></span>
                                </button>
                                <button type="button" class="btn btn-default" title="Diminuir recuo" onclick="formatar('outdent');">
                                    <span class="glyphicon glyphicon-indent-right"></span>
                                </button>
                                <button type="button" class="btn btn-default" title="Aumentar recuo" onclick="formatar('indent');">
                                    <span class="glyphicon glyphicon-indent-left"></span>
                                </button>
                                <button type="button" class="btn btn-default negrito" title="Negrito" onclick="formatar('bold');" >
                                    <span class="glyphicon glyphicon-bold"></span>
                                </button>
                                <button type="button" class="btn btn-default" title="Itálico" onclick="formatar('italic');">
                                    <span class="glyphicon glyphicon-italic"></span>
                                </button>
                                <button type="button" class="btn btn-default" title="Sublinhado" onclick="formatar('underline');" >
                                    <span class="glyphicon glyphicon-text-color"></span>
                                </button>
                                <button type="button" class="btn btn-default" title="Adicionar marcadores" onclick="formatar('insertUnorderedList');">
                                    <span class="glyphicon glyphicon-sort-by-attributes"></span>
                                </button>
                                <button type="button" class="btn btn-default" title="Adicionar numeração" onclick="formatar('insertOrderedList');">
                                    <span class="glyphicon glyphicon-sort-by-order"></span>
                                </button>
                                <button type="button" class="btn btn-default" title="Remover formatação" onclick="formatar('removeFormat');">
                                    <span class="glyphicon glyphicon-ban-circle"></span>
                                </button>
                            </div>
                        </div>
                        <div contenteditable="true" id="acompanhamento" class="form-control" onkeypress="input_destaque_remover(this);"></div>
                    </div>
                    <div class="form-group">
                        <a class="btn btn-primary" id="acompanhar" onclick="formulario_enviar(this);">Adicionar acompanhamento</a>
                        <textarea id="acompanhamento-textarea" name="acompanhamento-textarea"></textarea>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <label class="control-label" for="acompanhamentos">Acompanhamentos</label>

                    <div class="" id="acompanhamentos">
                        <?php
                        if (is_array($registro->acompanhamento)):
                            foreach ($registro->acompanhamento as $acompanhamento):
                                ?>
                                <div class="<?php echo $registro->tipoClass($acompanhamento->tipo); ?>">

                                    <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS) { ?>
                                        <a
                                            title="<?php echo ($acompanhamento->desativado) ? 'Restaurar o acompanhamento' : 'Ocultar o acompanhamento'; ?>"
                                            href="?mod=meta&formulario&id=<?php echo $_GET['id'] . '&acompanhamento' . (($acompanhamento->desativado) ? 'Restaurar' : 'Remover') . '=' . $acompanhamento->id; ?>"><span style="float: right" class="glyphicon <?php echo ($acompanhamento->desativado) ? 'glyphicon-repeat' : 'glyphicon-remove'; ?> "></span></a>
                                        <?php } ?>

                                    <?php if ($acompanhamento->desativado) { ?>
                                        <small><i>
                                                <?php echo 'Removido por ' . $acompanhamento->modificador_nome . ' - ' . (new DateTime($acompanhamento->modificado))->format('d/m/Y'); ?>
                                            </i></small>

                                    <?php } else { ?>

                                        <p><strong><?php echo $acompanhamento->dataHora; ?> - <?php echo $acompanhamento->usuarioNome; ?> - <?php echo $acompanhamento->usuarioPerfil; ?></strong> </p>

                                        <div>
                                            <?php echo $acompanhamento->texto; ?>
                                        </div>

                                    <?php } ?>
                                </div>
                                <?php
                            endforeach;
                        else:
                            echo'Nenhum acompanhamento encontrado';
                        endif;
                        ?>
                    </div>

                </div>
            </fieldset>
            <fieldset class="col-xs-12 col-md-6 col-lg-5">
                <div class="form-group<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && (in_array('responsavel', $GLOBALS['campo_erro'])) ? ' has-error' : ''); ?>">
                    <label class="control-label" for="responsavel">Responsável pela meta</label>
                    <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS && $this->registro->desativado == false): echo isset($responsaveis) ? $responsaveis : ''; ?>
                        <input class="form-control" id="responsavel_pessoa" maxlength="40" name="responsavel_pessoa" placeholder="Digite o nome da pessoa responsável" type="text" value="<?php echo $this->registro->responsavelPessoa; ?>">
                    <?php else: ?>
                        <div><?php echo $registro->responsavelPessoa . (strlen($registro->responsavelPessoa) > 0 ? ' - ' : '') . $registro->responsavelNome; ?></div>
                        <?php if ($this->permitirEdicao(isset($_GET['id']) ? $_GET['id'] : 0)): ?>
                            <input id="responsavel" name="responsavel" type="hidden" value="<?php echo $this->registro->responsavelId; ?>">
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="form-group<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && (in_array('prazo', $GLOBALS['campo_erro'])) ? ' has-error' : ''); ?>">
                    <label class="control-label" for="prazo">Prazo da meta</label>
                    <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS && $this->registro->desativado == false): ?>
                        <div>
                            <input class="form-control" id="data_inicial" name="data_inicial" type="date" value="<?php echo (strlen($registro->dataInicial) > 0 ? $registro->dataInicial : date('Y-01-01')); ?>">
                            <span class="text-center date-union">a</span>
                            <input class="form-control" id="data_final" name="data_final" type="date" value="<?php echo (strlen($registro->dataFinal) > 0 ? $registro->dataFinal : date('Y-12-31')); ?>">
                        </div>
                    <?php else: ?>
                        <div><?php echo $registro->dataInicialFormatada . ' a ' . $registro->dataFinalFormatada; ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && (in_array('conclusao', $GLOBALS['campo_erro'])) ? ' has-error' : ''); ?>">
                    <label class="control-label" for="data_conclusao">Meta concluida em: </label>
                    <?php if (($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS || $this->permitirEdicao(isset($_GET['id']) ? $_GET['id'] : 0)) && $this->registro->desativado == false): ?>
                        <input class="form-control" id="data_conclusao" name="data_conclusao" type="date" value="<?php echo $registro->dataConclusao; ?>">
                    <?php else: ?>
                        <span><?php echo (strlen($registro->dataConclusaoFormatada) > 0 ? $registro->dataConclusaoFormatada : ' meta não finalizada'); ?>.</span>
                    <?php endif; ?>
                    <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS): ?>
                        <label class="btn <?php echo $this->registro->manterMonitoria === true ? ' btn-warning active' : 'btn-default' ?>"><input <?php echo $this->registro->manterMonitoria === true ? 'checked' : '' ?> id="manter_monitoria" name="manter_monitoria" onchange="alterarBotaoMonitoriaContinuada(this)" type="checkbox"> Manter monitoria</label>
                    <?php else: ?>
                        <?php echo ($this->registro->manterMonitoria === true ? '<span class="btn btn-warning active"> Em monitoria continuada</span>' : ''); ?>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="control-label">Indicador da Meta</label><br>
                    <?php if (($this->login->permissao($this->modulo_arquivo, EDITAR) < TODOS && $this->registro->indIndicador > 0) || $this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS) echo $this->carregarBarraIndicador($this->registro, true); ?>
                    <div class="indicador">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <label class="control-label">Titulo do indicador </label>
                            </span>
                            <input class="form-control"  id="indicador_titulo" maxlength="200" name="indicador_titulo"<?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) < TODOS) echo 'readonly'; ?> type="text" value="<?php echo $this->registro->indTitulo; ?>">
                        </div>
                        <?php if (($this->login->permissao($this->modulo_arquivo, EDITAR) < TODOS && $this->registro->indIndicador > 0) || $this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS) { ?>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <label class="control-label">Valor Referência</label>
                                </span>
                                <input class="form-control" id="indicador_referencia" maxlength="14" name="indicador_referencia"<?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) < TODOS) echo 'readonly'; ?> onchange="atualizarIndicador(this.id)" onkeypress="return apenasNumeros('.,')" type="text" value="<?php echo $this->registro->indValorReferencia; ?>">
                                <input id="indicador_referencia_anterior" name="indicador_referencia_anterior" type="hidden" value="<?php echo $this->registro->indValorReferencia; ?>">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <label class="control-label">Valor Indicado</label>
                                </span>
                                <input class="form-control" id="indicador_indicador" maxlength="14" name="indicador_indicador"<?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) < TODOS) echo 'readonly'; ?> onchange="atualizarIndicador(this.id)" onkeypress="return apenasNumeros('.,')" type="text" value="<?php echo $this->registro->indValorIndicador; ?>">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <label class=" control-label">Unidade de medida</label>
                                </span>
                                <input class="form-control" id="indicador_unidade" maxlength="40" name="indicador_unidade"<?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) < TODOS) echo 'readonly'; ?> type="text" value="<?php echo $this->registro->indUnidade; ?>">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <label class=" control-label">Valor alcançado</label>
                                </span>
                                <input class="form-control" id="indicador_alcancado" maxlength="14" name="indicador_alcancado" onchange="atualizarIndicador(this.id)" onkeypress="return apenasNumeros('.,')" type="text" value="<?php echo $this->registro->indValorAtual; ?>">
                                <input id="indicador_alcancado_anterior" name="indicador_alcancado_anterior" type="hidden" value="<?php echo $this->registro->indValorAtual; ?>">
                                <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS) { ?>
                                    <span class="input-group-addon">
                                        <input id="indicador_alcancado_aprovado" name="indicador_alcancado_aprovado" onclick="atualizarIndicador(this.id)" type="checkbox">
                                    </span>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="form-group<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && in_array('acao', $GLOBALS['campo_erro']) ? ' has-error' : ''); ?>">
                    <label class="control-label" for="acoes">Ações</label>
                    <?php echo $this->carregarBarraAcao($registro->acaoTotais, true); ?>
                    <div class="acoes table-responsive">
                        <table class="table table-condensed table-hover table-striped">
                            <tbody>
                                <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS): ?>
                                    <tr class="acao-class">
                                <input name="acao_id[]" type="hidden" value="">
                                <td class="text-center">
                                    <input class="form-control" name="acao_prazo[]" type="date"></input>
                                </td>
                                <td>
                                    <input class="form-control" maxlength="200" name="acao_nome[]" type="text" value=""></input>
                                </td>
                                <td></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (is_array($registro->acao)): foreach ($registro->acao as $acao): ?>
                                    <tr class="<?php echo ($acao->aprovada ? 'success' : (strlen($acao->monitorada) > 0 ? 'danger' : (strlen($acao->concluida) > 0 ? 'warning' : ''))); ?>">
                                        <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS || $this->permitirEdicao(isset($_GET['id']) ? $_GET['id'] : 0)): ?>
                                        <input name="acao_id[<?php echo $acao->idString; ?>]" type="hidden" value="<?php echo $acao->id; ?>">
                                        <input name="acao_monitorada[<?php echo $acao->idString; ?>]" type="hidden" value="<?php echo $acao->monitorada; ?>">
                                    <?php endif; ?>
                                    <?php if ($this->permitirEdicao(isset($_GET['id']) ? $_GET['id'] : 0)): ?>
                                        <td class="text-center">
                                            <?php if ($acao->aprovada || (strlen($acao->concluida) > 0 && strlen($acao->monitorada) == 0)): ?>
                                                <input checked disabled="disabled" title="Ação concluída" type="checkbox">
                                                <input name="acao_concluida[<?php echo $acao->idString ?>]" type="hidden" value="<?php echo $acao->concluida; ?>">
                                            <?php else: ?>
                                                <input name="acao_concluida[<?php echo $acao->idString ?>]" onchange="concluirAcao(this)" title="Concluir ação" type="checkbox">
                                            <?php endif; ?>
                                        </td>
                                    <?php elseif ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS && strlen($acao->concluida) > 0): ?>
                                        <input name="acao_concluida[<?php echo $acao->idString ?>]" type="hidden" value="<?php echo $acao->concluida; ?>">
                                    <?php endif; ?>
                                    <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS && in_array($_SESSION['MAPA_LOGIN_USUARIO'], array('01708146024', '01040484093', '00380944014')) && $acao->aprovada == false && $this->registro->desativado == false): ?>
                                        <td class="text-center">
                                            <input class="form-control" name="acao_prazo[<?php echo $acao->idString; ?>]" type="date" value="<?php echo $acao->prazo; ?>"></input>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-control" maxlength="200" name="acao_nome[<?php echo $acao->idString; ?>]" type="text" value="<?php echo $acao->nome; ?>"></input>
                                        </td>
                                    <?php else: ?>
                                        <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS || $this->permitirEdicao(isset($_GET['id']) ? $_GET['id'] : 0)): ?>
                                            <input class="form-control" maxlength="200" name="acao_nome[<?php echo $acao->idString; ?>]" type="hidden" value="<?php echo $acao->nome; ?>"></input>
                                            <input class="form-control" name="acao_prazo[<?php echo $acao->idString; ?>]" type="hidden" value="<?php echo $acao->prazo; ?>"></input>
                                        <?php endif; ?>
                                        <td class="text-center">
                                            <span><?php echo $acao->prazoFormatada; ?></span>
                                        </td>
                                        <td>
                                            <span><?php echo $acao->nome; ?></span>
                                        </td>
                                    <?php endif; ?>
                                    </td>
                                    <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS && $this->registro->desativado == false): ?>
                                        <td>
                                            <div data-toggle="buttons">
                                                <label class="btn btn-success btn-acao-aprova <?php echo ($acao->aprovada == true ? 'active' : ''); ?> <?php echo ($acao->aprovada == true || strlen($acao->concluida) == 0 ? 'disabled' : ''); ?>" onclick="aprovarAcao(this)" title="Aprovar">
                                                    <input <?php echo ($acao->aprovada == true ? 'checked="checked"' : ''); ?> name="acao_aprova[<?php echo $acao->idString ?>]" type="radio" value="1"><span class="glyphicon glyphicon-thumbs-up"></span>
                                                </label>
                                                <label class="btn btn-danger btn-acao-aprova <?php echo ($acao->aprovada == false && strlen($acao->concluida) > 0 && strlen($acao->monitorada) > 0 ? 'active' : ''); ?> <?php echo ($acao->aprovada == true || strlen($acao->concluida) == 0 ? 'disabled' : ''); ?>" onclick="aprovarAcao(this)" title="Reprovar">
                                                    <input <?php echo ($acao->aprovada == false && strlen($acao->concluida) > 0 && strlen($acao->monitorada) > 0 ? 'checked="checked"' : ''); ?> name="acao_aprova[<?php echo $acao->idString ?>]" type="radio" value="0"><span class="glyphicon glyphicon-thumbs-down"></span>
                                                </label>
                                            </div>
                                        </td>
                                    <?php elseif ($this->permitirEdicao(isset($_GET['id']) ? $_GET['id'] : 0)): ?>
                                        <input name="acao_aprovada[<?php echo $acao->idString; ?>]" type="hidden" value="<?php echo $acao->aprovada; ?>">
                                    <?php endif; ?>
                                    <?php if ($this->login->permissao($this->modulo_arquivo, EXCLUIR) >= TODOS && $this->registro->desativado == false): ?>
                                        <td>
                                            <a class="btn btn-danger" href="javascript:;" onclick="excluirAcao(<?php echo $acao->id; ?>, '<?php echo $acao->nome; ?>');" title="Remover"><span class="glyphicon glyphicon-remove"></span></a>
                                        </td>
                                    <?php endif; ?>
                                    </tr>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS): ?>
                        <div>
                            <a class="btn btn-primary" id="adicionarAcao" onclick="adicionarAcao()">Adicionar ação</a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="control-label" for="anexo">Anexos</label>
                    <div class="anexos table-responsive">
                        <table class="table table-bordered table-condensed table-hover table-striped">
                            <?php
                            if (is_array($registro->anexo)):
                                foreach ($registro->anexo as $anexo):
                                    ?>
                                    <tr>
                                        <td class="text-center"><a class="" href="javascript:;" title="Remover arquivo"><a href="index.php?mod=meta&anexo=<?php echo $anexo->id; ?>" target="_blank" title="Baixar o arquivo"><?php echo date('d/m/Y', strtotime($anexo->criado)); ?></a></td>
                                        <td><a href="index.php?mod=meta&anexo=<?php echo $anexo->id; ?>" target="_blank" title="Baixar o arquivo"><span class="glyphicon glyphicon-file"></span> <?php echo $anexo->nome; ?></a></td>
                                        <?php if ($this->login->permissao($this->modulo_arquivo, EXCLUIR) >= TODOS && $this->registro->desativado == false) { ?>
                                            <td class="text-center"><a class="glyphicon glyphicon-remove text-danger" href="javascript:;" onclick="excluirAnexo(<?php echo $anexo->id; ?>, '<?php echo $anexo->nome; ?>')" title="Remover arquivo"></a></td>
                                        <?php } ?>
                                    </tr>
                                    <?php
                                endforeach;
                            else: echo'Nenhum anexo encontrado';
                            endif;
                            ?>
                        </table>
                    </div>
                    <?php if (($this->login->permissao($this->modulo_arquivo, EDITAR) >= TODOS || $this->permitirEdicao(isset($_GET['id']) ? $_GET['id'] : 0)) && $this->registro->desativado == false) { ?>
                        <div class="form-group text-center<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && in_array('anexo', $GLOBALS['campo_erro']) ? ' has-error' : ''); ?>" id="anexo">
                            <input class="form-control" id="arquivo" multiple name="arquivo[]" onchange="input_destaque_remover(this);" type="file">
                            <a class="btn btn-primary" id="anexar" onclick="formulario_enviar(this);">Anexar</a>
                        </div>
                    <?php } ?>
                </div>
            </fieldset>
        </form>
    </div>
<?php } ?>
