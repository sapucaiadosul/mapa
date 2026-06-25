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
	<div class="col-xs-12 btns-style">
		<?php
			if(isset($_GET['mod']) && $this->login->alterarsenha == false && (!isset($_GET['id']) || (!is_null($this->modulo->registro) && $this->modulo->registro->desativado == false))){
				if(($this->login->permissao($_GET['mod'],CADASTRAR) >= PROPRIOS && (!isset($_GET['id']) || $_GET['id'] == 0)) || ($this->login->permissao($_GET['mod'],EDITAR) >= TODOS) || ($this->login->permissao($_GET['mod'],EDITAR) >= PROPRIOS && isset($_GET['id']) && is_numeric($_GET['id']) && isset($this->modulo) && method_exists($this->modulo, 'permitirEdicao') && $this->modulo->permitirEdicao($_GET['id']))){
		?>
		<a class="btn btn-success" href="javascript:;" onclick="document.getElementById('f_form').action='index.php?mod=<?php echo (isset($_GET['mod'])?$_GET['mod']:'');?>&formulario&id=<?php echo (isset($_GET['id'])?$_GET['id']:0);?>'; formulario_enviar();">Salvar</a>
		<a class="btn btn-default" href="javascript:;" onclick="document.getElementById('f_form').action='index.php?mod=<?php echo (isset($_GET['mod'])?$_GET['mod']:'');?>'; formulario_enviar();">Salvar e fechar</a>
		<?php if($this->login->permissao($_GET['mod'],CADASTRAR) >= PROPRIOS){ ?>
		<a class="btn btn-default" href="javascript:;" onclick="document.getElementById('f_form').action='index.php?mod=<?php echo (isset($_GET['mod'])?$_GET['mod']:'');?>&formulario'; formulario_enviar();">Salvar e Adicionar outro</a>
		<?php }}}elseif($this->login->alterarsenha == true){ ?>
		<a class="btn btn-success" href="javascript:;" onclick="document.getElementById('f_form').submit();">Salvar</a>
		<?php } ?>
		<a class="btn btn-default" href="index.php<?php echo (isset($_GET['mod'])?'?mod='.$_GET['mod']:'');?>">Sair sem salvar</a>
	</div>
</div>