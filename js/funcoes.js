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
	$("#ajuste_automatico").fadeIn();
	$("#tela").html('').html(data.img+'<input type="hidden" name="foto" value="'+data.arquivo+'">');
    }else{
	alert("Aviso: "+data.msg);
    }
}

function processJsonc(data){
    $(".capa #telinha").html('').html(data.img);
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

function gravaData(valor){
    alert(valor);
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

$(document).ready(function(){
    var ur = $('#ur').data('url');
    
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
    
    $(".hastags").tagsInput({
	'width': '100%',
	'height': '58px',
	'onAddTag': function(){
	    addTag($(".hastags").val(),$(".hastags").data("codigo"),ur);
	},
    });
    
    $(document).on('click','#uploadpc', function(){
	$("#BrowserHidden").click();
    });
    
    $(document).on('mouseover','#tela', function(){
	if($("#ajuste_automatico").is(":visible")){
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
		    if((limity + ui.position.top) < 240){
			var alt = (limity - 240);
			$(this).animate({
			    top: '-'+alt+'px',
			},200);
		    }
		    
		    if(ui.position.left > 0){
			$(this).animate({
			    left: '0px',
			},200);
		    }
		    if((limitx + ui.position.left) < 270){
			var lar = (limitx - 270);
			$(this).animate({
			    left: '-'+lar+'px',
			},200);
		    }
		}
	    });
	}
    });
    
    $(".friends").tagsInput({
	'width': '100%',
	'height': '100px',
	'defaultText': 'Adicionar email'
    });
    
    $("a[rel^='prettyPhoto']").prettyPhoto({
	default_width: 700,
	default_height: 480,
	show_title: false,
	modal: false,
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
	    url: ur+'auth/fotos_facebook',
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
	    url: ur+'auth/fotos_instagram',
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
    
    $(document).on('click','#photo',function(e){
	var alt = $("#photo").height();
	var lar = $("#photo").width();
	var ofs = $("#photo").offset();
	
	alert("Altura: "+alt+" - Largura: "+lar+" - Posição: "+(e.clientX - ofs.left)+"x"+(e.clientY - ofs.top));
    });
    
    $(document).on('click','.fti',function(){
	var img = $(this).data('alta');
	var local = $(this).data('local');
	
	$.ajax({
	   type: 'post',
	   dataType: 'json',
	   data: 'imgi='+img+'&local='+local,
	   url: ur+'web/img_instagram',
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
    
    $("#ok_data").click(function(){
	if($("#calendario").val() === ''){
	    return false;
	}
    });
    
    $("#calendario").datepicker({
	dateFormat: 'dd/mm/yy',
	monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
	dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
	dayNamesMin: ['Do','Se','Te','Qu','Qu','Se','Sa'],
	minDate: '+0',
    });
    
    $(document).on('click','#ajuste_automatico', function(){
	var opt = $("#optimg");
	var img = $("#photo");
	var h = '';
	var w = '';
	var eixo = '';
	
	if(opt.val() === 's'){
	    opt.val('n');
	    $("#photo").attr({
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
	    if(img.data('wi') > img.data('he')){
		h = '240';
		w = '';
		eixo = 'x';
	    }else{
		h = '';
		w = '270';
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
    
    $(".mozaico").click(function(){
	var dis = $(this).data("disabled");
	
	$("#codigo_tip").val($(this).data('codigo'));
	$("input[name='titulo']").val($(this).data("titulo"));
	$("input[name='subtitulo']").val($(this).data("sub"));
	$("textarea[name='descricao']").val($(this).data("descricao"));
	if($(this).data("imagem") !== "no_image.jpg"){
	    $("input[name='foto']").val($(this).data('imagem'));
	    $("#tela").html('<img id="photo" width="270" height="240" src="'+ur+'tips/'+$(this).data('imagem')+'">').fadeIn();
	}else{
	    $("#tela").html('<img height="240" width="270" src="'+ur+'tips/no_image.jpg">').fadeIn();
	}
	$("#optimg").val('n');

	$("#ntip").html('<h5>Tip <strong>#'+$(this).data('tip')+'</strong> - Dia '+$(this).data('mostra')+'</h5>');
	$("#num_tip").html($(this).data('tip')+'/'+$(this).data('dias'));
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
	$("#num_tip").html('');
	$(".esconde").fadeOut();
    });
    
    $("#ok_volta").click(function(){
	var id = $(this).data("id");
	redirect(ur+'web/tips/'+id+'.html');
	return false;
    });
    
    $('#BrowserHidden').on('change',function(){
	var valor = $(this).val();
	$("#FileField").val(valor);
	$('#formtip').ajaxForm({
	    dataType: 'json',
	    success:   processJson
        }).submit();
     });
     
     $('#BrowserHiddenc').on('change',function(){
	var valor = $(this).val();
	$("#FileFieldc").val(valor);
	$('#formcapa').ajaxForm({
	    dataType: 'json',
	    success:   processJsonc
        }).submit();
     });
     
     $("#addtip").click(function(){
	 var imagem = $("input[name='foto']").val();
	 var titulo = $("input[name='titulo']").val();
	 var sub = $("input[name='subtitulo']").val();
	 var mensagem = $("#men").val();
	 var codigo = $("input[name='count']").val();
	 var id_tip = $("#codigo_tip").val();
	 var central = $("#optimg").val();
	 var posicao = updateCoords();
	 
	 var larimg = $("#photo").width();
	 var altimg = $("#photo").height();
	 
	 var dados = 'id_tip='+id_tip+'&codigo='+codigo+'&img='+imagem+'&central='+central+'&titulo='+titulo+'&sub='+sub+'&mensagem='+mensagem+'&largura='+larimg+'&altura='+altimg+'&posicao='+posicao;
	 
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
    
    $("input[name='dias_projeto']").blur(function(){
        var valor = $(this).val();
	if(valor !== ''){
	    var pagto = 9.9;

	    if(valor <= 10){
		pagto = 9.9;
	    }else{
		for(i=11;i <= valor;i++){
		    pagto = pagto + 1;
		}
	    }
	    $("#vlr_proj").val(pagto);
	    $("#resultado_dias").html("Valor a pagar: R$ "+pagto);
	}
    });
});