<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

//Verifica se recebeu o login
if(isset($this->login)){
	if(get_class($this->login) == 'LoginControle'){
		?>
	<div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 well">
		<p>Bem vindo(a) <strong><?php echo $this->login->usuario_nome;?></strong></p>
		<p>Utilize o menu acima para navegar pelo sistema</p>
	</div>

	<div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">

		<?php if($this->login->permissao('usuario',LISTAR)) { ?>    
		<div class="col-xs-6 col-sm-3">
			<a href="index.php?mod=usuario">
				<div class="btn-inicio">
					<img src="img/usuario.png"/>
					<h3><strong>Usuários</strong></h3>
				</div>
			</a>
		</div>
		<?php }
		if($this->login->permissao('usuario',CADASTRAR)) { ?>
		<div class="col-xs-6 col-sm-3">
			<a href="index.php?mod=usuario&formulario">
				<div class="btn-inicio">
					<img src="img/usuarioadd.png"/>
					<h3><strong>Adicionar Usuários</strong></h3>
				</div>
			</a>
		</div>
		<?php }
		if($this->login->permissao('perfil',LISTAR)) {  ?>
		<div class="col-xs-6 col-sm-3">
			<a href="index.php?mod=perfil">
				<div class="btn-inicio">
					<img src="img/perfil.png"/>
					<h3><strong>Perfil</strong></h3>
				</div>
			</a>
		</div>
		<?php }
		if($this->login->permissao('perfil',CADASTRAR)) { ?>  
		<div class="col-xs-6 col-sm-3">
			<a href="index.php?mod=perfil&formulario">
				<div class="btn-inicio">
					<img src="img/perfiladd.png"/>
					<h3><strong>Adicionar Perfil</strong></h3>
				</div>
			</a>
		</div>
		<?php }?>
		<div class="col-xs-6 col-sm-3">
			<a href="index.php?alterarsenha">
				<div class="btn-inicio">
					<img src="img/senha.png"/>
					<h3><strong>Alterar Senha</strong></h3>
				</div>
			</a>
		</div>
		<div class="col-xs-6 col-sm-3">
			<a href="index.php?sair">
				<div class="btn-inicio">
					<img src="img/sair.png"/>
					<h3><strong>Sair</strong></h3>
				</div>
			</a>
		</div>
	</div>
<?php }}?>