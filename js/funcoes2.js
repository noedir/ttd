function redirect(rld){
    if(rld === ''){
	window.location.reload();
    }else{
	window.location.href=rld;
    }
}

function manda_login(){
    var nome = $("input[name='email']").val();
    var senha = $("input[name='senha']").val();
    
    if(nome === ''){ alert("Preencha com seu email"); return false; }
    if(senha === ''){ alert("Preencha com sua senha"); return false; }
}

function retornaValor(nome){
    var ur = $('#ur').data('url');
    var vai = $("input[name='"+nome+"']");
    $.post(ur+'web/disponivel',{nome:vai.val()}, function(ret){
	if(ret === 'false'){
	    $(vai).focus().css({border: '1px solid #e98087', background: '#f5d4d7',});
	    $("input[name='unique']").val('n');
	}else{
	    $(vai).css({border: '1px solid #ddd'});
	    $("input[name='unique']").val('s');
	}
    });
}

function nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

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

function processJsonc(data){
    if(data.status === '200'){
	$("#redimensionar, #salvar").removeClass('oculto');
        $(".capa #telinha").html('').html(data.img+'<input type="hidden" name="fotoc" value="'+data.arquivo+'">');
    }else{
        alert("Aviso: "+data.msg);
    }
}

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

function addTag(valor,codigo,ur){
    $.ajax({
	type: 'post',
	dataType: 'html',
	data: 'tags='+valor+'&codigo='+codigo,
	async: false,
	url: ur+'web/gravatag'
    });
}

function updateCoords(){
    var tp = $("#photo").css('top');
    var lf = $("#photo").css('left');
    var res = lf+'/'+tp;
    return res;
};

