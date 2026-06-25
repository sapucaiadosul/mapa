/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

function formulario_enviar(){
	//Valida os campos, tendo como retorno mensagens de erro
	var retorno = campos_validar();
	
	//Verifica se tem algum erro
	if(retorno.length > 0){
		mensagem(retorno);
		return false;
	}
	
	//Envia formulário
	document.getElementById('f_form').submit();
}

function campos_validar(){
	//Inicia a mensagem
	var mensagem = '';
	
	//Valida campos
	if($('#nome').val().length == 0){
		if(mensagem.length == 0) mensagem = 'Preencha o(s) campo(s): ';
		else mensagem+= ', ';
		mensagem+= '<strong>Nome do usuário</strong>';
		$('#nome').parent().addClass('has-error');
	}else{
		$('#nome').parent().removeClass('has-error');
	}
	
	if($('#email').val().length > 0){
		var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
		if(filter.test($('#email').val())) $('#email').parent().removeClass('has-error');
		else{
			if(mensagem.length == 0) mensagem = 'Preencha o(s) campo(s): ';
			else mensagem+= ', ';
			mensagem+= '<strong>E-mail do usuário</strong>';
			$('#email').parent().addClass('has-error');
		}
	}else{
		$('#email').parent().removeClass('has-error');
	}
	
	if($('#perfil').val().length == 0){
		if(mensagem.length == 0) mensagem = 'Preencha o(s) campo(s): ';
		else mensagem+= ', ';
		mensagem+= '<strong>Perfil</strong>';
		$('#perfil').parent().addClass('has-error');
	}else{
		$('#perfil').parent().removeClass('has-error');
	}
	
	if($('#usuario').val().length == 0){
		if(mensagem.length == 0) mensagem = 'Preencha o(s) campo(s): ';
		else mensagem+= ', ';
		mensagem+= '<strong>Usuário</strong>';
		$('#usuario').parent().addClass('has-error');
	}else{
		$('#usuario').parent().removeClass('has-error');
	}
	
	if($('#id').val() == 0 || ($('#id').val() > 0 && ($('#nova_senha').val().length > 0 || $('#repete_senha').val().length > 0))){
		if($('#nova_senha').val().length == 0){
			if(mensagem.length == 0) mensagem = 'Preencha o(s) campo(s): ';
			else mensagem+= ', ';
			mensagem+= '<strong>Criar senha</strong>';
			$('#nova_senha').parent().addClass('has-error');
		}else{
			$('#nova_senha').parent().removeClass('has-error');
		}
		
		if($('#repete_senha').val().length == 0){
			if(mensagem.length == 0) mensagem = 'Preencha o(s) campo(s): ';
			else mensagem+= ', ';
			mensagem+= '<strong>Repetir senha</strong>';
			$('#repete_senha').parent().addClass('has-error');
		}else{
			$('#repete_senha').parent().removeClass('has-error');
		}
	}
	
	//Adiciona um ponto final, se houver mensagem
	if(mensagem.length > 0) mensagem+= '.';
	
	return mensagem;
}

function mensagem_usuario_existe_remover(campo){
	//Remove a mensagem que surge abaixo do campo quando o usuário de login já existe
	if($('#usuario_existe').length > 0){
		$('#usuario_existe').remove();
	}
	//Função padrão de limpeza de campo
	input_destaque_remover(campo);
}