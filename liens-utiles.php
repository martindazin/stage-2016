Â, Ê, Î, Ô, Û, Ä, Ë, Ï, Ö, Ü, À, Æ, æ, Ç, É, È, Œ, œ, Ù

<!-- ATM BAC A SABLE 2 -->
URL : http://dev-atmospherecommunication.fr/test/
URL Admin : http://dev-atmospherecommunication.fr/test/wp-admin/

FTP
Host : 92.222.204.48
Login : devatmos
Pwd : jF21D3aSss3K
Port : 21
Répertoire : /test/

Mysql
Host : mysql.link
Name : devatmospherec
User : devatmospherec
Pwd : Qr1nGPWS
Prefixe WP : test_
phpMyAdmin : http://92.222.204.48/_mysqladmin

Accès WP
Identifiant : atm_admin
Mot de passe : atm_admin

wp_mail()
Host : mail.atmospherecommunication.fr
Port : 587
User : dev@atmospherecommunication.fr
Pwd : jxufrNfTZ22g
Authentification : Mot de passe
Sécurité : Aucune


<!-- ATM -->
Accès WP
Identifiant : admin
Mot de passe : 8G[x5ax#Y3M_

BDD
Website : atmospherecommunication.fr
User : atmospherecommun
PWd : vUX5epURFq37PYV6
Prefixe WP : wp_
phpMyAdmin : http://92.222.204.48/_mysqladmin

FTP
Host : ftp.atmospherecommunication.fr
Login : atmospherecomm
Pwd : Ud09UY87



<!-- WEB ABORDAGE -->
FTP host: ftp.web-abordage.fr 
FTP User : webabordob 
FTP Pwd : Y95urXMr68fz



<!-- SERVEUR SMTP ATM -->
Host : mail.atmospherecommunication.fr
Port : 587
User : dev@atmospherecommunication.fr
Pwd : jxufrNfTZ22g
Authentification : Mot de passe
Sécurité : Aucune



<!-- ADRESSE MAIL --> 
http://mail.atmospherecommunication.fr/
login : stagiaire.dev
mdp : m4rt1n!



<!-- MANTIS -->
URL : http://dev-atmospherecommunication.fr/mantis/login_page.php
Username : stage-dev-web
Password : test

# severity
FEATURE, 10
TRIVIAL, 20
TEXT, 30
TWEAK, 40
MINOR, 50
MAJOR, 60
CRASH, 70
CRITICAL, 75
BLOCK, 80



<!-- PLUGINS --> 

>>> Tutoriels sur les plugins

https://openclassrooms.com/courses/propulsez-votre-site-avec-wordpress/creer-des-plugins
-> Un plugin complet
https://premium.wpmudev.org/blog/wordpress-plugin-development-guide/
-> Adding Scripts And Styles
http://www.yaconiello.com/blog/how-to-write-wordpress-plugin/
-> wp-plugin-name.php


>>> Listes des fonctions utilisant les hooks et les filtres
http://codex.wordpress.org/Plugin_API

>>> php_mailer
https://codex.wordpress.org/Plugin_API/Action_Reference/phpmailer_init



<!-- CRON -->

>>> Tutoriel
http://slides.com/jonathanbuttigieg/wp-cron



<!-- ANALYTICS -->
https://developers.google.com/analytics/devguides/collection/analyticsjs/sending-hits#handling_timeouts
google url builder

http://www.developpez.net/forums/d1426687/php/bibliotheques-frameworks/extraire-donnees-google-analytics-cli/



<!-- CHARTS -->
>>> Termes de recherches : google charts with sql php
http://sophiedogg.com/creating-a-google-pie-chart-using-sql-data/
http://www.kometschuh.de/GoogleChartToolswithJSON.html


<!-- MAIL PHP /VS/ PHP MAILER /VS/ PHP MAILER BMH -->
http://stackoverflow.com/questions/5571806/bounced-mail-handling-in-php-any-up-to-date-solutions

>>> Git Hub + Exemples
https://github.com/PHPMailer/PHPMailer
http://www.nicolas-verhoye.com/envoyer-des-mails-avec-phpmailer.html
https://phpmailer.github.io/PHPMailer/classes/PHPMailer.html
?? http://docs.drh.net/greenarrow-engine/Raw-Injection/PHPMailer-Raw-Injection-Example

>>> Pouquoi PHP Mailer ne récupère pas les bounces !!!
http://stackoverflow.com/questions/9055168/handling-bounce-email-w-phpmailer
PHPmailer does not handle receiving emails. It's purely a library for allowing PHP to talk to an SMTP server for sending emails. It has absolutely no support whatsoever to act as a mail client (e.g. receiving).
PHPmailer has no way of knowing the email bounced, as the bounce occurs LONG after PHPmailer's handed the email off to the outgoing SMTP server. IN real world terms, PHPmailer takes your letter and walks down the block to drop it into a mailbox. The bounce occurs later, when the letter carrier brings the letter back with 'return to sender' stamped on it - PHPmailer is not involved in this at all.
Your options are:
1) Use PHP's imap functions to connect to an existing pop/imap server and retrieve emails that way
2) Use a .forward or similar redirect on the SMTP side to "send" incoming email to a PHP script.



