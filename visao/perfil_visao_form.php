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
<div class="col-xs-12 col-md-10 col-md-offset-1">
	<form class="perfil" id="f_form" method="post">
		<input id="id" name="id" type="hidden" value="<?php echo (isset($registro) && get_class($registro)=='Perfil'?$registro->id:0);?>">
		<fieldset>
			<legend>Dados</legend>
			<div class="form-group<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && in_array('nome',$GLOBALS['campo_erro'])? ' has-error' : '');?>">
				<label for="nome">Nome do perfil</label>
				<input autofocus class="form-control" id="nome" name="nome" onkeypress="input_destaque_remover(this)" type="text" value="<?php echo (isset($registro) && get_class($registro)=='Perfil'?$registro->nome:'');?>">
			</div>
		</fieldset>
		<fieldset>
			<legend>Permissões</legend>
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Nome</th>
							<th>Listar</th>
							<th>Cadastrar</th>
							<th>Editar</th>
							<th>Excluir</th>
						</tr>
					</thead>
					<tbody>
						<?php echo (isset($modulos)?(gettype($modulos)=='string'?$modulos:''):'');?>
					</tbody>
				</table>
			</div>
		</fieldset>
	</form>
</div>