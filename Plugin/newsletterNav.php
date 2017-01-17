<?php
	require($_SERVER['DOCUMENT_ROOT'].'test/wp-load.php' );

	// Fonctions PHP //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

	// Requêtes SQL //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');
	
	// Requête SQL : on a besoin des params du $_GET pour pouvoir l'exécuter
	// Affiche `newsletter_message` en fonction de `newsletter_showed_id` et `newsletter_status` = 'C'
	if ($_GET['status'] == 'T') {
		$affichageNl = $wpdb->get_var("SELECT `newsletter_message`
										FROM ".$table_nl.
										" WHERE `newsletter_showed_id` = ".$_GET['id'].
										" AND (`newsletter_status` = 'T' OR `newsletter_status` = 'E')");
	} else if ($_GET['status'] == 'C') {
		$affichageNl = $wpdb->get_var("SELECT `newsletter_message`
										FROM ".$table_nl.
										" WHERE `newsletter_showed_id` = ".$_GET['id'].
										" AND `newsletter_status` = 'C'");
	}

	echo $affichageNl;
?>