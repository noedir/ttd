<?php
$nom = strpos(form_error('nome_usuario'),'required');
$avn = '';
$eme = strpos(form_error('email_usuario'), 'required');
$emv = strpos(form_error('email_usuario'), 'valid');
$ave = '';

if(form_error('nome_usuario')){
    if($nom === false){
    }else{
	$avn = '<small><small>O nome é obrigatório</small></small>';
    }
}

if(form_error('email_usuario')){
    if($eme === false && $emv === false){
    }else{
	if($eme === false){
	    $ave = '<small><small>O email é obrigatório</small></small>';
	}else{
	    $ave = '<small><small>Esse email não é válido</small></small>';
	}
    }
}

?>
<div id="container">
    <form id="formcad" method="post" action="<?php echo current_url(); ?>">
        <legend>Dados do Usuário</legend>
        <p><label>Nome:*<br><input type="text" size="45" name="nome_usuario" <?php if(form_error('nome_usuario')){ echo 'style="border: 1px solid #900" placeholder="Nome é obrigatório"'; } ?> value="<?php echo $this->session->userdata('us_nome'); ?>"> <?php echo $avn; ?></label></p>
        <p><label>Email:*<br><input type="text" size="45" name="email_usuario" <?php if(form_error('email_usuario')){ echo 'style="border: 1px solid #900" placeholder="Email é obrigatório"'; } ?> value="<?php echo $this->session->userdata('us_email'); ?>"> <?php echo $ave; ?></label></p>
	<p><label>Gênero:<br><select name="gen_usuario">
		    <option value="m" <?php echo set_select('gen_usuario', 'm', TRUE); ?>>Masculino</option>
		    <option value="f" <?php echo set_select('gen_usuario', 'f'); ?>>Feminino</option>
		    <option value="c" <?php echo set_select('gen_usuario', 'c'); ?>>Corporativo</option>
		</select></label></p>
        <p><label>Senha:<br><input type="password" size="45" <?php if(form_error('senha_usuario')){ echo 'style="border: 1px solid #900" placeholder="Senha é obrigatória"'; } ?> name="senha_usuario" placeholder="Caso não queira mudar, deixe em branco"></label></p>
	<input type="hidden" name="senhaatual" value="<?php echo $this->session->userdata('us_senha'); ?>">
        <p><button type="submit">Atualizar</button></p>
    </form>
</div>