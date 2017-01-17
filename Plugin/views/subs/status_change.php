<h4><em>Attention : Cette opération est irréversible</em></h4>
<?php
	// Fonctions PHP //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

	// Requêtes SQL //
	include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');
	
	// Status des persones :
		// - B : Blacklistés
		// - C : Confirmés
		// - D : Désinscrit
		// - N : Non-Confirmés
		// - T : Testeurs 

	// Changement de table pour les adresses sélectionnées
	if(isset($_POST['action6']) && !empty($_POST['action6']) && !is_null($_POST['action6']) && $_POST['action6'] == 'save'){
		if(!$_POST['case']){
		   echo "Aucune checkbox n'a été cochée.";
		} else {
			foreach(atm_sanitize($_POST['case']) as $valeur) {
				$wpdb->update($table_subs, array('subscriber_status' => 'B'), array('subscriber_email' => $valeur));
			}
		}
	}

    if(isset($toutesLesPersonnes) && !empty($toutesLesPersonnes) && !is_null($toutesLesPersonnes) && is_array($toutesLesPersonnes) && $toutesLesPersonnes !== false){
	 	echo '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">';
	        echo '<table id="myTable" class="tablesorter">';
	        	echo '<thead>';
	        		echo '<tr>';
	        			echo '<th>Addresse mail</th>';
	        			echo '<th>Statut de cette adresse mail</th>';
	        			echo '<th>Passer cette adresse d\'abonné en blacklisté</th>';
	        		echo '</tr>';
	        	echo '</thead>';
	        	echo '<tbody>';

				foreach ($toutesLesPersonnes as $personne) {
	    			echo '<tr>';
			            $subscriber_email = $personne->subscriber_email;
			            $subscriber_status = $personne->subscriber_status;
			           	echo '<td>'.$subscriber_email.'</td>';
			           	if ($subscriber_status == 'B') {
			           		$statut = "Blacklisté";
			           	} else if ($subscriber_status == 'C') {
		           			$statut = "Confirmé";
			           	} else if ($subscriber_status == 'D') {
			           		$statut = "Désinscrit";
	           			} else if ($subscriber_status == 'N') {
							$statut = "Non-Confirmé";
       					} else if ($subscriber_status == 'T') {
							$statut = "Testeur";
			           	}
			           	echo '<td>'.$statut.'</td>';
		           		echo '<td><input type="checkbox" name="case[]" value="'.$subscriber_email.'"></td>';
		           	echo '</tr>';
	    		}
	    		echo '</tbody>';
	        echo '</table>';
	      echo '<input type="hidden" name="action6" value="save"/>';
	      echo '<input class="btn btn-default" type="submit" name="button" value="Passer les abonnés séléectionnés en blacklistés"/>';
		echo '</form>';
    }	
?>