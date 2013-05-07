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
	    $(vai).focus().css({border: '1px solid #900'});
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
	$("#tela").html('').html(data.img+'<input type="hidden" id="arq" name="foto" value="'+data.arquivo+'">');
    }else{
	alert("Aviso: "+data.msg);
    }
}

function processJsonc(data){
    if(data.status === '200'){
	$(".capa #telinha").html('').html(data.img+'<input type="hidden" id="arqc" name="fotoc" value="'+data.arquivo+'">');
    }else{
	alert("Aviso: "+data.msg);
    }
}

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
    
    $(".tela").scroll(function(){
	$("#baseDock").fadeOut('fast');
    });
    
    $(document).on('click','.inv',function(){
	var api_images = [$(this).data('url')];
	$.prettyPhoto.open(api_images);
    })
    
    $("#ok_contato").click(function(){
	$("#loader").fadeIn();
    });
    
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
				height: img.height,
			    });
			    $('<input id="arq" data-wi="'+img.width+'" data-he="'+img.height+'" type="hidden" name="foto" value="'+img.src+'">').appendTo("#tela");

			    lar = img.width / 2.5;
			    alt = img.height / 2.5;

			    $("#photo").attr({
				width: lar,
				height: alt,
			    }).css({
				top: '0px',
				left: '0px',
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
    
    $(document).on('click','#computador', function(){
	$("#file-capa").click();
    });
    
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
		var html = '<p align="center"><img src="'+ur+'img/instagram.png"></p><hr>'+resp+''
		$(html).appendTo('.pag');
	    }
	});
    });
    
    $("#get_instagram").click(function(){
	$("#fundo_box").fadeIn(function(){
	    $("#pagina").fadeIn();
	    $(".pag").fadeIn();
	});
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
		var html = '<p align="center"><img src="'+ur+'img/instagram.png"></p><hr>'+resp+''
		$(html).appendTo('.pag');
		$("#redimensionar, #salvar").removeClass('oculto');
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
	
	$.post(ur+'web/encodeimg', {img: image}, function(resp){
	    image = resp.img64;
	    $("#tela").html('');
	    var html = '<img src="'+image+'">';
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
    });
    
    $(document).on('click', '#salvar', function(){
	$("#loader").fadeIn();
	var html;
	var central = $("#optimgc").val(); // imagem centralizada;
	
	if(imagem === ''){
	    alert("Sua Capa precisa de uma imagem");
	    $("#loader").fadeOut();
	    return false;
	}
	 
	var imagem = $("input[name='fotoc']").val();
	var imageObj = new Image();
	 
	imageObj.src = imagem;
	 
	imageObj.onload = function() {
	    var canvas = document.getElementById('canvascapa');
	    var context = canvas.getContext('2d');
	    
	    var codigo = $("input[name='count']").val();
	    var posicao = coordsCapa(); // pega LEFT x TOP
	    var n = posicao.split('/'); // separa as posições LEFT x TOP
	    var sx = n[0].replace("px",""); // retira o PX deixando somente o número negativo do LEFT
	    var sy = n[1].replace("px",""); // retira o PX deixando somente o número negativo do TOP
	    sx = sx * -1; // transforma o negativo em positivo do LEFT
	    sy = sy * -1; // transforma o negativo em positivo do TOP
	    
	    var sourceX = sx * 2; // imagem original X
	    var sourceY = sy * 2; // imagem original Y
	     
	    var w = 640;
	    var h = 200;
	     
	    if(central === 's'){
		w = $("#photoc").width() * 2;
		h = $("#photoc").height() * 2;
		
		sourceX = (sx / 2) - sourceX;
		sourceY =  (sy / 2) - sourceY;
		
		context.drawImage(imageObj, sourceX, sourceY, w, h);
	    }else{
		// SOMENTE CROP
		if(imageObj.width < 640 || imageObj.height < 200){
		    w = $("#photoc").width() * 2;
		    h = $("#photoc").height() * 2;

		    sourceX = (sx / 2) - sourceX;
		    sourceY =  (sy / 2) - sourceY;

		    context.drawImage(imageObj, sourceX, sourceY, w, h);
		}else{
		    context.drawImage(imageObj, sourceX, sourceY, w, h, 0, 0, 640, 200);
		}
	    }
	    var img_pronta = canvas.toDataURL("image/jpeg");
	    
	    context.clearRect(0,0,canvas.width,canvas.height);
	    
	    var dados = 'codigo='+codigo+'&img='+img_pronta;
	     
	    
	    $.ajax({
		type: 'post',
		dataType: 'json',
		data: dados,
		async: false,
		url: ur+'web/grava_capa',
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
		left: '0px',
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
	 
	if(imagem === ''){
	    alert("Sua Tip precisa de uma imagem");
	    $("#loader").fadeOut();
	    $("#addtip").html('Salvar');
	    return false;
	}
	 
	if(titulo === ''){
	    alert("Sua Tip precisa de um título");
	    $("input[name='titulo']").focus().css({
		border: '1px solid #900',
	    });
	    $("#loader").fadeOut();
	    $("#addtip").html('Salvar');
	    return false;
	}
	 
	if(sub === ''){
	    alert("Sua Tip precisa de um sub-título");
	    $("input[name='subtitulo']").focus().css({
		border: '1px solid #900',
	    });
	    $("#loader").fadeOut();
	    $("#addtip").html('Salvar');
	    return false;
	}
	 
	if(mensagem === ''){
	    alert("Sua Tip precisa de uma descrição");
	    $("#men").focus().css({
		border: '1px solid #900',
	    });
	    $("#loader").fadeOut();
	    $("#addtip").html('Salvar');
	    return false;
	}
	
	$("#photo").mouseover();
	
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