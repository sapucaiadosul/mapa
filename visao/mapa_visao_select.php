<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;
?>
<select class="form-control" id="mapa" name="mapa">
	<option value="0">Todos</option>
	<?php echo (isset($options)?(gettype($options)=='string'?$options:''):'');?>
</select>