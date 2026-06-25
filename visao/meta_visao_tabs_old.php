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
define('dataUltimaAtualizacao', file_exists(atualizacaoArquivo) ? date('Y-m-d',filemtime(atualizacaoArquivo)) : date('Y-m-d', 1));
define('diasDestaque', 7);
define('diasVisivel', 10);
?>

<ul id="abas" class="nav nav-tabs">
    <li role="presentation" class="active" data-toggle="tab" onclick="aba(this)"><a href="#formularioPesquisa"><span class="glyphicon glyphicon-search"></span> Pesquisar</a></li>
	<?php if(((strtotime(date('Y-m-d', time())) - strtotime(dataUltimaAtualizacao)) / 86400) < diasVisivel){?><li role="presentation" class="" data-toggle="tab" onclick="aba(this)"><a href="#atualizacoes"<?php echo (((strtotime(date('Y-m-d', time())) - strtotime(dataUltimaAtualizacao)) / 86400) >= diasDestaque ? ' style="color: #ccc;"' : '');?>><span class="glyphicon glyphicon-tag"></span> O que há de novo?</a></li><?php }?>
</ul>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 well tab-content">

    <form class="tab-pane active" id="formularioPesquisa" method="get" name="formularioPesquisa">
		<input id="mod" name="mod" type="hidden" value="meta">

            <div class="row">
			<div class="col-xs-12 col-sm-2 col-md-1">
                    <label>Número:</label>
                    <input class="form-control input-sm" id="numero" maxlength="4" name="numero" onkeypress="return apenasNumeros();" type="text" value="<?php echo(isset($_SESSION['numero'])?$_SESSION['numero']:''); ?>" />
                </div>
				<div class="col-xs-12 col-sm-6 col-md-7 col-lg-8">
                    <label>Digite sua pesquisa:</label>
                    <input class="form-control input-sm" id="pesquisa" maxlength="200" name="pesquisa" type="text" value="<?php echo(isset($_SESSION['pesquisa'])?$_SESSION['pesquisa']:''); ?>" />
                </div>
                <div class="col-xs-12 col-sm-4 col-lg-3">
                    <label>Pesquisar em:</label><br>
                    <select class="form-control" id="campo" name="campo">
						<option value="0">títulos</option>
						<option value="1"<?php echo isset($_SESSION['campo']) && $_SESSION['campo'] == 1?' selected':'';?>>acompanhamento</option>
						<option value="2"<?php echo isset($_SESSION['campo']) && $_SESSION['campo'] == 2?' selected':'';?>>títulos e acompanhamento</option>
					</select>
                </div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-2">
                    <label class="control-label">Mapa:</label>
					<?php echo isset($mapas)?$mapas:'';?>
				</div>
				<div class="col-xs-12 col-sm-3">
                    <label class="control-label">Responsável:</label>
					<?php echo isset($responsaveis)?$responsaveis:'';?>
				</div>
				<div class="col-xs-12 col-sm-3 col-md-4">
                    <label class="control-label">&nbsp;</label>
					<input class="form-control" id="responsavel_pessoa" maxlength="40" name="responsavel_pessoa" placeholder="Pessoa responsável" type="text" value="<?php echo (isset($_SESSION['responsavelPessoa'])?$_SESSION['responsavelPessoa']:'')?>">
				</div>
				<div class="col-xs-12 col-sm-4 col-md-3">
                    <label>Situação:</label><br>
                    <select class="form-control" id="situacao" name="situacao">
						<option value="0">todas</option>
						<option value="1"<?php echo isset($_SESSION['situacao']) && $_SESSION['situacao'] == 1?' selected':'';?>>no prazo</option>
						<option value="2"<?php echo isset($_SESSION['situacao']) && $_SESSION['situacao'] == 2?' selected':'';?>>menos de 15 dias do prazo</option>
						<option value="3"<?php echo isset($_SESSION['situacao']) && $_SESSION['situacao'] == 3?' selected':'';?>>em atraso</option>
						<option value="4"<?php echo isset($_SESSION['situacao']) && $_SESSION['situacao'] == 4?' selected':'';?>>concluídas</option>
						<option value="5"<?php echo isset($_SESSION['situacao']) && $_SESSION['situacao'] == 5?' selected':'';?>>em monitoria continuada</option>
					</select>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                      <input class="btn btn-primary" id="pesquisar" type="button" value="Pesquisar" onclick="enviarPesquisa();" />
					  <label class="control-label pull-right"><input <?php echo isset($_SESSION['metas_continuadas']) && $_SESSION['metas_continuadas'] == false ? '': 'checked';?> id="metas_continuadas" name="metas_continuadas" type="checkbox" value="1"> Exibir todas as metas em monitoria continuada</label>
                </div>
            </div>
    </form>
	
	<div class="tab-pane" id="atualizacoes">
		<ul>
<?php
	//Existindo o arquivo de atualização, abre-o carregando uma linha em cada LI
	if(file_exists(atualizacaoArquivo)){
		$arquivo = fopen (atualizacaoArquivo,"r");
		while(strlen($linha = fgets($arquivo)) > 0){?>
			<li><?php echo $linha;?></li>
		<?php }
		fclose($arquivo);
	}
?>
		</ul>
	</div>
</div>