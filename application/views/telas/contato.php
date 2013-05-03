<?php
$ave = '';
$avn = '';
$ava = '';
$avm = '';
$eme = strpos(form_error('email_contato'),'required');
$emv = strpos(form_error('email_contato'),'valid');

if(form_error('email_contato')){
    if($eme === false && $emv === false){
    }else{
	if($eme === false){
	    $ave = '<small><small>Esse email não é válido</small></small>';
	}else{
	    $ave = '<small><small>O email é necessário</small></small>';
	}
    }
}

$nov = strpos(form_error('nome_contato'),'required');
if($nov === false){
}else{
    $avn = '<small><small>O nome é necessário</small></small>';
}

$asv = strpos(form_error('assunto_contato'),'required');
if($asv === false){
}else{
    $ava = '<small><small>O assunto é necessário</small></small>';
}

$mev = strpos(form_error('nome_contato'),'required');
if($mev === false){
}else{
    $avm = '<small><small>A mensagem é necessária</small></small>';
}
?>
<div id="container">
    <form id="formcad" method="post" action="<?php echo current_url(); ?>">
	<div class="baseNovoCount">
	    <?php
	    if($erro != ''){
		echo $erro;
	    }
	    ?>
	    <h3>Entre em contato conosco</h3>
	    <div class="formNovoCount">
		<p><label>Nome<br><input type="text" size="45" name="nome_contato" placeholder="Digite seu nome"><?php echo $avn; ?></label></p>
	    </div>
	    <div class="formNovoCount">
		<p><label>Email<br><input type="text" size="45" name="email_contato" placeholder="Digite seu email"><?php echo $ave; ?></label></p>
	    </div>
	    <div class="formNovoCount">
		<p><label>Assunto<br><input type="text" size="45" name="assunto_contato" placeholder="Digite o assunto"><?php echo $ava; ?></label></p>
	    </div>
	    <div class="formNovoCount">
		<p><label>Mensagem<br><textarea rows="5" cols="41" name="mensagem_contato" placeholder="Descreva sua mensagem"></textarea><?php echo $avm; ?></label></p>
	    </div>
	    <div class="formNovoCount">
		<p><button type="submit" id="ok_contato">Enviar</button></p>
		<img class="loader" src="<?php echo base_url(); ?>img/ajax-loader.gif">
	    </div>
	</div>
    </form>
</div>