<?php
$uniq = strpos(form_error('email_usuario'),'unique');
$req = strpos(form_error('email_usuario'),'required');
$av = '';
if(form_error('email_usuario')){
    if($req === false && $uniq === false){
    }else{
	if($req === false){
	    $av = '<small class="red">Esse email já está cadastrado no sistema</small>';
	}else{
	    $av = '<small class="red">Email é obrigatório</small>';
	}
    }
}

$diaq = strpos(form_error('dias_projeto'), 'numbers');
$diareq = strpos(form_error('dias_projeto'), 'required');
$avds = '';
if(form_error('dias_projeto')){
    if($diareq === false && $diaq === false){
    }else{
	if($diareq === false){
	    $avds = '<small class="red">Dias do Projeto precisa ser um número</small>';
	}else{
	    $avds = '<small class="red">Dias do Projeto é obrigatório</small>';
	}
    }
}

$snhma = strpos(form_error('senha_usuario'),'length');
$avss = '';
if(form_error('senha_usuario')){
    if($snhma === false){
    }else{
	$avss = '<small class="red">A senha precisa ter entre 6 e 15 caracteres</small>';
    }
}
$confir  = strpos(form_error('confirma_senha'),'match');
$avcs = '';
if(form_error('confirma_senha')){
    if($confir === false){
    }else{
	$avcs = '<small class="red">Confirmação de senha diferente da senha</small>';
    }
}
?>
<div id="container">
    <form id="formcad" method="post" action="<?php echo current_url(); ?>">
    <div class="baseNovoCount">
        
	<h3>Novo Usuário</h3>
        
                <div class="formNovoCount">
		    <label>Nome: <br><input type="text" size="45" <?php if(form_error('nome_usuario')){ echo 'style="border: 1px solid #900" placeholder="Nome é obrigatório"'; }else{ echo 'placeholder="Nome do Usuário"'; } ?> name="nome_usuario" value="<?php echo set_value('nome_usuario'); ?>"></label>
                </div>
		<div class="formNovoCount">
		    <label>Email: <br><input type="text" size="45" <?php if(form_error('email_usuario')){ echo 'style="border: 1px solid #900" placeholder="Email é obrigatório"'; }else{ echo 'placeholder="Email do Usuário"'; } ?> name="email_usuario" value="<?php echo set_value('email_usuario'); ?>"></label> <?php echo $av; ?>
                </div>
		
		<div class="formNovoCount">
		    <select name="gen_usuario">
			<option value="m">Masculino</option>
			<option value="f">Feminino</option>
			<option value="c">Corporativo</option>
		    </select>
                </div>
		
		<div class="formNovoCount">
		    <label>Senha: <br><input type="password" size="45" <?php if(form_error('senha_usuario')){ echo 'style="border: 1px solid #900" placeholder="Senha é obrigatória"'; }else{ echo 'placeholder="Digite uma senha"'; } ?> name="senha_usuario" value="<?php echo set_value('senha_usuario'); ?>"></label> <?php echo $avss; ?>
                </div>
		
		<div class="formNovoCount">
		    <label>Confirmação de senha: <br><input type="password" size="45" <?php if(form_error('confirma_senha')){ echo 'style="border: 1px solid #900" placeholder="Obrigatória e deve ser igual à senha"'; }else{ echo 'placeholder="Digite a confirmação da senha"'; } ?> name="confirma_senha" value="<?php echo set_value('confirma_senha'); ?>"></label> <?php echo $avcs; ?>
                </div>
        </div>
    
    
    <div class="baseNovoCount">
	<h3>Novo Count</h3>
                <div class="formNovoCount">
		    <label>Título: <br><input type="text" size="45" <?php if(form_error('nome_projeto')){ echo 'style="border: 1px solid #900" placeholder="Nome do projeto é obrigatório"'; }else{ echo 'placeholder="Nome do Projeto"'; } ?> name="nome_projeto" id="titulo_projeto" value="<?php echo set_value('nome_projeto'); ?>"></label>
                </div>
                
                <div class="formNovoCount">
		    <label>Ocasião: <br><input type="text" size="45" <?php if(form_error('ocasiao_projeto')){ echo 'style="border: 1px solid #900" placeholder="Ocasião do projeto é obrigatório"'; }else{ echo 'placeholder="Ocasião do Projeto"'; } ?> name="ocasiao_projeto" value="<?php echo set_value('ocasiao_projeto'); ?>"></label>
            </div>
                
                <div class="formNovoCountDias">
                    <label>Quantos dias terá seu projeto?
		    <br /><input type="text" <?php if(form_error('dias_projeto')){ echo 'style="width: 20%; border: 1px solid #900" placeholder="Dias do projeto é obrigatório"'; }else{ echo 'style="width: 20%;" placeholder="Dias do projeto"'; } ?> name="dias_projeto" id="dias_projeto" value="<?php echo set_value('dias_projeto'); ?>"></label>
                    <div id="baseValores">
			<input type="hidden" name="valor_projeto" id="vlr_proj" value="">
                    	<div class="startfee">
                            Start-Fee
                            <Br />R$ 1.00
                        </div>
                        <div class="cadaDia">
                            Cada dia
                            <Br />R$ 0.99
                        </div>
                        <div class="valorFinal">
                            Total
                            <Br /><div id="resultado_dias">R$ 0.00</div>
                        </div>
                    </div>
                </div><?php echo $avds; ?>
                <div class="clr"></div>
                <div class="formNovoCount">
                    <label>Visibilidade:</label><br><input type="radio" id="radio1" name="privado" value="s"><label for="radio1">Privado</label> <input type="radio" id="radio2" name="privado" checked="checked" value="n"><label for="radio2">Público</label>
                </div>
		
                <div class="formNovoCount">    
                    <label for="nomeunico">Identificador desse Projeto</label>
                    <br /><input type="text" placeholder="Identificador do Projeto" name="nomeunico" id="nomeunico" readonly="true" value="<?php echo set_value('nomeunico'); ?>"><input type="hidden" name="unique" value="v">
                </div>
		<p style="float: left; margin-top: 25px; font-size: 12px;">* Ao clicar em Criar Projeto, você confirma que<br>aceita os <strong>Termos e Condições de Uso</strong>.</p>
		<button style="float: right;" type="submit" class="criarProjeto">Criar Projeto</button>
        </div>
    </form>
</div>