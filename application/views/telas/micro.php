<h3>Meu Computador</h3>

<form id="formtip" action="<?php echo base_url(); ?>web/img_upload" method="post" enctype="multipart/form-data">
    <div id="FileUpload">
	<input type="file" name="imagem" id="BrowserHidden">
	<div id="BrowserVisible">
	    <input type="text" id="FileField">
	</div>
    </div>
    <div id="telinha"></div>
</form>