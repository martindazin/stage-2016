<h4><em>Quels types de posts voulez-vous dans la newsletter ?</em></h4>

<?php
	// Fonctions PHP //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

	// Requêtes SQL //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');

	// Ajout des posts à la nl
	if(isset($_POST['action']) && !empty($_POST['action']) && !is_null($_POST['action']) && $_POST['action'] == 'save'){
		// Je mets tous les post_type_x à faux,
		// puis j'enregistre les post_type_x cochés par l'utilisateur
		$typesDePosts = $wpdb->get_results($typeDePosts);
    	foreach ($typesDePosts as $typeDePosts) {
		 	$post_type = $typeDePosts->post_type;
		 	update_option("atm_nl_post_type_".$post_type, 'false', 'yes');
		}

    	echo '<b>'."Vos préférences ont été sauvegardées".'</b><br>';
    	echo '<h1>Visualisation des posts envoyés dans la newsletter</h1>';

    	echo '<div class="col-xs-12 col-md-12 col-lg-12">';
	    	if(!$_POST['case']){
	    		
			   	echo "Aucune case à cochée a été cochée. De ce fait, il n'y a rien a visualiser !";
			} else {
				// Affichage du tableau de prévisualisation des articles
				echo '<table class="table table-hover">';
					echo '<thead>';
						echo '<tr>';
		        			echo '<th>Type de post</th>';
		        			echo '<th>Date de première parution</th>';
		        			echo '<th>Titre du post</th>';
		        			echo '<th>Contenu du post (Résumé)</th>';
		        		echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
						foreach(atm_sanitize($_POST['case']) as $valeur)
						{
							// Enregistrement des types de posts sélectionnés par les cases à cocher en BDD
							// On met les cases cochées à true	
							update_option("atm_nl_post_type_".$valeur, 'true', 'yes');
							
							// Requête SQL retournant les 10 derniers posts pour chaque post_type sélectionné(s)
				     		$affiche10Posts = ("SELECT *
											        FROM ".$table_posts."
											        WHERE `post_type` = '".$valeur."'
											        AND post_status = 'publish'
											     	ORDER BY ID DESC
											        LIMIT 10");

				     		$listeDePosts = $wpdb->get_results($affiche10Posts);
				     		if(isset($listeDePosts) && !empty($listeDePosts) && !is_null($listeDePosts) && is_array($listeDePosts) && $typesDePosts !== false){
								foreach ($listeDePosts as $post) {
				     				echo '<tr>';
				     					$post_type = $post->post_type;
						     			$post_date = $post->post_date;
						     			// On convertit le type de date retourné par la BDD
						     			$dateFinale = date('\L\e d/m/Y \à H\:i', strtotime($post_date));
						     			$post_title = $post->post_title;
						     			$resume = strip_tags($post->post_content);
						     			// Si le post_content a plus de 100 caractères on le coupe
						     			// et on affiche jusqu'au dernier mot complet
						     			if (strlen($resume) > 100) {
						     				$resume = substr($resume, 0, 100);
						     				// Permet de ne pas afficher un mot "coupé" par strlen()
						     				$coupeMot = strrpos($resume, ' ', -1);
						     				$resume = substr($resume, 0, ($coupeMot +1))."[...]";
						     			}
						     			echo '<th>'.$post_type.'</th>';
						     			echo '<th>'.$dateFinale.'</th>';
						     			echo '<th>'.$post_title.'</th>';
						     			echo '<th>'.$resume.'</th>';
					     			echo '</tr>';
					     		}
					     	}
						}
					echo '</tbody>';
				echo '</table>';
			}
		echo '</div>';
	}

	// Ajout d'un newsletter à la BDD
	if(isset($_POST['action']) && !empty($_POST['action']) && !is_null($_POST['action']) && $_POST['action'] == 'save'){
		
		$deuxFois = 0;
		while ($deuxFois < 2) {
			if ($deuxFois == 0) {
				include (ABSPATH.'wp-content/plugins/atm_newsletter_md/views/nl/nl_content.php');

				$nlSubject = substr($newsletterTitre, (stripos($newsletterTitre, 'N')), (stripos($newsletterTitre, '201')+4)-stripos($newsletterTitre, 'N'));
				
				$wpdb->insert($table_nl, array('newsletter_showed_id' => $last_newsletter_showed_id,
												'newsletter_subject' => $nlSubject,
												'newsletter_message' => $affichageDeLaNewsletter,
												'newsletter_date' => current_time('mysql'),
												'newsletter_status' => 'T') 
				);
			}

			if ($deuxFois == 1) {
				include (ABSPATH.'wp-content/plugins/atm_newsletter_md/views/nl/nl_content_draft.php');

				$nlSubject = substr($newsletterTitre, (stripos($newsletterTitre, 'N')), (stripos($newsletterTitre, '201')+4)-stripos($newsletterTitre, 'N'));

				$wpdb->insert($table_nl, array('newsletter_showed_id' => $last_newsletter_showed_id,
										'newsletter_subject' => $nlSubject,
										'newsletter_message' => $affichageDeLaNewsletter,
										'newsletter_date' => current_time('mysql'),
										'newsletter_status' => 'C') 
				);
			}
			$deuxFois++;
		}
		echo "Votre newsletter a bien été créée !";
 	}

    echo '<div class="col-xs-12 col-md-12 col-lg-12">';
	    // Exécution de la requête
	    $typesDePosts = $wpdb->get_results($typeDePosts);
	    if(isset($typesDePosts) && !empty($typesDePosts) && !is_null($typesDePosts) && is_array($typesDePosts) && $typesDePosts !== false){
	    	// Affichage du tableau des types de posts et de leur nombre total
		 	echo '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">';
		        echo '<table class="table width="100%" table-hover">';
		        	echo '<thead>';
		        		echo '<tr>';
		        			echo '<th>Type du post</th>';
		        			echo '<th>Nombre de posts existants</th>';
		        			echo '<th>Envoyer ces posts dans la newsletter</th>';
		        		echo '</tr>';
		        	echo '</thead>';
		        	echo '<tbody>';
					foreach ($typesDePosts as $typeDePosts) {
					 	$post_type = $typeDePosts->post_type;
			            $post_type_object = get_post_type_object($post_type);

			            // Si le type de post n'est pas référencé par un singular_name 
			            if (empty($post_type_object->labels->singular_name)) {
			            	$post_type_name = $post_type;
			            } else {
		            	  	$post_type_name = $post_type_object->labels->singular_name;
			            }
						
						// On ajoute tous les types de posts en BDD
						if (get_option("atm_nl_post_type_".$post_type)) {
							add_option("atm_nl_post_type_".$post_type, 'false', '', 'yes');
						}

		    			echo '<tr>';
				           	echo '<td>'.$post_type_name.'</td>';
				           	echo '<td>'.$typeDePosts->count.'</td>';
				           	if (get_option("atm_nl_post_type_".$post_type) == "true") {
				           		echo '<td><input type="checkbox" checked="checked" name="case[]" value="'.$post_type.'"></td>';
				           	} else {
				           		echo '<td><input type="checkbox" name="case[]" value="'.$post_type.'"></td>';
				           	}
			           	echo '</tr>';
		    		}
		    		echo '</tbody>';
		        echo '</table>';

		      	echo '<input type="hidden" name="action" value="save"/>';
		      	echo '<input class="btn btn-default" type="submit" name="button" value="Créer une newsletter"/>';
			echo '</form>';
	    }
	echo '</div>';
?>