<div id="container">
    <div id="baseMinhasCounts">
	<h3><?php echo $this->lang->line('pt_tminhas'); ?></h3>
            <table cellpadding="0" cellspacing="0" border="0" width="100%" id="tabelaCount">
            	<tr class="categorias">
                    <td width="15">Ativo</td>
                    <td style="text-align: center;">Admins</td>
                    <td>Título</td>
		    <td>Identificador</td>
                    <td style="text-align: center;">Dias</td>
                    <td style="text-align: center;">Início em</td>
                    <td style="text-align: center;">Finaliza em</td>
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
                <tr class="countGerencia zebra poshy" title="Gerenciar Tips de <?php echo $k->co_titulo; ?>" data-count="<?php echo $k->co_codigo; ?>">
                    <td style="text-align:center;" class="tdCounts zebra"><img title="<?php echo $at; ?>" src="<?php echo base_url(); ?>img/count_<?php echo $k->co_pago; ?>.png"></td>
                    <td style="text-align:center;" class="tdCounts zebra">
			<?php
			$a = $k->co_admin;
			if($a != ""){
			    echo count(explode(",",$k->co_admin));
			}else{
			    echo 0;
			} ?>
		    </td>
                    <td class="tdCounts zebra"><?php echo $k->co_titulo; ?></td>
		    <td class="tdCounts zebra"><?php echo $k->co_nomeunico; ?></td>
                    <td class="tdCountsDiasContagem zebra"><?php echo $k->co_dias; ?></td>
                    <td class="tdCountsDiasIniFim zebra"><?php
		    if($k->co_data_inicio == ''){
			echo '---';
		    }else{
			echo date("d/m/Y", strtotime($k->co_data_inicio));
		    }?></td>
                    <td class="tdCountsDiasIniFim zebra"><?php
		    if($k->co_data_inicio == ''){
			echo '---';
		    }else{
			echo date("d/m/Y", strtotime($k->co_data_inicio .' + '. ($k->co_dias - 1).' days'));
		    }?></td>
                    <td style="text-align:center;" class="tdCounts zebra"><?php print($k->co_privado == 's' ? '<span class="greenn">Sim</span>' : '<span class="redd">Não</span>'); ?></td>
                    <td class="tdCountsAcoes zebra"><?php
		    echo anchor('web/edit_count/'.$k->co_codigo,'<img style="margin-left: 0px;" src="'.base_url().'/img/editar_count.png" title="Editar essa Count" />');
		    echo anchor('web/invites/'.$k->co_codigo,' <img src="'.base_url().'img/convidar_amigos.png" title="Enviar Convites" />');
		    //echo anchor('web/estatisticas/'.$k->co_codigo,'<img src="'.base_url().'/img/stats_count.png" title="Ver as Estatísticas dessa TIP" />');
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