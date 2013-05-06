<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/cupertino/jquery-ui.css">
	<script src="<?php echo base_url(); ?>js/load-image.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>js/jquery-1.7.2.min.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery.html5-placeholder-shim.js"></script>
        <script src="<?php echo base_url(); ?>js/jquery-ui-1.8.21.custom.min.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery.mousewheel.min.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery.mCustomScrollbar.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery.form.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery.tagsinput.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>js/jquery.prettyPhoto.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>js/funcoes.js" type="text/javascript"></script>
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
                <div id="baseLogo"><?php echo anchor(index_page(),'<img title="TilTheDay" alt="Logotipo TilTheDay" src="'.base_url().'img/logotipo_header.jpg" />','class="white"'); ?></div>
		<ul>
		    <li><?php echo anchor('web/quem_somos','Quem Somos','class="white"'); ?></li>
		    <li><?php echo anchor('web/politica','Política de Privacidade','class="white"'); ?></li>
		    <li><?php echo anchor('web/contato','Contato','class="white"'); ?></li>
		    <?php if($this->session->userdata("us_nome") == ''){ ?>
		    <li><?php echo anchor('web/login',$this->lang->line('pt_tlogin'),'class="white"'); ?></li>
                    <li style="margin-right: 0px;"><?php echo anchor('web/criar_projeto',$this->lang->line('pt_tcriar'),'class="white"'); ?></li>
		    <?php }else{ ?>
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
                        <li><?php echo anchor('web/atualiza_dados',$this->lang->line('pt_tatualiza'),'class="white"'); ?></li>
                        <li><?php echo anchor('web/counts',$this->lang->line('pt_tminhas'),'class="white"'); ?></li>
                        <li><?php echo anchor('web/criar_novo_projeto',$this->lang->line('pt_tnovo'),'class="white"'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
	<?php } ?>
        <div id="clr"></div>
        <div id="tudo">