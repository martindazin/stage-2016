<style>.site,.mails{display:none;}.enregistrer_client{margin-top:1em !important;}</style>
<div class="wrap">
	<div id="icon-edit" class="icon32"><br></div>

	<!-- <h2>Formulaire permettant d'ajouter un client pour l'Espace disque et l'Espace Email</h2> -->
	<div id="poststuff">
		<div class="postbox">
			<h3 class="hndle">Formulaire permettant d'ajouter un client pour l'Espace disque et l'Espace Email</h3>
			<div class="inside">
				<label><span style="display:inline-block;width:100px;">Nom du client : </span><input type="text" nom="client" class="client" value="" /></label><br/>
				<label><span style="display:inline-block;width:100px;">Url du site : </span><input type="text" nom="url" class="url" value="" /></label><br/>
				<h4>Site Internet</h4>
				<label>Espace pour le site : <input type="checkbox" nom="is_site" class="is_site" /></label><br/>
				<div class="site">
					<label>Espace disque occupé par le site : <input type="text" nom="e_site_o" class="e_site_o" value="" /> Mo</label><br/>
					<label>Espace disque alloué pour le site : <input type="text" nom="e_site_a" class="e_site_a" value="" /> Mo</label><br/>
				</div>
				<label>Espace pour les emails : <input type="checkbox" nom="is_emails" class="is_emails" /></label><br/>
				<div class="mails">
					<label>Espace disque occupé par les emails : <input type="text" nom="e_mails_o" class="e_mails_o" value="" /> Mo</label><br/>
					<label>Espace disque alloué pour les emails : <input type="text" nom="e_mails_a" class="e_mails_a" value="" /> Mo</label><br/>
				</div>
				<button class="button button-primary button-large enregistrer_client">Enregistrer</button>
			</div>
		</div>
		<h1>Liste des clients - Espace disque</h1>
		<div class="api_table">
		<?php 
			//Prepare Table of elements
			$wp_list_table = new Link_List_Table();

			echo '<form method="post">
			    	<input type="hidden" name="page" value="'.$_REQUEST['page'].'"/>
			    	<p class="search-box">
						<label class="screen-reader-text" for="search_id-search-input">
						search:</label> 
						<input id="search_id-search-input" type="text" name="s" value="" /> 
						<input id="search-submit" class="button" type="submit" name="" value="Recherche" />
					</p>
				</form>';

			if( isset($_POST['s']) ){
	        	$wp_list_table->prepare_items($_POST['s']);
	        } else {
	        	$wp_list_table->prepare_items();
	        }
			
			//Table of elements
			$wp_list_table->display();
		?>
		</div>
	</div>

</div>

<script>
	var _path = '<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php';
	jQuery(function($){
		// Gestion des checkboxes
		$('label').on('click','input[type="checkbox"]',function(){
			if ($(this).hasClass('is_site')){
				$('.site').slideToggle();
			}else{
				$('.mails').slideToggle();
			}
		});

		// Edition d'une information du tableau
		$('#the-list').on('click','td',function(){
			var content = $(this).html();
			if(!$(this).children('input').length && !$(this).hasClass('col_link_client') && !$(this).hasClass('cell_active') && !$(this).hasClass('colspanchange')){
				$(this).addClass('cell_active');
				$(this).html('<input type="text" value="'+content+'" /><input type="hidden" value="'+content+'" /><br/><button class="button button-primary btn_edit">Modifier</button><button class="button button-secondary btn_edit_abort">Annuler</button>');
			}
		});

		// Modifier un champ du tableau
		$('#the-list').on('click','.btn_edit',function(){
			var $this = $(this);
			var content = $(this).parent().find('input[type="text"]').val();
			var cell = $(this).parent().attr('data-attr');
			var id = $(this).parents('tr').attr('data-id');
			jQuery.ajax({
				type: 'post',
				url: _path,
				data: {id:id,cell:cell,content:content,action:'hl_update_client'},
				success: function(_data){
					var result = jQuery.parseJSON(_data);
					if(result.success){
						var td = $this.parent();
						$this.parent().html('').html(content);
						setTimeout(function() { 
							td.removeClass('cell_active');
				        }, 500);
						console.log('Success');
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
		});

		// Annuler la modification
		$('#the-list').on('click','.btn_edit_abort',function(){
			var hidden = $(this).parent().children('input[type="hidden"]').val();
			var td = $(this).parent();
			$(this).parent().html('').html(hidden);
			setTimeout(function() { 
				td.removeClass('cell_active');
	        }, 500);
		});

		// Suppression d'un client
		$('#the-list').on('click','a.submitdelete',function(e){
			e.preventDefault();
			var id = $(this).attr("data-id");
			if(confirm('Etes-vous sûr de vouloir supprimer ce client ?')){
				jQuery.ajax({
					type: 'post',
					url: _path,
					data: {id:id,action:'hl_delete_client'},
					success: function(_data){
						var result = jQuery.parseJSON(_data);
						if(result.success){
							$('#the-list').find('#record_'+id).fadeOut('slow',function(){$(this).remove();});
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
			}
		});

		// Enregistrement d'un nouveau client
		$('.inside').on('click','.enregistrer_client',function(e){
			e.preventDefault();
			var client = $('.client').val();
			var url = $('.url').val();
			var e_site_o = $('.e_site_o').val();
			if(e_site_o == ''){e_site_o = 0;}
			var e_site_a = $('.e_site_a').val();
			if(e_site_a == ''){e_site_a = 0;}
			var e_mails_o = $('.e_mails_o').val();
			if(e_mails_o == ''){e_mails_o = 0;}
			var e_mails_a = $('.e_mails_a').val();
			if(e_mails_a == ''){e_mails_a = 0;}

			if(client == ''){
				alert('Vous devez entrer le nom du client...');
				return false;
			}

			if(url == ''){
				alert('Vous devez entrer l\'url du site client...');
				return false;
			}

			jQuery.ajax({
				type: 'post',
				url: _path,
				data: {client:client,url:url,e_site_o:e_site_o,e_site_a:e_site_a,e_mail_o:e_mails_o,e_mail_a:e_mails_a,action:'hl_add_client'},
				success: function(_data){
					var result = jQuery.parseJSON(_data);
					if(result.success){
						$('#the-list').prepend(result.message);
						$('.client').val('');
						$('.url').val('');
						$('.e_site_o').val('');
						$('.e_site_a').val('');
						$('.e_mails_o').val('');
						$('.e_mails_a').val('');
						$('.site').hide();
						$('.mails').hide();
						$('.is_site').removeAttr('checked');
						$('.is_emails').removeAttr('checked');
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
		});
	});
</script>