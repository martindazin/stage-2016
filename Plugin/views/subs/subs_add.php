<?php
	// Fonctions PHP //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

	// Requêtes SQL //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');
	
    // Upload de fichiers avec WP
	// Se stocke dans le dossier wp-content/upload
	// Mise en base de données des adresses du fichier .csv dans la table $wpdb->prefix.'clearmails'
	if(isset($_POST['action1']) && !empty($_POST['action1']) && !is_null($_POST['action1']) && $_POST['action1'] == 'save'){
		$serveur1 = array();
	    $errors1 = array();
	    $message1 = array();

	    if (!function_exists('wp_handle_upload')) {
	    	require_once(ABSPATH.'wp-admin/includes/file.php');
		}

		$uploadedfile = $_FILES['monfichier1'];

		// On teste si l'extension du fichier est bien "csv"
		$testExtension1 = pathinfo($uploadedfile['name']);
		mb_strtolower($testExtension1['extension']);

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
	      	$message1[] = 'error';
	     	$message1[] = implode('', $errors1);
	    }
	}
?>

<h4><em>Par un fichier CSV</em></h4>
<?php
  	if(isset($message1) && !empty($message1) && !is_null($message1) && is_array($message1)){echo '<div id="message1" class="'.$message1[0].' below-h2">'.$message1[1].'</div>';}
?>
	<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
  	<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
  	Uploader un fichier
	    <span class="btn btn-default btn-file">
        Choisir le fichier <input type="file" name="monfichier1">
    </span>
	<br>
  	<input type="hidden" name="action1" value="save"/>
  	<input class="btn btn-default" type="submit" value="Charger ce fichier"/>
</form>

<?php
	if(isset($_POST['action2']) && !empty($_POST['action2']) && !is_null($_POST['action2']) && $_POST['action2'] == 'save'){
		$serveur2 = array();
	    $errors2 = array();
	    $message2 = array();

		// Nettoyage des variables
		$serveur2['adressemanuelle1'] = stripcslashes(atm_sanitize($_POST['adressemanuelle1']));

		if(!isset($serveur2['adressemanuelle1'])
      	|| empty($serveur2['adressemanuelle1'])
      	|| is_null($serveur2['adressemanuelle1'])
      	|| is_valid_mail_adress($serveur2['adressemanuelle1']) == false) {
			$errors2["adressemanuelle1"] = "<p>Vous devez écrire une <strong>adresse mail</strong> valide.</p>";
		}

		if (empty($errors2)) {
	     	$booleenEstDejaPresent = 0;
	      	// $toutesLesPersonnes est une requête SQL
	    	foreach ($toutesLesPersonnes as $personne) {
	     		$subscriber_email = $personne->subscriber_email;

	     		// On teste si l'adresse est déjà présente en base de données
	     		if ($serveur2['adressemanuelle1'] == $subscriber_email) {
	     			$booleenEstDejaPresent = 1;
	     		}
	     	}
	     	if ($booleenEstDejaPresent == 0) {
     			$wpdb->insert($table_subs, array(
													'subscriber_email' => $serveur2['adressemanuelle1'],
													'subscriber_status' => 'C',
													'subscriber_created' => current_time('mysql')
												)
     			);
     			echo "Votre adresse d'abonné a bien été ajoutée !";
     		} else {
     			echo "L'adresse existe déjà dans la base de données !";
     		}
     	} else {
	      	$message2[] = 'error';
	     	$message2[] = implode('', $errors2);
	    }
	}
?>

<h4><em>Manuellement</em></h4>
<?php
  	if(isset($message2) && !empty($message2) && !is_null($message2) && is_array($message2)){echo '<div id="message2" class="'.$message2[0].' below-h2">'.$message2[1].'</div>';}
?>
<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
 	<div class="col-xs-12 col-md-3 col-lg-3">
        <label for="adressemanuelle1">Adresse email :</label>
    </div>
    <div class="col-xs-12 col-md-3 col-lg-3">
		<input class="form-control" id="adressemanuelle1" type="text" name="adressemanuelle1"/>
  	</div>
		<div class="col-xs-12 col-md-12 col-lg-12">
      	<input type="hidden" name="action2" value="save"/>
      	<input class="btn btn-default" type="submit" value="Ajouter cet abonné"/>
	</div>
</form>