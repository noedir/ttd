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
    <div class="baseNovoCount">
        
	<h3>Novo Usuário</h3>
        
                <div class="formNovoCount">
		    <input type="text" size="45" <?php if(form_error('nome_usuario')){ echo 'style="border: 1px solid #900" placeholder="Nome é obrigatório"'; }else{ echo 'placeholder="Nome do Usuário"'; } ?> name="nome_usuario" value="<?php echo set_value('nome_usuario'); ?>">
                </div>
		<div class="formNovoCount">
		    <input type="text" size="45" <?php if(form_error('email_usuario')){ echo 'style="border: 1px solid #900" placeholder="Email é obrigatório"'; }else{ echo 'placeholder="Email do Usuário"'; } ?> name="email_usuario" value="<?php echo set_value('email_usuario'); ?>"> <?php echo $av; ?>
                </div>
		
		<div class="formNovoCount">
		    <select name="gen_usuario">
			<option value="m">Masculino</option>
			<option value="f">Feminino</option>
			<option value="c">Corporativo</option>
		    </select>
                </div>
		
		<div class="formNovoCount">
		    <input type="password" size="45" <?php if(form_error('senha_usuario')){ echo 'style="border: 1px solid #900" placeholder="Senha é obrigatória"'; }else{ echo 'placeholder="Digite uma senha"'; } ?> name="senha_usuario" value="<?php echo set_value('senha_usuario'); ?>">
                </div>
		
		<div class="formNovoCount">
		    <input type="password" size="45" <?php if(form_error('confirma_senha')){ echo 'style="border: 1px solid #900" placeholder="Obrigatória e deve ser igual à senha"'; }else{ echo 'placeholder="Digite a confirmação da senha"'; } ?> name="confirma_senha" value="<?php echo set_value('confirma_senha'); ?>"> <?php echo $avcs; ?>
                </div>
        </div>
    
    
    <div class="baseNovoCount">
	<h3>Novo Count</h3>
                <div class="formNovoCount">
		    <input type="text" size="45" <?php if(form_error('nome_projeto')){ echo 'style="border: 1px solid #900" placeholder="Nome do projeto é obrigatório"'; }else{ echo 'placeholder="Nome do Projeto"'; } ?> name="nome_projeto" value="<?php echo set_value('nome_projeto'); ?>">
                </div>
                
                <div class="formNovoCount">
                <input type="text" size="45" <?php if(form_error('ocasiao_projeto')){ echo 'style="border: 1px solid #900" placeholder="Ocasião do projeto é obrigatório"'; }else{ echo 'placeholder="Ocasião do Projeto"'; } ?> name="ocasiao_projeto" value="<?php echo set_value('ocasiao_projeto'); ?>">
            </div>
                
                <div class="formNovoCountDias">
                    <label for="Dias">Quantos dias terá seu projeto?</label>
		    <br /><br /><input type="text" style="width: 20%" <?php if(form_error('dias_projeto')){ echo 'style="border: 1px solid #900" placeholder="Dias do projeto é obrigatório"'; }else{ echo 'placeholder="Dias do projeto"'; } ?> name="dias_projeto" id="dias_projeto" value="<?php echo set_value('dias_projeto'); ?>">
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
                </div>
                <div class="clr"></div>
                <div class="formNovoCount">
                    <input type="radio" id="radio1" checked="checked" name="privado" value="s"><label for="radio1">Privado</label> <input type="radio" id="radio2" name="privado" value="n"><label for="radio2">Público</label>
                </div>
		
                <div class="formNovoCount">    
                    <label for="nomeunico">Identificador desse Projeto</label>
                    <br /><br /><input type="text" <?php if(form_error('nomeunico')){ echo 'style="border: 1px solid #900" placeholder="Identificador é obrigatório e deve ser único"'; }else{ echo 'placeholder="Identificador do Projeto"'; } ?> onblur='retornaValor(this.name)' name="nomeunico" id="nomeunico" value="<?php echo set_value('nomeunico'); ?>"><input type="hidden" name="unique" value="v"> <?php echo $avi; ?>
                </div>
                <button type="submit" class="criarProjeto">Criar Projeto</button>
        </div>
    </form>
</div>