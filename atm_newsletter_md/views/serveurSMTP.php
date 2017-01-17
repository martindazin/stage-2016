<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<style type="text/css">

</style>

<?php
  // Fonctions PHP //
  include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

  // Requêtes SQL //
  include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');
  
  // Vérification du formulaire en PHP
  if(isset($_POST['action']) && !empty($_POST['action']) && !is_null($_POST['action']) && $_POST['action'] == 'save'){
    // Création de tableaux qui contiendront :
      // - toutes les informations saisies par l'utilisateur
      // - la liste des erreurs
      // - l'affichage des erreurs
    $serveur = array();
    $errors = array();
    $message = array();

    // Nettoyage des variables
      // Champs obligatoires
    $serveur['nomdomaine'] = stripcslashes(atm_sanitize($_POST['nomdomaine']));
    $serveur['port'] = stripcslashes(atm_sanitize($_POST['port']));
    $serveur['methodeauthentification'] = stripcslashes(atm_sanitize($_POST['methodeauthentification']));
    $serveur['nomutilisateur'] = stripcslashes(atm_sanitize($_POST['nomutilisateur']));
    $serveur['motdepasse'] = stripcslashes(atm_sanitize($_POST['motdepasse']));

      // Champs optionels
    $serveur['securiteconnexion'] = stripcslashes(atm_sanitize($_POST['securiteconnexion']));
    $serveur['adresseexpediteur'] = stripcslashes(atm_sanitize($_POST['adresseexpediteur']));
    $serveur['nomexpediteur'] = stripcslashes(atm_sanitize($_POST['nomexpediteur']));

    // Vérification de la "présence" des variables
      // Champs obligatoires (on traite seulement ces champs)
    if(!isset($serveur['nomdomaine'])
      || empty($serveur['nomdomaine'])
      || is_null($serveur['nomdomaine'])
      || is_valid_domain_name($serveur['nomdomaine']) == 0
      || is_valid_domain_name($serveur['nomdomaine']) == FALSE)
      {$errors["nomdomaine"] = "<p>Vous devez entrer l'hôte <strong>nomdomaine</strong> du serveur.</p>";}
    
    if((!isset($serveur['port'])
      || empty($serveur['port'])
      || is_null($serveur['port']))
      || intval($serveur['port']) == 0
      || intval($serveur['port']) == 1)
      {$errors["port"] = "<p>Vous devez entrer le <strong> numéro de port</strong> du serveur.</p>";}
    
    if(!isset($serveur['methodeauthentification'])
      || empty($serveur['methodeauthentification'])
      || is_null($serveur['methodeauthentification']))
      {$errors["methodeauthentification"] = "<p>Vous devez choisir une méthode d'<strong>authentification</strong> au serveur.</p>";}
    
    if(!isset($serveur['nomutilisateur'])
      || empty($serveur['nomutilisateur'])
      || is_null($serveur['nomutilisateur']))
      {$errors["nomutilisateur"] = "<p>Vous devez écrire le <strong>nom d'utilisateur</strong> pour le serveur.</p>";}
    
    if(!isset($serveur['motdepasse'])
      || empty($serveur['motdepasse'])
      || is_null($serveur['motdepasse']))
      {$errors["motdepasse"] = "<p>Vous devez entrer le <strong>mot de passe</strong> pour le serveur.</p>";}

    if(empty($errors)){
      // Ajout ou MAJ en BDD des différentes informations saisies par l'utilisateur
        // Champs obligatoires
      if (get_option('atm_nl_nomdomaine')) {
        update_option('atm_nl_nomdomaine', $serveur['nomdomaine'], 'yes');
      } else {
        add_option('atm_nl_nomdomaine', $serveur['nomdomaine'], '', 'yes');
      }
      if (get_option('atm_nl_port')) {
        update_option('atm_nl_port', $serveur['port'], 'yes');
      } else {
        add_option('atm_nl_port', $serveur['port'], '', 'yes');
      }
      if (get_option('atm_nl_methodeauthentification')) {
        update_option('atm_nl_methodeauthentification', $serveur['methodeauthentification'], 'yes');
      } else {
        add_option('atm_nl_methodeauthentification', $serveur['methodeauthentification'], '', 'yes');
      }
      if (get_option('atm_nl_nomutilisateur')) {
        update_option('atm_nl_nomutilisateur', $serveur['nomutilisateur'], 'yes');
      } else {
        add_option('atm_nl_nomutilisateur', $serveur['nomutilisateur'], '', 'yes');
      }
      if (get_option('atm_nl_motdepasse')) {
        update_option('atm_nl_motdepasse', $serveur['motdepasse'], 'yes');
      } else {
        add_option('atm_nl_motdepasse', $serveur['motdepasse'], '', 'yes');
      }
      
        // Champs optionels
      if(isset($serveur['securiteconnexion'])
        // Ne pourra jamais être "null"
        && !empty($serveur['securiteconnexion'])
        && !is_null($serveur['securiteconnexion'])) {
        if (get_option('atm_nl_securiteconnexion')){
          update_option('atm_nl_securiteconnexion', $serveur['securiteconnexion'], 'yes');
        } else {
          add_option('atm_nl_securiteconnexion', $serveur['securiteconnexion'], '', 'yes');
        }
      }
      {$errors["securiteconnexion"] = "<p>Vous devez choisir le type de <strong>sécurité de connexion</strong> pour le serveur.</p>";}
      
      if(isset($serveur['adresseexpediteur'])
        && !empty($serveur['adresseexpediteur'])
        && !is_null($serveur['adresseexpediteur'])
        && is_valid_mail_adress($serveur['adresseexpediteur']) == true) {
        if (get_option('atm_nl_adresseexpediteur')) {
          update_option('atm_nl_adresseexpediteur', $serveur['adresseexpediteur'], 'yes');
        } else {
          add_option('atm_nl_adresseexpediteur', $serveur['adresseexpediteur'], '', 'yes');
        }
      } else {
        {$errors["adresseexpediteur"] = "<p>Vous devez écrire une <strong>adresse d'expéditeur</strong> valide.</p>";}
      }
     
      if(isset($serveur['nomexpediteur'])
        && !empty($serveur['nomexpediteur'])
        && !is_null($serveur['nomexpediteur'])
        && is_valid_user_name($serveur['nomexpediteur'])) {
        if (get_option('atm_nl_nomexpediteur')) {
          update_option('atm_nl_nomexpediteur', $serveur['nomexpediteur'], 'yes');
        } else {
          add_option('atm_nl_nomexpediteur', $serveur['nomexpediteur'], '', 'yes');
        }
      } else {
        {$errors["nomexpediteur"] = "<p>Vous devez écrire un <strong>nom d'expéditeur</strong> valide.</p>";}
      }
      
    } else {
      $message[] = 'error';
      $message[] = implode('', $errors);
    }
  }
