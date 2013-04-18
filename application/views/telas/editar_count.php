<?php
$nom = strpos(form_error('nome_projeto'),'required');
$avn = '';
$oca = strpos(form_error('ocasiao_projeto'),'required');
$avo = '';
$dia = strpos(form_error('dias_projeto'),'required');
$avd = '';
if(form_error('nome_projeto')){
    if($nom === false){
	$avn = '<small><small>O nome do projeto é obrigatório</small></small>';
    }
}
if(form_error('ocasiao_projeto')){
    if($nom === false){
	$avo = '<small><small>A ocasião do projeto é obrigatório</small></small>';
    }
}
if(form_error('dias_projeto')){
    if($nom === false){
	$avd = '<small><small>Os dias do projeto é obrigatório</small></small>';
    }
}
?>
<div id="container">
    <?php if($this->session->flashdata('countok') != ''){ ?>
	<p><?php echo $this->session->flashdata('countok'); ?></p>
    <?php } ?>
    <form id="formcad" method="post" action="<?php echo current_url(); ?>">
        <legend>Dados do Projeto</legend>
        <p><label>Nome do Projeto:*<br><input type="text" size="45" <?php if(form_error('nome_projeto')){ echo 'style="border: 1px solid #900" placeholder="Nome do projeto é obrigatório"'; } ?> name="nome_projeto" value="<?php echo $edcount[0]['co_titulo']; ?>"> <?php echo $avn; ?></label></p>
        <p><label>Ocasião:*<br><input type="text" size="45" <?php if(form_error('ocasiao_projeto')){ echo 'style="border: 1px solid #900" placeholder="Ocasião do projeto é obrigatório"'; } ?> name="ocasiao_projeto" value="<?php echo $edcount[0]['co_descricao']; ?>"> <?php echo $avo; ?></label></p>
        <p><label>Dias:*<br><input type="text" size="45" <?php if(form_error('dias_projeto')){ echo 'style="border: 1px solid #900" placeholder="Dias é obrigatório"'; } ?> name="dias_projeto" id="dias_projeto" value="<?php echo $edcount[0]['co_dias']; ?>"> <?php echo $avd; ?></label></p>
	<p><label>Evento:</label>&nbsp;<label>Privado <input type="radio" <?php if($edcount[0]['co_privado'] == 's'){ echo 'checked="checked"';  } ?> name="privado" value="s"></label>&nbsp;&nbsp;<label>Público <input type="radio" <?php if($edcount[0]['co_privado'] == 'n'){ echo 'checked="checked"';  } ?> name="privado" value="n"></label></p>
	<input type="hidden" name="valor_projeto" id="vlr_proj" value="">
	<input type="hidden" name="codigo" value="<?php echo $edcount[0]['co_codigo']; ?>">
        <div id="resultado_dias"></div>
        <p><button type="submit">Salvar</button> &nbsp; <button type="reset">Cancelar</button></p>
    </form>
</div>