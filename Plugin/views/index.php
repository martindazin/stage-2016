<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<style type="text/css">
</style>

<?php

  

  require ABSPATH."wp-content/plugins/atm_newsletter_md/PHPMailer/PHPMailerAutoload.php";

  // Fonctions PHP //
  include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

  // Requêtes SQL //
  include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');

  // Lecture du fichier de logs
  $filename = ABSPATH.'wp-content/plugins/atm_newsletter_md/tracking/pixel.log';
  $handle = @fopen($filename, "r");
  if ($handle) {
    // Le booléen $booleenEstDejaPresent permet de savoir si l'insertion que l'on veut faire en BDD existe déjà ou non
    $booleenEstDejaPresent = 0;

    while (($buffer = fgets($handle, 4096)) !== false) {
      $data = explode(',', $buffer);
      foreach ($toutesLesStats as $statistique) {
        $stat_date = $statistique->stat_date;
        // On teste si la stat est déjà présente en BDD
        if ($data[0] == $stat_date) {
          $booleenEstDejaPresent = 1;
        }
      }
      
      if ($booleenEstDejaPresent == 0) {
        $wpdb->insert($table_stats, array(
                            'stat_date' => date('Y-m-d H:i:s', strtotime($data[0])),
                            'stat_ip' => $data[1],
                            'newsletter_showed_id' => $data[2]
                          )
        );
      }
      $booleenEstDejaPresent = 0;
    }
    if (!feof($handle)) {
        echo "Erreur: fgets() a échoué\n";
    }
    fclose($handle);
  }

  // MAJ des statistiques
  foreach ($nombreLecturesParNlT as $statistique) {
    $newsletter_showed_id = $statistique->newsletter_showed_id;
    $nombreDeLecturesActuelles = $statistique->nombre;

    $nombreDeLecturesPassees = $wpdb->get_var("SELECT `newsletter_read_count` 
                                                  FROM ".$table_nl.
                                                  " WHERE `newsletter_showed_id` = ".$newsletter_showed_id.
                                                  " AND (`newsletter_status` = 'T' OR `newsletter_status` = 'E')");

    if ($nombreDeLecturesActuelles > $nombreDeLecturesPassees) {
      $wpdb->update($table_nl,
                    array('newsletter_read_count' => $nombreDeLecturesActuelles), 
                    array('newsletter_showed_id' => $newsletter_showed_id, 'newsletter_status' => 'T', 'newsletter_status' => 'E')
      );
    }
  }

  // Requête SQL où no a besoin d'avoir un résultat contenu dans ../others/requetes.php
  // Le contenu de la ligne de la dernière nl Tracking
  $ligneDerniereNl = $wpdb->get_row("SELECT *
                                      FROM ".$table_nl.
                                      " WHERE `newsletter_showed_id` LIKE ".$last_newsletter_showed_id.
                                      " AND (`newsletter_status` = 'T' OR `newsletter_status` = 'E')");

  // Tableaux permettants d'effectuer un comptage de stats
  $tableauTotalDeStats = [$totalEmailsLus, $totalEmailsCliques, $totalEmailsOuverts];
  $tableauStatsDerniereNl = [$ligneDerniereNl->newsletter_read_count, $ligneDerniereNl->newsletter_click_count, $ligneDerniereNl->newsletter_open_count];

  // Formulaire douple opt-in
  if(isset($_POST['action']) && !empty($_POST['action']) && !is_null($_POST['action']) && $_POST['action'] == 'save'){

    $serveur = array();
    $errors = array();
    $message1 = array();

    // Nettoyage des variables
    $serveur['email'] = stripcslashes(atm_sanitize($_POST['email']));

    if(!isset($serveur['email'])
        || empty($serveur['email'])
        || is_null($serveur['email'])
        || is_valid_mail_adress($serveur['email']) == false) {
      $errors["email"] = "<p>Vous devez écrire une <strong>adresse mail</strong> valide.</p>";
    }

    if (empty($errors)) {
      $booleenEstDejaPresent = 0;
      foreach ($toutesLesPersonnes as $personne) {
        $subscriber_email = $personne->subscriber_email;
        // On teste si l'adresse est déjà présente en BDD
        if ($_POST['email'] == $subscriber_email) {
          $booleenEstDejaPresent = 1;
        }
      }

      // Si l'adresse n'est pas présente en BDD
      if ($booleenEstDejaPresent == 0) {
        $adresseMailExpediteur = get_option('atm_nl_adresseexpediteur');
        
        $wpdb->insert($table_subs, array(
                            'subscriber_email' => $_POST['email'],
                            'subscriber_status' => 'N',
                          'subscriber_created' => current_time('mysql')
                          )
        );

        $message =
        '<html>
          <head>
              <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
          </head>
          <body>
              <div style="margin: 0;">
                  <table width="100%" height="" cellpadding="0" cellspacing="0" style="font-family: Arial, Helvetica, sans-serif; color: #000000; font-size: 12px; line-height: 18px; background-color:#FFFFFF;">
                      <tbody>
                          <tr>
                              <td align="center">
                                  <table border="0" width="600" cellspacing="0" cellpadding="0" align="center">
                                      <tbody>

                                          <!-- HEADER -->
                                          <tr>
                                              <td colspan="8" width="100%" style="padding: 15px 25px;" bgcolor="#FFFFFF"></td>
                                          </tr>
                                          <tr>
                                              <td colspan="8" width="100%" style="font-size: 25px; text-align: center;">Logo de l\'entreprise</td>
                                          </tr>
                                          <tr>
                                              <td colspan="8" width="100%" style="padding: 15px 25px;" bgcolor="#FFFFFF"></td>
                                          </tr>

                                          <!-- BODY -->
                                          <tr>
                                             <td colspan="8" width="100%" style="text-align: center; padding: 20px; font-family: Arial, Helvetica, sans-serif;">'.sprintf('Veuillez cliquer sur ce lien suivant pour confirmer votre inscription :<br>'
                                              .esc_url(home_url('/')).'wp-content/plugins/atm_newsletter_md/views/verifcontact.php?key=%s', $_POST['email']).'</td>
                                          </tr>
                                          <tr>
                                              <td colspan="8" width="100%" style="padding: 10px 25px;" bgcolor="#FFFFFF"></td>
                                          </tr>
                                          
                                      </tbody>
                                  </table>

                                  <table border="0" width="600" cellspacing="0" cellpadding="0" align="center">
                                      <tbody>
                                          <!-- FOOTER -->

                                          <tr>
                                              <td colspan="10" width="100%" style="font-size: 15px; text-align : center;" bgcolor="#FFFFFF">Rejoignez-nous !</td>
                                          </tr>
                                          <tr>
                                              <td colspan="10" width="100%" style="padding: 10px; text-align : center;">
                                                  <!-- RESEAUX SOCIAUX -->
                                                  RESEAUX SOCIAUX
                                              </td>
                                          </tr>
                                          <tr>
                                              <td colspan="10" width="100%" style="padding: 10px; text-align : center;">
                                                  <!-- PARTENAIRES -->
                                                  PARTENAIRES
                                              </td>
                                          </tr>
                                          <tr>
                                              <td colspan="10" width="100%" bgcolor="#FFFFFF" style="text-align: center;">
                                                  <!-- AUTRES CHOSES À METTRE EN AVANT PAR EXEMPLE : Mentions légales, Protection de la vie privée, Contact, etc. -->
                                                  MENTIONS LEGALES // PROTECTTION DE LA VIE PRIVEE // CONTACT // ETC..
                                              </td>
                                          </tr>
                                          <tr>
                                              <td colspan="10" width="100%" style="text-align: center;">
                                                  <!-- NOM DE L\'ENTREPRISE -->
                                                  &copy; NOM DE L\'ENTREPRISE
                                              </td>
                                          </tr>                                       
                                          <tr>
                                              <td colspan="10" width="100%" style="text-align: center;"><a href="http://www.atmospherecommunication.fr/"><img src="http://img15.hostingpics.net/thumbs/mini_216907atmlogo.png" alt="Atmosphère Communication" /></a></td>
                                          </tr>
                                      </tbody>
                                  </table>
                              </td>
                          </tr>
                      </tbody>
                  </table>
              </div>
          </body>
        </html>';

        $headers = 'From: '. $adresseMailExpediteur . "\r\n" .
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        mail($_POST['email'], "Confirmation de votre adresse email sur notre site", $message, $headers);
        

        echo "coucou ! ".'<br>';
      } else {
        echo "L'adresse mail saisie est déjà en BDD !";
      }
    }
  }