>>> Meilleur exemple ?
http://www.formget.com/tracking-email/


>>> Pixel espion !
http://www.codexpedia.com/php/tracking-pixel-implementation-in-html-and-php/
http://stackoverflow.com/questions/13079666/developing-a-tracking-pixel

>>> Méthode de MailChimp
http://blog.mailchimp.com/ask-mailchimp-how-do-you-track-email-opens/

>>> Méthode VERP (Variable Envelope Return Path)
https://fr.wikipedia.org/wiki/M%C3%A9thode_de_l'adresse_de_retour_variable

>>> Autres
	>>> Tabs en JS http://www.w3schools.com/howto/howto_js_tabs.asp
		focus tab after refresh
http://php.net/manual/fr/function.ezmlm-hash.php

	>>> Datas aléatoires pour BDD http://www.mockaroo.com/
	>>> Double opt-in http://www.sitepoint.com/forums/showthread.php?632689-Guide-to-creating-a-double-opt-in-email-list
	>>> Exécution périodique http://matthieu.developpez.com/execution_periodique/
	>>> Limites pour l'envois d'emails http://stackoverflow.com/questions/18361233/gmail-sending-limits

Mon criètre de recherche est : "phpmailer"
				/var/www/dev-atmospherecommunication.fr/prod/test/wp-content/plugins/newsletter/plugin.php
				/var/www/dev-atmospherecommunication.fr/prod/test/wp-content/plugins/newsletter/emails/edit.php
				/var/www/dev-atmospherecommunication.fr/prod/test/wp-content/plugins/newsletter/main/diagnostic.php
				/var/www/dev-atmospherecommunication.fr/prod/test/wp-content/plugins/newsletter/main/main.php
				/var/www/dev-atmospherecommunication.fr/prod/test/wp-content/plugins/newsletter/main/smtp.php
				/var/www/dev-atmospherecommunication.fr/prod/test/wp-content/plugins/newsletter/main/languages/en_US.php
Mon compteur est égal à : 6


Mon criètre de recherche est : "->send("
				/var/www/dev-atmospherecommunication.fr/prod/test/wp-content/plugins/newsletter/plugin.php
				/var/www/dev-atmospherecommunication.fr/prod/test/wp-content/plugins/newsletter/emails/edit.php
				/var/www/dev-atmospherecommunication.fr/prod/test/wp-content/plugins/newsletter/main/diagnostic.php
Mon compteur est égal à : 3


