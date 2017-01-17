<?php

	// Fonction pour avoir un nom de domaine valide
	if (!function_exists('is_valid_domain_name')) {
	    function is_valid_domain_name($domain_name) {
	      	// Caractères valides
	        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*\.([a-zA-Z]{2,})$/i", $domain_name)
	        // Longueur du nom de domaine
	        && preg_match("/^.{1,253}$/", $domain_name)
	        // Longueur de chaque partie
	        && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name));
	    }
	}

	// Fonction pour avoir un nom d'utilisateur valide
	if (!function_exists('is_valid_user_name')) {
	    function is_valid_user_name($user_name) {
	    	// Caractères valides
	    	return (preg_match("#^[a-zA-ZàâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ ]+$#", $user_name));
	    }
	}

	// Fonction pour avoir une adresse mail valide
    if (!function_exists('is_valid_mail_adress')) {
     	function is_valid_mail_adress($adress_mail){
	        // Caractères valides
	        if (filter_var($adress_mail, FILTER_VALIDATE_EMAIL)) {
	          return true;
	      	} else {
	        return false;
	      	}
	    }
	}

	// Fonction de regex sur les couleurs hexadecimales
    if (!function_exists('is_valid_hexadecimal')) {
		function is_valid_hexadecimal($color) {
			return (preg_match("/#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})/", $color));
		}
	}
	
	// Fonction de regex sur les pages Facebook
	if (!function_exists('is_valid_facebook')) {
		function is_valid_facebook($url) {
			return (preg_match("/^https:\/\/www.facebook.com\/.+/", $url));
		}
	}

	// Fonction de regex sur les pages Twitter
	if (!function_exists('is_valid_twitter')) {
	    function is_valid_twitter($url) {
			return (preg_match("/^https:\/\/twitter.com\/.+/", $url));
		}
	}

	// Fonction permettant d'envoyer les uploads vers un dossier spécifique
	// Est associé à un add_filter() et un remove_filter()
	if (!function_exists('file_upload_dir')) {
		function file_upload_dir($dir) {
		    return array(
		        'path'   => $dir['basedir'].'/atm_newsletter_md',
		        'url'    => $dir['baseurl'].'/atm_newsletter_md',
		        'subdir' => '/atm_newsletter_md',
		    ) + $dir;
		}
	}

	



?>