function coordsCapa(){
    var tp = $("#photoc").css('top');
    var lf = $("#photoc").css('left');
    var res = lf+'/'+tp;
    return res;
};

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
    var ur = $('#ur').data('url');
    
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
    
    $(".dt_projeto").hover(function(){
	$(".altdata").fadeIn();
    },
    function(){
	$(".altdata").fadeOut();
    });
    
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
    
    $(document).on('click','.inv',function(){
	var api_images = [$(this).data('url')];
	$.prettyPhoto.open(api_images);
    })
    
    $("#ok_contato").click(function(){
	$("#loader").fadeIn();
    });
    
    $(".countGerencia").click(function(){
	var cod = $(this).data('count');
	redirect(ur+'web/tips/'+cod+'.html');
    });
    
    $(".formNovoCount").buttonset();
    
    $('.poshy').tooltip({
	position: {
	    my: "left bottom-10",
	    at: "left top",
	},
	tooltipClass: 'poshy_style',
    });
    
    $("#tabs").tabs();
    
    $(".ui-button-text").on('mouseover', function(){
	$("#seta_priv").fadeIn();
    });
    
    $(".ui-button-text").on('click', function(){
	if($("#seta_priv").css('margin-left') === '98px'){
	    $("#seta_priv").css({
		'margin-left': '30px',
	    });
	}else{
	    $("#seta_priv").css({
		'margin-left': '98px',
	    });
	}
    })
    
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
    
    if($("#convida_face").data('volta') === 'sim'){
	$("#convida_face").click();
	convida_face();
    }
    
    $("#convida_face").click(function(){
	convida_face();
    });
    
    $(".hastags").tagsInput({
	'width': '100%',
	'height': '58px',
	'onAddTag': function(){
	    addTag($(".hastags").val(),$(".hastags").data("codigo"),ur);
	},
    });
    
    
    $(document).on('click','#computador', function(){
        $("#file-capa").click();
    });
    
    $('#file-up').on('change',function(){
	var valor = $(this).val();
	$("#FileField").val(valor);
	$('#formtip').ajaxForm({
	    dataType: 'json',
	    uploadProgress: function(event, position, total, percentComplete) {
	    $(".barraup").fadeIn();
		$('progress').attr('value',percentComplete);
		$('#porcentagem').html(percentComplete+'%');
	    },
	    success: processJson
	    }).submit();
	});

    $('#file-capa').on('change',function(){
	var valor = $(this).val();
	$("#FileFieldc").val(valor);
	$('#formcapa').ajaxForm({
	    dataType: 'json',
	    success: processJsonc
        }).submit();
     });
    
    $(document).on('click','#uploadpc', function(){
	$("#file-up").click();
    });
    
    $(".controle_capa").click(function(){
	$("#opcoes_capa").fadeToggle();
    });
    
    if($(".retcapa").length){
	$(".controle_capa").click();
    };
    
    if($(".rettip").length){
	$(".controle_capa").click();
    };
    
    $(document).on('mouseover','#telinha', function(){
	if($("#opcoes_capa").is(":visible")){
	    $("#photoc").draggable({
		cursor: "move",
		scroll: false,
		snapTolerance: 5,
		stop: function (event,ui){
		    var limitx = $(this).width();
		    var limity = $(this).height();
		    
		    if(ui.position.top > 0){
			$(this).animate({
			    top: '0px',
			},200);
		    }
		    if((limity + ui.position.top) < 100){
			var alt = (limity - 100);
			$(this).animate({
			    top: '-'+alt+'px',
			},200);
		    }
		    
		    if(ui.position.left > 0){
			$(this).animate({
			    left: '0px',
			},200);
		    }
		    if((limitx + ui.position.left) < 320){
			var lar = (limitx - 320);
			$(this).animate({
			    left: '-'+lar+'px',
			},200);
		    }
		}
	    });
	}
    });
    
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
		var limitx = $(this).width();
		var limity = $(this).height();

		if(ui.position.top > 0){
		    $(this).animate({
			top: '0px',
		    },200);
		}
		if((limity + ui.position.top) < 228){
		    var alt = (limity - 228);
		    $(this).animate({
			top: '-'+alt+'px',
		    },200);
		}

		if(ui.position.left > 0){
		    $(this).animate({
			left: '0px',
		    },200);
		}
		if((limitx + ui.position.left) < 256){
		    var lar = (limitx - 256);
		    $(this).animate({
			left: '-'+lar+'px',
		    },200);
		}
	    }
	});
    });
    
    $(".friends").tagsInput({
	'width': '100%',
	'height': '100px',
	'defaultText': 'Adicionar email'
    });
    
    $("a[rel^='prettyPhoto']").prettyPhoto({
	social_tools: false
    });
    
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
		var html = '<p align="center"><img src="'+ur+'img/facebook_photo.jpg"></p><hr>'+resp+''
		$(html).appendTo('.pag');
		$("#loader").fadeOut();
	    }
	});
	$("#loader").fadeOut();
    });
    
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
		var html = '<p align="center"><img src="'+ur+'img/facebook_photo.jpg"></p><hr>'+resp+''
		$(html).appendTo('.pag');
		$("#loader").fadeOut();
	    }
	});
	$("#loader").fadeOut();
    });
    
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
		    var html = '<p align="left" id="instagram_lightbox"><img style="float:left;" src="'+ur+'img/instagram_logo.png"><div id="title_lightbox">Instagram</div><div id="trocar_instagram"><a href="'+ur+'web/sair_instagram/capa_'+count+'">Trocar Conta<br /> do Instagram</a></div><div style="clear:both;"></div></p><hr>'+resp+''
		    $(html).appendTo('.pag');
		}else{
		    alert("Houve um erro. Por favor, tente novamente");
		    $("#fundo_box").click();
		}
		$("#loadering").fadeOut();
            }
});
    });
    
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
                var html = '<p align="left" id="instagram_lightbox"><img style="float:left;" src="'+ur+'img/instagram_logo.png"><div id="title_lightbox">Instagram</div><div id="trocar_instagram"><a href="'+ur+'web/sair_instagram/capa_'+count+'">Trocar Conta<br /> do Instagram</a></div><div style="clear:both;"></div></p><hr>'+resp+''
                $(html).appendTo('.pag');
		$("#redimensionar, #salvar").removeClass('oculto');
                $("#loadering").fadeOut();
            }
        });
    });
    
    
    $("#fundo_box").click(function(){
	$("#pagina").fadeOut();
	$(".pag").fadeOut().html('');
	$("#loader").fadeOut();
	$("#redimensionar, #salvar").addClass("oculto");
	$(this).fadeOut();
    });
    
    $(document).on('click','.ftitips',function(){
	var image = $(this).data('alta');
	
	$.post(ur+'web/img_instagram', {imgi: image, local: 'tips', loca: 'instagram'}, function(resp){
	    image = resp.url;
	    
	    $("#tela").html('');
	    var html = '<img src="'+ur+'tips/'+image+'">';
	    $(html).appendTo("#tela").attr({
		id: 'photo',
		width: 256,
		height: 256
	    }).css({
		top: '0px',
		left: '0px',
	    },"json");
	    $('<input id="arq" data-wi="640" data-he="640" type="hidden" name="foto" value="'+image+'">').appendTo("#tela");
	}, "json");
	$("#ajuste_automatico").fadeIn();
	$("#pagina").fadeOut();
	$(".pag").fadeOut().html('');
	$("#fundo_box").fadeOut();
    });
    
    $("#triggerSelect").on({
	click: function(){
	      $("#baseDock").fadeToggle();
	},
    });
    
    $(document).on('click','.fticapa',function(){
	var image = $(this).data('alta');
	var idcount = $("#cod_count").val();
	$("#loadering").fadeIn();
	$.post(ur+'web/img_instagram', {imgi: image, local: 'capa', loca: 'instagram', idcount: idcount}, function(resp){
	    image = resp.url;
	    $("#telinha").html('');
	    var html = '<img id="photoc" height="" width="320" data-he="640" data-wi="640" src="'+ur+'capa/'+image+'"><input type="hidden" name="fotoc" value="'+image+'">';
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
    
    $("#ok_data").click(function(){
	if($("#calendario").val() === ''){
	    return false;
	}
    });
    
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
    
    $(document).on('click','#redimensionar', function(){
	var opt = $("#optimgc");
	var img = $("#photoc");
	var h = '';
	var w = '';
	var eixo = '';
	if(opt.val() === 's'){
	    opt.val('n');
	    img.attr({
		height: img.data('he'),
		width: img.data('wi')
	    }).css({
		top: '0px',
		left: '0px',
	    }).draggable({
		axis: ''
	    });
	}else{
	    opt.val('s');
	    if((img.data('he')) < 100){
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
		width: w,
	    }).css({
		top: '0px',
		left: '0px',
	    }).draggable({
		axis: eixo
	    });
	}
    });
    
    $(document).on('click','#ajuste_automatico', function(){
	var opt = $("#optimg");
	var img = $("#photo");
	var h = '';
	var w = '';
	var eixo;
	
	if(opt.val() === 's'){
	    opt.val('n');
	    $("#photo").attr({
		height: img.data('he'),
		width: img.data('wi')
	    }).css({
		top: '0px',
		left: '0px',
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
		width: w,
	    }).css({
		top: '0px',
		left: '0px'
	    }).draggable({
		axis: eixo
	    });
	}
    });
    
    $(document).on('change','.campos', function(){
	$("#mudou").val('s');
    });
    
    $(".mozaico").click(function(){
	if($(".trava").length){
	    alert("Você ainda não escolheu uma capa para esse projeto");
	    return false;
	}
	
	if($("#mudou").val() === 's'){
	    if(!confirm("Os dados dessa tip serão perdidos. Continuar assim mesmo?")){
		return false;
	    }
	}
	
	if (!window.File && !window.FileReader && !window.FileList && !window.Blob) {
	    alert("Seu navegador não suporta os recursos dessa página.");
	    return false;
	}
	
	$("#mudou").val('n');
	$(".campos").css({
	    border: '1px solid #ddd',
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
    
    $("#ok_volta").click(function(){
	var id = $(this).data("id");
	redirect(ur+'web/tips/'+id+'.html');
	return false;
    });
    
    $(document).on('blur','#tit', function(){
	if($("#tit").val() !== ''){
	    $("#tit").css({
		border: '1px solid #090',
	    });
	}
    });
     
    $(document).on('blur','#sub', function(){
	if($("#sub").val() !== ''){
	    $("#sub").css({
		border: '1px solid #090',
	    });
	}
    });
     
    $(document).on('blur','#men', function(){
	if($("#men").val() !== ''){
	    $("#men").css({
		border: '1px solid #090',
	    });
	}
    });
     
    $("#addtip").click(function(){
	$("#loader").fadeIn();
	$(this).html("Processando...").delay(100);
	
	var titulo = $("input[name='titulo']").val();
	var sub = $("input[name='subtitulo']").val();
	var mensagem = $("#men").val();
	var codigo = $("input[name='count']").val(); 
	var id_tip = $("#codigo_tip").val();
	var central = $("#optimg").val(); // imagem centralizada;
	var imagem = $("input[name='foto']").val();
	$("#mudou").val('n');
	
	var posicao = updateCoords();

	var larimg = $("#photo").width();
	var altimg = $("#photo").height();
	 
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
		background: '#f5d4d7',
	    });
	    $("#loader").fadeOut();
	    $("#addtip").html('Salvar');
	    return false;
	}
	 
	if(sub === ''){
	    alert("Sua Tip precisa de um sub-título");
	    $("input[name='subtitulo']").focus().css({
		border: '1px solid #e98087',
		background: '#f5d4d7',
	    });
	    $("#loader").fadeOut();
	    $("#addtip").html('Salvar');
	    return false;
	}
	 
	if(mensagem === ''){
	    alert("Sua Tip precisa de uma descrição");
	    $("#men").focus().css({
		border: '1px solid #e98087',
		background: '#f5d4d7',
	    });
	    $("#loader").fadeOut();
	    $("#addtip").html('Salvar');
	    return false;
	}
	
	$("#photo").mouseover();
	
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
     
    $("#tit").keyup(function(){
	var valor = $(this).val();
	$("#tit_tip").html(valor);
    });
     
    $("#sub").keyup(function(){
	var valor = $(this).val();
	$("#sub_tip").html(valor);
    });
     
    $("#men").keyup(function(){
	var valor = $(this).val();
	$("#men_tip").html(nl2br(valor));
    });
    
    $(".exc").click(function(){
	var id = $(this).data('id');
	var ur = $(this).data('ur');
	
	if(confirm("Deseja realmente excluir esse Count? Essa ação não tem retorno.")){
	    window.location.href=ur+"web/excluir_count/"+id;
	}
	
	return false;
    });
    
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
    
    $("#btn_idioma").click(function(){
	$("#lang").fadeToggle();
    });
    
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
    
    $(".fechar").click(function(){
        $(this).parent().fadeOut();
    });
    $(".btn_login").click(function(){
        $("#login").fadeToggle(function(){
            $("input[name='email']").focus();
        });
    });
    $("input[name='email'], input[name='senha']").focus(function(){
        $(this).css('border-color','#ddd');
    });
    
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