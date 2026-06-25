/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

$(function () {
	$('[data-toggle="tooltip"]').tooltip();
	$('.btn-acao-aprova').button();
	//Altera formato da data nos outros navegadores, pois só o Chrome usa o formato de data do banco de dados
	if(window.chrome == undefined && $("#data_inicial").length > 0){
		if(typeof($("#data_inicial")[0].value) == "string" && $("#data_inicial")[0].value.length > 0 && $("#data_inicial")[0].value.split("-").length == 3){
			var data = $("#data_inicial")[0].value.split("-");
			$("#data_inicial").val(data[2]+'/'+data[1]+'/'+data[0]);
		}
		if(typeof($("#data_final")[0].value) == "string" && $("#data_final")[0].value.length > 0 && $("#data_final")[0].value.split("-").length == 3){
			var data = $("#data_final")[0].value.split("-");
			$("#data_final").val(data[2]+'/'+data[1]+'/'+data[0]);
		}
		if(typeof($("#data_conclusao")[0].value) == "string" && $("#data_conclusao")[0].value.length > 0 && $("#data_conclusao")[0].value.split("-").length == 3){
			var data = $("#data_conclusao")[0].value.split("-");
			$("#data_conclusao").val(data[2]+'/'+data[1]+'/'+data[0]);
		}
	}
	//Se estiver da tela de pesquisa, adiciona submissão de formulário automática ao pressionar ENTER
	if($('#formularioPesquisa').length == 1){
		$('#formularioPesquisa input').keypress(function(e){
			if(e.which == 13) $('#formularioPesquisa').submit();
		});
	}
});

function formulario_enviar(origem){
	//Valida os campos, tendo como retorno mensagens de erro
	var retorno = validarCamposFormulario(origem);
	
	//Verifica se tem algum erro
	if(retorno.length > 0){
		mensagem(retorno);
		return false;
	}
	
	//Chama a função para transferir conteudo da div para o textarea
    transfereConteudo();
	
	//Envia formulário
	document.getElementById('f_form').submit();
}

function adicionarAcao(){
	var tableAcoes = $('.acoes table tbody');
	var novo = $('.acao-class').clone();
	var novoInput = $('input', novo);
	var inputId = $('input[name^="acao_id[');
	var maiorId = 0;
	
	//Percorre a lista de ações existentes procurando o maior ID para incrementar na nova ação
	inputId.each(function(input){
		//if($('input[name^="acao_id[')[input].name.match(/[0-9]+/) > maiorId) maiorId = $('input[name^="acao_id[')[input].name.match(/[0-9]+/);
		if(inputId[input].name.match(/[0-9]+/) > maiorId) maiorId = inputId[input].name.match(/[0-9]+/);
	});
	
	//Incrementa o maior ID encontrado para criar a nova ação
	maiorId++;
	
	//Percorre campos da nova ação alterando seus nome para inserir um numerador
	novoInput.each(function(input){
		switch(novoInput[input].name){
			case 'acao_id[]':
				novoInput[input].name = 'acao_id['+maiorId+']';
				break;
			case 'acao_concluida[]':
				novoInput[input].name = 'acao_concluida['+maiorId+']';
				break;
			case 'acao_nome[]':
				novoInput[input].name = 'acao_nome['+maiorId+']';
				break;
			case 'acao_prazo[]':
				novoInput[input].name = 'acao_prazo['+maiorId+']';
				break;
		}
	});
	
	//Remove classe para exibir ação
	novo.removeClass('acao-class');
	
	//Adiciona linha da ação na tabela
	tableAcoes.append(novo);
	
	//Adicionar nova ação ao total de ações novas
	$('#total_aberta').val(parseInt($('#total_aberta').val())+1);
	
	//Re-calcula tamanho da barra de progresso das ações
	calcularBarraAcoes();
	
	//Passa o foco para o campo adicionado
	document.getElementsByName('acao_nome['+maiorId+']')[0].focus()
}

function alterarBotaoMonitoriaContinuada(btn){
	if(btn == undefined) return false;
	//Adiciona e remove destaque conforme situação do botão
	if(btn.checked){
		$(btn.parentElement).addClass('btn-warning active');
		$(btn.parentElement).removeClass('btn-default');
	}else{
		$(btn.parentElement).addClass('btn-default');
		$(btn.parentElement).removeClass('btn-warning active');
	}
}

