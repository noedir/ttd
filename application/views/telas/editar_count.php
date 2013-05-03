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
    <?php if($this->session->flashdata('countok') != ''){ ?>
	<p><?php echo $this->session->flashdata('countok'); ?></p>
    <?php } ?>
    <form id="formcad" method="post" action="<?php echo current_url(); ?>">
        <div class="baseNovoCount">
            <h3>Atualizar Count</h3>
            <div class="formNovoCount">
                <label>Título: <br><input type="text" size="45" <?php if(form_error('nome_projeto')){ echo 'style="border: 1px solid #900" placeholder="Nome do projeto é obrigatório"'; }else{ echo 'placeholder="Nome do Projeto"'; } ?> name="nome_projeto" value="<?php echo $edcount[0]['co_titulo']; ?>"></label> <?php echo $avn; ?>
            </div>
            <div class="formNovoCount">
                <label>Ocasião: <br><input type="text" size="45" <?php if(form_error('ocasiao_projeto')){ echo 'style="border: 1px solid #900" placeholder="Ocasião do projeto é obrigatório"'; }else{ echo 'placeholder="Ocasião do Projeto"'; } ?> name="ocasiao_projeto" value="<?php echo $edcount[0]['co_descricao']; ?>"></label> <?php echo $avo; ?>
            </div>
            <div class="formNovoCountDias">
                <label>Quantos dias terá seu projeto?
                <br /><input type="text" style="width: 20%" <?php if(form_error('dias_projeto')){ echo 'style="border: 1px solid #900" placeholder="Dias do projeto é obrigatório"'; }else{ echo 'placeholder="Dias do projeto"'; } ?> name="dias_projeto" id="dias_projeto" readonly="true" value="<?php echo $edcount[0]['co_dias']; ?>"></label>
                <div id="baseValores">
                    <input type="hidden" name="valor_projeto" id="vlr_proj" value="<?php print(number_format(($edcount[0]['co_dias'] * 0.99 + 1),2,".","")) ; ?>">
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
                        <Br /><div id="resultado_dias">R$ <?php print(number_format(($edcount[0]['co_dias'] * 0.99 + 1),2,".","")) ; ?></div>
                    </div>
                </div>
            </div>
            <div class="clr"></div>
            <div class="formNovoCount">
                <input type="radio" id="radio1" <?php if($edcount[0]['co_privado'] == 's'){ echo 'checked="checked"';  } ?> name="privado" value="s"><label for="radio1">Privado</label> <input type="radio" <?php if($edcount[0]['co_privado'] == 'n'){ echo 'checked="checked"';  } ?> id="radio2" name="privado" value="n"><label for="radio2">Público</label>
            </div>
            
            <div class="formNovoCount">    
                <label>Identificador desse Projeto
                <br><input type="text" name="nome_unico" readonly="true" value="<?php echo $edcount[0]['co_nomeunico']; ?>"></label>
            </div>
            
            <input type="hidden" name="codigo" value="<?php echo $edcount[0]['co_codigo']; ?>">
            
            <button type="submit" class="criarProjeto">Gravar</button>
        </div>
    </form>
</div>