/*
 * Função que redireciona uma página ou faz reload
 * @param String rld : Url que será redirecionada. Vazia ('') para recarregar página.
 * @returns redireciona ou faz reload
 */
function redirect(rld){
    if(rld === ''){
	window.location.reload();
    }else{
	window.location.href=rld;
    }
}

/*
 * Função que verifica se os campos email e senha estão preenchido
 * @returns true ou false
 */
function manda_login(){
    var nome = $("input[name='email']").val();
    var senha = $("input[name='senha']").val();
    
    if(nome === ''){ alert("Preencha com seu email"); return false; }
    if(senha === ''){ alert("Preencha com sua senha"); return false; }
}

/*
 * Função que substitui o \n de um campo textarea para <br>
 * @param String str : pega os valores
 * @param String is_xhtml : se é ou não xhtml
 * @returns retorna o texto formatado, pronto para gravar no banco de dados
 */
function nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

/*
 * Função que recebe o resultado do upload de imagens do TIP para o servidor em json
 * @param Json data : dados em json
 * @returns Escreve na tela os dados da imagem
 */
function processJson(data){
    if(data.status === '200'){
        $(".barraup").fadeIn();
        $('progress').attr('value','');
        $('#porcentagem').html('0%');
        $("#ajuste_automatico").fadeIn();
        $("#tela").html('').html(data.img+'<input type="hidden" name="foto" value="'+data.arquivo+'">');
    }else{
	$(".barraup").fadeOut();
        $('progress').attr('value','');
        $('#porcentagem').html('0%');
        alert("Aviso: "+data.msg);
    }
}

/*
 * Função que recebe o resultado do upload de imagens da CAPA para o servidor em json
 * @param Json data : dados em json
 * @returns Escreve na tela os dados da imagem
 */
function processJsonc(data){
    if(data.status === '200'){
	$("#redimensionar, #salvar").removeClass('oculto');
        $(".capa #telinha").html('').html(data.img+'<input type="hidden" name="fotoc" value="'+data.arquivo+'">');
    }else{
        alert("Aviso: "+data.msg);
    }
}

/*
 * Função que busca as imagens no Instagram
 * @param String ur : url básica da página
 * @returns mostra as imagens do Instagram na tela
 */
function instagram(ur){
    $.ajax({
        type: 'post',
        dataType: 'html',
        url: ur+'auth/fotos_instagram',
        success: function(resp){
            $('#list_foto').html(resp);
            $(".loader").fadeOut();
        }
    });
}

/*
 * Função para adicionar as tags das Counts
 * @param String valor : tag a ser adicionada
 * @param Integer codigo : código do Count
 * @param String ur : url básica da página
 * @returns sem retorno, só grava na página
 */
function addTag(valor,codigo,ur){
    $.ajax({
	type: 'post',
	dataType: 'html',
	data: 'tags='+valor+'&codigo='+codigo,
	async: false,
	url: ur+'web/gravatag'
    });
}

/*
 * Função que pega a posição da imagem da TIP antes de fazer o crop
 * @returns retorna o left e o top da imagem, nessa ordem
 */
function updateCoords(){
    var tp = $("#photo").css('top');
    var lf = $("#photo").css('left');
    var res = lf+'/'+tp;
    return res;
};

/*
 * Função que pega a posição da imagem da CAPA antes de fazer o crop
 * @returns retorna o left e o top da imagem, nessa ordem
 */
function coordsCapa(){
    var tp = $("#photoc").css('top');
    var lf = $("#photoc").css('left');
    var res = lf+'/'+tp;
    return res;
};

/*
 * Função buscar os amigos do Facebook e listar na tela
 * @returns mostra todos os amigos
 */
function convida_face(){
    if($("#lista_facebook").length){
	var ur = $('#ur').data('url');
	var html = '';
	var url = '';
	var count = $("#convida_face").data('count');
	$.getJSON(ur+'auth/get_amigos', function(resp){
	    $.each(resp, function(index, value){
		url = ur+'auth/send_dialog_face/'+value['id']+'/'+count;
		html += '<div class="fb"><img class="pict" src="'+value['pic']+'"><p>'+value['nome']+'</p><img data-url="'+url+'?iframe=true&height=300&width=450" class="inv" src="'+ur+'img/invite_facebook.jpg"></div>';
	    });

	    $(html).appendTo("#lista_facebook");
	});
    }
}

