<?php
/**
 *
 *  FONCTIONS DU THEME 
 *
 *  Author: Antoine Brunet
 *  Based on the Bones Theme by Eddie Machado
 *
 */

/**
 *
 *  FICHIERS A INCLURE ABSOLUMENT
 *
 */
/**
 *  1. library/php/config.php
*/
require_once('library/php/config.php');
/**
 *  2. library/php/admin.php 
 */
require_once('library/php/admin.php');
/**
 *  3. library/php/headers.php 
 */
require_once('library/php/headers.php');
/**
 *  4. library/php/ajax/atm-ajax-front.php 
 */
require_once('library/php/ajax/atm-ajax-front.php');

if (get_option('atm_newsletter_nomdomaine')
  && get_option('atm_newsletter_port')
  && get_option('atm_newsletter_nomutilisateur')
  && get_option('atm_newsletter_motdepasse')) {
  add_action('phpmailer_init', 'my_phpmailer_example');
}

function my_phpmailer_example($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host = get_option('atm_newsletter_nomdomaine');
    $phpmailer->SMTPAuth = true; // Force it to use Username and Password to authenticate
    $phpmailer->Port = get_option('atm_newsletter_port');
    $phpmailer->Username = get_option('atm_newsletter_nomutilisateur');
    $phpmailer->Password = get_option('atm_newsletter_motdepasse');

    // Additional settings…
      // Choose SSL or TLS, if necessary for your server
    if (get_option('atm_newsletter_securiteconnexion') && get_option('atm_newsletter_securiteconnexion') != "Aucune"){
      $phpmailer->SMTPSecure = get_option('atm_newsletter_securiteconnexion'); 
    }
    if (get_option('atm_newsletter_adressedestinataire')){
      $phpmailer->From = get_option('atm_newsletter_adressedestinataire');
    }
    if (get_option('atm_newsletter_nomdestinataire')){
      $phpmailer->FromName = get_option('atm_newsletter_nomdestinataire');
    }
}

/**
 *
 *  OPTIONS DE TAILLE DES MINIATURES
 *
 */
add_image_size( 'atm-thumb-100', 100, 100, true );


/**
 *
 * Fonction qui supprime le [...] du lien Read More
 *
 */
function atm_excerpt_more($more) {
  global $post;
  return '...  <a class="excerpt-read-more" href="'. get_permalink($post->ID) . '" title="'. __('Read', 'atm-theme') . get_the_title($post->ID).'">'. __('Read more &raquo;', 'atm-theme') .'</a>';
}

/**
 *
 *  Version modifiée de la fonction the_author_posts_link() qui retourne juste un lien
 *
 */
function atm_get_the_author_posts_link() {
  global $authordata;
  if ( !is_object( $authordata ) )
    return false;
  $link = sprintf(
    '<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
    get_author_posts_url( $authordata->ID, $authordata->user_nicename ),
    esc_attr( sprintf( __( 'Posted by %s', 'atm-theme' ), get_the_author() ) ),
    get_the_author()
  );
  return $link;
}


/**
 *
 *  FORMULAIRE DE RECHERCHE PERSO
 *
 */
function atm_wpsearch($form) {
  $form = '<form role="search" method="get" id="searchform" action="' . home_url( "/" ) . '" >
  <input type="text" value="' . get_search_query() . '" name="s" class="s" placeholder="'.__("Search on the website...","atm-theme").'" />
  <input type="submit" class="searchsubmit" value="'. __("Send", "atm-theme") .'" />
  </form>';
  return $form;
}


/**
 *
 *  Sécurise les envois vers la base de données : supprime les codes malicieux d'une chaine de caractères
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
  }
  return $output;
}


/**
 *
 *  Convert a string to float
 *
 *  @param string $string 
 *  @return float
 *
 */
function floatize($string){
    return floatval(preg_replace("/[^0-9.]/", "", str_replace(',','.',atm_sanitize($string))));
}


/**
 *
 *  Emulates mb_ucfirst
 *
 *  @param string $string 
 *  @return string
 *
 */
if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = true) {
      $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
      $str_end = "";
      if ($lower_str_end) {
  $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
      }
      else {
  $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
      }
      $str = $first_letter . $str_end;
      return $str;
    }
  }


/**
 *
 * Convert an object to an array
 *
 * @param    object  $object The object to convert
 * @reeturn      array
 *
 */
function objectToArray( $object )
{
  if( !is_object( $object ) && !is_array( $object ) )
  {
    return $object;
  }
  if( is_object( $object ) )
  {
    $object = get_object_vars( $object );
  }
  return array_map( 'objectToArray', $object );
}


/**
 *
 *  Encodage d'email pour contrer le spam
 *
 *  Transforme les caractères de l'email en équivalent ASCII dans le code source
 *
 */
