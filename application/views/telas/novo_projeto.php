<?php
$diaq = strpos(form_error('dias_projeto'), 'numbers');
$diareq = strpos(form_error('dias_projeto'), 'required');
$diamax = strpos(form_error('dias_projeto'), 'less than');

$avds = '';
if(form_error('dias_projeto')){
    if($diareq === false && $diaq === false && $diamax === false){
    }else{
	if($diamax === false && $diareq === false){
	    $avds = '<small class="red">Dias do Projeto precisa ser um número</small>';
	}else if($diamax === false && $diaq === false){
	    $avds = '<small class="red">Dias do Projeto é obrigatório</small>';
	}else{
	    $avds = '<small class="red">Você pode criar no máximo 10 dias</small>';
	}
    }
}
?>
<div id="container">
    <form id="formcad" method="post" action="<?php echo current_url(); ?>">
        <div class="baseNovoCount">
            <h3>Novo Count</h3>
            <div class="formNovoCount">
                <input type="text" size="45" <?php if(form_error('nome_projeto')){ echo 'style="border: 1px solid #ffd6d9; background: #fff6f7" placeholder="Nome do projeto é obrigatório"'; }else{ echo 'placeholder="Nome do Projeto"'; } ?> id="titulo_projeto" name="nome_projeto" value="<?php echo set_value('nome_projeto'); ?>">
            <input type="hidden" name="ocasiao_projeto" value=".">
            </div>
            
            <div class="formNovoCountDias">
                <label for="Dias">Quantos dias terá seu projeto?</label>
                <br /><br /><input type="text" <?php if(form_error('dias_projeto')){ echo 'style="border: 1px solid #ffd6d9; background: #fff6f7; width: 20%;" placeholder="Dias do projeto"'; }else{ echo 'style="width: 20%;" placeholder="Dias do projeto"'; } ?> name="dias_projeto" id="dias_projeto" value="<?php echo set_value('dias_projeto'); ?>">
		<?php if(PRECO_CADASTRO == 's'){ ?>
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
		<?php } ?>
            </div><?php echo $avds; ?>
            
            <div class="clr"></div>
            <div class="formNovoCount">
		<!--<img id="seta_priv" src="<?php echo base_url(); ?>img/seta.gif">-->
		<div class="clr"></div>
                <input type="radio" id="radio1" name="privado" value="s"><label for="radio1">Privado</label> <input type="radio" id="radio2" name="privado" value="n" checked="checked"><label for="radio2">Público</label>
            </div>

            <div class="formNovoCount">    
                <label for="nomeunico">Identificador desse Projeto</label>
                <br /><br /><input type="text" <?php if(form_error('nomeunico')){ echo 'style="border: 1px solid #ffd6d9; background: #fff6f7" placeholder="Identificador é obrigatório e deve ser único"'; }else{ echo 'placeholder="Identificador do Projeto"'; } ?> onblur='retornaValor(this.name)' name="nomeunico" id="nomeunico" value="<?php echo set_value('nomeunico'); ?>"><input type="hidden" name="unique" value="v">
            </div>
            <button type="submit" class="criarProjeto">Criar Projeto</button>
        </div>
    </form>
</div>