<div id="container">
    <h3><?php echo $this->lang->line('pt_tminhas'); ?></h3>
    
    <table cellspacing="0" cellpadding="2" border="0" width="100%" class="table">
	<tr class="row">
	    <td align="center"><?php echo $this->lang->line('pt_cativo');?></td>
	    <td align="center"><?php echo $this->lang->line('pt_cadmin');?></td>
	    <td><?php echo $this->lang->line('pt_ctitulo');?></td>
	    <td><?php echo $this->lang->line('pt_cocasiao');?></td>
	    <td align="center"><?php echo $this->lang->line('pt_cdias');?></td>
	    <td align="center"><?php echo $this->lang->line('pt_ccriado');?></td>
	    <td align="center"><?php echo $this->lang->line('pt_cexpira');?></td>
	    <td align="center"><?php echo $this->lang->line('pt_cprivado');?></td>
	    <td align="center"><?php echo $this->lang->line('pt_cacoes');?></td>
	</tr>
	<?php if(count($counts) > 0){ ?>
	<?php foreach($counts as $k){
	    if($k->co_pago == 'n'){
		$at = "Falta confirmação de pagamento";
	    }else{
		$at = "Count ativo";
	    }
	    ?>
	<tr class="linha">
	    <td align="center"><img title="<?php echo $at; ?>" src="<?php echo base_url(); ?>img/priv_<?php echo $k->co_pago; ?>.png"></td>
	    <td align="center"><?php
	    $a = $k->co_admin;
	    if($a != ""){
		echo count(explode(",",$k->co_admin));
	    }else{
		echo 0;
	    } ?></td>
	    <td><?php echo $k->co_titulo; ?></td>
	    <td><?php echo $k->co_descricao; ?></td>
	    <td align="center"><?php echo $k->co_dias; ?></td>
	    <td align="center"><?php echo date("d/m/Y", strtotime($k->co_data_compra)); ?></td>
	    <td align="center"><?php echo date("d/m/Y", strtotime($k->co_data_expira)); ?></td>
	    <td align="center"><?php print($k->co_privado == 's' ? '<span class="green">Sim</span>' : '<span class="red">Não</span>'); ?></td>
	    <td align="center">
		<?php echo anchor('web/edit_count/'.$k->co_codigo,'<img title="Editar Count" src="'.base_url().'img/editar.png">'); ?>&nbsp;
		<img title="Excluir Count" class="exc" data-ur="<?php echo base_url(); ?>" data-id="<?php echo $k->co_codigo; ?>" src="<?php echo base_url(); ?>img/exclude.png">&nbsp;
		<?php echo anchor('web/tips/'.$k->co_codigo,'<img title="Gerenciar Tips" src="'.base_url().'img/tips.gif">'); ?>
	    </td>
	</tr>
	<?php } ?>
	<?php }else{ ?>
	<tr class="linha">
	    <td colspan="9"><h4>Você ainda não criou nenhum Count.</h4></td>
	</tr>
	<?php } ?>
    </table>
</div>