function atm_emailEncode($email) {
  $email_encode = '';
  $nb_caractere = strlen($email);
  for ($a = 0; $a < $nb_caractere; $a ++) {
    $ord = ord(substr($email, $a, 1) );
    $email_encode .= '&#'.$ord.';';
  }
  return $email_encode;
}


/**
 *
 *  Vérification et ré-écriture des urls externes
 *
 */
function atm_urlFormat($url){
  if(!preg_match('#http#',$url)){
    return 'http://'.$url;
  }else{
    return $url;
  }
}


/**
 * 
 * Fonction de génération de string alétaoire et unique
 * 
 */
function crypto_rand_secure($min, $max) {
    $range = $max - $min;
    if ($range < 0) return $min; // not so random...
    $log = log($range, 2);
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd >= $range);
    return $min + $rnd;
}


 /**
 * 
 * Génère un tokien
 * 
 */
function getToken($length){
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    for($i=0;$i<$length;$i++){
        $token .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
    }
    return $token;
}


 /*
 * 
 * Cryptage / Décryptage de mot de passe
 * 
 */
function generateHash($password) {
    if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
        $salt = '$2y$11$' . substr(md5(uniqid(rand(), true)), 0, 22);
        return crypt($password, $salt);
    }
}


 /**
 * 
 * Vérification de la correspondance de 2 mots de passe cryptés
 * 
 */
function verifyPassword($password, $hashedPassword) {
    return crypt($password, $hashedPassword) == $hashedPassword;
}

 
 /**
 * 
 * Vérification du format des emails
 * 
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}


/**
 *
 *  Formate un texte HTML pour l'inclure dans une infoWindow Google Maps
 *
 */
function atm_format_html_for_google_maps_infoWindow($contenu){
  $output = str_replace(array("\r\n", "\r"), "\n", $contenu);
  $lines = explode("\n", $output);
  $new_lines = array();
  foreach ($lines as $i => $line) {
    if(!empty($line))
      $new_lines[] = addslashes(trim($line));
  }
  $description = implode($new_lines);
  $description = str_replace(array('"','<br />','<\p>'),array('\'','<br />"+','</p>"+'),$description);
  $test = explode('"+',$description);
  $d = '';
  foreach($test as $s){
    $d .= '"'.$s.'"+';
  }
  $d = substr($d,0,-1);

  return substr($d,0,-1);
}


/**
 *
 *  RENOMMAGE DES MEDIAS LORS DE L'IMPORT
 *
 */
function atm_rename_filename_on_upload($filename) {
  $info = pathinfo($filename);
  $ext  = empty($info['extension']) ? '' : '.' . $info['extension'];
  $name = basename($filename, $ext);
  $sanitized = preg_replace('/[^a-zA-Z0-9-_.]/','', $name);
  $sanitized = str_replace(array('.','_'),'-', $sanitized);
  $sanitized = sanitize_title_with_dashes(remove_accents($sanitized));
  return strtolower($sanitized . $ext);
}
add_filter('sanitize_file_name', 'atm_rename_filename_on_upload', 10);


/**
 *
 *  AJOUT DE L'ATTRIBUT target=_blank SUR LES LIENS PDF
 *
 */
function atm_autoblank($text) {
  //$return = str_replace('href=', 'target="_blank" href=', $text);
  $return = preg_replace('/(<table[^>]*>(?:.|\n)*?<\/table>)/', '<div class="table-responsive">$1</div>', $text);
  return $return;
}
add_filter('the_content', 'atm_autoblank');
add_filter('comment_text', 'atm_autoblank');


/**
 *
 *  RESTRICTION DES MIMES TYPES AUTHORISÉS
 *
 */
/*
function atm_restrict_mime($mimes) {
  $mimes = array(
                  'jpg|jpeg|jpe' => 'image/jpeg',
                  'gif' => 'image/gif',
                  'png' => 'image/png'
  );
  return $mimes;
}
add_filter('upload_mimes','atm_restrict_mime');
*/

/**
 *
 * Afficher la date relative
 *
 */
