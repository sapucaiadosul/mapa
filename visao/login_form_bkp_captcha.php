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
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4 well" id="login" >

	<?php if(!isset($_GET['action'])) { ?>

	<form action="index.php" id="f_login" method="post">
		<input name="form_name" type="hidden" value="login">
		<fieldset>
			<div id="logo"></div>
			<div class="text-center">
				<br><img class="logo" style="margin: auto; display: block; width: 75%" src="img/logo.png"><br>
			</div>
			<div class="form-group">
				<label for="usuario">Usuário</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
					<input autofocus class="form-control" title="Digite seu usuário" id="usuario" name="usuario" type="text" placeholder="Digite seu usuário">
				</div>
			</div>
			<div class="form-group">
				<label for="senha">Senha</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
					<input class="form-control" title="Digite a sua senha" id="senha" name="senha" type="password" placeholder="Digite sua senha">
				</div>
			</div>
			<div class="form-group">
				<br><div class="g-recaptcha" style="justify-content: center; align-items: center;" data-sitekey="6LdMyJ0cAAAAAJYMaqQBHD2P0ORmfQCznZRhESWI"></div><br>
				<input class="btn btn-primary" id="entrar" name="entrar" type="submit" value="Entrar">
			</div>
		</fieldset>
	</form>

	<?php } ?>
	

	<?php 

		if($_GET['action'] == 'esqueci') {
			?>
			    <form action="index.php?action=esqueci" method="post">
					<h3> Recuperação de senha</h3>	
					<input name="form_name" type="hidden" value="esqueci_senha">
						<div class="form-group">
						<label for="senha">CPF</label>
							<input class="form-control" title="Digite seu CPF" name="esqueci_senha_cpf" placeholder="Digite seu CPF">
						</div>
						<div class="form-group">
						<label for="senha">E-mail</label>
							<input class="form-control" title="Digite seu e-mail" name="esqueci_senha_email" placeholder="Digite seu e-mail">
						</div>
						<div class="form-group">
							<input class="btn btn-primary" id="entrar" name="entrar" type="submit" value="Enviar e-mail">
						</div>
				</form>
				
		
	<?php } ?>
	<br>
	<a href="?">Login</a> &nbsp;|&nbsp;
	<a href="?action=esqueci">Esqueci a senha</a>
	
	
</div>