?>

<div class="container-fluid">
  <div class="col-xs-12 col-md-12 col-lg-12">
  	<h1>Configuration du serveur SMTP</h1>

    <!-- Affichage des messages des éventuels messages d'erreurs -->
    <?php
      if(isset($message) && !empty($message) && !is_null($message) && is_array($message)){echo '<div id="message" class="'.$message[0].' below-h2">'.$message[1].'</div>';}
    ?>

  	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

  	  <!-- Champs obligatoires -->
  	  <div class="col-xs-12 col-md-12 col-lg-12">
  	    <h3>Champs obligatoires</h3>
	      <div class="col-xs-12 col-md-6 col-lg-6">
	        <label for="nomdomaine">Nom de domaine :</label>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-6">
	        <input class="form-control" id="nomdomaine" type="text" name="nomdomaine" value="<?php echo get_option('atm_nl_nomdomaine') ?>"/>
	      </div>
	      <div class="col-xs-12 col-md-6 col-lg-6">
	        <label for="port">Port :</label>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-6">
	        <input class="form-control" id="port" type="text" name="port" value="<?php echo get_option('atm_nl_port') ?>"/>
	      </div>
	      <div class="col-xs-12 col-md-6 col-lg-6">
	        <label for="methodeauthentification">Authentification SMTP lors de l'envoi d'un e-mail :</label>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-6">
	        <select class="form-control" id="methodeauthentification" name="methodeauthentification">
            <option>Choisir votre méthode</option>
            <?php
            	$methodeauthentification = get_option('atm_nl_methodeauthentification');

            	$listeOptionsMethodes = array(
                "vraie" => "Vraie",
                "faux" => "Faux",
            	);

            	foreach ($listeOptionsMethodes as $keyOptionsMethodes => $valueOptionsMethodes) {
                echo  '<option value="'.$keyOptionsMethodes.'"';
                if(isset($methodeauthentification)
                	&& !empty($methodeauthentification)
                	&& !is_null($methodeauthentification)
                	&& $methodeauthentification == $keyOptionsMethodes)
                	{echo ' selected="selected"';}
              	echo '>'.$valueOptionsMethodes.'</option>';
          		}
            ?>
          </select>
	      </div>
	      <div class="col-xs-12 col-md-6 col-lg-6">
	        <label for="nomutilisateur">Nom d'utilisateur :</label>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-6">
	        <input class="form-control" id="nomutilisateur" type="text" name="nomutilisateur" value="<?php echo get_option('atm_nl_nomutilisateur') ?>"/>
	      </div>
	      <div class="col-xs-12 col-md-6 col-lg-6">
	        <label for="motdepasse">Mot de passe :</label>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-6">
	        <input class="form-control" id="motdepasse" type="password" name="motdepasse" value="<?php echo get_option('atm_nl_motdepasse') ?>"/>
	      </div>
      </div>

	    <!-- Champs optionels -->
      <div class="col-xs-12 col-md-12 col-lg-12">
  	    <h3>Champs optionels</h3>
  	  	<div class="col-xs-12 col-md-6 col-lg-6">
  		    <label for="securiteconnexion">Sécurité de la connexion :</label>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-6">
  		    <select class="form-control" id="securiteconnexion" name="securiteconnexion">
  		      	<option>Choisir votre sécurité</option>
  		      	<?php
  		      		$securiteconnexion = get_option('atm_nl_securiteconnexion');

  		      		$listeOptionsSecurite = array(
  		      			"aucune" => "Aucune",
  		      			"SSL" => "SSL",
  		      			"TLS" => "TLS"
  		      		);

  		      		foreach ($listeOptionsSecurite as $keyOptionsSecurite => $valueOptionsSecurite) {
	                echo  '<option value="'.$keyOptionsSecurite.'"';
	                if(isset($securiteconnexion)
	                	&& !empty($securiteconnexion)
	                	&& !is_null($securiteconnexion)
	                	&& $securiteconnexion == $keyOptionsSecurite)
	                	{echo ' selected="selected"';}
	                echo '>'.$valueOptionsSecurite.'</option>';
              	}
  		      	?>
  	      	</select>
  	  	</div>
  	    <div class="col-xs-12 col-md-6 col-lg-6">
	        <label for="adresseexpediteur">Adresse mail du expéditeur :</label>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-6">
	        <input class="form-control" id="adresseexpediteur" type="text" name="adresseexpediteur" value="<?php echo get_option('atm_nl_adresseexpediteur') ?>"/>
	      </div>
	      <div class="col-xs-12 col-md-6 col-lg-6">
	        <label for="nomexpediteur">Nom du expéditeur :</label>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-6">
	        <input class="form-control" id="nomexpediteur" type="text" name="nomexpediteur" value="<?php echo get_option('atm_nl_nomexpediteur') ?>"/>
	      </div>
      </div>

	    <!-- Envoi du formulaire -->
	    <div class="col-xs-12 col-md-12 col-lg-12">
        <input type="hidden" name="action" value="save"/>
        <input class="btn btn-default" type="submit" name="button" value="Valider les informations"/>
	    </div>
  	</form>
  </div>

  <?php
    setlocale(LC_TIME, "fr_FR");
    date_default_timezone_set("Europe/Paris");

    if(isset($_POST['action2']) && !empty($_POST['action2']) && !is_null($_POST['action2']) && $_POST['action2'] == 'save2'){
      $serveur = array();
      $errors = array();
      $message = array();

      // Nettoyage des variables
      $serveur['adressetest'] = stripcslashes(atm_sanitize($_POST['adressetest']));

      if(!isset($serveur['adressetest'])
          || empty($serveur['adressetest'])
          || is_null($serveur['adressetest'])
          || is_valid_mail_adress($serveur['adressetest']) == false) {
        $errors["adressetest"] = "<p>Vous devez écrire une <strong>adresse mail</strong> valide.</p>";
      }

      if (empty($errors)) {
        wp_mail($serveur['adressetest'], "Ceci est un email de test", "Aujourd'hui on est le ".date("d/m/y", time())." et ce mail de test vous a été envoyé à ".date("H:i:s", time()));

        echo "Un courriel de test vient d'être envoyé à votre adresse saisie.";
      } else {
          $message[] = 'error';
        $message[] = implode('', $errors);
      }
    }

    // Formulaire de test
    if(isset($message) && !empty($message) && !is_null($message) && is_array($message)){echo '<div id="message" class="'.$message[0].' below-h2">'.$message[1].'</div>';}

    if (get_option('atm_nl_nomdomaine') != null && get_option('atm_nl_nomdomaine') != '') {
      echo '<br>';
      echo '<h3>Test d\'envoi d\'un email</h3>';

      echo '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">';
        echo '<div class="col-xs-12 col-md-6 col-lg-6">';
          echo '<label for="adressetest">Adresse mail de test :</label>';
        echo '</div>';
        echo '<div class="col-xs-12 col-md-6 col-lg-6">';
          echo '<input class="form-control" id="adressetest" type="text" name="adressetest" />';
        echo '</div>';

        // Envoi du formulaire
        echo '<div class="col-xs-12 col-md-12 col-lg-12">';
          echo '<input type="hidden" name="action2" value="save2"/>';
          echo '<input class="btn btn-default" type="submit" name="button" value="Envoyer un mail de test"/>';
        echo '</div>';
      echo '</form>';
    }
  ?>
</div>