function getRelativeTime($date)
{
  $date_now = current_time('mysql');
  $date_a_comparer = new DateTime($date);
  $date_actuelle = new DateTime($date_now);

  $intervalle = $date_a_comparer->diff($date_actuelle);

  if ($date_a_comparer > $date_actuelle)
  {
    $prefixe = 'Dans ';
  }
  else
  {
    $prefixe = 'Il y a ';
  }

  $ans = $intervalle->format('%y');
  $mois = $intervalle->format('%m');
  $jours = $intervalle->format('%d');
  $heures = $intervalle->format('%h');
  $minutes = $intervalle->format('%i');
  $secondes = $intervalle->format('%s');

  if ($ans != 0)
  {
    $relative_date = $prefixe . $ans . ' an' . (($ans > 1) ? 's' : '');
    if ($mois >= 6) $relative_date .= ' et demi';
  }
  elseif ($mois != 0)
  {
    $relative_date = $prefixe . $mois . ' mois';
    if ($jours >= 15) $relative_date .= ' et demi';
  }
  elseif ($jours != 0)
  {
    $relative_date = $prefixe . $jours . ' jour' . (($jours > 1) ? 's' : '');
  }
  elseif ($heures != 0)
  {
    $relative_date = $prefixe . $heures . ' heure' . (($heures > 1) ? 's' : '');
  }
  elseif ($minutes != 0)
  {
    $relative_date = $prefixe . $minutes . ' minute' . (($minutes > 1) ? 's' : '');
  }
  else
  {
    $relative_date = $prefixe . ' quelques secondes';
  }

  return $relative_date;
}


/**
 *
 *  FIL D'ARIANE PERSONNALISÉ
 *
 */
function atm_ariane_get_category_parents($id, $link = false,$separator = '/',$nicename = false,$visited = array()) {
  $chain = '';$parent = &get_category($id);
  if (is_wp_error($parent))return $parent;
  if ($nicename)$name = $parent->name;
  else $name = $parent->cat_name;
  if ($parent->parent && ($parent->parent != $parent->term_id ) && !in_array($parent->parent, $visited)) {$visited[] = $parent->parent;$chain .= atm_ariane_get_category_parents( $parent->parent, $link, $separator, $nicename, $visited );}
  if ($link) $chain .= '<span><a href="' . str_replace('category/','',get_category_link( $parent->term_id )) . '" title="'.__("See all the posts","atm-theme").' '.$parent->cat_name.'" rel="v:url" property="v:title">'.$name.'</a></span>' . $separator;
  else $chain .= $name.$separator;
  return $chain;
}
function atm_bread() {
  global $wp_query;$ped=get_query_var('paged');$rendu = '<div>';  
  if ( !is_home() ) {$rendu .= '<span id="breadex">'.__("You are here :","atm-theme").'</span> <span><a title="'. get_bloginfo('name') .'" id="breadh" href="'.home_url().'">'. get_bloginfo('name') .'</a></span>';}
  elseif ( is_home() ) {$rendu .= '<span id="breadex">'.__("You are here :","atm-theme").'</span> <span>'.__("Home of ","atm-theme"). get_bloginfo('name') .'</span>';}
  if ( is_category() ) {
    $cat_obj = $wp_query->get_queried_object();$thisCat = $cat_obj->term_id;$thisCat = get_category($thisCat);$parentCat = get_category($thisCat->parent);
    if ($thisCat->parent != 0) $rendu .= " &raquo; ".atm_ariane_get_category_parents($parentCat, true, " &raquo; ", true);
    if ($thisCat->parent == 0) {$rendu .= " &raquo; ";}
    if ( $ped <= 1 ) {$rendu .= single_cat_title("", false);}
    elseif ( $ped > 1 ) {
      $rendu .= '<span><a href="' . str_replace('category/','',get_category_link( $thisCat )) . '" title="'.__("See all the posts in","atm-theme").' '.single_cat_title("", false).'">'.single_cat_title("", false).'</a></span>';}}
  elseif ( is_author()){
    global $author;$user_info = get_userdata($author);$rendu .= " &raquo; ".__("Posts by the author","atm-theme")." ".$user_info->display_name."</span>";}  
  elseif ( is_tag()){
    $tag=single_tag_title("",FALSE);$rendu .= " &raquo; ".__("Posts on the theme","atm-theme")." <span>".$tag."</span>";}
  elseif ( is_date() ) {
    if ( is_day() ) {
      global $wp_locale;
      $rendu .= '<span><a href="'.get_month_link( get_query_var('year'), get_query_var('monthnum') ).'">'.$wp_locale->get_month( get_query_var('monthnum') ).' '.get_query_var('year').'</a></span> ';
      $rendu .= " &raquo; ".__("Archives for ","atm-theme").get_the_date();}
    else if ( is_month() ) {
      $rendu .= " &raquo; ".__("Archives for ","atm-theme").single_month_title(' ',false);}
    else if ( is_year() ) {
      $rendu .= " &raquo; ".__("Archives for ","atm-theme").get_query_var('year');}}
  elseif ( is_archive() && !is_category()){
    $posttype = get_post_type();
    $tata = get_post_type_object( $posttype );
    $var = '';
    $the_tax = get_taxonomy( get_query_var( 'taxonomy' ) );
    $titrearchive = $tata->labels->menu_name;
    if (!empty($the_tax)){$var = $the_tax->labels->name.' ';}
    if (empty($the_tax)){$var = $titrearchive;}
    $rendu .= ' &raquo; '.__("Archives on","atm-theme").' "'.$var.'"';}
  elseif ( is_search()) {
    $rendu .= " &raquo; ".__("Results for your search","atm-theme")." <span>&raquo; ".get_search_query()."</span>";}
  elseif ( is_404()){
    $rendu .= " &raquo; ".__("404 Page not found","atm-theme");}
  elseif ( is_single()){
    $category = get_the_category();
    $category_id = get_cat_ID( $category[0]->cat_name );
    if ($category_id != 0) {
      $rendu .= " &raquo; ".atm_ariane_get_category_parents($category_id,TRUE,' &raquo; ')."<span>".the_title('','',FALSE)."</span>";}
    elseif ($category_id == 0) {
      $post_type = get_post_type();
      $tata = get_post_type_object( $post_type );
      $titrearchive = $tata->labels->menu_name;
      $urlarchive = get_post_type_archive_link( $post_type );
      $rendu .= ' &raquo; <span><a class="breadl" href="'.$urlarchive.'" title="'.$titrearchive.'">'.$titrearchive.'</a></span> &raquo; <span>'.the_title('','',FALSE).'</span>';}}
  elseif ( is_page()) {
    $post = $wp_query->get_queried_object();
    if ( $post->post_parent == 0 ){$rendu .= " &raquo; ".the_title('','',FALSE)."";}
    elseif ( $post->post_parent != 0 ) {
      $title = the_title('','',FALSE);$ancestors = array_reverse(get_post_ancestors($post->ID));array_push($ancestors, $post->ID);
      foreach ( $ancestors as $ancestor ){
        if( $ancestor != end($ancestors) ){$rendu .= '&raquo; <span><a href="'. get_permalink($ancestor) .'">'.strip_tags( apply_filters( 'single_post_title', get_the_title( $ancestor ) ) ) .'</a></span>';}
        else {$rendu .= ' &raquo; '.strip_tags(apply_filters('single_post_title',get_the_title($ancestor))).'';}}}}
  if ( $ped >= 1 ) {$rendu .= ' (Page '.$ped.')';}
  $rendu .= '</div>';
  echo $rendu;
}


