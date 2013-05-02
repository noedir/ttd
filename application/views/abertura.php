<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1"/>
  
  <title><?php echo $title; ?></title>
  <link rel="shortcut icon" href="<?php echo base_url(); ?>images/favicon.ico">
  <link rel="icon" href="<?php echo base_url(); ?>images/favicon.ico">
  <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url(); ?>css/styles.css">
  <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url(); ?>css/bigvideo.css">
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.imagesloaded.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>js/modernizr-2.5.3.min.js"></script>
  <script type="text/javascript" src="http://vjs.zencdn.net/c/video.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>js/bigvideo.js"></script>
  
   
   <link rel="stylesheet" type="text/css" media="only screen and (max-device-width: 480px)" href="<?php echo base_url(); ?>css/mobile-device.css" />
   <link rel="stylesheet" type="text/css" media="only screen and (min-device-width: 768px) and (max-device-width: 1024px)" href="<?php echo base_url(); ?>css/mobile-device.css" />
   
</head>

<body>

	<div id="baseGeral">
        
          	<div id="baseEmBreve">
            
            		<img class="logo" src="images/logotipo_header.jpg">
                    
                    <br />
                    
                     <p>         
                         uma nova experiência
                         <br />para a sua contagem regressiva.
                         <br />atinja o máximo de sua expectativa.
                     </p>
              
          	</div>
            
            <div style="clear:both"></div>
            
                          
            <div class="emBrevetext">
                    
                 <p>Em breve disponível na AppStore e Google Play</p>
                 
                 <br />
                 
                 
		    <input name="email_cadastro" id="email_cadastro" placeholder="Cadastre e seja o primeiro">
                    <input id="ok_envio" type="submit" value="OK"><img id="loading" src="images/loading.gif">
                 
                 
                 <br />

                        
             </div>
          
    </div>

    <script type="text/javascript">
    $(function() {
	var BV = new $.BigVideo();
	BV.init();
	if (Modernizr.touch) {
	    BV.show('images/big_image_video.jpg');
	} else {
	    BV.show('video/video_background.mp4',{ambient:true});
	}
	$("#ok_envio").click(function () {
	    var email=$("#email_cadastro").val();
		if (email === ""){
		    alert("Preencha com seu e-mail!");
		    return false;
		}
	    $.ajax({
		type: "post",
		data: "email="+email,
		dataType: "html",
		url: "<?php echo base_url();?>web/cad_email",
		before: function(){
		    $("#loading").show();
		},
		success: function(result){
		    $("#loading").hide();
		    if (result === ""){
			alert("Email cadastrado com sucesso!");
			$("#email_cadastro").val('');
		    } else {
			alert(result);
		    }
		}
	    });
	});
    });
    </script>
    <script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-40630009-1', 'tiltheday.com');
	ga('send', 'pageview');

    </script>
</body>
</html>