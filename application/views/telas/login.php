<?php
$ave = '';
$avs = '';
$eme = strpos(form_error('email'),'required');
$emv = strpos(form_error('email'),'valid');

if(form_error('email')){
    if($eme === false && $emv === false){
    }else{
	if($eme === false){
	    $ave = '<small><small>Esse email não é válido</small></small>';
	}else{
	    $ave = '<small><small>O email é necessário</small></small>';
	}
    }
}

$sev = strpos(form_error('senha'),'required');
if($sev === false){
}else{
    $avs = '<small><small>A senha é necessária</small></small>';
}
?>
<div id="container">
    <form method="post" id="form_login" action="<?php echo base_url().'web/login'?>">
    <div class="baseNovoCount">
	<?php
	if($erro != ''){
	    echo $erro;
	}
	?>
	<div class="formNovoCount">
	    <p><label><?echo $this->lang->line('pt_temail')?>: <input type="text" size="16" name="email" placeholder="<?php echo $this->lang->line('pt_pemail'); ?>" autofocus="autofocus" value="<?php echo set_value('email');?>"> <?php echo $ave; ?></label></p>
	</div>
	    <p><label><?php echo $this->lang->line('pt_tsenha'); ?>: <input type="password" size="16" name="senha" placeholder="<?php echo $this->lang->line('pt_psenha'); ?>" value="<?php echo set_value('senha'); ?>"> <?php echo $avs; ?></label></p>
	    <button type="submit"><?php echo $this->lang->line('pt_bentrar'); ?></button>
	</form>
    </div>
</div>