/**
 *
 *  PAGINATION NUMÉRIQUE DES ARTICLES
 *
 */
function atm_page_navi($before = '', $after = '') {
  global $wpdb, $wp_query;
  $request = $wp_query->request;
  $posts_per_page = intval(get_query_var('posts_per_page'));
  $paged = intval(get_query_var('paged'));
  $numposts = $wp_query->found_posts;
  $max_page = $wp_query->max_num_pages;
  if ( $numposts <= $posts_per_page ) { return; }
  if(empty($paged) || $paged == 0) {
    $paged = 1;
  }
  $pages_to_show = 7;
  $pages_to_show_minus_1 = $pages_to_show-1;
  $half_page_start = floor($pages_to_show_minus_1/2);
  $half_page_end = ceil($pages_to_show_minus_1/2);
  $start_page = $paged - $half_page_start;
  if($start_page <= 0) {
    $start_page = 1;
  }
  $end_page = $paged + $half_page_end;
  if(($end_page - $start_page) != $pages_to_show_minus_1) {
    $end_page = $start_page + $pages_to_show_minus_1;
  }
  if($end_page > $max_page) {
    $start_page = $max_page - $pages_to_show_minus_1;
    $end_page = $max_page;
  }
  if($start_page <= 0) {
    $start_page = 1;
  }
  echo $before.'<nav class="page-navigation"><ol class="atm_page_navi clearfix">'."";
  if ($start_page >= 2 && $pages_to_show < $max_page) {
    $first_page_text = __( "First", 'atm-theme' );
    echo '<li class="bpn-first-page-link"><a href="'.get_pagenum_link().'" title="'.$first_page_text.'">'.$first_page_text.'</a></li>';
  }
  echo '<li class="bpn-prev-link">';
  previous_posts_link('<<');
  echo '</li>';
  for($i = $start_page; $i  <= $end_page; $i++) {
    if($i == $paged) {
      echo '<li class="bpn-current">'.$i.'</li>';
    } else {
      echo '<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
    }
  }
  echo '<li class="bpn-next-link">';
  next_posts_link('>>');
  echo '</li>';
  if ($end_page < $max_page) {
    $last_page_text = __( "Last", 'atm-theme' );
    echo '<li class="bpn-last-page-link"><a href="'.get_pagenum_link($max_page).'" title="'.$last_page_text.'">'.$last_page_text.'</a></li>';
  }
  echo '</ol></nav>'.$after."";
} /* end page navi */


/**
 *
 *  FONCTION D'AFFICHAGE DE CONTENT - Longueur personnalisée pour le content d'une page par ID
 *
 */
