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
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="content-language" content="pt-br">
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="author" content="Kleyton Fantin">
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo (isset($this->title)?$this->title:'');?></title>
		<?php echo (isset($this->css)?$this->css:'');?>
		<?php echo (isset($this->js)?$this->js:'');?>
	</head>
	<body>
		<?php echo (isset($this->menu)?$this->menu:'');?>
		<div class="container-fluid">
			<?php echo (isset($this->mensagem)?$this->mensagem:'');?>
			<?php echo (isset($this->acoes)?$this->acoes:'');?>
			<?php echo (isset($this->breadcrumb)?$this->breadcrumb:'');?>
			<div class="row">
				<?php echo (isset($this->conteudo)?$this->conteudo:'');?>
			</div>
		</div>
	</body>
</html>