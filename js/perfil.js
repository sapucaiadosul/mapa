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
		if(mensagem.length == 0) mensagem = 'Preencha o campo: ';
		else mensagem+= ', ';
		mensagem+= '<strong>Nome do perfil</strong>';
		$('#nome').parent().addClass('has-error');
	}else{
		$('#nome').parent().removeClass('has-error');
	}
	
	//Adiciona um ponto final, se houver mensagem
	if(mensagem.length > 0) mensagem+= '.';
	
	return mensagem;
}