<?php
/**
 *
 *	GESTION DES FONCTIONNALITÉS POUR LES INSCRIPTIONS
 *
 *	Auteur : Antoine Brunet
 *
 */

global $HOPITECH_TEMPLATE_EMAIL_TOP, $HOPITECH_TEMPLATE_EMAIL_BOTTOM;
$HOPITECH_TEMPLATE_EMAIL_TOP = '<HTML><HEAD><TITLE>HOPITECH</TITLE>
</HEAD>
<BODY leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">
<TABLE border="0" width="80%" style="MAX-WIDTH: 800px; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; PADDING-LEFT: 0px; PADDING-RIGHT: 0px" cellSpacing="0" cellPadding="0" align="center">
  <TBODY>
    <TR>
      <TD colspan="3" bgColor="#182024">&nbsp;</TD>
    </TR>
  <TR>
    <TD bgColor="#182024" width="10%">&nbsp;</TD>
    <TD bgColor="#182024" width="80%">
      <TABLE align="center" border="0">
        <TR>
          <TD><A HREF="'.home_url().'" target="_blank"><IMG align="left" src="http://www.hopitech.org/newsletters/generique/logo-hopitech.png" style="MAX-WIDTH: 291px; HEIGHT: auto; WIDTH: 100%; MIN-WIDTH: 80px" border="0" alt="hopitech"></A>
              <TABLE align="right" border="0">
                  <TR>
                    <TD>
                      <span style="font-family: \'Century Gothic\',CenturyGothic,AppleGothic,sans-serif;color:#ffffff;font-size:16px;">&nbsp;&nbsp;4&nbsp;/&nbsp;5&nbsp;/&nbsp;6 Octobre</span><br>
                      <span style="font-family: \'Century Gothic\',CenturyGothic,AppleGothic,sans-serif;color:#e3d700;font-size:25px;">&nbsp;SAINT-ETIENNE&nbsp;2016</span>
                    </TD>
                  </TR>
              </TABLE>
          </TD>
        </TR>
      </TABLE>
    </TD>
      <TD bgColor="#182024" width="10%">&nbsp;</TD>
  </TR>
  <TR>  
    <TD colspan="3" bgColor="#182024">&nbsp;</TD>
  </TR>
  <TR>  
    <TD colspan="3" bgColor="#040404" align="center"><center><IMG src="http://www.hopitech.org/newsletters/201602/hopitech-banniere-nl-1.png" style="MAX-WIDTH: 700; HEIGHT: auto; WIDTH: 100%; MIN-WIDTH: 100px" border="0" alt="hopitech"></center></TD>
  </TR>
  <TR>  
    <TD colspan="3" bgColor="#182024">&nbsp;</TD>
  </TR>
  <TR>  
    <TD bgColor="#182024" width="10%">&nbsp;</TD>';
    
    $HOPITECH_TEMPLATE_EMAIL_BOTTOM = '<TR>  
            <TD bgColor="#0071a5" width="10%">&nbsp;</TD>    
            <TD bgColor="#0071a5"><center><span style="font-family: arial,sans-serif;color:#ffffff;font-size:11px;">Si&egrave;ge social : HOPITECH c/o F&eacute;d&eacute;ration Hospitali&egrave;re de France<br>1 bis rue Cabanis - 75014 Paris.</span></center></TD>
            <TD bgColor="#0071a5" width="10%">&nbsp;</TD>
          </TR>
               <TR>  
            <TD colspan="3" bgColor="#0071a5">&nbsp;</TD>
          </TR> 
          </TBODY>
        </TABLE>
        </BODY>
        </HTML>';

/**
 *  Import du fichier de fonctions pour les inscriptions
 */
require_once('atm-functions.php');
require_once('atm-post.php');
require_once('atm-ajax.php');

/**
 *
 *	AJOUT DE MENUS SUR-MESURE
 *
 */
