<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;
?>
<nav class="navbar navbar-default navbar-fixed-top" id="menu" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Clique para abrir menu de navegação</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.php"><img src="img/logo.png"/></a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
				
				<?php echo (isset($itens)?(is_array($itens)?(is_string($itens[0])?$itens[0]:''):''):'');?>
				
				<li<?php echo (isset($_GET['alterarsenha'])?' class="active"':'');?>>
                    <a href="index.php?alterarsenha" title="Alterar senha">
                        <span class="hidden-xs glyphicon glyphicon-lock icons-nav"></span>
                        <span class="visible-xs-inline"> Alterar senha</span>
                    </a>
                </li>
				<li>
                    <a href="index.php?sair" title="Sair">
                        <span class="hidden-xs glyphicon glyphicon-log-out icons-nav"></span>
                        <span class="visible-xs-inline">Sair</span>
                    </a>
                </li>
			</ul>
		</div>
	</div>
</nav>