function awd_content_by_id($id, $limit, $strip=null) {
  $content_post = get_post($id);
  $content = $content_post->post_content;
  if(!is_null($strip) && $strip){
    $content = strip_tags($content);
    $content = explode(' ', $content, $limit);
  }else{
    $content = explode(' ', $content, $limit);
  }
  if (count($content)>=$limit) {
    array_pop($content);
    $content = implode(" ",$content).'...';
  } else {
    $content = implode(" ",$content);
  } 
  $content = preg_replace('/\[.+\]/','', $content);
  $content = apply_filters('the_content', $content); 
  $content = str_replace(']]>', ']]&gt;', $content);
  
  echo $content;
}


/**
 *
 *  FONCTION D'AFFICHAGE DE CONTENT - Longueur personnalisée pour le content
 *
 */
function awd_content($limit,$strip=null,$continue=null) {
  $permalink = get_permalink($post->ID);
  $title = get_the_title($post->ID);
  if(!is_null($strip) && $strip){
    $content = strip_tags(get_the_content());
    $content = explode(' ', $content, $limit);
  }else{
    $content = explode(' ', get_the_content(), $limit);
  }
  if (count($content)>=$limit) {
    array_pop($content);
      $content = implode(" ",$content).'...';
  } else {
      $content = implode(" ",$content);
  } 
  $content = preg_replace('/\[.+\]/','', $content);
  $content = apply_filters('the_content', $content); 
  $content = str_replace(']]>', ']]&gt;', $content);
  if (isset($continue) && !empty($continue) && !is_null($continue)) {
    $content = $content.'<a class="excerpt-read-more" href="'.$permalink.'" title="'.$title.'">'.$continue.' »</a>';
  }
  echo $content;
}


/**
 *
 *  FONCTION D'AFFICHAGE DE CONTENT - Longueur personnalisée pour un texte
 *
 */
function awd_content_page($text,$limit=null) {
  $content = preg_replace('/\[.+\]/','', $text);
  $content = apply_filters('the_content', $content); 
  $content = str_replace(']]>', ']]&gt;', $content);
  $array = explode('<img',$content);
  $newContent = substr($array[0],0,-3);
  $newContent = explode(' ', $newContent, $limit);
  if (count($newContent)>=$limit) {
    array_pop($newContent);
    $newContent = implode(" ",$newContent).'...';
  } else {
    $newContent = implode(" ",$newContent);
  } 
  echo strip_tags($newContent);
}


/**
 *
 *  FONCTIONS QUI PERMET DE RECUPERER L'ID DU TOP PARENT D'UNE PAGE - Get_Top_Parent
 *
 */
if (is_category()) {if(!function_exists("get_top_parent")) {function get_top_parent($cat){$curr_cat = get_category_parents($cat, false, "/" ,true);$curr_cat = explode("/",$curr_cat);$idObj = get_category_by_slug($curr_cat[0]);return $idObj->term_id;}} $category = get_category(get_query_var("cat"),false);$category_id = $category->cat_ID;$parentpagetitle = get_top_parent($category_id);} else {if(!function_exists("get_top_parent_page_id")) {function get_top_parent_page_id() {global $post;if ($post->ancestors) {return end($post->ancestors);} else {return $post->ID;}}} $parent = get_top_parent_page_id($post_ID);$post_id_7 = get_post($parent);$parentpagetitle = $post_id_7->ID;}


/**
 *
 *  FONCTIONS QUI PERMET DE RECUPERER L'ID DU TOP PARENT D'UNE PAGE - On récupère la page Top Parent de la Page en cours
 *
 */
function awd_get_topParentPageId($post_ID){
  if (is_category()) {
    $category = get_category(get_query_var("cat"),false);
    $category_id = $category->cat_ID;
    $parentpageid = get_top_parent($category_id);
  } else {
    $parent = get_top_parent_page_id($post_ID);
    $post_id = get_post($parent);
    $parentpageid = $post_id->ID;
  }
  
  return $parentpageid;
}


/**
 *
 *  AJOUT D'UNE METABOX ATMOSPHERE DANS LE DASHBOARD
 *
 */
function atm_dashboard_widgets() {
  global $wp_meta_boxes;
  wp_add_dashboard_widget('custom_help_widget', 'Site Internet développé par Atmosphère Communication', 'atm_custom_dashboard_help');
}

function atm_custom_dashboard_help() {
  $page = file_get_contents('http://www.atmospherecommunication.fr/formation/formation.html');
  echo utf8_encode($page);
}
add_action('wp_dashboard_setup', 'atm_dashboard_widgets');


/**
 *
 *  GESTION DU CHARGEMENT DES SCRIPTS
 *
 */
function atm_custom_deregister_javascript() {
  if ( !is_page('contact') ) {
    wp_deregister_script( 'contact-form-7' );
  }
  if (is_home()) {
    wp_deregister_script( 'atm-fancybox-js' );
  }
  /*if (!is_home()) {
    wp_deregister_script( 'atm-flexslider' );
  }*/
}
add_action( 'wp_print_scripts', 'atm_custom_deregister_javascript', 100 );


