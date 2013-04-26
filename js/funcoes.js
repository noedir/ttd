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
	$("#tela").html('').html(data.img+'<input type="hidden" name="foto" value="'+data.arquivo+'">');
    }else{
	alert("Aviso: "+data.msg);
    }
}

function processJsonc(data){
    if(data.status === '200'){
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
    var ur = $('#ur').data('url');
    var html = '';
    var url = '';
    var count = $("#convida_face").data('count');
    $.getJSON(ur+'auth/get_amigos', function(resp){
	$.each(resp, function(index, value){
	    url = ur+'auth/send_dialog_face/'+value['id']+'/'+count;
	    html += '<div class="fb"><img class="pict" src="'+value['pic']+'"><p>'+value['nome']+'</p><a rel="prettyPhoto[face]" href="'+url+'"><img class="inv" src="'+ur+'img/invite_facebook.jpg"></a></div>';
	});

	$("#lista_facebook").html(html);
    });
}

$(document).ready(function(){
    var ur = $('#ur').data('url');    
    $("#file-up").on('change', function(e){
	var lar;
	var alt;
	
	$("#ajuste_automatico").fadeIn();
	$("#tela").html('');
	
	var files = e.target.files;
	var f = files[0];
        var reader = new FileReader();
	reader.onload = (function () {
	    return function (e) {
		window.loadImage(
		    e.target.result,
		    function (img) {
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
			minHeight: 570,
		    }
		);
	    };
	})(f);
	reader.readAsDataURL(f);
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
    
    $("input[name='dias_projeto']").on('blur', function(){
	var nome = $("input[name='nome_usuario']");
	var proj = $("input[name='nome_projeto']");
	var dias = $("input[name='dias_projeto']");
	
	if(dias !== ''){
	    $.ajax({
		type: 'post',
		dataType: 'json',
		data: 'nome='+nome.val()+'&proj='+proj.val()+'&dias='+dias.val(),
		url: ur+'web/nomeunico',
		success: function(resp){
		    $("input[name='nomeunico']").val(resp.result).focus();
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
    
    $("#controle_capa").click(function(){
	$("#opcoes_capa").fadeToggle();
    });
    
    $(document).on('click','#computador', function(){
	$("#BrowserHiddenc").click();
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
	//}
    });
    
    $(".friends").tagsInput({
	'width': '100%',
	'height': '100px',
	'defaultText': 'Adicionar email'
    });
    
    $("a[rel^='prettyPhoto']").prettyPhoto({
	modal: true,
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
		$(".loader").fadeIn();
	    },
	    success: function(resp){
		var html = '<p align="center"><img src="'+ur+'img/facebook_photo.jpg"></p><hr>'+resp+''
		$(html).appendTo('.pag');
		$(".loader").fadeOut();
	    }
	});
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
		$(".loader").fadeIn();
	    },
	    success: function(resp){
		var html = '<p align="center"><img src="'+ur+'img/facebook_photo.jpg"></p><hr>'+resp+''
		$(html).appendTo('.pag');
		$(".loader").fadeOut();
	    }
	});
    });
    
    $("#pega_instagram").click(function(){
	$("#fundo_box").fadeIn(function(){
	    $("#pagina").fadeIn();
	    $(".pag").fadeIn();
	});
	$.ajax({
	    type: 'post',
	    dataType: 'html',
	    url: ur+'auth/fotos_instagram/tips',
	    beforeSend: function(){
		$(".loader").fadeIn();
	    },
	    success: function(resp){
		var html = '<p align="center"><img src="'+ur+'img/instagram.png"></p><hr>'+resp+''
		$(html).appendTo('.pag');
		$(".loader").fadeOut();
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
	    url: ur+'auth/fotos_instagram/capa',
	    beforeSend: function(){
		$(".loader").fadeIn();
	    },
	    success: function(resp){
		var html = '<p align="center"><img src="'+ur+'img/instagram.png"></p><hr>'+resp+''
		$(html).appendTo('.pag');
		$(".loader").fadeOut();
	    }
	});
    });
    
    
    $("#fundo_box").click(function(){
	$("#pagina").fadeOut();
	$(".pag").fadeOut().html('');
	$(this).fadeOut();
    });
    
    $(document).on('click','.ftitips',function(){
	var img = $(this).data('alta');
	var local = $(this).data('local');
	
	$.ajax({
	   type: 'post',
	   dataType: 'json',
	   data: 'imgi='+img+'&local='+local,
	   url: ur+'web/img_instagram/tips',
	   success: function(resp){
	       if(resp.erro === 'sim'){
		   alert('Aviso: A imagem precisa ter no mínimo 640 x 570 pixels.')
	       }else{
		   $("#ajuste_automatico").fadeIn();
		    $("#pagina").fadeOut();
		    $(".pag").fadeOut().html('');
		    $("#fundo_box").fadeOut();
		   $("#tela").html('').html('<img id="photo" data-he="'+resp.height+'" data-wi="'+resp.width+'" src="'+ur+'/tips/tmp_'+resp.url+'">'+'<input type="hidden" name="foto" value="'+resp.url+'">');
	       }
	   }
	});
    });
    
    $("#triggerSelect").on({
	click: function(){
	      $("#baseDock").fadeToggle();
	},
    });
    
    $(document).on('click','.fticapa',function(){
	var img = $(this).data('alta');
	var local = $(this).data('local');
	var cod_count = $("#cod_count").val();
	
	$.ajax({
	   type: 'post',
	   dataType: 'json',
	   data: 'imgi='+img+'&local='+local+'&idcount='+cod_count,
	   url: ur+'web/img_instagram/capa',
	   success: function(resp){
	       if(resp.erro === 'sim'){
		   alert('Aviso: A imagem precisa ter no mínimo 640 x 200 pixels.')
	       }else{
		   $("#pagina").fadeOut();
		   $(".pag").fadeOut().html('');
		   $("#fundo_box").fadeOut();
		   $(".capa #telinha").html('').html('<img id="photoc" data-he="'+resp.height+'" data-wi="'+resp.width+'" src="'+ur+'/capa/tmp_'+resp.url+'">'+'<input type="hidden" name="fotoc" value="'+resp.url+'">');
	       }
	   }
	});
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
    
    $(document).on('click','#salvar', function(){
	var html = '';
	var imagem = $("input[name='fotoc']").val();
	var codigo = $("input[name='cod_count']").val();
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
	    url: ur+'web/grava_capa',
	    success: function(resp){
		if(resp.erro !== "sim"){
		    $("#opcoes_capa").fadeOut();
		    
		    html = '<img src="'+resp.msg+'" width="320" height="100">';
		    
		    $(".capa #telinha").html('').html(html);
		    
		    $('#photoc').draggable( "destroy" );
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
		height: '',
		width: '',
	    }).css({
		top: '0px',
		left: '0px',
	    }).draggable({
		axis: ''
	    });
	}else{
	    opt.val('s');
	    
	    if(img.data('he') > 100){
		h = '';
		w = '320';
		eixo = 'y';
	    }else{
		h = '';
		w = '';
		eixo = 'x';
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
	var eixo = '';
	
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
		left: '0px',
	    }).draggable({
		axis: eixo
	    });
	}
    });
    
    $(document).on('change','.campos', function(){
	$("#mudou").val('s');
    });
    
    $(".mozaico").click(function(){
	if($("#mudou").val() === 's'){
	    if(!confirm("Os dados dessa tip serão perdidos. Continuar assim mesmo?")){
		return false;
	    }
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
	    $("#tela").html('<img id="photo" width="256" height="228" src="'+ur+'tips/'+$(this).data('imagem')+'">').fadeIn();
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
	    $(".menu_foto").fadeOut();
	}else{
	    $("#tit").prop("disabled", false);
	    $("#sub").prop("disabled", false);
	    $("#men").prop("disabled", false);
	    $("#addtip").css({display: 'inline-block'});
	    $("#cantip").css({display: 'inline-block'});
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
    
    /*$('#BrowserHidden').on('change',function(){
	var valor = $(this).val();
	$("#FileField").val(valor);
	$('#formtip').ajaxForm({
	    dataType: 'json',
	    uploadProgress: function(event, position, total, percentComplete) {
		$(".barraup").fadeIn();
                $('progress').attr('value',percentComplete);
                $('#porcentagem').html(percentComplete+'%');
            },
	    success:   processJson
        }).submit();
     });*/
     
    $('#BrowserHiddenc').on('change',function(){
	var valor = $(this).val();
	$("#FileFieldc").val(valor);
	$('#formcapa').ajaxForm({
	    dataType: 'json',
	    success:   processJsonc
        }).submit();
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
	 var titulo = $("input[name='titulo']").val();
	 var sub = $("input[name='subtitulo']").val();
	 var mensagem = $("#men").val();
	 var central = $("#optimg").val(); // imagem centralizada;
	 $("#mudou").val('n');
	 
	 if(imagem === ''){
	     alert("Sua Tip precisa de uma imagem");
	     return false;
	 }
	 
	 if(titulo === ''){
	     alert("Sua Tip precisa de um título");
	     $("input[name='titulo']").focus().css({
		 border: '1px solid #900',
	     });
	     return false;
	 }
	 
	 if(sub === ''){
	     alert("Sua Tip precisa de um sub-título");
	     $("input[name='subtitulo']").focus().css({
		 border: '1px solid #900',
	     });
	     return false;
	 }
	 
	 if(mensagem === ''){
	     alert("Sua Tip precisa de uma descrição");
	     $("#men").focus().css({
		 border: '1px solid #900',
	     });
	     return false;
	 }
	 
	 var canvas = document.getElementById('canvas');
	 var context = canvas.getContext('2d');
	 var imagem = $("input[name='foto']").val();
	 
	 var imageObj = new Image();
	 
	 imageObj.src = imagem;
	 
	imageObj.onload = function() {
	    var codigo = $("input[name='count']").val(); 
	    var id_tip = $("#codigo_tip").val();
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
		
		if(sx === 0){
		    // RESIZE e CROP
		    context.drawImage(imageObj, 0, 0, w, h, 0, 0, w, h);
		}else if(sy === 0){
		    // RESIZE e CROP
		    context.drawImage(imageObj, sourceX, sourceY, w, h, 0, 0, w, h);
		}
	    }else{
		// SOMENTE CROP
		context.drawImage(imageObj, sourceX, sourceY, w, h, 0, 0, 640, 570);
	    }
	    var i = canvas.toDataURL('image/jpeg');
	    var img_pronta = i;
	     
	    var dados = 'id_tip='+id_tip+'&codigo='+codigo+'&img='+img_pronta+'&titulo='+titulo+'&sub='+sub+'&mensagem='+mensagem;
	     
	    if(confirm("Deseja salvar?")){
		$.ajax({
		    type: 'post',
		    dataType: 'json',
		    data: dados,
		    async: false,
		    url: ur+'web/grava_tip',
		    success: function(resp){
		        $(".menu_foto").fadeOut();
		        if(resp.erro === 'ok'){
			   redirect('');
		        }
		    }
		});
	    }
	};
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
    });
    
    $("button[type='reset']").click(function(){
	redirect(ur+'web/counts');
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
    
    if($("input[name='dias_projeto']").val() !== ''){
	var valor = $(this).val();
	if(valor !== ''){
	    var dia = 0.99;
	    var pagto = (valor * dia) + 1;
	    
	    var num = new Number(pagto);
	    
	    $("#vlr_proj").val(num.toFixed(2));
	    $("#resultado_dias").html("R$ "+num.toFixed(2));
	}
    }
    
    $("input[name='dias_projeto']").blur(function(){
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