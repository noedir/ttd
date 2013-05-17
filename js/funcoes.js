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
 * Função que retorna o nome disponível para o Identificador do Count
 * @param String nome : name do campo que quer tratar
 * @returns retorna um nome disponível
 */
function retornaValor(nome){
    var ur = $('#ur').data('url');
    var vai = $("input[name='"+nome+"']");
    $.post(ur+'web/disponivel',{nome:vai.val()}, function(ret){
	if(ret === 'false'){
	    $(vai).focus().css({border: '1px solid #e98087', background: '#f5d4d7'});
	    $("input[name='unique']").val('n');
	}else{
	    $(vai).css({border: '1px solid #ddd'});
	    $("input[name='unique']").val('s');
	}
    });
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
	$("#tela").html('').html(data.img+'<input type="hidden" id="arq" name="foto" value="'+data.arquivo+'">');
    }else{
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
	$(".capa #telinha").html('').html(data.img+'<input type="hidden" id="arqc" name="fotoc" value="'+data.arquivo+'">');
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
    $("#loadering").fadeIn();
    $.ajax({
	type: 'post',
	dataType: 'html',
	url: ur+'auth/fotos_instagram',
	beforeSend:  function(){
	    $("#loadering").fadeIn();
	},
	success: function(resp){
	    $('#list_foto').html(resp);
	    $("#loadering").fadeOut();
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
     * Se o tutorial estiver ativo na tela, abre essas opções de click.
     * @start: Inicia o tutorial.
     */
    if($("#baseTutoriais").length){
	var img = 2;
	$("#start").click(function(){
	    $("#navegador").show();
	    $("#start").hide();
	    $(".img").css({
		'z-index': '100'
	    });
	    $("#img2").css({
		'z-index': '910'
	    });
	});
	
	$("#avanca").click(function(){
	    if(img >= 4){
		$("#sair_tutorial").fadeIn();
	    }
	    if(img >= 5){
		return false;
	    }
	    img++;
	    console.log(img);
	    $(".img").css({
		'z-index': '100'
	    });
	    $("#img"+img).css({
		'z-index': '910'
	    });
	});
	
	$("#volta").click(function(){
	    img--;
	    if(img < 5){
		$("#sair_tutorial").fadeOut();
	    }
	    console.log(img);
	    $(".img").css({
		'z-index': '100'
	    });
	    $("#img"+img).css({
		'z-index': '910'
	    });
	    if(img < 2){
		img = 2;
		$("#navegador").hide();
		$("#start").show();
	    }
	});
	
	$(".bola").click(function(){
	    var ima = $(this).data('img');
	    var num = $(this).data('num');
	    
	    if(num == 5){
		$("#sair_tutorial").fadeIn();
	    }else{
		$("#sair_tutorial").fadeOut();
	    }
	    
	    img = num;
	    
	    $(".img").css({
		'z-index': '100'
	    });
	    $("#"+ima).css({
		'z-index': '910'
	    });	    
	});
	
	$("#sair_tutorial").click(function(){
	    $.ajax({
		type: 'post',
		dataType: 'html',
		url: ur+'web/tuto',
		success: function(){
		    redirect(ur+'web/counts');
		}
	    });
	});
    }
    
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
     * Função que faz a imagem aparecer no navegador sem fazer upload para o servidor.
     * A pessoa escolhe a imagem e ela aparece no navegador quase instantaneamente.
     * Essa opção não funciona em navegadores mais antigos.
     * Mais detalhes na página http://caniuse.com/filereader
     * 
     * Seletor #file-capa faz a imagem aparecer na capa
     */
    $("#file-capa").on('change', function(e){
	var lar;
	var alt;
	
	var files = e.target.files;
	var f = files[0];
	
	if(f.type === 'image/jpeg' || f.type === 'image/png'){
	    $("#opcoes_capa").fadeIn();
	    $("#telinha").html('<img src="'+ur+'img/loader.gif">');
	    
	    var reader = new FileReader();
	    reader.onload = (function () {
		return function (e) {
		    window.loadImage(
			e.target.result,
			function (img) {
			    $("#telinha").html('');
			    $(img).appendTo("#telinha").attr({
				id: 'photoc',
				width: img.width,
				height: img.height
			    }).data('wi',img.width).data('he',img.height);
			    $('<input id="arqc" type="hidden" name="fotoc" value="'+img.src+'">').appendTo("#telinha");

			    lar = img.width / 2;
			    alt = img.height / 2;

			    $("#photoc").attr({
				width: lar,
				height: alt
			    }).css({
				top: '0px',
				left: '0px'
			    });
			    $("#redimensionar, #salvar").removeClass('oculto');
			}
		    );
		};
	    })(f);
	    reader.readAsDataURL(f);
	}else{
	    alert("Use imagens JPG ou PNG.");
	    return false;
	}
    });
    
    /*
     * Função que faz a imagem aparecer no navegador sem fazer upload para o servidor.
     * A pessoa escolhe a imagem e ela aparece no navegador quase instantaneamente.
     * Essa opção não funciona em navegadores mais antigos.
     * Mais detalhes na página http://caniuse.com/filereader
     * 
     * Seletor #file-up faz a imagem aparecer na TIP
     */
    $("#file-up").on('change', function(e){
	var lar;
	var alt;
	
	var files = e.target.files;
	var f = files[0];
	
	if(f.type === 'image/jpeg' || f.type === 'image/png'){
	    $("#ajuste_automatico").fadeIn();
	    $("#tela").html('<img src="'+ur+'img/loader.gif">');
	    var reader = new FileReader();
	    reader.onload = (function () {
		return function (e) {
		    window.loadImage(
			e.target.result,
			function (img) {
			    $("#tela").html('');
			    $(img).appendTo("#tela").attr({
				id: 'photo',
				width: img.width,
				height: img.height
			    });
			    $('<input id="arq" data-wi="'+img.width+'" data-he="'+img.height+'" type="hidden" name="foto" value="'+img.src+'">').appendTo("#tela");

			    lar = img.width / 2.5;
			    alt = img.height / 2.5;

			    $("#photo").attr({
				width: lar,
				height: alt
			    }).css({
				top: '0px',
				left: '0px'
			    });
			},
			{
			    minWidth: 640,
			    minHeight: 570
			}
		    );
		};
	    })(f);
	    reader.readAsDataURL(f);
	}else{
	    alert("Use imagens JPG ou PNG.");
	    return false;
	}
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
    $(document).on('click','#uploadpc', function(){
	$("#file-up").click();
    });
    
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
     * Abre a tela para escolher um arquivo de imagem para upload
     */
    $(document).on('click','#computador', function(){
	$("#file-capa").click();
    });
    
    
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
	    //start: function(){
		//if($("#optimg").val() === 's'){
		//    $(this).draggable( "destroy" );
		//}
	    //},
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
	var tip = $("#codigo_tip").val();
	$.ajax({
	    type: 'post',
	    dataType: 'html',
	    data: 'local=tips',
	    url: ur+'auth/fotos_instagram/tips',
	    beforeSend: function(){
		$("#loadering").fadeIn();
	    },
	    success: function(resp){
		$("#loadering").fadeOut();
		var html = '<p align="left" id="instagram_lightbox"><img style="float:left;" src="'+ur+'img/instagram_logo.png"><div id="title_lightbox">Instagram</div><div id="trocar_instagram"><a href="'+ur+'web/sair_instagram/tips_'+count+'_'+tip+'">Trocar Conta<br /> do Instagram</a></div><div style="clear:both;"></div></p><hr>'+resp+'';
		$(html).appendTo('.pag');
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
		$("#loadering").fadeOut();
		var html = '<p align="left" id="instagram_lightbox"><img style="float:left;" src="'+ur+'img/instagram_logo.png"><div id="title_lightbox">Instagram</div><div id="trocar_instagram"><a href="'+ur+'web/sair_instagram/capa_'+count+'">Trocar Conta<br /> do Instagram</a></div><div style="clear:both;"></div></p><hr>'+resp+'';
		$(html).appendTo('.pag');
		$("#redimensionar, #salvar").removeClass('oculto');
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
	
	$.post(ur+'web/encodeimg', {img: image}, function(resp){
	    image = resp.img64;
	    
	    // Limpa o seletor #tela
	    $("#tela").html('');
	    
	    // Cria o html que será carregado na tela
	    var html = '<img src="'+image+'">';
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
	$("#loadering").fadeIn();
	$.post(ur+'web/encodeimg', {img: image}, function(resp){
	    image = resp.img64;
	    $("#telinha").html('');
	    var html = '<img id="photoc" height="" width="320" data-he="640" data-wi="640" src="'+image+'"><input type="hidden" name="fotoc" id="arqc" value="'+image+'">';
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
    $(document).on('click', '#salvar', function(){
	$("#loader").fadeIn();
	var html;
	var central = $("#optimgc").val(); // imagem centralizada;
	
	if(imagem === ''){
	    alert("Sua Capa precisa de uma imagem");
	    $("#loader").fadeOut();
	    return false;
	}
	
	/*
	 * Cria a instância da imagem.
	 * 
	 * Pega a imagem do input name='fotoc'
	 */	
	var imagem = $("input[name='fotoc']").val();
	var imageObj = new Image();
	 
	// Muda a url da imagem na instancia
	imageObj.src = imagem;
	 
	
	imageObj.onload = function() {
	    // Obtem o elemento canvas da capa
	    var canvas = document.getElementById('canvascapa');
	    var context = canvas.getContext('2d');
	    
	    var codigo = $("input[name='count']").val(); // Peag o código da count
	    var posicao = coordsCapa(); // pega LEFT x TOP
	    var n = posicao.split('/'); // separa as posições LEFT x TOP
	    var sx = n[0].replace("px",""); // retira o PX deixando somente o número negativo do LEFT
	    var sy = n[1].replace("px",""); // retira o PX deixando somente o número negativo do TOP
	    sx = sx * -1; // transforma o negativo em positivo do LEFT
	    sy = sy * -1; // transforma o negativo em positivo do TOP
	    
	    var sourceX = sx * 2; // imagem original X
	    var sourceY = sy * 2; // imagem original Y
	     
	    var w = 640; // seta a largura
	    var h = 200; // seta a altura
	    
	    // Caso a imagem tenha sido ajustada
	    if(central === 's'){
		w = $("#photoc").width() * 2; // pega a largura da imagem atualizada * 2
		h = $("#photoc").height() * 2; // pega a altura da imagem atualizada * 2
		
		sourceX = (sx / 2) - (sourceX * 1.25); // Faz o cálculo para ajustar a largura
		sourceY =  (sy / 2) - (sourceY * 1.25); // Faz o cálculo para ajustar a altura
		
		context.drawImage(imageObj, sourceX, sourceY, w, h); // Desenha imagem no canvas oculto
	    }else{
		// Caso a imagem tenha sido cropada
		if(imageObj.width < 640 || imageObj.height < 200){
		    w = $("#photoc").width() * 2;
		    h = $("#photoc").height() * 2;

		    sourceX = (sx / 2) - (sourceX * 1.25);
		    sourceY =  (sy / 2) - (sourceY * 1.25);

		    context.drawImage(imageObj, sourceX, sourceY, w, h);
		}else{
		    context.drawImage(imageObj, sourceX, sourceY, w, h, 0, 0, 640, 200);
		}
	    }
	    
	    // Imagem pronta para ser enviada para o servidor
	    var img_pronta = canvas.toDataURL("image/jpeg");
	    
	    
	    // Faz a limpeza do canvas, para evitar sobreposição de imagens, principalmente PNG transparente
	    context.clearRect(0,0,canvas.width,canvas.height);
	    
	    // Dados que vão para o servidor
	    var dados = 'codigo='+codigo+'&img='+img_pronta;
	     
	    
	    $.ajax({
		type: 'post',
		dataType: 'json',
		data: dados,
		async: false,
		url: ur+'web/grava_capa',
		beforeSend: function(){
		    $(".progress").progressbar({
			value: 45
		    });
		},
		success: function(resp){
		    $("#opcoes_capa").fadeOut();
		    $("#loader").fadeOut();
		    html = '<img src="'+resp.msg+'" width="320" height="100">';
		    $(".capa #telinha").html('').html(html);
		    $('#photoc').draggable( "destroy" );
		}
	    });
	    $("#loader").fadeOut();
	};
	$("#loader").fadeOut();
	$("#redimensionar, #salvar").addClass("oculto");
    });
    
    /*
     * Usado para ajustar o tamanho da imagem na Capa.
     */
    $(document).on('click','#redimensionar', function(){
	var opt = $("#optimgc");
	var img = $("#photoc");
	var h = '';
	var w = '';
	var eixo = '';
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
	    if((img.data('he') / 2) < 100){
		h = '100';
		w = '';
		eixo = 'x';
	    }else{
		h = '';
		w = '320';
		eixo = 'y';
	    }
	    img.attr({
		height: h,
		width: w
	    }).css({
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
	var img = $("input[name='foto']");
	var h = '';
	var w = '';
	var eixo;
	
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
		h = '228';
		w = '';
		eixo = 'x';
	    }else{
		h = '';
		w = '256';
		eixo = 'y';
	    }
	    $("#photo").attr({
		height: h,
		width: w
	    }).css({
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
	var codigo = $("input[name='count']").val(); 
	var id_tip = $("#codigo_tip").val();
	var central = $("#optimg").val(); // imagem centralizada;
	var imagem = $("input[name='foto']").val();
	$("#mudou").val('n');
	 
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
	
	// Verificação para saber se é uma nova tip ou atualização
	// Caso seja atualização, se foi ou não trocada a imagem.
	var n = imagem.match("/*_tip*");
	
	if(n === null){
	    var canvas = document.getElementById('canvas');
	    var context = canvas.getContext('2d');
	    
	    var imageObj = new Image();
	    imageObj.src = imagem;
	    
	    imageObj.onload = function() {
		
		var posicao = updateCoords(); // pega LEFT x TOP
		var n = posicao.split('/'); // separa as posições LEFT x TOP
		var sx = n[0].replace("px",""); // retira o PX deixando somente o número negativo do LEFT
		var sy = n[1].replace("px",""); // retira o PX deixando somente o número negativo do TOP
		sx = sx * -1; // transforma o negativo em positivo do LEFT
		sy = sy * -1; // transforma o negativo em positivo do TOP

		var sourceX = sx * 2.5; // imagem original X
		var sourceY = sy * 2.5; // imagem original Y

		var w = 640;
		var h = 570;

		if(central === 's'){
		    w = $("#photo").width() * 2.5;
		    h = $("#photo").height() * 2.5;

		    sourceX = (sx / 2.5) - sourceX;
		    sourceY =  (sy / 2.5) - sourceY;

		    context.drawImage(imageObj, sourceX, sourceY, w, h);
		}else{
		    // SOMENTE CROP
		    if(imageObj.width < 640 || imageObj.height < 570){
			w = $("#photo").width() * 2.5;
			h = $("#photo").height() * 2.5;

			sourceX = (sx / 2.5) - sourceX;
			sourceY =  (sy / 2.5) - sourceY;

			context.drawImage(imageObj, sourceX, sourceY, w, h);
		    }else{
			context.drawImage(imageObj, sourceX, sourceY, w, h, 0, 0, 640, 570);
		    }
		}

		var img_pronta = canvas.toDataURL("image/jpeg");
		
		context.clearRect(0,0,canvas.width,canvas.height);
		
		var dados = 'id_tip='+id_tip+'&codigo='+codigo+'&img='+img_pronta+'&titulo='+titulo+'&sub='+sub+'&mensagem='+mensagem;

		$.ajax({
		    type: 'post',
		    dataType: 'json',
		    data: dados,
		    async: false,
		    url: ur+'web/grava_tip',
		    beforeSend: function(){
			$("#loader").fadeIn();
			$("#addtip").html('Processando...');
		    },
		    success: function(resp){
			$("#loader").fadeOut();
			$(".menu_foto").fadeOut();
			if(resp.erro === 'ok'){
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
		$("#loader").fadeOut();
	    };
	}else{
	    var dados = 'id_tip='+id_tip+'&codigo='+codigo+'&img=sem&titulo='+titulo+'&sub='+sub+'&mensagem='+mensagem;
	    
	    $.ajax({
		type: 'post',
		dataType: 'json',
		data: dados,
		async: false,
		url: ur+'web/grava_tip',
		beforeSend: function(){
		    $("#loader").fadeIn();
		    $("#addtip").html('Processando...');
		},
		success: function(resp){
		    $("#loader").fadeOut();
		    $(".menu_foto").fadeOut();
		    if(resp.erro === 'ok'){			
			$("#tip_"+id_tip).data('titulo',titulo);
			$("#tip_"+id_tip).data('sub',sub);
			$("#tip_"+id_tip).data('descricao',mensagem);
			$(".esconde, #num_tip, #loader").fadeOut();
			$("#tit_tip, #sub_tip, #men_tip").html('');
			$("#tela").html('');
		    }
		    $("#addtip").html('Salvar');
		}
	    });
	}
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
        $(this).parent().fadeOut();
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