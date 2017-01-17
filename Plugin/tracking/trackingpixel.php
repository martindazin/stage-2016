<?php
	// Attention il ne faut pas laisser de echo ou de var_dump() non commentés dans ce fichier,
	// sinon le pixel ne s'intégrera pas lors se la création d'une nl

	// Permet de charger toutes les fonctionnalités de WP dpour un document externe
	require($_SERVER['DOCUMENT_ROOT'] . 'test/wp-load.php' );

	setlocale(LC_TIME, "fr_FR");
	date_default_timezone_set("Europe/Paris");

	global $wpdb;

	$table_nl = $wpdb->prefix.'atm_nl_newsletter';

 	// Récupérer le dernier ID newsletter_showed_id
 	// Enlever cette requête SQL quand on fait des tests pour ne pas fausser les stats
    $last_newsletter_showed_id = $wpdb->get_var("SELECT MAX(`newsletter_showed_id`)
                        						FROM ".$table_nl);
	
	// On s'assure que le dossier où est contenu pixel.log possède des droits
	// On enregistre des infos
	function printLog($str)
	{
	  file_put_contents(ABSPATH.'wp-content/plugins/atm_newsletter_md/tracking/pixel.log', $str."\n", FILE_APPEND | LOCK_EX );
	}

	printLog(date('Y-m-d H:i:s').','.$_SERVER['REMOTE_ADDR'].','.$last_newsletter_showed_id);

	// On affiche le pixel 
	header('Content-Type: image/gif');
	// This echo is equivalent to read an image
	// echo "\x47\x49\x46\x38\x37\x61\x1\x0\x1\x0\x80\x0\x0\xfc\x6a\x6c\x0\x0\x0\x2c\x0\x0\x0\x0\x1\x0\x1\x0\x0\x2\x2\x44\x1\x0\x3b";
	readfile(ABSPATH."/wp-content/plugins/atm_newsletter_md/tracking/pixel.gif");	
?>