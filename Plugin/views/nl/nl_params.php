<?php
	// Fonctions PHP //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

	// Requêtes SQL //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');
	
	// Ce tableau et ces booléens permettent de vérifier s'il y a un fichier :
	$tableauDesExtensions = array('.jpeg', '.jpg', '.png');

	if(isset($_POST['action1']) && !empty($_POST['action1']) && !is_null($_POST['action1']) && $_POST['action1'] == 'save'){

		var_dump($_POST);
		echo '<br>';

		if (!function_exists('wp_handle_upload')) {
	    	require_once(ABSPATH.'wp-admin/includes/file.php');
		}

		$newsletterOptions = array();
		$errors = array();
    	$message = array();

		// Booléen permettant de vérifier s'il y a déjà un fichier :
  		// - logo.extension qui existe déjà dans uploads/atm_newsletter_md/
  		$boolMonFichierExisteLogo = false;

  		// On liste tous les fichiers présents dans uploads/atm_newsletter_md/
  		$listeDesFichiers = scandir(ABSPATH."/wp-content/uploads/atm_newsletter_md");

  		// Champs obligatoires
		$newsletterOptions['periodiciteNewsletter'] = stripcslashes(atm_sanitize($_POST['periodiciteNewsletter']));
		$newsletterOptions['couleur1'] = stripcslashes(atm_sanitize($_POST['couleur1']));
		$newsletterOptions['couleur2'] = stripcslashes(atm_sanitize($_POST['couleur2']));
		$newsletterOptions['couleur3'] = stripcslashes(atm_sanitize($_POST['couleur3']));
		$newsletterOptions['typographieNewsletter'] = stripcslashes(atm_sanitize($_POST['typographieNewsletter']));

		// Champs optionnels
		$newsletterOptions['facebook'] = stripcslashes(atm_sanitize($_POST['facebook']));
		$newsletterOptions['twitter'] = stripcslashes(atm_sanitize($_POST['twitter']));

		if(!isset($newsletterOptions['periodiciteNewsletter'])
	      	|| empty($newsletterOptions['periodiciteNewsletter'])
	      	|| is_null($newsletterOptions['periodiciteNewsletter'])
			|| $newsletterOptions['periodiciteNewsletter'] == "Choisir votre périodicité")
      		{$errors["periodiciteNewsletter"] = "<p>Vous devez choisir une <strong>périodicité</strong> d'envoi de newsletters.</p>";}
      
      	// La gestion de la taille du fichier est gérée par $movefile['error']
      	// Donc pas de gestion dans les erreurs (ici)
      	for ($i = 2; $i < count($listeDesFichiers); $i++) {
			$nomDuFichier = explode('.', $listeDesFichiers[$i]);
			if ($nomDuFichier[0] == "logo") {
				$boolMonFichierExisteLogo = true;
			}
		}

		$uploadedFile = $_FILES['monfichier'];
		$testExtension = pathinfo($uploadedFile['name']);
		// On met en minuscules l'extension
		$testExtension = mb_strtolower($testExtension['extension']);
			
		// On teste si l'extension du fichier est bien "jpeg", "jpg" ou "png" et qu'il n'y en a pas
		if (($testExtension !== "jpeg") && ($testExtension !== "jpg") && ($testExtension !== "png") && ($boolMonFichierExisteLogo == false)) {
			$errors["fichier1"] = '<p>'."Vous n'avez pas choisi de fichier pour le <strong>logo</strong> de votre entreprise <br> OU il y a une erreur d'extension.<br>Le fichier à fournir est de <strong>type jpeg, jpg ou png</strong>.".'</p>';
		}

      	if(!isset($newsletterOptions['couleur1'])
	      	|| empty($newsletterOptions['couleur1'])
	      	|| is_null($newsletterOptions['couleur1'])
	      	|| is_valid_hexadecimal($newsletterOptions['couleur1']) == 0
	      	|| is_valid_hexadecimal($newsletterOptions['couleur1']) == false)
      		{$errors["couleur1"] = "<p>Vous devez choisir une <strong>couleur de titres</strong> pour votre newsletters.</p>";}

      	if(!isset($newsletterOptions['couleur2'])
	      	|| empty($newsletterOptions['couleur2'])
	      	|| is_null($newsletterOptions['couleur2'])
	      	|| is_valid_hexadecimal($newsletterOptions['couleur2']) == 0
	      	|| is_valid_hexadecimal($newsletterOptions['couleur2']) == false)
      		{$errors["couleur2"] = "<p>Vous devez choisir une <strong>couleur de sous-titres</strong> pour votre newsletters.</p>";}

  		if(!isset($newsletterOptions['couleur3'])
	      	|| empty($newsletterOptions['couleur3'])
	      	|| is_null($newsletterOptions['couleur3'])
	      	|| is_valid_hexadecimal($newsletterOptions['couleur3']) == 0
	      	|| is_valid_hexadecimal($newsletterOptions['couleur3']) == false)
      		{$errors["couleur3"] = "<p>Vous devez choisir une <strong>couleur de fond</strong> pour votre newsletters.</p>";}

      	if(!isset($newsletterOptions['typographieNewsletter'])
	      	|| empty($newsletterOptions['typographieNewsletter'])
	      	|| is_null($newsletterOptions['typographieNewsletter'])
			|| $newsletterOptions['typographieNewsletter'] == "Choisir votre typographie")
      		{$errors["typographieNewsletter"] = "<p>Vous devez choisir une <strong>typographie</strong> pour votre newsletter.</p>";}

      
      	if (empty($errors)) {

      		// Champs obligatoires
  			if (get_option('atm_nl_periodiciteNl')) {
		      	update_option('atm_nl_periodiciteNl', $newsletterOptions['periodiciteNewsletter'], 'yes');
		    } else {
		      	add_option('atm_nl_periodiciteNl', $newsletterOptions['periodiciteNewsletter'], '', 'yes');
		    }

		    // Je teste s'il le téléchargement de $_FILES['monfichier']['error'] s'est bien passé
		    if ($_FILES['monfichier']['error'] == 0) {
		    	// echo "Il y a quelque chose !".'<br>';
	    	 	
			    // Fichier logo d'entreprise
			    $upload_overrides = array('test_form' => false);
				// On enregistre dans notre répertoire d'upload personnalisé
				add_filter('upload_dir', 'file_upload_dir');
				$movefile = wp_handle_upload($_FILES['monfichier'], $upload_overrides);
				// On referme notre répertoire d'upload personalisé
				// et on remet le répertoire d'upload par défaut.
				remove_filter('upload_dir', 'file_upload_dir');

				if ($movefile && ! isset($movefile['error'])) {
	 				// On renomme le fichier pour qu'il soit plus facile à afficher dans la newsletter
					$tableauCheminDuFichier1 = explode('/', $movefile['file']);

					for ($i = 0; $i < (count($tableauCheminDuFichier1)-1); $i++) { 
						$tableauCheminDuFichier1SansNom = $tableauCheminDuFichier1SansNom.$tableauCheminDuFichier1[$i].'/';
					}

					// On cherche à savoir s'il y a déjà un fichier logo.extension dans uploads/atm_newsletter_md/
					for ($i = 0; $i < count($tableauDesExtensions); $i++) { 
						if (file_exists($tableauCheminDuFichier1SansNom."logo".$tableauDesExtensions[$i])) {
							// echo "Mon fichier logo s'appellait : ".$tableauCheminDuFichier1SansNom."logo".$tableauDesExtensions[$i].'<br>'."Mon ancien fichier s'appellait : ".$movefile['file']." et s'appellera désormais : ".$tableauCheminDuFichier1SansNom."logo".strstr($tableauCheminDuFichier1[count($tableauCheminDuFichier1)-1], '.').'<br>';
							rename($tableauCheminDuFichier1SansNom."logo".$tableauDesExtensions[$i], $tableauCheminDuFichier1SansNom."logo".strstr($tableauCheminDuFichier1[count($tableauCheminDuFichier1)-1], '.'));
						}
					}

					// Il n'y a pas encore de fichier logo.extension dans uploads/atm_newsletter_md/
					if (file_exists($tableauCheminDuFichier1SansNom."logo".$tableauDesExtensions[$i]) == false) {
						rename($movefile['file'], $tableauCheminDuFichier1SansNom."logo.".$testExtension);
					}
				} else {
				    echo '<p>'."Votre logo d'entreprise n'a pas été enregistré à cause de l'erreur suivante : ".$movefile['error'].'<br>'."Veuillez recharger la page s'il vous plait !".'</p>';
				    // Arrêt du code PHP si erreur
				    die();
				}	
		    }

		    if (get_option('atm_nl_couleur1')) {
		      	update_option('atm_nl_couleur1', $newsletterOptions['couleur1'], 'yes');
		    } else {
		      	add_option('atm_nl_couleur1', $newsletterOptions['couleur1'], '', 'yes');
		    }
		    if (get_option('atm_nl_couleur2')) {
		      	update_option('atm_nl_couleur2', $newsletterOptions['couleur2'], 'yes');
		    } else {
		      	add_option('atm_nl_couleur2', $newsletterOptions['couleur2'], '', 'yes');
		    }
		    if (get_option('atm_nl_couleur3')) {
		      	update_option('atm_nl_couleur3', $newsletterOptions['couleur3'], 'yes');
		    } else {
		      	add_option('atm_nl_couleur3', $newsletterOptions['couleur3'], '', 'yes');
		    }

		    if (get_option('atm_nl_typographieNl')) {
		      	update_option('atm_nl_typographieNl', $newsletterOptions['typographieNewsletter'], 'yes');
		    } else {
		      	add_option('atm_nl_typographieNl', $newsletterOptions['typographieNewsletter'], '', 'yes');
		    }

		    // Champs optionnels
		   	if(isset($newsletterOptions['facebook'])
	        && !empty($newsletterOptions['facebook'])
	        && !is_null($newsletterOptions['facebook'])
	        && is_valid_facebook($newsletterOptions['facebook']) == 1) {
	        	if (get_option('atm_nl_facebook')) {
	          		update_option('atm_nl_facebook', $newsletterOptions['facebook'], 'yes');
	        	} else {
		          add_option('atm_nl_facebook', $newsletterOptions['facebook'], '', 'yes');
		        }
	      	} else {
		        {$errors["adressedestinataire"] = "<p>Vous devez une <strong>URL Facebook</strong> valide.</p>";}
	      	}

      		if(isset($newsletterOptions['twitter'])
	        && !empty($newsletterOptions['twitter'])
	        && !is_null($newsletterOptions['twitter'])
	        && is_valid_twitter($newsletterOptions['twitter']) == 1) {
	        	if (get_option('atm_nl_twitter')) {
	          		update_option('atm_nl_twitter', $newsletterOptions['twitter'], 'yes');
	        	} else {
		          add_option('atm_nl_twitter', $newsletterOptions['twitter'], '', 'yes');
		        }
	      	} else {
		        {$errors["adressedestinataire"] = "<p>Vous devez une <strong>URL Twitter</strong> valide.</p>";}
	      	}

      	} else {
	      	$message[] = 'error';
	     	$message[] = implode('', $errors);
	    }
    }
