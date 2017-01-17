<?php

	/*
	Plugin Name: mu-plugin_md
	Plugin URI: http://www.atmospherecommunication.fr/ 
	Description: Un plugin de MU fait par Martin Dazin
	Version: 0.1
	Author: Martin Dazin
	Author URI: http://www.atmospherecommunication.fr 
	License: GPL2
	*/



	/*if (! wp_next_scheduled('newsletter_cron')) {
      wp_schedule_event(time(), 'annee', 'newsletter_cron');
	}*/

	add_action('init', 'everyone_schedule_cron');
	function everyone_schedule_cron() {
	    if (! wp_next_scheduled('newsletter_cron')) {
	        wp_schedule_event(time(), 'annee', 'newsletter_cron');
	    }
	}

	add_action('newsletter_cron', 'atm_newsletter_cron_envoiMails');
	function atm_newsletter_cron_envoiMails() {
      	// On insère ici le code qui sera automatiquement exécuté par le cron.
      	// Dans le cas du plugin de newsletter, on enverra un email.
      	wp_mail(get_option('atm_newsletter_adressetest'),'Mon sujet', "Coucou, aujourd'hui on est le ".date('\L\e d/m/Y \à H\:i', strtotime(time())));
	} 

	// spawn_cron();

	add_filter('cron_schedules', 'add_cron_schedule');
	function add_cron_schedule($schedules) {
	    // Occurence de test
	    $schedules['minute'] = array(
	        'interval' => 60, // En secondes
	        'display'  => __('Une minute'),
	    );
	    $schedules['semaine'] = array(
	        'interval' => 604800, // En secondes
	        'display'  => __('Une semaine'),
	    );
	    $schedules['quinzaine'] = array(
	        'interval' => 1209600, // En secondes
	        'display'  => __('Une quinzaine'),
	    );
     	$schedules['mensuel'] = array(
	        'interval' => 2629800, // En secondes
	        'display'  => __('Un mois'),
	    );
      	$schedules['bimestriel'] = array(
	        'interval' => 5259600, // En secondes
	        'display'  => __('Deux mois'),
	    );
	    $schedules['trismestriel'] = array(
	        'interval' => 7889400, // En secondes
	        'display'  => __('Un trimestre'),
	    );
	    $schedules['annee'] = array(
	    	'interval' => 31557600, // En secondes
	        'display'  => __('Un trimestre'),
	    );
	 
	    return $schedules;
	}


	// On récupère le CRON pour savoir quand il sera programmé
	$monTemps = wp_next_scheduled('newsletter_cron');

	// Si l'événement a été créé avec des arguments spécifiques, il les avoir aussi
	// On crée donc un tableau
	$original_args = array();

	// On le désactive jusqu'à un nouvel appel de la fonction wp_schedule_event()
	wp_unschedule_event($monTemps, 'newsletter_cron', $original_args);



?>