function aprovarAcao(label){
	//Não continua se a ação já estiver concluida
	if($(label).hasClass('disabled') == true) return false;
	
	//Verifica se está aprovando ou reprovando a ação
	if(label.title == 'Aprovar'){
		//Se já foi aprovado, faz nada
		if($(label).parent().parent().parent().attr('class') == 'success') return false;
		
		//Incrementa a opção clicada e decrementa a opção anterior, conforme classe CSS ativa
		$('#total_aprovada').val(parseInt($('#total_aprovada').val())+1);
		if($(label).parent().parent().parent().attr('class') == 'danger'){
			$('#total_reprovada').val(parseInt($('#total_reprovada').val())-1);
		}else if($(label).parent().parent().parent().attr('class') == 'warning'){
			$('#total_aguardando').val(parseInt($('#total_aguardando').val())-1);
		}
		
		//Remove possíveis classes anteriores
		$(label).parent().parent().parent().removeClass('danger warning');
		//Adiciona classe de aprovação
		$(label).parent().parent().parent().addClass('success');
	}else{
		//Se já foi reprovado, faz nada
		if($(label).parent().parent().parent().attr('class') == 'danger') return false;
		
		//Incrementa a opção clicada e decrementa a opção anterior, conforme classe CSS ativa
		$('#total_reprovada').val(parseInt($('#total_reprovada').val())+1);
		if($(label).parent().parent().parent().attr('class') == 'success')
			$('#total_aprovada').val(parseInt($('#total_aprovada').val())-1);
		else if($(label).parent().parent().parent().attr('class') == 'warning')
			$('#total_aguardando').val(parseInt($('#total_aguardando').val())-1);
		
		//Remove possíveis classes anteriores
		$(label).parent().parent().parent().removeClass('success warning');
		//Adiciona classe de reprovação
		$(label).parent().parent().parent().addClass('danger');
	}
	
	//Re-calcula tamanho da barra de progresso das ações
	calcularBarraAcoes();
	
	return false;
}

