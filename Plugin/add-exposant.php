<?php
ob_start();
/*
 *
 *
 *  ADMIN "AJOUTER UN EXPOSANT"
 *
 *
 *
 */
?>
<?php
if(!function_exists('set_html_content_type')){
  function set_html_content_type() {return 'text/html';}
}

/*
 *
 * Suppression d'un exposant
 *
 */
if(isset($_GET['atm_action']) && !empty($_GET['atm_action']) && !is_null($_GET['atm_action']) && $_GET['atm_action'] == 'delete'){
  // Verify that the nonce is valid.
  if ( ! wp_verify_nonce( $_POST['delete-nonce'], 'delete-nonce' ) && $_SERVER['HTTP_REFERER'] == '/wp-admin/admin.php?page=atm_cpt_add_exposant' && is_admin()) {
    $message[] = 'error';
    $message[] = '<p>Erreur de sécurité. Vous n\'êtes pas autorisé à effectuer cette action.</p>';
  }else{  
    if(isset($_GET['exposant']) && !empty($_GET['exposant']) && !is_null($_GET['exposant'])){
      global $wpdb;
      $table_utilisateur = $wpdb->prefix.'ins_utilisateur';
      $table_utilisateur_exposant = $wpdb->prefix.'ins_utilisateur_exposant';
      $table_utilisateur_contact = $wpdb->prefix.'ins_utilisateur_exposant_contact';
      $id_utilisateur = intval(atm_sanitize($_GET['exposant']));
      $wpdb->delete($table_utilisateur, array('id_utilisateur' => $id_utilisateur));
      $id_utilisateur_exposant = intval($wpdb->get_var("SELECT id_utilisateur_exposant FROM $table_utilisateur_exposant WHERE id_utilisateur = $id_utilisateur"));
      $wpdb->delete($table_utilisateur_exposant, array('id_utilisateur' => $id_utilisateur));
      $wpdb->delete($table_utilisateur_contact, array('id_utilisateur_exposant' => $id_utilisateur_exposant));
      echo '<script type="text/javascript">window.location = "' .home_url().'/wp-admin/admin.php?page=atm_cpt_exposants"</script>';
      exit;
    }else{
      echo '<script type="text/javascript">window.location = "' .home_url().'/wp-admin/admin.php?page=atm_cpt_exposants"</script>';
      exit;
    }
  }
}


/*
 *
 * Récupérer et sauvegarder les informations de la metabox pour les exposants
 *
 */
if( isset($_POST['atm_action']) && !empty($_POST['atm_action']) && !is_null($_POST['atm_action']) && isset($_POST['atm_add_exposant_nonce']) && !empty($_POST['atm_add_exposant_nonce']) && !is_null($_POST['atm_add_exposant_nonce']) ){
  // Check if our nonce is set.
  global $wpdb, $HOPITECH_TEMPLATE_EMAIL_TOP, $HOPITECH_TEMPLATE_EMAIL_BOTTOM;
  $table_ins_utilisateur = $wpdb->prefix.'ins_utilisateur';
  $table_ins_utilisateur_exposant = $wpdb->prefix.'ins_utilisateur_exposant';
  $table_ins_utilisateur_exposant_contact = $wpdb->prefix.'ins_utilisateur_exposant_contact';

  // Verify that the nonce is valid.
  if ( ! wp_verify_nonce( $_POST['atm_add_exposant_nonce'], 'atm_add_exposant_nonce' ) && isset($_POST['_wp_http_referer']) && !empty($_POST['_wp_http_referer']) && $_POST['_wp_http_referer'] == '/wp-admin/admin.php?page=atm_cpt_add_exposant' && is_admin()) {
    $message[] = 'error';
    $message[] = '<p>Erreur de sécurité lors de la tentative d\'enregistrement.</p>';
  }else{  
    // Récupération et sanitisation des variables
    $errors = array(); 
    $exposant = array();
    $exposant_responsable = array();
    $exposant_facturation = array();
    $exposant_correspondant = array();
    $utilisateur = array();
    
    $annee = date('Y');

    $id_utilisateur = intval(atm_sanitize($_POST['id_utilisateur']));
    $id_utilisateur_exposant = intval(atm_sanitize($_POST['id_utilisateur_exposant']));

    // Récupération des données de l'entreprise
    $exposant['str_raisonsociale']          = stripslashes(atm_sanitize($_POST['str_raisonsociale']));
    $exposant['str_adresse']                = stripslashes(atm_sanitize($_POST['str_adresse']));
    $exposant['str_codepostal']             = stripslashes(atm_sanitize($_POST['str_codepostal']));
    $exposant['str_ville']                  = stripslashes(atm_sanitize($_POST['str_ville']));
    $exposant['str_pays']                   = stripslashes(atm_sanitize($_POST['str_pays']));
    $exposant['str_tvaintra']               = stripslashes(preg_replace('#[^a-zA-Z0-9]#','',atm_sanitize($_POST['str_tvaintra'])));
    $exposant['str_ref_facture']            = stripslashes(atm_sanitize($_POST['str_ref_facture']));
    $exposant['txt_presentation']           = stripslashes(atm_sanitize($_POST['txt_presentation']));
    $exposant['str_fixe']                   = stripslashes(preg_replace('#[^0-9+\(\)]#','',atm_sanitize($_POST['str_fixe'])));
    $exposant['str_mobile']                 = stripslashes(preg_replace('#[^0-9+\(\)]#','',atm_sanitize($_POST['str_mobile'])));
    $exposant['str_fax']                    = stripslashes(preg_replace('#[^0-9+\(\)]#','',atm_sanitize($_POST['str_fax'])));
    $exposant['str_email']                  = stripslashes(atm_sanitize($_POST['str_email']));
    $exposant['str_site']                   = stripslashes(str_replace(array('http://','https://'),'',atm_sanitize($_POST['str_site'])));
    $exposant['id_secteur_activite']        = intval(atm_sanitize($_POST['id_secteur_activite']));
    $exposant['str_secteur_activite_autre'] = stripslashes(atm_sanitize($_POST['str_secteur_activite_autre']));
    $exposant['int_annee_participation']    = intval(atm_sanitize($_POST['int_annee_participation']));

    // Récupération des données du responsable de la participation
    $bl_responsable_identique = intval(atm_sanitize($_POST['bl_responsable_identique']));
    $exposant_responsable['str_nom']      = stripslashes(atm_sanitize($_POST['str_r_nom']));
    $exposant_responsable['str_prenom']   = stripslashes(atm_sanitize($_POST['str_r_prenom']));
    $exposant_responsable['str_email']    = stripslashes(atm_sanitize($_POST['str_r_email']));
    $exposant_responsable['str_fixe']     = stripslashes(preg_replace('#[^0-9+\(\)]#','',atm_sanitize($_POST['str_r_fixe'])));
    $exposant_responsable['str_mobile']   = stripslashes(preg_replace('#[^0-9+\(\)]#','',atm_sanitize($_POST['str_r_mobile'])));
    $exposant_responsable['str_fonction'] = stripslashes(atm_sanitize($_POST['str_r_fonction']));
    $exposant_responsable['id_utilisateur_exposant_contact_type'] = 1;

    // Récupération des données du contact de facturation
    $bl_facturation_identique = intval(atm_sanitize($_POST['bl_facturation_identique']));
    $exposant_facturation['str_nom']                    = stripslashes(atm_sanitize($_POST['str_f_nom']));
    $exposant_facturation['str_prenom']                 = stripslashes(atm_sanitize($_POST['str_f_prenom']));
    $exposant_facturation['str_facture_raisonsociale']  = stripslashes(atm_sanitize($_POST['str_f_raisonsociale']));
    $exposant_facturation['str_facture_reference']      = stripslashes(atm_sanitize($_POST['str_f_reference']));
    $exposant_facturation['str_email']                  = stripslashes(atm_sanitize($_POST['str_f_email']));
    $exposant_facturation['str_fixe']                   = stripslashes(preg_replace('#[^0-9+\(\)]#','',atm_sanitize($_POST['str_f_fixe'])));
    $exposant_facturation['str_mobile']                 = stripslashes(preg_replace('#[^0-9+\(\)]#','',atm_sanitize($_POST['str_f_mobile'])));
    $exposant_facturation['str_fax']                    = stripslashes(preg_replace('#[^0-9+\(\)]#','',atm_sanitize($_POST['str_f_fax'])));
    $exposant_facturation['str_facture_adresse']        = stripslashes(atm_sanitize($_POST['str_f_adresse']));
    $exposant_facturation['str_facture_codepostal']     = stripslashes(atm_sanitize($_POST['str_f_codepostal']));
    $exposant_facturation['str_facture_ville']          = stripslashes(atm_sanitize($_POST['str_f_ville']));
    $exposant_facturation['str_facture_pays']           = stripslashes(atm_sanitize($_POST['str_f_pays']));
    $exposant_facturation['id_utilisateur_exposant_contact_type'] = 2;

    // Récupération des données du correspondant sur place
    $bl_correspondant_identique = intval(atm_sanitize($_POST['bl_correspondant_identique']));
    $exposant_correspondant['str_nom']      = stripslashes(atm_sanitize($_POST['str_c_nom']));
    $exposant_correspondant['str_prenom']   = stripslashes(atm_sanitize($_POST['str_c_prenom']));
    $exposant_correspondant['str_email']    = stripslashes(atm_sanitize($_POST['str_c_email']));
    $exposant_correspondant['str_fixe']     = stripslashes(preg_replace('#[^0-9+\(\)]#','',atm_sanitize($_POST['str_c_fixe'])));
    $exposant_correspondant['str_mobile']   = stripslashes(preg_replace('#[^0-9+\(\)]#','',atm_sanitize($_POST['str_c_mobile'])));
    $exposant_correspondant['str_fonction'] = stripslashes(atm_sanitize($_POST['str_c_fonction']));
    $exposant_correspondant['id_utilisateur_exposant_contact_type'] = 3;

    // Récupération des données de l'utilisateur
    $utilisateur['str_nom']      = stripslashes(atm_sanitize($_POST['str_u_nom']));
    $utilisateur['str_prenom']   = stripslashes(atm_sanitize($_POST['str_u_prenom']));
    $utilisateur['str_email']    = stripslashes(atm_sanitize($_POST['str_u_email']));
    $utilisateur['str_fixe']     = stripslashes(preg_replace('#[^0-9+\(\)]#','',atm_sanitize($_POST['str_u_fixe'])));
    $utilisateur['str_password'] = stripslashes(atm_sanitize($_POST['str_u_password']));
        
    // Vérification des variables obligatoires et sensibles
    if( !isset($exposant['str_raisonsociale']) || empty($exposant['str_raisonsociale']) || is_null($exposant['str_raisonsociale']) ){$errors["str_raisonsociale"] = "<p>Vous devez entrer la <strong>raison sociale</strong> de l'entreprise de l'exposant.</p>";}
    if( !isset($exposant['str_adresse']) || empty($exposant['str_adresse']) || is_null($exposant['str_adresse']) ){$errors["str_adresse"] = "<p>Vous devez entrer l'<strong>adresse</strong> de l'entreprise de l'exposant.</p>";}
    if( !isset($exposant['str_codepostal']) || empty($exposant['str_codepostal']) || is_null($exposant['str_codepostal']) ){$errors["str_codepostal"] = "<p>Vous devez indiquer le <strong>code postal</strong> de l'entreprise de l'exposant.</p>";}
    if( !isset($exposant['str_ville']) || empty($exposant['str_ville']) || is_null($exposant['str_ville']) ){$errors["str_ville"] = "<p>Vous devez indiquer la <strong>ville</strong> de l'entreprise de l'exposant.</p>";}
    if( !isset($exposant['str_pays']) || empty($exposant['str_pays']) || is_null($exposant['str_pays']) ){$errors["str_pays"] = "<p>Vous devez indiquer le <strong>pays</strong> de l'entreprise de l'exposant.</p>";}
    if( !isset($exposant['str_tvaintra']) || empty($exposant['str_tvaintra']) || is_null($exposant['str_tvaintra']) ){$errors["str_tvaintra"] = "<p>Vous devez indiquer la <strong>TVA</strong> de l'entreprise de l'exposant.</p>";}
    if( !isset($exposant['txt_presentation']) || empty($exposant['txt_presentation']) || is_null($exposant['txt_presentation']) ){$errors["txt_presentation"] = "<p>Vous devez entrer la <strong>présentation</strong> de l'entreprise de l'exposant.</p>";}
    if( !isset($exposant['str_fixe']) || empty($exposant['str_fixe']) || is_null($exposant['str_fixe']) ){$errors["str_fixe"] = "<p>Vous devez indiquer le <strong>téléphone fixe</strong> de l'entreprise de l'exposant.</p>";}
    if( !isset($exposant['str_email']) || empty($exposant['str_email']) || is_null($exposant['str_email']) && isValidEmail($exposant['str_email']) ){$errors["str_email"] = "<p><strong>Email</strong> de l'entreprise de l'exposant non renseigné ou format de l'<strong>email</strong> invalide.</p>";}
    if( !isset($exposant['str_site']) || empty($exposant['str_site']) || is_null($exposant['str_site']) ){$errors["str_site"] = "<p>Vous devez indiquer l'<strong>url du site web</strong> de l'entreprise de l'exposant.</p>";}
    if( !isset($exposant['id_secteur_activite']) || empty($exposant['id_secteur_activite']) || is_null($exposant['id_secteur_activite']) ){$errors["id_secteur_activite"] = "<p>Vous devez indiquer le <strong>secteur d'activité</strong> de l'entreprise de l'exposant.</p>";}
    if( !isset($exposant['int_annee_participation']) || empty($exposant['int_annee_participation']) || is_null($exposant['int_annee_participation']) ){$errors["int_annee_participation"] = "<p>Vous devez indiquer l'<strong>année de première participation au salon HOPITECH</strong> de l'entreprise de l'exposant.</p>";}
    
    if(empty($bl_responsable_identique)){
      if( !isset($exposant_responsable['str_nom']) || empty($exposant_responsable['str_nom']) || is_null($exposant_responsable['str_nom']) ){$errors["str_r_nom"] = "<p>Vous devez indiquer le <strong>nom</strong> du responsable.</p>";}
      if( !isset($exposant_responsable['str_prenom']) || empty($exposant_responsable['str_prenom']) || is_null($exposant_responsable['str_prenom']) ){$errors["str_r_prenom"] = "<p>Vous devez indiquer le <strong>prénom</strong> du responsable.</p>";}
      if( !isset($exposant_responsable['str_email']) || empty($exposant_responsable['str_email']) || is_null($exposant_responsable['str_email']) && isValidEmail($exposant_responsable['str_email'])){$errors["str_r_email"] = "<p><strong>Email</strong> du responsable non renseigné ou format de l'<strong>email</strong> invalide.</p>";}
      if( !isset($exposant_responsable['str_fixe']) || empty($exposant_responsable['str_fixe']) || is_null($exposant_responsable['str_fixe']) ){$errors["str_r_fixe"] = "<p>Vous devez indiquer le <strong>téléphone fixe</strong> du responsable.</p>";}
      if( !isset($exposant_responsable['str_fonction']) || empty($exposant_responsable['str_fonction']) || is_null($exposant_responsable['str_fonction']) ){$errors["str_r_fonction"] = "<p>Vous devez indiquer la <strong>fonction</strong> du responsable.</p>";}
    }

    if(empty($bl_facturation_identique)){
      if( !isset($exposant_facturation['str_facture_raisonsociale']) || empty($exposant_facturation['str_facture_raisonsociale']) || is_null($exposant_facturation['str_facture_raisonsociale']) ){$errors["str_f_raisonsociale"] = "<p>Vous devez indiquer la <strong>raison sociale</strong> pour la facturation.</p>";}
      //if( !isset($exposant_facturation['str_facture_reference']) || empty($exposant_facturation['str_facture_reference']) || is_null($exposant_facturation['str_facture_reference']) ){$errors["str_f_reference"] = "<p>Vous devez indiquer une <strong>référence</strong> pour la facture.</p>";}
      if( !isset($exposant_facturation['str_email']) || empty($exposant_facturation['str_email']) || is_null($exposant_facturation['str_email']) && isValidEmail($exposant_facturation['str_email'])){$errors["str_f_email"] = "<p><strong>Email</strong> de facturation non renseigné ou format de l'<strong>email</strong> invalide.</p>";}
      if( !isset($exposant_facturation['str_fixe']) || empty($exposant_facturation['str_fixe']) || is_null($exposant_facturation['str_fixe']) ){$errors["str_f_fixe"] = "<p>Vous devez indiquer le <strong>téléphone fixe</strong> pour la facturation.</p>";}
      if( !isset($exposant_facturation['str_facture_adresse']) || empty($exposant_facturation['str_facture_adresse']) || is_null($exposant_facturation['str_facture_adresse']) ){$errors["str_f_adresse"] = "<p>Vous devez indiquer l'<strong>adresse</strong> de facturation.</p>";}
      if( !isset($exposant_facturation['str_facture_codepostal']) || empty($exposant_facturation['str_facture_codepostal']) || is_null($exposant_facturation['str_facture_codepostal']) ){$errors["str_f_codepostal"] = "<p>Vous devez indiquer le <strong>code postal</strong> de facturation.</p>";}
      if( !isset($exposant_facturation['str_facture_ville']) || empty($exposant_facturation['str_facture_ville']) || is_null($exposant_facturation['str_facture_ville']) ){$errors["str_f_ville"] = "<p>Vous devez indiquer la <strong>ville</strong> de facturation.</p>";}
      if( !isset($exposant_facturation['str_facture_pays']) || empty($exposant_facturation['str_facture_pays']) || is_null($exposant_facturation['str_facture_pays']) ){$errors["str_f_pays"] = "<p>Vous devez indiquer le <strong>pays</strong> de facturation.</p>";}
    }else{
      $exposant_facturation['str_facture_raisonsociale']  = $exposant['str_raisonsociale'];
      $exposant_facturation['str_facture_reference']      = $exposant['str_ref_facture'];
      $exposant_facturation['str_email']                  = $exposant['str_email'];
      $exposant_facturation['str_fixe']                   = $exposant['str_fixe'];
      $exposant_facturation['str_facture_adresse']        = $exposant['str_adresse'];
      $exposant_facturation['str_facture_codepostal']     = $exposant['str_codepostal'];
      $exposant_facturation['str_facture_ville']          = $exposant['str_ville'];
      $exposant_facturation['str_facture_pays']           = $exposant['str_pays'];
    }

    if(empty($bl_correspondant_identique)){
      if( !isset($exposant_correspondant['str_nom']) || empty($exposant_correspondant['str_nom']) || is_null($exposant_correspondant['str_nom']) ){$errors["str_c_nom"] = "<p>Vous devez indiquer le <strong>nom</strong> du correspondant sur place.</p>";}
      if( !isset($exposant_correspondant['str_prenom']) || empty($exposant_correspondant['str_prenom']) || is_null($exposant_correspondant['str_prenom']) ){$errors["str_c_prenom"] = "<p>Vous devez indiquer le <strong>prénom</strong> du correspondant sur place.</p>";}
      if( !isset($exposant_correspondant['str_email']) || empty($exposant_correspondant['str_email']) || is_null($exposant_correspondant['str_email']) && isValidEmail($exposant_correspondant['str_email'])){$errors["str_c_email"] = "<p><strong>Email</strong> du correspondant sur place non renseigné ou format de l'<strong>email</strong> invalide.</p>";}
      if( !isset($exposant_correspondant['str_fixe']) || empty($exposant_correspondant['str_fixe']) || is_null($exposant_correspondant['str_fixe']) ){$errors["str_c_fixe"] = "<p>Vous devez indiquer le <strong>téléphone fixe</strong> du correspondant sur place.</p>";}
      if( !isset($exposant_correspondant['str_fonction']) || empty($exposant_correspondant['str_fonction']) || is_null($exposant_correspondant['str_fonction']) ){$str_c_fixe["str_c_fonction"] = "<p>Vous devez indiquer la <strong>fonction</strong> du correspondant sur place.</p>";}
    }

    if( !isset($utilisateur['str_nom']) || empty($utilisateur['str_nom']) || is_null($utilisateur['str_nom']) ){$errors["str_u_nom"] = "<p>Vous devez indiquer le <strong>nom</strong> de l'utilisateur.</p>";}
    if( !isset($utilisateur['str_prenom']) || empty($utilisateur['str_prenom']) || is_null($utilisateur['str_prenom']) ){$errors["str_u_prenom"] = "<p>Vous devez indiquer le <strong>prénom</strong> de l'utilisateur.</p>";}
    if( !isset($utilisateur['str_email']) || empty($utilisateur['str_email']) || is_null($utilisateur['str_email']) && isValidEmail($utilisateur['str_email'])){$errors["str_u_email"] = "<p><strong>Email</strong> de l'utilisateur non renseigné ou format de l'<strong>email</strong> invalide.</p>";}
    if( !isset($utilisateur['str_fixe']) || empty($utilisateur['str_fixe']) || is_null($utilisateur['str_fixe']) ){$errors["str_u_fixe"] = "<p>Vous devez indiquer le <strong>téléphone fixe</strong> de l'utilisateur.</p>";}
    //if( !isset($utilisateur['str_password']) || empty($utilisateur['str_password']) || is_null($utilisateur['str_password']) ){$errors["str_u_password"] = "<p>Vous devez indiquer le <strong>mot de passe</strong> de l'utilisateur.</p>";}

    // On vérifie si un utilisateur avec cet email existe déjà
    if(isset($id_utilisateur) && !empty($id_utilisateur) && !is_null($id_utilisateur)){
      $is_already_utilisateur = intval($wpdb->get_var("SELECT id_utilisateur FROM $table_ins_utilisateur WHERE str_email = '".$utilisateur['str_email']."' AND id_utilisateur <> $id_utilisateur"));
      if(!empty($is_already_utilisateur)){
        $errors["str_email"] = "<p>Un utilisateur avec cette adresse email est déjà enregistré sur le site !</p>";
      }
    }else{
      $is_already_utilisateur = intval($wpdb->get_var("SELECT id_utilisateur FROM $table_ins_utilisateur WHERE str_email = '".$utilisateur['str_email']."'"));
      if(!empty($is_already_utilisateur)){
        $errors["str_email"] = "<p>Un utilisateur avec cette adresse email est déjà enregistré sur le site !</p>";
      }
    }

    // On vérifie si un exposant avec ce numéro de TVA existe déjà
    if(isset($id_utilisateur_exposant) && !empty($id_utilisateur_exposant) && !is_null($id_utilisateur_exposant)){
        $is_already_tva = intval($wpdb->get_var("SELECT id_utilisateur FROM $table_ins_utilisateur_exposant WHERE str_tvaintra = '".$exposant['str_tvaintra']."' AND id_utilisateur_exposant <> $id_utilisateur_exposant"));
        if(!empty($is_already_tva)){
          $errors["str_tvaintra"] = "<p>Un exposant avec ce numéro de TVA est déjà enregistré sur le site !</p>";
        }
    }else{
      $is_already_tva = intval($wpdb->get_var("SELECT id_utilisateur FROM $table_ins_utilisateur_exposant WHERE str_tvaintra = '".$exposant['str_tvaintra']."'"));
        if(!empty($is_already_tva)){
          $errors["str_tvaintra"] = "<p>Un exposant avec ce numéro de TVA est déjà enregistré sur le site !</p>";
        }
    }

    // Enregistrement de l'exposant
    if(empty($errors) && sizeof($errors) == 0){
      if($_POST['atm_action'] == 'add-new-exposant'){
        // On vérifie si un utilisateur avec cet email existe déjà
        $is_already_utilisateur = intval($wpdb->get_var("SELECT id_utilisateur FROM $table_ins_utilisateur WHERE str_email = '".$utilisateur['str_email']."'"));

        // On vérifie si un exposant avec ce numéro de TVA existe déjà
        $is_already_tva = intval($wpdb->get_var("SELECT id_utilisateur FROM $table_ins_utilisateur_exposant WHERE str_tvaintra = '".$exposant['str_tvaintra']."'"));
        
        if(!empty($is_already_utilisateur)){
          echo '<script type="text/javascript">window.location = "' .home_url().'/wp-admin/admin.php?page=atm_cpt_add_exposant&exposant='.$id_utilisateur_exposant.'&action=edit&num_message=7"</script>';
        }elseif(!empty($is_already_tva)){
          echo '<script type="text/javascript">window.location = "' .home_url().'/wp-admin/admin.php?page=atm_cpt_add_exposant&exposant='.$id_utilisateur_exposant.'&action=edit&num_message=6"</script>';
        }else{
          // Création de l'utilisateur
          $utilisateur['date_creation'] = current_time('mysql');
          if($wpdb->insert($table_ins_utilisateur, $utilisateur)){
            $id_utilisateur = $wpdb->insert_id;

            // Création de l'exposant
            $exposant['id_utilisateur'] = $id_utilisateur;
            $exposant['date_creation'] = date('Y-m-d');
            if($wpdb->insert($table_ins_utilisateur_exposant, $exposant)){
              $id_utilisateur_exposant = $wpdb->insert_id;
              // Infos responsable
              if(empty($bl_responsable_identique)){
                $exposant_responsable['id_utilisateur_exposant'] = $id_utilisateur_exposant;
                $exposant_responsable['date_creation'] = date('Y-m-d');
                $wpdb->insert($table_ins_utilisateur_exposant_contact, $exposant_responsable);
              }
              
              // Infos facturation
              $exposant_facturation['id_utilisateur_exposant'] = $id_utilisateur_exposant;
              $exposant_facturation['date_creation'] = date('Y-m-d');
              $wpdb->insert($table_ins_utilisateur_exposant_contact, $exposant_facturation);
              
              
              // Infos correspondant
              if(empty($bl_correspondant_identique)){
                $exposant_correspondant['id_utilisateur_exposant'] = $id_utilisateur_exposant;
                $exposant_correspondant['date_creation'] = date('Y-m-d');
                $wpdb->insert($table_ins_utilisateur_exposant_contact, $exposant_correspondant);
              }
              // Envoi de l'email de confirmation
              $user_subject = "HOPITECH - Création de votre compte utilisateur";
              $user_headers[] = "From : HOPITECH <tresorier@hopitech.org>";
              $user_message = '<TD bgColor="#182024" width="80%"><span style="font-family: \'Century Gothic\',CenturyGothic,AppleGothic,sans-serif;color:#ded826;font-size:30px;font-weight:bold;">
                      Création de votre compte utilisateur
                      </span></TD><TD bgColor="#182024" width="10%">&nbsp;</TD></TR><TR><TD colspan="3" bgColor="#182024">&nbsp;</TD></TR><TR><TD colspan="3" bgColor="#f1f1f1">&nbsp;</TD></TR><TR><TD bgColor="#f1f1f1" width="10%">&nbsp;</TD><TD bgColor="#f1f1f1" width="80%"><span style="font-family: arial,sans-serif;color:#72797c;font-size:12px;">
                Bonjour '.$utilisateur['str_prenom'].' '.$utilisateur['str_nom'].',<br/><br/>
                Votre compte utilisateur vient d\'être créé sur le site <a href="'.home_url().'" title="HOPITECH">HOPITECH</a><br/><br/>
                Voici le récapitulatif de vos identifiants : <br/>
                <ul><li><strong>Identifiant : </strong> '.$utilisateur['str_email'].'</li><li><strong>Mot de passe : </strong> '.$utilisateur['str_password'].'</li></ul><br/>
                Vous pouvez vous connecter à votre espace privé en suivant ce lient : <a href="'.home_url('/espace-prive/connexion/').'" title="Mon espace privé HOPITECH">Mon espace privé HOPITECH</a>
                Merci et à bientôt,<br/><br/>
                L\'équipe d\'HOPITECH<br/>
                </span></TD><TD bgColor="#f1f1f1" width="10%">&nbsp;</TD></TR>';
              
              add_filter( 'wp_mail_content_type', 'set_html_content_type' );
              wp_mail( $utilisateur['str_email'], $user_subject, $HOPITECH_TEMPLATE_EMAIL_TOP.stripslashes($user_message).$HOPITECH_TEMPLATE_EMAIL_BOTTOM, $user_headers );
              remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
             
              echo '<script type="text/javascript">window.location = "' .home_url().'/wp-admin/admin.php?page=atm_cpt_add_exposant&exposant='.$id_utilisateur_exposant.'&action=edit&num_message=1"</script>';
            }else{
              echo '<script type="text/javascript">window.location = "' .home_url().'/wp-admin/admin.php?page=atm_cpt_add_exposant&exposant='.$id_utilisateur_exposant.'&action=edit&num_message=9"</script>';
            }
          }else{
            echo '<script type="text/javascript">window.location = "' .home_url().'/wp-admin/admin.php?page=atm_cpt_add_exposant&exposant='.$id_utilisateur_exposant.'&action=edit&num_message=9"</script>';
          }
        }
      }elseif($_POST['atm_action'] == 'edit-exposant'){
        // On vérifie si l'email de l'utilisateur a changé
        $current_email = $wpdb->get_var("SELECT str_email FROM $table_ins_utilisateur WHERE id_utilisateur = $id_utilisateur");
        // On vérifie si le mot de passe de l'utilisateur a changé
        $current_password = $wpdb->get_var("SELECT str_password FROM $table_ins_utilisateur WHERE id_utilisateur = $id_utilisateur");
        if( $current_email !== $utilisateur['str_email'] || $current_password !== $utilisateur['str_password'] ){
          // Email et/ou mot de passe différents
          // Envoi d'un email à l'utilisateur
          $email_user = $utilisateur['str_email'];
          $subject_user = "HOPITECH - Vos identifiants de connexion ont été modifiés par un administrateur";
          $message_user = '<table style="border-collapse: collapse; width: 100%">
                  <tr><td style="width:25%;"></td><td style="width:50%;text-align:center;"><div style="display:block;width:100%;text-align:center;"><img width="274" height="63" alt="Hopitech" src="'.home_url().'/wp-content/themes/atm-theme/library/images/login-logo.png"></div></td><td style="width:25%;"></td></tr>
                  <tr><td style="width:25%;"></td><td style="width:50%;height:20px;">&nbsp;</td><td style="width:25%;"></td></tr>
                  <tr><td style="width:25%;"></td><td style="width:50%;"><h1 style="color:#0071a5;text-align:center;font-size:18px;">Modification de vos identifiants<br/>de connexion</h1></td><td style="width:25%;"></td></tr>
                  <tr><td style="width:25%;"></td><td style="width:50%;">
                    <p>Bonjour,</p>
                    <p>Vos identifiants de connexion à votre espace privé sur le site <a href="'.home_url('/espace-prive/connexion/').'" title="Aller sur mon espace privé">HOPITECH</a> ont été modifiés par un administrateur.</p>
                    <p>Récapitulatif de vos identifiants :</p>
                    <ul>
                      <li><strong>Email n° :</strong> '.$utilisateur['str_email'].'</li>
                      <li><strong>Mot de passe</strong> : '.$utilisateur['str_password'].'</li>
                    </ul>
                    <p>Cordialement,</p>
                    <p>HOPITECH</p>
                  </td><td style="width:25%;"></td></tr>
                  <tr><td style="width:25%;"></td><td style="width:50%;height:20px;">&nbsp;</td><td style="width:25%;"></td></tr>
                  </table>
                ';
          
          $headers[] = "HOPITECH <tresorier@hopitech.org>";

          add_filter( 'wp_mail_content_type', 'atm_paybox_set_html_content_type' );
          wp_mail($email_user, $subject_user, stripslashes($message_user), $headers );
          remove_filter( 'wp_mail_content_type', 'atm_paybox_set_html_content_type' );
        }

        // Mise à jour de l'utilisateur
        $wpdb->update($table_ins_utilisateur, $utilisateur, array('id_utilisateur' => $id_utilisateur));
        
        // Mise à jour de l'exposant
        $wpdb->update($table_ins_utilisateur_exposant, $exposant, array('id_utilisateur' => $id_utilisateur, 'id_utilisateur_exposant' => $id_utilisateur_exposant));
        
        // Infos responsable
        if(empty($bl_responsable_identique)){
          $is_already_exposant_responsable = intval($wpdb->get_var("SELECT id_utilisateur_exposant_contact FROM $table_ins_utilisateur_exposant_contact WHERE id_utilisateur_exposant = $id_utilisateur_exposant AND id_utilisateur_exposant_contact_type = 1"));
          if(!empty($is_already_exposant_responsable)){
            $exposant_responsable['id_utilisateur_exposant'] = $id_utilisateur_exposant;
            $wpdb->update($table_ins_utilisateur_exposant_contact, $exposant_responsable, array('id_utilisateur_exposant_contact' => $is_already_exposant_responsable));
          }else{
            $exposant_responsable['id_utilisateur_exposant'] = $id_utilisateur_exposant;
            $wpdb->insert($table_ins_utilisateur_exposant_contact, $exposant_responsable);
          }
        }
        
        // Infos facturation
        $is_already_exposant_facturation = intval($wpdb->get_var("SELECT id_utilisateur_exposant_contact FROM $table_ins_utilisateur_exposant_contact WHERE id_utilisateur_exposant = $id_utilisateur_exposant AND id_utilisateur_exposant_contact_type = 2"));
        if(!empty($is_already_exposant_facturation)){
          $exposant_facturation['id_utilisateur_exposant'] = $id_utilisateur_exposant;
          $wpdb->update($table_ins_utilisateur_exposant_contact, $exposant_facturation, array('id_utilisateur_exposant_contact' => $is_already_exposant_facturation));
        }else{
          $exposant_facturation['id_utilisateur_exposant'] = $id_utilisateur_exposant;
          $wpdb->insert($table_ins_utilisateur_exposant_contact, $exposant_facturation);
        }
        
        // Infos correspondant
        if(empty($bl_correspondant_identique)){
          $is_already_exposant_correspondant = intval($wpdb->get_var("SELECT id_utilisateur_exposant_contact FROM $table_ins_utilisateur_exposant_contact WHERE id_utilisateur_exposant = $id_utilisateur_exposant AND id_utilisateur_exposant_contact_type = 3"));
          if(!empty($is_already_exposant_correspondant)){
            $exposant_correspondant['id_utilisateur_exposant'] = $id_utilisateur_exposant;
            $wpdb->update($table_ins_utilisateur_exposant_contact, $exposant_correspondant, array('id_utilisateur_exposant_contact' => $is_already_exposant_correspondant));
          }else{
            $exposant_correspondant['id_utilisateur_exposant'] = $id_utilisateur_exposant;
            $wpdb->insert($table_ins_utilisateur_exposant_contact, $exposant_correspondant);
          }
        }
        
        echo '<script type="text/javascript">window.location = "' .home_url().'/wp-admin/admin.php?page=atm_cpt_add_exposant&exposant='.$id_utilisateur_exposant.'&action=edit&num_message=2"</script>';
      }
    }else{
      $message[] = 'error';
      $message[] = implode('',$errors);
    }
  }
}


