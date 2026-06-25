<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 * @package		
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

//Variáveis de configuração
class Config{
	public static $db_limite = 30;
	public static $nome_arquivo_incluso = '';
	public static $sistema_css = array('');
	public static $sistema_js = array('mapa');
	public static $sistema_nome = 'Mapa estratégico';
	public static $sistema_sessao = 'MAPA';
	public static $sistema_versao = '1';
	public $db_banco = 'mysql:host=localhost; dbname=mapa; charset=UTF8';
	public $db_senha = '1234';
	public $db_usuario = 'root';
}
?>