function atualizarIndicador(campo){
	//Valida parâmetro
	if(campo == undefined) campo = '';

	//Base de cálculo
	var indicador = parseFloat($('#indicador_indicador').val().replace(/\./g,'').replace(/\,/g, '.'));
	var referencia = parseFloat($('#indicador_referencia').val().replace(/\./g,'').replace(/\,/g, '.'));
	
	//Últimos valores salvos, carregados junto com o formulário
	var alcancadoAnterior = parseFloat($('#indicador_alcancado_anterior').val().replace(/\./g,'').replace(/\,/g, '.'));
	var referenciaAnterior = parseFloat($('#indicador_referencia_anterior').val().replace(/\./g,'').replace(/\,/g, '.'));

	//Valores da barra de progresso
	var aprovado = aprovadoTotal = parseFloat($('#indicador_total_aprovado').val());
	var reprovado = reprovadoTotal = parseFloat($('#indicador_total_reprovado').val());
	var aguardando = aguardandoTotal = parseFloat($('#indicador_total_aguardando').val());

	//Calcula divisor (se indicador descrescente, o divisor será negativo)
	var divisor = indicador - referencia;

	//Verifica inclinação do indicador
	if(divisor > 0){
		//Impede valor alcançado inferior ao valor de referência
		if(parseFloat($('#indicador_alcancado').val().replace(/\./g,'').replace(/\,/g, '.')) < referencia) $('#indicador_alcancado').val($('#indicador_referencia').val());
		
		//Impede valor alcançado superior ao valor do indicador
		//if(parseFloat($('#indicador_alcancado').val().replace(/\./g,'').replace(/\,/g, '.')) > indicador) $('#indicador_alcancado').val($('#indicador_indicador').val());
	}else{
		//Impede valor alcançado inferior ao valor de indicador
		if(parseFloat($('#indicador_alcancado').val().replace(/\./g,'').replace(/\,/g, '.')) < indicador) $('#indicador_alcancado').val($('#indicador_indicador').val());
		
		//Impede valor alcançado superior ao valor do referência
		if(parseFloat($('#indicador_alcancado').val().replace(/\./g,'').replace(/\,/g, '.')) > referencia) $('#indicador_alcancado').val($('#indicador_referencia').val());
	}

	//Valor alcançado atualizado
	var alcancado = parseFloat($('#indicador_alcancado').val().replace(/\./g,'').replace(/\,/g, '.'));
	
	//Verifica se quem está alterando é monitor
	if($('#indicador_alcancado_aprovado').length == 1){
		//Se clicou no botão de aprovação, volta ao valor informado pela secretaria para colocar como aprovado ou aguardando aprovação
		if(campo == 'indicador_alcancado_aprovado'){
			$('#indicador_alcancado').val(alcancadoAnterior);
			alcancado = alcancadoAnterior;
		}
		//Se aprovou ou alterou o valor alcançado, atualizada valores de aprovação, reprovação e aguardo
		if($('#indicador_alcancado_aprovado:checked').length == 1 || campo == 'indicador_alcancado'){
			aprovado = alcancado - referencia;
			reprovado = alcancadoAnterior - alcancado;
			if(aprovado != aprovadoTotal || reprovado != reprovadoTotal) aguardando = aguardando = 0;
		}
		//Se alterou a referência, re-calcula posições
		if(campo == 'indicador_referencia'){
			if(alcancado != alcancadoAnterior){
				if($('#indicador_alcancado_aprovado:checked').length == 1) aprovado = alcancado - alcancadoAnterior;
				else if(alcancado > alcancadoAnterior){
					if(referencia > alcancadoAnterior) alcancado = referencia;
					else alcancado = alcancadoAnterior;
					$('#indicador_alcancado').val(alcancado);
				}
			}
			//Se não houver valor reprovado, o valor alcançado foi preenchido por último pela secretaria
			if(reprovadoTotal == 0 && alcancadoAnterior == alcancado){
				if(aprovadoTotal > 0) aprovado = aprovadoTotal + referenciaAnterior - referencia;
				if(aprovado < 0) aprovado = 0;
				aguardando = (referenciaAnterior + aprovadoTotal + aguardandoTotal) - referencia - aprovado;
				if(aguardando < 0) aguardando = 0;
			}else{
				if(aprovado > 0) aprovado = alcancado - referencia;
				reprovado = (alcancadoAnterior == (aprovadoTotal + referenciaAnterior) ? reprovado : 0) + alcancadoAnterior - alcancado;
				aguardando = 0;
			}
		}
	}else{
		var alcancado = alcancado - referencia;
		if(alcancado != alcancadoAnterior){
			reprovado = 0;
			if(divisor > 0){
				if(alcancado > aprovado){
					aguardando = alcancado - aprovado;
				}else{
					aguardando = 0;
					aprovado = alcancado;
				}
			}else{
				if(alcancado < -aprovado){
					aguardando = alcancado + aprovado;
				}else{
					aguardando = 0;
					aprovado = alcancado;
				}
			}
		}
	}

	if(divisor < 0){
		if(divisor < 0) divisor*=-1;
		if(aprovado < 0) aprovado*=-1;
		if(reprovado < 0) reprovado*=-1;
		if(aguardando < 0) aguardando*=-1;
	}

	/* (valor - referencia) / (indicador - referencia) */

	//Atualiza valores
	$('#indicador_bar .progress-bar-success').css("width",((divisor > 0 ? aprovado/divisor : 1)*100)+"%");
	$('#indicador_bar .progress-bar-success').html(parseFloat((divisor > 0 ? aprovado/divisor : 1)*100).toFixed(2)+"%");
	$('#indicador_bar .progress-bar-warning').css("width",((divisor > 0 ? aguardando/divisor : 1)*100)+"%");
	$('#indicador_bar .progress-bar-warning').html(parseFloat((divisor > 0 ? aguardando/divisor : 1)*100).toFixed(2)+"%");
	$('#indicador_bar .progress-bar-danger').css("width",((divisor > 0 ? reprovado/divisor : 1)*100)+"%");
	$('#indicador_bar .progress-bar-danger').html(parseFloat((divisor > 0 ? reprovado/divisor : 1)*100).toFixed(2)+"%");
}

function calcularBarraAcoes(){
	var aprovada = parseInt($('#total_aprovada').val());
	var reprovada = parseInt($('#total_reprovada').val());
	var aguardando = parseInt($('#total_aguardando').val());
	var aberta = parseInt($('#total_aberta').val());
	var total = aprovada + reprovada + aguardando + aberta;
	
	//Atualiza valores
	$('#acao_bar .progress-bar-success').css("width",((aprovada/total)*100)+"%");
	$('#acao_bar .progress-bar-success').html(parseFloat((aprovada/total)*100).toFixed(2)+"%");
	$('#acao_bar .progress-bar-warning').css("width",((aguardando/total)*100)+"%");
	$('#acao_bar .progress-bar-warning').html(parseFloat((aguardando/total)*100).toFixed(2)+"%");
	$('#acao_bar .progress-bar-danger').css("width",((reprovada/total)*100)+"%");
	$('#acao_bar .progress-bar-danger').html(parseFloat((reprovada/total)*100).toFixed(2)+"%");
}