function atm_register_inscriptions_menu_pages() {
	add_menu_page(__('Inscriptions'), __('Inscriptions'), 'edit_themes', 'atm_cpt_inscriptions', 'printAdminCptInscriptions', null, 90);
  add_menu_page(__('Exposants'), __('Exposants'), 'edit_themes', 'atm_cpt_exposants', 'printAdminCptExposants', null, 91);
  //add_menu_page(__('Stagiaires'), __('Stagiaires'), 'edit_themes', 'atm_cpt_stagiaires', 'printAdminCptStagiaires', null, 92);
  add_menu_page(__('Inscrits'), __('Inscrits'), 'edit_themes', 'atm_cpt_inscrits', 'printAdminCptInscrits', null, 93);
}
function atm_register_inscriptions_submenu_pages() {
  add_submenu_page('atm_cpt_exposants', __('Tous les exposants'), __('Tous les exposants'), 'edit_themes', 'atm_cpt_exposants', 'printAdminCptExposants');
  add_submenu_page('atm_cpt_exposants', __('Ajouter un exposant'), __('Ajouter un exposant'), 'edit_themes', 'atm_cpt_add_exposant', 'printAdminCptAddExposant');
  //add_submenu_page('atm_cpt_exposants', __('Réservations stands'), __('Réservations stands'), 'edit_themes', 'atm_cpt_exposant_stand', 'printAdminCptReservationsStandsExposant');
  //add_submenu_page('atm_cpt_exposants', __('Réservations publicités'), __('Réservations publicités'), 'edit_themes', 'atm_cpt_exposant_publicite', 'printAdminCptReservationsPublicitesExposant');
  //add_submenu_page('atm_cpt_exposants', __('Réservations repas'), __('Réservations repas'), 'edit_themes', 'atm_cpt_exposant_repas', 'printAdminCptReservationsRepasExposant');
	//add_submenu_page('atm_cpt_stagiaires', __('Tous les stagiaires'), __('Tous les stagiaires'), 'edit_themes', 'atm_cpt_stagiaires', 'printAdminCptStagiaires');
  //add_submenu_page('atm_cpt_stagiaires', __('Ajouter un stagiaire'), __('Ajouter un stagiaire'), 'edit_themes', 'atm_cpt_add_stagiaire', 'printAdminCptAddStagiaire');
  add_submenu_page('atm_cpt_inscriptions', __('Stands'), __('Stands'), 'edit_themes', 'atm_cpt_stands', 'printAdminCptStands');
  add_submenu_page('atm_cpt_inscriptions', __('Repas'), __('Repas'), 'edit_themes', 'atm_cpt_repas', 'printAdminCptRepas');
  add_submenu_page('atm_cpt_inscriptions', __('Publicités'), __('Publicités'), 'edit_themes', 'atm_cpt_publicites', 'printAdminCptPublicites');
  add_submenu_page('atm_cpt_inscriptions', __('Secteurs d\'activité'), __('Secteurs d\'activité'), 'edit_themes', 'atm_cpt_secteurs_activite', 'printAdminCptSecteursActivite');
  add_submenu_page('atm_cpt_inscrits', __('Tous les inscrits'), __('Tous les inscrits'), 'edit_themes', 'atm_cpt_inscrits', 'printAdminCptInscrits');
}
add_action('admin_menu', 'atm_register_inscriptions_menu_pages');
add_action('admin_menu', 'atm_register_inscriptions_submenu_pages');


/*
 *
 * Affichage du menu "Inscriptions"
 *
 */
function printAdminCptInscriptions(){
    if(!is_admin()) die();
    include('options/index.php');
}


/*
 *
 * Affichage du menu "Tous les exposants"
 *
 */
function printAdminCptExposants(){
    if(!is_admin()) die();
    include('exposant/all-exposants.php');
}


/*
 *
 * Affichage du menu "Ajouter un exposant"
 *
 */
function printAdminCptAddExposant(){
    if(!is_admin()) die();
    include('exposant/add-exposant.php');
}


/*
 *
 * Affichage du menu "Exposants - Réservations stands"
 *
 */
function printAdminCptReservationsStandsExposant(){
    if(!is_admin()) die();
    include('exposant/reservations-stands/all-stand.php');
}


/*
 *
 * Affichage du menu "Exposants - Réservations publicités"
 *
 */
function printAdminCptReservationsPublicitesExposant(){
    if(!is_admin()) die();
    include('exposant/reservations-publicites/all-publicite.php');
}


/*
 *
 * Affichage du menu "Exposants - Réservations repas"
 *
 */
function printAdminCptReservationsRepasExposant(){
    if(!is_admin()) die();
    include('exposant/reservations-repas/all-repas.php');
}


/*
 *
 * Affichage du menu "Tous les stagiaires"
 *
 */
function printAdminCptStagiaires(){
    if(!is_admin()) die();
    include('stagiaire/all-stagiaires.php');
}

/*
 *
 * Affichage du menu "Tous les stagiaires"
 *
 */
function printAdminCptInscrits(){
    if(!is_admin()) die();
    include('all-inscrits.php');
}

/*
 *
 * Affichage du menu "Ajouter un stagiaire"
 *
 */
function printAdminCptAddStagiaire(){
    if(!is_admin()) die();
    include('stagiaire/add-stagiaire.php');
}


/*
 *
 * Affichage du menu "Stands"
 *
 */
function printAdminCptStands(){
    if(!is_admin()) die();
    include('options/stands.php');
}


/*
 *
 * Affichage du menu "Repas"
 *
 */
function printAdminCptRepas(){
    if(!is_admin()) die();
    include('options/repas.php');
}


