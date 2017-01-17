<?php
	// Fonctions PHP //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

	// Requêtes SQL //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');

	// On définit les globales temporelles :
	// Modification des informations de localisation
	// Choix du fuseau horaire : Europe/Paris
	// setlocale(LC_TIME, "fr_FR");
	// date_default_timezone_set("Europe/Paris");

    // Upload de fichiers avec WP
	// Se stocke dans le dossier wp-content/upload
	// Mise en BDD des adresses du fichier .csv dans la table $wpdb->prefix.'clearmails'
	if(isset($_POST['action1']) && !empty($_POST['action1']) && !is_null($_POST['action1']) && $_POST['action1'] == 'save'){
		
		$serveur1 = array();
	    $errors1 = array();
	    $message1 = array();

	    if (!function_exists('wp_handle_upload')) {
	    	require_once(ABSPATH.'wp-admin/includes/file.php');
		}

		$uploadedfile = $_FILES['monfichier1'];
		// var_dump($uploadedfile);

		$testExtension1 = pathinfo($uploadedfile['name']);
		mb_strtolower($testExtension1['extension']);

		// On teste si l'extension du fichier est bien "csv"
		if ($testExtension1['extension'] !== "csv") {
	 		$errors1["fichier1"] = '<p>'."Vous n'avez pas choisi de fichier d'<strong>import d'emails abonnés</strong> OU il y a une erreur d'extension.<br>Le fichier à fournir est de <strong>type csv</strong>.".'</p>';
		}

		if (empty($errors1)) {
			$upload_overrides = array('test_form' => false);
				
			// On enregistre dans notre répertoire d'upload personnalisé
			add_filter('upload_dir', 'file_upload_dir');
			$movefile = wp_handle_upload($uploadedfile, $upload_overrides);
			// On referme notre répertoire d'upload personalisé
			// et on remet le répertoire d'upload par défaut.
			remove_filter('upload_dir', 'file_upload_dir');
		
			if ($movefile && ! isset($movefile['error'])) {
				global $wpdb;
			    $table_subscriber = $wpdb->prefix.'atm_nl_subscriber';
		     	$resultsQuery = $wpdb->get_results("SELECT *
											     	FROM ".$table_subscriber,
											     	OBJECT);

		     	// Le booléen $booleenEstDejaPresent permet de savoir si l'adresse que l'on veut ajouter à la BDD est déjà présente ou non
		     	$booleenEstDejaPresent = 0;
		     	$estDejaPresentDansMaTable = 0;
		     	$estBon = 0;
		     	// $estErrone est égal à -1 pour enlever la ligne d'en-tête
		     	$estErrone = -1;
				if (($handle = fopen($movefile['file'], "r")) !== FALSE) {
				    while (($data = fgetcsv($handle, 1000, "\n")) !== FALSE) {
			            if (is_valid_mail_adress($data[0]) == true){
			            	// Permet de récupérer toutes les adresses email de la table test_clearmails
					     	foreach ($resultsQuery as $resultQuery) {
					     		$subscriber_email = $resultQuery->subscriber_email;
					     		// On teste si l'adresse est déjà présente en BDD
					     		if ($data[0] == $subscriber_email) {
					     			$booleenEstDejaPresent = 1;
					     			$estDejaPresentDansMaTable++;
					     		}
					     	}
					     	if ($booleenEstDejaPresent == 0) {
					     		$wpdb->insert($table_subscriber, array(
														     			'subscriber_email' => $data[0],
														     			'subscriber_status' => 'C',
																		'subscriber_created' => current_time('mysql')
					     											)
				     			);
					     		$estBon++;
					     	}
			            } else {
			            	$estErrone++;
			            }
			            $booleenEstDejaPresent = 0;
				    }
				    fclose($handle);
				}

			 	echo "Le fichier d'ajout d'abonnés a bien été traité !".'<br>';
				echo '<p>';
					echo "Le nombre d'emails ajoutés à la BDD est de ".$estBon." email(s).".'<br>';
					echo "Le nombre d'emails déjà présents dans ma table est de ".$estDejaPresentDansMaTable." email(s).".'<br>';
					echo "Le nombre d'emails non-ajoutés à la BDD car ce ne sont pas des email(s) est de ".$estErrone." email(s).".'<br>';
				echo '</p>';
			   
			} else {
			    echo "Le fichier n'a pas été traité à cause de l'erreur suivante : ".$movefile['error'].'<br>';
			    // Arrêt du code PHP si erreur
			    die();
			}
		} else {
	      	$message1[] = 'error';
	     	$message1[] = implode('', $errors1);
	    }
	}
?>