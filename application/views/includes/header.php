<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery-ui-1.8.21.custom.css">
        <script src="<?php echo base_url(); ?>js/jquery-1.7.2.min.js"></script>
        <script src="<?php echo base_url(); ?>js/jquery-ui-1.8.21.custom.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/imgareaselect-default.css" />
	<script type="text/javascript" src="<?php echo base_url(); ?>scripts/jquery.imgareaselect.pack.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery.form.js"></script>
	<script src="<?php echo base_url(); ?>js/jquery.tagsinput.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>js/funcoes.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>js/jquery.prettyPhoto.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>js/jquery.Jcrop.js" type="text/javascript"></script>
	
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/prettyPhoto.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/estilo.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.Jcrop.css">
        <title><?php echo $title; ?></title>
    </head>
    <body>
	<div id="ur" data-url="<?php echo base_url(); ?>"></div>
	<div id="pagina">
	    <div id="close"></div>
	    <img class="loader" src="<?php echo base_url(); ?>img/loader.gif">
	    <div class="pag"></div>
	</div>
	<div id="fundo_box"></div>
        <div id="tudo">
            <div id="topo">
		<div class="engrenagem"></div>
                <p class="left"><?php echo anchor('web',$this->lang->line('pt_thome'),'class="white"'); ?></p>
                <p class="left well"><span id="btn_idioma"><?php echo $this->lang->line('pt_tidioma'); ?></span></p>
		<div id="lang">
		    <span class="fechar">x</span>
		    <ul>
			<li class="lang" data-lang="pt"><img src="<?php echo base_url(); ?>img/pt.png"> Português</li>
			<li class="lang" data-lang="en"><img src="<?php echo base_url(); ?>img/en.jpg"> English</li>
		    </ul>
		</div>
		<?php if($this->session->userdata('us_nome') == ''){ ?>
		<p class="right"><?php echo anchor('web/login',$this->lang->line('pt_tlogin'),'class="white"'); ?></p>
		    <p class="right well"><?php echo anchor('web/criar_projeto',$this->lang->line('pt_tcriar'),'class="white"'); ?></p>
		<?php } ?>
            </div>
	    <div id="log">
            <?php if($this->session->userdata('us_nome') != ''){ ?>
		Master: <strong><?php echo $this->session->userdata('nomecurto'); ?></strong> | <?php echo anchor('web/atualiza_dados','<strong>'.$this->lang->line('pt_tatualiza').'</strong>','class="white"'); ?> | <?php echo anchor('web/counts','<strong>'.$this->lang->line('pt_tminhas').'</strong>','class="white"'); ?> | <?php echo anchor('web/criar_novo_projeto','<strong>'.$this->lang->line('pt_tnovo').'</strong>','class="white"'); ?> | <?php echo anchor('web/sair','<strong>'.$this->lang->line('pt_tlogout').'</strong>','class="white"'); ?>
            <?php }else{ ?>
		<?php echo $this->lang->line('pt_tola'); ?>, <strong><?php echo $this->lang->line('pt_tvisitante'); ?></strong>. <?php echo $this->lang->line('pt_tfaca'); ?> <span class="btn_login"><strong><?php echo anchor('web/login',$this->lang->line('pt_tlogin2'),'class="white"'); ?></strong></span> ou <?php echo anchor('web/criar_projeto','<strong>'.$this->lang->line('pt_tcrie').'</strong>','class="white"'); ?>.
	    <?php } ?>
	    </div>