function concluirAcao(input){
	//Desmarca aprovação para não dar conflito de ações
	if($('input[value=1]:checked',$(input).parent().parent()).length == 1){
		$('input[value=1]',$(input).parent().parent()).parent().removeClass('active');
		//Decrementa aprovação caso esteja marcada
		$('#total_aprovada').val(parseInt($('#total_aprovada').val())-1);
	}
	
	//Verifica a classe da ação marcada, verifca se marcou ou desmarcou e altera os valores
	if(input.checked == true){
		$('#total_aguardando').val(parseInt($('#total_aguardando').val())+1);
		
		//Verifica se já foi monitorado
		if($('input[name*=acao_monitorada]', $(input).parent().parent()).length > 0 && $('input[name*=acao_monitorada]', $(input).parent().parent())[0].value.length > 0){
			//Decrementa apenas se não estava como aprovada, pois já decrementou quando aprovou
			if($('input[value=1]:checked',$(input).parent().parent()).length == 0)
				$('#total_reprovada').val(parseInt($('#total_reprovada').val())-1);
			//Desmarca reprovação para não dar conflito no salvar
			$('input[value=0]',$(input).parent().parent()).prop('checked', false);
			$('input[value=0]',$(input).parent().parent()).parent().removeClass('active');
			//Remove possível classe anterior
			$(input).parent().parent().removeClass('danger success');
		}else{
			$('#total_aberta').val(parseInt($('#total_aberta').val())-1);
		}
		//Adiciona classe de conclusão
		$(input).parent().parent().addClass('warning');
	}else{
		//Decrementa apenas se não estava como aprovada/reprovada, pois já decrementou quando aprovou
		if($('input[value=0]:checked',$(input).parent().parent()).length == 0 && $('input[value=1]:checked',$(input).parent().parent()).length == 0)
			$('#total_aguardando').val(parseInt($('#total_aguardando').val())-1);
		
		//Verifica se já foi monitorado
		if($('input[name*=acao_monitorada]', $(input).parent().parent()).length > 0 && $('input[name*=acao_monitorada]', $(input).parent().parent())[0].value.length > 0){
			//Só incrementa se já não estiver marcado
			if($('input[value=0]:checked',$(input).parent().parent()).length == 0)
				$('#total_reprovada').val(parseInt($('#total_reprovada').val())+1);
			
			//Adiciona classe de reprovação
			$(input).parent().parent().addClass('danger');
			//Re-marca reprovação
			$('input[value=0]',$(input).parent().parent()).prop('checked', true);
			$('input[value=0]',$(input).parent().parent()).parent().addClass('active');
		}else
			$('#total_aberta').val(parseInt($('#total_aberta').val())+1);
		
		//Remove possível classe de conclusão
		$(input).parent().parent().removeClass('success warning');
	}
	
	//Desmarca aprovação para não dar conflito de ações
	$('input[value=1]',$(input).parent().parent()).prop('checked', false);
	
	//Re-calcula tamanho da barra de progresso das ações
	calcularBarraAcoes();
}

function enviarPesquisa(){
	//Envia formulário
	document.getElementById('formularioPesquisa').submit();
}

function excluirAcao(id, nome){
	//Verifica se recebeu o ID
	if(id == undefined){
		alert('ID inválido. Contate o administrador do sistema.');
		return false;
	}
	
	//Confirma a exclusão, mostrando o nome do registro se receber
	if(confirm('Deseja realmente excluir a ação'+(nome != undefined ? ' '+nome : '')+'?')){
		//Mantem a página atual, adicionando os parâmetros necessários
		window.open(document.URL+'&acao='+id,'_self');
	}
}

function excluirAnexo(id, nome){
	//Verifica se recebeu o ID
	if(id == undefined){
		alert('ID inválido. Contate o administrador do sistema.');
		return false;
	}
	
	//Confirma a exclusão, mostrando o nome do registro se receber
	if(confirm('Deseja realmente excluir o anexo'+(nome != undefined ? ' '+nome : '')+'?')){
		//Mantem a página atual, adicionando os parâmetros necessários
		window.open(document.URL+'&anexo='+id,'_self');
	}
}

