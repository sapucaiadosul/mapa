<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 *
 * @package		
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class Funcao{
	public static function carrega_arquivo($pasta, $nome){
		//Valida pasta do arquivo
		if(!in_array($pasta,array('controle','modelo','visao'))) return false;
		
		//Valida nome do arquivo
		if(preg_match('/^[a-z_]+$/', $nome) != 1) return false;
		
		//Verifica se tem acesso ao arquivo solicitado
		$arquivo = './'.$pasta.'/'.$nome.'.php';
		if(!file_exists($arquivo)) return false;
		require_once $arquivo;
		
		return true;
	}
	
	public function limpar_data($data){

		$data = (string)$data;
		
		if(strlen($data) >= 10){
			//Se tiver mais que dez dígitos, pode ser que tenha vindo com horário, então limita nos dez dígitos
			if(strlen($data) > 10) $data = substr($data, 0 ,10);
			//Troca hífem por barra
			$data = str_replace('-','/',$data);
			//Separa campos
			$data = explode('/', $data);
			if(count($data) == 3){
				//Padrão brasileiro
				if(preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', implode('/',$data)) && implode('/',$data) != '00/00/0000'){
					//Se a data for válida, monta-a no formato do banco YYYY-mm-dd
					if(checkdate($data[1],$data[0],$data[2])) $data = $data[2].'-'.$data[1].'-'.$data[0];
						else $data = '';
				//Padrão americano (via Chrome)
				}elseif(preg_match('/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/', implode('/',$data)) && implode('/',$data) != '0000-00-00'){
					//Se a data for válida, monta-a no formato do banco YYYY-mm-dd
					if(checkdate($data[1],$data[2],$data[0])) $data = $data[0].'-'.$data[1].'-'.$data[2];
						else $data = '';
				}else $data = '';
			}else $data = '';
		}else $data = '';
		
		return $data;
	}
	
	public function limpaInput($campo) {
		$campo = trim($campo);
		$campo = stripslashes($campo);
		$campo = htmlspecialchars($campo);
		return $campo;
	}
	
	public function mensagem($mensagem, $tipo = MENSAGEM_PADRAO){
		//Tipos de classes CSS
		$class[MENSAGEM_PADRAO] = 'alert-warning';
		$class[MENSAGEM_ERRO] = 'alert-danger';
		$class[MENSAGEM_SUCESSO] = 'alert-success';
		
		//Valida parâmetros
		if((is_string($mensagem) && strlen($mensagem) == 0) && !is_array($mensagem)) return false;
		if(!array_key_exists($tipo,$class)) $tipo = MENSAGEM_PADRAO;
		
		//Monta caixa da mensagem
		$caixa = '<div class="row mensagem">';
		$caixa.= '<div class="col-xs-12">';
		$caixa.= '<div class="alert '.$class[$tipo].'">';
		$caixa.= '<button class="close" data-dismiss="alert">';
		$caixa.= '<span aria-hidden="true">&times;</span>';
		$caixa.= '</button>';
		$caixa.= '<p>';
		//Mostra mensagem recebida ou informa que há campos inválidos
		if(is_array($mensagem)) $caixa.= "Preencha corretamente os campos destacados";
		else $caixa.= $mensagem;
		$caixa.= '</p>';
		$caixa.= '</div>';
		$caixa.= '</div>';
		$caixa.= '</div>';
		
		return $caixa;
	}
	
	//return string HTML, LISTA DE LINKS (A) DE PAGINAÇÃO PARA O MÓDULO
	public function pagina_criar_links($modulo, $paginas, $pagina = 1){
		//Valida parâmetro
		if(!is_string($modulo) || strlen($modulo) == 0) return '';
		if(!is_numeric($paginas)) $paginas = 0;
		if(!is_numeric($pagina)) $pagina = 1;
		if($pagina > $paginas) return '';
		
		//Inicia lista de links
		$links = '';
		$links.= '<div class="col-xs-12" id="paginas">';
		$links.= '<label>Páginas:</label>';
		
		//Cria lista de links de páginas
		for($p = 1; $p <= $paginas; $p++){
			$links.= '<a class="btn btn-default '.($pagina == $p ? 'active' : '').'" href="index.php?mod='.$modulo.'&pagina='.$p.'">'.$p.'</a>';
		}
		
		//Fecha lista de links
		$links.= '</div>';
		
		return $links;
	}
	
	//return string HTML, linha com o número de registros
	public function exibirTotalRegistros($registros){
		//Valida parâmetro
		if(!is_numeric($registros) || $registros < 0) $registros = 0;
		
		//Monta HTML
		$links = '';
		$links.= '<div class="col-xs-12" id="">';
		$links.= '<label>Registros encontrados:</label> ';
		$links.= $registros;
		$links.= '</div>';
		
		return $links;
	}
}
?>
