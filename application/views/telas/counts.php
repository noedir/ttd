<div id="container">
    <h3><?php echo $this->lang->line('pt_tminhas'); ?></h3>
    <div id="baseMinhasCounts">
            <table cellpadding="0" cellspacing="0" border="0" width="100%" id="tabelaCount">
            	<tr class="categorias">
                    <td width="15">Ativo</td>
                    <td style="text-align: center;">Admins</td>
                    <td>Título</td>
		    <td>Identificador</td>
                    <td style="text-align: center;">Dias</td>
                    <td style="text-align: center;">Início em</td>
                    <td style="text-align: center;">Expira em</td>
                    <td style="text-align: center;">Privado</td>
                    <td style="text-align: center;">Ações</td>
                    <td></td>
                </tr>
                <?php if(count($counts) > 0){ ?>
		<?php foreach($counts as $k){
		if($k->co_pago == 'n'){
		    $at = "Falta confirmação de pagamento";
		}else{
		    $at = "Count ativo";
		}
		?>
                <tr class="countGerencia">
                    <td style="text-align:center;" class="tdCounts"><img title="<?php echo $at; ?>" src="<?php echo base_url(); ?>img/count_<?php echo $k->co_pago; ?>.png"></td>
                    <td style="text-align:center;" class="tdCounts">
			<?php
			$a = $k->co_admin;
			if($a != ""){
			    echo count(explode(",",$k->co_admin));
			}else{
			    echo 0;
			} ?>
		    </td>
                    <td class="tdCounts"><?php echo anchor('web/tips/'.$k->co_codigo,'&raquo; '.$k->co_titulo,'class="poshy" title="Gerenciar Tips desta Count"'); ?></td>
		    <td class="tdCounts"><small><?php echo $k->co_nomeunico; ?></small></td>
                    <td class="tdCountsDiasContagem"><?php echo $k->co_dias; ?></td>
                    <td class="tdCountsDiasIniFim"><?php echo date("d/m/Y", strtotime($k->co_data_compra)); ?></td>
                    <td class="tdCountsDiasIniFim"><?php echo date("d/m/Y", strtotime($k->co_data_expira)); ?></td>
                    <td style="text-align:center;" class="tdCounts"><?php print($k->co_privado == 's' ? '<span class="green">Sim</span>' : '<span class="red">Não</span>'); ?></td>
                    <td class="tdCountsAcoes"><?php
		    echo anchor('web/edit_count/'.$k->co_codigo,'<img style="margin-left: 0px;" src="'.base_url().'/img/editar_count.png" />');
		    echo anchor('web/invites/'.$k->co_codigo,' <img src="'.base_url().'img/convidar_amigos.png" />');
		    echo anchor('web/estatisticas/'.$k->co_codigo,'<img src="'.base_url().'/img/stats_count.png" />');
		    ?></td>
                    <td  style="text-align:center; border-top: none; margin-left: 0px;" class="tdCounts"><img title="Excluir Count" class="exc" data-ur="<?php echo base_url(); ?>" data-id="<?php echo $k->co_codigo; ?>" src="<?php echo base_url(); ?>img/excluir_count.png"></td>
                </tr>
		<?php } ?>
		<?php }else{ ?>
		<tr class="linha">
		    <td colspan="10"><h4>Você ainda não criou nenhuma Count.</h4></td>
		</tr>
		<?php } ?>
            </table>
        </div>
</div>