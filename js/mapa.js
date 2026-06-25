/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Navega entre as abas
function aba(item){
	//Valida item
	if(typeof item != 'object') return false;
	
	//Oculta todas as tabs
	$('.tab-pane.active').removeClass('active');

	$(item).tab('show');
	$($(item).children('a').attr('href')).addClass('active');
}

function formatar(valor) {
	var range, sel;
	if (window.getSelection) {
		// Non-IE case
		sel = window.getSelection();
		if (sel.getRangeAt) {
			range = sel.getRangeAt(0);
		}

		if (range) {
			sel.removeAllRanges();
			sel.addRange(range);
		}
		document.execCommand(valor, false, null);

	} else if (document.selection && document.selection.createRange &&
			document.selection.type != "None") {
		// IE
		range = document.selection.createRange();
		range.execCommand(valor, false, null);
	}
}