/**
 *
 *  GESTION DU CHARGEMENT DES STYLES
 *
 */
function atm_custom_deregister_styles() {
  if ( !is_page('contact') ) {
    wp_deregister_style( 'contact-form-7' );
  }
  if (is_home()) {
    wp_deregister_style('atm-fancybox-css');
  }
}
add_action( 'wp_print_styles', 'atm_custom_deregister_styles', 100 );


/**
 *
 *  AJOUT DES ICONES PERSONNALISES
 *
 */
function atm_add_menu_icons_styles(){
    ?>
    <style>
    #adminmenu #toplevel_page_wpcf7 div.wp-menu-image:before {
    content: "\f466";
    }
    #adminmenu #toplevel_page_edit-post_type-acf-field-group div.wp-menu-image:before {
    content: "\f163";
    }
    #adminmenu #toplevel_page_options-du-site div.wp-menu-image:before {
    content: "\f314";
    }
    </style>
    <?php
    }
add_action( 'admin_head', 'atm_add_menu_icons_styles');


/**
 *
 *  Création d'une page d'options ACF pour le site
 *
 */
if( function_exists('acf_add_options_page') ) {
  
  acf_add_options_page(array(
    'page_title'  => 'Options du site',
    'menu_title'  => 'Options du site',
    'menu_slug'   => 'options-du-site',
    'capability'  => 'edit_posts',
    'redirect'    => false
  ));
  
}


/**
 *
 *  Ajout d'un champ description dans les réglages généraux
 *
 */
$new_general_setting = new new_general_setting();

class new_general_setting {
    function new_general_setting( ) {
        add_filter( 'admin_init' , array( &$this , 'atm_add_desc_to_options_general' ) );
    }
    function atm_add_desc_to_options_general() {
      // Meta description
        register_setting( 'general', 'site_meta_description', 'esc_attr' );
        add_settings_field('meta_description', '<label for="site_meta_description">'.__('SEO Méta Description du site' , 'site_meta_description' ).'</label>' , array(&$this, 'atm_add_desc_to_options_generalfields_html') , 'general' );
      // Code Google UA
        register_setting( 'general', 'site_google_ua', 'esc_attr' );
        add_settings_field('google_ua', '<label for="site_google_ua">'.__('Code Google Universal Analytics' , 'site_google_ua' ).'</label>' , array(&$this, 'atm_add_google_ua_to_options_generalfields_html') , 'general' );

    }
    function atm_add_desc_to_options_generalfields_html() {
        $value = get_option( 'site_meta_description', '' );
        echo '<textarea id="site_meta_description" name="site_meta_description" style="height:7em;min-height:7em;width:25em;">' . $value . '</textarea>';
    }
    function atm_add_google_ua_to_options_generalfields_html() {
        $value_gua = get_option( 'site_google_ua', '' );
        echo '<input type="text" id="site_google_ua" name="site_google_ua" style="width:25em;">' . $value_gua . '</textarea>';
    }
}


/**
 *
 *  Sauvegarder les informations des ACF des "Blocs de contenu" d'une page dans le post_content en BDD
 *
 */
function atm_save_meta_box_data( $post_id ) {
  global $wpdb;

  // If this is an autosave, our form has not been submitted, so we don't want to do anything.
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }

  $content = atm_get_the_content($post_id);
  if(isset($content) && !empty($content) && !is_null($content) && !is_array($content)){
    $table_name = $wpdb->prefix.'posts';
    $wpdb->update( $table_name, array( 'post_content' => $content ), array( 'ID' => $post_id ), array( '%s' ), array( '%d' ) );
  }
}
add_action( 'save_post', 'atm_save_meta_box_data' );


/**
 *
 *
 *  GESTION DES BLOCS DE CONTENUS - Création des blocs
 *
 *
 */
