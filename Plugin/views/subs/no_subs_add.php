<?php
	// Fonctions PHP //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

	// Requêtes SQL //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');
	
	// Upload de fichiers avec WP
	// Se stocke dans le dossier wp-content/upload
	// Mise en base de données des adresses du fichier .csv dans la table $wpdb->prefix.'atm_nl_subscriber' avec le status = 'B'
	if(isset($_POST['action3']) && !empty($_POST['action3']) && !is_null($_POST['action3']) && $_POST['action3'] == 'save'){		
		$serveur3 = array();
	    $errors3 = array();
	    $message3 = array();

	    if (!function_exists('wp_handle_upload')) {
	    	require_once(ABSPATH.'wp-admin/includes/file.php');
		}

		$uploadedfile = $_FILES['monfichier2'];

		// On teste si l'extension du fichier est bien "csv"
		$testExtension2 = pathinfo($uploadedfile['name']);
		mb_strtolower($testExtension2['extension']);

		if ($testExtension2['extension'] !== "csv") {
			$errors3["fichier2"] = '<p>'."Vous n'avez pas choisi de fichier d'<strong>import d'emails à blacklisté</strong> OU il y a une erreur d'extension.<br>Le fichier à fournir est de <strong>type csv</strong>.".'</p>';
		}

		if (empty($errors3)) {
			$upload_overrides = array('test_form' => false);

			// On enregistre dans notre répertoire d'upload personnalisé
			add_filter('upload_dir', 'file_upload_dir');
			$movefile = wp_handle_upload($uploadedfile, $upload_overrides);
			// On referme notre répertoire d'upload personalisé
			// et on remet le répertoire d'upload par défaut.
			remove_filter('upload_dir', 'file_upload_dir');

			if ($movefile && ! isset($movefile['error'])) {
		     	// Le booléen $booleenEstDejaPresent permet de savoir si l'adresse que l'on veut ajouter à la base de données est déjà présente ou non
		     	$booleenEstDejaPresent = 0;
		     	$estDejaPresentDansMaTable = 0;
		     	$estBon = 0;
		     	// $estErrone est égal à -1 pour enlever la ligne d'en-tête
		     	$estErrone = -1;
				if (($handle = fopen($movefile['file'], "r")) !== FALSE) {
				    while (($data = fgetcsv($handle, 1000, "\n")) !== FALSE) {
			            if (is_valid_mail_adress($data[0]) == true){
			            	// Permet de récupérer toutes les adresses email de la table test_clearmails
					     	foreach ($toutesLesPersonnes as $personne) {
					     		$subscriber_email = $personne->subscriber_email;
					     		// On teste si l'adresse est déjà présente en base de données
					     		if ($data[0] == $subscriber_email) {
					     			$booleenEstDejaPresent = 1;
					     			$estDejaPresentDansMaTable++;
					     		}
					     	}
					     	if ($booleenEstDejaPresent == 0) {
					     		$wpdb->insert($table_subs, array(
				     												'subscriber_email' => $data[0],
				     												'subscriber_status' => 'B',
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

			  	echo "Le fichier d'ajout de blacklistés a bien été traité !".'<br>';
				echo '<p>';
					echo "Le nombre d'emails ajoutés à la base de données est de ".$estBon." email(s).".'<br>';
					echo "Le nombre d'emails déjà présents dans ma table est de ".$estDejaPresentDansMaTable." email(s).".'<br>';
					echo "Le nombre d'emails non-ajoutés à la base de données car ce ne sont pas des email(s) est de ".$estErrone." email(s).".'<br>';
				echo '</p>';
			} else {
			    echo "Le fichier n'a pas été traité à cause de l'erreur suivante : ".$movefile['error'].'<br>';
			    // Arrêt du code PHP si erreur
			    die();
			}
		} else {
	      	$message3[] = 'error';
	     	$message3[] = implode('', $errors3);
	    }
	}
?>

<h4><em>Par un fichier CSV</em></h4>
<?php
  	if(isset($message3) && !empty($message3) && !is_null($message3) && is_array($message3)){echo '<div id="message3" class="'.$message3[0].' below-h2">'.$message3[1].'</div>';}
?>
	<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
  	<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
  	Uploader un fichier
	    <span class="btn btn-default btn-file">
        Choisir le fichier <input type="file" name="monfichier2">
    </span>
    <br>
  	<input type="hidden" name="action3" value="save"/>
  	<input class="btn btn-default" type="submit" value="Charger ce fichier"/>
</form>

<?php
	
	
	if(isset($_POST['action4']) && !empty($_POST['action4']) && !is_null($_POST['action4']) && $_POST['action4'] == 'save'){
		$serveur4 = array();
	    $errors4 = array();
	    $message4 = array();

		// Nettoyage des variables
		$serveur4['adressemanuelle2'] = stripcslashes(atm_sanitize($_POST['adressemanuelle2']));

		if(!isset($serveur4['adressemanuelle2'])
      	|| empty($serveur4['adressemanuelle2'])
      	|| is_null($serveur4['adressemanuelle2'])
      	|| is_valid_mail_adress($serveur4['adressemanuelle2']) == false) {
			$errors4['adressemanuelle2'] = "<p>Vous devez entrer une <strong>adresse mail</strong> valide.</p>";
		} 

		if (empty($errors4)) {
	     	$booleenEstDejaPresent = 0;
	      	// $toutesLesPersonnes est une requête SQL
	    	foreach ($toutesLesPersonnes as $personne) {
	     		$subscriber_email = $personne->subscriber_email;

	     		// On teste si l'adresse est déjà présente en base de données
	     		if ($serveur2['adressemanuelle2'] == $subscriber_email) {
	     			$booleenEstDejaPresent = 1;
	     		}
	     	}
	     	if ($booleenEstDejaPresent == 0) {
     			$wpdb->insert($table_subs, array(
 													'subscriber_email' => $serveur4['adressemanuelle2'],
														'subscriber_status' => 'B',
 													'subscriber_created' => current_time('mysql')
												)
				);

     			echo "Votre adresse de blaclisté a bien été ajoutée dans la base de données !";
     		} else {
     			echo "L'adresse existe déjà dans la base de données !";
     		}
		} else {
			$message4[] = 'error';
	     	$message4[] = implode('', $errors4);
     	}
	}
?>

<h4><em>Manuellement</em></h4>
<?php
  	if(isset($message4) && !empty($message4) && !is_null($message4) && is_array($message4)){echo '<div id="message4" class="'.$message4[0].' below-h2">'.$message4[1].'</div>';}
?>

<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
 	<div class="col-xs-12 col-md-3 col-lg-3">
        <label for="adressemanuelle2">Adresse email :</label>
    </div>
    <div class="col-xs-12 col-md-3 col-lg-3">
		<input class="form-control" id="adressemanuelle2" type="text" name="adressemanuelle2"/>
  	</div>
		<div class="col-xs-12 col-md-12 col-lg-12">
      	<input type="hidden" name="action4" value="save"/>
      	<input class="btn btn-default" type="submit" value="Ajouter ce blacklisté"/>
	</div>
</form>