<?php
if($this->uri->segment(4) != ''){
    if($this->uri->segment(4) == 'capa'){
	$dt = 'data-capa="'.$this->uri->segment(4).'"';
    }else{
	$tp = explode("_",$this->uri->segment(4));
	$dt = 'data-tip="'.$tp[0].'_'.$tp[1].'" data-capa="'.$tp[0].'"';
    }
    echo '<iframe id="louins" '.$dt.' src="https://instagram.com/accounts/logout/" width="0" height="0"></iframe>';
}
?>
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
		    <img style="margin-top: 3px; margin-right: 6px;" src="<?php echo base_url(); ?>img/ops_fotos.png" />
		</div>
		<div id="baseDock">
		    <div id="metodosUpload">        
			<div class="upload" id="uploadpc"><input type="hidden" value="s" name="central" id="optimg">Computador</div>
			<?php
			if(!is_numeric($instagram) || $instagram == 0){
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
	    <p style="padding:0px; margin:0px" class="esconde"><input class="campos" maxlength="16" placeholder="Título (Máximo 16 caracteres)" type="text" name="titulo" id="tit"></p><br>
	    <p style="padding:0px; margin:0px" class="esconde"><input class="campos" maxlength="32" placeholder="Sub-título (Máximo 32 caracteres)" type="text" name="subtitulo" id="sub"></p><br>
	    <p style="padding:0px; margin:0px" class="esconde"><textarea class="campos" maxlength="2000" placeholder="Descrição (Máximo 2000 caracteres)" wrap="hard" rows="25" cols="30" name="descricao" id="men"></textarea></p>
	    
	    <p style="width:343px" class="esconde">
		<input type="hidden" value="n" id="mudou">
		<button style="width: 100px;" id="addtip" type="submit" class="esconde">Salvar</button>
		<button style="width: 100px;" id="cantip" type="cancel" class="esconde">Cancelar</button>
		<button style="width: 100px; float:right;" id="cleantip" type="reset" class="esconde">Limpar Tip</button>
		<img src="<?php echo base_url(); ?>img/ajax-loader.gif" id="loader">
	    </p>
	</div>
	<div class="tip-maior">
	    <h5 class="projetoTitle">Capa da Count</h5>
	    <div class="barraupc"><progress value="0" max="100"></progress><span id="porcentagemc">0%</span></div>
	    <div id="opcoes_capa">
		    <ul>
			<li id="computador">Computador</li>
			<!-- <li id="get_facebook">Facebook</li> -->
			<?php
			if(!is_numeric($instagram) || $instagram == 0){
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
			<input style="top: -100000px;" type="file" name="imagem" id="file-capa">
			<div id="BrowserVisiblec">
			    <input type="text" id="FileFieldc" style="width: 0px; cursor: pointer;" name="foto">
			    <input type="hidden" data-dias="<?php echo $count[0]->co_dias; ?>" id="cod_count" name="cod_count" value="<?php echo $count[0]->co_codigo; ?>">
			</div>
		    </div>
		    <canvas id="canvascapa" width="640" height="200"></canvas>
		    <div id="telinha" class="controle_capa <?php if($this->uri->segment(3) == 'ret'){ echo 'retcapa'; } ?>">
			<?php if($count[0]->co_capa != '' && $count[0]->co_capa != 'escolher_foto_capa.png'){
			    $opc = 'width="320" height="100"';
			}else{
			    $opc = 'class="trava" style="margin-top: 25px; cursor: pointer;"';
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
		<div class="ini_data_projeto"><!--<span style="color: #999">Início do Projeto:</span><br />--><input size="30" type="text" placeholder="Início do Projeto" id="calendario" name="calendario"><input type="hidden" name="cd_count" value="<?php echo $count[0]->co_codigo; ?>"><input type="hidden" name="dias_count" value="<?php echo $count[0]->co_dias; ?>"> <button type="submit" class="ok_calendario" id="ok_data" style="width: 40px; padding: 9px; font-size: 10px; font-weight: bold;">OK</button></div>
		<?php echo form_close(); ?>
		<?php }else{ ?>
		<span class="inidata">Início: <?php echo date("d/m/Y", strtotime($count[0]->co_data_inicio)); ?></span>
		<span class="fimdata">Término: <?php echo date("d/m/Y", strtotime($count[0]->co_data_inicio." + ".($count[0]->co_dias - 1)." days")); ?></span>
		<?php if($count[0]->co_data_inicio > date("Y-m-d")){?>
		    <br><br><br>
		    <div class="altdata">Alterar Data<div id="altdata">&nbsp;</div></div>
		<?php }?>
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
			$txt = 'Tip do dia '.date("d/m/Y", strtotime($t->ti_data_mostra));
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