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
        <div class="baseNovoCount">
            <h3>Dados do Usuário</h3>
            <div class="formNovoCount">
                <label>Nome: <br><input type="text" size="45" name="nome_usuario" <?php if(form_error('nome_usuario')){ echo 'style="border: 1px solid #900" placeholder="Nome é obrigatório"'; }else{ echo 'placeholder="Nome do Usuário"'; } ?> value="<?php echo $this->session->userdata('us_nome'); ?>"></label> <?php echo $avn; ?>
            </div>
            
            <div class="formNovoCount">
		<label>Email: <br><input type="text" size="45" name="email_usuario" <?php if(form_error('email_usuario')){ echo 'style="border: 1px solid #900" placeholder="Email é obrigatório"'; }else{ echo 'placeholder="Email do Usuário"'; } ?> value="<?php echo $this->session->userdata('us_email'); ?>"></label> <?php echo $ave; ?>
            </div>
            
            <div class="formNovoCount">
                <select name="gen_usuario">
                    <option value="m" <?php echo set_select('gen_usuario', 'm', TRUE); ?>>Masculino</option>
                    <option value="f" <?php echo set_select('gen_usuario', 'f'); ?>>Feminino</option>
                    <option value="c" <?php echo set_select('gen_usuario', 'c'); ?>>Corporativo</option>
                </select>
            </div>
            <div class="formNovoCount">
                <label>Senha: <br><input type="password" size="45" <?php if(form_error('senha_usuario')){ echo 'style="border: 1px solid #900" placeholder="Senha é obrigatória"'; } ?> name="senha_usuario" placeholder="Caso não queira mudar, deixe em branco"></label>
            </div>
            <input type="hidden" name="senhaatual" value="<?php echo $this->session->userdata('us_senha'); ?>">
	    
	    <?php if(count($oauth) > 0 && is_numeric($oauth[0]['oa_instagram_id']) && $oauth[0]['oa_instagram_id'] > 0){ ?>
	    <?php echo anchor('web/sair_instagram','<img title="Sair do Instagram" style="float: left; margin-top: 15px;" src="'.base_url().'/img/up_instagram.png">'); ?>
	    <?php } ?>
	    <?php if($this->uri->segment(3) == 'ok'){ ?>
	    <iframe src="https://instagram.com/accounts/logout/" width="0" height="0"></iframe>
	    <?php } ?>
            <button type="submit" class="criarProjeto">Atualizar</button>
        </div>
    </form>
</div>