<div class="wrap">
	<div id="icon-edit" class="icon32"><br></div>
	<h2>Informations sur l'agence</h2>
	<div id="poststuff">
		<div class="postbox">
			<h3 class="hndle">Encart de présentation</h3>
			<div class="inside">
				<?php 
				$content = get_option( 'hl_infos' );
				$editor_id = 'infoseditor';
				wp_editor( $content, $editor_id );
				?>
				<button style="margin-top:1em;" class="button button-primary button-large envoyer_info">Mettre à jour</button>
			</div>
		</div>
		<div class="postbox">
			<h3 class="hndle">Test</h3>
			<div class="inside">
			<?php
			include_once 'AtmApiCaller.php';

			// $apicaller = new AtmApiCaller('ATMUPDATES', '28e336ac6c9423d946ba02d19c6a2632', 'http://dev-atmospherecommunication.fr/demo/wp-content/plugins/atm_api_client/atm_api.php');
			$apicaller = new AtmApiCaller('ATMUPDATES', '28e336ac6c9423d946ba02d19c6a2632', 'http://www.atmospherecommunication.fr/');

			$items = $apicaller->sendRequest(array(
				'controller' => 'atmupdates',
			    'action' => 'read'
			));
			echo '<code><pre>';
			print_r($items);
			echo '</pre></code>';
			?>
			</div>
		</div>
	</div>
</div>
<script>
var _path = '<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php';
jQuery(function($){
	$('.inside').on('click','.envoyer_info',function(e){
		e.preventDefault();
		var content = $('#infoseditor').val();
		if(content!==''){
			jQuery.ajax({
				type: 'post',
				url: _path,
				data: {content:content,action:'hl_update_infos'},
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
				}
			});
		}else{
			alert("Vous n'avez pas mis de contenu...");
		}
	});
});
</script>