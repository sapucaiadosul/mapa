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

if (version_compare(PHP_VERSION, '5.3.10', '<')) die('O servidor precisa ter PHP 5.3.10 ou superior para executar esta versão do SiGeM!');

//Informa que está dentro do sistema
define('SIGEM_EXEC', true);
//Informa formatação de texto
header('Content-Type: text/html; charset=utf-8');
//Inicia sessão
session_start();

//Classe que carrega todos os elementos
require_once "./controle/carrega.php";
$carrega = new Carrega();

//Carrega template do sistema
echo $carrega->template();
?>