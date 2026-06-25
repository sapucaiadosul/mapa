/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

const MENSAGEM_PADRAO = 1;
const MENSAGEM_ERRO = 2;
const MENSAGEM_SUCESSO =  3;

function seleciona_tudo(marcar){
	//Só segue se receber um boolean para marcar ou desmarcar
	if(typeof marcar != 'boolean') return false;
	
	var input = document.getElementsByTagName('table')[0].getElementsByTagName('input');
	//Percorre lista de inputs marcando ou desmarcando os checkboxes
	for(i=0; i < input.length; i++){
		if(input[i].type == 'checkbox') input[i].checked = marcar;
	}
}

function registro_del(modulo, id, nome){
	//Verifica se recebeu o ID
	if(id == undefined){
		alert('ID inválido. Contate o administrador do sistema.');
		return false;
	}
	
	//Verifica se recebeu o MÓDULO
	if(modulo == undefined){
		alert('Módulo inválido. Contate o administrador do sistema.');
		return false;
	}
	
	//Confirma a exclusão, mostrando o nome do registro se receber
	if(confirm('Deseja realmente alterar o '+modulo+(nome != undefined ? ' '+nome : '')+'?')){
		//Mantem a página atual, adicionando os parâmetros necessários
		window.open('./index.php?mod='+modulo+'&excluir&id='+id,'_self');
	}
}

function registros_del(modulo){
	var id = new Array();
	var inputs = document.getElementsByTagName('input');
	
	//Verifica se recebeu o MÓDULO
	if(modulo == undefined){
		alert('Módulo inválido. Contate o administrador do sistema.');
		return false;
	}
	
	//Varre inputs criando lista de checkbox
	for(var i=0; i < inputs.length; i++){
		if(inputs[i].type=='checkbox')
			if(inputs[i].checked == true && !isNaN(inputs[i].value)) id.push(inputs[i].value);
	}
	//Verifica se há registros selecionados
	if(id.length == 0){
		alert('Nenhum registro selecionado');
		return false;
	}
	//Passa lista para lista de parâmetros
	var parametros = '';
	for (i = 0; i < id.length; i++) {
		parametros += '&id[]='+id[i];
	}
	//Confirma a exclusão, mostrando a quantidade de registros que serão excluídos
	if(confirm('Deseja realmente excluir '+id.length+' '+modulo+'(s) selecionado(s)?')){
		//Mantem a página atual, adicionando os parâmetros necessários
		window.open('./index.php?mod='+modulo+'&excluir'+parametros,'_self');
	}
}

function mensagem(mensagem, tipo){
	//Tipos de classes CSS
	var classe = new Array();
	classe[MENSAGEM_PADRAO] = 'alert-warning';
	classe[MENSAGEM_ERRO] = 'alert-danger';
	classe[MENSAGEM_SUCESSO] = 'alert-success';
	
	//Verifica se tem alguma mensagem para exibir na tela
	if(mensagem.length > 0){
		//Remove a mensagem anterior, se existir
		if($('.mensagem').length > 0) $('.mensagem').remove();
		
		//Verifica o tipo da mensagem
		if(tipo == undefined || !(tipo in classe)) tipo = MENSAGEM_PADRAO;
		
		//Monta caixa da mensagem
		var caixa = '<div class="row mensagem">';
		caixa+= '<div class="col-xs-12">';
		caixa+= '<div class="alert '+classe[tipo]+'">';
		caixa+= '<button class="close" data-dismiss="alert">';
		caixa+= '<span aria-hidden="true">&times;</span>';
		caixa+= '</button>';
		caixa+= '<p>';
		caixa+= mensagem;
		caixa+= '</p>';
		caixa+= '</div>';
		caixa+= '</div>';
		caixa+= '</div>';
		$(caixa).insertBefore('#acoes');
		return true;
	}
	return false;
}

function input_destaque_remover(campo){
	//Remove a marca de campo com erro ao escrever algo nele
	$(campo).parent().removeClass('has-error');
}

function apenasNumeros(extra){
	if(window.event){
		if((window.event.keyCode <48) || (window.event.keyCode>57)){
			if(extra == undefined) return false;
			if(extra.indexOf(String.fromCharCode(window.event.keyCode)) < 0) return false;
		}
	}
	return true;
}

function validaEmail(email){
	var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	if(!filter.test(email)){
		alert('E-mail inválido');
		return false;
	}else{
		return true;
	}
}

function validaData(data){
    //Verifica se há parâmetro e se o formato é válido
    if(data == undefined || typeof(data) != "string" || (data.split("/").length != 3 && data.split("-").length != 3)) return false;
    
    //Valida tipo de formatação
    var brasil = data.split("/").length == 3 ? true : false;
    var aAr =  brasil == true ? data.split("/") : data.split("-");
    
    //Separa os campos para validar
    var lDay = parseInt(aAr[(brasil == true ? 0 : 2)]), lMon = parseInt(aAr[1]), lYear = parseInt(aAr[(brasil == true ? 2 : 0)]),
        BiY = (lYear % 4 == 0 && lYear % 100 != 0) || lYear % 400 == 0,
        MT = [1, BiY ? -1 : -2, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1]; //Quantidade de dias além dos 30 básicos (30+0 ou 30-1 ou 30+1)
    
    if (lMon <= 12 && lMon > 0 && lDay <= MT[lMon - 1] + 30 && lDay > 0) return true;
    else return false;
}
