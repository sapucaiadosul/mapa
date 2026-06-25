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
<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4 col-lg-2 col-lg-offset-5">
	<form class="alterarsenha" id="f_form" method="post">
		<input name="form_name" type="hidden" value="senha">
		<fieldset>
			<legend>Alterar senha</legend>
			<div class="form-group">
				<label for="senha_atual">Senha atual</label>
				<input autofocus class="form-control" id="senha_atual" name="senha_atual" type="password">
			</div>
			<div class="form-group">
				<label for="nova_senha">Nova senha</label>
				<input class="form-control" id="nova_senha" name="nova_senha" type="password">
			</div>
			<div class="form-group">
				<label for="repita_senha">Repita a nova senha</label>
				<input class="form-control" id="repita_senha" name="repita_senha" type="password">
			</div>
		</fieldset>
	</form>
</div>