?>

<script type="text/javascript">

  var tableauTotalDeStats = ["<?php echo implode('", "', $tableauTotalDeStats); ?>"];
  var tableauStatsDerniereNl = ["<?php echo implode('", "', $tableauStatsDerniereNl); ?>"];

  google.charts.load("current", {packages:["corechart"]});

  google.charts.setOnLoadCallback(drawFirstChart);
  google.charts.setOnLoadCallback(drawSecondChart);

  function drawFirstChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Statistique');
    data.addColumn('number', 'Nombre');
    data.addRows([
      ['Emails Lus', parseInt(tableauTotalDeStats[0])],
      ['Lien(s) cliqué(s)', parseInt(tableauTotalDeStats[1])],
      ['Lien(s) ouvert(s)', parseInt(tableauTotalDeStats[2])]
    ]);

    var options = {
      title: 'Statistiques totales des Newsletters',
      width:500,
      height:400,
      colors: ['#33CC44', '#FF8800', '#0077FF'],
      pieHole: 0.4
    };

    var chart = new google.visualization.PieChart(document.getElementById('donutchart1'));
    chart.draw(data, options);
  }

  function drawSecondChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Statistique');
    data.addColumn('number', 'Nombre');
    data.addRows([
      ['Emails Lus', parseInt(tableauStatsDerniereNl[0])],
      ['Lien(s) cliqué(s)', parseInt(tableauStatsDerniereNl[1])],
      ['Lien(s) ouvert(s)', parseInt(tableauStatsDerniereNl[2])]
    ]);

    var options = {
      title: 'Statistiques de la dernière Newsletter',
      width:500,
      height:400,
      colors: ['#33CC44', '#FF8800', '#0077FF'],
      pieHole: 0.4
    };

    var chart = new google.visualization.PieChart(document.getElementById('donutchart2'));
    chart.draw(data, options);
  }

</script>


<table class="columns">
  <tr>
    <td><div id="donutchart1" style="border: 1px solid #ccc"></div></td>
    <td><div id="donutchart2" style="border: 1px solid #ccc"></div></td>
  </tr>
</table>


<?php
    if(isset($message) && !empty($message) && !is_null($message) && is_array($message)){echo '<div id="message" class="'.$message[0].' below-h2">'.$message[1].'</div>';}
?>
<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
  <div class="col-xs-12 col-md-3 col-lg-3">
        <label for="email">Adresse email à saisir :</label>
    </div>
    <div class="col-xs-12 col-md-3 col-lg-3">
      <input id="email" type="text" name="email"/>
    </div>
    <div class="col-xs-12 col-md-12 col-lg-12">
        <input type="hidden" name="action" value="save"/>
        <input class="btn btn-default" type="submit" value="Ajouter cet abonné"/>
  </div>
</form>
