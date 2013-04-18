<?php
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
        <legend>Dados do Projeto</legend>
        <p><label>Nome do Projeto:*<br><input type="text" size="45" <?php if(form_error('nome_projeto')){ echo 'style="border: 1px solid #900" placeholder="Nome do projeto é obrigatório"'; } ?> name="nome_projeto" value="<?php echo set_value('nome_projeto'); ?>"></label></p>
        <p><label>Ocasião:*<br><input type="text" size="45" <?php if(form_error('ocasiao_projeto')){ echo 'style="border: 1px solid #900" placeholder="Ocasião do projeto é obrigatório"'; } ?> name="ocasiao_projeto" value="<?php echo set_value('ocasiao_projeto'); ?>"></label></p>
        <p><label>Dias:*<br><input type="text" size="45" <?php if(form_error('dias_projeto')){ echo 'style="border: 1px solid #900" placeholder="Dias é obrigatório"'; } ?> name="dias_projeto" id="dias_projeto" value="<?php echo set_value('dias_projeto'); ?>"></label></p>
	<p><label>Evento:</label>&nbsp;<label>Privado <input type="radio" checked="checked" name="privado" value="s"></label>&nbsp;&nbsp;<label>Público <input type="radio" name="privado" value="n"></label></p>
	<input type="hidden" name="valor_projeto" id="vlr_proj" value="">
        <div id="resultado_dias"></div>
	<p><label>Identificador dessa Count:*<br><input type="text" <?php if(form_error('nomeunico')){ echo 'style="border: 1px solid #900" placeholder="Identificador é obrigatório e deve ser único"'; } ?> onblur='retornaValor(this.name)' name="nomeunico" id="nomeunico" value="<?php echo set_value('nomeunico'); ?>"><input type="hidden" name="unique" value="v"> <?php echo $avi; ?></label></p>
        <p><button type="submit">Cadastrar</button></p>
    </form>
</div>