<!DOCTYPE HTML>
<html lang="fr">
  	<head>
  		<title>Options de la newletter</title>
	    <meta charset="utf-8">

		<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<style type="text/css">

		</style>
	</head>

	<body>
		<?php
			// timestamp en millisecondes du début du script (en PHP 5)
			$debutDuScript = microtime(true);

			require($_SERVER['DOCUMENT_ROOT'].'test/wp-load.php' );
			require ABSPATH."wp-content/plugins/atm_newsletter_md/PHPMailer/PHPMailerAutoload.php";

			// Fonctions PHP //
			include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

			// Requêtes SQL //
			include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');

			echo 	'<div style="border: 1px solid #ccc;">
						<a style="text-decoration: none; color: black;" title="Revenir en arrière" href="'.esc_url(home_url('/')).'wp-admin/admin.php?page=atm_newsletter_newsletter">Retour à la page précédente...</a>
					</div> ';

			if (is_numeric($_POST['newsletter'])) {
				
				// Requêtes SQL dépendantes de $_POST

				// Affiche `newsletter_message` en fonction de `newsletter_showed_id` et `newsletter_status` = 'C' ou 'E'
				$affichageNlA = $wpdb->get_var("SELECT `newsletter_message`
												FROM ".$table_nl.
												" WHERE `newsletter_showed_id` = ".$_POST['newsletter'].
												" AND (`newsletter_status` = 'C' OR `newsletter_status` = 'E')");

				// Affiche `newsletter_message` en fonction de `newsletter_showed_id` et `newsletter_status` = 'T'
				$affichageNlT = $wpdb->get_var("SELECT `newsletter_message`
												FROM ".$table_nl.
												" WHERE `newsletter_showed_id` = ".$_POST['newsletter'].
												" AND `newsletter_status` = 'T'");

				// Affiche `newsletter_subject` en fonction de `newsletter_showed_id`
				$titreNl = $wpdb->get_var("SELECT `newsletter_subject` 
											FROM ".$table_nl.
											" WHERE `newsletter_showed_id` = ".$_POST['newsletter']);


				// Bouton "Visualiser"
				if ($_POST['event'] == "Visualiser") {
					echo $affichageNlA;
				}

				// Bouton "Envoyer un test"
				if ($_POST['event'] == "EnvoyerTest") {
					foreach ($tousLesTesteurs as $testeur) {
						$subscriber_id = $testeur->subscriber_id;
						$subscriber_email = $testeur->subscriber_email;

						$hote = get_option('atm_nl_nomdomaine');
						$nomDUtilisateur = get_option('atm_nl_nomutilisateur');
						$motDePasse = get_option('atm_nl_motdepasse');
						$adresseMailExpediteur = get_option('atm_nl_adresseexpediteur');
						$nomDuDestinaire = get_option('atm_nl_nomexpediteur');

					 	$mail = new PHPMailer;

					 	$mail->CharSet = "UTF-8"; 

						// $mail->SMTPDebug = 4;
						// $mail->Debugoutput = "html";

					 	$mail->setLanguage("fr");

						$mail->isSMTP();
						$mail->Host = $hote;
						$mail->SMTPAuth = true;
						$mail->Username = $nomDUtilisateur;
						$mail->Password = $motDePasse;
						$mail->Port = $port;

						$mail->SMTPKeepAlive = true;
						                  
						$mail->setFrom($adresseMailExpediteur, $nomDuDestinaire);

					 	$mail->addAddress($subscriber_email);
					 	
						$mail->AddCustomHeader("Sender:".$adresseMailExpediteur);
						$mail->AddCustomHeader("X-Confirm-Reading-To:".$adresseMailExpediteur);
						$mail->AddCustomHeader("Return-receipt-to:".$adresseMailExpediteur);

						$mail->isHTML(true);

						$mail->Subject = $titreNl;
						$mail->Body    = '<pre>'.$affichageNlA.'</pre>';
						$mail->AltBody = "Veuillez accepter la lecture du HTML sur votre messagerie !";
						
						if(!$mail->send()) {
						    echo "Le message n'a pas pu être envoyé. Il y a une erreur : ".$mail->ErrorInfo.'<br>';
						} else {
							echo "Message envoyé à l'adresse suivante : ".$subscriber_email.'<br>';
						}
						// Pour ne pas passer en tant qu'adresse de spam on espace les envois d'emails
						// Temps d'attente d'une seconde et demie
						sleep(1);
					}

					// timestamp en millisecondes de la fin du script
					$finDuScript = microtime(true);
					// Différence en millisecondes entre le début et la fin
					$tempsDExecutionDuScript = $finDuScript - $debutDuScript;
					// Affichage du résultat
					echo "L'envoi des emails a duré : ".$tempsDExecutionDuScript." secondes.";
				}				

				// Bouton "Envoyer réellement"
				if ($_POST['event'] == "EnvoyerReel") {

					$wpdb->update($table_nl,
			                    	array('newsletter_total' => $nombreDeConfirmes, 'newsletter_sent' => 0, 'newsletter_send_on' => $nombreDeConfirmes), 
				                    array('newsletter_showed_id' => $_POST['newsletter'], 'newsletter_status' => 'T')
			      	);

					// On instancie les compteurs pour la BDD
					$newsletter_send = 0;
					$newsletter_send_on = $nombreDeConfirmes;

					foreach ($tousLesConfirmes as $confirme) {
						$subscriber_id = $confirme->subscriber_id;
						$subscriber_email = $confirme->subscriber_email;
						
						$hote = get_option('atm_nl_nomdomaine');
						$nomDUtilisateur = get_option('atm_nl_nomutilisateur');
						$motDePasse = get_option('atm_nl_motdepasse');
						$adresseMailExpediteur = get_option('atm_nl_adresseexpediteur');
						$nomDuDestinaire = get_option('atm_nl_nomexpediteur');

					 	$mail = new PHPMailer;

					 	$mail->CharSet = "UTF-8"; 

						// $mail->SMTPDebug = 4;
						// $mail->Debugoutput = "html";

					 	$mail->setLanguage("fr");

						$mail->isSMTP();
						$mail->Host = $hote;
						$mail->SMTPAuth = true;
						$mail->Username = $nomDUtilisateur;
						$mail->Password = $motDePasse;
						$mail->Port = $port;

						$mail->SMTPKeepAlive = true;
						                  
						$mail->setFrom($adresseMailExpediteur, $nomDuDestinaire);

					 	$mail->addAddress($subscriber_email);
					 	
						$mail->AddCustomHeader("Sender:".$adresseMailExpediteur);
						$mail->AddCustomHeader("X-Confirm-Reading-To:".$adresseMailExpediteur);
						$mail->AddCustomHeader("Return-receipt-to:".$adresseMailExpediteur);

						$mail->isHTML(true);

						$mail->Subject = $titreNl;
						$mail->Body    = '<pre>'.$affichageNlT.'</pre>';
						$mail->AltBody = "Veuillez accepter la lecture du HTML sur votre messagerie !";
						
						if(!$mail->send()) {
						    echo "Le message n'a pas pu être envoyé. Il y a une erreur : ".$mail->ErrorInfo.'<br>';
						} else {
							echo "Le message envoyé à l'adresse suivante : ".$confirme->subscriber_email.'<br>';

							$newsletter_send++;
							$newsletter_send_on--;

							// On MAJ les envois de nl : ce qu'il reste à envoyer et ce qui a été envoyé
							$wpdb->update($table_nl,
				                    	array('newsletter_last_id' => $confirme->subscriber_id, 'newsletter_sent' => $newsletter_send, 'newsletter_send_on' => $newsletter_send_on), 
					                    array('newsletter_showed_id' => $_POST['newsletter'], 'newsletter_status' => 'T')
					      	);
						}
						// Pour ne pas passer en tant qu'adresse de spam on espace les envois d'emails
						// Temps d'attente d'une seconde et demie
						sleep(1);
					}
					
					// On MAJ à la fin des envois d'email la nl concernée
			      	$wpdb->update($table_nl,
		                    	array('newsletter_status' => 'E', 'newsletter_date' => current_time('mysql')), 
			                    array('newsletter_showed_id' => $_POST['newsletter'], 'newsletter_status' => 'T')
			      	);
				
					// timestamp en millisecondes de la fin du script
					$finDuScript = microtime(true);
					// Différence en millisecondes entre le début et la fin
					$tempsDExecutionDuScript = $finDuScript - $debutDuScript;
					// Affichage du résultat
					echo "L'envoi des emails a duré : ".$tempsDExecutionDuScript." secondes.";
				}

				// Bouton "Statistiques"
				if ($_POST['event'] == "Statistiques") {
					$statistiquesNl = $wpdb->get_row("SELECT *
		                                      FROM ".$table_nl. "
		                                      WHERE `newsletter_id`= ".$_POST['newsletter']);

					$tableauStatsNl = [$statistiquesNl->newsletter_read_count, $statistiquesNl->newsletter_click_count, $statistiquesNl->newsletter_open_count];

					if ($tableauStatsNl[0] != 0) {
						echo '<div id="donutchart" style="border: 1px solid #ccc"></div>';
					}
				}

				// Le bouton supprimé est traité dans la page précédente
			}

		?>

		<script type="text/javascript">

			var tableauStatsNl = ["<?php echo implode('", "', $tableauStatsNl); ?>"];

		  	if (tableauStatsNl[0] == 0) {
		  		document.write("Il n'y a pas données pertinentes à afficher... ");
		  	} else {
			  	google.charts.load("current", {packages:["corechart"]});
			  	google.charts.setOnLoadCallback(drawChart);

		  		function drawChart() {
				    var data = new google.visualization.DataTable();
				    data.addColumn('string', 'Statistique');
				    data.addColumn('number', 'Nombre');
				    data.addRows([
				      	['Emails Lus', parseInt(tableauStatsNl[0])],
				      	['Lien(s) cliqué(s)', parseInt(tableauStatsNl[1])],
				      	['Lien(s) ouvert(s)', parseInt(tableauStatsNl[2])]
				    ]);

			    var options = {
			      	title: 'Statistiques de la Newsletter sélectionnée',
			      	width:500,
			      	height:400,
			      	colors: ['#33CC44', '#FF8800', '#0077FF'],
			      	pieHole: 0.4
			    };

			    var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
			    chart.draw(data, options);
			  }
		  }
		</script>
	</body>
</html>