<?php
	// Fonctions PHP //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

	// Requêtes SQL //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');
		
	if(isset($_POST['action5']) && !empty($_POST['action5']) && !is_null($_POST['action5']) && $_POST['action5'] == 'save'){
		$serveur5 = array();
	    $errors5 = array();
	    $message5 = array();

		// Nettoyage des variables
		$serveur5['adressemanuelle3'] = stripcslashes(atm_sanitize($_POST['adressemanuelle3']));

		if(!isset($serveur5['adressemanuelle3'])
      	|| empty($serveur5['adressemanuelle3'])
      	|| is_null($serveur5['adressemanuelle3'])
      	|| is_valid_mail_adress($serveur5['adressemanuelle3']) == false) {
			$errors5["adressemanuelle3"] = "<p>Vous devez écrire une <strong>adresse mail</strong> valide.</p>";
		}

		if (empty($errors5)) {
	     	$booleenEstDejaPresent = 0;
	      	// $toutesLesPersonnes est une requête SQL
	    	foreach ($toutesLesPersonnes as $personne) {
	     		$subscriber_email = $personne->subscriber_email;

	     		// On teste si l'adresse est déjà présente en base de données
	     		if ($serveur5['adressemanuelle3'] == $subscriber_email) {
	     			$booleenEstDejaPresent = 1;
	     		}
	     	}
	     	if ($booleenEstDejaPresent == 0) {
     			$wpdb->insert($table_subs, array(
 													'subscriber_email' => $serveur5['adressemanuelle3'],
 													'subscriber_status' => 'T',
 													'subscriber_created' => current_time('mysql')
 												)
     			);

     			echo "Votre adresse de testeur a bien été ajoutée dans la base de données !";
     		} else {
     			echo "L'adresse existe déjà dans la base de données !";
     		}
     	} else {
	      	$message5[] = 'error';
	     	$message5[] = implode('', $errors5);
	    }
	}
?>

<h4><em>Manuellement</em></h4>
<?php
  	if(isset($message5) && !empty($message5) && !is_null($message5) && is_array($message5)){echo '<div id="message5" class="'.$message5[0].' below-h2">'.$message5[1].'</div>';}
?>
<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
 	<div class="col-xs-12 col-md-3 col-lg-3">
        <label for="adressemanuelle3">Adresse email :</label>
    </div>
    <div class="col-xs-12 col-md-3 col-lg-3">
		<input class="form-control" id="adressemanuelle3" type="text" name="adressemanuelle3"/>
  	</div>
		<div class="col-xs-12 col-md-12 col-lg-12">
      	<input type="hidden" name="action5" value="save"/>
      	<input class="btn btn-default" type="submit" value="Ajouter ce testeur"/>
	</div>
</form>