<?php
error_reporting(-1);

header('Content-Type: text/html; charset=utf-8');

/**
 *
 *  Fonction de validation d'emails
 *
 *  @param String $email
 *  @return Boolean
 *
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 *
 *  Fonction de nettoyage des variables
 *
 *  @param String | Array $input
 *  @return String | Array $output
 *
 */
function atm_sanitize($input) {
  if (is_array($input)) {
    foreach($input as $var=>$val) {
      $output[$var] = atm_sanitize($val);
    }
  }else {
    $search = array(
    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
    );
   
    $output = preg_replace($search, '', $input);
    $output = strip_tags($output, '<br>');
    $output = trim($output);
    $output = htmlspecialchars($output);
  }
  return $output;
}


/**
 *
 *  Fonction d'envoi d'email avec pièce joite
 *
 */
function atm_mail_with_attachment($nom_societe, $nom_prenom, $telephone, $email, $message, $template, $color1, $color2, $typo, $no_idea_for_colors, $no_idea_for_typo, $no_idea_for_logo){

  $message = '<p>Bonjour bande de pirates du Web !</p>
  <p>Une nouvelle personne à répondu au formulaire Web Abordage. Voici les informations transmises : </p>
  <p><strong>Informations sur le contact</strong></p>
  <ul><li><strong>Nom de la société :</strong> ' . $nom_societe .'</li>
  <li><strong>Nom et prénom :</strong> ' . $nom_prenom .'</li>
  <li><strong>Telephone :</strong> ' . $telephone .'</li>
  <li><strong>Email :</strong> <a href="mailto:'.$email.'" title="Envoyer un mail">' . $email .'</a></li>
  <li><strong>Message :</strong> ' . $message .'</li></ul><br/>
  <p><strong>Informations sur le site web</strong></p>
  <ul><li><strong>Choix du template :</strong> ' . $template . '</li>';
  if(!isset($no_idea_for_colors) || empty($no_idea_for_colors) || is_null($no_idea_for_colors)){
    $message .= '<li><strong>Choix de la couleur 1 :</strong> #'.$color1.'&nbsp;<span style="display:inline-block;position:relative;width:80px;height:20px;background-color:#'.$color1.'"></span></li>
                <li><strong>Choix de la couleur 2 :</strong> #'.$color2.'&nbsp;<span style="display:inline-block;position:relative;width:80px;height:20px;background-color:#'.$color2.'"></span></li>';
  }else{
    $message .= '<li><strong>Le client n\'a pas d\'idée de couleurs pour son site.</strong></li>';
  }
  if(!isset($no_idea_for_typo) || empty($no_idea_for_typo) || is_null($no_idea_for_typo)){
    $message .= '<li><strong>Choix de la typo :</strong> ' . $typo .' </li>';
  }else{
    $message .= '<li><strong>Le client n\'a pas d\'idée pour la typographie de son site.</strong></li>';
  }
  if(!isset($no_idea_for_logo) || empty($no_idea_for_logo) || is_null($no_idea_for_logo)){
    $message .= '<li>Le logo se trouve en <strong>pièce jointe</strong>.</li>';
  }else{
    $message .= '<li><strong>Le client n\'a pas fourni de logo !</strong></li>';
  }
  $message .= '</ul>';

  if(!isset($no_idea_for_logo) || empty($no_idea_for_logo) || is_null($no_idea_for_logo)){
    $file_ext = mb_strtolower(end(explode('.',$_FILES['logo']['name'])), 'UTF-8');

    if (array_key_exists('logo', $_FILES)) {
      // Undefined | Multiple Files | $_FILES Corruption Attack
      // If this request falls under any of them, treat it invalid.
      if (
          !isset($_FILES['logo']['error']) ||
          is_array($_FILES['logo']['error'])
      ) {
          return array('success' => false, 'msg' => 'Paramètres invalides.');exit;
      }

      // Check $_FILES['logo']['error'] value.
      switch ($_FILES['logo']['error']) {
          case UPLOAD_ERR_OK:
              break;
          case UPLOAD_ERR_NO_FILE:
              return array('success' => false, 'msg' => 'Pas de fichier envoyé.');exit;
          case UPLOAD_ERR_INI_SIZE:
          case UPLOAD_ERR_FORM_SIZE:
             return array('success' => false, 'msg' => 'Limite de poids de fichier dépassée.');exit;
          default:
              return array('success' => false, 'msg' => 'Erreues inconnues.');exit;
      }

      // You should also check filesize here -> 5Mo
      if ($_FILES['logo']['size'] > 5242880) {
          return array('success' => false, 'msg' => 'Limite de poids de fichier dépassée > 5 Mo');exit;
      }

      // DO NOT TRUST $_FILES['logo']['mime'] VALUE !!
      // Check MIME Type by yourself.
      $finfo = new finfo(FILEINFO_MIME_TYPE);
      if (false === $ext = array_search(
          $finfo->file($_FILES['logo']['tmp_name']),
          array(
              'eps' => 'application/octet-stream',
              'ai' => 'application/pdf',
              'psd' => 'image/vnd.adobe.photoshop',
              'jpg' => 'image/jpeg',
              'jpeg' => 'image/jpeg',
              'png' => 'image/png',
              'gif' => 'image/gif',
              'tiff' => 'image/tiff',
              'bmp' => 'image/bmp',
              'pdf' => 'application/pdf',
          ),
          true
      )) {
          return array('success' => false, 'msg' => 'Format de fichier invalide ('.$finfo->file($_FILES['logo']['tmp_name']).').');exit;
      }

      if($file_ext == 'pdf' && $ext == 'ai'){$ext = 'pdf';}

      $uploadfile = tempnam(sys_get_temp_dir(), sha1($_FILES['logo']['name'])).'.'.$ext;
      if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadfile)) {
          // Upload handled successfully
          // Now create a message
          // This should be somewhere in your include_path
          require 'library/php/PHPMailer/PHPMailerAutoload.php';
          $mail = new PHPMailer;
          $mail->CharSet = "UTF-8";
          $mail->setLanguage('fr', 'library/php/PHPMailer/language/');
          $mail->setFrom('no-reply@web-abordage.fr', 'Web Abordage');
          $mail->addAddress('web.abordage@atmospherecommunication.fr', 'Équipe Atmosphère');
          $mail->addBCC('dev@atmospherecommunication.fr');
          $mail->Subject = 'Nouveau contact sur Web Abordage';
          $mail->msgHTML($message);
          // Attach the uploaded file
          $mail->addAttachment($uploadfile, 'web-abordage-logo-'.mb_strtolower($nom_societe, 'UTF-8').'--'.date('Ymd').'.'.$ext);
          if (!$mail->send()) {
              $msg .= "PHPMailer Erreur: " . $mail->ErrorInfo;
              return array('success' => false, 'msg' => $msg);exit;
          } else {
              $msg .= "Message envoyé !";
              return array('success' => true, 'msg' => $msg);exit;
          }
      } else {
          $msg .= 'Erreur lors du transfert du fichier ' . $uploadfile;
          return array('success' => false, 'msg' => $msg);exit;
      }
    }
  }else{
    require 'library/php/PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->CharSet = "UTF-8";
    $mail->setLanguage('fr', 'library/php/PHPMailer/language/');
    $mail->setFrom('no-reply@web-abordage.fr', 'Web Abordage');
    $mail->addAddress('web.abordage@atmospherecommunication.fr', 'Équipe Atmosphère');
    $mail->addBCC('dev@atmospherecommunication.fr');
    $mail->Subject = 'Nouveau contact sur Web Abordage';
    $mail->msgHTML($message);
    if (!$mail->send()) {
        $msg .= "PHPMailer Erreur: " . $mail->ErrorInfo;
        return array('success' => false, 'msg' => $msg);exit;
    } else {
        $msg .= "Message envoyé !";
        return array('success' => true, 'msg' => $msg);exit;
    }
  }
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html prefix="og: http://ogp.me/ns#" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html prefix="og: http://ogp.me/ns#" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html prefix="og: http://ogp.me/ns#" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html prefix="og: http://ogp.me/ns#" class="no-js" lang="fr"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Web Abordage - Création de site internet qualitatifs</title>
        <meta name="description" content="Un site vitrine moderne, entièrement administrable, utilisant Wordpress, compatible smartphones et tablettes à moins de 1500€ HT. En savoir plus.">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="format-detection" content="telephone=no">
        <meta property="og:title" content="Web Abordage - Création de site internet qualitatifs" />
        <meta property="og:description" content="Un site vitrine moderne, entièrement administrable, utilisant Wordpress, compatible smartphones et tablettes à moins de 1500€ HT. En savoir plus.">
        <meta property="og:type" content="website" />
        <meta property="og:url" content="<?php echo 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]; ?>" />
        <meta property="og:image" content="http://www.web-abordage.fr/library/images/logo.png" />
        <meta property="og:site_name" content="Web Abordage - Création de site internet qualitatifs" />
        <link rel="image_src" href="http://www.web-abordage.fr/library/images/logo.png" />
        <link rel="apple-touch-icon" href="http://www.web-abordage.fr/library/images/apple-touch-icon.png">
        <link rel="icon" href="favicon.png">
        <link rel="stylesheet" href="http://www.web-abordage.fr/library/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="http://www.web-abordage.fr/library/js/libs/modernizr.custom.min.js"></script>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,300,700' rel='stylesheet' type='text/css'>
        <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-24767699-15', 'auto');
        ga('send', 'pageview');
        </script>
        <script>

          window.addEventListener('load', function()
          {
            if(window.ga && ga.create) 
            {
              console.log('Google Analytics is loaded');

              // var img = document.createElement('img');
              // img.setAttribute('style','display:none;');
              // img.src = chaineFinale+'/collect.php?tid=UA-24767699-14&ec=Allowing&ea=Google%20Analytics';
              // document.body.appendChild(img);    
            }
            else 
            {
              console.log('Google Analytics is not loaded');

              // var img = document.createElement('img');
              // img.setAttribute('style','display:none;');
              // img.src = chaineFinale+'/collect.php?tid=UA-24767699-14&ec=Blocking&ea=Google%20Analytics';
              // document.body.appendChild(img);
            }
          }, false);

        </script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <div class="contain">
      <header class="clearfix header_generique">
        <div class="top">
           <!-- menu gauche -->
           <div class="menu left">
            <a href="#" data-name="concept" title="Le Concept">Le Concept</a>
            <a href="#" data-name="website" title="Votre site web">Votre site web</a>
           </div>
           <!-- menu droit -->
           <div class="menu right">
            <a href="#" data-name="cout" title="Le coût">Le coût</a>
            <a href="#" data-name="avantages" title="Les avantages">Les avantages</a>
           </div>
       </div>
       <!-- logo -->
       <div class="logo">
        <img src="http://www.web-abordage.fr/library/images/logo.png" alt="description de l'image" width="212" height="212" />
       </div>
       <div class="flexslider flex_home">
           <ul class="slides">
                <li>
                    <!-- image -->
                    <img width="1600" height="429" src="http://www.web-abordage.fr/library/images/slide1.jpg" alt="Titre de l'image"/>
                    <div class="flex-caption">
                      <!-- texte 1 -->
                      <div><span class="texte1">Votre site vitrine haut de gamme</span></div>
                      <!-- texte 2 -->
                      <div><span class="texte2">À moins de 1500€ HT</span></div>
                    </div>
                </li>
                <li>
                    <!-- image -->
                    <img width="1600" height="429" src="http://www.web-abordage.fr/library/images/slide2.jpg" alt="Titre de l'image"/>
                    <div class="flex-caption">
                      <!-- texte 1 -->
                      <div><span class="texte1">Votre site internet clé en main</span></div>
                      <!-- texte 2 -->
                      <div><span class="texte2">Entièrement administrable et évolutif</span></div>
                    </div>
                </li>
                <li>
                    <!-- image -->
                    <img width="1600" height="429" src="http://www.web-abordage.fr/library/images/slide3.jpg" alt="Titre de l'image"/>
                    <div class="flex-caption">
                      <!-- texte 1 -->
                      <div><span class="texte1">Votre site internet optimisé</span></div>
                      <!-- texte 2 -->
                      <div><span class="texte2">Pour une bonne visibilité sur le web</span></div>
                    </div>
                </li>
           </ul>
       </div>
      </header>

      <!-- bloc si formulaire validé -->
      <?php if(isset($_POST['submit_x']) && !empty($_POST['submit_x']) && isset($_POST['submit_y']) && !empty($_POST['submit_y']) ): ?>
      <section class="validation">
        <?php
          // Récupération des variables
          $errors = array();
          $nom_societe = atm_sanitize($_POST['nom_societe']);
          $nom_prenom = atm_sanitize($_POST['nom_prenom']);
          $telephone = atm_sanitize($_POST['telephone']);
          $email = atm_sanitize($_POST['email']);
          $message = atm_sanitize($_POST['message']);
          $template = atm_sanitize($_POST['template']);
          $color1 = atm_sanitize($_POST['color1']);
          $color2 = atm_sanitize($_POST['color2']);
          $typo = atm_sanitize($_POST['typo']);

          $no_idea_for_colors = intval(atm_sanitize($_POST['no_idea_for_colors']));
          $no_idea_for_typo = intval(atm_sanitize($_POST['no_idea_for_typo']));
          $no_idea_for_logo = intval(atm_sanitize($_POST['no_idea_for_logo']));

          // Vérification des variables
          if( !isset($nom_societe) || empty($nom_societe) || is_null($nom_societe) ){$errors["nom_societe"] = "Vous n'avez pas entré le nom de votre société.";}
          if( !isset($nom_prenom) || empty($nom_prenom) || is_null($nom_prenom) ){$errors["nom_prenom"] = "Vous n'avez pas entré votre nom et votre prénom.";}
          if( !isset($telephone) || empty($telephone) || is_null($telephone) ){$errors["telephone"] = "Vous n'avez pas entré votre numéro de téléphone.";}
          if( !isset($email) || empty($email) || is_null($email) || !isValidEmail($email)){$errors["email"] = "Vous n'avez pas entré votre email ou votre email n'est pas valide.";}
          if( !isset($message) || empty($message) || is_null($message) ){$errors["message"] = "Votre message est vide.";}
          if( !isset($template) || empty($template) || is_null($template) ){$errors["template"] = "Vous n'avez choisi votre thème.";}
          if( empty($no_idea_for_colors) && (!isset($color1) || empty($color1) || is_null($color1)) ){$errors["color1"] = "Vous n'avez pas choisi votre première couleur.";}
          if( empty($no_idea_for_colors) && (!isset($color2) || empty($color2) || is_null($color2)) ){$errors["color2"] = "Vous n'avez pas choisi votre deuxième couleur.";}
          if( empty($no_idea_for_typo) && (!isset($typo) || empty($typo) || is_null($typo)) ){$errors["typo"] = "Vous n'avez pas choisi de typographie.";}

          if(empty($errors) && sizeof($errors) == 0):
            // envoi de mail         
            $mail_status = atm_mail_with_attachment($nom_societe, $nom_prenom, $telephone, $email, $message, $template, $color1, $color2, $typo, $no_idea_for_colors, $no_idea_for_typo, $no_idea_for_logo);
            if($mail_status['success']):
        ?>
              <div class="valide">
                <p>Votre demande a bien été prise en compte ! Un mail a été envoyé automatiquement à notre service commercial qui vous recontactera dans les plus brefs délais !</p>
              </div>
            <?php else: ?>
              <div class="erreur">
                <p>Une erreur s'est produite.</p>
                <p><?php echo $mail_status['msg']; ?></p>
              </div>          
           <?php endif; ?>
        <?php else: ?>
          <div class="erreur">
            <p>Votre demande est incomplète.</p>
            <p><?php echo implode('<br/>', $errors); ?></p>
          </div>
        <?php endif; ?>
      </section>
      <?php endif; ?>

      <!-- bloc bleu -->
      <section class="bloc_bleu">
          <div class="left">
            <!-- image -->
            <img src="http://www.web-abordage.fr/library/images/boue.png" alt="Web Abordage - Accompagnement par un veritable conseiller" width="78" height="78" />
            <!-- texte -->
            <span class="txt">Accompagnement par<br />un veritable conseiller</span>
          </div>
          <div class="right">
            <!-- image -->
            <img src="http://www.web-abordage.fr/library/images/ancre.png" alt="Web Abordage - 3 Paiements de 499€ HT pour une trésorerie préservée" width="78" height="78" />
            <!-- texte -->
            <span class="txt">3 Paiements de 499€ HT pour<br />une trésorerie préservée</span>
          </div>
      </section>

      <!-- bloc vert -->
      <section class="bloc_vert">
        <div>
            <!-- image -->
            <img src="http://www.web-abordage.fr/library/images/illu1.png" alt="Web Abordage - Optimisé pour le référencement naturel" width="226" height="216" />
            <!-- texte -->
            <p>Design moderne<span>Optimisé pour le référencement naturel</span></p>
        </div>
        <div>
            <!-- image -->
            <img src="http://www.web-abordage.fr/library/images/illu2.png" alt="Web Abordage - Entièrement Administrable" width="275" height="192" />
            <!-- texte -->
            <p>Entièrement Administrable<span>(Pages, Articles, photos, vidéos, etc.)</span></p>
        </div>
        <div>
            <!-- image -->
            <img src="http://www.web-abordage.fr/library/images/illu3.png" alt="Web Abordage - Compatible Smartphones / Tablettes" width="186" height="194" />
            <!-- texte -->
            <p>Compatible<br />Smartphones / Tablettes</p>
        </div>
        <div>
            <!-- image -->
            <img src="http://www.web-abordage.fr/library/images/illu4.png" alt="Web Abordage - Utilise Wordpress le numéro 1 des logiciels libres" width="302" height="187" />
            <!-- texte -->
            <p>Utilise Wordpress<span>Le numéro 1 des logiciels libres</span></p>
        </div>
      </section>

      <!-- 4 etapes -->
      <section class="quatre_etapes">
        <form method="post" action=""  enctype="multipart/form-data">
          <div class="titre">
            <h2 class="main_h2">Un site web en 4 étapes</h2>
          </div>
            <div class="step1">
              <span class="step_number">1</span>
              <h2>Choisissez parmi trois thèmes professionnels</h2>
              <div>
                <label>
                  <input data-value="t1" required type="radio" name="template" value="Template Design" class="hidden" />
                  <img width="400" height="235" src="http://www.web-abordage.fr/library/images/site1.png" alt="Template 1" />
                  <span>Design</span>
                  <div class="liste_boutons">
                    <a target="_blank" href="http://atm-com.fr/abordage/" title="Voir le design 1">Voir le design 1</a>
                    <button data-value="t1">Choisir le thème Design</button>
                  </div>
                </label>
                <label>
                  <input data-value="t2" required type="radio" name="template" value="Template Fashion" class="hidden" />
                  <img width="400" height="235" src="http://www.web-abordage.fr/library/images/site2.png" alt="Template 2" />
                  <span>Fashion</span>
                  <div class="liste_boutons">
                    <a target="_blank" href="http://atm-com.fr/abordage-2/" title="Voir le design 2">Voir le design 2</a>
                    <button data-value="t2">Choisir le thème Fashion</button>
                  </div>
                </label>
                <label>
                  <input data-value="t3" required type="radio" name="template" value="Template Corporate" class="hidden" />
                  <img width="400" height="235" src="http://www.web-abordage.fr/library/images/site3.png" alt="Template 3" />
                  <span>Corporate</span>
                  <div class="liste_boutons">
                    <a target="_blank" href="http://atm-com.fr/abordage-3/" title="Voir le design 3">Voir le design 3</a>
                    <button data-value="t3">Choisir le thème Corporate</button>
                  </div>
                </label>
              </div>
            </div>
            <div class="step2">
              <span class="step_number">2</span>
              <h2>Orientez-nous dans un choix de deux couleurs et d'une typographie</h2>
              <div class="couleur">
                <div class="left">
                  <h4>Première couleur</h4>
                  <input required type="hidden" name="color1" value="" />
                  <div class="color color1" title="Cliquez pour sélectionner votre couleur..." style="background-color: #45b29d"></div>
                </div>
                <div class="right">
                  <h4>Deuxième couleur</h4>
                  <input required type="hidden" name="color2" value="" />
                  <div class="color color2" title="Cliquez pour sélectionner votre couleur..." style="background-color: #f06363"></div>
                </div>
                <div class="no-idea-wrapper">
                  <label><input type="checkbox" name="no_idea_for_colors" value="1"><span>Je n'ai pas d'idée concernant les couleurs</span></label>
                </div>
              </div>
              <div class="typo clearfix">
                <h4>Typographie</h4>
                  <div class="typographie-wrapper">
                    <label class="typo1"><input required type="radio" name="typo" value="Open Sans" ><div class="img"></div></label>
                    <label class="typo2"><input required type="radio" name="typo" value="Merriweather" ><div class="img"></div></label>
                    <label class="typo3"><input required type="radio" name="typo" value="Roboto Condensed" ><div class="img"></div></label>
                    <label class="typo4"><input required type="radio" name="typo" value="Crison Text" ><div class="img"></div></label>
                  </div>
                  <div class="no-idea-wrapper">
                  <label><input type="checkbox" name="no_idea_for_typo" value="1"><span>Je n'ai pas d'idée concernant la typographie</span></label>
                </div>
              </div>
              <div class="logo">
                <h4>Chargez votre logo</h4>
                <div class="meta-infos-logo">
                  <p>Formats supportés : .eps, .ai, .psd, .jpg, .jpeg, .png, .tiff, .gif, .bmp et .pdf</p>
                  <p>Taille maximum du fichier : <strong>5 Mo</strong></p>
                </div>
                <input type="file" required name="logo" id="logo" />
                <img id="imagePreview" src="#" alt="Aperçu de votre logo ici" />
              </div>
              <div class="no-idea-wrapper">
                <label><input type="checkbox" name="no_idea_for_logo" value="1"><span>Je n'ai pas de logo</span></label>
              </div>
            </div>
            <div class="step3">
              <span class="step_number">3</span>
              <h2>À propos de vous et de votre entreprise</h2>
              <div class="un_tiers">
                <label>Nom de votre société</label>
                <input required type="texte" name="nom_societe">
                <label>Nom et prénom</label>
                <input required type="texte" name="nom_prenom">
              </div>
              <div class="un_tiers">
                <label>Téléphone</label>
                <input required type="telephone" name="telephone">
                <label>Email</label>
                <input required type="email" name="email">
              </div>
              <div class="un_tiers">
                <label>Message</label>
                <textarea required name="message"></textarea>
              </div>
            </div>
            <div class="step4">
              <span class="step_number">4</span>
              <div class="left">
                <h2>Un conseiller Web Abordage<br />vous rappelle sous 24H</h2>
                <p>Pour valider avec vous votre demande,</p>
                <p>Lancer la création de votre site Internet,</p>
                <p>Convenir d'une date de formation à l'utilisation du site.</p>
              </div>
              <div class="right">
                <h2>Une fois formé à l'utilisation<br />du site par un conseiller :</h2>
                <p>Vous ajoutez vous-même vos textes, photos, vidéos, etc...</p>
                <p>Vous validez pour la mise en ligne.</p>
                <p>Vous continuez de mettre votre site à jour.</p>
              </div>
            </div>
            <div class="bouton_envoi">
              <input type="image" src="http://www.web-abordage.fr/library/images/bouton.png" name="submit">
            </div>
          </form>
      </section>


      <!-- bloc 3 couleurs -->
      <section class="trois_couleurs clearfix">
        <div class="left">
          <div class="top">
            <h2>Combien ça coûte ?</h2>
            <p><strong>499€ HT</strong> à validation de la commande puis deux versements de 499€ HT, le tout échelonné sur 3 mois pour un coût total de <strong>1497€ HT</strong> la première année.</p>
            <p><strong>149€ HT</strong> d'hébergement et de maintenance les années suivantes.</p>
          </div>
          <div class="bot">
            <h2>Vos conseillers</h2>
            <!-- 4 images -->
            <div>
              <img width="150" height="165" src="http://www.web-abordage.fr/library/images/Equipe-01.png" alt="Vincent" />
              <img width="150" height="165" src="http://www.web-abordage.fr/library/images/Equipe-02.png" alt="Anne Cécile" />
              <img width="150" height="165" src="http://www.web-abordage.fr/library/images/Equipe-03.png" alt="Yann" />
              <img width="150" height="165" src="http://www.web-abordage.fr/library/images/Equipe-04.png" alt="Pierre" />
            </div>
          </div>
        </div>
        <div class="right">
          <h2>Les avantages</h2>
          <ul>
            <li>Un site <strong>entièrement administrable</strong> et évolutif : modifiez à tout moment vos pages, articles, photos, vidéos, onglets du menu ou encarts de contenu sur l'accueil et en pied-de-page.</li>
            <li>Un site qui utilise <strong>Wordpress</strong>, le n°1 des outils de gestion de contenus libre.</li>
            <li>Un site optimisé pour le <strong>référencement naturel</strong> (SEO).</li>
            <li>Un site <strong>responsive</strong>, donc compatible avec les smartphones et les tablettes</li>
            <li>Un <strong>conseiller disponible</strong> pour vous épauler lors du démarrage.</li>
            <li>Une <strong>formation à l'exploitation</strong> du site par téléphone ou dans nos locaux (Angers ou Paris).</li>
            <li>Une <strong>trésorerie préservée</strong> grâce à un coût global lissé sur 3 mois.</li>
            <li>Pas de coût mensuel de location sur plusieurs années,<br /> seulement le coût annuel d'hébergement et de maintenance.</li>
            <li><strong>Fabrication française</strong>, par une équipe de professionnels basée à Angers (49).</li>
          </ul>
        </div>
      </section>

      <footer class="footer_generique">
          <!-- logo atmosphere -->
          <a href=" http://www.atmospherecommunication.fr" title="Atmosph&egrave;re, agence de communication &agrave; Angers et Le Mans: print, web, &eacute;tudes, conseil, strat&eacute;gie" target="_blank" class="icon-atmos"><img width="17" height="17" src="http://www.web-abordage.fr/library/images/atmosphere.png" alt="Atmosphere Communication" /></a>
          <p>Web Abordage est un produit <a href="http://atmospherecommunication.fr" title="Atmosphere Communication">Atmosphère Communication</a></p>
      </footer>
    </div> <!-- end of .contain -->
        <script src="http://www.web-abordage.fr/library/js/libs/jquery-1.12.0.min.js"></script>
        <script>window.jQuery || document.write('<script src="http://www.web-abordage.fr/library/js/libs/jquery-1.12.0.min.js"><\/script>')</script>
        <script type="text/javascript" src="http://www.web-abordage.fr/library/js/libs/jquery.flexslider-min.js"></script>
        <script src="http://www.web-abordage.fr/library/js/libs/colpick.min.js"></script>
        <script>
        /*! Check whether an object is Array or not */ var isArray=function(){if(Array.isArray)return Array.isArray;var r=Object.prototype.toString,t=r.call([]);return function(a){return r.call(a)===t}}();
        /*! Object elements count */ Object.size=function(r){var n,e=0;for(n in r)r.hasOwnProperty(n)&&e++;return e};
        /*! Check email validity */ function isEmail(w){return/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(w)}
        /*! Retourn MIME type */ function mimeType(e){var s=e.slice(0,4);if("424d"==s)type="image/bmp",success=!0;else switch(e){case"89504e47":type="image/png",success=!0;break;case"47494638":type="image/gif",success=!0;break;case"ffd8ffe0":case"ffd8ffe1":case"ffd8ffe2":type="image/jpeg",success=!0;break;case"49492A00":case"4D4D002A":type="image/tiff",success=!0;break;case"47494638":type="image/gif",success=!0;break;case"38425053":type="application/octet-stream",success=!0;break;case"25504446":type="application/pdf",success=!0;break;case"c5d0d3c6":type="application/postscript",success=!0;break;default:type="unknown",success=!1}return success}
        /*! Read Url */ function readURL(e){if(e.files&&e.files[0]){var a=new FileReader;a.onload=function(e){$("#imagePreview").attr("src",e.target.result)},a.readAsDataURL(e.files[0])}}
        /*! Logo Change */ $("#logo").change(function(){if(window.FileReader&&window.Blob){var e=$(this)[0].files,r=e[0],a=new FileReader;a.onloadend=function(e){for(var r=new Uint8Array(e.target.result).subarray(0,4),a="",o=0;o<r.length;o++)a+=r[o].toString(16);mimeType(a)?($(".logo").find(".alert-danger").remove(),$("#logo").removeClass("error")):$(".logo").find(".alert-danger").length||($("#logo").after('<div class="alert alert-danger"><p>Le format de ce fichier n\'est pas supporté !</p></div>'),$("#logo").addClass("error"))},a.readAsArrayBuffer(r),readURL(this)}});
        /*! Color picker */ $(".color1").colpick({layout:"full",color:"45b29d",submit:0,onChange:function(o,c,l,n,u){$(".color1").css("background-color","#"+c),u||$('input[name="color1"]').val(c)}}).keyup(function(){$(this).colpickSetColor(this.value)}),$(".color2").colpick({layout:"full",color:"f06363",submit:0,onChange:function(o,c,l,n,u){$(".color2").css("background-color","#"+c),$('input[name="color2"]').val(c)}}).keyup(function(){$(this).colpickSetColor(this.value)});
        /*! Header Flexslider */ $(window).load(function(){$(".flex_home").flexslider({animation:"fade",controlNav:!1,directionNav:!1})});
        /*! Menu scrolling */ $(document).ready(function(){$(".menu > a").on("click",function(o){o.preventDefault();var t=$(this).data("name");console.log(t),"concept"==t?$("html, body").animate({scrollTop:$("section.bloc_bleu").offset().top},"slow"):"website"==t?$("html, body").animate({scrollTop:$("section.quatre_etapes").offset().top},"slow"):$("html, body").animate({scrollTop:$("section.trois_couleurs").offset().top},"slow")})});
        /*! Check form before send */ $(".quatre_etapes").on("submit","form",function(e){$(".quatre_etapes").find(".alert").remove();var r={},t=$('input[name="nom_societe"]').val();""==t&&(r.nom_societe="Vous devez renseigner votre <strong>société</strong>.");var o=$('input[name="nom_prenom"]').val();""==o&&(r.nom_prenom="Vous devez renseigner votre <strong>nom et votre prénom</strong>.");var a=$('input[name="telephone"]').val();""==a&&(r.telephone="Vous devez renseigner votre <strong>numéro de telephone</strong>.");var n=$('input[name="email"]').val();""!=n&&isEmail(n)||(r.email="Vous n'avez pas renseigné votre <strong>email</strong> ou votre email n'est pas valide.");var s=$('textarea[name="message"]').val();""==s&&(r.message="Vous <strong>message</strong> est vide.");var i=!1;$('input[name="template"').each(function(){return $(this).prop("checked")?(i=!0,!1):void 0}),i||(r.template="Vous n'avez pas choisi votre <strong>thème</strong>.");var u=!1;$('input[name="typo"]')[0].hasAttribute("required")?($('input[name="typo"').each(function(){return $(this).prop("checked")?(u=!0,!1):void 0}),u||(r.typo="Vous n'avez pas choisi votre <strong>typographie</strong>.")):u=!0;var p=$('input[name="color1"]').val();""==p&&$('input[name="color1"]')[0].hasAttribute("required")&&(r.color1="Vous n'avez pas choisi votre <strong>première couleur</strong>.");var l=$('input[name="color2"]').val();""==l&&$('input[name="color2"]')[0].hasAttribute("required")&&(r.color2="Vous n'avez pas choisi votre <strong>deuxième couleur</strong>.");var v=$('input[name="logo"]').val();""==v&&$('input[name="logo"]')[0].hasAttribute("required")&&(r.logo="Vous n'avez pas sélectionné votre <strong>logo</strong>."),$("#logo").hasClass("error")&&(r.logo="Le format de ce fichier <strong>n'est pas supporté</strong>.");var m=Object.size(r);return m>0?($.each(r,function(e,r){console.log("Erreur : "+e+": "+r),"message"==e?$(".quatre_etapes").find('textarea[name="'+e+'"]').first().addClass("champ-erreur").after('<div class="alert alert-danger"><p>'+r+"</p></div>"):$(".quatre_etapes").find('input[name="'+e+'"]').first().addClass("champ-erreur").after('<div class="alert alert-danger"><p>'+r+"</p></div>")}),$("html, body").animate({scrollTop:$(".quatre_etapes").find(".alert-danger").first().offset().top},1e3),!1):void 0});
        /*! User doesn't know what he wants */ $(".no-idea-wrapper").on("change","input",function(o){var t=$(this),e=t.attr("name");"no_idea_for_colors"==e?t.prop("checked")?($(".couleur").find(".color1").stop().slideUp("fast",function(){$('input[name="color1"]').attr("required",!1)}),$(".couleur").find(".color2").stop().slideUp("fast",function(){$('input[name="color2"]').attr("required",!1)})):($(".couleur").find(".color1").stop().slideDown("fast",function(){$('input[name="color1"]').attr("required",!0)}),$(".couleur").find(".color2").stop().slideDown("fast",function(){$('input[name="color2"]').attr("required",!0)})):"no_idea_for_typo"==e?t.prop("checked")?$(".typographie-wrapper").stop().slideUp("fast",function(){$('input[name="typo"]').attr("required",!1)}):$(".typographie-wrapper").stop().slideDown("fast",function(){$('input[name="typo"]').attr("required",!0)}):"no_idea_for_logo"==e&&(t.prop("checked")?($(".meta-infos-logo").stop().slideUp("fast"),$("#logo").stop().slideUp("fast"),$("#imagePreview").stop().slideUp("fast",function(){$('input[name="logo"]').attr("required",!1)})):($(".meta-infos-logo").stop().slideDown("fast"),$("#logo").stop().slideDown("fast"),$("#imagePreview").stop().slideDown("fast",function(){$('input[name="logo"]').attr("required",!0)})))});
        /*! Theme select input emulator */ $(".step1").on("click","button",function(t){t.preventDefault();var a=$(this),e=a.attr("data-value");$('input[data-value="'+e+'"]').prop("checked",!0)});
        </script>
    </body>
</html>