/*
 *
 * Affichage du menu "Pubilicités"
 *
 */
function printAdminCptPublicites(){
    if(!is_admin()) die();
    include('options/pubilicites.php');
}


/*
 *
 * Affichage du menu "Secteurs d'activité"
 *
 */
function printAdminCptSecteursActivite(){
    if(!is_admin()) die();
    include('options/secteurs-activite.php');
}


/*
 *
 * Chargement des scripts JS complémentaires
 *
 */
function atm_enqueue_admin_inscriptions_javascript(){
    if(isset($_GET['page']) && !empty($_GET['page']) && ($_GET['page'] == 'atm_cpt_inscriptions' || $_GET['page'] == 'atm_cpt_repas') && is_admin()) {
        wp_enqueue_script( 'atm-jquery-ui-js', 'https://code.jquery.com/ui/1.11.4/jquery-ui.min.js', array( 'jquery' ) );
    }
    /*
    if( ( preg_match('#atm_cpt_delpro_categories#', $_SERVER['REQUEST_URI']) || preg_match('#atm_cpt_trucs_astuces_categories#', $_SERVER['REQUEST_URI']) ) && is_admin()) {
        wp_enqueue_script(  'atm_menu_jquery_ui_js', 'http://code.jquery.com/ui/1.11.4/jquery-ui.js', array('jquery'), '1.11.4', true);
        //wp_enqueue_script(  'atm_menu_inscriptions_js',  home_url() . '/wp-content/themes/atm-theme/library/js/atm-add-bien.js', array('jquery'), '2.2.0', true);
    }
    if(preg_match('#atm_cpt_trucs_astuces_categories#', $_SERVER['REQUEST_URI']) && is_admin()){
        wp_enqueue_media();
    }*/
}
add_action('admin_print_scripts', 'atm_enqueue_admin_inscriptions_javascript');


/*
 *
 * Chargements des styles CSS complémentaires
 *
 */
function atm_enqueue_admin_inscriptions_styles(){
    if(isset($_GET['page']) && !empty($_GET['page']) && ($_GET['page'] == 'atm_cpt_inscriptions' || $_GET['page'] == 'atm_cpt_repas') && is_admin()) {
        wp_enqueue_style('atm-jquery-ui-css', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css'); 
        wp_enqueue_style('atm-jquery-ui-structure-css', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.structure.min.css'); 
        wp_enqueue_style('atm-jquery-ui-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.theme.min.css'); 
    }
    /*if((preg_match('#atm_cpt_add_enquete#', $_SERVER['REQUEST_URI']) || preg_match('#atm_cpt_enquetes#', $_SERVER['REQUEST_URI']) ) && is_admin()) {
        //wp_enqueue_style('jquery-ui-css', home_url() . '/wp-content/themes/atm-theme/library/css/jquery-ui.min.css');
        //wp_enqueue_style('jquery-ui-theme-css', home_url() . '/wp-content/themes/atm-theme/library/css/jquery-ui.theme.min.css');
        //wp_enqueue_style('jquery-ui-stucture-css', home_url() . '/wp-content/themes/atm-theme/library/css/jquery-ui.structure.min.css'); 
    }
    if(( preg_match('#atm_cpt_delpro_categories#', $_SERVER['REQUEST_URI']) || preg_match('#atm_cpt_trucs_astuces_categories#', $_SERVER['REQUEST_URI']) ) && is_admin()) {
        wp_enqueue_style('bootstrap-min', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css');
        wp_enqueue_style('bootstrap-theme', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css');
        //wp_enqueue_style('atm-edit-eleveur-css', WP_CONTENT_URL . '/themes/atm-theme/library/css/atm-edit-eleveur.css');  
    }
    if(preg_match('#atm_cpt_trucs_astuces_categories#', $_SERVER['REQUEST_URI']) && is_admin()){
        wp_enqueue_style('media');
    }*/
}
add_action('admin_print_styles', 'atm_enqueue_admin_inscriptions_styles');


/**
 *
 *  AJOUT DES ICONES PERSONNALISES
 *
 */
function atm_add_inscriptions_menus_icons_styles(){
    ?>
    <style>
    #adminmenu #toplevel_page_atm_cpt_inscriptions div.wp-menu-image:before {
    content: "\f524";
    }
    #adminmenu #toplevel_page_atm_cpt_exposants div.wp-menu-image:before {
    content: "\f484";
    }
    #adminmenu #toplevel_page_atm_cpt_stagiaires div.wp-menu-image:before {
    content: "\f481";
    }
    #adminmenu #toplevel_page_atm_cpt_inscrits div.wp-menu-image:before {
    content: "\f307";
    }
    </style>
    <?php
    }
add_action( 'admin_head', 'atm_add_inscriptions_menus_icons_styles');


?>