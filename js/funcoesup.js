$(document).ready(function(){
    $(".fti").click(function(){
	var img = $(this).attr('src');
	console.log(img);
	$(".menu_foto").fadeOut();
	$("#tela").html('').html(img+'<input type="text" name="foto">');
	$("#photo").draggable({
	    cursor: "move"
	});
	
	$("#pagina").fadeOut();
	$(".pag").fadeOut();
	$(this).fadeOut();
    });
});