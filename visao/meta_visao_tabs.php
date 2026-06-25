<?php
/*
 * @package    SiMPLEs - Sistema Municipal de Portarias Lavradas em Esteio.
 *
 * @copyright  Copyright (C) 2015 Karine Bender, Todos os direitos reservados.
 * @license    GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;
//Define data de atualização e limites para exibição
define('atualizacaoArquivo', 'atualizacao.txt');
define('dataUltimaAtualizacao', file_exists(atualizacaoArquivo) ? date('Y-m-d', filemtime(atualizacaoArquivo)) : date('Y-m-d', 1));
define('diasDestaque', 7);
define('diasVisivel', 10);
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
?>

<ul id="abas" class="nav nav-tabs">
    <li role="presentation" class="active" data-toggle="tab" onclick="aba(this)"><a href="#formularioPesquisa"><span class="glyphicon glyphicon-search"></span> Pesquisar</a></li>
    <?php if (((strtotime(date('Y-m-d', time())) - strtotime(dataUltimaAtualizacao)) / 86400) < diasVisivel) { ?><li role="presentation" class="" data-toggle="tab" onclick="aba(this)"><a href="#atualizacoes"<?php echo (((strtotime(date('Y-m-d', time())) - strtotime(dataUltimaAtualizacao)) / 86400) >= diasDestaque ? ' style="color: #ccc;"' : ''); ?>><span class="glyphicon glyphicon-tag"></span> O que há de novo?</a></li><?php } ?>
</ul>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 well tab-content">

    <form class="tab-pane active" id="formularioPesquisa" method="get" name="formularioPesquisa">
        <input id="mod" name="mod" type="hidden" value="meta">

        <div class="row">
            <div class="col-xs-12 col-sm-2 col-md-1">
                <label>Número:</label>
                <input class="form-control input-sm" id="numero" maxlength="4" name="numero" onkeypress="return apenasNumeros();" type="text" value="<?php echo(isset($_SESSION['numero']) ? $_SESSION['numero'] : ''); ?>" />
            </div>
            <div class="col-xs-12 col-sm-4 col-md-3">
                <label>Indicador ODS:</label>
                <select class="form-control" id="ods" name="ods">
                    <option value="">Todos</option>
                    <?php foreach ($objetivos as $valor => $info): ?>
                        <option value="<?= $valor ?>"<?= isset($_SESSION['ods']) && $_SESSION['ods'] == $valor ? ' selected' : ''; ?> title="<?= $info['descricao'] ?>">
                            <?= $valor ?>: <?= $info['nome'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-xs-7 col-sm-2 col-md-2 col-lg-5">
                <label>Digite sua pesquisa:</label>
                <input class="form-control input-sm" id="pesquisa" maxlength="200" name="pesquisa" type="text" value="<?php echo(isset($_SESSION['pesquisa']) ? $_SESSION['pesquisa'] : ''); ?>" />
            </div>
            <div class="col-xs-12 col-sm-4 col-lg-3">
                <label>Pesquisar em:</label><br>
                <select class="form-control" id="campo" name="campo">
                    <option value="0">títulos</option>
                    <option value="1"<?php echo isset($_SESSION['campo']) && $_SESSION['campo'] == 1 ? ' selected' : ''; ?>>acompanhamento</option>
                    <option value="2"<?php echo isset($_SESSION['campo']) && $_SESSION['campo'] == 2 ? ' selected' : ''; ?>>títulos e acompanhamento</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-2">
                <label class="control-label">Mapa:</label>
                <?php echo isset($mapas) ? $mapas : ''; ?>
            </div>
            <div class="col-xs-12 col-sm-3">
                <label class="control-label">Responsável:</label>
                <?php echo isset($responsaveis) ? $responsaveis : ''; ?>
            </div>
            <div class="col-xs-12 col-sm-3 col-md-4">
                <label class="control-label">&nbsp;</label>
                <input class="form-control" id="responsavel_pessoa" maxlength="40" name="responsavel_pessoa" placeholder="Pessoa responsável" type="text" value="<?php echo (isset($_SESSION['responsavelPessoa']) ? $_SESSION['responsavelPessoa'] : '') ?>">
            </div>
            <div class="col-xs-12 col-sm-4 col-md-3">
                <label>Situação:</label><br>
                <select class="form-control" id="situacao" name="situacao">
                    <option value="0">todas</option>
                    <option value="1"<?php echo isset($_SESSION['situacao']) && $_SESSION['situacao'] == 1 ? ' selected' : ''; ?>>no prazo</option>
                    <option value="2"<?php echo isset($_SESSION['situacao']) && $_SESSION['situacao'] == 2 ? ' selected' : ''; ?>>menos de 15 dias do prazo</option>
                    <option value="3"<?php echo isset($_SESSION['situacao']) && $_SESSION['situacao'] == 3 ? ' selected' : ''; ?>>em atraso</option>
                    <option value="4"<?php echo isset($_SESSION['situacao']) && $_SESSION['situacao'] == 4 ? ' selected' : ''; ?>>concluídas</option>
                    <option value="5"<?php echo isset($_SESSION['situacao']) && $_SESSION['situacao'] == 5 ? ' selected' : ''; ?>>em monitoria continuada</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input class="btn btn-primary" id="pesquisar" type="button" value="Pesquisar" onclick="enviarPesquisa();" />
                <label class="control-label pull-right"><input <?php echo isset($_SESSION['metas_continuadas']) && $_SESSION['metas_continuadas'] == false ? '' : 'checked'; ?> id="metas_continuadas" name="metas_continuadas" type="checkbox" value="1"> Exibir todas as metas em monitoria continuada</label>
            </div>
        </div>
    </form>

    <div class="tab-pane" id="atualizacoes">
        <ul>
            <?php
            //Existindo o arquivo de atualização, abre-o carregando uma linha em cada LI
            if (file_exists(atualizacaoArquivo)) {
                $arquivo = fopen(atualizacaoArquivo, "r");
                while (strlen($linha = fgets($arquivo)) > 0) {
                    ?>
                    <li><?php echo $linha; ?></li>
                    <?php
                }
                fclose($arquivo);
            }
            ?>
        </ul>
    </div>
</div>