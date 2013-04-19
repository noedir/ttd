<div id="container">
    <div id="tip-container">
	<h3><?php echo $count[0]->co_titulo; ?></h3>
	<div class="tip-box iphone">
	    <div id="tela_fundo"></div>
	    <div class="tela">
		<div id="tela"><img id="get_up" width="219" src="<?php echo base_url(); ?>tips/no_image.jpg"></div>
		<p id="num_tip"></p>
		<p id="tit_tip"></p>
		<p id="sub_tip"></p>
		<p id="men_tip"></p>
	    </div>
	    <div id="ajuste_automatico"></div>
	    <div class="menu_foto">
		<?php if(count($oauth) > 0 && $oauth[0]['oa_facebook_access_token'] != ''){
		    echo '<div class="facebook" id="pega_facebook"><img src="'.base_url().'img/facebook.png"></div>';
		}else{
		    echo '<div class="facebook"><a href="'.base_url().'facebook?inv='.current_url().'"><img src="'.base_url().'img/facebook.png"></a></div>';
		}
		if(count($oauth) > 0 && $oauth[0]['oa_instagram_access_token'] != ''){
		    echo '<div class="instagram" id="pega_instagram"><img src="'.base_url().'img/up_instagram.png"></div>';
		}else{
		    echo '<div class="instagram"><a href="https://api.instagram.com/oauth/authorize/?client_id=4df5f47cf2fa4da98b0d0f91beb158fb&redirect_uri='.base_url().'auth/token&response_type=code"><img src="'.base_url().'img/up_instagram.png"></a></div>';
		}
		?>
		<div id="uploadpc"><input type="hidden" value="s" name="central" id="optimg"><img src="<?php echo base_url(); ?>/img/upload_icon.png"></div>
		<div id="FileUpload">
		    <form id="formtip" action="<?php echo base_url(); ?>web/img_upload" method="post" enctype="multipart/form-data">
			<input type="file" name="imagem" style="width: 0px;" id="BrowserHidden">
			<div id="BrowserVisible">
			    <input type="text" id="FileField" style="width: 50px;" name="foto">
			</div>
		    </form>
		</div>
		<div id="telinha"></div>
	    </div>
	</div>
	<div class="tip-box">
	    <h3 class="esconde">Edição de Tips</h3>
	    <p class="esconde" id="ntip"></p>
	    <input type="hidden" name="count" value="<?php echo $count[0]->co_codigo; ?>">
	    <input type="hidden" name="codigo_tip" id="codigo_tip">
	    <input type="hidden" id="posicao" name="posicao" value="0/0" />
	    <p class="esconde"><label>Título:<br><input maxlength="16" placeholder="Limite de 16 caracteres" type="text" name="titulo" id="tit"></label></p>
	    <p class="esconde"><label>Sub título:<br><input maxlength="32" placeholder="Limite de 32 caracteres" type="text" name="subtitulo" id="sub"></label></p>
	    <p class="esconde"><label>Descrição:<br><textarea maxlength="2000" placeholder="Limite de 2000 caracteres" wrap="hard" rows="10" cols="30" name="descricao" id="men"></textarea></label></p>
	    <button id="addtip" type="submit" class="esconde">Salvar</button>
	    <button id="cantip" type="cancel" class="esconde">Cancelar</button>
	</div>
	<div class="tip-maior">
	    <h3>Painel de Controle</h3>
	    <h5>Capa da Contagem</h5>
	    <div class="capa" title="Clique para escolher uma foto para esse count">
		<form id="formcapa" action="<?php echo base_url(); ?>web/img_upload/capa" method="post" enctype="multipart/form-data">
		    <div id="FileUploadc">
			<input type="file" name="imagem" style="width: 335px; height: 120px; cursor: pointer;" id="BrowserHiddenc">
			<div id="BrowserVisiblec">
			    <input type="text" id="FileFieldc" style="width: 0px; cursor: pointer;" name="foto">
			    <input type="hidden" id="cod_count" name="cod_count" value="<?php echo $count[0]->co_codigo; ?>">
			</div>
		    </div>
		    <div id="telinha">
			<?php if($count[0]->co_capa !== '' || $count[0]->co_capa != 'no_capa.png'){ ?>
			    <img src="<?php echo base_url().'capa/'.$count[0]->co_capa; ?>" width="335" height="120">
			<?php } ?>
		    </div>
		</form>
	    </div>
	    <div class="tags_projeto">
		<input type="text" name="tags" id="temas" class="hastags" data-codigo="<?php echo $count[0]->co_codigo; ?>" value="<?php echo $count[0]->co_tags; ?>">
	    </div>
	    <div class="dt_projeto">
		<?php if($count[0]->co_data_inicio == '' || $count[0]->co_data_inicio == '0000-00-00'){ ?>
		<?php echo form_open('web/gravadata'); ?>
		<p>Início do projeto: <input size="15" type="text" id="calendario" name="calendario"><input type="hidden" name="cd_count" value="<?php echo $count[0]->co_codigo; ?>"><input type="hidden" name="dias_count" value="<?php echo $count[0]->co_dias; ?>"> <button type="submit" id="ok_data">OK</button></p>
		<?php echo form_close(); ?>
		<?php }else{ ?>
		    <p>Início: <?php echo date("d/m/Y", strtotime($count[0]->co_data_inicio)); ?> - Término: <?php echo date("d/m/Y", strtotime($count[0]->co_data_inicio." + ".($count[0]->co_dias - 1)." days")); ?></p>
		<?php } ?>
	    </div>
	    <h5>Calendário de Tips</h5>
	    <?php if($count[0]->co_data_inicio == '' || $count[0]->co_data_inicio == '0000-00-00'){ ?>
	    <p class="sdt_projeto">Escolha a data de início da count para gerenciar as Tips.</p>
	    <?php }else{ ?>
	    <div class="box-tip">
		<?php
		$c = 1;
		foreach($tips as $t){
		    if($t->ti_data_mostra <= date("Y-m-d")){
			$dis = 'data-disabled="yes"';
		    }else{
			$dis = 'data-disabled="no"';
		    }
		    ?>
		<div class="mozaico" <?php echo $dis; ?> data-central="<?php echo $t->ti_imgcentral; ?>" data-mostra="<?php echo date("d/m/Y", strtotime($t->ti_data_mostra)); ?>" data-codigo="<?php echo $t->ti_codigo; ?>" data-tip="<?php echo $c; ?>" data-dias="<?php echo $count[0]->co_dias; ?>" data-titulo="<?php echo $t->ti_titulo; ?>" data-sub="<?php echo $t->ti_subtitulo; ?>" data-descricao="<?php echo $t->ti_descricao; ?>" data-imagem="<?php
		if($t->ti_imagem != ''){
		    echo $t->ti_imagem;
		}else{
		    echo 'no_image.jpg';
		}
		?>">
		    <img title="Clique para editar essa tip" width="100" src="<?php
		    if($t->ti_imagem != ''){
			echo base_url().'tips/thumb_'.$t->ti_imagem;
		    }else{
			echo base_url().'img/tip_add_box.png';
		    }
		    ?>">
		    <div class="fundo">
			<strong><?php echo $c."/".$totaltips; ?></strong>
		    </div>
		</div>
		<?php
		$c++;
		} ?>
	    </div>
	    <?php } ?>
	    <div class="invite">
		<h5><?php if($count[0]->co_privado == 's'){ echo anchor('web/invites/'.$count[0]->co_codigo,'Invites','class="black"').' |'; } ?> <?php echo anchor('web/estatisticas/'.$count[0]->co_codigo,'Estatísticas','class="black"'); ?></h5>
	    </div>
	</div>
    </div>
</div>