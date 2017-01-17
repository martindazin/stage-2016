<?php
	// Permet d'accéder à toutes les fonctions WP
	require($_SERVER['DOCUMENT_ROOT'].'test/wp-load.php' );

	// Globale pour se servir des BDD
	global $wpdb;

	// Appel aux tables de la BDD dont on a besoin
	$table_nl = $wpdb->prefix.'atm_nl_newsletter';
	$table_subs = $wpdb->prefix.'atm_nl_subscriber';
  	$table_stats = $wpdb->prefix.'atm_nl_stat';
  	$table_options = $wpdb->prefix.'options';
 	$table_posts = $wpdb->prefix.'posts';

 	

 	//////////
 	////////// PARTIE test_atm_nl_newsletter
 	//////////

 	// Le nombre d'emails
	$nombreDeNl = $wpdb->get_var("SELECT COUNT(*)
									FROM ".$table_nl);

	// Toutes les nl Sans tracking affichées par ordre décroissant
	$toutesLesNlSansCDec = $wpdb->get_results("SELECT *
												FROM ".$table_nl.
												" WHERE NOT `newsletter_status` = 'C'
												 ORDER BY `newsletter_id` DESC");

  	// Le nombre total de lectures sur toutes les nl
  	$totalEmailsLus = $wpdb->get_var("SELECT SUM(`newsletter_read_count`)
                                    	FROM ".$table_nl);

  	// Le nombre total de clics sur toutes les nl
  	$totalEmailsCliques = $wpdb->get_var("SELECT SUM(`newsletter_click_count`) 
                                        	FROM ".$table_nl);

  	// Le nombre total d'ouvertures de liens sur toutes les nl
  	$totalEmailsOuverts = $wpdb->get_var("SELECT SUM(`newsletter_open_count`) 
                                        	FROM ".$table_nl);

  	// Le dernier ID newsletter_id
  	$idDerniereNl = $wpdb->get_var("SELECT MAX(`newsletter_id`)
	                                FROM ".$table_nl);

  	// Le dernier ID newsletter_showed_id
	$last_newsletter_showed_id = $wpdb->get_var("SELECT MAX(`newsletter_showed_id`)
        										FROM ".$table_nl);

	


	
  	//////////
 	////////// PARTIE test_atm_nl_subscriber
 	//////////

  	// Toutes les personnes
  	$toutesLesPersonnes = $wpdb->get_results("SELECT *
	                                            FROM ".$table_subs,
	                                            OBJECT);

  	// Tous les testeurs
  	$tousLesTesteurs = $wpdb->get_results("SELECT *
									     	FROM ".$table_subs.
									     	" WHERE `subscriber_status` LIKE 'T'",
									     	OBJECT);

  	// Tous les confirmés
	$tousLesConfirmes = $wpdb->get_results("SELECT *
											FROM ".$table_subs.
											" WHERE `subscriber_status` LIKE 'C'",
											OBJECT);

	// Nombre de confirmés
	$nombreDeConfirmes = $wpdb->get_var("SELECT COUNT(*) FROM ".$table_subs.
				     					" WHERE `subscriber_status` LIKE 'C'");




  	//////////
 	////////// PARTIE test_atm_nl_stat
 	//////////

  	// Avoir toutes les stats
  	$toutesLesStats = $wpdb->get_results("SELECT *
	                                        FROM ".$table_stats,
	                                        OBJECT);

  	// Le nombre de lectures pour chaque nl avec Tracking
  	$nombreLecturesParNlT = $wpdb->get_results ("SELECT `newsletter_showed_id`, COUNT(`newsletter_showed_id`) as nombre
	                                              FROM ".$table_stats.
	                                              " GROUP BY `newsletter_showed_id`",
	                                              OBJECT);

  	// var_dump($wpdb);
	// echo '<br>';


  	//////////
 	////////// PARTIE test_options
 	//////////

    // Couleur principale
	$couleurPrincipaleNl = $wpdb->get_row("SELECT *
								 			FROM ".$table_options.
								 			" WHERE `option_name` LIKE 'atm_nl_couleur1'");

	// Couleur secondaire
	$couleurSecondaireNl = $wpdb->get_row("SELECT *
								 			FROM ".$table_options.
								 			" WHERE `option_name` LIKE 'atm_nl_couleur2'");

	// Background color de la newsletter
    $backgroundColorNl = $wpdb->get_row("SELECT *
							 			FROM ".$table_options.
							 			" WHERE `option_name` LIKE 'atm_nl_couleur3'");

    // Police de la newsletter
	$policeNl = $wpdb->get_row("SELECT *
					 			FROM ".$table_options.
					 			" WHERE `option_name` LIKE 'atm_nl_typographieNl'");
  	
	// Titre de la newsletter
    $titreNl = $wpdb->get_row("SELECT *
					 			FROM ".$table_options.
					 			" WHERE `option_name` LIKE 'atm_nl_periodiciteNl'");

    // <body> de la newsletter
    $bodyNl = ("SELECT * 
				FROM ".$table_options.
				" WHERE `option_name` LIKE 'atm_nl_post_type_%'");

    // var_dump($wpdb);
    // echo '<br>';

	// Réseaux social Facebook
 	$facebookNl = $wpdb->get_row("SELECT *
						 			FROM ".$table_options.
						 			" WHERE `option_name` LIKE 'atm_nl_facebook'");

 	// Réseaux social Twitter
 	$twitterNl = $wpdb->get_row("SELECT *
					 			FROM ".$table_options."
					 			WHERE `option_name` LIKE 'atm_nl_twitter'");




  	//////////
 	////////// PARTIE test_posts
 	//////////

	// Le nombre de chaque types de posts exceptés
	// ACF, page, revision, contact form  
    // qui ont été publiés
    $typeDePosts = ("SELECT `post_type`, COUNT(`post_type`) as count
						 	FROM ".$table_posts.
						 	" WHERE NOT `post_type` = 'acf-field'
							AND NOT `post_type` = 'acf-field-group'
						 	AND NOT `post_type` = 'page'
						 	AND NOT `post_type` = 'revision'
						 	AND NOT `post_type` = 'wpcf7_contact_form'
							AND post_status = 'publish'
						 	GROUP BY `post_type`");




?>