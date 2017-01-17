>>>>> Dans header.php :

-> Créer une nouvelle balise <script></script>
   Et y mettre le code suivant :

<script>
	/*
	* Fonction de suivi des clics sur des liens sortants dans Analytics
	* Cette fonction utilise une chaîne d'URL valide comme argument et se sert de cette chaîne d'URL
	* comme libellé d'événement. Configurer la méthode de transport sur 'beacon' permet d'envoyer le clic
	* au moyen de 'navigator.sendBeacon' dans les navigateurs compatibles.
	*/
	var trackOutboundLink = function(url, nomEvenement) {
		/*
		* The ga function defined by the tracking code creates a JavaScript array ga.q
		* (if it doesn’t exist yet) and then pushes its arguments onto that array.
		* Once analytics.js is loaded, it processes the queued events and replaces ga by a completely different function,
		* so that ga.q is no longer defined. During page load at least two items are added to the queue,
		* one to create the tracker and another one to record the page view.
		* This means that ga.q is defined only if the tracking code has been executed, but analytics.js has not been loaded yet.
		* This means that it is possible to determine if Google Analytics is blocked simply by checking if ga.q is defined:
		*/
	  	if (ga.q) {
		    // Cas où Google Analytics est bloqué par des programmes tiers
		  	// tels que Ad Block, Ghostery, etc...
		    document.location = url;
	  	} else {
	  		// Cas où Google Analytics n'est pas bloqué
		    ga("send", "event", nomEvenement, "click", url, {"hitCallback":
	      	function () {
	        	document.location = url;
	  		}
	    });
	  }
	}
</script>




>>>>> Dans functions.php :

-> Dans la fonction : function create_blocs($array_contenus)
	-> Dans le switch case : case 'bouton'
	   Et y mettre le code suivant :

<?php
	case 'bouton':
		$texte = $element['btn_texte'];
		$lien = $element['btn_lien'];
		// $nomEvenement est le nom de l'événement qui apparaitra dans Google Analytics
		$nomEvenement = 'nomEvenement';
		// Ajouter l'attribut : onclick="trackOutboundLink(\''.$lien.'\', \''.$nomEvenement.'\'); return false;" dans la balise <a></a>
		$return .= '<a target="_blank" href="'.$lien.'" title="'.$texte.'" class="btn btn_rouge" onclick="trackOutboundLink(\''.$lien.'\', \''.$nomEvenement.'\'); return false;"><span>'.$texte.'</span><i class="icon-fleche_droite"></i></a>';
		break;
?>
