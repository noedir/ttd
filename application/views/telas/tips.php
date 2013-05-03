<div id="container">
    <div id="tip-container">
	<div class="tip-box iphone">
	    <canvas id="canvas" width="640" height="570"></canvas>
	    <div id="tela_fundo"></div>
	    <div class="tela">
		<div id="tela"></div>
		<p id="num_tip"></p>
		<p id="tit_tip"></p>
		<p id="sub_tip"></p>
		<p id="men_tip"></p>
	    </div>
	    <div class="menu_foto">
		<div id="triggerSelect">
		    Opções<br />de Foto
		</div>
		<div id="baseDock">
		    <div id="metodosUpload">        
			<div class="upload" id="uploadpc"><input type="hidden" value="s" name="central" id="optimg">Computador</div>
			<?php
			if(!is_numeric($instagram)){
			    echo '<div class="instagram"><a class="white" href="https://api.instagram.com/oauth/authorize/?client_id=4df5f47cf2fa4da98b0d0f91beb158fb&redirect_uri='.base_url().'auth/token&response_type=code">Instagram</a></div>';
			}else{
			    echo '<div class="instagram" id="pega_instagram">Instagram</div>';
			}
			?>
			<div class="ajustar" id="ajuste_automatico">Ajustar</div>
			<div class="barraup"><progress value="0" max="100"></progress><span id="porcentagem">0%</span></div>
		    </div>
		</div>
		
		<?php
		// DESATIVADO TEMPORARIAMENTE
		//<img id="get_up" width="256" src="<?php echo base_url(); tips/no_image.jpg">
		/*if(count($oauth) > 0 && $oauth[0]['oa_facebook_access_token'] != ''){
		    echo '<div class="facebook" id="pega_facebook"><img src="'.base_url().'img/facebook.png"></div>';
		}else{
		    echo '<div class="facebook"><a href="'.base_url().'facebook?inv='.current_url().'"><img src="'.base_url().'img/facebook.png"></a></div>';
		}
		if(count($oauth) > 0 && $oauth[0]['oa_instagram_access_token'] != ''){
		    echo '<div class="instagram" id="pega_instagram"><img src="'.base_url().'img/up_instagram.png"></div>';
		}else{
		    echo '<div class="instagram"><a href="https://api.instagram.com/oauth/authorize/?client_id=4df5f47cf2fa4da98b0d0f91beb158fb&redirect_uri='.base_url().'auth/token&response_type=code"><img src="'.base_url().'img/up_instagram.png"></a></div>';
		}
		 * 
		 */
		?>
		<div id="FileUpload">
		    <form id="formtip" action="<?php echo base_url(); ?>web/img_upload" method="post" enctype="multipart/form-data">
			<input type="file" name="imagem" style="width: 0px;" id="file-up">
			<div id="BrowserVisible">
			    <input type="text" id="FileField" style="width: 50px;" name="foto">
			</div>
		    </form>
		</div>
		
	    </div>
	</div>
	<div class="tip-box" style="width: 330px;">
	    <p class="thin" style="margin-bottom: 5px;">Projeto: <span class="thinbold"><?php echo $count[0]->co_titulo; ?></span></p>
	    <p class="esconde" id="ntip"></p><br>
	    <input type="hidden" name="count" value="<?php echo $count[0]->co_codigo; ?>">
	    <input type="hidden" name="codigo_tip" id="codigo_tip">
	    <input type="hidden" id="posicao" name="posicao" value="0/0">
	    <input type="hidden" id="suporta" value="s">
	    <p class="esconde"><input class="campos" maxlength="16" placeholder="Título (Máximo 16 caracteres)" type="text" name="titulo" id="tit"></p><br>
	    <p class="esconde"><input class="campos" maxlength="32" placeholder="Sub-título (Máximo 32 caracteres)" type="text" name="subtitulo" id="sub"></p><br>
	    <p class="esconde"><textarea class="campos" maxlength="2000" placeholder="Descrição (Máximo 2000 caracteres)" wrap="hard" rows="15" cols="30" name="descricao" id="men"></textarea></p>
	    <br>
	    <p class="esconde">
		<input type="hidden" value="n" id="mudou">
		<button id="addtip" type="submit" class="esconde">Salvar</button>
		<button id="cantip" type="cancel" class="esconde">Cancelar</button>
		<img src="<?php echo base_url(); ?>img/ajax-loader.gif" id="loader">
	    </p>
	</div>
	<div class="tip-maior">
	    <h5 class="projetoTitle">Capa da Contagem</h5>
	    <div id="opcoes_capa">
		    <ul>
			<li id="computador">Computador</li>
			<!-- <li id="get_facebook">Facebook</li> -->
			<?php
			if(!is_numeric($instagram)){
			    echo '<li class="instagram"><a class="white" href="https://api.instagram.com/oauth/authorize/?client_id=4df5f47cf2fa4da98b0d0f91beb158fb&redirect_uri='.base_url().'auth/token&response_type=code">Instagram</a></li>';
			}else{
			    echo '<li id="get_instagram">Instagram</li>';
			}
			?>
			<li class="oculto" id="redimensionar">Ajustar</li>
			<li class="oculto" id="salvar">Salvar</li>
		    </ul>
		</div>
	    <div class="capa" title="Clique para mais opções">
		<form id="formcapa" action="<?php echo base_url(); ?>web/img_upload/capa" method="post" enctype="multipart/form-data">
		    <input type="hidden" name="optimgc" id="optimgc" value="n">
		    <div id="FileUploadc">
			<input type="file" name="imagem" id="file-capa">
			<div id="BrowserVisiblec">
			    <input type="text" id="FileFieldc" style="width: 0px; cursor: pointer;" name="foto">
			    <input type="hidden" id="cod_count" name="cod_count" value="<?php echo $count[0]->co_codigo; ?>">
			</div>
		    </div>
		    <canvas id="canvascapa" width="640" height="200"></canvas>
		    <div id="telinha" class="controle_capa <?php if($this->uri->segment(3) == 'ret'){ echo 'retcapa'; } ?>">
			<?php if($count[0]->co_capa != '' && $count[0]->co_capa != 'escolher_foto_capa.png'){
			    $opc = 'width="320" height="100"';
			}else{
			    $opc = 'style="margin-top: 25px; cursor: pointer;"';
			}
			?>
			<img <?php echo $opc; ?> src="<?php echo base_url().'capa/'.$count[0]->co_capa; ?>">
		    </div>
		</form>
	    </div>
	    <div class="tags_projeto">
		<input type="text" name="tags" id="temas" class="hastags" data-codigo="<?php echo $count[0]->co_codigo; ?>" value="<?php echo $count[0]->co_tags; ?>">
	    </div>
	    
	    <!-- MOSTRA A DATA DE INÍCIO E FIM DO PROJETO -->
	    <div class="dt_projeto">
		<?php if($count[0]->co_data_inicio == '' || $count[0]->co_data_inicio == '0000-00-00'){ ?>
		<?php echo form_open('web/gravadata'); ?>
		<p>Início do projeto: <input size="15" type="text" id="calendario" name="calendario"><input type="hidden" name="cd_count" value="<?php echo $count[0]->co_codigo; ?>"><input type="hidden" name="dias_count" value="<?php echo $count[0]->co_dias; ?>"> <button type="submit" id="ok_data">OK</button></p>
		<?php echo form_close(); ?>
		<?php }else{ ?>
		<span class="inidata">Início: <?php echo date("d/m/Y", strtotime($count[0]->co_data_inicio)); ?></span><span class="fimdata">Término: <?php echo date("d/m/Y", strtotime($count[0]->co_data_inicio." + ".($count[0]->co_dias - 1)." days")); ?></span>
		<?php } ?>
	    </div>
	    <!-- FIM DA DATA -->
	    <br><br><br>
	    <h5 class="projetoTitle">Calendário de Tips</h5>
	    <?php if($count[0]->co_data_inicio == '' || $count[0]->co_data_inicio == '0000-00-00'){ ?>
	    <p class="sdt_projeto">Escolha a data de início da count para gerenciar as Tips.</p>
	    <?php }else{ ?>
	    <div class="box-tip">
		<?php
		$c = 1;
		foreach($tips as $t){
		    if($t->ti_data_mostra <= date("Y-m-d")){
			$dis = 'data-disabled="yes"';
			$txt = 'Tip antiga';
		    }else{
			$dis = 'data-disabled="no"';
			$txt = 'Clique para editar essa tip';
		    }
		    ?>
		<div title="<?php echo $txt; ?>" class="mozaico poshy" id="tip_<?php echo $t->ti_codigo ;?>" <?php echo $dis; ?> data-central="<?php echo $t->ti_imgcentral; ?>" data-mostra="<?php echo date("d/m/Y", strtotime($t->ti_data_mostra)); ?>" data-codigo="<?php echo $t->ti_codigo; ?>" data-tip="<?php echo $c; ?>" data-dias="<?php echo $count[0]->co_dias; ?>" data-titulo="<?php echo $t->ti_titulo; ?>" data-sub="<?php echo $t->ti_subtitulo; ?>" data-descricao="<?php echo $t->ti_descricao; ?>" data-imagem="<?php
		if($t->ti_imagem != ''){
		    echo $t->ti_imagem;
		}else{
		    echo 'no_image.jpg';
		}
		?>">
		    <img width="100" src="<?php
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
	</div>
    </div>
</div>