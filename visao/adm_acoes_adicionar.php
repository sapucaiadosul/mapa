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
<div class="row" id="acoes">
	<div class="col-xs-12">
		<?php if($this->login->permissao($_GET['mod'],CADASTRAR) >= PROPRIOS){?>
		<a class="btn btn-success" href="index.php?mod=<?php echo (isset($_GET['mod'])?$_GET['mod']:'');?>&formulario">Adicionar</a>
		<?php }?>
		<?php if($this->login->permissao($_GET['mod'],EXCLUIR) >= PROPRIOS){?>
		<a class="btn btn-danger" href="javascript:;" onclick="registros_del('<?php echo (isset($_GET['mod'])?$_GET['mod']:'');?>');">Apagar</a>
		<?php }?>
		<?php if($this->login->permissao($_GET['mod'],LISTAR) >= EXCLUIDOS){?>
		<a class="btn btn-warning<?php echo (isset($_SESSION[$_GET['mod'].'_excluidos'])?' active':'');?>" href="index.php?mod=<?php echo (isset($_GET['mod'])?$_GET['mod']:'');?>&todos">Exibir até excluídos</a>
		<?php }?>
	</div>
</div>