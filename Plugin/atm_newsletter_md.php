<?php

	/*
	Plugin Name: atm_newsletter_md
	Plugin URI: http://www.atmospherecommunication.fr/ 
	Description: Un plugin de newsletter fait par Martin Dazin
	Version: 0.9
	Author: Martin Dazin
	Author URI: http://www.atmospherecommunication.fr 
	License: GPL2
	*/


	// Fonction de requêtes SQL pour créer les tables dans la BDD
	function atm_newsletter_SQLQueries(){

		require_once(ABSPATH.'wp-admin/includes/upgrade.php');

	 	global $wpdb;

		$table_subscriber = $wpdb->prefix."atm_nl_subscriber";
		$table_newsletter = $wpdb->prefix."atm_nl_newsletter";
		$table_sent = $wpdb->prefix."atm_nl_sent";
		$table_stat = $wpdb->prefix."atm_nl_stat";

		$sqlQueryCreateTables = "
								CREATE TABLE $table_newsletter (
								  	`newsletter_id` int(11) unsigned NOT NULL auto_increment,
								  	`newsletter_showed_id` int(11) unsigned NOT NULL,
							  	 	`newsletter_subject` longtext NOT NULL,
								  	`newsletter_message` longtext NOT NULL,
								  	`newsletter_message_text` longtext NOT NULL,
								  	`newsletter_date` datetime NOT NULL,
								  	`newsletter_status` varchar(1) NOT NULL,
								  	`newsletter_total` int(11) unsigned NOT NULL ,
								  	`newsletter_last_id` int(11) unsigned NOT NULL,
								  	`newsletter_sent` int(11) unsigned NOT NULL,
								  	`newsletter_send_on` int(11) unsigned NOT NULL,
								  	`newsletter_read_count` int(11) unsigned NOT NULL,
								  	`newsletter_click_count` int(11) unsigned NOT NULL,
								  	`newsletter_open_count` int(11) unsigned NOT NULL,
								  	PRIMARY KEY (`newsletter_id`)
								) $charset_collate;

								CREATE TABLE $table_stat (
									`stat_id` int(11) unsigned auto_increment,
									`stat_date` datetime NOT NULL,
									`stat_ip` varchar(20) NOT NULL,
									`newsletter_showed_id` int(11) unsigned NOT NULL,
									PRIMARY KEY (`stat_id`)
								) $charset_collate;

								CREATE TABLE $table_subscriber (
								  	`subscriber_id` int(11) unsigned NOT NULL auto_increment,
								  	`subscriber_email` varchar(100) NOT NULL,
								  	`subscriber_status` varchar(1) NOT NULL,
								  	`subscriber_created` datetime NOT NULL,
								  	`subscriber_ip` varchar(20) NOT NULL,
								  	PRIMARY KEY (`subscriber_id`)
								) $charset_collate;
							";

								/*
								CREATE TABLE $table_sent (
									`sent_id` int(11) unsigned NOT NULL auto_increment,
								  	`subscriber_id` int(11) unsigned NOT NULL,
								  	`newsletter_id` int(11) unsigned NOT NULL,
								  	`sent_status` varchar(1) NOT NULL,
								  	`sent_time` datetime NOT NULL,
								  	`sent_error` int(11) unsigned NOT NULL,
								  	`sent_open` tinyint(1) unsigned NOT NULL,
								  	`sent_ip` varchar(30) NOT NULL,
								  	PRIMARY KEY (`sent_id`)
								)

								ALTER TABLE `test_atm_nl_sent`
									ADD CONSTRAINT `fk_subscriber_id` FOREIGN KEY (`subscriber_id`) REFERENCES `test_atm_nl_subscriber` (`subscriber_id`) ON DELETE CASCADE ON UPDATE CASCADE,
									ADD CONSTRAINT `fk_newsletter_id` FOREIGN KEY (`newsletter_id`) REFERENCES `test_atm_nl_newsletter` (`newsletter_id`) ON DELETE CASCADE ON UPDATE CASCADE;
								*/

		// Exécution des CREATE TABLE
		dbDelta($sqlQueryCreateTables);

		// On ajoute l'option atm_newsletter_CRON_activation dans la table $wpdb->prefix.'options';
		// add_option('atm_newsletter_CRON_activation', 'faux', '', 'yes');
	}

	// Fonction pour créer le dossier d'upload personnalisé
	function atm_newsletter_createFolder(){
		// Retourne le dossier de travail courant de la forme [...]/nomduclient/wp-admin
		$dossierTravailCourant = explode('/', getcwd());

		// On garde [...]/nomduclient/
		for ($i = 0; $i < count($dossierTravailCourant)-1; $i++){
			$monCheminOriginel = $monCheminOriginel.$dossierTravailCourant[$i].'/';
		}
		
		// On enlève le dernier '/' de $monCheminOriginel
		chdir($monCheminOriginel);
	
		// Retourne le répertoire courant de la forme [...]/nomduclient
		$monCheminOriginel = getcwd();

		// On crée le dossier personalisé d'uploads
			// S'il est déjà créé mkdir() renvoie une erreur
		if (!file_exists($monCheminOriginel."/wp-content/uploads/atm_newsletter_md")) {
			mkdir($monCheminOriginel."/wp-content/uploads/atm_newsletter_md", 0755);
		}
	}

	// Fonctions pour créer et paramétrer le CRON d'envoi de la newsletter
	/*
	add_action('init', 'everyone_schedule_cron');
	function everyone_schedule_cron() {
	    if (! wp_next_scheduled('atm_newsletter_cron')) {
	        wp_schedule_event(time(), 'annee', 'atm_newsletter_cron');
	    }
	}
	*/

	/*
	add_action('atm_newsletter_cron', 'atm_newsletter_cron_envoiMails');
	function atm_newsletter_cron_envoiMails() {
      	// On insère ici le code qui sera automatiquement exécuté par le cron.
      	// Dans le cas du plugin de newsletter, on enverra un email.
      	wp_mail(get_option('atm_newsletter_adressetest'),'Mon sujet', "Coucou, aujourd'hui on est le ".date('\L\e d/m/Y \à H\:i', strtotime(time())));
	}
	*/

	// spawn_cron();

	/*
	add_filter('cron_schedules', 'add_cron_schedule');
	function add_cron_schedule($schedules) {
	    // Occurence de test
	    $schedules['minute'] = array(
	        'interval' => 60, // In seconds
	        'display'  => __('Une minute'),
	    );
	    $schedules['semaine'] = array(
	        'interval' => 604800, // In seconds
	        'display'  => __('Une semaine'),
	    );
	    $schedules['quinzaine'] = array(
	        'interval' => 1209600, // In seconds
	        'display'  => __('Une quinzaine'),
	    );
     	$schedules['mensuel'] = array(
	        'interval' => 2629800, // In seconds
	        'display'  => __('Un mois'),
	    );
      	$schedules['bimestriel'] = array(
	        'interval' => 5259600, // In seconds
	        'display'  => __('Deux mois'),
	    );
	    $schedules['trismestriel'] = array(
	        'interval' => 7889400, // In seconds
	        'display'  => __('Un trimestre'),
	    );
	    $schedules['annee'] = array(
	    	'interval' => 31557600, // In seconds
	        'display'  => __('Une année'),
	    );
	 
	    return $schedules;
	}
	*/

	// Lorsque l'on active le plugin
	function atm_newsletter_activate(){
		atm_newsletter_SQLQueries();
		atm_newsletter_createFolder();
		// atm_newsletter_createCRON();

	}
	register_activation_hook(__FILE__, 'atm_newsletter_activate');	


	// Fonction de requêtes SQL pour supprimer les tables dans la BDD
	function atm_newsletter_desinstallTables(){
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');

		// Accès à la BDD
		global $wpdb;
	 	$charset_collate = $wpdb->get_charset_collate();
	    $tables = array("atm_nl_newsletter", /*"atm_nl_sent",*/ "atm_nl_subscriber", "atm_nl_stat");

	    // Suppression des tables
		foreach ($tables as $table) {
   			$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}$table");
   		}
	}

	// Fonction de requêtes SQL pour supprimer toutes les `options_name` avec le préfixe atm_newsletter
	function atm_newsletter_deleteOptions(){
		global $wpdb;
    	$table_options = $wpdb->prefix.'options';

	 	$sqlQuery = ("SELECT *
			    	FROM ".$table_options."
			    	WHERE `option_name`
			    	LIKE 'atm_nl_%'");
	 	$resultsQuery = $wpdb->get_results($sqlQuery);

	    if(isset($resultsQuery) && !empty($resultsQuery) && !is_null($resultsQuery) && is_array($resultsQuery) && $resultsQuery !== false){
	    	foreach ($resultsQuery as $resultQuery) {
	    		$option_name = $resultQuery->option_name;
	           	delete_option($option_name);
	    	}
	    }
	}

	// Fonction qui supprime les fichiers contenus dans /atm_newsletter_md
	// Puis suppression du dossier
	function atm_newsletter_deleteFolder(){
		// Retourne le dossier de travail courant de la forme [...]/nomduclient/wp-admin
		$dossierTravailCourant = explode('/', getcwd());
		
		// On garde [...]/nomduclient/
		for ($i = 0; $i < count($dossierTravailCourant)-1; $i++){
			$monCheminOriginel = $monCheminOriginel.$dossierTravailCourant[$i].'/';
		}
		
		// On enlève le dernier '/' de $monCheminOriginel
		chdir($monCheminOriginel);
		// Retourne le répertoire courant de la forme [...]/nomduclient
		$monCheminOriginel = getcwd();
		// On liste dans un tableau tous les fichiers présents dans le dossier /atm_newsletter_md 
		$contenuDeMonDossier = scandir($monCheminOriginel."/wp-content/uploads/atm_newsletter_md");
		// Suppression de tous les fichiers contenus dans le dossier /atm_newsletter_md
		if (count($contenuDeMonDossier) > 2) {
			for ($i = 2; $i < count($contenuDeMonDossier); $i++) {
				unlink($monCheminOriginel."/wp-content/uploads/atm_newsletter_md/".$contenuDeMonDossier[$i]);
			}
		}
		// Suppression du dossier /atm_newsletter_md
		if (count($contenuDeMonDossier) == 2){
			rmdir($monCheminOriginel."/wp-content/uploads/atm_newsletter_md/");
		}
	}
	

	// Lorsque l'on désactive le plugin	
	function atm_newsletter_deactivate(){
		atm_newsletter_deleteFolder();
		atm_newsletter_desinstallTables();
		atm_newsletter_deleteOptions();

	}
	register_deactivation_hook( __FILE__, 'atm_newsletter_deactivate');


	// Création et affichage du menu et des pages
	function atm_newsletter_display_menu_pages() {
	  	add_menu_page(__('Newsletter'), __('Newsletter'), 'edit_themes', 'atm_newsletter', 'atm_newsletter_printStatistiques', null, 30);
	}
	add_action('admin_menu', 'atm_newsletter_display_menu_pages');
	
	function atm_newsletter_register_submenu_pages() {
		add_submenu_page('atm_newsletter', __('Statistiques'), __('Statistiques'), 'edit_themes', 'atm_newsletter', 'atm_newsletter_printStatistiques');
		add_submenu_page('atm_newsletter', __('Configuration du serveur SMTP'), __('Configuration du serveur SMTP'), 'edit_themes', 'atm_newsletter_serveurSMTP', 'atm_newsletter_printServeurSMTP');
		add_submenu_page('atm_newsletter', __('Import/Gestion des Personnes'), __('Import/Gestion des Personnes'), 'edit_themes', 'atm_newsletter_personnes', 'atm_newsletter_printSubsIndex');
		add_submenu_page('atm_newsletter', __('Newsletters'), __('Newsletters'), 'edit_themes', 'atm_newsletter_newsletter', 'atm_newsletter_printNlIndex');
	}
	add_action('admin_menu', 'atm_newsletter_register_submenu_pages');

	// Affichage du menu "Statistiques"
	function atm_newsletter_printStatistiques(){
	    if(!is_admin()) die();
	    include('views/index.php');
	}

	// Affichage du menu "Configuration du Serveur SMTP"
	function atm_newsletter_printServeurSMTP(){
	    if(!is_admin()) die();
	    include('views/serveurSMTP.php');
	}

	// Affichage du menu "Import/Gestion des Personnes"
	function atm_newsletter_printSubsIndex(){
	    if(!is_admin()) die();
	    include('views/subs/subs_index.php');
	}

	// Affichage du menu "Newsletters"
	function atm_newsletter_printNlIndex(){
	    if(!is_admin()) die();
	    include('views/nl/nl_index.php');
	}


	// Ajout des icônes personnalisées
	function atm_add_inscriptions_menus_icons_styles(){
    ?>
	    <style>
	    #adminmenu #toplevel_page_atm_newsletter div.wp-menu-image:before {
	    	content: "\f511";
	    }
	    </style>
    <?php
    }
	add_action('admin_head', 'atm_add_inscriptions_menus_icons_styles');
?>