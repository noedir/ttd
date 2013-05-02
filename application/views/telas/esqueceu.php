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
    <form method="post" id="form_login" action="<?php echo current_url(); ?>">
	<div class="baseNovoCount">
	    <h3>Esqueceu a Senha</h3>
	    <?php
	    if($erro != ''){
		echo $erro;
	    }
	    ?>
	    <div class="formNovoCount">
		<p><input type="text" size="16" name="email" placeholder="<?php echo $this->lang->line('pt_pemail'); ?>" autofocus="autofocus" value="<?php echo set_value('email');?>"> <?php echo $ave; ?></p>
	    </div>
	    <div class="formNovoCount">
		<button type="submit"><?php echo $this->lang->line('pt_benviar'); ?></button>
	    </div>
	</div>
    </form>
</div>