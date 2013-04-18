<?php
$uniq = strpos(form_error('email_usuario'),'unique');
$req = strpos(form_error('email_usuario'),'required');
$av = '';
if(form_error('email_usuario')){
    if($req === false && $uniq === false){
    }else{
	if($req === false){
	    $av = '<small><small>Esse email já está cadastrado no sistema</small></small>';
	}else{
	    $av = '<small><small>Email é obrigatório</small></small>';
	}
    }
}
$confir  = strpos(form_error('confirma_senha'),'match');
$avcs = '';
if(form_error('confirma_senha')){
    if($confir === false){
    }else{
	$avcs = '<small><small>Confirmação de senha diferente da senha</small></small>';
    }
}

$idu = strpos(form_error('nomeunico'),'unique');
$idv = strpos(form_error('nomeunico'),'required');
if(form_error('nomeunico')){
    if($idu === false && $idv === false){
    }else{
	if($idu === false){
	    $avi = '<small><small>O Identificador é obrigatório</small></small>';
	}else{
	    $avi = '<small><small>Já existe um  Identificador com esse nome.</small></small>';
	}
    }
}else{
    $avi = '';
}

?>
<div id="container">
    <form id="formcad" method="post" action="<?php echo current_url(); ?>">
	<legend>Dados do Usuário</legend>
	<p><label>Nome:*<br><input type="text" size="45" <?php if(form_error('nome_usuario')){ echo 'style="border: 1px solid #900" placeholder="Nome é obrigatório"'; } ?> name="nome_usuario" value="<?php echo set_value('nome_usuario'); ?>"></label></p>
        <p><label>Email:*<br><input type="text" size="45" <?php if(form_error('email_usuario')){ echo 'style="border: 1px solid #900" placeholder="Email é obrigatório"'; } ?> name="email_usuario" value="<?php echo set_value('email_usuario'); ?>"> <?php echo $av; ?></label></p>
	<p><label>Gênero:<br><select name="gen_usuario">
		    <option value="m">Masculino</option>
		    <option value="f">Feminino</option>
		    <option value="c">Corporativo</option>
		</select></label></p>
        <p><label>Senha:*<br><input type="password" size="45" <?php if(form_error('senha_usuario')){ echo 'style="border: 1px solid #900" placeholder="Senha é obrigatória"'; } ?> name="senha_usuario" value="<?php echo set_value('senha_usuario'); ?>"></label></p>
        <p><label>Confirma Senha:*<br><input type="password" size="45" <?php if(form_error('confirma_senha')){ echo 'style="border: 1px solid #900" placeholder="Obrigatória e deve ser igual à senha"'; } ?> name="confirma_senha" value="<?php echo set_value('confirma_senha'); ?>"> <?php echo $avcs; ?></label></p>
	<p>&nbsp;</p>
	<legend>Dados do Projeto</legend>
        <p><label>Nome do Projeto:*<br><input type="text" size="45" <?php if(form_error('nome_projeto')){ echo 'style="border: 1px solid #900" placeholder="Nome do projeto é obrigatório"'; } ?> name="nome_projeto" value="<?php echo set_value('nome_projeto'); ?>"></label></p>
        <p><label>Ocasião:*<br><input type="text" size="45" <?php if(form_error('ocasiao_projeto')){ echo 'style="border: 1px solid #900" placeholder="Ocasião do projeto é obrigatório"'; } ?> name="ocasiao_projeto" value="<?php echo set_value('ocasiao_projeto'); ?>"></label></p>
        <p><label>Dias:*<br><input type="text" size="45" <?php if(form_error('dias_projeto')){ echo 'style="border: 1px solid #900" placeholder="Dias do projeto é obrigatório"'; } ?> name="dias_projeto" id="dias_projeto" value="<?php echo set_value('dias_projeto'); ?>"></label></p>
	<p><label>Evento:</label>&nbsp;<label>Privado <input type="radio" checked="checked" name="privado" value="s"></label>&nbsp;&nbsp;<label>Público <input type="radio" name="privado" value="n"></label></p>
	<input type="hidden" name="valor_projeto" id="vlr_proj" value="">
        <div id="resultado_dias"></div>
	<p><label>Identificador dessa Count:*<br><input type="text" <?php if(form_error('nomeunico')){ echo 'style="border: 1px solid #900" placeholder="Identificador é obrigatório e deve ser único"'; } ?> onblur='retornaValor(this.name)' name="nomeunico" id="nomeunico" value="<?php echo set_value('nomeunico'); ?>"><input type="hidden" name="unique" value="v"> <?php echo $avi; ?></label></p>
        <p><button type="submit">Cadastrar</button></p>
    </form>
</div>