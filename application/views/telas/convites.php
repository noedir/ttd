<div id="container">
    <h3>Convidar Amigos <small>(<?php echo $counts[0]->co_titulo; ?>)</small></h3>
    <div id="tabs">
	<ul>
	    <li><a href="#tabs-1"><img src="<?php echo base_url(); ?>img/envelope.png"> E-mail</a></li>
	    <li><a href="#tabs-2" id="convida_face" data-volta="<?php echo $this->uri->segment(4); ?>" data-count="<?php echo $this->uri->segment(3); ?>"><img src="<?php echo base_url(); ?>img/fa.png"> Facebook</a></li>
	</ul>
	
	<div id="tabs-1">
	    <?php if($this->session->flashdata('total') != ''){ ?>
	    <p><?php echo $this->session->flashdata('total'); ?></p>
	    <?php } ?>
	    <?php echo form_open('web/grava_convite'); ?>
	    <input type="hidden" name="count" value="<?php echo $this->uri->segment(3); ?>" />
	    <p><label>Digite os emails dos amigos que quer convidar para este count:<br><textarea name="amigos" cols="30" rows="10" placeholder="Dica: Separe os e-mails com vírgulas" class="friends" id="amigos"></textarea></label></p>
	    <p><button type="submit">Enviar Convites</button>  <button data-id="<?php echo $this->uri->segment(3); ?>" id="ok_volta" type="cancel">Voltar</button></p>
	    <?php echo form_close(); ?>
	</div>
	<div id="tabs-2">
	    <br>
	    <?php if($appID == ''){ ?>
	    <div id="lista_facebook"></div>
	    <?php }else{ ?>
	    <a href="<?php echo $facebook.'?inv='.current_url(); ?>"><img src="<?php echo base_url(); ?>img/oauth_facebook.jpg"></a>
	    <?php } ?>
	</div>
    </div>
</div>
