<?php
	// Fonctions PHP //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

	// Requêtes SQL //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');
	
	// Ce tableau et ces booléens permettent de vérifier s'il y a un fichier :
	$tableauDesExtensions = array('.jpeg', '.jpg', '.png');

	// On définit les globales temporelles :
	// Modification des informations de localisation
	// Choix du fuseau horaire : Europe/Paris
	setlocale(LC_TIME, "fr_FR");
	date_default_timezone_set("Europe/Paris");

	// Affichage de la nl
	$cheminDuFichier = ABSPATH."wp-content/plugins/atm_newsletter_md/newsletterSent.html";

	$encodage = mb_detect_encoding($cheminDuFichier);
	$cheminDuFichier = mb_convert_encoding($cheminDuFichier, "ASCII", $encodage);
	$cheminDuFichier = str_replace("?", "", $cheminDuFichier);
	$cheminDuFichier = addslashes($cheminDuFichier);

	if(file_exists($cheminDuFichier)) {
	    $handle = fopen($cheminDuFichier, "r"); 
	    $affichageDeLaNewsletter = fread($handle, filesize($cheminDuFichier));
	    fclose($handle);

	    // Variables de contenus pour la newsletter
	    $newsletterPolice = "";
	    $newsletterAffichage = "";
	    $newsletterTitre = "";
	    $newsletterLogoClient = "";
	    // Tableau pour les trois couleurs
	    $newsletterCouleurs = array('', '', '');
	    $newsletterBody = "";
	    $newsletterReseauxSociaux = "";
	    $newsletterDesinscription = "";
	    
		if(isset($couleurPrincipaleNl) && !empty($couleurPrincipaleNl) && !is_null($couleurPrincipaleNl) && $couleurPrincipaleNl !== false){
			$newsletterCouleurs[0] = $couleurPrincipaleNl->option_value;
		}

		if(isset($couleurSecondaireNl) && !empty($couleurSecondaireNl) && !is_null($couleurSecondaireNl) && $couleurSecondaireNl !== false){
			$newsletterCouleurs[1] = $couleurSecondaireNl->option_value;
		}

		if(isset($backgroundColorNl) && !empty($backgroundColorNl) && !is_null($backgroundColorNl) && $backgroundColorNl !== false){
			$newsletterCouleurs[2] = $backgroundColorNl->option_value;
		}

		if(isset($policeNl) && !empty($policeNl) && !is_null($policeNl) && $policeNl !== false){
			$newsletterPolice = $policeNl->option_value;
		}

		// Affiche de "Si ce message ne s'affiche pas correctement, cliquez-ici."
		$newsletterAffichage = '<h5><hr style="height: 1px; color:'.$newsletterCouleurs[1].'; background-color:'.$newsletterCouleurs[1].'; border: none;"><a target="_blank" style="text-decoration:none; color:'.$newsletterCouleurs[0].';" href="'.esc_url(home_url('/'))."wp-content/plugins/atm_newsletter_md/newsletterNav.php?id=".$last_newsletter_showed_id."&status=C".'" title="">'."Si ce message ne s'affiche pas correctement, cliquez-ici.".'</a><hr style="height: 1px; color:'.$newsletterCouleurs[1].'; background-color:'.$newsletterCouleurs[1].'; border: none;"></h5>';

		// On affiche d'abord la couleur principale parce qu'elle est utile pour certains affichage d'éléments 

		if(isset($titreNl) && !empty($titreNl) && !is_null($titreNl) && $titreNl !== false){
			$option_value = $titreNl->option_value;

			// Affichage du titre de la newsletter
			$newsletterTitre = '<p><h1 style="color:'.$newsletterCouleurs[0].';">'."Newsletter n°".$last_newsletter_showed_id;

			switch ($option_value) {
				case "minute":
					$newsletterTitre .= " de ".strftime("%c").'</h1><hr style="height: 1px; color:'.$newsletterCouleurs[1].'; background-color:'.$newsletterCouleurs[0].'; border: none;"></p>';
					break;

				case "hedbomadaire":
					$newsletterTitre .= " du ".utf8_encode(strftime("%A %e %B %Y", time()))." au ".utf8_encode(strftime("%A %e %B %Y", strtotime("+6 day"))).'</h1><hr style="height: 1px; color:'.$newsletterCouleurs[1].'; background-color:'.$newsletterCouleurs[0].'; border: none;"></p>';
					break;
				
				case "quinzaine":
					$newsletterTitre .= " du ".utf8_encode(strftime("%A %e %B %Y", time()))." au ".utf8_encode(strftime("%A %e %B %Y", strtotime("+13 day"))).'</h1><hr style="height: 1px; color:'.$newsletterCouleurs[1].'; background-color:'.$newsletterCouleurs[0].'; border: none;"></p>';
					break;

				case "mensuel":
					if ((strftime("%B") == "avril") || (strftime("%B") == "août")) {
						$newsletterTitre .= " du mois d'".utf8_encode(strftime("%B %Y")).'</h1><hr style="height: 1px; color:'.$newsletterCouleurs[1].'; background-color:'.$newsletterCouleurs[0].'; border: none;"></p>';
					} else {
						$newsletterTitre .= " du mois de ".utf8_encode(strftime("%B %Y")).'</h1><hr style="height: 1px; color:'.$newsletterCouleurs[1].'; background-color:'.$newsletterCouleurs[0].'; border: none;"></p>';
					}
					break;

				case "bimestriel":
					if ((strftime("%B") == "avril") || (strftime("%B") == "août") || (strftime("%B") == "octobre")) { // Cas mois Avril/Août/Octobre
						$newsletterTitre .= " des mois d'".utf8_encode(strftime("%B")).'/'.utf8_encode(strftime("%B %Y", strtotime("+1 month"))).'</h1><hr style="height: 1px; color:'.$newsletterCouleurs[1].'; background-color:'.$newsletterCouleurs[0].'; border: none;"></p>';
					} else { // Cas mois Janvier/Février/Mars/Mai/Juin/Juillet/Septembre/Novembre/Décembre
						if (strftime("%Y") !== (strftime("%Y", strtotime("+1 month")))) { // Cas mois Décembre N/Janvier N+1 où N est égal à l'année
							$newsletterTitre .= " des mois de ".utf8_encode(strftime("%B")).' '.strftime("%Y").'/'.utf8_encode(strftime("%B %Y", strtotime("+1 month"))).'</h1><hr style="height: 1px; color:'.$newsletterCouleurs[1].'; background-color:'.$newsletterCouleurs[0].'; border: none;"></p>';
						} else { // Cas mois Janvier/Février/Mars/Mai/Juin/Juillet/Septembre/Novembre/
							$newsletterTitre .= " des mois de ".strftime("%B").'/'.strftime("%B %Y", strtotime("+1 month")).'</h1><hr style="height: 1px; color:'.$newsletterCouleurs[1].'; background-color:'.$newsletterCouleurs[0].'; border: none;"></p>';
						}
					}
					break;

				case "trimestriel":
					if ((strftime("%B") == "avril") || (strftime("%B") == "août") || (strftime("%B") == "octobre")) { // Cas mois Avril/Août/Octobre
						$newsletterTitre .= " des mois d'".utf8_encode(strftime("%B")).'/'.utf8_encode(strftime("%B", strtotime("+1 month"))).'/'.utf8_encode(strftime("%B %Y", strtotime("+2 month"))).'</h1><hr style="height: 1px; color:'.$newsletterCouleurs[1].'; background-color:'.$newsletterCouleurs[0].'; border: none;"></p>';
					} else { // Cas mois Janvier/Février/Mars/Mai/Juin/Juillet/Septembre/Novembre/Décembre
						if ((strftime("%Y")) !== (strftime("%Y", strtotime("+1 month")))) { // Cas mois Novembre N/Décembre N/Janvier N+1 où N est égal à l'année
							$newsletterTitre .= " des mois de ".utf8_encode(strftime("%B %Y")).'/'.utf8_encode(strftime("%B", strtotime("+1 month"))).'/'.utf8_encode(strftime("%B %Y", strtotime("+2 month"))).'</h1><hr style="height: 1px; color:'.$newsletterCouleurs[1].'; background-color:'.$newsletterCouleurs[0].'; border: none;"></p>';
						} else if ((strftime("%Y")) !== (strftime("%Y"))) { // Cas mois Décembre N/Janvier N+1/Février N+1 où N est égal à l'année
							$newsletterTitre .= " des mois de ".utf8_encode(strftime("%B")).'/'.utf8_encode(strftime("%B %Y", strtotime("+1 month"))).'/'.utf8_encode(strftime("%B %Y", strtotime("+2 month"))).'</h1><hr style="height: 1px; color:'.$newsletterCouleurs[1].'; background-color:'.$newsletterCouleurs[0].'; border: none;"></p>';
						} else { // Cas mois Janvier/Février/Mars/Mai/Juin/Juillet/Septembre
							$newsletterTitre .= " des mois de ".utf8_encode(strftime("%B")).'/'.utf8_encode(strftime("%B", strtotime("+1 month"))).'/'.utf8_encode(strftime("%B %Y", strtotime("+2 month"))).'</h1><hr style="height: 1px; color:'.$newsletterCouleurs[1].'; background-color:'.$newsletterCouleurs[0].'; border: none;"></p>';
						}
					}
					break;

				default:
					break;
			}
		}

        $resultatsTableau = array();
        $compteur = 0;
    	$resultsQueryFirstTable = $wpdb->get_results($bodyNl);

    	var_dump($resultsQueryFirstTable);
    	echo '<br>';

    	foreach ($resultsQueryFirstTable as $resultQueryFirstQuery) {
    		$option_name = $resultQueryFirstQuery->option_name;
    		$tableauExplode = explode('_', $option_name);
    		$estPresent = get_option('atm_nl_post_type_'.$tableauExplode[4]); 

    		if (get_option('atm_nl_post_type_'.$tableauExplode[4]) == "true") {
    			$resultatsTableau[$compteur] = $tableauExplode[4];
	    		$compteur++;
    		}
    	}

    	for ($i = 0; $i < count($resultatsTableau); $i++) {
    		// Requête SQL retournant les 10 derniers posts pour chaque post_type sélectionné(s)
	 		$sqlQuerySecondTable = ("SELECT *
							        FROM ".$table_posts."
							        WHERE `post_type` = '".$resultatsTableau[$i]."'
							        AND post_status = 'publish'
							     	ORDER BY ID DESC
							        LIMIT 10");

	 		$resultsQuerySecondTable = $wpdb->get_results($sqlQuerySecondTable);

 		 	$post_type_object = get_post_type_object($resultatsTableau[$i]);
            $post_type_name = $post_type_object->labels->menu_name;

 			$newsletterBody .= '<p><h1 style="color:'.$newsletterCouleurs[0].';">'."Liste des derniers ".$post_type_name.'</h1></p><hr style="height: 1px; color:'.$newsletterCouleurs[0].'; background-color:'.$newsletterCouleurs[0].'; border: none;">';

 			$newsletterBody .= '<div>';
 				$newsletterBody .= '<table width="100%">';
			 		foreach ($resultsQuerySecondTable as $resultQuerySecondTable) {
		     			$post_date = $resultQuerySecondTable->post_date;
		     			// On convertit le type de date retourné par la BDD
		     			$dateFinale = date('\L\e d/m/Y \à H\:i', strtotime($post_date));
		     			$post_title = $resultQuerySecondTable->post_title;
		     			$resume = strip_tags($resultQuerySecondTable->post_content);
		     			// Si le post_content a plus de 100 caractères on le coupe
		     			// et on affiche jusqu'au dernier mot complet
		     			if (strlen($resume) > 100) {
		     				$resume = substr($resume, 0, 100);
		     				// Permet de ne pas afficher un mot "coupé" par strlen()
		     				$coupeMot = strrpos($resume, ' ', -1);
		     				$resume = substr($resume, 0, ($coupeMot +1))."[...]";
		     			}
     					$newsletterBody .= '<tr>';
     						$newsletterBody .= '<td colspan="2" width="50%" style="padding-left:30px">';
				     			$newsletterBody .= '<b><a target="_blank" style="text-decoration:none; color:'.$newsletterCouleurs[1].';" href="'.get_permalink($resultQuerySecondTable->ID).'"title="'.$post_title.'">'.$post_title.'</a></b><br>';
				     			$newsletterBody .= '<p style="color:black;">';
					     			$newsletterBody	.= $dateFinale.'<br>';
					     			$newsletterBody .= $resume.'<br><a target="_blank" style="text-decoration:none; color:'.$newsletterCouleurs[1].';" href="'.get_permalink($resultQuerySecondTable->ID).'"title="'.$post_title.'"><b>'."Lire la suite...".'</b></a>';
				     			$newsletterBody .= '</p></td>';
				     		$newsletterBody .= '<td colspan="1" width="25%"></td>';
				     		$newsletterBody .= '<td colspan="1" width="25%">';
				     			$newsletterBody .= get_the_post_thumbnail($resultQuerySecondTable->ID, 'thumbnail');
				     		$newsletterBody .= '</td>';
			     		$newsletterBody .= '</tr>';
			     		$newsletterBody .= '<tr>';
			     			$newsletterBody .= '<td colspan="4" width="100%">';
				     			$newsletterBody .= '<hr style="height: 1px; color:'.$newsletterCouleurs[0].'; background-color:'.$newsletterCouleurs[0].'; border: none;">';
				     		$newsletterBody .= '</td>';
			     		$newsletterBody .= '</tr>';
		     		}
     			$newsletterBody .= '</table>';
			$newsletterBody .= '</div>';
    	}

		// Logo du client
		for ($i = 0; $i < count($tableauDesExtensions); $i++) {
			if (file_exists(ABSPATH."wp-content/uploads/atm_newsletter_md/logo".$tableauDesExtensions[$i])) {
				$newsletterLogoClient = '<a style="text-decoration:none;" href="'.esc_url(home_url('/')).'" target="_blank"> <img src="'.home_url()."/wp-content/uploads/atm_newsletter_md/logo".$tableauDesExtensions[$i].'" style="MAX-WIDTH: 150px; HEIGHT: auto; WIDTH: 100%; MIN-WIDTH: 120px" alt=""></a>';
			}
		}

		// Ce tableau permet de stocker les RS et les afficher dans un '<table>'
		$tableauReseauxSociaux = array();

		if(isset($facebookNl) && !empty($facebookNl) && !is_null($facebookNl) && $facebookNl !== false){
			$tableauReseauxSociaux['facebook'] = $facebookNl->option_value;
		}
	
		if(isset($twitterNl) && !empty($twitterNl) && !is_null($twitterNl) && $twitterNl !== false){
			$tableauReseauxSociaux['twitter'] = $twitterNl->option_value;
		}

		$nombreReseauxSociaux = count($tableauReseauxSociaux);
		switch ($nombreReseauxSociaux) {
			case 0:
				$newsletterReseauxSociaux = "";
				break;

			case 1:
				if (get_option('atm_nl_facebook')){
					$newsletterReseauxSociaux = '<table width="100%">';
						$newsletterReseauxSociaux .= '<tr>';
							$newsletterReseauxSociaux .= "Suivez-nous sur notre Facebook :".'<br>';
						$newsletterReseauxSociaux .= '</tr>';
						$newsletterReseauxSociaux .= '<tr>';
							$newsletterReseauxSociaux .= '<td width="100%">';
								$newsletterReseauxSociaux .= '<a style="text-decoration:none;" href="'.get_option('atm_nl_facebook').'" target="_blank"> <img src="'.home_url().'/wp-content/plugins/atm_newsletter_md/fb.png" alt=""></a>';
							$newsletterReseauxSociaux .= '</td>';
						$newsletterReseauxSociaux .= '</tr>';
					$newsletterReseauxSociaux .= '</table>';
				} else {
					$newsletterReseauxSociaux = '<table width="100%">';
						$newsletterReseauxSociaux .= '<tr>';
							$newsletterReseauxSociaux .= "Suivez-nous sur notre Twitter :".'<br>';
						$newsletterReseauxSociaux .= '</tr>';
						$newsletterReseauxSociaux .= '<tr>';
							$newsletterReseauxSociaux .= '<td width="100%">';
								$newsletterReseauxSociaux .= '<a style="text-decoration:none;" href="'.get_option('atm_nl_twitter').'" target="_blank"> <img src="'.home_url().'/wp-content/plugins/atm_newsletter_md/tw.png" alt=""></a>';
							$newsletterReseauxSociaux .= '</td>';
						$newsletterReseauxSociaux .= '</tr>';
					$newsletterReseauxSociaux .= '</table>';
				}
				break;

			case 2:
				// Compatible Hotmail/Gmail/Thunderbird
				$newsletterReseauxSociaux = "Suivez-nous sur nos réseaux sociaux :".'<br>';
				$newsletterReseauxSociaux .= '<table width="100%">';
					$newsletterReseauxSociaux .= '<tr>';
						$newsletterReseauxSociaux .= '<td width="25%">&nbsp;</td>';
						$newsletterReseauxSociaux .= '<td width="25%">';
							$newsletterReseauxSociaux .= '<a style="text-decoration:none;" href="'.get_option('atm_nl_facebook').'" target="_blank"> <img src="'.home_url().'/wp-content/plugins/atm_newsletter_md/fb.png" alt=""></a>';
						$newsletterReseauxSociaux .= '</td>';
						$newsletterReseauxSociaux .= '<td width="25%">';
							$newsletterReseauxSociaux .= '<a style="text-decoration:none;" href="'.get_option('atm_nl_twitter').'" target="_blank"> <img src="'.home_url().'/wp-content/plugins/atm_newsletter_md/tw.png" alt=""></a>';
						$newsletterReseauxSociaux .= '</td>';
						$newsletterReseauxSociaux .= '<td width="25%">&nbsp;</td>';
					$newsletterReseauxSociaux .= '</tr>';
				$newsletterReseauxSociaux .= '</table>';
				break;
				
			default:
				$newsletterReseauxSociaux = "";
				break;
		}

		$newsletterDesinscription = '<a target="_blank" style="text-decoration:none; color:'.$newsletterCouleurs[1].';" href="'.esc_url(home_url('/'))."wp-content/plugins/atm_newsletter_md/views/desinscription.php".'" title="">'."Désinscription".'</a>';

		// On affiche la newsletter
    	$affichageDeLaNewsletter = str_replace(array('{POLICE}', '{NEWSLETTER}', '{TITRE}', '{LOGO}', '{COULEUR1}', '{COULEUR2}', '{COULEUR3}', '{BODY}', '{RS}', '{DESINSCRIPTION}'), array($newsletterPolice, $newsletterAffichage, $newsletterTitre, $newsletterLogoClient, $newsletterCouleurs[0], $newsletterCouleurs[1], $newsletterCouleurs[2], $newsletterBody, $newsletterReseauxSociaux, $newsletterDesinscription), $affichageDeLaNewsletter);
	} else {
	    echo "Fichier de modèle de newsletter HTML non trouvé.";
	}
?>