?>

<h4><em>Les champs marqués d'une '*' sont obligatoires</em></h4>
<!-- Affichage des messages des éventuels messages d'erreurs -->
<?php
  if(isset($message) && !empty($message) && !is_null($message) && is_array($message)){echo '<div id="message" class="'.$message[0].' below-h2">'.$message[1].'</div>';}
?>

<div class="col-xs-12 col-md-6 col-lg-6">
	<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	 	<div class="col-xs-12 col-md-6 col-lg-6">
	        <label for="periodiciteNewsletter">Choix de votre périodicité d'envoi de newsletters : *</label>
        </div>
		    <select class="form-control" id="periodiciteNewsletter" name="periodiciteNewsletter">
		      	<option>Choisir votre périodicité*</option>
		      	<?php
		      		$listePeriodiciteNewsletter = get_option('atm_nl_periodiciteNl');

		      		$listeOptionsPeriodiciteNewsletter = array(
		      			"minute" => "Une minute (destiné aux tests)",
		      			"hedbomadaire" => "Hebdomadaire",
		      			"quinzaine" => "Quinzaine",
		      			"mensuel" => "Mensuel",
		      			"bimestriel" => "Bimestriel",
		      			"trimestriel" => "Trimestriel"
		      		);

		      		foreach ($listeOptionsPeriodiciteNewsletter as $keyPeriodiciteNewsletter => $valuePeriodiciteNewsletter) {
		                echo  '<option value="'.$keyPeriodiciteNewsletter.'"';
		                if(isset($listePeriodiciteNewsletter)
		                	&& !empty($listePeriodiciteNewsletter)
		                	&& !is_null($listePeriodiciteNewsletter)
		                	&& $listePeriodiciteNewsletter == $keyPeriodiciteNewsletter) {
	                		echo ' selected="selected"';
	                	}
		                echo '>'.$valuePeriodiciteNewsletter.'</option>';
	              	}
		      	?>
	      	</select>
	      	<div class="col-xs-12 col-md-12 col-lg-12">
	      		<input type="hidden" name="MAX_FILE_SIZE" value="1000" />
      	    <span class="btn btn-default btn-file">
		        Choisir le logo de votre entreprise (taille inférieure à 1000 Ko) *<input type="file" name="monfichier">
		    </span>

		    <?php
		    	echo '<br>'."Aperçu de votre logo d'entreprise déjà présent : ";
		    	for ($i = 0; $i < count($tableauDesExtensions); $i++) {
					if (file_exists(ABSPATH."wp-content/uploads/atm_newsletter_md/logo".$tableauDesExtensions[$i])) {
						echo '<img src="'.home_url()."/wp-content/uploads/atm_newsletter_md/logo".$tableauDesExtensions[$i].'" style="MAX-WIDTH: 150px; HEIGHT: auto; WIDTH: 100%; MIN-WIDTH: 120px" alt="">';
					}
				}
		    ?>
		</div>
      	<div class="col-xs-12 col-md-6 col-lg-6">
	        <label for="couleur1">Couleur pour les titres en héxadécimal : *</label>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-6">
        	<input class="form-control" id="couleur1" type="text" name="couleur1" value="<?php echo get_option('atm_nl_couleur1') ?>"/>
      	</div>
      	<div class="col-xs-12 col-md-6 col-lg-6">
	        <label for="couleur2">Couleur pour les sous-titres en héxadécimal : *</label>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-6">
        	<input class="form-control" id="couleur2" type="text" name="couleur2" value="<?php echo get_option('atm_nl_couleur2') ?>"/>
      	</div>
      	<div class="col-xs-12 col-md-6 col-lg-6">
	        <label for="couleur3">Couleur pour le fond de la newsletter en héxadécimal : *</label>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-6">
        	<input class="form-control" id="couleur3" type="text" name="couleur3" value="<?php echo get_option('atm_nl_couleur3') ?>"/>
      	</div>
      	<div class="col-xs-12 col-md-6 col-lg-6">
	        <label for="typographieNewsletter">Choix de votre typographie pour votre newsletter : *</label>
        </div>
  	 	<select class="form-control" id="typographieNewsletter" name="typographieNewsletter">
		      	<option>Choisir votre typographie*</option>
		      	<?php
		      		$listeTypographieNewsletter = get_option('atm_nl_typographieNl');

		      		$listeOptionsTypographieNewsletter = array(
		      			"Arial" => "Arial",
		      			"Arial Black" => "Arial Black",
		      			"Comic Sans MS" => "Century Gothic",
		      			"Courier New" => "Courier New",
		      			"Georgia" => "Georgia",
		      			"Impact" => "Impact",
		      			"Times New Roman" => "Times New Roman",
		      			"Trebuchet MS" => "Trebuchet MS",
		      			"Verdana" => "Verdana"
		      		);

		      		foreach ($listeOptionsTypographieNewsletter as $keyTypographieNewsletter => $valueTypographieNewsletter) {
		                echo  '<option value="'.$keyTypographieNewsletter.'"';
		                if(isset($listeTypographieNewsletter)
		                	&& !empty($listeTypographieNewsletter)
		                	&& !is_null($listeTypographieNewsletter)
		                	&& $listeTypographieNewsletter == $keyTypographieNewsletter) {
	                		echo ' selected="selected"';
	                	}
		                echo '>'.$valueTypographieNewsletter.'</option>';
	              	}
		      	?>
	      	</select>
      		<div class="col-xs-12 col-md-6 col-lg-6">
        	<label for="facebook">Page Facebook (URL complète) : </label>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-6">
        	<input class="form-control" id="facebook" type="text" name="facebook" value="<?php echo get_option('atm_nl_facebook') ?>"/>
      	</div>
  		<div class="col-xs-12 col-md-6 col-lg-6">
        	<label for="twitter">Page Twitter (URL complète) : </label>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-6">
        	<input class="form-control" id="twitter" type="text" name="twitter" value="<?php echo get_option('atm_nl_twitter') ?>"/>
      	</div>

	      	<div class="col-xs-12 col-md-12 col-lg-12">
	      	<input type="hidden" name="action1" value="save"/>
	      	<input class="btn btn-default" type="submit" name="button" value="Valider les informations"/>
	    </div>
  	</form>
</div>