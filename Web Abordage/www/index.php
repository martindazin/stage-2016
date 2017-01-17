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
function atm_mail_with_attachment($nom_societe, $nom_prenom, $telephone, $email, $message, $sujet){

  $message = '<p>Bonjour bande de pirates du Web !</p>
  <p>Une nouvelle personne à répondu au formulaire Web Abordage. Voici les informations transmises : </p>
  <p><strong>Sujet : </strong> ' . $sujet . '</p>
  <p><strong>Message :</strong> ' . $message .'</p><br/>
  <p><strong>Informations sur le contact</strong></p>
  <ul><li><strong>Nom de la société :</strong> ' . $nom_societe .'</li>
  <li><strong>Nom et prénom :</strong> ' . $nom_prenom .'</li>
  <li><strong>Telephone :</strong> ' . $telephone .'</li>
  <li><strong>Email :</strong> <a href="mailto:'.$email.'" title="Envoyer un mail">' . $email .'</a></li>
  </ul>';

  require 'library/php/PHPMailer/PHPMailerAutoload.php';
  $mail = new PHPMailer;
  $mail->CharSet = "UTF-8";
  $mail->setLanguage('fr', 'library/php/PHPMailer/language/');
  $mail->setFrom('no-reply@web-abordage.fr', 'Web Abordage');
  $mail->addAddress('web.abordage@atmospherecommunication.fr', 'Équipe Atmosphère');
  $mail->addBCC('ab@atm-com.fr');
  $mail->addBCC('stagiare.web@atmospherecommunication.fr');
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
        <meta property="og:image:width" content="212" />
        <meta property="og:image:height" content="212" />
        <meta property="og:site_name" content="Web Abordage - Création de site internet qualitatifs" />
        <link rel="image_src" href="http://www.web-abordage.fr/library/images/logo.png" />
        <link rel="apple-touch-icon" href="http://www.web-abordage.fr/library/images/apple-touch-icon.png">
        <link rel="icon" href="favicon.png">
        <link rel="stylesheet" href="http://www.web-abordage.fr/library/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="http://www.web-abordage.fr/library/js/libs/modernizr.custom.min.js"></script>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,300,700' rel='stylesheet' type='text/css'>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js" type="text/javascript"></script>
        <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-24767699-15', 'auto');
        ga('send', 'pageview');
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
            <a href="#" data-name="avantages" title="Les avantages">Les avantages</a>
            <a href="#" data-name="cout" title="Le coût">Le coût</a>
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
                    <img width="1600" height="429" src="http://www.web-abordage.fr/library/images/slide3.jpg" alt="Votre site internet entièrement administrable pour 499€ HT par mois pendant 3 mois"/>
                    <div class="flex-caption">
                      <!-- texte 1 -->
                      <div><span class="texte1">Votre site internet administrable</span></div>
                      <!-- texte 2 -->
                      <div><span class="texte2">Pour 499€ HT par mois pendant 3 mois</span></div>
                    </div>
                </li>
                <li>
                    <!-- image -->
                    <img width="1600" height="429" src="http://www.web-abordage.fr/library/images/slide1.jpg" alt="Votre site vitrine haut de gamme à moins de 1500€ HT"/>
                    <div class="flex-caption">
                      <!-- texte 1 -->
                      <div><span class="texte1">Votre site vitrine haut de gamme</span></div>
                      <!-- texte 2 -->
                      <div><span class="texte2">Et un conseiller à votre écoute</span></div>
                    </div>
                </li>
                <li>
                    <!-- image -->
                    <img width="1600" height="429" src="http://www.web-abordage.fr/library/images/slide2.jpg" alt="Votre site internet clé en main entièrement administrable et évolutif"/>
                    <div class="flex-caption">
                      <!-- texte 1 -->
                      <div><span class="texte1">Votre site Internet clé en main</span></div>
                      <!-- texte 2 -->
                      <div><span class="texte2">Entièrement administrable et évolutif</span></div>
                    </div>
                </li>
                <li>
                    <!-- image -->
                    <img width="1600" height="429" src="http://www.web-abordage.fr/library/images/slide3.jpg" alt="Votre site internet optimisé pour une bonne visibilité sur le web"/>
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
          // var_dump($_POST);
          // Récupération des variables
          $errors = array();
          $nom_societe = atm_sanitize($_POST['nom_societe']);
          $nom_prenom = atm_sanitize($_POST['nom_prenom']);
          $telephone = atm_sanitize($_POST['telephone']);
          $email = atm_sanitize($_POST['email']);
          $message = atm_sanitize($_POST['message']);
          $sujet = atm_sanitize($_POST['sujet']);

          // Vérification des variables
          if( !isset($nom_societe) || empty($nom_societe) || is_null($nom_societe) ){$errors["nom_societe"] = "Vous n'avez pas entré le nom de votre société ou de votre organisation.";}
          if( !isset($nom_prenom) || empty($nom_prenom) || is_null($nom_prenom) ){$errors["nom_prenom"] = "Vous n'avez pas entré votre prénom et votre nom.";}
          if( !isset($telephone) || empty($telephone) || is_null($telephone) ){$errors["telephone"] = "Vous n'avez pas entré votre numéro de téléphone.";}
          if( !isset($email) || empty($email) || is_null($email) || !isValidEmail($email)){$errors["email"] = "Vous n'avez pas entré votre email ou votre email n'est pas valide.";}
          if( !isset($message) || empty($message) || is_null($message) ){$errors["message"] = "Votre message est vide.";}

          if(empty($errors) && sizeof($errors) == 0):
            // envoi de mail         
            $mail_status = atm_mail_with_attachment($nom_societe, $nom_prenom, $telephone, $email, $message, $sujet);

            if($mail_status['success']):

              // Suivi Google Analytics
              echo "<script>
                    $(document).ready(function() {
                      ga('send', 'event', 'formulaireAbordageEnvoye', 'submit', {
                        hitCallback: function() {
                          // document.location = url;
                          document.location.href;
                        }
                      });
                    });
                    </script>"
              
        ?>
              
              <div class="valide">
                <p>Votre demande a bien été prise en compte ! Un conseiller Web Abordage vous rappellera sous 24H.</p>
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
            <span class="txt">Accompagnement par<br />un véritable conseiller</span>
          </div>
          <div class="right">
            <!-- image -->
            <img src="http://www.web-abordage.fr/library/images/ancre.png" alt="Web Abordage - 3 Paiements de 499€ HT pour une trésorerie préservée" width="78" height="78" />
            <!-- texte -->
            <span class="txt"><strong>3 Paiements de 499€ HT</strong> pour<br />une trésorerie préservée</span>
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
        <form method="post" action="" id="formulaireContact" enctype="multipart/form-data">
            <div class="step1">
              <h2>Trois thèmes professionnels</h2>
              <div>
                <label>
                  <span>Design</span>
                  <img width="400" height="235" src="http://www.web-abordage.fr/library/images/site1.png" alt="Template 1" />
                  <div class="liste_boutons">
                    <a target="_blank" href="http://atm-com.fr/abordage/" title="Voir le design 1">Voir le design 1</a>
                  </div>
                </label>
                <label>
                  <span>Fashion</span>
                  <img width="400" height="235" src="http://www.web-abordage.fr/library/images/site2.png" alt="Template 2" />
                  <div class="liste_boutons">
                    <a target="_blank" href="http://atm-com.fr/abordage-2/" title="Voir le design 2">Voir le design 2</a>
                  </div>
                </label>
                <label>
                  <span>Corporate</span>
                  <img width="400" height="235" src="http://www.web-abordage.fr/library/images/site3.png" alt="Template 3" />
                  <div class="liste_boutons">
                    <a target="_blank" href="http://atm-com.fr/abordage-3/" title="Voir le design 3">Voir le design 3</a>
                  </div>
                </label>
              </div>
            </div>
            
            <div class="step4">
              <div class="un_tiers">
                <h2>Un design <br />professionnel</h2>
                <p>Nous adaptons les typographies et le jeu de couleurs pour un site à votre image.</p>
                <p>Affichez fièrement votre logo sur un site professionnel.</p>
              </div>
              <div class="un_tiers">
                <h2>Un conseiller <br />à votre écoute</h2>
                <p>Pour valider avec vous votre demande,</p>
                <p>Lancer la création de votre site Internet,</p>
                <p>Convenir d'une date de formation à l'utilisation du site.</p>
              </div>
              <div class="un_tiers">
                <h2>Un site Internet<br />vivant</h2>
                <p>Vous ajoutez vous-même vos textes, photos, vidéos, etc...</p>
                <p>Vous validez pour la mise en ligne.</p>
                <p>Vous continuez de mettre votre site à jour.</p>
              </div>
            </div>
            <div class="step3">
              <h2>Intéressé ? Entrez en relation avec nos conseillers</h2>
              <div class="un_tiers">
                <label for="nomSociete">Société ou organisation</label>
                <input id="nomSociete" type="texte" name="nom_societe">
                <label for="nomPrenom">Prénom &amp; nom</label>
                <input id="nomPrenom" type="texte" name="nom_prenom">
              </div>
              <div class="un_tiers">
                <label for="telephone">Téléphone</label>
                <input id="telephone" type="telephone" name="telephone">
                <label for="email">Email</label>
                <input id="email" type="email" name="email">
              </div>
              <div class="un_tiers">
                <label for="sujet">Sujet</label>
                <input id="sujet" type="texte" name="sujet" value="">
                <label for="message">Message</label>
                <textarea id="message" name="message"></textarea>
              </div>
              <p>
                Vous préférez le téléphone ? Contactez nous au 02 41 20 59 74
              </p>
              <div class="bouton_envoi">
                <button type="submit">Envoyer</button>
              </div>
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
        /*! Logo Change */ $("#logo").change(function(){if(window.FileReader&&window.Blob){var e=$(this)[0].files,r=e[0],a=new FileReader;a.onloadend=function(e){for(var r=new Uint8Array(e.target.result).subarray(0,4),a="",o=0;o<r.length;o++)a+=r[o].toString(16);mimeType(a)?($(".logo").find(".alert-danger").remove(),$("#logo").removeClass("error")):$(".logo").find(".alert-danger").length||($("#logo").after('<div class="alert alert-danger"><p>Le format de ce fichier n\'est pas supporté !</p></div>'),$("#logo").addClass("error"))},a.readAsArrayBuffer(r),readURL(this)}});
        /*! Header Flexslider */ $(window).load(function(){$(".flex_home").flexslider({animation:"fade",slideshowSpeed:3500,controlNav:!1,directionNav:!1})});
        /*! Menu scrolling */ $(document).ready(function(){$(".menu > a").on("click",function(o){o.preventDefault();var t=$(this).data("name");console.log(t),"concept"==t?$("html, body").animate({scrollTop:$("section.bloc_bleu").offset().top},"slow"):"website"==t?$("html, body").animate({scrollTop:$("section.quatre_etapes").offset().top},"slow"):$("html, body").animate({scrollTop:$("section.trois_couleurs").offset().top},"slow")})});
        </script>
        <!-- script required -->
        <script type="text/javascript">
          $(document).ready(function(){
            $('#formulaireContact').on('submit', function(){
              $('.step3').find('.error').remove();
              //liste des messages d'erreurs
              var error_msg = "N'oubliez pas de remplir correctement la totalité du formulaire !";
              var er_nom = 'Merci de renseigner le nom de votre société';
              var er_np = 'Merci de renseigner votre nom &amp; prénom';
              var er_tel = 'Merci de vérifier votre numéro de téléphone';
              var er_mail = 'Merci de vérifier votre adresse email';
              var er_msg = 'N\'oubliez pas de nous en apprendre plus sur votre projet !';
              
              //verif infos
              // return true if all OK
              var er_infos_mid = '';
              var infos = false;
              var info_nom = $('input#nomSociete').val();
              var info_np = $('input#nomPrenom').val();
              var info_tel = $('input#telephone').val();
              var info_mail = $('input#email').val();
              var info_msg = $('input#message').val();
              console.log(info_msg);
                // verif nom
                if(info_msg == ''){
                  info_msg = false;
                  er_infos_mid += '<li>Votre message</li>';
                } else {
                  info_msg = true;
                }
                 // verif nom
                if(info_nom == ''){
                  info_nom = false;
                  er_infos_mid += '<li>Le nom de votre entreprise</li>';
                } else {
                  info_nom = true;
                }
                // verif np
                if(info_np == '') {
                  info_np = false;
                  er_infos_mid += '<li>Votre Nom / Prénom</li>';
                } else {
                  info_np = true;
                }
                // verif tel
                if(info_tel == ''){
                  info_tel = false;
                } else {
                  regex_phone = /^((\+|00)33\s?|0)[12345679](\s?\d{2}){4}$/;
                  var t = info_tel.match(regex_phone);
                  if(t != null){
                    info_tel = true;
                  }
                }
                if(info_tel == false){
                  er_infos_mid += '<li>Votre numéro de téléphone</li>';
                }
                // verif mail
                //return only true if OK, else non-OK
                if(info_mail == ''){
                  info_mail = false;
                } else if(!isEmail(info_mail)) {
                  info_mail = false;
                } else {
                  info_mail = true;
                }
                if(info_mail == false){
                  er_infos_mid += '<li>Votre adresse mail</li>';
                }
              if(info_nom == true && info_np == true && info_tel == true && info_mail == true && info_message == true){
                infos = true;
              }
              console.log("infos : " + infos);
              if(infos == false){
                //on crée le msg : 
                var er_infos_dbt = '<div class="error">Merci de vérifier : <ul>';
                var er_infos_fin ='</ul></div>';
                var end = er_infos_dbt + er_infos_mid + er_infos_fin;
                $('.step3').append(end);
              }
              // si tout est ok, on valide le formulaire
              if(infos != true){
                return false;
              }
            });
          });
        </script>
    </body>
</html>
