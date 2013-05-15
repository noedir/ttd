<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE9"/>
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery-ui-1.10.3.custom.css">
	<script src="<?php echo base_url(); ?>js/load-image.min.js" type="text/javascript"></script>
	<!--[if IE]><script src="<?php echo base_url(); ?>js/excanvas.js" type="text/javascript"></script><![endif]-->
        <script src="<?php echo base_url(); ?>js/jquery-1.7.2.min.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery.html5-placeholder-shim.js"></script>
        <script src="<?php echo base_url(); ?>js/jquery-ui-1.8.21.custom.min.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery.mousewheel.min.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery.mCustomScrollbar.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery.form.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery.tagsinput.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>js/jquery.prettyPhoto.js" type="text/javascript"></script>
        <?php
	$bro = '';
	$scrp = '';
	if($this->uri->segment(2) == 'tips'){
	    switch($browser['browser']){
		case "IE":
		    if($browser['version'] < 10){
			$bro = '2';
			$scrp = '<script type="text/javascript" src="'.base_url().'js/ajaxupload3.5.3.js"></script>';
		    }
		break;
	    
		case "Safari":
		    if($browser['version'] < 6){
			$bro = '2';
			$scrp = '<script type="text/javascript" src="'.base_url().'js/ajaxupload3.5.3.js"></script>';
		    }
		break;
	    }
	}
        ?>
	<script src="<?php echo base_url(); ?>js/funcoes<?php echo $bro; ?>.js" type="text/javascript"></script>
	<?php echo $scrp;?>
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/prettyPhoto.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/estilo.css">
        <title><?php echo $title; ?></title>
    </head>
    <body>
	<div id="fb-root"></div>
	<div id="ur" data-url="<?php echo base_url(); ?>"></div>
	<div id="pagina">
	    <div id="close"></div>
	    <img id="loadering" src="<?php echo base_url(); ?>img/loader.gif">
	    <div class="pag"></div>
	</div>
	<div id="fundo_box"></div>
	<div id="baseHeader">
	    <div id="baseMenuGeral">
                <div id="baseLogo"><?php echo anchor(index_page(),'<img title="'.$this->config->item('title_page').'" alt="Logotipo '.$this->config->item('title_page').'" src="'.base_url().'img/logotipo_header.jpg" />','class="white"'); ?></div>
		<ul>
		    <li><?php echo anchor('web/quem_somos','Quem Somos','class="white"'); ?></li>
		    <li><?php echo anchor('web/politica','Política de Privacidade','class="white"'); ?></li>
		    <li><?php echo anchor('web/contato','Contato','class="white"'); ?></li>
		    <?php if($this->session->userdata("us_nome") == ''){ ?>
		    <li><?php echo anchor('web/login',$this->lang->line('pt_tlogin'),'class="white"'); ?></li>
                    <li style="margin-right: 0px;"><?php echo anchor('web/criar_projeto',$this->lang->line('pt_tcriar'),'class="white"'); ?></li>
		    <?php }else{ ?>
		    <li><?php echo anchor('web/atualiza_dados',$this->lang->line('pt_tatualiza'),'class="white"'); ?></li>
		    <li style="margin-right: 0px;"><?php echo anchor('web/sair',$this->lang->line('pt_tlogout'),'class="white"'); ?></li>
		    <?php } ?>
		</ul>    
            </div>
        </div>
	<div id="clr"></div>
	
	<?php if($this->session->userdata("us_nome") != ''){ ?>
	<div id="baseBarraAdmin">	    
        	<div id="baseMenuAdmin">
                <div class="settingsTick"></div>
                <div class="welcome">
		    <?php echo "Bem vindo, ".$this->session->userdata('nomecurto'); ?>
                </div>
                <div class="linksAdmin">
		    <ul>
                        <li><?php echo anchor('web/counts',$this->lang->line('pt_tminhas'),'class="white"'); ?></li>
                        <li><?php echo anchor('web/criar_novo_projeto',$this->lang->line('pt_tnovo'),'class="white"'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
	<?php } ?>
	<?php if($bro === '2'){ ?>
	    <div id="aviso">
		<span class="fechar" style='display: none;'>x</span>
		<p class="aviso">
		    <span class="fechar">x</span>
		    As versões antigas do SAFARI e IE não foram desenvolvidas para suportar a tecnologia da nossa plataforma!<br>Aconselhamos utilizar as últimas versões disponíveis do seu browser favorito!
		</p>
	    </div>
	    <div id="clr"></div>
	<?php } ?>
        <div id="tudo">