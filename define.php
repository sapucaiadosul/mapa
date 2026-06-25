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

//PERMISSÃO - AÇÃO
define('LISTAR', 1);
define('CADASTRAR', 2);
define('EDITAR', 3);
define('EXCLUIR', 4);

//PERMISSÃO - TIPO
define('PROPRIOS', 1);
define('TODOS', 2);
define('EXCLUIDOS', 3);

//MENSAGEM - TIPO
define('MENSAGEM_PADRAO', 1);
define('MENSAGEM_ERRO', 2);
define('MENSAGEM_SUCESSO', 3);
?>