?>
<style>
  .btn{display:inline-block;padding:6px 12px;margin-bottom:0;font-size:14px;font-weight:400;line-height:1.42857143;text-align:center;white-space:nowrap;vertical-align:middle;-ms-touch-action:manipulation;touch-action:manipulation;cursor:pointer;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background-image:none;border:1px solid transparent;border-radius:4px}.btn.active.focus,.btn.active:focus,.btn.focus,.btn:active.focus,.btn:active:focus,.btn:focus{outline:thin dotted;outline:5px auto -webkit-focus-ring-color;outline-offset:-2px}.btn.focus,.btn:focus,.btn:hover{color:#333;text-decoration:none}.btn.active,.btn:active{background-image:none;outline:0;-webkit-box-shadow:inset 0 3px 5px rgba(0,0,0,.125);box-shadow:inset 0 3px 5px rgba(0,0,0,.125)}.btn.disabled,.btn[disabled],fieldset[disabled] .btn{cursor:not-allowed;filter:alpha(opacity=65);-webkit-box-shadow:none;box-shadow:none;opacity:.65}a.btn.disabled,fieldset[disabled] a.btn{pointer-events:none}.btn-default{color:#333;background-color:#fff;border-color:#ccc}.btn-default.focus,.btn-default:focus{color:#333;background-color:#e6e6e6;border-color:#8c8c8c}.btn-default:hover{color:#333;background-color:#e6e6e6;border-color:#adadad}.btn-default.active,.btn-default:active,.open>.dropdown-toggle.btn-default{color:#333;background-color:#e6e6e6;border-color:#adadad}.btn-default.active.focus,.btn-default.active:focus,.btn-default.active:hover,.btn-default:active.focus,.btn-default:active:focus,.btn-default:active:hover,.open>.dropdown-toggle.btn-default.focus,.open>.dropdown-toggle.btn-default:focus,.open>.dropdown-toggle.btn-default:hover{color:#333;background-color:#d4d4d4;border-color:#8c8c8c}.btn-default.active,.btn-default:active,.open>.dropdown-toggle.btn-default{background-image:none}.btn-default.disabled.focus,.btn-default.disabled:focus,.btn-default.disabled:hover,.btn-default[disabled].focus,.btn-default[disabled]:focus,.btn-default[disabled]:hover,fieldset[disabled] .btn-default.focus,fieldset[disabled] .btn-default:focus,fieldset[disabled] .btn-default:hover{background-color:#fff;border-color:#ccc}.btn-default .badge{color:#fff;background-color:#333}.btn-primary{color:#fff;background-color:#337ab7;border-color:#2e6da4}.btn-primary.focus,.btn-primary:focus{color:#fff;background-color:#286090;border-color:#122b40}.btn-primary:hover{color:#fff;background-color:#286090;border-color:#204d74}.btn-primary.active,.btn-primary:active,.open>.dropdown-toggle.btn-primary{color:#fff;background-color:#286090;border-color:#204d74}.btn-primary.active.focus,.btn-primary.active:focus,.btn-primary.active:hover,.btn-primary:active.focus,.btn-primary:active:focus,.btn-primary:active:hover,.open>.dropdown-toggle.btn-primary.focus,.open>.dropdown-toggle.btn-primary:focus,.open>.dropdown-toggle.btn-primary:hover{color:#fff;background-color:#204d74;border-color:#122b40}.btn-primary.active,.btn-primary:active,.open>.dropdown-toggle.btn-primary{background-image:none}.btn-primary.disabled.focus,.btn-primary.disabled:focus,.btn-primary.disabled:hover,.btn-primary[disabled].focus,.btn-primary[disabled]:focus,.btn-primary[disabled]:hover,fieldset[disabled] .btn-primary.focus,fieldset[disabled] .btn-primary:focus,fieldset[disabled] .btn-primary:hover{background-color:#337ab7;border-color:#2e6da4}.btn-primary .badge{color:#337ab7;background-color:#fff}.btn-success{color:#fff;background-color:#5cb85c;border-color:#4cae4c}.btn-success.focus,.btn-success:focus{color:#fff;background-color:#449d44;border-color:#255625}.btn-success:hover{color:#fff;background-color:#449d44;border-color:#398439}.btn-success.active,.btn-success:active,.open>.dropdown-toggle.btn-success{color:#fff;background-color:#449d44;border-color:#398439}.btn-success.active.focus,.btn-success.active:focus,.btn-success.active:hover,.btn-success:active.focus,.btn-success:active:focus,.btn-success:active:hover,.open>.dropdown-toggle.btn-success.focus,.open>.dropdown-toggle.btn-success:focus,.open>.dropdown-toggle.btn-success:hover{color:#fff;background-color:#398439;border-color:#255625}.btn-success.active,.btn-success:active,.open>.dropdown-toggle.btn-success{background-image:none}.btn-success.disabled.focus,.btn-success.disabled:focus,.btn-success.disabled:hover,.btn-success[disabled].focus,.btn-success[disabled]:focus,.btn-success[disabled]:hover,fieldset[disabled] .btn-success.focus,fieldset[disabled] .btn-success:focus,fieldset[disabled] .btn-success:hover{background-color:#5cb85c;border-color:#4cae4c}.btn-success .badge{color:#5cb85c;background-color:#fff}.btn-info{color:#fff;background-color:#5bc0de;border-color:#46b8da}.btn-info.focus,.btn-info:focus{color:#fff;background-color:#31b0d5;border-color:#1b6d85}.btn-info:hover{color:#fff;background-color:#31b0d5;border-color:#269abc}.btn-info.active,.btn-info:active,.open>.dropdown-toggle.btn-info{color:#fff;background-color:#31b0d5;border-color:#269abc}.btn-info.active.focus,.btn-info.active:focus,.btn-info.active:hover,.btn-info:active.focus,.btn-info:active:focus,.btn-info:active:hover,.open>.dropdown-toggle.btn-info.focus,.open>.dropdown-toggle.btn-info:focus,.open>.dropdown-toggle.btn-info:hover{color:#fff;background-color:#269abc;border-color:#1b6d85}.btn-info.active,.btn-info:active,.open>.dropdown-toggle.btn-info{background-image:none}.btn-info.disabled.focus,.btn-info.disabled:focus,.btn-info.disabled:hover,.btn-info[disabled].focus,.btn-info[disabled]:focus,.btn-info[disabled]:hover,fieldset[disabled] .btn-info.focus,fieldset[disabled] .btn-info:focus,fieldset[disabled] .btn-info:hover{background-color:#5bc0de;border-color:#46b8da}.btn-info .badge{color:#5bc0de;background-color:#fff}.btn-warning{color:#fff;background-color:#f0ad4e;border-color:#eea236}.btn-warning.focus,.btn-warning:focus{color:#fff;background-color:#ec971f;border-color:#985f0d}.btn-warning:hover{color:#fff;background-color:#ec971f;border-color:#d58512}.btn-warning.active,.btn-warning:active,.open>.dropdown-toggle.btn-warning{color:#fff;background-color:#ec971f;border-color:#d58512}.btn-warning.active.focus,.btn-warning.active:focus,.btn-warning.active:hover,.btn-warning:active.focus,.btn-warning:active:focus,.btn-warning:active:hover,.open>.dropdown-toggle.btn-warning.focus,.open>.dropdown-toggle.btn-warning:focus,.open>.dropdown-toggle.btn-warning:hover{color:#fff;background-color:#d58512;border-color:#985f0d}.btn-warning.active,.btn-warning:active,.open>.dropdown-toggle.btn-warning{background-image:none}.btn-warning.disabled.focus,.btn-warning.disabled:focus,.btn-warning.disabled:hover,.btn-warning[disabled].focus,.btn-warning[disabled]:focus,.btn-warning[disabled]:hover,fieldset[disabled] .btn-warning.focus,fieldset[disabled] .btn-warning:focus,fieldset[disabled] .btn-warning:hover{background-color:#f0ad4e;border-color:#eea236}.btn-warning .badge{color:#f0ad4e;background-color:#fff}.btn-danger{color:#fff;background-color:#d9534f;border-color:#d43f3a}.btn-danger.focus,.btn-danger:focus{color:#fff;background-color:#c9302c;border-color:#761c19}.btn-danger:hover{color:#fff;background-color:#c9302c;border-color:#ac2925}.btn-danger.active,.btn-danger:active,.open>.dropdown-toggle.btn-danger{color:#fff;background-color:#c9302c;border-color:#ac2925}.btn-danger.active.focus,.btn-danger.active:focus,.btn-danger.active:hover,.btn-danger:active.focus,.btn-danger:active:focus,.btn-danger:active:hover,.open>.dropdown-toggle.btn-danger.focus,.open>.dropdown-toggle.btn-danger:focus,.open>.dropdown-toggle.btn-danger:hover{color:#fff;background-color:#ac2925;border-color:#761c19}.btn-danger.active,.btn-danger:active,.open>.dropdown-toggle.btn-danger{background-image:none}.btn-danger.disabled.focus,.btn-danger.disabled:focus,.btn-danger.disabled:hover,.btn-danger[disabled].focus,.btn-danger[disabled]:focus,.btn-danger[disabled]:hover,fieldset[disabled] .btn-danger.focus,fieldset[disabled] .btn-danger:focus,fieldset[disabled] .btn-danger:hover{background-color:#d9534f;border-color:#d43f3a}.btn-danger .badge{color:#d9534f;background-color:#fff}
  .alert {border: 1px solid transparent;border-radius: 4px;margin-bottom: 20px;padding: 15px;}
  .alert-success {background-color: #dff0d8;border-color: #d6e9c6;color: #3c763d;}
  .alert-info{background-color: #d9edf7;border-color: #bce8f1;color: #31708f;}
  .alert-warning {background-color: #fcf8e3;border-color: #faebcc;color: #8a6d3b;}
  .alert-danger {background-color: #f2dede;border-color: #ebccd1;color: #a94442;}
  .js .postbox .hndle, .js .widget .widget-top {cursor: default;}
  #atm_exposant_sectionid .line_form{margin:8px 30px;}
  #atm_exposant_sectionid label, #atm_exposant_sectionid input, #atm_exposant_sectionid select, #atm_exposant_sectionid textarea{display:inline-block;vertical-align:middle;position:relative;padding:5px 8px;*zoom:1;*display:inline;}
  #atm_exposant_sectionid select{padding:0;}
  #atm_exposant_sectionid label{width:200px;}
  #atm_exposant_sectionid .custom-form-field{display:block;position:relative;padding:10px;background:rgba(255,255,255,0.7);margin-bottom:10px;border-bottom: 1px solid #EAEAEA;}
  #atm_exposant_sectionid .custom-form-field h4{cursor:move;}
  #atm_exposant_sectionid .custom-form-field label, .custom-form-field-apercu-wrapper label{width:100%;}
  #atm_exposant_sectionid .custom-form-field label input{margin-left:1em;}
  #atm_exposant_sectionid input, #atm_exposant_sectionid textarea{width:50%;}
  #atm_exposant_sectionid .suffix{color: #777777;display: inline-block;line-height: 27px;margin-left: 0.5em;vertical-align: top;}
  #atm_exposant_sectionid input.champ-erreur, #atm_exposant_sectionid select.champ-erreur, #atm_exposant_sectionid textarea.champ-erreur{border:1px solid #F00;}
  .btn-remove, .btn-remove-item{color:red;cursor: pointer;}
  .custom-form-field-apercu-wrapper{padding:10px;margin:10px;border:1px solid #DFDFDF;background-color:#EAEAEA;}
  .custom-form-field-apercu-wrapper ul{list-style:none;}
  .btn-remove-item {
    position: absolute;
    right: 5px;
    top: 25px;
}
  .liste-cases-a-cocher{padding:1em;}
  .liste-cases-a-cocher > li{padding:0.5em;border:1px solid #EAEAEA;background-color:#FFF;}
  .liste-cases-a-cocher > li.ui-sortable-helper{background-color:#EEEEEE;}
  .button.button-large.button-primary.btn-custom-form-apercu{margin:1em;}
  .exposant_delpro-reponses{margin:1em;}
  input[type="file"]{margin-left: 210px;}
  .hidden{display:none;}
  #atm_exposant_sectionid label.label_checkbox{width:100%;display:block;text-align:left;}
  #atm_exposant_sectionid label.label_checkbox > input{width:auto;}
  .recapitulatif-commandes table{width: 100%;font-size: 0.938em;background-color: #eee;}
  .recapitulatif-commandes table thead th, .recapitulatif-commandes table th{text-transform: uppercase;color: #0071a5;font-weight: 900;}
  .recapitulatif-commandes table thead{border-bottom: 3px solid #ababab}
  .recapitulatif-commandes table tbody tr{color: #0071a5;text-align: center;}
  .recapitulatif-commandes table tbody tr:nth-child(odd){background-color: #e3e3e3;}
  .recapitulatif-commandes table tbody tr:nth-child(even){background-color: #eaeaea;}
  .recapitulatif-commandes table th, .recapitulatif-commandes table td{padding:20px;}
  .table-recapitulatif td, .table-recapitulatif th {color: #222;}
  .publicite.recapitulatif-commandes table td {padding: 10px;}
  #tab_recap_commande > tr > td:first-child {text-align: right;}
  .commande-status-annulee, .commande-status-refusee {background-color: #f2dede !important;}
  .commande-status-commande-enregistree-en-attente-de-reglement, .commande-status-commande-enregistree-en-attente-dacompte{background-color: #fcf8e3 !important;}
  .commande-status-commande-reglee-en-attente-de-confirmation-hopitech, .commande-status-acompte-verse-en-attente-de-confirmation-hopitech {background-color: #d9edf7 !important;}
  .commande-status-commande-confirmee-en-attente-du-solde {background-color: #f0ad4e !important;}
  .commande-status-commande-reglee-et-confirmee {background-color: #dff0d8 !important;}
  .salon_wrapper {background-color: #FFFFFF;border: 1px solid #ddd;padding: 0;}
  .salon_wrapper.stands_wrapper {background-color: #ddd;}
  .salon_wrapper.closed > .stand, .salon_wrapper.closed > .recapitulatif-commandes{display:none;}
  .salon_wrapper > h4 {background-color: #fff;border-bottom: 1px solid #ddd;margin: 0;padding: 10px;}
  .salon_wrapper > .stand, .salon_wrapper > .publicite, .salon_wrapper > .repas{display:block;margin:20px;}
  .closed .toggle-indicator::before {content: "" !important;}
  .salon_wrapper_button {background-color: transparent;border: medium none;cursor: pointer;float: right;}
  </style>
<div class="wrap">
  <?php if(isset($_GET['action']) && $_GET['action'] == 'edit'): ?>
  <h2>Modifier les informations d'un exposant <a class="add-new-h2" href="/wp-admin/admin.php?page=atm_cpt_add_exposant">Ajouter un exposant</a></h2>
  <?php else: ?>
  <h2>Ajouter une nouveau exposant</h2>
  <?php endif; ?>
  <?php 
    if(isset($_GET['num_message']) && !empty($_GET['num_message']) && !is_null($_GET['num_message'])){
      if(intval($_GET['num_message']) == 1){
        $message[] = 'updated'; $message[] = '<p>L\'exposant a bien été ajouté.</p>';
      }
      if(intval($_GET['num_message']) == 2){
        $message[] = 'updated'; $message[] = '<p>L\'exposant a bien été mis à jour.</p>';  
      }
      if(intval($_GET['num_message']) == 3){
        $message[] = 'error'; $message[] = '<p>L\'exposant n\'a pas pu être uploadé sur le serveur !</p>';  
      }
      if(intval($_GET['num_message']) == 4){
        $message[] = 'updated'; $message[] = '<p>L\'exposant a bien été publié.</p>';  
      }
      if(intval($_GET['num_message']) == 6){
        $message[] = 'error'; $message[] = '<p>Un exposant avec ce numéro de TVA existe déjà !</p>';  
      }
      if(intval($_GET['num_message']) == 7){
        $message[] = 'error'; $message[] = '<p>Un utilisateur avec cette adresse email existe déjà !</p>';  
      }
      if(intval($_GET['num_message']) == 8){
        $message[] = 'error';$message[] = '<p>Désolé, une erreur est survenue lors de la suppression de l\'exposant.</p>';
      }
      if(intval($_GET['num_message']) == 9){
        $message[] = 'error';$message[] = '<p>Désolé, un problème est survenu lors de l\'enregistrement en base de donnée.</p>';
      }
    }
    if(isset($message) && !empty($message) && !is_null($message) && is_array($message)){echo '<div id="message" class="'.$message[0].' below-h2">'.$message[1].'</div>';} 
  ?>
  <div id="poststuff">
    <?php if(isset($_GET['action']) && $_GET['action'] == 'edit'): ?>
    <form method="post" action="/wp-admin/admin.php?page=atm_cpt_add_exposant&amp;exposant=<?php echo intval(atm_sanitize($_GET['exposant'])); ?>&amp;action=edit" enctype="multipart/form-data">
    <?php else: ?>
    <form method="post" action="/wp-admin/admin.php?page=atm_cpt_add_exposant" enctype="multipart/form-data">
    <?php endif; ?>
    <div id="post-body" class="metabox-holder columns-2">
      <div id="post-body-content" style="position: relative;" class="meta-box-sortables">
        <div id="atm_exposant_sectionid" class="postbox">
          <button aria-expanded="true" class="handlediv button-link" type="button"><span class="screen-reader-text">Ouvrir/fermer le bloc Publier</span><span aria-hidden="true" class="toggle-indicator"></span></button>
          <h3 class="hndle">Informations générales</h3>
          <?php
            global $wpdb;
            $table_ins_evenement = $wpdb->prefix.'ins_evenement';
            $table_ins_utilisateur_exposant = $wpdb->prefix.'ins_utilisateur_exposant';
            $table_ins_utilisateur_exposant_contact = $wpdb->prefix.'ins_utilisateur_exposant_contact';
            if(isset($_GET['exposant']) && !empty($_GET['exposant']) && !is_null($_GET['exposant'])){$id_utilisateur_exposant = intval(atm_sanitize($_GET['exposant']));}else{$id_utilisateur_exposant = 0;}
            $infos_exposant = $wpdb->get_results("SELECT * FROM $table_ins_utilisateur_exposant WHERE id_utilisateur_exposant = $id_utilisateur_exposant");
            if(isset($infos_exposant) && !empty($infos_exposant) && !is_null($infos_exposant) && is_array($infos_exposant) && sizeof($infos_exposant) > 0 && $infos_exposant !== false){
              $infos_exposant = $infos_exposant[0];
            }else{
              $infos_exposant = array();
            }
          ?>
          <div class="inside">
            <div class="line_form">
              <label for="str_raisonsociale">Raison sociale <span style="color:red;">*</span></label>
              <input required="required" type="text" name="str_raisonsociale" id="str_raisonsociale" placeholder="Raison sociale de l'exposant..." value="<?php if(isset($_POST['str_raisonsociale'])){echo $_POST['str_raisonsociale'];}else{echo stripslashes($infos_exposant->str_raisonsociale);} ?>" />
            </div>
            <div class="line_form">
              <label for="str_adresse">Adresse <span style="color:red;">*</span></label>
              <textarea required="required" name="str_adresse" id="str_adresse" placeholder="Adresse postal de l'exposant..."><?php if(isset($_POST['str_adresse'])){echo $_POST['str_adresse'];}else{echo stripslashes($infos_exposant->str_adresse);} ?></textarea>
            </div>
            <div class="line_form">
              <label for="str_codepostal">Code Postal <span style="color:red;">*</span></label>
              <input required="required" type="text" name="str_codepostal" id="str_codepostal" placeholder="Code postal de l'exposant..." value="<?php if(isset($_POST['str_codepostal'])){echo $_POST['str_codepostal'];}else{echo stripslashes($infos_exposant->str_codepostal);} ?>" />
            </div>
            <div class="line_form">
              <label for="str_ville">Ville <span style="color:red;">*</span></label>
              <input required="required" type="text" name="str_ville" id="str_ville" placeholder="Ville de l'exposant..." value="<?php if(isset($_POST['str_ville'])){echo $_POST['str_ville'];}else{echo stripslashes($infos_exposant->str_ville);} ?>" />
            </div>
            <div class="line_form">
              <label for="str_pays">Pays <span style="color:red;">*</span></label>
              <input required="required" type="text" name="str_pays" id="str_pays" placeholder="Pays de l'exposant..." value="<?php if(isset($_POST['str_pays'])){echo $_POST['str_pays'];}else{echo stripslashes($infos_exposant->str_pays);} ?>" />
            </div>
            <div class="line_form">
              <label for="str_tvaintra">Numéro TVA Intracommunautaire <span style="color:red;">*</span></label>
              <input required="required" type="text" name="str_tvaintra" id="str_tvaintra" placeholder="Numéro TVA Intracommunautaire..." value="<?php if(isset($_POST['str_tvaintra'])){echo $_POST['str_tvaintra'];}else{echo stripslashes($infos_exposant->str_tvaintra);} ?>" />
            </div>
            <div class="line_form">
              <label for="txt_presentation">Présentation de la société <span style="color:red;">*</span></label>
              <textarea required="required" name="txt_presentation" id="txt_presentation" placeholder="Présentation de la société..."><?php if(isset($_POST['txt_presentation'])){echo $_POST['txt_presentation'];}else{echo stripslashes($infos_exposant->txt_presentation);} ?></textarea>
            </div>
            <div class="line_form">
              <label for="str_fixe">Téléphone fixe <span style="color:red;">*</span></label>
              <input required="required" type="text" name="str_fixe" id="str_fixe" placeholder="Numéro de téléphone fixe de l'exposant..." value="<?php if(isset($_POST['str_fixe'])){echo $_POST['str_fixe'];}else{echo stripslashes($infos_exposant->str_fixe);} ?>" />
            </div>
            <div class="line_form">
              <label for="str_mobile">Téléphone mobile </label>
              <input type="text" name="str_mobile" id="str_mobile" placeholder="Numéro de téléphone portable de l'exposant..." value="<?php if(isset($_POST['str_mobile'])){echo $_POST['str_mobile'];}else{echo stripslashes($infos_exposant->str_mobile);} ?>" />
            </div>
            <div class="line_form">
              <label for="str_fax">Fax </label>
              <input type="text" name="str_fax" id="str_fax" placeholder="Numéro de fax de l'exposant..." value="<?php if(isset($_POST['str_fax'])){echo $_POST['str_fax'];}else{echo stripslashes($infos_exposant->str_fax);} ?>" />
            </div>
            <div class="line_form">
              <label for="str_email">Email de l'exposant<span style="color:red;">*</span></label>
              <input required="required" type="email" name="str_email" id="str_email" placeholder="Adresse email de l'exposant..."  value="<?php if(isset($_POST['str_email'])){echo $_POST['str_email'];}else{echo stripslashes($infos_exposant->str_email);} ?>" />
            </div>
            <div class="line_form">
              <label for="str_site">Site web (sans http://) <span style="color:red;">*</span></label>
              <input required="required" type="text" name="str_site" id="str_site" placeholder="Nom de l'exposant..." value="<?php if(isset($_POST['str_site'])){echo $_POST['str_site'];}else{echo stripslashes($infos_exposant->str_site);} ?>" />
            </div>
            <div class="line_form">
              <label for="id_secteur_activite">Secteur d'activité <span style="color:red;">*</span></label>
              <select required="required" name="id_secteur_activite" id="id_secteur_activite" placeholder="Secteur d'activité...">
              <?php
                $table_ins_secteur_activite = $wpdb->prefix.'ins_secteur_activite';
                $get_secteurs_activite = $wpdb->get_results("SELECT * FROM $table_ins_secteur_activite ORDER BY id_secteur_activite ASC");
                if(isset($get_secteurs_activite) && !empty($get_secteurs_activite) && !is_null($get_secteurs_activite) && is_array($get_secteurs_activite) && sizeof($get_secteurs_activite) > 0 && $get_secteurs_activite !== false){
                  foreach ($get_secteurs_activite as $secteur) {
                    echo '<option value="'.$secteur->id_secteur_activite.'"';
                    if(intval($secteur->id_secteur_activite) == intval($infos_exposant->id_secteur_activite)){echo ' selected="selected"';}
                    echo '>'.$secteur->str_libelle.'</option>';
                  }
                }
              ?>
              </select>
            </div>
            <div class="line_form">
              <label for="str_secteur_activite_autre">Autre secteur d'activité </label>
              <input type="text" name="str_secteur_activite_autre" id="str_secteur_activite_autre" placeholder="Autre secteur d'activité..." value="<?php if(isset($_POST['str_secteur_activite_autre'])){echo $_POST['str_secteur_activite_autre'];}else{echo stripslashes($infos_exposant->str_secteur_activite_autre);} ?>" />
            </div>
            <div class="line_form">
              <label for="int_annee_participation">Première année de participation au salon HOPITECH <span style="color:red;">*</span></label>
              <input required="required" type="text" name="int_annee_participation" id="int_annee_participation" placeholder="Participe au salon HOPITECH depuis l'année..." value="<?php if(isset($_POST['int_annee_participation'])){echo $_POST['int_annee_participation'];}else{echo stripslashes($infos_exposant->int_annee_participation);} ?>" />
            </div>
            <?php if(isset($_GET['action']) && $_GET['action'] == 'edit'): ?>
            <div class="line_form">
              <label for="date_creation">Date de l'inscription </label>
              <input disabled="disabled" type="text" name="date_creation" id="date_creation" value="<?php echo date('d/m/Y',strtotime(stripslashes($infos_exposant->date_creation))); ?>" />
            </div>
            <?php endif; ?>
          </div>
        </div>

        <div id="atm_exposant_sectionid" class="postbox">
          <button aria-expanded="true" class="handlediv button-link" type="button"><span class="screen-reader-text">Ouvrir/fermer le bloc Publier</span><span aria-hidden="true" class="toggle-indicator"></span></button>
          <h3 class="hndle">Contact - Responsable de la participation</h3>
          <div class="inside">
            <?php
              $infos_exposant_responsable = $wpdb->get_results("SELECT * FROM $table_ins_utilisateur_exposant_contact WHERE id_utilisateur_exposant = $id_utilisateur_exposant AND id_utilisateur_exposant_contact_type = 1");
              if(isset($infos_exposant_responsable) && !empty($infos_exposant_responsable) && !is_null($infos_exposant_responsable) && is_array($infos_exposant_responsable) && sizeof($infos_exposant_responsable) > 0 && $infos_exposant_responsable !== false){
                $infos_exposant_responsable = $infos_exposant_responsable[0];
                $bl_responsable_identique = 0;
              }else{
                $bl_responsable_identique = 1;
                $infos_exposant_responsable = array();
              }
            ?>
            <div class="line_form">
                <label class="label_checkbox" for="bl_responsable_identique"><input<?php if(!empty($bl_responsable_identique)){echo ' checked="checked"';} ?> type="checkbox" name="bl_responsable_identique" id="bl_responsable_identique" value="1" /> Responsable de la participation au salon identique aux informations générales </label>
            </div>
            <div class="responsable_wrapper<?php if(!empty($bl_responsable_identique)){echo ' hidden';} ?>">
              <div class="line_form">
                <label for="str_r_nom">Nom <span style="color:red;">*</span></label>
                <input type="text" name="str_r_nom" id="str_r_nom" placeholder="Nom du responsable..." value="<?php if(isset($_POST['str_r_nom'])){echo $_POST['str_r_nom'];}else{echo stripslashes($infos_exposant_responsable->str_nom);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_r_prenom">Prénom <span style="color:red;">*</span></label>
                <input type="text" name="str_r_prenom" id="str_r_prenom" placeholder="Prénom du responsable..." value="<?php if(isset($_POST['str_r_prenom'])){echo $_POST['str_r_prenom'];}else{echo stripslashes($infos_exposant_responsable->str_prenom);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_r_email">Email <span style="color:red;">*</span></label>
                <input type="email" name="str_r_email" id="str_r_email" placeholder="Adresse email du responsable..."  value="<?php if(isset($_POST['str_r_email'])){echo $_POST['str_r_email'];}else{echo stripslashes($infos_exposant_responsable->str_email);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_r_fixe">Téléphone fixe <span style="color:red;">*</span></label>
                <input type="text" name="str_r_fixe" id="str_r_fixe" placeholder="Numéro de téléphone fixe du responsable..." value="<?php if(isset($_POST['str_r_fixe'])){echo $_POST['str_r_fixe'];}else{echo stripslashes($infos_exposant_responsable->str_fixe);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_r_mobile">Téléphone mobile </label>
                <input type="text" name="str_r_mobile" id="str_r_mobile" placeholder="Numéro de téléphone portable du responsable..." value="<?php if(isset($_POST['str_r_mobile'])){echo $_POST['str_r_mobile'];}else{echo stripslashes($infos_exposant_responsable->str_mobile);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_r_fonction">Fonction <span style="color:red;">*</span></label>
                <input type="text" name="str_r_fonction" id="str_r_fonction" placeholder="Fonction du responsable..." value="<?php if(isset($_POST['str_r_fonction'])){echo $_POST['str_r_fonction'];}else{echo stripslashes($infos_exposant_responsable->str_fonction);} ?>" />
              </div>
            </div>
          </div>
        </div>

        <div id="atm_exposant_sectionid" class="postbox">
          <button aria-expanded="true" class="handlediv button-link" type="button"><span class="screen-reader-text">Ouvrir/fermer le bloc Publier</span><span aria-hidden="true" class="toggle-indicator"></span></button>
          <h3 class="hndle">Contact - Informations de facturation</h3>
          <div class="inside">
            <?php
              $infos_exposant_facturation = $wpdb->get_results("SELECT * FROM $table_ins_utilisateur_exposant_contact WHERE id_utilisateur_exposant = $id_utilisateur_exposant AND id_utilisateur_exposant_contact_type = 2");
              if(isset($infos_exposant_facturation) && !empty($infos_exposant_facturation) && !is_null($infos_exposant_facturation) && is_array($infos_exposant_facturation) && sizeof($infos_exposant_facturation) > 0 && $infos_exposant_facturation !== false){
                $infos_exposant_facturation = $infos_exposant_facturation[0];
                $bl_facturation_identique = 0;
              }else{
                $bl_facturation_identique = 1;
                $infos_exposant_facturation = array();
              }
            ?>
            <div class="line_form">
                <label class="label_checkbox" for="bl_facturation_identique"><input<?php if(!empty($bl_facturation_identique)){echo ' checked="checked"';} ?> type="checkbox" name="bl_facturation_identique" id="bl_facturation_identique" value="1" /> Destinataire de la facture identique aux informations générales</label>
            </div>
            <div class="facturation_wrapper<?php if(!empty($bl_facturation_identique)){echo ' hidden';} ?>">
              <div class="line_form">
                <label for="str_f_nom">Nom </label>
                <input type="text" name="str_f_nom" id="str_f_nom" placeholder="Nom du contact facturation..." value="<?php if(isset($_POST['str_f_nom'])){echo $_POST['str_f_nom'];}else{echo stripslashes($infos_exposant_facturation->str_nom);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_f_prenom">Prénom </label>
                <input type="text" name="str_f_prenom" id="str_f_prenom" placeholder="Prénom du contact facturation..." value="<?php if(isset($_POST['str_f_prenom'])){echo $_POST['str_f_prenom'];}else{echo stripslashes($infos_exposant_facturation->str_prenom);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_f_raisonsociale">Raison sociale <span style="color:red;">*</span></label>
                <input type="text" name="str_f_raisonsociale" id="str_f_raisonsociale" placeholder="Raison sociale de l'entreprise..." value="<?php if(isset($_POST['str_f_raisonsociale'])){echo $_POST['str_f_raisonsociale'];}else{echo stripslashes($infos_exposant_facturation->str_facture_raisonsociale);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_f_reference">Référence à faire figurer sur la facture <span style="color:red;">*</span></label>
                <input type="text" name="str_f_reference" id="str_f_reference" placeholder="Référence à faire figurer sur la facture..." value="<?php if(isset($_POST['str_f_reference'])){echo $_POST['str_f_reference'];}else{echo stripslashes($infos_exposant_facturation->str_facture_reference);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_f_email">Email <span style="color:red;">*</span></label>
                <input type="email" name="str_f_email" id="str_f_email" placeholder="Adresse email du contact facturation..."  value="<?php if(isset($_POST['str_f_email'])){echo $_POST['str_f_email'];}else{echo stripslashes($infos_exposant_facturation->str_email);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_f_fixe">Téléphone fixe <span style="color:red;">*</span></label>
                <input type="text" name="str_f_fixe" id="str_f_fixe" placeholder="Numéro de téléphone fixe du contact facturation..." value="<?php if(isset($_POST['str_f_fixe'])){echo $_POST['str_f_fixe'];}else{echo stripslashes($infos_exposant_facturation->str_fixe);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_f_mobile">Téléphone mobile </label>
                <input type="text" name="str_f_mobile" id="str_f_mobile" placeholder="Numéro de téléphone portable du contact facturation..." value="<?php if(isset($_POST['str_f_mobile'])){echo $_POST['str_f_mobile'];}else{echo stripslashes($infos_exposant_facturation->str_mobile);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_f_fax">Fax </label>
                <input type="text" name="str_f_fax" id="str_f_fax" placeholder="Numéro de fax du contact facturation..." value="<?php if(isset($_POST['str_f_fax'])){echo $_POST['str_f_fax'];}else{echo stripslashes($infos_exposant_facturation->str_fax);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_f_adresse">Adresse <span style="color:red;">*</span></label>
                <textarea name="str_f_adresse" id="str_f_adresse" placeholder="Adresse postal du contact facturation..."><?php if(isset($_POST['str_f_adresse'])){echo $_POST['str_f_adresse'];}else{echo stripslashes($infos_exposant_facturation->str_facture_adresse);} ?></textarea>
              </div>
              <div class="line_form">
                <label for="str_f_codepostal">Code Postal <span style="color:red;">*</span></label>
                <input type="text" name="str_f_codepostal" id="str_f_codepostal" placeholder="Code postal du contact facturation..." value="<?php if(isset($_POST['str_f_codepostal'])){echo $_POST['str_f_codepostal'];}else{echo stripslashes($infos_exposant_facturation->str_facture_codepostal);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_f_ville">Ville <span style="color:red;">*</span></label>
                <input type="text" name="str_f_ville" id="str_f_ville" placeholder="Ville du contact facturation..." value="<?php if(isset($_POST['str_f_ville'])){echo $_POST['str_f_ville'];}else{echo stripslashes($infos_exposant_facturation->str_facture_ville);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_f_pays">Pays <span style="color:red;">*</span></label>
                <input type="text" name="str_f_pays" id="str_f_pays" placeholder="Pays du contact facturation..." value="<?php if(isset($_POST['str_f_pays'])){echo $_POST['str_f_pays'];}else{echo stripslashes($infos_exposant_facturation->str_facture_pays);} ?>" />
              </div>
            </div>
          </div>
        </div>

        <div id="atm_exposant_sectionid" class="postbox">
          <button aria-expanded="true" class="handlediv button-link" type="button"><span class="screen-reader-text">Ouvrir/fermer le bloc Publier</span><span aria-hidden="true" class="toggle-indicator"></span></button>
          <h3 class="hndle">Contact - Correspondant sur place</h3>
          <div class="inside">
            <?php
              $infos_exposant_correspondant = $wpdb->get_results("SELECT * FROM $table_ins_utilisateur_exposant_contact WHERE id_utilisateur_exposant = $id_utilisateur_exposant AND id_utilisateur_exposant_contact_type = 3");
              if(isset($infos_exposant_correspondant) && !empty($infos_exposant_correspondant) && !is_null($infos_exposant_correspondant) && is_array($infos_exposant_correspondant) && sizeof($infos_exposant_correspondant) > 0 && $infos_exposant_correspondant !== false){
                $infos_exposant_correspondant = $infos_exposant_correspondant[0];
                $bl_correspondant_identique = 0;
              }else{
                $bl_correspondant_identique = 1;
                $infos_exposant_correspondant = array();
              }
            ?>
            <div class="line_form">
                <label class="label_checkbox" for="bl_correspondant_identique"><input<?php if(!empty($bl_correspondant_identique)){echo ' checked="checked"';} ?> type="checkbox" name="bl_correspondant_identique" id="bl_correspondant_identique" value="1" /> Correspondant sur place identique aux informations générales</label>
            </div>
            <div class="correspondant_wrapper<?php if(!empty($bl_correspondant_identique)){echo ' hidden';} ?>">
              <div class="line_form">
                <label for="str_c_nom">Nom <span style="color:red;">*</span></label>
                <input type="text" name="str_c_nom" id="str_c_nom" placeholder="Nom du correspondant sur place..." value="<?php if(isset($_POST['str_c_nom'])){echo $_POST['str_c_nom'];}else{echo stripslashes($infos_exposant_correspondant->str_nom);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_c_prenom">Prénom <span style="color:red;">*</span></label>
                <input type="text" name="str_c_prenom" id="str_c_prenom" placeholder="Prénom du correspondant sur place..." value="<?php if(isset($_POST['str_c_prenom'])){echo $_POST['str_c_prenom'];}else{echo stripslashes($infos_exposant_correspondant->str_prenom);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_c_email">Email <span style="color:red;">*</span></label>
                <input type="email" name="str_c_email" id="str_c_email" placeholder="Adresse email du correspondant sur place..."  value="<?php if(isset($_POST['str_c_email'])){echo $_POST['str_c_email'];}else{echo stripslashes($infos_exposant_correspondant->str_email);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_c_fixe">Téléphone fixe <span style="color:red;">*</span></label>
                <input type="text" name="str_c_fixe" id="str_c_fixe" placeholder="Numéro de téléphone fixe du correspondant sur place..." value="<?php if(isset($_POST['str_c_fixe'])){echo $_POST['str_c_fixe'];}else{echo stripslashes($infos_exposant_correspondant->str_fixe);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_c_mobile">Téléphone mobile <span style="color:red;">*</span></label>
                <input type="text" name="str_c_mobile" id="str_c_mobile" placeholder="Numéro de téléphone portable du correspondant sur place..." value="<?php if(isset($_POST['str_c_mobile'])){echo $_POST['str_c_mobile'];}else{echo stripslashes($infos_exposant_correspondant->str_mobile);} ?>" />
              </div>
              <div class="line_form">
                <label for="str_c_fonction">Fonction <span style="color:red;">*</span></label>
                <input type="text" name="str_c_fonction" id="str_c_fonction" placeholder="Fonction du correspondant sur place..." value="<?php if(isset($_POST['str_c_fonction'])){echo $_POST['str_c_fonction'];}else{echo stripslashes($infos_exposant_correspondant->str_fonction);} ?>" />
              </div>
            </div>
          </div>
        </div>

        <div id="atm_exposant_sectionid" class="postbox<?php if(isset($_GET['action']) && $_GET['action'] == 'edit'): ?> closed<?php endif; ?>">
          <button aria-expanded="true" class="handlediv button-link" type="button"><span class="screen-reader-text">Ouvrir/fermer le bloc Publier</span><span aria-hidden="true" class="toggle-indicator"></span></button>
          <h3 class="hndle">Utilisateur du site</h3>
          <div class="inside"<?php if(isset($_GET['action']) && $_GET['action'] == 'edit'): ?> style="display:none"<?php endif; ?>>
            <?php
              $table_ins_utilisateur = $wpdb->prefix.'ins_utilisateur';
              $id_utilisateur = $infos_exposant->id_utilisateur;
              $infos_utilisateur_site = $wpdb->get_results("SELECT * FROM $table_ins_utilisateur WHERE id_utilisateur = $id_utilisateur");
              if(isset($infos_utilisateur_site) && !empty($infos_utilisateur_site) && !is_null($infos_utilisateur_site) && is_array($infos_utilisateur_site) && sizeof($infos_utilisateur_site) > 0 && $infos_utilisateur_site !== false){
                $infos_utilisateur_site = $infos_utilisateur_site[0];
              }else{
                $infos_utilisateur_site = array();
              }
            ?>
            <input type="hidden" name="id_utilisateur" id="id_utilisateur" value="<?php echo $id_utilisateur; ?>" />
            <div class="line_form">
              <label for="str_u_nom">Nom <span style="color:red;">*</span></label>
              <input required="required" type="text" name="str_u_nom" id="str_u_nom" placeholder="Nom de l'utilisateur du site..." value="<?php if(isset($_POST['str_u_nom'])){echo $_POST['str_u_nom'];}else{echo stripslashes($infos_utilisateur_site->str_nom);} ?>" />
            </div>
            <div class="line_form">
              <label for="str_u_prenom">Prénom <span style="color:red;">*</span></label>
              <input required="required" type="text" name="str_u_prenom" id="str_u_prenom" placeholder="Prénom de l'utilisateur du site..." value="<?php if(isset($_POST['str_u_prenom'])){echo $_POST['str_u_prenom'];}else{echo stripslashes($infos_utilisateur_site->str_prenom);} ?>" />
            </div>
            <div class="line_form">
              <label for="str_u_email">Email <span style="color:red;">*</span></label>
              <input required="required" type="email" name="str_u_email" id="str_u_email" placeholder="Adresse email de l'utilisateur du site..."  value="<?php if(isset($_POST['str_u_email'])){echo $_POST['str_u_email'];}else{echo stripslashes($infos_utilisateur_site->str_email);} ?>" />
            </div>
            <div class="line_form">
              <label for="str_u_fixe">Téléphone fixe <span style="color:red;">*</span></label>
              <input required="required" type="text" name="str_u_fixe" id="str_u_fixe" placeholder="Numéro de téléphone fixe de l'utilisateur du site..." value="<?php if(isset($_POST['str_u_fixe'])){echo $_POST['str_u_fixe'];}else{echo stripslashes($infos_utilisateur_site->str_fixe);} ?>" />
            </div>
            <div class="line_form">
              <label for="str_u_password">Mot de passe <span style="color:red;">*</span></label>
              <input type="text" name="str_u_password" id="str_u_password" placeholder="Mot de passe de l'utilisateur du site..." value="<?php if(isset($_POST['str_u_password'])){echo $_POST['str_u_password'];}else{echo stripslashes($infos_utilisateur_site->str_password);} ?>" />
            </div>
          </div>
        </div>
        <?php if(isset($_GET['action']) && $_GET['action'] == 'edit'): ?>
        <hr/>
        <!-- RÉSERVATION DE STAND -->
        <div id="atm_exposant_sectionid" class="postbox">
          <button aria-expanded="true" class="handlediv button-link" type="button"><span class="screen-reader-text">Ouvrir/fermer le bloc Publier</span><span aria-hidden="true" class="toggle-indicator"></span></button>
          <h3 class="hndle">Réservation de stand</h3>
          <div class="inside">
            <?php
              $table_ins_reservation_stand = $wpdb->prefix.'ins_reservation_stand';
              $table_ins_dimension_stand = $wpdb->prefix.'ins_dimension_stand';
              $table_ins_commande = $wpdb->prefix.'ins_commande';
              $table_ins_commande_ligne = $wpdb->prefix.'ins_commande_ligne';
              $table_ins_commande_status = $wpdb->prefix.'ins_commande_status';
              $id_utilisateur = $infos_exposant->id_utilisateur;

              // On récupère la liste des salons HOPITECH
              $liste_salons = $wpdb->get_results("SELECT id_evenement, str_libelle, str_annee FROM $table_ins_evenement ORDER BY date_ouverture_exposant DESC");
              if(isset($liste_salons) && !empty($liste_salons) && !is_null($liste_salons) && is_array($liste_salons) && sizeof($liste_salons) > 0 && $liste_salons !== false){
                foreach ($liste_salons as $salon) {
                  $id_evenement = $salon->id_evenement;

                  // On récupère les informations sur les stands
                  $liste_stands = $wpdb->get_results("SELECT * FROM $table_ins_dimension_stand WHERE id_evenement = ".$salon->id_evenement." ORDER BY id_dimension_stand ASC");

                  $liste_reservations_stands = $wpdb->get_results("SELECT c.id_reservation_stand, c.str_choix_1, c.str_choix_2, c.str_choix_3, c.str_choix_4, c.str_enseigne, c.str_stand_definitif, c.date_creation, ds.id_dimension_stand, ds.str_libelle, ds.int_dimension, ds.int_prix, ds.float_tva 
                                                                  FROM $table_ins_reservation_stand as c
                                                                  LEFT JOIN $table_ins_dimension_stand as ds ON ds.id_dimension_stand = c.id_dimension_stand
                                                                  WHERE c.id_utilisateur_exposant = $id_utilisateur_exposant AND c.id_evenement = $id_evenement
                                                                  ORDER BY c.date_creation DESC");
                  if(isset($liste_reservations_stands) && !empty($liste_reservations_stands) && !is_null($liste_reservations_stands) && is_array($liste_reservations_stands) && sizeof($liste_reservations_stands) > 0 && $liste_reservations_stands !== false){
                    echo '<div class="salon_wrapper stands_wrapper" data-id_evenement="'.$salon->id_evenement.'">';
                    echo '<h4>'.$salon->str_libelle.'<button class="salon_wrapper_button" title="Afficher / Masquer ce bloc"><span aria-hidden="true" class="toggle-indicator"></span></button></h4>';
                    foreach ($liste_reservations_stands as $reservation_stand) {

                      $id_reservation_stand = intval($reservation_stand->id_reservation_stand);
                      
                      $infos_commande = $wpdb->get_results("SELECT cs.str_libelle, c.id_commande, cl.id_commande_ligne, c.id_commande_status
                                                                  FROM $table_ins_commande as c
                                                                  LEFT JOIN $table_ins_commande_ligne as cl ON cl.id_commande = c.id_commande
                                                                  LEFT JOIN $table_ins_commande_status as cs ON cs.id_commande_status = c.id_commande_status
                                                                  WHERE c.id_utilisateur = $id_utilisateur AND c.id_evenement = $id_evenement AND c.id_commande_type = 1 AND cl.id_reservation = $id_reservation_stand AND c.id_commande_status < 7
                                                                  ");
                      if(isset($infos_commande) && !empty($infos_commande) && !is_null($infos_commande) && is_array($infos_commande) && sizeof($infos_commande) > 0 && $infos_commande !== false){
                        $infos_commande = $infos_commande[0];
                        $statut = $infos_commande->str_libelle;
                        $id_commande_status = intval($infos_commande->id_commande_status);
                        $is_reservation = 0;
                        $is_commande = 1;
                        $id_commande = $infos_commande->id_commande;
                        $id_commande_ligne = $infos_commande->id_commande_ligne;
                      }else{
                        $statut = "Enregistrée mais pas encore commandée";
                        $id_commande_status = 0;
                        $is_reservation = 1;
                        $is_commande = 0;
                        $id_commande = 0;
                        $id_commande_ligne = 0;
                      }
                      echo '<div class="stand" data-id_evenement="'.$salon->id_evenement.'" data-id_reservation_stand="'.$id_reservation_stand.'" data-id_commande="'.$id_commande.'" data-id_commande_ligne="'.$id_commande_ligne.'">
                              <div class="alert ';if($id_commande_status==5){echo 'alert-warning';}elseif($id_commande_status==6){echo 'alert-success';}else{echo 'alert-info';}echo '"><strong>Statut : </strong> '.$statut.'</div>
                              <strong>Surface d\'exposition :</strong><br/>
                              <select name="id_dimension_stand">';
                              foreach ($liste_stands as $stand) {
                                echo '<option value="'.$stand->id_dimension_stand.'"';
                                if($reservation_stand->id_dimension_stand == $stand->id_dimension_stand){echo ' selected="selected"';}
                                echo '>'.$stand->str_libelle.' - '.number_format($stand->int_prix, 2, ',', '&nbsp;').'&nbsp;&euro;&nbsp;HT</option>';
                              }

                        echo '</select><br/><br/>
                              <strong class="">Choix d\'implantation par ordre de préférence :</strong>
                              <ul>
                                <li><strong>Choix #1</strong> : <input type="text" name="str_choix_1" value="'.$reservation_stand->str_choix_1.'" /></li>
                                <li><strong>Choix #2</strong> : <input type="text" name="str_choix_2" value="'.$reservation_stand->str_choix_2.'" /></li>
                                <li><strong>Choix #3</strong> : <input type="text" name="str_choix_3" value="'.$reservation_stand->str_choix_3.'" /></li>
                                <li><strong>Choix #4</strong> : <input type="text" name="str_choix_4" value="'.$reservation_stand->str_choix_4.'" /></li>
                              </ul>
                              <p><strong class="">Nom de l’enseigne</strong> : <br/><input type="text" name="str_enseigne" maxlength="18" value="'.$reservation_stand->str_enseigne.'" /></p>
                               <div class="reservation-stand-actions-wrapper"';if(empty($is_reservation)){echo ' style="display:none"';} echo '> 
                                <hr/>
                                <h4>Actions sur la réservation</h4>
                                <div class="reservation-stand-actions">
                                  <div class="reservation-stand-buttons-actions">
                                    <button type="button" class="btn btn-danger btn-supprimer-reservation">Supprimer la réservation</button>
                                    <!--<button type="button" class="btn btn-primary btn-creer-reservation">Créer la réservation</button>-->
                                    <button type="button" class="btn btn-warning btn-forcer-commande">Transformer la réservation en commande</button>
                                  </div>
                                </div>
                              </div>
                              <div class="commande-stand-actions-wrapper"';if(empty($is_commande)){echo ' style="display:none"';} echo '>
                                <hr/>
                                <h4>Actions sur la commande</h4>
                                <div class="commande-stand-actions">';
                                if($id_commande_status==3 || $id_commande_status==4){echo '<p><strong class="">Emplacement définitif</strong> : <br/><input type="text" name="str_stand" value="'.$reservation_stand->str_stand_definitif.'" /></p>';}
                                echo '<div class="reservation-stand-buttons-actions">
                                    <button type="button" class="btn btn-danger btn-annuler-commande">Annuler la commande</button>
                                    <button type="button" class="btn btn-primary btn-acompte-recu"';if($id_commande_status!==2){echo ' disabled="disabled"';}echo '>Acompte reçu</button>
                                    <button type="button" class="btn btn-warning btn-confirmer-reservation"';if($id_commande_status!==3){echo ' disabled="disabled"';}echo '>Confirmer la commande</button>
                                    <button type="button" class="btn btn-success btn-reglement-effectue"';if($id_commande_status!==5){echo ' disabled="disabled"';}echo '>Règlement effectué</button>
                                  </div>
                                </div>
                              </div>
                            </div>';
                    }
                    echo '</div>';
                  }else{
                    // Pas de réservation
                    if(intval(date('Y')) == intval($salon->str_annee)){
                      echo '<div class="salon_wrapper stands_wrapper" data-id_evenement="'.$salon->id_evenement.'">';
                      echo '<h4>'.$salon->str_libelle.'<button class="salon_wrapper_button" title="Afficher / Masquer ce bloc"><span aria-hidden="true" class="toggle-indicator"></span></button></h4>';
                      echo '<div class="stand" data-id_evenement="'.$salon->id_evenement.'">
                              <div class="alert alert-danger"><strong>Statut : </strong> Pas de réservation !</div>
                              <strong>Surface d\'exposition :</strong><br/>
                              <select name="id_dimension_stand">';
                              foreach ($liste_stands as $stand) {
                                echo '<option value="'.$stand->id_dimension_stand.'">'.$stand->str_libelle.' - '.number_format($stand->int_prix, 2, ',', '&nbsp;').'&nbsp;&euro;&nbsp;HT</option>';
                              }

                        echo '</select><br/><br/>
                              <strong class="">Choix d\'implantation par ordre de préférence :</strong>
                              <ul>
                                <li><strong>Choix #1</strong> : <input type="text" name="str_choix_1" value="" /></li>
                                <li><strong>Choix #2</strong> : <input type="text" name="str_choix_2" value="" /></li>
                                <li><strong>Choix #3</strong> : <input type="text" name="str_choix_3" value="" /></li>
                                <li><strong>Choix #4</strong> : <input type="text" name="str_choix_4" value="" /></li>
                              </ul>
                              <p><strong class="">Nom de l’enseigne</strong> : <br/><input type="text" name="str_enseigne" maxlength="18" value="" /></p>
                               <div class="reservation-stand-actions-wrapper"> 
                                <hr/>
                                <h4>Actions sur la réservation</h4>
                                <div class="reservation-stand-actions">
                                  <div class="reservation-stand-buttons-actions">
                                    <button type="button" class="btn btn-primary btn-creer-reservation">Créer la réservation</button>
                                  </div>
                                </div>
                              </div>
                            </div>';
                      echo '</div>';
                    }
                  }
                }
              }
            ?>            
          </div>
        </div>

        <!-- RÉSERVATION DE PUBLICITÉS -->
        <div id="atm_exposant_sectionid" class="postbox">
          <button aria-expanded="true" class="handlediv button-link" type="button"><span class="screen-reader-text">Ouvrir/fermer le bloc Publier</span><span aria-hidden="true" class="toggle-indicator"></span></button>
          <h3 class="hndle">Réservation de publicité(s)</h3>
          <div class="inside">
            <?php
              $table_ins_reservation_publicite = $wpdb->prefix.'ins_reservation_publicite';
              $table_ins_publicite = $wpdb->prefix.'ins_publicite';
              $table_ins_commande = $wpdb->prefix.'ins_commande';
              $table_ins_commande_ligne = $wpdb->prefix.'ins_commande_ligne';
              $table_ins_commande_status = $wpdb->prefix.'ins_commande_status';
              $id_utilisateur = $infos_exposant->id_utilisateur;

              // On récupère la liste des salons HOPITECH
              $liste_salons = $wpdb->get_results("SELECT id_evenement, str_libelle, str_annee FROM $table_ins_evenement ORDER BY date_ouverture_exposant DESC");
              if(isset($liste_salons) && !empty($liste_salons) && !is_null($liste_salons) && is_array($liste_salons) && sizeof($liste_salons) > 0 && $liste_salons !== false){
                foreach ($liste_salons as $salon) {
                  $id_evenement = $salon->id_evenement;

                  // On récupère les informations sur les publicités
                  $liste_publicites = $wpdb->get_results("SELECT * FROM $table_ins_publicite WHERE id_evenement = ".$id_evenement." ORDER BY id_publicite ASC");

                  $infos_commande = $wpdb->get_row("SELECT cs.str_libelle, c.id_commande, c.id_commande_status
                                                                  FROM $table_ins_commande as c
                                                                  LEFT JOIN $table_ins_commande_ligne as cl ON cl.id_commande = c.id_commande
                                                                  LEFT JOIN $table_ins_commande_status as cs ON cs.id_commande_status = c.id_commande_status
                                                                  WHERE c.id_utilisateur = $id_utilisateur AND c.id_evenement = $id_evenement AND c.id_commande_type = 2 AND c.id_commande_status < 7
                                                                  GROUP BY c.id_commande");
                  if(count($infos_commande)!==0){
                    $statut = $infos_commande->str_libelle;
                    $id_commande_status = intval($infos_commande->id_commande_status);
                    $is_reservation = 0;
                    $is_commande = 1;
                    $id_commande = $infos_commande->id_commande;
                    $reservation_publicite=$wpdb->get_results( 'SELECT id_publicite FROM '.$wpdb->prefix.'ins_reservation_publicite WHERE id_evenement='.$id_evenement.' AND id_utilisateur_exposant='.$id_utilisateur_exposant);
                    if(isset($reservation_publicite) && !empty($reservation_publicite) && !is_null($reservation_publicite) && is_array($reservation_publicite) && sizeof($reservation_publicite) > 0 && $reservation_publicite !== false){
                      foreach ($reservation_publicite as $resa_pub) {
                        $liste_reservations_publicites[] = $resa_pub->id_publicite;
                      }
                    }
                  }else{
                    $reservation_publicite=$wpdb->get_results( 'SELECT id_publicite FROM '.$wpdb->prefix.'ins_reservation_publicite WHERE id_evenement='.$id_evenement.' AND id_utilisateur_exposant='.$id_utilisateur_exposant);
                    if(isset($reservation_publicite) && !empty($reservation_publicite) && !is_null($reservation_publicite) && is_array($reservation_publicite) && sizeof($reservation_publicite) > 0 && $reservation_publicite !== false){
                      foreach ($reservation_publicite as $resa_pub) {
                        $liste_reservations_publicites[] = $resa_pub->id_publicite;
                      }
                    }else{
                      $liste_reservations_publicites = array();
                    }
                    if(count($reservation_publicite)!==0){
                      $statut = "Enregistrée mais pas encore commandée";
                      $id_commande_status = 2;
                      $is_reservation = 1;
                      $is_commande = 0;
                      $id_commande = 0;
                    }else{
                      $statut = "Pas de réservation !";
                      $id_commande_status = 0;
                      $is_reservation = 0;
                      $is_commande = 0;
                      $id_commande = 0;
                    }
                  }
                  echo '<div class="salon_wrapper publicites_wrapper" data-id_evenement="'.$salon->id_evenement.'">';
                  echo '<h4>'.$salon->str_libelle.'<button class="salon_wrapper_button" title="Afficher / Masquer ce bloc"><span aria-hidden="true" class="toggle-indicator"></span></button></h4>
                    <div class="publicite recapitulatif-commandes" data-id_evenement="'.$salon->id_evenement.'" data-id_commande="'.$id_commande.'">';
                  echo '<div class="alert ';if($id_commande_status==5){echo 'alert-warning';}elseif($id_commande_status==6){echo 'alert-success';}elseif($id_commande_status==0){echo 'alert-danger';}else{echo 'alert-info';}echo '"><strong>Statut : </strong> '.$statut.'</div>';
                  if(isset($liste_publicites) && !empty($liste_publicites) && !is_null($liste_publicites) && is_array($liste_publicites) && sizeof($liste_publicites) > 0 && $liste_publicites !== false && $id_commande_status > 0){
                    echo '<table>
                        <thead>
                            <tr>
                                <th>Libellé</th>
                                <th>Quantité restante</th>
                                <th>Tarif (HT)</th>
                                <th>Choisir</th>
                            </tr>
                        </thead><tbody>';
                    foreach ($liste_publicites as $publicite) {                      
                      echo '<tr>
                            <td class="libelle">'.$publicite->str_libelle.'</td>
                            <td>'.$publicite->int_qte_dispo.'</td>
                            <td>'.$publicite->int_prix.'€</td>
                            <td width="100">';
                      if($publicite->int_qte_dispo>=1 || in_array($publicite->id_publicite,$liste_reservations_publicites)){
                          if(in_array($publicite->id_publicite,$liste_reservations_publicites)){
                            echo '<input type="checkbox" value="'.$publicite->id_publicite.'" name="id_publicite[]"  id="id_publicite[]" class="checkbox" checked>';
                          }else{
                            echo '<input type="checkbox" value="'.$publicite->id_publicite.'" name="id_publicite[]" id="id_publicite[]" class="checkbox">';
                          }
                      }
                         echo '</td>
                        </tr>';
                    }
                    echo '</tbody></table>';
                    echo '<div class="reservation-stand-actions-wrapper"';if(empty($is_reservation)){echo ' style="display:none"';} echo '> 
                                <hr/>
                                <h4>Actions sur la réservation</h4>
                                <div class="reservation-stand-actions">
                                  <div class="reservation-stand-buttons-actions">
                                    <button type="button" class="btn btn-danger btn-supprimer-reservation">Supprimer la réservation</button>
                                    <!--<button type="button" class="btn btn-primary btn-creer-reservation">Créer la réservation</button>-->
                                    <button type="button" class="btn btn-warning btn-forcer-commande">Transformer la réservation en commande</button>
                                  </div>
                                </div>
                              </div>
                              <div class="commande-stand-actions-wrapper"';if(empty($is_commande)){echo ' style="display:none"';} echo '>
                                <hr/>
                                <h4>Actions sur la commande</h4>
                                <div class="commande-stand-actions">
                                <div class="reservation-stand-buttons-actions">
                                    <button type="button" class="btn btn-danger btn-annuler-commande">Annuler la commande</button>
                                    <button type="button" class="btn btn-primary btn-acompte-recu"';if($id_commande_status!==2){echo ' disabled="disabled"';}echo '>Acompte reçu</button>
                                    <button type="button" class="btn btn-warning btn-confirmer-reservation"';if($id_commande_status!==3){echo ' disabled="disabled"';}echo '>Confirmer la commande</button>
                                    <button type="button" class="btn btn-success btn-reglement-effectue"';if($id_commande_status!==5){echo ' disabled="disabled"';}echo '>Règlement effectué</button>
                                  </div>
                                </div>
                              </div></div></div>';
                  }else{
                    // Pas de réservation
                    if(intval(date('Y')) == intval($salon->str_annee)){
                     echo '<table>
                        <thead>
                            <tr>
                                <th>Libellé</th>
                                <th>Quantité restante</th>
                                <th>Tarif (HT)</th>
                                <th>Choisir</th>
                            </tr>
                        </thead><tbody>';
                    foreach ($liste_publicites as $publicite) {
                      $reservation_publicite=$wpdb->get_row( 'SELECT  * FROM '.$wpdb->prefix.'ins_reservation_publicite WHERE id_publicite='.$publicite->id_publicite.' AND id_utilisateur_exposant='.$id_utilisateur_exposant);
                      echo '<tr>
                            <td class="libelle">'.$publicite->str_libelle.'</td>
                            <td>'.$publicite->int_qte_dispo.'</td>
                            <td>'.$publicite->int_prix.'€</td>
                            <td width="100">';
                      if($publicite->int_qte_dispo>=1){
                          if($reservation_publicite->id_publicite==$publicite->id_publicite){
                                  echo '<input type="checkbox" value="'.$publicite->id_publicite.'" name="id_publicite[]"  id="id_publicite[]" class="checkbox" checked>';
                          }else{
                            echo '<input type="checkbox" value="'.$publicite->id_publicite.'" name="id_publicite[]" id="id_publicite[]" class="checkbox">';
                          }
                      }
                         echo '</td>
                        </tr>';
                    }
                    echo '</tbody></table>';
                    echo '<div class="reservation-stand-actions-wrapper"> 
                                <hr/>
                                <h4>Actions sur la réservation</h4>
                                <div class="reservation-stand-actions">
                                  <div class="reservation-stand-buttons-actions">
                                    <button type="button" class="btn btn-primary btn-creer-reservation">Créer la réservation</button>
                                  </div>
                                </div>
                              </div>
                              </div></div>';
                    }
                  }
                }
              }
            ?>
          </div>
        </div>

        <!-- RÉSERVATION DE REPAS -->
        <div id="atm_exposant_sectionid" class="postbox">
          <button aria-expanded="true" class="handlediv button-link" type="button"><span class="screen-reader-text">Ouvrir/fermer le bloc Publier</span><span aria-hidden="true" class="toggle-indicator"></span></button>
          <h3 class="hndle">Réservation de repas</h3>
          <div class="inside">
            <?php
              $table_ins_reservation_repas = $wpdb->prefix.'ins_reservation_repas';
              $table_ins_repas = $wpdb->prefix.'ins_repas';
              $table_ins_commande = $wpdb->prefix.'ins_commande';
              $table_ins_commande_ligne = $wpdb->prefix.'ins_commande_ligne';
              $table_ins_commande_status = $wpdb->prefix.'ins_commande_status';
              $id_utilisateur = $infos_exposant->id_utilisateur;

              // On récupère la liste des salons HOPITECH
              $liste_salons = $wpdb->get_results("SELECT id_evenement, str_libelle, str_annee FROM $table_ins_evenement ORDER BY date_ouverture_exposant DESC");
              if(isset($liste_salons) && !empty($liste_salons) && !is_null($liste_salons) && is_array($liste_salons) && sizeof($liste_salons) > 0 && $liste_salons !== false){
                foreach ($liste_salons as $salon) {
                  $id_evenement = $salon->id_evenement;

                  // On récupère les informations sur les publicités
                  $liste_repass = $wpdb->get_results("SELECT * FROM $table_ins_repas WHERE id_evenement = ".$id_evenement." ORDER BY id_repas ASC");

                  $infos_commande = $wpdb->get_row("SELECT cs.str_libelle, c.id_commande, c.id_commande_status
                                                                  FROM $table_ins_commande as c
                                                                  LEFT JOIN $table_ins_commande_ligne as cl ON cl.id_commande = c.id_commande
                                                                  LEFT JOIN $table_ins_commande_status as cs ON cs.id_commande_status = c.id_commande_status
                                                                  WHERE c.id_utilisateur = $id_utilisateur AND c.id_evenement = $id_evenement AND c.id_commande_type = 3 AND c.id_commande_status < 7
                                                                  GROUP BY c.id_commande");
                  if(count($infos_commande)!==0){
                    $statut = $infos_commande->str_libelle;
                    $id_commande_status = intval($infos_commande->id_commande_status);
                    $is_reservation = 0;
                    $is_commande = 1;
                    $id_commande = $infos_commande->id_commande;
                    $reservation_repas=$wpdb->get_results( 'SELECT id_repas FROM '.$wpdb->prefix.'ins_reservation_repas WHERE id_evenement='.$id_evenement.' AND id_utilisateur_exposant='.$id_utilisateur_exposant);
                    if(isset($reservation_repas) && !empty($reservation_repas) && !is_null($reservation_repas) && is_array($reservation_repas) && sizeof($reservation_repas) > 0 && $reservation_repas !== false){
                      foreach ($reservation_repas as $resa_pub) {
                        $liste_reservations_repass[] = $resa_pub->id_repas;
                      }
                    }
                  }else{
                    $reservation_repas=$wpdb->get_results( 'SELECT id_repas FROM '.$wpdb->prefix.'ins_reservation_repas WHERE id_evenement='.$id_evenement.' AND id_utilisateur_exposant='.$id_utilisateur_exposant);
                    if(isset($reservation_repas) && !empty($reservation_repas) && !is_null($reservation_repas) && is_array($reservation_repas) && sizeof($reservation_repas) > 0 && $reservation_repas !== false){
                      foreach ($reservation_repas as $resa_pub) {
                        $liste_reservations_repass[] = $resa_pub->id_repas;
                      }
                    }else{
                      $liste_reservations_repass = array();
                    }
                    if(count($reservation_repas)!==0){
                      $statut = "Enregistrée mais pas encore commandée";
                      $id_commande_status = 2;
                      $is_reservation = 1;
                      $is_commande = 0;
                      $id_commande = 0;
                    }else{
                      $statut = "Pas de réservation !";
                      $id_commande_status = 0;
                      $is_reservation = 0;
                      $is_commande = 0;
                      $id_commande = 0;
                    }
                  }
                  echo '<div class="salon_wrapper repass_wrapper" data-id_evenement="'.$salon->id_evenement.'">';
                  echo '<h4>'.$salon->str_libelle.'<button class="salon_wrapper_button" title="Afficher / Masquer ce bloc"><span aria-hidden="true" class="toggle-indicator"></span></button></h4>
                    <div class="repas recapitulatif-commandes" data-id_evenement="'.$salon->id_evenement.'" data-id_commande="'.$id_commande.'">';
                  echo '<div class="alert ';if($id_commande_status==5){echo 'alert-warning';}elseif($id_commande_status==6){echo 'alert-success';}elseif($id_commande_status==0){echo 'alert-danger';}else{echo 'alert-info';}echo '"><strong>Statut : </strong> '.$statut.'</div>';
                  if(isset($liste_repass) && !empty($liste_repass) && !is_null($liste_repass) && is_array($liste_repass) && sizeof($liste_repass) > 0 && $liste_repass !== false && $id_commande_status > 0){
                    echo '<table class="table-repas"><thead><tr><th></th>';
                    foreach ($liste_repass as $o_repas){
                      echo '<th>'.$o_repas->str_libelle.'</sup></th>';
                    }
                    echo '</tr></thead><tbody id="tab_gens" name="tab_gens">';
                    $liste_accompagnants = $wpdb->get_results( 'SELECT  * FROM '.$wpdb->prefix.'ins_utilisateur_exposant_contact WHERE id_utilisateur_exposant='.$id_utilisateur_exposant.' AND id_utilisateur_exposant_contact_type=4' );
                    foreach($liste_accompagnants as $o_accompagnant){ 
                      echo '<tr data-id_utilisateur_exposant_contact="'.$o_accompagnant->id_utilisateur_exposant_contact.'">';
                       echo '<td>'.$o_accompagnant->str_prenom.' '.$o_accompagnant->str_nom.'</td>';
                         foreach ($liste_repass as $o_repas){
                           $r_accompagnant_repas=$wpdb->get_row('SELECT  * FROM '.$wpdb->prefix.'ins_reservation_repas WHERE id_utilisateur_exposant='.$o_accompagnant->id_utilisateur_exposant.' AND id_repas='.$o_repas->id_repas.' AND bl_accompagnant=1 AND id_utilisateur_exposant_contact='.$o_accompagnant->id_utilisateur_exposant_contact);
                           if(count($r_accompagnant_repas)>0){
                             echo '<td><input type="checkbox" value="'.$o_repas->id_repas.'" checked="checked" name="id_accompagnant_'.$o_accompagnant->id_utilisateur_exposant_contact.'" id="id_accompagnant_'.$o_accompagnant->id_utilisateur_exposant_contact.'" class="checkboxRepas"></td>'; 
                           }else{
                             echo '<td><input type="checkbox" value="'.$o_repas->id_repas.'" name="id_accompagnant_'.$o_accompagnant->id_utilisateur_exposant_contact.'" id="id_accompagnant_'.$o_accompagnant->id_utilisateur_exposant_contact.'" class="checkboxRepas"></td>'; 
                           }
                         }
                      echo '</tr>';
                    }
                    echo '<tr id="btn_gens" name="btn_gens">
                              <td colspan="8">
                                  <a class="button button-secondary btn-add-accompagnant">Ajouter une nouvelle personne</a>
                              </td>
                          </tr>
                          <tr id="repas_add_personne" class="hidden"><td colspan="8">
                            <h3>Ajouter une personne</h3>
                            <div class="line_form">
                              <label for="str_a_email">Email <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Email..." id="str_a_email" name="str_a_email" class="form-control">         
                            </div>
                            <div class="line_form">
                              <label for="str_a_prenom">Prénom <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Prénom..." id="str_a_prenom" name="str_a_prenom" class="form-control">
                            </div>
                            <div class="line_form">
                              <label for="str_a_nom">Nom <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Nom..." id="str_a_nom" name="str_a_nom" class="form-control">
                            </div>
                            <div class="line_form">
                              <label for="str_a_adresse">Adresse <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Adresse..." id="str_a_adresse" name="str_a_adresse" class="form-control">
                            </div>
                            <div class="line_form">
                              <label for="str_a_codepostal">Code postal <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Code postal..." id="str_a_codepostal" name="str_a_codepostal" class="form-control">
                            </div>
                            <div class="line_form">
                              <label for="str_a_ville">Ville <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Ville..." id="str_a_ville" name="str_a_ville" class="form-control">
                            </div>
                            <div class="line_form">
                              <label for="str_a_pays">Pays <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Pays..." id="str_a_pays" name="str_a_pays" class="form-control">
                            </div>                                
                            <div class="line_form">
                              <label for="str_a_fixe">Téléphone fixe <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Téléphone fixe..." id="str_a_fixe" name="str_a_fixe">
                            </div>
                            <div class="line_form">
                              <label for="str_a_mobile">Téléphone mobile</label>
                              <input type="text" value="" placeholder="Téléphone mobile..." id="str_a_mobile" name="str_a_mobile" class="form-control">
                            </div>                                    
                            <div class="line_form">
                              <label for="str_a_fonction">Fonction <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Fonction..." id="str_a_fonction" name="str_a_fonction" class="form-control">
                            </div>
                            <div class="line_form">
                              <button class="button button-primary btn-add-accompagnant">Ajouter</button>
                            </div>
                          </td></tr>
                        </tbody>
                    </table>';
                    echo '<div class="reservation-stand-actions-wrapper"';if(empty($is_reservation)){echo ' style="display:none"';} echo '> 
                                <hr/>
                                <h4>Actions sur la réservation</h4>
                                <div class="reservation-stand-actions">
                                  <div class="reservation-stand-buttons-actions">
                                    <button type="button" class="btn btn-danger btn-supprimer-reservation">Supprimer la réservation</button>
                                    <!--<button type="button" class="btn btn-primary btn-creer-reservation">Créer la réservation</button>-->
                                    <button type="button" class="btn btn-warning btn-forcer-commande">Transformer la réservation en commande</button>
                                  </div>
                                </div>
                              </div>
                              <div class="commande-stand-actions-wrapper"';if(empty($is_commande)){echo ' style="display:none"';} echo '>
                                <hr/>
                                <h4>Actions sur la commande</h4>
                                <div class="commande-stand-actions">
                                <div class="reservation-stand-buttons-actions">
                                    <button type="button" class="btn btn-danger btn-annuler-commande">Annuler la commande</button>
                                    <button type="button" class="btn btn-primary btn-paiement-recu"';if($id_commande_status!==1){echo ' disabled="disabled"';}echo '>Paiement reçu</button>
                                    <button type="button" class="btn btn-warning btn-confirmer-reservation"';if($id_commande_status!==4){echo ' disabled="disabled"';}echo '>Confirmer la commande</button>
                                  </div>
                                </div>
                              </div></div></div>';
                  }else{
                    // Pas de réservation
                    if(intval(date('Y')) == intval($salon->str_annee)){
                      echo '<table class="table-repas"><thead><tr><th></th>';
                    foreach ($liste_repass as $o_repas){
                      echo '<th>'.$o_repas->str_libelle.'</sup></th>';
                    }
                    echo '</tr></thead><tbody id="tab_gens" name="tab_gens">';
                    $liste_accompagnants = $wpdb->get_results( 'SELECT  * FROM '.$wpdb->prefix.'ins_utilisateur_exposant_contact WHERE id_utilisateur_exposant='.$id_utilisateur_exposant.' AND id_utilisateur_exposant_contact_type=4' );
                    foreach($liste_accompagnants as $o_accompagnant){ 
                      echo '<tr data-id_utilisateur_exposant_contact="'.$o_accompagnant->id_utilisateur_exposant_contact.'">';
                       echo '<td>'.$o_accompagnant->str_prenom.' '.$o_accompagnant->str_nom.'</td>';
                         foreach ($liste_repass as $o_repas){
                           $r_accompagnant_repas=$wpdb->get_row('SELECT  * FROM '.$wpdb->prefix.'ins_reservation_repas WHERE id_utilisateur_exposant='.$o_accompagnant->id_utilisateur_exposant.' AND id_repas='.$o_repas->id_repas.' AND bl_accompagnant=1 AND id_utilisateur_exposant_contact='.$o_accompagnant->id_utilisateur_exposant_contact);
                           if(count($r_accompagnant_repas)>0){
                             echo '<td><input type="checkbox" value="'.$o_repas->id_repas.'" checked="checked" name="id_accompagnant_'.$o_accompagnant->id_utilisateur_exposant_contact.'" id="id_accompagnant_'.$o_accompagnant->id_utilisateur_exposant_contact.'" class="checkboxRepas"></td>'; 
                           }else{
                             echo '<td><input type="checkbox" value="'.$o_repas->id_repas.'" name="id_accompagnant_'.$o_accompagnant->id_utilisateur_exposant_contact.'" id="id_accompagnant_'.$o_accompagnant->id_utilisateur_exposant_contact.'" class="checkboxRepas"></td>'; 
                           }
                         }
                      echo '</tr>';
                    }
                    echo '<tr id="btn_gens" name="btn_gens">
                              <td colspan="8">
                                  <a class="button button-secondary btn-add-accompagnant">Ajouter une nouvelle personne</a>
                              </td>
                          </tr>
                          <tr id="repas_add_personne" class="hidden"><td colspan="8">
                            <h3>Ajouter une personne</h3>
                            <div class="line_form">
                              <label for="str_a_email">Email <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Email..." id="str_a_email" name="str_a_email" class="form-control">         
                            </div>
                            <div class="line_form">
                              <label for="str_a_prenom">Prénom <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Prénom..." id="str_a_prenom" name="str_a_prenom" class="form-control">
                            </div>
                            <div class="line_form">
                              <label for="str_a_nom">Nom <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Nom..." id="str_a_nom" name="str_a_nom" class="form-control">
                            </div>
                            <div class="line_form">
                              <label for="str_a_adresse">Adresse <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Adresse..." id="str_a_adresse" name="str_a_adresse" class="form-control">
                            </div>
                            <div class="line_form">
                              <label for="str_a_codepostal">Code postal <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Code postal..." id="str_a_codepostal" name="str_a_codepostal" class="form-control">
                            </div>
                            <div class="line_form">
                              <label for="str_a_ville">Ville <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Ville..." id="str_a_ville" name="str_a_ville" class="form-control">
                            </div>
                            <div class="line_form">
                              <label for="str_a_pays">Pays <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Pays..." id="str_a_pays" name="str_a_pays" class="form-control">
                            </div>                                
                            <div class="line_form">
                              <label for="str_a_fixe">Téléphone fixe <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Téléphone fixe..." id="str_a_fixe" name="str_a_fixe">
                            </div>
                            <div class="line_form">
                              <label for="str_a_mobile">Téléphone mobile</label>
                              <input type="text" value="" placeholder="Téléphone mobile..." id="str_a_mobile" name="str_a_mobile" class="form-control">
                            </div>                                    
                            <div class="line_form">
                              <label for="str_a_fonction">Fonction <span style="color:red;">*</span></label>
                              <input type="text" value="" placeholder="Fonction..." id="str_a_fonction" name="str_a_fonction" class="form-control">
                            </div>
                            <div class="line_form">
                              <button class="button button-primary btn-add-accompagnant">Ajouter</button>
                            </div>
                          </td></tr>
                        </tbody>
                    </table>';
                      echo '<div class="reservation-stand-actions-wrapper"> 
                                <hr/>
                                <h4>Actions sur la réservation</h4>
                                <div class="reservation-stand-actions">
                                  <div class="reservation-stand-buttons-actions">
                                    <button type="button" class="btn btn-primary btn-creer-reservation">Créer la réservation</button>
                                  </div>
                                </div>
                              </div>
                              </div></div>';
                    }
                  }
                }
              }
            ?>
          </div>
        </div>
        <hr/>
        <!-- RÉCAPITULATIF DES COMMANDES -->
        <div id="atm_exposant_sectionid" class="postbox">
          <button aria-expanded="true" class="handlediv button-link" type="button"><span class="screen-reader-text">Ouvrir/fermer le bloc Publier</span><span aria-hidden="true" class="toggle-indicator"></span></button>
          <h3 class="hndle">Récapitulatif des commandes</h3>
          <div class="inside">
            <?php 
              // On récupère la liste des salons HOPITECH
              $liste_salons = $wpdb->get_results("SELECT id_evenement, str_libelle, str_annee FROM $table_ins_evenement ORDER BY date_ouverture_exposant DESC");
              if(isset($liste_salons) && !empty($liste_salons) && !is_null($liste_salons) && is_array($liste_salons) && sizeof($liste_salons) > 0 && $liste_salons !== false){
                echo '<div class="salon_wrapper" data-id_evenement="'.$salon->id_evenement.'">';
                echo '<h4>'.$salon->str_libelle.'<button class="salon_wrapper_button" title="Afficher / Masquer ce bloc"><span aria-hidden="true" class="toggle-indicator"></span></button></h4>';
                foreach ($liste_salons as $salon) {
                  $id_evenement = $salon->id_evenement;
            ?>
            <div class="recapitulatif-commandes">
              <div class="commande">
                <div class="commande-entete-wrapper">
                  <table>
                    <thead>
                        <tr>
                            <th>Référence Commande</th>
                            <th>Type</th>
                            <th>Créée le</th>
                            <th>Total (TTC)</th>
                            <th>Acompte (30 %)</th>
                            <th>Statut</th>
                            <th>Paiement</th>
                            <th>Téléchargements PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php echo atm_get_recapitulatif_commandes(0, $infos_exposant->id_utilisateur, $id_evenement); ?>
                    </tbody>
                  </table>
                </div>
              </div>  
            </div>
            <?php
              }
              echo '</div>';
            }
            ?>
          </div>
        </div>
      <?php endif; ?>
      </div>
      <div id="postbox-container-1" class="postbox-container">
        <div class="meta-box-sortables ui-sortable" id="side-sortables" style="">
          <div class="postbox " id="submitdiv">
            <div title="Cliquer pour inverser." class="handlediv"><br></div>
            <h3 class="hndle ui-sortable-handle"><span>Sauvegarder</span></h3>
            <div class="inside">
              <div id="submitpost" class="submitbox">
                <div id="major-publishing-actions">
                   <?php
                    // Add an nonce field so we can check for it later.
                    wp_nonce_field( 'atm_add_exposant_nonce', 'atm_add_exposant_nonce' );
                  ?>
                  <?php if(isset($_GET['action']) && $_GET['action'] == 'edit'): ?>
                    <input type="hidden" name="atm_action" value="edit-exposant" />
                    <input type="hidden" name="id_utilisateur_exposant" value="<?php echo intval(atm_sanitize($_GET['exposant'])); ?>" />
                  <?php else: ?>
                    <input type="hidden" name="atm_action" value="add-new-exposant" />
                  <?php endif; ?>
                  <?php  if(isset($_GET['exposant']) && !empty($_GET['exposant']) && !is_null($_GET['exposant']) ):  ?>
                  <div id="delete-action"><a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=atm_cpt_add_exposant<?php if(isset($_GET['exposant']) && !empty($_GET['exposant']) && !is_null($_GET['exposant']) ){echo '&exposant='.intval($_GET['exposant']);} ?><?php if(isset($_GET['action']) && $_GET['action'] == 'edit'){echo '&action=edit';} ?>&atm_action=delete&_wp_nonce=<?php echo wp_create_nonce( 'delete-exposant-nonce' ); ?>" class="submitdelete deletion">Supprimer cet exposant</a></div>
                  <?php endif; ?>
                  <div id="publishing-action">
                    <span class="spinner"></span>
                    <?php if(isset($_GET['action']) && $_GET['action'] == 'edit'): ?>
                        <input type="submit" value="Mettre à jour" class="button button-primary button-large" id="publish" name="atm_status_action">
                    <?php else: ?>
                      <input type="submit" value="Publier" class="button button-primary button-large" id="publish" name="atm_status_action">
                    <?php endif; ?>
                  </div>
                  <div class="clear"></div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
    </form>
  </div>
</div>
<script>
/**
 * Retourne le nombre d'éléments contenus dans un objet
 *
 * @param {Object} obj
 * @return {Int} size
 */
Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

// Vérifie la validité d'un email
function isEmail(email){
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

    if (!filter.test(email)) {
      return false;
    }else{
      return true;
    }
 }

// On change la balise title si en mode "Edition"
var pattern = /action=edit/g;
var is_edit_mode = pattern.test(window.location.search);
if(is_edit_mode){
  var titre = document.title;
  titre = titre.replace('Ajouter','Modifier');
  document.title = titre;
}
jQuery(function($){
  $('.commande-entete-wrapper').on('click','a.details',function(e){
    e.preventDefault();
    $(this).closest('tr').next('tr').toggleClass('hidden');
  });
});
jQuery('.submitdelete.deletion').on('click',function(e){
  if(!confirm("Êtes-vous sûr de vouloir supprimer cet exposant ?")){
    e.preventDefault();
    return false;
  }
});
jQuery('#bl_responsable_identique, #bl_facturation_identique, #bl_correspondant_identique').on('change',function(e){
  e.preventDefault();
  jQuery(this).closest('.line_form').next().toggleClass('hidden');
});
jQuery(function($){
  $('#post-body-content').on('click','.button-link',function(e){
    e.preventDefault();
    $(this).parent().find('.inside').slideToggle();
    $(this).closest('.postbox').toggleClass('closed');
  });

  $('.salon_wrapper_button').on('click',function(e){
    e.preventDefault();
    $(this).closest('.salon_wrapper').toggleClass('closed');
  });

 $('.repass_wrapper').on('click', '.btn-add-accompagnant',function(e){
    e.preventDefault();
    $(this).closest('.repass_wrapper').find('#repas_add_personne').toggleClass('hidden');
  });

  // Vérification des champs avant envoi
  /*$('#submitpost').on('click','#publish, #update, #draft, #pending',function(e){
    $('#message').remove();

    var errors = {};
    
    // Nom
    var str_nom = $('#str_nom').val();
    if(str_nom ==''){errors["str_nom"] = '<p>Vous devez entrer le <strong>nom</strong> de l\'exposant.</p>';}

    // Prénom
    var str_prenom = $('#str_prenom').val();
    if(str_prenom ==''){errors["str_prenom"] = '<p>Vous devez entrer le <strong>prénom</strong> de l\'exposant.</p>';}

    // Email
    var str_email = $('#str_email').val();
    if(str_email ==''){errors["str_email"] = '<p>Vous devez entrer l\'<strong>email</strong> de l\'exposant.</p>';}
    if(!isEmail(str_email)){errors["str_email"] = '<p>Cet <strong>email</strong> n\'est pas valide.</p>';}

    // Téléphone fixe
    var str_fixe = $('#str_fixe').val();
    if(str_fixe ==''){errors["str_fixe"] = '<p>Vous devez entrer le <strong>numéro de téléphone fixe</strong> de l\'exposant.</p>';}

    // Adresse
    var str_adresse = $('#str_adresse').val();
    if(str_adresse ==''){errors["str_adresse"] = '<p>Vous devez entrer l\'<strong>adresse</strong> de l\'exposant.</p>';}

    // Code Postal
    var str_codepostal = $('#str_codepostal').val();
    if(str_codepostal ==''){errors["str_codepostal"] = '<p>Vous devez entrer le <strong>code postal</strong> de l\'exposant.</p>';}

    // Ville
    var str_ville = $('#str_ville').val();
    if(str_ville ==''){errors["str_ville"] = '<p>Vous devez entrer la <strong>ville</strong> de l\'exposant.</p>';}

    // Pays
    var str_pays = $('#str_pays').val();
    if(str_pays ==''){errors["str_pays"] = '<p>Vous devez entrer le <strong>pays</strong> de l\'exposant.</p>';}

    var sizeErrors = Object.size(errors);
    
    if(sizeErrors > 0){
      var message = '';

      $.each( errors, function( key, value ) {
            console.log( "Erreur : "+key + ": " + value );
            message += '<p>'+value+'</p>';
            $('#post-body').find('#'+key).addClass('champ-erreur');
          });

      $('.wrap > h2').after('<div class="error below-h2" id="message">'+message+'</div>');
      $("html, body").animate({ scrollTop: 0 }, "slow");
      return false;
    }

  });*/

  /**
   *
   *    REQUÊTES AJAX
   *
   */
  var _path = window.location.protocol+'//'+window.location.hostname+'/wp-admin/admin-ajax.php';
  
  //  -----------------------------
  //  RÉSERVATIONS STAND
  //  -----------------------------

  // Créer la réservation stand
  $('.stand').on('click','.btn-creer-reservation',function(e){
    e.preventDefault();

    if (confirm("Êtes-vous sûr de vouloir créer une réservation pour ce client ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
      var id_evenement = parseInt($(this).closest('.stand').attr('data-id_evenement'),10);

      var id_dimension_stand = parseInt($(this).closest('.stand').find('select[name="id_dimension_stand"]').val(),10);
      var str_choix_1 = $(this).closest('.stand').find('input[name="str_choix_1"]').val();
      var str_choix_2 = $(this).closest('.stand').find('input[name="str_choix_2"]').val();
      var str_choix_3 = $(this).closest('.stand').find('input[name="str_choix_3"]').val();
      var str_choix_4 = $(this).closest('.stand').find('input[name="str_choix_4"]').val();
      var str_enseigne = $(this).closest('.stand').find('input[name="str_enseigne"]').val();

      if(!isNaN(id_evenement) && id_evenement>0){
        if(!isNaN(id_dimension_stand) && id_dimension_stand>0 && str_choix_1!=='' && str_choix_2!=='' && str_choix_3!=='' && str_choix_4!=='' && str_enseigne!==''){
          $.ajax({
              type: 'post',
              url: _path,
              data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, id_dimension_stand:id_dimension_stand, str_choix_1:str_choix_1, str_choix_2:str_choix_2, str_choix_3:str_choix_3, str_choix_4:str_choix_4, str_enseigne:str_enseigne, atm_back_action:'atm_hopitech_bo_stand_reservation_creer_reservation',atm_back_referrer:window.location.hostname},
              success: function(_data){
                  var result = $.parseJSON(_data);
                  if(result.success){
                    console.info('Créer une réservation');
                    window.location.reload();
                  }else{
                    console.error(result.message);
                  }
              }
          });
        }else{
          alert("Tous les champs ne sont pas correctement remplis !");
        }
      }
    }
  });

  // Supprimer la réservation stand
  $('.stand').on('click','.btn-supprimer-reservation',function(e){
    e.preventDefault();
    if (confirm("Êtes-vous sûr de vouloir SUPPRIMER la réservation ? (Cette action est irréversible)")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_evenement = parseInt($(this).closest('.stand').attr('data-id_evenement'),10);
      var id_reservation_stand = parseInt($(this).closest('.stand').attr('data-id_reservation_stand'),10);

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_reservation_stand) && id_reservation_stand>0){
        $.ajax({
            type: 'post',
            url: _path,
            data: {id_utilisateur:id_utilisateur, id_evenement:id_evenement, id_reservation_stand:id_reservation_stand, atm_back_action:'atm_hopitech_bo_stand_reservation_supprimer',atm_back_referrer:window.location.hostname},
            success: function(_data){
                var result = $.parseJSON(_data);
                if(result.success){
                  console.info('Supprimer la réservation');
                  window.location.reload();
                }else{
                  console.error(result.message);
                }
            }
        });
      }
    }
  });

  // Transformer la réservation de stand en commande
  $('.stand').on('click','.btn-forcer-commande',function(e){
    e.preventDefault();

    if (confirm("Êtes-vous sûr de vouloir transformer la réservation de ce client en commande ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_evenement = parseInt($(this).closest('.stand').attr('data-id_evenement'),10);
      var id_reservation_stand = parseInt($(this).closest('.stand').attr('data-id_reservation_stand'),10);

      var id_dimension_stand = parseInt($(this).closest('.stand').find('select[name="id_dimension_stand"]').val(),10);
      var str_choix_1 = $(this).closest('.stand').find('input[name="str_choix_1"]').val();
      var str_choix_2 = $(this).closest('.stand').find('input[name="str_choix_2"]').val();
      var str_choix_3 = $(this).closest('.stand').find('input[name="str_choix_3"]').val();
      var str_choix_4 = $(this).closest('.stand').find('input[name="str_choix_4"]').val();
      var str_enseigne = $(this).closest('.stand').find('input[name="str_enseigne"]').val();

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_reservation_stand) && id_reservation_stand>0){
        if(!isNaN(id_dimension_stand) && id_dimension_stand>0 && str_choix_1!=='' && str_choix_2!=='' && str_choix_3!=='' && str_choix_4!=='' && str_enseigne!==''){
          $.ajax({
              type: 'post',
              url: _path,
              data: {id_utilisateur:id_utilisateur, id_evenement:id_evenement, id_reservation_stand:id_reservation_stand, id_dimension_stand:id_dimension_stand, str_choix_1:str_choix_1, str_choix_2:str_choix_2, str_choix_3:str_choix_3, str_choix_4:str_choix_4, str_enseigne:str_enseigne, atm_back_action:'atm_hopitech_bo_stand_reservation_forcer_commande',atm_back_referrer:window.location.hostname},
              success: function(_data){
                  var result = $.parseJSON(_data);
                  if(result.success){
                    console.info('Transformer la réservation en commande');
                    window.location.reload();
                  }else{
                    console.error(result.message);
                  }
              }
          });
        }else{
          alert("Tous les champs ne sont pas correctement remplis !");
        }
      }
    }
  });


  //  -----------------------------
  //  COMMANDES STAND
  //  -----------------------------

  // Annuler la commande de stand
  $('.stand').on('click','.btn-annuler-commande',function(e){
    e.preventDefault();

    if (confirm("Êtes-vous sûr de vouloir ANNULER la commande ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_evenement = parseInt($(this).closest('.stand').attr('data-id_evenement'),10);
      var id_reservation_stand = parseInt($(this).closest('.stand').attr('data-id_reservation_stand'),10);
      var id_commande = parseInt($(this).closest('.stand').attr('data-id_commande'),10);
      var id_commande_ligne = parseInt($(this).closest('.stand').attr('data-id_commande_ligne'),10);

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_reservation_stand) && id_reservation_stand>0 && !isNaN(id_commande) && id_commande>0 && !isNaN(id_commande_ligne) && id_commande_ligne>0){
        $.ajax({
            type: 'post',
            url: _path,
            data: {id_utilisateur:id_utilisateur, id_evenement:id_evenement, id_reservation_stand:id_reservation_stand, id_commande:id_commande, id_commande_ligne:id_commande_ligne, atm_back_action:'atm_hopitech_bo_stand_commande_annuler',atm_back_referrer:window.location.hostname},
            success: function(_data){
                var result = $.parseJSON(_data);
                if(result.success){
                  console.info('Annuler la commande');
                  window.location.reload();
                }else{
                  console.error(result.message);
                }
            }
        });
      }
    }
  });

  // Paiement reçu de la commande de stand
  $('.stand').on('click','.btn-acompte-recu',function(e){
    e.preventDefault();

    if (confirm("Avez-vous bien reçu l'acompte ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_evenement = parseInt($(this).closest('.stand').attr('data-id_evenement'),10);
      var id_reservation_stand = parseInt($(this).closest('.stand').attr('data-id_reservation_stand'),10);
      var id_commande = parseInt($(this).closest('.stand').attr('data-id_commande'),10);
      var id_commande_ligne = parseInt($(this).closest('.stand').attr('data-id_commande_ligne'),10);

      var id_dimension_stand = parseInt($(this).closest('.stand').find('select[name="id_dimension_stand"]').val(),10);
      var str_choix_1 = $(this).closest('.stand').find('input[name="str_choix_1"]').val();
      var str_choix_2 = $(this).closest('.stand').find('input[name="str_choix_2"]').val();
      var str_choix_3 = $(this).closest('.stand').find('input[name="str_choix_3"]').val();
      var str_choix_4 = $(this).closest('.stand').find('input[name="str_choix_4"]').val();
      var str_enseigne = $(this).closest('.stand').find('input[name="str_enseigne"]').val();

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_reservation_stand) && id_reservation_stand>0 && !isNaN(id_commande) && id_commande>0 && !isNaN(id_commande_ligne) && id_commande_ligne>0){
        if(!isNaN(id_dimension_stand) && id_dimension_stand>0 && str_choix_1!=='' && str_choix_2!=='' && str_choix_3!=='' && str_choix_4!=='' && str_enseigne!==''){
          $.ajax({
              type: 'post',
              url: _path,
              data: {id_utilisateur:id_utilisateur, id_evenement:id_evenement, id_reservation_stand:id_reservation_stand, id_commande:id_commande, id_commande_ligne:id_commande_ligne, id_dimension_stand:id_dimension_stand, str_choix_1:str_choix_1, str_choix_2:str_choix_2, str_choix_3:str_choix_3, str_choix_4:str_choix_4, str_enseigne:str_enseigne, atm_back_action:'atm_hopitech_bo_stand_commande_acompte_recu',atm_back_referrer:window.location.hostname},
              success: function(_data){
                  var result = $.parseJSON(_data);
                  if(result.success){
                    console.info('Paiement reçu');
                    window.location.reload();
                  }else{
                    console.error(result.message);
                  }
              }
          });
        }else{
          alert("Tous les champs ne sont pas correctement remplis !");
        }
      }
    }
  });

  // Confimation de réservation de stand
  $('.stand').on('click','.btn-confirmer-reservation',function(e){
    e.preventDefault();

    if (confirm("Êtes-vous sûr de vouloir confirmer la commande ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_evenement = parseInt($(this).closest('.stand').attr('data-id_evenement'),10);
      var id_reservation_stand = parseInt($(this).closest('.stand').attr('data-id_reservation_stand'),10);
      var id_commande = parseInt($(this).closest('.stand').attr('data-id_commande'),10);
      var id_commande_ligne = parseInt($(this).closest('.stand').attr('data-id_commande_ligne'),10);
      var str_stand = $(this).closest('.stand').find('input[name="str_stand"]').val();

      var id_dimension_stand = parseInt($(this).closest('.stand').find('select[name="id_dimension_stand"]').val(),10);
      var str_choix_1 = $(this).closest('.stand').find('input[name="str_choix_1"]').val();
      var str_choix_2 = $(this).closest('.stand').find('input[name="str_choix_2"]').val();
      var str_choix_3 = $(this).closest('.stand').find('input[name="str_choix_3"]').val();
      var str_choix_4 = $(this).closest('.stand').find('input[name="str_choix_4"]').val();
      var str_enseigne = $(this).closest('.stand').find('input[name="str_enseigne"]').val();

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_reservation_stand) && id_reservation_stand>0 && !isNaN(id_commande) && id_commande>0 && !isNaN(id_commande_ligne) && id_commande_ligne>0){
        if(!isNaN(id_dimension_stand) && id_dimension_stand>0 && str_choix_1!=='' && str_choix_2!=='' && str_choix_3!=='' && str_choix_4!=='' && str_enseigne!=='' && str_stand!==''){
          $.ajax({
              type: 'post',
              url: _path,
              data: {id_utilisateur:id_utilisateur, id_evenement:id_evenement, id_reservation_stand:id_reservation_stand, id_commande:id_commande, id_commande_ligne:id_commande_ligne, str_stand:str_stand, id_dimension_stand:id_dimension_stand, str_choix_1:str_choix_1, str_choix_2:str_choix_2, str_choix_3:str_choix_3, str_choix_4:str_choix_4, str_enseigne:str_enseigne, atm_back_action:'atm_hopitech_bo_stand_commande_confirmation',atm_back_referrer:window.location.hostname},
              success: function(_data){
                  var result = $.parseJSON(_data);
                  if(result.success){
                    console.info('Confimation de réservation');
                    window.location.reload();
                  }else{
                    console.error(result.message);
                  }
              }
          });
        }else{
          alert("Tous les champs ne sont pas correctement remplis !");
        }
      }
    }
  });

  // Règlement effectué - réservation de stand
  $('.stand').on('click','.btn-reglement-effectue',function(e){
    e.preventDefault();

    if (confirm("Avez-vous bien reçu le règlement complet de la commande ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_evenement = parseInt($(this).closest('.stand').attr('data-id_evenement'),10);
      var id_reservation_stand = parseInt($(this).closest('.stand').attr('data-id_reservation_stand'),10);
      var id_commande = parseInt($(this).closest('.stand').attr('data-id_commande'),10);
      var id_commande_ligne = parseInt($(this).closest('.stand').attr('data-id_commande_ligne'),10);

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_reservation_stand) && id_reservation_stand>0 && !isNaN(id_commande) && id_commande>0 && !isNaN(id_commande_ligne) && id_commande_ligne>0){
        $.ajax({
          type: 'post',
          url: _path,
          data: {id_utilisateur:id_utilisateur, id_evenement:id_evenement, id_reservation_stand:id_reservation_stand, id_commande:id_commande, id_commande_ligne:id_commande_ligne, atm_back_action:'atm_hopitech_bo_stand_reglement_effectue',atm_back_referrer:window.location.hostname},
          success: function(_data){
              var result = $.parseJSON(_data);
              if(result.success){
                console.info('Règlement effectué');
                window.location.reload();
              }else{
                console.error(result.message);
              }
          }
        });
      }
    }
  });

  //  -----------------------------
  //  RÉSERVATIONS PUBLICITES
  //  -----------------------------

  // Créer la réservation publicite
  $('.publicite').on('click','.btn-creer-reservation',function(e){
    e.preventDefault();

    if (confirm("Êtes-vous sûr de vouloir créer une réservation pour ce client ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
      var id_evenement = parseInt($(this).closest('.publicite').attr('data-id_evenement'),10);

      var ids_publicite = [];

      $(this).closest('.publicite').find('.checkbox').each(function(){
        if($(this).prop('checked')){
          ids_publicite.push($(this).val());
        }
      });

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_utilisateur) && id_utilisateur>0){
        if(ids_publicite.length > 0){
          $.ajax({
              type: 'post',
              url: _path,
              data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, ids_publicite:ids_publicite, atm_back_action:'atm_hopitech_bo_publicite_reservation_creer_reservation',atm_back_referrer:window.location.hostname},
              success: function(_data){
                  var result = $.parseJSON(_data);
                  if(result.success){
                    console.info('Créer une réservation');
                    window.location.reload();
                  }else{
                    console.error(result.message);
                  }
              }
          });
        }else{
          alert("Tous les champs ne sont pas correctement remplis !");
        }
      }
    }
  });

  // Supprimer la réservation publicite
  $('.publicite').on('click','.btn-supprimer-reservation',function(e){
    e.preventDefault();
    if (confirm("Êtes-vous sûr de vouloir SUPPRIMER la réservation ? (Cette action est irréversible)")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
      var id_evenement = parseInt($(this).closest('.publicite').attr('data-id_evenement'),10);

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_utilisateur) && id_utilisateur>0){
        $.ajax({
            type: 'post',
            url: _path,
            data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, atm_back_action:'atm_hopitech_bo_publicite_reservation_supprimer',atm_back_referrer:window.location.hostname},
            success: function(_data){
                var result = $.parseJSON(_data);
                if(result.success){
                  console.info('Supprimer la réservation');
                  window.location.reload();
                }else{
                  console.error(result.message);
                }
            }
        });
      }
    }
  });

  // Transformer la réservation de publicite en commande
  $('.publicite').on('click','.btn-forcer-commande',function(e){
    e.preventDefault();

    if (confirm("Êtes-vous sûr de vouloir transformer la réservation de ce client en commande ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
      var id_evenement = parseInt($(this).closest('.publicite').attr('data-id_evenement'),10);

      var ids_publicite = [];

      $(this).closest('.publicite').find('.checkbox').each(function(){
        if($(this).prop('checked')){
          ids_publicite.push($(this).val());
        }
      });

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_utilisateur) && id_utilisateur>0){
        if(ids_publicite.length > 0){
          $.ajax({
              type: 'post',
              url: _path,
              data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, ids_publicite:ids_publicite, atm_back_action:'atm_hopitech_bo_publicite_reservation_forcer_commande',atm_back_referrer:window.location.hostname},
              success: function(_data){
                  var result = $.parseJSON(_data);
                  if(result.success){
                    console.info('Transformer la réservation en commande');
                    window.location.reload();
                  }else{
                    console.error(result.message);
                  }
              }
          });
        }else{
          alert("Tous les champs ne sont pas correctement remplis !");
        }
      }
    }
  });


  //  -----------------------------
  //  COMMANDES PUBLICITES
  //  -----------------------------

  // Annuler la commande de publicite
  $('.publicite').on('click','.btn-annuler-commande',function(e){
    e.preventDefault();

    if (confirm("Êtes-vous sûr de vouloir ANNULER la commande ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
      var id_evenement = parseInt($(this).closest('.publicite').attr('data-id_evenement'),10);
      var id_commande = parseInt($(this).closest('.publicite').attr('data-id_commande'),10);

      var ids_publicite = [];

      $(this).closest('.publicite').find('.checkbox').each(function(){
        if($(this).prop('checked')){
          ids_publicite.push($(this).val());
        }
      });

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_commande) && id_commande>0 && ids_publicite.length > 0){
        $.ajax({
            type: 'post',
            url: _path,
            data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, id_commande:id_commande, ids_publicite:ids_publicite, atm_back_action:'atm_hopitech_bo_publicite_commande_annuler',atm_back_referrer:window.location.hostname},
            success: function(_data){
                var result = $.parseJSON(_data);
                if(result.success){
                  console.info('Annuler la commande');
                  window.location.reload();
                }else{
                  console.error(result.message);
                }
            }
        });
      }
    }
  });

  // Acompte reçu de la commande de publicite
  $('.publicite').on('click','.btn-acompte-recu',function(e){
    e.preventDefault();

    if (confirm("Avez-vous bien reçu l'acompte ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
      var id_evenement = parseInt($(this).closest('.publicite').attr('data-id_evenement'),10);
      var id_commande = parseInt($(this).closest('.publicite').attr('data-id_commande'),10);

      var ids_publicite = [];

      $(this).closest('.publicite').find('.checkbox').each(function(){
        if($(this).prop('checked')){
          ids_publicite.push($(this).val());
        }
      });

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_commande) && id_commande>0){
        if(ids_publicite.length > 0){
          $.ajax({
              type: 'post',
              url: _path,
              data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, id_commande:id_commande, ids_publicite:ids_publicite, atm_back_action:'atm_hopitech_bo_publicite_commande_acompte_recu',atm_back_referrer:window.location.hostname},
              success: function(_data){
                  var result = $.parseJSON(_data);
                  if(result.success){
                    console.info('Acompte reçu');
                    window.location.reload();
                  }else{
                    console.error(result.message);
                  }
              }
          });
        }else{
          alert("Vous n'avez coché aucune case...");
        }
      }
    }
  });

  // Confimation de réservation de publicite
  $('.publicite').on('click','.btn-confirmer-reservation',function(e){
    e.preventDefault();

    if (confirm("Êtes-vous sûr de vouloir confirmer la commande ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
      var id_evenement = parseInt($(this).closest('.publicite').attr('data-id_evenement'),10);
      var id_commande = parseInt($(this).closest('.publicite').attr('data-id_commande'),10);

      var ids_publicite = [];

      $(this).closest('.publicite').find('.checkbox').each(function(){
        if($(this).prop('checked')){
          ids_publicite.push($(this).val());
        }
      });

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_commande) && id_commande>0){
        if(ids_publicite.length > 0){
          $.ajax({
              type: 'post',
              url: _path,
              data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, id_commande:id_commande, ids_publicite:ids_publicite, atm_back_action:'atm_hopitech_bo_publicite_commande_confirmation',atm_back_referrer:window.location.hostname},
              success: function(_data){
                  var result = $.parseJSON(_data);
                  if(result.success){
                    console.info('Confimation de réservation');
                    window.location.reload();
                  }else{
                    console.error(result.message);
                  }
              }
          });
        }else{
          alert("Vous n'avez coché aucune case...");
        }
      }
    }
  });
  
  // Règlement effectué - réservation de publicité
  $('.publicite').on('click','.btn-reglement-effectue',function(e){
    e.preventDefault();

    if (confirm("Avez-vous bien reçu le règlement complet de la commande ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_evenement = parseInt($(this).closest('.publicite').attr('data-id_evenement'),10);
      var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
      var id_commande = parseInt($(this).closest('.publicite').attr('data-id_commande'),10);

      var ids_publicite = [];

      $(this).closest('.publicite').find('.checkbox').each(function(){
        if($(this).prop('checked')){
          ids_publicite.push($(this).val());
        }
      });

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_commande) && id_commande>0){
        if(ids_publicite.length > 0){
          $.ajax({
            type: 'post',
            url: _path,
            data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, id_commande:id_commande, ids_publicite:ids_publicite, atm_back_action:'atm_hopitech_bo_publicite_reglement_effectue',atm_back_referrer:window.location.hostname},
            success: function(_data){
                var result = $.parseJSON(_data);
                if(result.success){
                  console.info('Règlement effectué');
                  window.location.reload();
                }else{
                  console.error(result.message);
                }
            }
          });
        }else{
          alert("Vous n'avez coché aucune case...");
        }
      }
    }
  });

  
  //  -----------------------------
  //  RÉSERVATIONS REPAS
  //  -----------------------------

  // Ajouter une personne  
   $('.repas').on('click','.btn-add-personne',function(e){
    e.preventDefault();
  });

   // Ajouter une personne  
   $('#repas_add_personne').on('click','.btn-add-accompagnant',function(e){
    e.preventDefault();
    var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
    var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
    var id_evenement = parseInt($(this).closest('.repas').attr('data-id_evenement'),10);

    var data = {};
    var str_a_email = $('#str_a_email').val();
    data.email = str_a_email;
    var str_a_prenom = $('#str_a_prenom').val();
    data.prenom = str_a_prenom;
    var str_a_nom = $('#str_a_nom').val();
    data.nom = str_a_nom;
    var str_a_adresse = $('#str_a_adresse').val();
    data.adresse = str_a_adresse;
    var str_a_codepostal = $('#str_a_codepostal').val();
    data.codepostal = str_a_codepostal;
    var str_a_ville = $('#str_a_ville').val();
    data.ville = str_a_ville;
    var str_a_pays = $('#str_a_pays').val();
    data.pays = str_a_pays;
    var str_a_fixe = $('#str_a_fixe').val();
    data.fixe = str_a_fixe;
    var str_a_mobile = $('#str_a_mobile').val();
    data.mobile = str_a_mobile;
    var str_a_fonction = $('#str_a_fonction').val();
    data.fonction = str_a_fonction;

    if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_utilisateur) && id_utilisateur>0 && str_a_email!=='' && str_a_prenom!=='' && str_a_nom!=='' && str_a_adresse!=='' && str_a_codepostal!=='' && str_a_ville!=='' && str_a_pays!=='' && str_a_fixe!=='' && str_a_fonction!==''){
      $.ajax({
          type: 'post',
          url: _path,
          data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, data:data, atm_back_action:'atm_hopitech_bo_repas_reservation_ajouter_acompagnant',atm_back_referrer:window.location.hostname},
          success: function(_data){
              var result = $.parseJSON(_data);
              if(result.success){
                console.info('Ajouter un accompagnant au repas');
                $('#str_a_email').val('');
                $('#str_a_prenom').val('');
                $('#str_a_nom').val('');
                $('#str_a_adresse').val('');
                $('#str_a_codepostal').val('');
                $('#str_a_ville').val('');
                $('#str_a_pays').val('');
                $('#str_a_fixe').val('');
                $('#str_a_mobile').val('');
                $('#str_a_fonction').val('');
                window.location.reload();
              }else{
                console.error(result.message);
              }
          }
      });
    }else{
      alert("Tous les champs ne sont pas correctement remplis !");
    }
  });
   
  // Créer la réservation repas
  $('.repas').on('click','.btn-creer-reservation',function(e){
    e.preventDefault();

    if (confirm("Êtes-vous sûr de vouloir créer une réservation pour ce client ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
      var id_evenement = parseInt($(this).closest('.repas').attr('data-id_evenement'),10);
      var data = [];
      
      var c_trs = 0;
      var nb_trs = $('.table-repas').find('tr[data-id_utilisateur_exposant_contact]').length;
      $('.table-repas').find('tr[data-id_utilisateur_exposant_contact]').each(function(){
        var $tr = $(this);
        var ids_repas = [];
        var c_inputs = 0;
        var nb_inputs = $tr.find('input[name^="id_accompagnant_"]:checked').length;
        $tr.find('input[name^="id_accompagnant_"]:checked').each(function(){
          ids_repas.push(this.value);
          c_inputs++;
          if(c_inputs == nb_inputs){
            var tmp = [];
            tmp.push($tr.attr('data-id_utilisateur_exposant_contact'));
            tmp.push(ids_repas);
            data.push(tmp);
          }
        });

        c_trs++;
        if(c_trs == nb_trs){
          console.log(data);
          if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_utilisateur) && id_utilisateur>0){
            if(data.length > 0){
              $.ajax({
                  type: 'post',
                  url: _path,
                  data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, data:data, atm_back_action:'atm_hopitech_bo_repas_reservation_creer_reservation',atm_back_referrer:window.location.hostname},
                  success: function(_data){
                      var result = $.parseJSON(_data);
                      if(result.success){
                        console.info('Créer une réservation');
                        window.location.reload();
                      }else{
                        console.error(result.message);
                      }
                  }
              });
            }else{
              alert("Tous les champs ne sont pas correctement remplis !");
            }
          }
        }
      });
    }
  });

  // Supprimer la réservation repas
  $('.repas').on('click','.btn-supprimer-reservation',function(e){
    e.preventDefault();
    if (confirm("Êtes-vous sûr de vouloir SUPPRIMER la réservation ? Cela supprimera aussi les accompagnants !(Cette action est irréversible)")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
      var id_evenement = parseInt($(this).closest('.repas').attr('data-id_evenement'),10);

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_utilisateur) && id_utilisateur>0){
        $.ajax({
            type: 'post',
            url: _path,
            data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, atm_back_action:'atm_hopitech_bo_repas_reservation_supprimer',atm_back_referrer:window.location.hostname},
            success: function(_data){
                var result = $.parseJSON(_data);
                if(result.success){
                  console.info('Supprimer la réservation');
                  window.location.reload();
                }else{
                  console.error(result.message);
                }
            }
        });
      }
    }
  });

  // Transformer la réservation de repas en commande
  $('.repas').on('click','.btn-forcer-commande',function(e){
    e.preventDefault();

    if (confirm("Êtes-vous sûr de vouloir transformer la réservation de ce client en commande ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
      var id_evenement = parseInt($(this).closest('.repas').attr('data-id_evenement'),10);
      var data = [];
      
      var c_trs = 0;
      var nb_trs = $('.table-repas').find('tr[data-id_utilisateur_exposant_contact]').length;
      $('.table-repas').find('tr[data-id_utilisateur_exposant_contact]').each(function(){
        var $tr = $(this);
        var ids_repas = [];
        var c_inputs = 0;
        var nb_inputs = $tr.find('input[name^="id_accompagnant_"]:checked').length;
        $tr.find('input[name^="id_accompagnant_"]:checked').each(function(){
          ids_repas.push(this.value);
          c_inputs++;
          if(c_inputs == nb_inputs){
            var tmp = [];
            tmp.push($tr.attr('data-id_utilisateur_exposant_contact'));
            tmp.push(ids_repas);
            data.push(tmp);
          }
        });

        c_trs++;
        if(c_trs == nb_trs){
          console.log(data);
          if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_utilisateur) && id_utilisateur>0){
            if(data.length > 0){
              $.ajax({
                  type: 'post',
                  url: _path,
                  data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, data:data, atm_back_action:'atm_hopitech_bo_repas_reservation_forcer_commande',atm_back_referrer:window.location.hostname},
                  success: function(_data){
                      var result = $.parseJSON(_data);
                      if(result.success){
                        console.info('Transformer la réservation en commande');
                        window.location.reload();
                      }else{
                        console.error(result.message);
                      }
                  }
              });
            }else{
              alert("Tous les champs ne sont pas correctement remplis !");
            }
          }
        }
      });
    }
  });


  //  -----------------------------
  //  COMMANDES REPAS
  //  -----------------------------

  // Annuler la commande de repas
  $('.repas').on('click','.btn-annuler-commande',function(e){
    e.preventDefault();

    if (confirm("Êtes-vous sûr de vouloir ANNULER la commande ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
      var id_evenement = parseInt($(this).closest('.repas').attr('data-id_evenement'),10);
      var id_commande = parseInt($(this).closest('.repas').attr('data-id_commande'),10);

      if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_commande) && id_commande>0){
        $.ajax({
            type: 'post',
            url: _path,
            data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, id_commande:id_commande, atm_back_action:'atm_hopitech_bo_repas_commande_annuler',atm_back_referrer:window.location.hostname},
            success: function(_data){
                var result = $.parseJSON(_data);
                if(result.success){
                  console.info('Annuler la commande');
                  window.location.reload();
                }else{
                  console.error(result.message);
                }
            }
        });
      }
    }
  });

  // Paiement reçu de la commande de repas
  $('.repas').on('click','.btn-paiement-recu',function(e){
    e.preventDefault();

    if (confirm("Avez-vous bien reçu le paiement ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
      var id_evenement = parseInt($(this).closest('.repas').attr('data-id_evenement'),10);
      var id_commande = parseInt($(this).closest('.repas').attr('data-id_commande'),10);
      var data = [];
      
      var c_trs = 0;
      var nb_trs = $('.table-repas').find('tr[data-id_utilisateur_exposant_contact]').length;
      $('.table-repas').find('tr[data-id_utilisateur_exposant_contact]').each(function(){
        var $tr = $(this);
        var ids_repas = [];
        var c_inputs = 0;
        var nb_inputs = $tr.find('input[name^="id_accompagnant_"]:checked').length;
        $tr.find('input[name^="id_accompagnant_"]:checked').each(function(){
          ids_repas.push(this.value);
          c_inputs++;
          if(c_inputs == nb_inputs){
            var tmp = [];
            tmp.push($tr.attr('data-id_utilisateur_exposant_contact'));
            tmp.push(ids_repas);
            data.push(tmp);
          }
        });

        c_trs++;
        if(c_trs == nb_trs){
          if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_utilisateur) && id_utilisateur>0){
            if(data.length > 0){
              $.ajax({
                  type: 'post',
                  url: _path,
                  data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, id_commande:id_commande, data:data, atm_back_action:'atm_hopitech_bo_repas_commande_paiement_recu',atm_back_referrer:window.location.hostname},
                  success: function(_data){
                      var result = $.parseJSON(_data);
                      if(result.success){
                        console.info('Paiement reçu !');
                        window.location.reload();
                      }else{
                        console.error(result.message);
                      }
                  }
              });
            }else{
              alert("Tous les champs ne sont pas correctement remplis !");
            }
          }
        }
      });
    }
  });

  // Confimation de réservation de repas
  $('.repas').on('click','.btn-confirmer-reservation',function(e){
    e.preventDefault();

    if (confirm("Êtes-vous sûr de vouloir confirmer la commande ?")) {
      var id_utilisateur = parseInt($('#id_utilisateur').val(),10);
      var id_utilisateur_exposant = parseInt($('input[name="id_utilisateur_exposant"]').val(),10);
      var id_evenement = parseInt($(this).closest('.repas').attr('data-id_evenement'),10);
      var id_commande = parseInt($(this).closest('.repas').attr('data-id_commande'),10);
      var data = [];
      
      var c_trs = 0;
      var nb_trs = $('.table-repas').find('tr[data-id_utilisateur_exposant_contact]').length;
      $('.table-repas').find('tr[data-id_utilisateur_exposant_contact]').each(function(){
        var $tr = $(this);
        var ids_repas = [];
        var c_inputs = 0;
        var nb_inputs = $tr.find('input[name^="id_accompagnant_"]:checked').length;
        $tr.find('input[name^="id_accompagnant_"]:checked').each(function(){
          ids_repas.push(this.value);
          c_inputs++;
          if(c_inputs == nb_inputs){
            var tmp = [];
            tmp.push($tr.attr('data-id_utilisateur_exposant_contact'));
            tmp.push(ids_repas);
            data.push(tmp);
          }
        });

        c_trs++;
        if(c_trs == nb_trs){
          if(!isNaN(id_evenement) && id_evenement>0 && !isNaN(id_utilisateur) && id_utilisateur>0){
            if(data.length > 0){
              $.ajax({
                  type: 'post',
                  url: _path,
                  data: {id_utilisateur:id_utilisateur, id_utilisateur_exposant:id_utilisateur_exposant, id_evenement:id_evenement, id_commande:id_commande, data:data, atm_back_action:'atm_hopitech_bo_repas_commande_confirmation',atm_back_referrer:window.location.hostname},
                  success: function(_data){
                      var result = $.parseJSON(_data);
                      if(result.success){
                        console.info('Confimation de réservation');
                        window.location.reload();
                      }else{
                        console.error(result.message);
                      }
                  }
              });
            }else{
              alert("Tous les champs ne sont pas correctement remplis !");
            }
          }
        }
      });
    }
  });

});
</script>

