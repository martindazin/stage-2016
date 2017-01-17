<div class="wrap">
	<div id="icon-edit" class="icon32"><br></div>
	<h2>Actualité de l'agence</h2>
	<div id="poststuff">
		<div class="postbox">
			<h3 class="hndle">Éditer l'actualité</h3>
			<div class="inside">
				<div id="titlediv">
					<input id="title" class="actutitle" type="text" autocomplete="off" value="<?php echo get_option('hl_actu_titre'); ?>" size="30" name="actu_title" placeholder="Titre de l'actualité" />
				</div>
				<?php 
					/*if(get_option('hl_actu_content')){
						$content = get_option('hl_actu_content');
					}else{
						$content = '';
					}
					$editor_id = 'actueditor';
					wp_editor( $content, $editor_id );*/
				?>
				<button class="button button-primary button-large envoyer_actu">Enregistrer</button>
			</div>
		</div>
	</div>
</div>
<script>
var _path = '<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php';
jQuery(function($){
	$('.inside').on('click','.envoyer_actu',function(e){
		e.preventDefault();
		var title = $('.actutitle').val();
		var actueditor = $('#actueditor');
		var content = tinyMCE.activeEditor.getContent(actueditor);
		console.log(title);
		console.log(content);
		//alert(content);
		if(title !=='' && content!==''){
			jQuery.ajax({
				type: 'post',
				url: _path,
				data: {title:title,content:content,action:'hl_update_actu'},
				success: function(_data){
					var result = jQuery.parseJSON(_data);
					if(result.success){
						console.log(result.message);
						if($('#message').length){
							$('#message').fadeOut(function(){$('#message').remove();$('h2').after('<div class="updated below-h2" id="message"><p>'+result.message+'</p></div>');})
						}else{
							$('h2').after('<div class="updated below-h2" id="message"><p>'+result.message+'</p></div>');
						}
					}else{
						if($('#message').length){
							$('#message').fadeOut(function(){$('#message').remove();$('h2').after('<div class="error below-h2" id="message"><p>'+result.message+'</p></div>');})
						}else{
							$('h2').after('<div class="error below-h2" id="message"><p>'+result.message+'</p></div>');
						}
						alert(result.message);
					}
					$('html, body').animate({scrollTop : 0},800);
				}
			});
		}else{
			alert("Un des champs est vide...");
		}
	});
});
</script>