function selecionarTipo(tipo){
	//Valida parâmetro
	if(isNaN(tipo) || tipo < 0 || tipo > 3) return false;
	
	//Remove todas as classes
	$('.text-toolbar').removeClass('alert-danger alert-default alert-success alert-warning');
	$('#acompanhamento').removeClass('alert-danger alert-default alert-success alert-warning');
	
	//Adiciona a classe do tipo selecionado
	switch(parseInt(tipo)){
		case 0:
			$('.text-toolbar').addClass('alert-default');
			$('#acompanhamento').addClass('alert-default');
			break;
		case 1:
			$('.text-toolbar').addClass('alert-success');
			$('#acompanhamento').addClass('alert-success');
			break;
		case 2:
			$('.text-toolbar').addClass('alert-danger');
			$('#acompanhamento').addClass('alert-danger');
			break;
		case 3:
			$('.text-toolbar').addClass('alert-warning');
			$('#acompanhamento').addClass('alert-warning');
			break;
	}
}

//Transfere o conteudo da div para o textarea e o exibe ao enviar, para o php reconhecer e manipular
function transfereConteudo(){
	//Valida existência de conteúdo, pois se não tiver permissão para editar os campos não existirão
	if($('#acompanhamento').length > 0 && $('#acompanhamento').html().length > 0){
		var conteudo = $('#acompanhamento').html();
		$('#acompanhamento-textarea').val(conteudo);
		$('#acompanhamento-textarea').show();
	}
	//Se for monitor, verifica se aprovou o valor do indicador alcançado
	if($('#indicador_alcancado_aprovado:checked').length == 1 && $('#indicador_alcancado_anterior').length == 1){
		$('#indicador_alcancado_anterior').val('-1');
	}
}

function validarCamposFormulario(origem){
	//Inicia a mensagem
	var mensagem = '';
	
	//Valida campos
	if($('#titulo').length > 0 && $('#responsavel').length > 0 && $('#data_inicial').length > 0 && $('#data_final').length > 0){
		if($('#titulo').val().length == 0){
			if(mensagem.length > 0) mensagem+= ', ';
			mensagem+= '<strong>título</strong>';
			$('#titulo').parent().addClass('has-error');
		}else{
			$('#titulo').parent().removeClass('has-error');
		}
		
		if($('#responsavel').val().length == 0){
			if(mensagem.length > 0) mensagem+= ', ';
			mensagem+= '<strong>responsavel</strong>';
			$('#responsavel').parent().addClass('has-error');
		}else{
			$('#responsavel').parent().removeClass('has-error');
		}
		
		if(validaData($('#data_inicial').val()) == false || validaData($('#data_final').val()) == false){
			if(mensagem.length > 0) mensagem+= ', ';
			mensagem+= '<strong>Prazo</strong>';
			$('#data_final').parent().parent().addClass('has-error');
		}else if($('#data_inicial').val() > $('#data_final').val()){
			if(mensagem.length > 0) mensagem+= ', ';
			mensagem+= '<strong>Prazo inválido</strong>';
			$('#data_final').parent().parent().addClass('has-error');
		}else{
			$('#data_final').parent().parent().removeClass('has-error');
		}
	}
	
	//Valida acompanhamento e anexo, se clicar no salvar referente a eles
	if(origem != undefined && origem.id != ''){
		if(origem.id == 'acompanhar'){
			if($('#acompanhamento').html().length == 0){
				if(mensagem.length > 0) mensagem+= ', ';
				mensagem+= '<strong>acompanhamento</strong>';
				$('#acompanhamento').parent().addClass('has-error');
			}else{
				$('#acompanhamento').parent().removeClass('has-error');
			}
		}else if(origem.id == 'anexar'){
			if($('#arquivo').val().length == 0){
				if(mensagem.length > 0) mensagem+= ', ';
				mensagem+= '<strong>anexo</strong>';
				$('#arquivo').parent().addClass('has-error');
			}else{
				$('#arquivo').parent().removeClass('has-error');
			}
		}
	}
	
	//Adiciona início da linha e um ponto final, se houver mensagem
	if(mensagem.length > 0) mensagem = 'Preencha o(s) campo(s): ' + mensagem + '.';
	
	return mensagem;
}
