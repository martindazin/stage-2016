<!DOCTYPE HTML>
<html lang="fr">
  	<head>
  		<title>Désinscription</title>
	    <meta charset="utf-8">

		<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
		<style type="text/css">

		</style>
	</head>

	<body>
		<?php
			// Permet de charger toutes les fonctionnalités de WP pour un document externe
			require($_SERVER['DOCUMENT_ROOT'].'test/wp-load.php' );

			// Fonctions PHP //
			include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

			// Requêtes SQL //
			include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');

		  	if(isset($_POST['action']) && !empty($_POST['action']) && !is_null($_POST['action']) && $_POST['action'] == 'save'){
		  		$desinscription = array();
			  	$errors = array();
			    $message = array();

			 	$desinscription['adressemail'] = stripcslashes(atm_sanitize($_POST['adressemail']));

			 	if(!isset($desinscription['adressemail'])
			      || empty($desinscription['adressemail'])
			      || is_null($desinscription['adressemail'])
			      || is_valid_mail_adress($desinscription['adressemail']) == false)
				{$errors["adressemail"] = "<p>Vous devez écrire une <strong>adresse mail </strong>valide.</p>";}

				if(empty($errors)){
					// Requêtes SQL
					global $wpdb;
				 	$table_subscriber = $wpdb->prefix.'atm_nl_subscriber';
			     	
			     	$toutesLesPersonnes = $wpdb->get_results("SELECT *
														     	FROM ".$table_subscriber,
														     	OBJECT);

		     		if(isset($toutesLesPersonnes) && !empty($toutesLesPersonnes) && !is_null($toutesLesPersonnes) && $toutesLesPersonnes !== false){
		     			$wpdb->update($table_subscriber, array('subscriber_status' => 'D'), array('subscriber_email' => $desinscription['adressemail']));
						echo "La désinscription à notre newsletter s'est bien effectuée !";
					} else {
						echo "Cette adresse email n'existe pas !";
					}
				}else{
		      		$message[] = 'error';
			      	$message[] = implode('<br/>',$errors);
		    	}
			}
		?>

		<div class="container-fluid">
		  	<div class="col-xs-12 col-md-12 col-lg-12">
			  	<?php 
			  		if(isset($message) && !empty($message) && !is_null($message) && is_array($message)){echo '<div id="message" class="'.$message[0].' below-h2">'.$message[1].'</div>';}
		  		?>

			  	<h1>Se désinscrire à la newsletter</h1>
			  	Vous voulez déjà nous quitter :'( ?

			  	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			  		<div class="col-xs-12 col-md-6 col-lg-6">
				        <label for="adressemail">Adresse mail :</label>
			        </div>
			        <div class="col-xs-12 col-md-6 col-lg-6">
				        <input class="form-control" id="adressemail" type="text" name="adressemail" value=""/>
			      	</div>
			      	<div class="col-xs-12 col-md-12 col-lg-12">
				        <input type="hidden" name="action" value="save"/>
				        <input class="btn btn-default" type="submit" name="button" value="Se désinscrire !"/>
				    </div>
			  	</form>
			</div>
		</div>
	</body>
</html>