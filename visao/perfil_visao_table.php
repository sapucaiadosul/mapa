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
<div class="col-xs-12 table-responsive">
	<table class="table table-hover table-striped">
		<thead>
			<tr>
				<?php if($permissao_excluir >= PROPRIOS || isset($pemissao_excluir_proprio)){?><th class="text-center"><input onclick="seleciona_tudo(this.checked)" type="checkbox"></th><?php }?>
				<th class="text-center">ID</th>
				<th>Nome</th>
				<?php if($permissao_excluir >= PROPRIOS || isset($pemissao_excluir_proprio)){?><th class="text-center">Excluir</th><?php }?>
			</tr>
		</thead>
		<tbody>
			<?php echo (isset($linhas)?(gettype($linhas)=='string'?$linhas:''):'');?>
		</tbody>
	</table>
</div>