function create_blocs($array_contenus){
  global $diaporama_count;
  global $diaporama_options;
  global $galerie_count;
  global $gm_liste;
  $return = '';
  $array_replace = array('_gauche', '_droite', '_une', '_deux', '_trois', '_quatre', '_1', '_2', '_3', '_pb');
  
  if(isset($array_contenus) && !empty($array_contenus) && !is_null($array_contenus) && is_array($array_contenus) && sizeof($array_contenus) > 0 && $array_contenus !== false){
    foreach ($array_contenus as $element) {
      $acf_fc_layout = str_replace($array_replace, '',$element['acf_fc_layout']);
      switch ($acf_fc_layout) {
        case 'sous_titre':
          $contenu = $element['sous_titre'];
          $return .= '<h2>'.$contenu.'</h2>';
          break;

        case 'contenu_texte_images':
          $contenu = apply_filters('the_content',$element['contenu']);
          $return .= $contenu;
          break;
                          
        case 'diaporama':
          $contenu = $element['diaporama'];
          $affichage = $element['affichage'];
          if(isset($contenu) && !empty($contenu) && !is_null($contenu)){
            // Diaporama
            if($affichage == 'diaporama'){
              $diaporama_count++;
              $diaporama_options[$diaporama_count] = array('vitesse' => $element['vitesse'], 'animation' => $element['animation']);
              $return .= '<div class="diaporama_'.$diaporama_count.' diaporama flexslider"><ul class="slides">';
              foreach($contenu as $objet){
                if(isset($objet['image']) && !empty($objet['image']) && !is_null($objet['image'])){
                  $image_object = $objet['image'];
                  $src = $image_object['url'];
                  $width = $image_object['width'];
                  $height = $image_object['height'];
                  $alt = $image_object['title'];
                  if(isset($objet['legende']) && !empty($objet['legende']) && !is_null($objet['legende'])){
                    $legende = $objet['legende'];
                  }else{
                    $legende = $alt;
                  }
                  $return .= '<li><img src="'.$src.'" alt="'.$legende.'" width="'.$width.'" height="'.$height.'" /></li>';
                } 
              }
              $return .= '</ul></div>';
            }
            // Galerie d'images
            if($affichage == 'galerie'){
              $galerie_count++;
              $return .= '<div class="galerie_'.$galerie_count.' galerie boxsize clearfix"><ul class="galerie_liste">';
              foreach($contenu as $objet){
                if(isset($objet['image']) && !empty($objet['image']) && !is_null($objet['image'])){
                  $image_object = $objet['image'];
                  $src = $image_object['sizes']['thumbnail'];
                  $width = $image_object['sizes']['thumbnail-width'];
                  $height = $image_object['sizes']['thumbnail-height'];
                  $src_full = $image_object['url'];
                  $width_full = $image_object['width'];
                  $height_full = $image_object['height'];
                  $alt = $image_object['title'];
                  if(isset($objet['legende']) && !empty($objet['legende']) && !is_null($objet['legende'])){
                    $legende = $objet['legende'];
                  }else{
                    $legende = $alt;
                  }
                  $return .= '<li><a href="'.$src_full.'" class="fancybox" rel="group_'.$galerie_count.'" title="'.$legende.'"><figure><img src="'.$src.'" alt="'.$legende.'" width="'.$width.'" height="'.$height.'" /><figcaption>'.$legende.'</figcaption></figure></li></a>';
                } 
              }
              $return .= '</ul></div>';
            }
          }
          break;

          case 'google_map':
            $contenu = $element['gm'];
            if(isset($element['gm_titre']) && !empty($element['gm_titre']) && !is_null($element['gm_titre'])){
              $contenu['title'] = $element['gm_titre'];
            }else{
              $contenu['title'] = $element['gm']['address'];
            }
            $contenu['zoom'] = $element['gm_zoom'];
            $contenu['type'] = $element['gm_type'];
            $gm_liste[] = $contenu;
            $gm_size = sizeof($gm_liste);
            $return .= '<div class="map-container"><div id="map_'.$gm_size.'" style="width:100%;height:350px;"></div></div>';
            //echo '<pre>';print_r($contenu);echo '</pre>';
            break;

            // onclick="trackOutboundLink('http://www.example.com'); return false;"

          case 'bouton':
            $texte = $element['btn_texte'];
            $lien = $element['btn_lien'];
            $nomEvenement = '19Mai2016';
            $return .= '<a target="_blank" href="'.$lien.'" title="'.$texte.'" class="btn btn_rouge" onclick="trackOutboundLink(\''.$lien.'\', \''.$nomEvenement.'\'); return false;"><span>'.$texte.'</span><i class="icon-fleche_droite"></i></a>';
            break;

          default:
            break;
      }
    }
  }
  return $return;
}


/**
 *
 *
 *  GESTION DES BLOCS DE CONTENUS - Affichage des blocs (echo)
 *
 *
 */
function atm_the_content($post_id = 0){
  $atm_get_the_content = atm_get_the_content($post_id);
  if(isset($atm_get_the_content) && !empty($atm_get_the_content) && !is_null($atm_get_the_content)){
    echo $atm_get_the_content;
  }else{
    return false;
  }
}


/**
 *
 *
 *  GESTION DES BLOCS DE CONTENUS - Récupération des blocs (return)
 *
 *
 */
