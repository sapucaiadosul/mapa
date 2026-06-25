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
<select class="form-control" id="<?php echo $ocultarAdministrador==true?'responsavel':'perfil';?>" name="<?php echo $ocultarAdministrador==true?'responsavel':'perfil';?>" onchange="input_destaque_remover(this)">
	<option value=""><?php echo $ocultarAdministrador==true?(isset($_GET['formulario'])?'Selecione um responsável':'Todas as secretarias'):'Selecione um perfil';?></option>
	<?php echo (isset($options)?(gettype($options)=='string'?$options:''):'');?>
</select>