$(document).ready(function(){
    /*
     * @string pega a url básica da página.
     */
    var ur = $('#ur').data('url');
    
    /*
     * Se o seletor @louins existir na página, então pergunta se quer ir para o Instagram
     * É usado quando se faz o logout do Instagram pela página.
     */
    if($("#louins").length){
	var local = $("#louins").data("capa");
	var tip = $("#louins").data('tip');
	if(confirm("Deseja ir para o Instagram agora?")){
	    var link = $(".instagram .white").attr('href');
	    redirect(link);
	}else{
	    if(local === 'capa'){
		$("#opcoes_capa").show();
	    }
	}
    }
    
    /*
     * Mostrar / Ocultar o calendário para alterar a data de início do Count
     */
    $(".dt_projeto").hover(function(){
	$(".altdata").fadeIn();
    },
    function(){
	$(".altdata").fadeOut();
    });
    
    /*
     * Ao clicar, inicia o calendário e mostra na tela.
     * Ao selecionar o dia, o calendário é fechado e é atualizado o início do Count
     * 
     * @var String novadata : seleciona o novo dia de início
     * @var Integer dias : pega o dias da Count para calcular o término
     */
    $(".altdata").click(function(){
	$("#altdata").datepicker({
	    dateFormat: 'dd/mm/yy',
	    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
	    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
	    dayNamesMin: ['Do','Se','Te','Qu','Qu','Se','Sa'],
	    minDate: '+1',
	    onSelect: function(){
		var novadata = $(this).val();
		var dias = $("#cod_count").data('dias');
		$.ajax({
		    type: 'post',
		    dataType: 'html',
		    data: 'calendario='+novadata+'&cd_count='+$("#cod_count").val()+"&dias_count="+dias,
		    url: ur+'web/altdata',
		    beforeSend: function(){
			if(!confirm("Deseja realmente alterar a data de início para "+novadata+"?")){
			    $("#altdata").datepicker( "destroy" );
			    return false;
			}
		    },
		    success: function(){
			$("#altdata").datepicker( "destroy" );
			redirect('');
		    }
		});
	    }
	});
    });
    
    /*
     * Após enviar o invite pelo facebook, essa tela faz o fechamendo do lightbox.
     */
    $(document).on('click','.inv',function(){
	var api_images = [$(this).data('url')];
	$.prettyPhoto.open(api_images);
    });
    
    /*
     * Inicia o seletor #loader ao enviar o contato
     */
    $("#ok_contato").click(function(){
	$("#loader").fadeIn();
    });
    
    /*
     * Pega o código da Count e redireciona para gerenciar as TIPS
     */
    $(".countGerencia").click(function(){
	var cod = $(this).data('count');
	redirect(ur+'web/tips/'+cod+'.html');
    });
    
    $(".formNovoCount").buttonset();
    
    /*
     * Inicia o ToolTip (dicas) para os seletores com a classe .poshy
     * @tooltipClass: classe que estiliza o poshytip.
     */ 
    $('.poshy').tooltip({
	position: {
	    my: "left bottom-10",
	    at: "left top"
	},
	tooltipClass: 'poshy_style'
    });
    
    
    $("#tabs").tabs();
    
    /*
     * Ao sair dos campos #dias_projeto e/ou #titulo_projeto,
     * é gerado o Identificador do Count e escreve no campo #nomeunico
     */
    $(document).on('blur', '#dias_projeto, #titulo_projeto', function(){
	var proj = $("#titulo_projeto");
	var dias = $("#dias_projeto");
	
	if(dias !== '' && proj !== ''){
	    $.ajax({
		type: 'post',
		dataType: 'json',
		data: 'proj='+proj.val()+'&dias='+dias.val(),
		url: ur+'web/nomeunico',
		success: function(resp){
		    $("#nomeunico").val(resp.result).focus();
		}
	    });
	}
    });
    
    /*
     * Controle para saber se no seletor #convida_face existe o data-volta
     * e se está setado como sim.
     * Caso esteja, abre a aba para os amigos do facebook.
     * Isso acontece após fazer o Oauth no facebook
     */
    if($("#convida_face").data('volta') === 'sim'){
	$("#convida_face").click();
	convida_face();
    }
    
    /*
     * Mesmo que função anterior
     */
    $("#convida_face").click(function(){
	convida_face();
    });
    
    /*
     * Formata as tags e, cada tag escrita, é adicionada no banco de dados.
     */
    if($(".hastags").length){
	$(".hastags").tagsInput({
	    'width': '100%',
	    'height': '58px',
	    'onAddTag': function(){
		addTag($(".hastags").val(),$(".hastags").data("codigo"),ur);
	    }
	});
    }    
    
    /*
     * Abre a tela para escolher um arquivo de imagem para upload
     */
    $(document).on('click','#computador', function(){
        $("#file-capa").click();
    });
    
    /*
     * Método para upload usado para fazer subir imagens para a capa.
     * Após subir a imagem, mostra na tela
     */
    var btnUpload=$('#uploadpc');
    new AjaxUpload(btnUpload, {
	action: ur+'web/img_upload/tips',
	//Nome da caixa de entrada do arquivo
	name: 'imagem',
	data: {'cod_count': $("#cod_count").val()},
	onSubmit: function(file, ext){
	    $(".barraup").fadeIn();
	    $('progress').attr('value','10').delay(2000).attr('value','20').delay(2000).attr('value','40').delay(2000).attr('value','60').delay(1000).attr('value','80');
	    $('#porcentagem').html('10%').delay(2000).html('20%').delay(2000).html('40%').delay(2000).html('60%').delay(1000).html('80%');
	    
	    if (! (ext && /^(jpg|png|jpeg)$/.test(ext))){
		// verificar a extensão de arquivo válido
		alert('Somente JPG, PNG são permitidas');
		return false;
	    }
	},
	onComplete: function(file, response){
	    //Adicionar arquivo carregado na lista
	    $('progress').attr('value','100');
	    $('#porcentagem').html('100%');
	    $(".barraup").delay(500).fadeOut();
	    
	    if(response === 'erro1'){
		alert("A imagem precisa ter no mínimo 640 x 570 pixels.");
		return false;
	    }
	    
	    if(response === 'erro2'){
		alert("A imagem precisa ter no mínimo 640 x 200 pixels.");
		return false;
	    }
	    $("#tela").html('').html(response);
	    $("#ajuste_automatico").fadeIn();
	}
    });
    
    /*
     * Método para upload usado para fazer subir imagens para a TIP.
     * Após subir a imagem, mostra na tela
     */
    var btnUploadc=$('#computador');
    new AjaxUpload(btnUploadc, {
	action: ur+'web/img_upload/capa',
	//Nome da caixa de entrada do arquivo
	name: 'imagem',
	data: {'cod_count': $("#cod_count").val()},
	onSubmit: function(file, ext){
	    $(".barraupc").fadeIn();
	    $('progress').attr('value','50');
	    $('#porcentagemc').html('50%');
	    
	    if (! (ext && /^(jpg|png|jpeg)$/.test(ext))){
		// verificar a extensão de arquivo válido
		alert('Somente JPG, PNG ou GIF são permitidas');
		return false;
	    }
	},
	onComplete: function(file, response){
	    //Adicionar arquivo carregado na lista
	    $('progress').attr('value','100');
	    $('#porcentagemc').html('100%');
	    $(".barraupc").delay(500).fadeOut();
	    
	    if(response === 'erro1'){
		alert("A imagem precisa ter no mínimo 640 x 570 pixels.");
		return false;
	    }
	    
	    if(response === 'erro2'){
		alert("A imagem precisa ter no mínimo 640 x 200 pixels.");
		return false;
	    }
	    $("#redimensionar, #salvar").removeClass('oculto');
	    $(".capa #telinha").html('').html(response);
	}
    });
    
    /*
     * 
    
    $('#file-capa').on('change',function(){
	var valor = $(this).val();
	$("#FileFieldc").val(valor);
	
     });
    */
   
    /*
     * Mostra / oculta as opção na capa
     */
    $(".controle_capa").click(function(){
	$("#opcoes_capa").fadeToggle();
    });
    
    /*
     * Usado para quando retorna do Oauth no instagram, as opções da capa estejam abertas.
     */
    if($(".retcapa").length){
	$(".controle_capa").click();
    };
    
    /*
     * Usado para quando retorna do Oauth no instagram, as opções da tip estejam abertas.
     */
    if($(".rettip").length){
	$("#triggerSelect").click();
    };
    
    /*
     * Inicia a drag na imagem da capa e seta os limites da imagem ao arrastar.
     */
    $(document).on('mouseover','#telinha', function(){
	if($("#opcoes_capa").is(":visible")){
	    $("#photoc").draggable({
		cursor: "move",
		scroll: false,
		snapTolerance: 5,
		stop: function (event,ui){
		    
		    // Pega o tamanho da imagem  (largura e altura)
		    var limitx = $(this).width();
		    var limity = $(this).height();
		    
		    // Verifica a posição TOP. Se maior que zero, retorna à posição zero (encosta no topo)
		    if(ui.position.top > 0){
			$(this).animate({
			    top: '0px'
			},200);
		    }
		    
		    // Se a soma do limite TOP da imagem for menor que 100, retorna à posição da base da imagem
		    if((limity + ui.position.top) < 100){
			var alt = (limity - 100);
			$(this).animate({
			    top: '-'+alt+'px'
			},200);
		    }
		    
		    // Verifica a posição LEFT. Se maior que zero, retorna à posição zero (encosta na lateral direita)
		    if(ui.position.left > 0){
			$(this).animate({
			    left: '0px'
			},200);
		    }
		    
		    // Se a soma do limit LEFT da image for menor que 320, retorna à posição do lado direito da imagem
		    if((limitx + ui.position.left) < 320){
			var lar = (limitx - 320);
			$(this).animate({
			    left: '-'+lar+'px'
			},200);
		    }
		}
	    });
	}
    });
    
    /*
     * Inicia a drag na imagem da TIP e seta os limites da imagem ao arrastar.
     */
    $(document).on('mouseover','#tela', function(){
	if($("#ajuste_automatico").is(":visible")){
	    $("#baseDock").fadeToggle();
	}
	
	$("#photo").draggable({
	    cursor: "move",
	    scroll: false,
	    snap: '#tela',
	    snapTolerance: 5,
	    stop: function (event,ui){
		// Pega o tamanho da imagem  (largura e altura)
		var limitx = $(this).width();
		var limity = $(this).height();
		
		// Verifica a posição TOP. Se maior que zero, retorna à posição zero (encosta no topo)
		if(ui.position.top > 0){
		    $(this).animate({
			top: '0px'
		    },200);
		}
		
		// Se a soma do limite TOP da imagem for menor que 100, retorna à posição da base da imagem
		if((limity + ui.position.top) < 228){
		    var alt = (limity - 228);
		    $(this).animate({
			top: '-'+alt+'px'
		    },200);
		}
		
		// Verifica a posição LEFT. Se maior que zero, retorna à posição zero (encosta na lateral direita)
		if(ui.position.left > 0){
		    $(this).animate({
			left: '0px'
		    },200);
		}
		
		// Se a soma do limit LEFT da image for menor que 320, retorna à posição do lado direito da imagem
		if((limitx + ui.position.left) < 256){
		    var lar = (limitx - 256);
		    $(this).animate({
			left: '-'+lar+'px'
		    },200);
		}
	    }
	});
    });
    
    /*
     * Formata o campo para adicionar emails para convites
     */
    if($(".friends").length){
	$(".friends").tagsInput({
	    'width': '100%',
	    'height': '100px',
	    'defaultText': 'Adicionar email'
	});
    }
    
    /*
     * Inicia o lightbox sem as redes sociais no rodapé
     */
    $("a[rel^='prettyPhoto']").prettyPhoto({
	social_tools: false
    });
    
    /*
     * Opção para buscar imagens no facebook para as TIPS
     */
    $("#pega_facebook").click(function(){
	$("#fundo_box").fadeIn(function(){
	    $("#pagina").fadeIn();
	    $(".pag").fadeIn().html('');
	});
	$.ajax({
	    type: 'post',
	    dataType: 'html',
	    url: ur+'auth/fotos_facebook/tips',
	    beforeSend: function(){
		$("#loader").fadeIn();
	    },
	    success: function(resp){
		var html = '<p align="center"><img src="'+ur+'img/facebook_photo.jpg"></p><hr>'+resp+'';
		$(html).appendTo('.pag');
		$("#loader").fadeOut();
	    }
	});
	$("#loader").fadeOut();
    });
    
    /*
     * Opção para buscar imagens no facebook para as CAPAS
     */
    $("#get_facebook").click(function(){
	$("#fundo_box").fadeIn(function(){
	    $("#pagina").fadeIn();
	    $(".pag").fadeIn().html('');
	});
	$.ajax({
	    type: 'post',
	    dataType: 'html',
	    url: ur+'auth/fotos_facebook/capa',
	    beforeSend: function(){
		$("#loader").fadeIn();
	    },
	    success: function(resp){
		var html = '<p align="center"><img src="'+ur+'img/facebook_photo.jpg"></p><hr>'+resp+'';
		$(html).appendTo('.pag');
		$("#loader").fadeOut();
	    }
	});
	$("#loader").fadeOut();
    });
    
    /*
     * Opção para pegar imagens no Instagram para as TIPS
     */
    $("#pega_instagram").click(function(){
        $("#fundo_box").fadeIn(function(){
            $("#pagina").fadeIn();
            $(".pag").fadeIn();
        });
	var count = $("#cod_count").val();
        $.ajax({
            type: 'post',
            dataType: 'html',
	    data: 'local=tips',
            url: ur+'auth/fotos_instagram/tips',
            beforeSend: function(){
                $("#loadering").fadeIn();
            },
            success: function(resp){
		if(resp !== 'falha'){
		    var html = '<p align="left" id="instagram_lightbox"><img style="float:left;" src="'+ur+'img/instagram_logo.png"><div id="title_lightbox">Instagram</div><div id="trocar_instagram"><a href="'+ur+'web/sair_instagram/capa_'+count+'">Trocar Conta<br /> do Instagram</a></div><div style="clear:both;"></div></p><hr>'+resp+'';
		    $(html).appendTo('.pag');
		}else{
		    alert("Houve um erro. Por favor, tente novamente");
		    $("#fundo_box").click();
		}
		$("#loadering").fadeOut();
            }
});
    });
    
    /*
     * Opção para buscar imagens no Instagram para a CAPA
     */
    $("#get_instagram").click(function(){
        $("#fundo_box").fadeIn(function(){
            $("#pagina").fadeIn();
            $(".pag").fadeIn();
        });
	var count = $("#cod_count").val();
        $.ajax({
            type: 'post',
            dataType: 'html',
	    data: 'local=capa',
            url: ur+'auth/fotos_instagram/capa',
            beforeSend: function(){
                $("#loadering").fadeIn();
            },
            success: function(resp){
                var html = '<p align="left" id="instagram_lightbox"><img style="float:left;" src="'+ur+'img/instagram_logo.png"><div id="title_lightbox">Instagram</div><div id="trocar_instagram"><a href="'+ur+'web/sair_instagram/capa_'+count+'">Trocar Conta<br /> do Instagram</a></div><div style="clear:both;"></div></p><hr>'+resp+'';
                $(html).appendTo('.pag');
		$("#redimensionar, #salvar").removeClass('oculto');
                $("#loadering").fadeOut();
            }
        });
    });
    
    /*
     * Cria um lightbox "caseiro" usado para buscar imagens do Instagram
     */
    $("#fundo_box").click(function(){
	$("#pagina").fadeOut();
	$(".pag").fadeOut().html('');
	$("#loader").fadeOut();
	$("#redimensionar, #salvar").addClass("oculto");
	$(this).fadeOut();
    });
    
    /*
     * Busca a imagem que veio do Instagram e coloca na TIP
     */
    $(document).on('click','.ftitips',function(){
	var image = $(this).data('alta');
	
	$.post(ur+'web/img_instagram', {imgi: image, local: 'tips', loca: 'instagram'}, function(resp){
	    image = resp.url;
	    
	    // Limpa o seletor #tela
	    $("#tela").html('');
	    
	    // Cria o html que será carregado na tela
	    var html = '<img data-wi="640" data-he="640" src="'+ur+'tips/'+image+'">';
	    $(html).appendTo("#tela").attr({
		id: 'photo',
		width: 256,
		height: 256
	    }).css({
		top: '0px',
		left: '0px'
	    },"json");
	    $('<input id="arq" data-wi="640" data-he="640" type="hidden" name="foto" value="'+image+'">').appendTo("#tela");
	}, "json");
	$("#ajuste_automatico").fadeIn();
	$("#pagina").fadeOut();
	$(".pag").fadeOut().html('');
	$("#fundo_box").fadeOut();
    });
    
    /*
     * Mostra / Oculta as opção da imagem na TIP
     */
    $("#triggerSelect").on({
	click: function(){
	      $("#baseDock").fadeToggle();
	}
    });
    
    /*
     * Busca a imagem que veio do Instagram e coloca na CAPA
     */
    $(document).on('click','.fticapa',function(){
	var image = $(this).data('alta');
	var idcount = $("#cod_count").val();
	$("#loadering").fadeIn();
	$.post(ur+'web/img_instagram', {imgi: image, local: 'capa', loca: 'instagram', idcount: idcount}, function(resp){
	    image = resp.url;
	    $("#telinha").html('');
	    var html = '<img id="photoc" height="'+resp.height+'" width="'+resp.width+'" data-he="640" data-wi="640" src="'+ur+'capa/'+image+'"><input type="hidden" name="fotoc" value="'+image+'">';
	    $(html).appendTo(".capa #telinha").css({
		top: '0px',
		left: '0px'
	    });
	    $("#loadering").fadeOut();
	    $("#pagina").fadeOut();
	    $(".pag").fadeOut().html('');
	    $("#fundo_box").fadeOut();
	}, "json");
    });
    
    /*
     * Fecha o calendário
     */
    $("#ok_data").click(function(){
	if($("#calendario").val() === ''){
	    return false;
	}
    });
    
    /*
     * Inicia o calendário
     */
    $("#calendario, #fimcount, #inicount").datepicker({
	dateFormat: 'dd/mm/yy',
	monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
	dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
	dayNamesMin: ['Do','Se','Te','Qu','Qu','Se','Sa'],
	minDate: '+1',
	showOn: "button",
	buttonImage: ur+"img/calendar.jpg",
	buttonImageOnly: true
    });
    
    /*
     * Usado para salvar a capa
     */
    $(document).on('click','#salvar', function(){
        var html = '';
        var imagem = $("input[name='fotoc']").val();
        var codigo = $("#cod_count").val();
        var central = $("#optimgc").val();
        var posicao = coordsCapa();

        var larimg = $("#photoc").width();
        var altimg = $("#photoc").height();

        var dados = 'codigo='+codigo+'&img='+imagem+'&central='+central+'&largura='+larimg+'&altura='+altimg+'&posicao='+posicao;

        $.ajax({
	    type: 'post',
	    dataType: 'json',
	    data: dados,
	    async: false,
	    url: ur+'web/grava_capa_antigo',
	    success: function(resp){
		if(resp.erro !== "sim"){
		    $("#redimensionar, #salvar").addClass('oculto');
		    $("#opcoes_capa").fadeOut();
		    /*html = '<img src="'+ur+'capa/'+imagem+'" width="320" height="100">';
		    $(".capa #telinha").html('').html(html);
		    $('#photoc').draggable( "destroy" );*/
		    redirect('');
		}
	    }
        });
    });
    
    /*
     * Usado para ajustar o tamanho da imagem na Capa.
     */
    $(document).on('click','#redimensionar', function(){
	var opt = $("#optimgc");
	var img = $("#photoc");
	var eixo = '';
	var aspect;
	
	/*
	 * Cria o aspectRatio, usado para redimensionar a imagem proporcionalmente
	 */
	if(img.data('wi') >= img.data('he')){
	    aspect = (img.data('wi')) / (img.data('he') / 2);
	}else{
	    aspect = (img.data('he')) / (img.data('wi') / 2);
	}
	
	console.log(aspect);
	
	if(opt.val() === 's'){
	    opt.val('n');
	    img.attr({
		height: img.data('he') / 2,
		width: img.data('wi') / 2
	    }).css({
		top: '0px',
		left: '0px'
	    }).draggable({
		axis: ''
	    });
	}else{
	    opt.val('s');
	    if(img.data('he') > img.data('wi')){
		img.attr({
		    height: (img.data('he') * 2) / (aspect * 2),
		    width: 320
		});
		eixo = 'y';
	    }else{
		img.attr({
		    height: (img.data('he') * 2) / (aspect * 2),
		    width: 320
		});
		eixo = 'y';
	    }
	    img.css({
		top: '0px',
		left: '0px'
	    }).draggable({
		axis: eixo
	    });
	}
    });
    
    /*
     * Usado para ajustar a imagem na TIP
     */
    $(document).on('click','#ajuste_automatico', function(){
	var opt = $("#optimg");
	var img = $("#photo");
	var h = '';
	var w = '';
	var eixo;
	var aspect = '';
	
	if(img.data('wi') > img.data('he')){
	    aspect = (img.data('wi') / 2.5) / (img.data('he') / 2.5);
	}else{
	    aspect = (img.data('he') / 2.5) / (img.data('wi') / 2.5);
	}
	
	if(opt.val() === 's'){
	    opt.val('n');
	    $("#photo").attr({
		height: img.data('he') / 2.5,
		width: img.data('wi') / 2.5
	    }).css({
		top: '0px',
		left: '0px'
	    }).draggable({
		axis: ''
	    });
	}else{
	    opt.val('s');
	    if(img.data('wi') > img.data('he')){
		$("#photo").attr({
		    height: 228,
		    width: (img.data('wi') / 2.5) / aspect
		});
		eixo = 'x';
	    }else{
		$("#photo").attr({
		    height: (img.data('he') / 2.5) / aspect,
		    width: 256
		});
		eixo = 'y';
	    }
	    $("#photo").css({
		top: '0px',
		left: '0px'
	    }).draggable({
		axis: eixo
	    });
	}
    });
    
    /*
     * Setar seletor #mudou para 's', caso tenho alterado algo em uma TIP.
     * É emitido um aviso, caso o usuário clique em outra TIP sem salvar a TIP aberta
     */
    $(document).on('change','.campos', function(){
	$("#mudou").val('s');
    });
    
    /*
     * Abre uma TIP para edição
     * Caso já tenha uma TIP aberta, é emitido um aviso
     */
    $(".mozaico").click(function(){
	/*
	 * Usado caso não tenha sido escolhida uma capa para o projeto
	 */
	if($(".trava").length){
	    alert("Você ainda não escolheu uma capa para esse projeto");
	    return false;
	}
	
	// Aviso emitido, quando uma tip esteja aberta para edição
	if($("#mudou").val() === 's'){
	    if(!confirm("Os dados dessa tip serão perdidos. Continuar assim mesmo?")){
		return false;
	    }
	}
	
	$("#mudou").val('n');
	$(".campos").css({
	    border: '1px solid #ddd'
	});
	
	var dis = $(this).data("disabled");
	$("#baseDock").fadeIn();
	$("#ajuste_automatico").fadeOut();
	
	$("#codigo_tip").val($(this).data('codigo'));
	$("input[name='titulo']").val($(this).data("titulo"));
	$("input[name='subtitulo']").val($(this).data("sub"));
	$("textarea[name='descricao']").val($(this).data("descricao"));
	if($(this).data("imagem") !== "no_image.jpg"){
	    $("input[name='foto']").val($(this).data('imagem'));
	    $("#tela").html('<img width="256" height="228" src="'+ur+'tips/'+$(this).data('imagem')+'">').fadeIn();
	}else{
	    $("#tela").html('<img height="228" width="256" src="'+ur+'tips/no_image.jpg">').fadeIn();
	}
	$("#optimg").val('n');

	$("#ntip").html('<p>Edição de Tips - Tip #'+$(this).data('tip')+' - '+$(this).data('mostra')+'</p>');
	$("#num_tip").html($(this).data('tip')+'/'+$(this).data('dias')).fadeIn();
	$("#tit_tip").html($(this).data("titulo"));
	$("#sub_tip").html($(this).data("sub"));
	$("#men_tip").html(nl2br($(this).data("descricao")));
	$(".esconde").fadeIn();
	
	// Usado caso uma TIP já tenha passado da data
	if(dis === 'yes'){
	    $("#tit").prop("disabled", true);
	    $("#sub").prop("disabled", true);
	    $("#men").prop("disabled", true);
	    $("#addtip").css({display: 'none'});
	    $("#cantip").css({display: 'none'});
	    $("#cleantip").css({display: 'none'});
	    $(".menu_foto").fadeOut();
	}else{
	    $("#tit").prop("disabled", false);
	    $("#sub").prop("disabled", false);
	    $("#men").prop("disabled", false);
	    $("#addtip").css({display: 'inline-block'});
	    $("#cantip").css({display: 'inline-block'});
	    if($(this).data('titulo') === ''){
		$("#cleantip").hide();
	    }else{
		$("#cleantip").show();
	    }
	    $(".menu_foto").fadeIn();
	}
    });
    
    /*
     * Cancelar a edição de uma TIP
     */
    $("#cantip").click(function(){
	$("#codigo_tip").val('');
	$("#tit_tip").html('');
        $("#sub_tip").html('');
	$("#men_tip").html('');
	$("#tela").html('');
	$(".menu_foto").fadeOut();
	$("#num_tip").html('').hide();
	$(".esconde").fadeOut();
    });
    
    /*
     * Usado para voltar para a TIP
     */
    $("#ok_volta").click(function(){
	var id = $(this).data("id");
	redirect(ur+'web/tips/'+id+'.html');
	return false;
    });
    
    /*
     * Verificar se foi digitado um título para a TIP
     */
    $(document).on('blur','#tit', function(){
	if($("#tit").val() !== ''){
	    $("#tit").css({
		border: '1px solid #090'
	    });
	}
    });
     
     /*
     * Verificar se foi digitado um subtítulo para a TIP
     */
    $(document).on('blur','#sub', function(){
	if($("#sub").val() !== ''){
	    $("#sub").css({
		border: '1px solid #090'
	    });
	}
    });
     
     /*
     * Verificar se foi digitado uma mensagem para a TIP
     */
    $(document).on('blur','#men', function(){
	if($("#men").val() !== ''){
	    $("#men").css({
		border: '1px solid #090'
	    });
	}
    });
    
    /*
     * Usado para salvar a TIP que foi editada
     */
    $("#addtip").click(function(){
	$("#loader").fadeIn();
	
	// Troca o texto do botão Salvar por Processando
	$(this).html("Processando...").delay(100);
	
	// Pega as informações da TIP para salvar e da imagem.
	var titulo = $("input[name='titulo']").val();
	var sub = $("input[name='subtitulo']").val();
	var mensagem = $("#men").val();
	var codigo = $("#cod_count").val(); 
	var id_tip = $("#codigo_tip").val();
	var central = $("#optimg").val(); // imagem centralizada;
	var imagem = $("input[name='foto']").val();
	$("#mudou").val('n');
	
	// Pega a posição da imagem LEFT/TOP
	var posicao = updateCoords();

	// Pega o tamanho da imagem na tela
	var larimg = $("#photo").width();
	var altimg = $("#photo").height();
	 
	// Faz as verificações antes de continuar.
	// Se existe imagem, titulo, sub e mensagem.
	if(imagem === ''){
	    alert("Sua Tip precisa de uma imagem");
	    $("#loader").fadeOut();
	    $("#addtip").html('Salvar');
	    return false;
	}
	 
	if(titulo === ''){
	    alert("Sua Tip precisa de um título");
	    $("input[name='titulo']").focus().css({
		border: '1px solid #e98087',
		background: '#f5d4d7'
	    });
	    $("#loader").fadeOut();
	    $("#addtip").html('Salvar');
	    return false;
	}
	 
	if(sub === ''){
	    alert("Sua Tip precisa de um sub-título");
	    $("input[name='subtitulo']").focus().css({
		border: '1px solid #e98087',
		background: '#f5d4d7'
	    });
	    $("#loader").fadeOut();
	    $("#addtip").html('Salvar');
	    return false;
	}
	 
	if(mensagem === ''){
	    alert("Sua Tip precisa de uma descrição");
	    $("#men").focus().css({
		border: '1px solid #e98087',
		background: '#f5d4d7'
	    });
	    $("#loader").fadeOut();
	    $("#addtip").html('Salvar');
	    return false;
	}
	
	/*
	 * HACK para funcionar em alguns navegadores, senão as imagens ficam pretas.
	 */
	$("#photo").mouseover();
	
	// Monta a query para enviar para o back-end
	var dados = 'id_tip='+id_tip+'&codigo='+codigo+'&img='+imagem+'&central='+central+'&titulo='+titulo+'&sub='+sub+'&mensagem='+mensagem+'&largura='+larimg+'&altura='+altimg+'&posicao='+posicao;

	$.ajax({
	    type: 'post',
	    dataType: 'json',
	    data: dados,
	    async: false,
	    url: ur+'web/grava_tip_antigo',
	    success: function(resp){
		$("#loader").fadeOut();
		$(".barraup").fadeOut();
		$(".menu_foto").fadeOut();
		if(resp.erro === 'ok'){
		    
		    // Monta a string para aparecer no mozaico
		    var img = '<img width="100" src="'+ur+'tips/thumb_'+resp.imagem+'"><div class="fundo"><strong>'+$("#tip_"+id_tip).data('tip')+'/'+$("#tip_"+id_tip).data('dias')+'</strong></div>';
		    $("#tip_"+id_tip).html('').html(img);
		    $("#tip_"+id_tip).data('titulo',titulo);
		    $("#tip_"+id_tip).data('sub',sub);
		    $("#tip_"+id_tip).data('descricao',mensagem);
		    $("#tip_"+id_tip).data('imagem',resp.imagem);
		    $(".esconde, #num_tip, #loader").fadeOut();
		    $("#tit_tip, #sub_tip, #men_tip").html('');
		    $("#tela").html('');
		}
		$("#addtip").html('Salvar');
	    }
	});
    });
    
    // Ao digitar no campos específico, preenche a div no  Iphone de exemplo
    $("#tit").keyup(function(){
	var valor = $(this).val();
	$("#tit_tip").html(valor);
    });
    
    // Ao digitar no campos específico, preenche a div no  Iphone de exemplo
    $("#sub").keyup(function(){
	var valor = $(this).val();
	$("#sub_tip").html(valor);
    });
     
    // Ao digitar no campos específico, preenche a div no  Iphone de exemplo
    $("#men").keyup(function(){
	var valor = $(this).val();
	$("#men_tip").html(nl2br(valor));
    });
    
    // Usado para setar uma count como excluida no sistema
    $(".exc").click(function(){
	var id = $(this).data('id');
	var ur = $(this).data('ur');
	
	if(confirm("Deseja realmente excluir esse Count? Essa ação não tem retorno.")){
	    window.location.href=ur+"web/excluir_count/"+id;
	}
	
	return false;
    });
    
    // Usado para resetar uma TIP
    $(document).on('click','#cleantip', function(){
	if(confirm("Deseja realmente limpar essa TIP? Essa ação não tem retorno.")){
	    var id = $("#codigo_tip").val();
	    $("#loader").fadeIn();
	    $.ajax({
		type: 'post',
		dataType: 'html',
		url: ur+'web/clean_tip',
		data: 'id='+id,
		beforeSend: function(){
		    $("#loader").fadeIn();
		},
		success: function(resp){
		    if(resp === ''){
			$(".menu_foto").fadeOut();
			$("#tip_"+id).data('titulo','');
			$("#tip_"+id).data('sub','');
			$("#tip_"+id).data('descricao','');
			$("#tip_"+id).data('imagem','no_image.jpg');
			$(".esconde, #num_tip, #loader").fadeOut();
			$("#tit_tip, #sub_tip, #men_tip").html('');
			$("#tip_"+id).html('<img width="100" src="'+ur+'img/tip_add_box.png">');
			$("#tela").html('');
		    }else{
			alert(resp);
		    }
		}
	    });
	}
    });
    
    /*
     * Mudar de idioma
     */
    $("#btn_idioma").click(function(){
	$("#lang").fadeToggle();
    });
    
    /*
     * Mudar de idioma
     */
    $(".lang").click(function(){
	var lng = $(this).data('lang');
	
	$.ajax({
	    type: 'post',
	    dataType: 'html',
	    url: ur+'web/idioma',
	    async: false,
	    data: 'lng='+lng,
	    success: function(){
		window.location.reload();
	    }
	});
    });
    
    // Fechar uma tela
    $(".fechar").click(function(){
        $('.fechar').parent().fadeOut();
    });
    
    /*
     * Fazer login, mas não é mais usado
     */
    $(".btn_login").click(function(){
        $("#login").fadeToggle(function(){
            $("input[name='email']").focus();
        });
    });
    
    /*
     * Mudar cor da borda, em caso de foco.
     */
    $("input[name='email'], input[name='senha']").focus(function(){
        $(this).css('border-color','#ddd');
    });
    
    // Calcula o valor do projeto, baseado no número de dias e se o campo for
    // diferente de vazio
    if($("#dias_projeto").val() !== ''){
	var valor = $(this).val();
	if(valor !== ''){
	    var dia = 0.99;
	    var pagto = (valor * dia) + 1;
	    
	    var num = new Number(pagto);
	    
	    $("#vlr_proj").val(num.toFixed(2));
	    $("#resultado_dias").html("R$ "+num.toFixed(2));
	}
    }
    
    // Faz o cálculo do valor do projeto, ao escolher o número de dias
    $("#dias_projeto").blur(function(){
        var valor = $(this).val();
	if(valor !== ''){
	    var dia = 0.99;
	    var pagto = (valor * dia) + 1;
	    
	    var num = new Number(pagto);
	    
	    $("#vlr_proj").val(num.toFixed(2));
	    $("#resultado_dias").html("R$ "+num.toFixed(2));
	}
    });
});