mailer->IsHTML(false);
mailer->IsHTML(true);
mailer->Body = $message;
mailer->IsHTML(true);
mailer->Body = $message['html'];
mailer->IsHTML(false);
mailer->Body = $message['text'];
mailer->IsHTML(true);
mailer->Body = $message['html'];
mailer->AltBody = $message['text'];
mailer->Subject = $subject;
mailer->ClearCustomHeaders();
mailer->AddCustomHeader($key . ': ' . $value);
mailer->ClearAddresses();
mailer->AddAddress($to);
mailer->Send();
if (mailer->IsError()) {
mail_last_error = mailer->ErrorInfo;
logger->error('mail> ' . mailer->ErrorInfo);
mailer->IsSMTP();
mailer->Host = $smtp_options['host'];
mailer->Port = (int) $smtp_options['port'];
mailer->SMTPAuth = true;
mailer->Username = $smtp_options['user'];
mailer->Password = $smtp_options['pass'];
mailer->SMTPKeepAlive = true;
mailer->SMTPSecure = $smtp_options['secure'];
mailer->SMTPAutoTLS = false;
mailer->SMTPOptions = array(
mailer->IsMail();
mailer->Encoding = options['content_transfer_encoding'];
mailer->Encoding = 'base64';
mailer->CharSet = 'UTF-8';
mailer->From = options['sender_email'];
mailer->Sender = $return_path;
mailer->AddReplyTo(options['reply_to']);
mailer->FromName = options['sender_name'];
mailer->SmtpClose();


Il me reste à faire (à-peu-près) :
- La gestion du CRON.
- La gestion des statistiques (l'affichage de celles-ci c'est bon).
- Un peu de stockage de données en BDD.


/////
	$dir_iterator = new RecursiveDirectoryIterator(ABSPATH.'wp-content/plugins/newsletter');
	$iterator = new RecursiveIteratorIterator($dir_iterator);
	$compteur = 0;
	$critereDeRecherche = "get_admin_page_url(";
	echo "Mon critère de recherche est : ".'"'.$critereDeRecherche.'"'.'<br>';

	foreach ($iterator as $file) {		
		// Chercher par $critereDeRecherche
		if (!is_dir($file) && !strpos($file, "tiny_mce")){
			$handle = fopen($file, 'r');
			$affichageDuFichier = fread($handle, filesize($file));

			if (strpos($affichageDuFichier, $critereDeRecherche)) {
				echo $file.'<br>';
				$compteur++;
			}
			fclose($handle);
		}

		// Chercher "$this->mailer->" dans le fichier "/var/www/dev-atmospherecommunication.fr/prod/test/wp-content/plugins/newsletter/plugin.php"
		/*
		if (!is_dir($file) && !strpos($file, "tiny_mce")){

			$handle = fopen($file, 'r');
			$affichageDuFichier = fread($handle, filesize($file));
			fclose($handle);

			if ($file == "/var/www/dev-atmospherecommunication.fr/prod/test/wp-content/plugins/newsletter/plugin.php") {

				$tabfich=file($file); 
				for($i = 0 ; $i < count($tabfich) ; $i++) {

					if (strpos($tabfich[$i], '$this->mailer->')) {
						echo str_replace('$this->', "", $tabfich[$i]).'<br>';
						$compteur++;
					}
					
				}
			}
		}
		*/
	}
	echo "Mon compteur est égal à : ".$compteur.'<br>';



	
	function search_file ($folder_name, $criterionSearched_name) {
    $dir_iterator = new RecursiveDirectoryIterator($folder_name);
    $iterator = new RecursiveIteratorIterator($dir_iterator);
    $compteur = 0;
    echo "Mon critère de recherche est : ".'"'.$criterionSearched_name.'"'.'<br>';

    foreach ($iterator as $file) {
      var_dump($file);
      echo '<br>';
      // Chercher par $criterionSearched_name
      if (!is_dir($file) /*&& !strpos($file, $criterionSearched_name)*/){
        $handle = fopen($file, 'r');
        $affichageDuFichier = fread($handle, filesize($file));

        if (strpos($affichageDuFichier, $criterionSearched_name)) {
          // echo $file.'<br>';
          $compteur++;
        }
        fclose($handle);
      }
    }
  }

  function search_expression_in_file ($file_name, $criterionSearched_name) {
    $dir_iterator = new RecursiveDirectoryIterator($folder_name);
    $iterator = new RecursiveIteratorIterator($dir_iterator);
    $compteur = 0;
    // $critereDeRecherche = $criterionSearched_name;
    // echo "Mon critère de recherche est : ".'"'.$criterionSearched_name.'"'.'<br>';

    foreach ($iterator as $file) {

      // Chercher "$this->mailer->" dans le fichier "/var/www/dev-atmospherecommunication.fr/prod/test/wp-content/plugins/newsletter/plugin.php"
      
      if (!is_dir($file) && !strpos($file, "tiny_mce")){

        $handle = fopen($file, 'r');
        $affichageDuFichier = fread($handle, filesize($file));
        fclose($handle);

        if ($file == $file_name) {

          $tabfich=file($file); 
          for($i = 0 ; $i < count($tabfich) ; $i++) {

            if (strpos($tabfich[$i], '$this->mailer->')) {
              echo str_replace('$this->', "", $tabfich[$i]).'<br>';
              $compteur++;
            }
            
          }
        }
      }
    }

  }

  search_file(ABSPATH.'wp-content/plugins/atm_newsletter_md', "subs");

  // search_expression_in_file(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/index.php', "idDerniereNl");



<?php
	$hote = get_option('atm_nlnomdomaine');
	$nomDUtilisateur = get_option('atm_nlnomutilisateur');
	$motDePasse = get_option('atm_nlmotdepasse');
	$adresseMailExpediteur = get_option('atm_nladresseexpediteur');
	$nomDuDestinaire = get_option('atm_nlnomexpediteur');

	$mail = new PHPMailer();
	$mail->IsSMTP();

	$mail->CharSet = "UTF-8"; 

	// $mail->SMTPDebug = 4;
	// $mail->Debugoutput = "html";

 	$mail->setLanguage("fr");

	$mail->Host = $hote;
	$mail->SMTPAuth = true;
	$mail->Username = $nomDUtilisateur;
	$mail->Password = $motDePasse;
	$mail->Port = $port;
	$mail->SMTPKeepAlive = true;

	$mail->setFrom($adresseMailExpediteur, $nomDuDestinaire);

	$mail->AddAddress("mart.49@hotmail.fr");

	
	for($i=0; $i<=10; $i++){
	    $date = date("H:i:s m/d/Y");
	    $mail->Subject  = "$date";

	    $mail->Body = "Test $i of PHPMailer.";

	    if(!$mail->Send()){
	       echo "Error sending: " . $mail->ErrorInfo;
	       break;
	    }else{
	       echo "$i. E-mail sent => $date<BR>";
	       sleep(2);
	       continue;
	    }
	}
?>