<?php
$ave = '';
$avs = '';
$eme = strpos(form_error('email'),'required');
$emv = strpos(form_error('email'),'valid');

if(form_error('email')){
    if($eme === false && $emv === false){
    }else{
	if($eme === false){
	    $ave = '<small class="red">Esse email não é válido</small>';
	}else{
	    $ave = '<small class="red">O email é necessário</small>';
	}
    }
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
		<p><label>Email<br><input type="text" size="16" name="email" placeholder="<?php echo $this->lang->line('pt_pemail'); ?>" autofocus="autofocus" value="<?php echo set_value('email');?>"> <?php echo $ave; ?></label></p>
	    </div>
	    <div class="formNovoCount">
		<button type="submit">Enviar</button>
	    </div>
	</div>
    </form>
</div>