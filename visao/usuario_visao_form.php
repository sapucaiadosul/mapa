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
<div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
	<form class="usuario" id="f_form" method="post">
		<input id="id" name="id" type="hidden" value="<?php echo (isset($registro) && get_class($registro)=='Usuario'?$registro->id:0);?>">
		<fieldset>
			<legend>Dados</legend>
			<div class="form-group<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && in_array('nome',$GLOBALS['campo_erro'])? ' has-error' : '');?>">
				<label class="control-label" for="nome">Nome do usuário</label>
				<input autofocus class="form-control" id="nome" name="nome" onkeypress="input_destaque_remover(this)" type="text" value="<?php echo (isset($registro) && get_class($registro)=='Usuario'?$registro->nome:'');?>">
			</div>
			<div class="form-group<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && in_array('email',$GLOBALS['campo_erro'])? ' has-error' : '');?>">
				<label class="control-label" for="email">E-mail do usuário</label>
				<input autofocus class="form-control" id="email" name="email" onkeypress="input_destaque_remover(this)" type="text" value="<?php echo (isset($registro) && get_class($registro)=='Usuario'?$registro->email:'');?>">
			</div>
			<div class="form-group<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && in_array('perfil',$GLOBALS['campo_erro'])? ' has-error' : '');?>">
				<label class="control-label" for="perfil">Perfil</label>
				<?php echo (isset($select)?(gettype($select)=='string'?$select:''):'');?>
			</div>
		</fieldset>
		<fieldset>
			<legend>Acesso</legend>
			<div class="form-group<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && (in_array('usuario',$GLOBALS['campo_erro']) || in_array('usuario_existe',$GLOBALS['campo_erro']))? ' has-error' : '');?>">
				<label class="control-label" for="usuario">Usuário</label>
				<input class="form-control" id="usuario" name="usuario" onkeypress="mensagem_usuario_existe_remover(this)" type="text" value="<?php echo (isset($registro) && get_class($registro)=='Usuario'?$registro->usuario:'');?>">
				<?php if(isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && in_array('usuario_existe',$GLOBALS['campo_erro'])) echo '<span class="text-danger" id="usuario_existe">Este usuário já existe</span>';?>
			</div>
			<div class="form-group<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && in_array('nova_senha',$GLOBALS['campo_erro'])? ' has-error' : '');?>">
				<label class="control-label" for="nova_senha"><?php echo (isset($registro) && get_class($registro)=='Usuario'?'Redefinir':'Criar')?> senha</label>
				<input class="form-control" id="nova_senha" name="nova_senha" onkeypress="input_destaque_remover(this)" type="password" value="">
				<span>*Preencher apenas se necessário</span>
			</div>
			<div class="form-group<?php echo (isset($GLOBALS['campo_erro']) && is_array($GLOBALS['campo_erro']) && in_array('repete_senha',$GLOBALS['campo_erro'])? ' has-error' : '');?>">
				<label class="control-label" for="repete_senha">Repetir senha</label>
				<input class="form-control" id="repete_senha" name="repete_senha" onkeypress="input_destaque_remover(this)" type="password" value="">
				<span>*Preencher apenas se necessário</span>
			</div>
			<div class="form-group">
				<input checked id="senha_provisoria" name="senha_provisoria" type="checkbox"> Senha Provisória
			</div>
		</fieldset>
	</form>
</div>