function atm_get_the_content($post_id = 0, $limit=null, $strip=null, $continue=null){
  if($post_id == 0){
    global $post;
    $post_id = $post->ID;
  }
  
  $return = '';

  if(get_field('blocs_contenu',$post_id)){
    $diaporama_count = 0;
    $gm_liste = array();
    global $diaporama_count;
    global $diaporama_options;
    global $galerie_count;
    global $gm_liste;
    $return = '';

    $blocs_contenu = get_field('blocs_contenu',$post_id);
    if(isset($blocs_contenu) && !empty($blocs_contenu) && !is_null($blocs_contenu) && is_array($blocs_contenu) && sizeof($blocs_contenu) > 0 && $blocs_contenu !== false){  
      foreach($blocs_contenu as $bloc){
        switch ($bloc['acf_fc_layout']) {
          case 'une_colonne':
            $return .= '<div class="row">';
            $return .= '<div class="col-lg-12">';
            $array_contenus = $bloc['ajouter_du_contenu'];
            $return .= create_blocs($array_contenus);
            $return .= '</div>';
            $return .= '</div>';
            break;

        case 'deux_colonnes':
          $dimension_blocs = intval($bloc['dimension_blocs']);
          if($dimension_blocs == 5050){
            $class_gauche = '6';
            $class_droite = '6';
          }elseif($dimension_blocs == 3366){
            $class_gauche = '4';
            $class_droite = '8';
          }elseif($dimension_blocs == 6633){
            $class_gauche = '8';
            $class_droite = '4';
          }elseif($dimension_blocs == 2575){
            $class_gauche = '3';
            $class_droite = '9';
          }elseif($dimension_blocs == 7525){
            $class_gauche = '9';
            $class_droite = '3';
          }else{
            $class_gauche = '6';
            $class_droite = '6';
          }
          $return .= '<div class="row">';
          $return .= '<div class="col-lg-'.$class_gauche.' col-md-'.$class_gauche.' col-sm-12 col-xs-12">';
          $array_contenus = $bloc['ajouter_du_contenu_gauche'];
          $return .= create_blocs($array_contenus);
          $return .= '</div>';

          $return .= '<div class="col-lg-'.$class_droite.' col-md-'.$class_droite.' col-sm-12 col-xs-12">';
          $array_contenus = $bloc['ajouter_du_contenu_droite'];
          $return .= create_blocs($array_contenus);
          $return .= '</div>';

          $return .= '</div>';
          break;

          case 'trois_colonnes':
           $return .= '<div class="row">';
            $return .= '<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">';
            $array_contenus = $bloc['ajouter_contenu_1'];
            $return .= create_blocs($array_contenus);
            $return .= '</div>';

            $return .= '<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">';
            $array_contenus = $bloc['ajouter_contenu_2'];
            $return .= create_blocs($array_contenus);
            $return .= '</div>';

            $return .= '<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">';
            $array_contenus = $bloc['ajouter_contenu_3'];
            $return .= create_blocs($array_contenus);
            $return .= '</div>';

            $return .= '</div>';
            break;

          case 'quatre_colonnes':
            $return .= '<div class="row">';
            $return .= '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">';
            $array_contenus = $bloc['ajouter_du_contenu_une'];
            $return .= create_blocs($array_contenus);
            $return .= '</div>';

            $return .= '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">';
            $array_contenus = $bloc['ajouter_du_contenu_deux'];
            $return .= create_blocs($array_contenus);
            $return .= '</div>';

            $return .= '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">';
            $array_contenus = $bloc['ajouter_du_contenu_trois'];
            $return .= create_blocs($array_contenus);
            $return .= '</div>';

            $return .= '<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">';
            $array_contenus = $bloc['ajouter_du_contenu_quatre'];
            $return .= create_blocs($array_contenus);
            $return .= '</div>';

            $return .= '</div>';
            break;
                    
          default:
            break;
        }
      }
    }

    if(!empty($return) && isset($return) && !is_null($return)){
      if(!is_null($strip) && $strip){
        $return = strip_tags($return);
        $return = trim(preg_replace('/\s*\[[^)]*\]/', '', $return));
      }

      if (!is_null($limit) && $limit) {
        $return = explode(' ', $return, $limit);
        if(count($return)>=$limit){
          array_pop($return);
          $return = implode(" ",$return).'...';
        }else{
          $return = implode(" ",$return);
        }  
      }
    }else{$return = '';}

    return $return;
  }else{
    $return = get_the_content();

    if(!empty($return) && isset($return) && !is_null($return)){
      if(!is_null($strip) && $strip){
        $return = strip_tags($return);
        $return = trim(preg_replace('/\s*\[[^)]*\]/', '', $return));
      }else{
        $return = apply_filters('the_content',$return);
      }
      
      if (!is_null($limit) && $limit) {
        $return = explode(' ', $return, $limit);
        if(count($return)>=$limit){
          array_pop($return);
          $return = implode(" ",$return).'...';
        }else{
          $return = implode(" ",$return);
        }
      }
    }else{$return = '';}

    return $return;
  }
}

?>