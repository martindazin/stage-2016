<?php  
/* 
Plugin Name: AWD Host Limits
Plugin URI: http://angerswebdev.fr/ 
Description: Gestion des informations relatives à l'espace disque alloué aux clients pour héberger leur site et leurs emails.
Version: 1.1.0 
Author: Antoine Brunet
Author URI: http://www.angerswebdev.fr
*/  
if(!isset($_SESSION)) {
    session_start();
}

//Our class extends the WP_List_Table class, so we need to make sure that it's there
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Link_List_Table extends WP_List_Table {

    /**
     * Constructor, we override the parent to pass our own arguments
     * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
     */
     function __construct() {
         parent::__construct( array(
        'singular'=> 'wp_list_text_link', //Singular label
        'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
        'ajax'  => false //We won't support Ajax for this table
        ) );
     }

     /**
     * Add extra markup in the toolbars before or after the list
     * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
     */
    function extra_tablenav( $which ) {
        /*if ( $which == "top" ){
            //The code that goes before the table is here
            echo "<h1>Liste des recherches de produits</h1>";
        }*/
        /*if ( $which == "bottom" ){
            //The code that goes after the table is there
            echo "Hi, I'm after the table";
        }*/
    }

    /**
     * Define the columns that are going to be used in the table
     * @return array $columns, the array of columns to use with the table
     */
    function get_columns() {
        return $columns= array(
            'col_link_client'=>__('Client'),
            'col_link_url'=>__('Url du site'),
            'col_link_e_site_o'=>__('Site : Espace occupé'),
            'col_link_e_site_a'=>__('Site : Espace alloué')
            // 'col_link_e_mails_o'=>__('Emails : Espace occupé'),
            // 'col_link_e_mails_a'=>__('Emails : Espace alloué')
        );
    }

    function get_columns_emails(){
        return $columns= array(
            'col_link_client'=>__('Client'),
            'col_link_url'=>__('Url du site'),
            // 'col_link_e_site_o'=>__('Site : Espace occupé'),
            // 'col_link_e_site_a'=>__('Site : Espace alloué'),
            'col_link_e_mails_o'=>__('Emails : Espace occupé'),
            'col_link_e_mails_a'=>__('Emails : Espace alloué')
        );
    }

    /**
     * Decide which columns to activate the sorting functionality on
     * @return array $sortable, the array of columns that can be sorted by the user
     */
    public function get_sortable_columns() {
        return $sortable = array(
            'col_link_client'=>array('client',false),
            'col_link_url'=>array('url',false),
            'col_link_e_site_o'=>array('e_site_o',false),
            'col_link_e_site_a'=>array('e_site_a',false),
            // 'col_link_e_mails_o'=>array('e_mail_o',false),
            // 'col_link_e_mails_a'=>array('e_mail_a',false)
        );
    }

    public function get_sortable_columns_emails() {
        return $sortable = array(
            'col_link_client'=>array('client',false),
            'col_link_url'=>array('url',false),
            // 'col_link_e_site_o'=>array('e_site_o',false),
            // 'col_link_e_site_a'=>array('e_site_a',false),
            'col_link_e_mails_o'=>array('e_mail_o',false),
            'col_link_e_mails_a'=>array('e_mail_a',false)
        );
    }

    /**
     * Prepare the table with different parameters, pagination, columns and table elements
     */
    function prepare_items($search = NULL) {
        global $wpdb, $_wp_column_headers;
        $screen = get_current_screen();
        $table_name = $wpdb->prefix.'clients';

        /* If the value is not NULL, do a search for it. */
        if( $search != NULL ){
            // Trim Search Term
            $search = trim($search);

            /* Notice how you can search multiple columns for your search term easily, and return one data set */
            $query ="SELECT * FROM $table_name WHERE NOT e_site_o = 0 OR NOT e_site_a = 0 AND `client` LIKE '%".$search."%' OR `url` LIKE '%".$search."%' ORDER BY client ASC";
        }else{
        /* -- Preparing your query -- */
            $query = "SELECT * FROM $table_name WHERE NOT e_site_o = 0 OR NOT e_site_a = 0 ORDER BY client ASC";
        }

        /* -- Ordering parameters -- */
            //Parameters that are going to be used to order the result
            $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
            $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
            if(!empty($orderby) & !empty($order)){ $query='SELECT * FROM '.$table_name.' WHERE NOT e_site_o = 0 OR NOT e_site_a = 0 ORDER BY `'.$orderby.'` '.$order; }

        /* -- Pagination parameters -- */
            //Number of elements in your table?
            if( $search != NULL ){
                $totalitems = $wpdb->query($query); //return the total number of affected rows
            }else{
                $totalitems = $wpdb->query($query); //return the total number of affected rows
            }
            //How many to display per page?
            $perpage = 200;
            //Which page is this?
            $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
            //Page Number
            if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
            //How many pages do we have in total?
            $totalpages = ceil($totalitems/$perpage);
            //adjust the query to take pagination into account
            if(!empty($paged) && !empty($perpage)){
                $offset=($paged-1)*$perpage;
                $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
            }

        /* -- Register the pagination -- */
            $this->set_pagination_args( array(
                "total_items" => $totalitems,
                "total_pages" => $totalpages,
                "per_page" => $perpage,
            ) );
            //The pagination links are automatically built according to those parameters

        /* -- Register the Columns -- */
            /*$columns = $this->get_columns();
            $_wp_column_headers[$screen->id]=$columns;*/
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);

        /* -- Fetch the items -- */
            if( $search != NULL ){
                $this->items = $wpdb->get_results($query);
            }else{
                $this->items = $wpdb->get_results($query);
            }
    }

    function prepare_items_emails($search = NULL){
        global $wpdb, $_wp_column_headers;
        $screen = get_current_screen();
        $table_name = $wpdb->prefix.'clients';

        /* If the value is not NULL, do a search for it. */
        if( $search != NULL ){
            // Trim Search Term
            $search = trim($search);

            /* Notice how you can search multiple columns for your search term easily, and return one data set */
            $query ="SELECT * FROM $table_name WHERE NOT e_mail_o = 0 OR NOT e_mail_a = 0 AND `client` LIKE '%".$search."%' OR `url` LIKE '%".$search."%' ORDER BY client ASC";
        }else{
        /* -- Preparing your query -- */
            $query = "SELECT * FROM $table_name WHERE NOT e_mail_o = 0 OR NOT e_mail_a = 0 ORDER BY `client` ASC";
        }

        /* -- Ordering parameters -- */
            //Parameters that are going to be used to order the result
            $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
            $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';

            if(!empty($orderby) & !empty($order)){ $query='SELECT * FROM '.$table_name.' WHERE NOT e_mail_o = 0 OR NOT e_mail_a = 0 ORDER BY `'.$orderby.'` '.$order; }

            // Pagination parameters
            //Number of elements in your table?
            if( $search != NULL ){
                $totalitems = $wpdb->query($query); //return the total number of affected rows
            }else{
                $totalitems = $wpdb->query($query); //return the total number of affected rows
            }
            //How many to display per page?
            $perpage = 200;
            //Which page is this?
            $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
            //Page Number
            if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
            //How many pages do we have in total?
            $totalpages = ceil($totalitems/$perpage);
            //adjust the query to take pagination into account
            if(!empty($paged) && !empty($perpage)){
                $offset=($paged-1)*$perpage;
                $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
            }

            // Register the pagination
            $this->set_pagination_args( array(
                "total_items" => $totalitems,
                "total_pages" => $totalpages,
                "per_page" => $perpage,
            ) );
            //The pagination links are automatically built according to those parameters

            // Register the Columns
            // $columns = $this->get_columns();
            // $_wp_column_headers[$screen->id]=$columns;
            $columns = $this->get_columns_emails();
            $hidden = array();
            $sortable = $this->get_sortable_columns_emails();
            $this->_column_headers = array($columns, $hidden, $sortable);

            // Fetch the items
            if( $search != NULL ){
                $this->items = $wpdb->get_results($query);
            }else{
                $this->items = $wpdb->get_results($query);
            }

            // echo '<br>';
            // echo "Ma dernière requête est égale à : ".$query;
    }

    /**
     * Display the rows of records in the table
     * @return string, echo the markup of the rows
     */
    function display_rows() {
        // include_once 'php/AtmApiCaller.php';
        //Get the records registered in the prepare_items method
        $records = $this->items;

        //Get the columns registered in the get_columns and get_sortable_columns methods
        list( $columns, $hidden ) = $this->get_column_info();

        //Loop for each record
        if(!empty($records)){foreach($records as $rec){
            $color_site = '';
            $site = -1;
            $emails = -1;
            if($rec->e_site_a > 0){ // plop
                // On détermine la couleur d'affichage de la ligne
                // Il faut d'y prendre d'une autre manière car le $color_site affiché à l'écran est le dernier pris
                // Conflit entre e_site_o, e_site_a ET e_mail_o, e_mail_a 
                if(((($rec->e_site_o) * 100 / $rec->e_site_a) < 80)) {
                    $color_site = '';
                    $site = 0;
                }
                if(($rec->e_site_o * 100 / $rec->e_site_a) >= 80){
                    $color_site = ' style="background-color:#FFE33E;"';
                    $site = 80;
                }
                if(($rec->e_site_o * 100 / $rec->e_site_a) >= 95){
                    $color_site = ' style="background-color:#FFB0B0;"';
                    $site = 95;
                }
            }
            if ($rec->e_site_o > $rec->e_site_a){
                $color_site = ' style="background-color:#FFB0B0;"';
                $site = 95;
            }
            if ($rec->e_mail_a > 0){
                if((((($rec->e_mail_o) * 100 / $rec->e_mail_a) < 80))){
                    $color_site = '';
                    $site = 0;
                }
                if(($rec->e_mail_o * 100 / $rec->e_mail_a) >= 80 ){
                    $color_site = ' style="background-color:#FFE33E;"';
                    $site = 80;
                }
                if(($rec->e_mail_o * 100 / $rec->e_mail_a) >= 95){
                    $color_site = ' style="background-color:#FFB0B0;"';
                    $site = 95;
                }
            }
            if ($rec->e_mail_o > $rec->e_mail_a){
                $color_site = ' style="background-color:#FFB0B0;"';
                $site = 95;
            }
            /*if($emails == -1 && $site >= 0){
                if($site == 0){
                    $color_site = '';
                }
                if($site == 80){
                    $color_site = ' style="background-color:#FFE33E;"';
                }
                if($site == 95){
                    $color_site = ' style="background-color:#FFB0B0;"';
                }
            }
            if($site == -1 && $emails >= 0){
                if($emails == 0){
                    $color_site = '';
                }
                if($emails == 80){
                    $color_site = ' style="background-color:#FFE33E;"';
                }
                if($emails == 95){
                    $color_site = ' style="background-color:#FFB0B0;"';
                }
            }
            if($site >= 0 && $emails >= 0){
                if($site == 0 && $emails == 0){
                    $color_site = '';
                }
                if(($site == 0 && $emails == 80) || ($site == 80 && $emails == 0) || ($site == 80 && $emails == 80)){
                    $color_site = ' style="background-color:#FFE33E;"';
                }
                if(($site == 95 && $emails <= 95) ||($emails == 95 && $site <= 95)){
                    $color_site = ' style="background-color:#FFB0B0;"';
                }
            }
            if($site == -1 && $emails == -1){
                $color_site = '';
            }*/
            echo $rec->id.' '.$rec->client.' '.$color_site.'<br>';
            //Open the line
            echo '<tr'.$color_site.' id="record_'.$rec->id.'" data-id="'.$rec->id.'">';
            foreach ( $columns as $column_name => $column_display_name ) {

                //Style attributes for each col
                $class = "class='$column_name column-$column_name'";
                $style = "";
                if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
                $attributes = $class . $style;

                $url = stripslashes($rec->url);
                if(preg_match('#http://#',$url)){
                    $url = preg_replace('#http://#','',$url);
                }

                // Affichage des colonnes
                switch ( $column_name ) {
                    case "col_link_client": echo '<td data-attr="client" '.$attributes.'><strong>'.stripslashes($rec->client).'</strong><div class="row-actions"><span class="trash"><a data-id="'.$rec->id.'" href="?page=awd_host_limits_awd&action=deleteClient&id='.$rec->id.'" title="Supprimer ce client" class="submitdelete">Supprimer</a></div></td>'; break;
                    case "col_link_url":   echo '<td data-attr="url" '.$attributes.'>'.$url.'</td>';   break;
                    case "col_link_e_site_o": echo '<td data-attr="e_site_o" '.$attributes.'>'.stripslashes($rec->e_site_o).'</td>'; break;
                    case "col_link_e_site_a": echo '<td data-attr="e_site_a" '.$attributes.'>'.stripslashes($rec->e_site_a).'</td>'; break;
                    case "col_link_e_mails_o": echo '<td data-attr="e_mail_o" '.$attributes.'>'.stripslashes($rec->e_mail_o).'</td>'; break;
                    case "col_link_e_mails_a": echo '<td data-attr="e_mail_a" '.$attributes.'>'.stripslashes($rec->e_mail_a).'</td>'; break;
                }
            }

            //Close the line
            echo'</tr>';
        }}
    }
}


/* VERIFICATION QU'UN AUTRE PLUGIN AVEC LE MEME NOM N'EXISTE PAS */
// Si la classe n'existe pas, on la créé
if (!class_exists("awd_host_limits"))
{
    class awd_host_limits {

        var $version = '1.1.0';

        /**
         * Constructeur
         */
        function awd_host_limits()
        {
            
        }
         /**
         * Sécurise les envois vers la base de données 
         * Supprime les codes malicieux d'une chaine de caractères
         * @global wpdb $wpdb
         */
        function sanitize($input) {
          if (is_array($input)) {
                foreach($input as $var=>$val) {
                    $output[$var] = $this->sanitize($val);
                }
            }
            else {
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
         * Vérifie si un site est en ligne ou hors-ligne
         * 
         */
        function checkOnline($domain) {
            $curlInit = curl_init($domain);
            curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
            curl_setopt($curlInit,CURLOPT_HEADER,true);
            curl_setopt($curlInit,CURLOPT_NOBODY,true);
            curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

            //get answer
            $response = curl_exec($curlInit);

            curl_close($curlInit);
            if ($response) return true;
            return false;
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
                $token .= $codeAlphabet[$this->crypto_rand_secure(0,strlen($codeAlphabet))];
            }
            return $token;
        }
         /**
         * Affichage du menu "Gestion des clients"
         * @global wpdb $wpdb
         */
        function printAdminGestion()
        {
            global $wpdb;
            if(!is_admin()) die();
            include('php/admin.php');
        }
        /**
         * Affichage du menu "Informations sur l'Agence"
         * @global wpdb $wpdb
         */
        /*function printAdminInformations()
        {
            global $wpdb;
            if(!is_admin()) die();
            include('php/admin_infos.php');
        }*/
        /**
         * Affichage du menu "Actualités de l'Agence"
         * @global wpdb $wpdb
         */
        /*function printAdminActualites()
        {
            global $wpdb;
            if(!is_admin()) die();
            include('php/admin_actus.php');
        }*/
        /**
         * Affichage du menu "Etat des sites clients - Mises à jour"
         * @global wpdb $wpdb
         */
        function printAdminMaj()
        {
            global $wpdb;
            if(!is_admin()) die();
            include('php/admin_etat_maj.php');
        }
        /**
         * Affichage du menu "Etat des sites clients - Mises à jour"
         * @global wpdb $wpdb
         */
        function printMails()
        {
            global $wpdb;
            if(!is_admin()) die();
            include('php/admin_mails.php');
        }
        /**
         * Enregistrement d'un nouveau sit
         * @global wpdb $wpdb
         */
        function ajax_add_site(){
            global $wpdb;
            if(!is_admin()) die();
            if (isset($_POST["client"]) && isset($_POST["url"])){
                $client     = $this->sanitize($_POST["client"]);
                $url        = $this->sanitize($_POST["url"]);

                $url = preg_replace('#/$#','',$url);
                if(!preg_match('#http://#',$url)){
                    $url = 'http://'.$url;
                }

                $ak = $this->getToken(32);

                $table_name = $wpdb->prefix.'api';
                $wpdb->insert($table_name, array(
                                'client' => $client,
                                'url' => $url,
                                'clef' => $ak
                            ), 
                            array( 
                                '%s',
                                '%s',
                                '%s'
                            ));

                $lastid = $wpdb->insert_id;

                $queryCheck = $wpdb->prepare("SELECT id FROM $table_name WHERE id='%d'",$lastid);
                $responses = $wpdb->get_results($queryCheck);
                // si il y a un résultat, l'album est bien enregistré -> on renvoit l'id et le titre
                if (sizeof($responses)>0) {
                    foreach ($responses as $reponse) {
                        $arr["success"] = true;
                        echo json_encode($arr);
                        break;
                    }
                // sinon -> Erreur
                }else{
                    $arr["success"] = false;
                    $arr["message"] = "Une erreur s'est produite lors de l'enregistrement. Veuillez ré-essayer...";
                    echo json_encode($arr);
                }
            }else{
                $arr["success"] = false;
                $arr["message"] = "Une erreur s'est produite lors de l'enregistrement. Veuillez ré-essayer...";
                echo json_encode($arr);
            }
            exit();
        }
        /**
         * Enregistrement d'un nouveau client
         * @global wpdb $wpdb
         */
        function ajax_add_client(){
            global $wpdb;
            if(!is_admin()) die();
            if (isset($_POST["client"]) && isset($_POST["url"]) && isset($_POST["e_site_a"]) && isset($_POST["e_site_o"]) && isset($_POST["e_mail_a"]) && isset($_POST["e_mail_o"])){
                $client     = $this->sanitize($_POST["client"]);
                $url        = $this->sanitize($_POST["url"]);

                $url = preg_replace('#/$#','',$url);
                if(!preg_match('#http://#',$url)){
                    $url = 'http://'.$url;
                }

                $e_site_a   = intval($this->sanitize($_POST["e_site_a"]));
                $e_site_o   = intval($this->sanitize($_POST["e_site_o"]));
                $e_mail_a   = intval($this->sanitize($_POST["e_mail_a"]));
                $e_mail_o   = intval($this->sanitize($_POST["e_mail_o"]));
                $table_name = $wpdb->prefix.'clients';
                $wpdb->insert($table_name, array(
                                'client' => $client,
                                'url' => $url,
                                'e_site_a' => $e_site_a,
                                'e_site_o' => $e_site_o,
                                'e_mail_a' => $e_mail_a,
                                'e_mail_o' => $e_mail_o
                            ), 
                            array( 
                                '%s',
                                '%s',
                                '%d',
                                '%d',
                                '%d',
                                '%d'
                            ));

                $lastid = $wpdb->insert_id;

                $queryCheck = $wpdb->prepare("SELECT * FROM $table_name WHERE id='%d'",$lastid);
                $responses = $wpdb->get_results($queryCheck);
                // si il y a un résultat, l'album est bien enregistré -> on renvoit l'id et le titre
                if (sizeof($responses)>0) {
                    foreach ($responses as $reponse) {
                        $color_site = '';
                        $site = -1;
                        $emails = -1; // plop
                        if($reponse->e_site_a > 0){
                            // On détermine la couleur d'affichage de la ligne
                            if(((($reponse->e_site_o) * 100 / $reponse->e_site_a) < 80)) {
                                $color_site = '';
                                $site = 0;
                            }
                            if(($reponse->e_site_o * 100 / $reponse->e_site_a) >= 80){
                                $color_site = ' style="background-color:#FFE33E;"';
                                $site = 80;
                            }
                            if(($reponse->e_site_o * 100 / $reponse->e_site_a) >= 95){
                                $color_site = ' style="background-color:#FFB0B0;"';
                                $site = 95;
                            }
                        }
                        if ($rec->e_site_o > $rec->e_site_a){
                            $color_site = ' style="background-color:#FFB0B0;"';
                            $site = 95;
                        }
                        if ($reponse->e_mail_a > 0){
                            if((((($reponse->e_mail_o) * 100 / $reponse->e_mail_a) < 80))){
                                $emails = 0;
                            }
                            if(($reponse->e_mail_o * 100 / $reponse->e_mail_a) >= 80 ){
                                $emails = 80;
                            }
                            if(($reponse->e_mail_o * 100 / $reponse->e_mail_a) >= 95){
                                $emails = 95;
                            }
                        }
                        if ($rec->e_mail_o > $rec->e_mail_a){
                            $color_site = ' style="background-color:#FFB0B0;"';
                            $site = 95;
                        }
                        /*if($emails == -1 && $site >= 0){
                            if($site == 0){
                                $color_site = '';
                            }
                            if($site == 80){
                                $color_site = ' style="background-color:#FFE33E;"';
                            }
                            if($site == 95){
                                $color_site = ' style="background-color:#FFB0B0;"';
                            }
                        }
                        if($site == -1 && $emails >= 0){
                            if($emails == 0){
                                $color_site = '';
                            }
                            if($emails == 80){
                                $color_site = ' style="background-color:#FFE33E;"';
                            }
                            if($emails == 95){
                                $color_site = ' style="background-color:#FFB0B0;"';
                            }
                        }
                        if($site >= 0 && $emails >= 0){
                            if($site == 0 && $emails == 0){
                                $color_site = '';
                            }
                            if(($site == 0 && $emails == 80) || ($site == 80 && $emails == 0) || ($site == 80 && $emails == 80)){
                                $color_site = ' style="background-color:#FFE33E;"';
                            }
                            if(($site == 95 && $emails <= 95) ||($emails == 95 && $site <= 95)){
                                $color_site = ' style="background-color:#FFB0B0;"';
                            }
                        }
                        if($site == -1 && $emails == -1){
                            $color_site = '';
                        }*/
                        $url = stripslashes($reponse->url);
                        if(preg_match('#http://#',$url)){
                            $url = preg_replace('#http://#','',$url);
                        }
                        $arr["success"] = true;
                        $arr["message"] = '<tr id="record_'.$reponse->id.'" data-id="'.$reponse->id.'"><td data-attr="client" class="col_link_client column-col_link_client"><strong>'.$reponse->client.'</strong><div class="row-actions"><span class="trash"><a data-id="'.$reponse->id.'" class="submitdelete" title="Supprimer ce client" href="?page=awd_host_limits_awd&amp;action=deleteClient&amp;id='.$reponse->id.'">Supprimer</a></span></div></td><td data-attr="url" class="col_link_url column-col_link_url">'.$url.'</td><td data-attr="e_site_o" class="col_link_e_site_o column-col_link_e_site_o">'.$reponse->e_site_o.'</td><td data-attr="e_site_a" class="col_link_e_site_a column-col_link_e_site_a">'.$reponse->e_site_a.'</td><td data-attr="e_mail_o" class="col_link_e_mails_o column-col_link_e_mails_o">'.$reponse->e_mail_o.'</td><td data-attr="e_mails_a" class="col_link_e_mail_a column-col_link_e_mails_a">'.$reponse->e_mail_a.'</td></tr>';
                        echo json_encode($arr);
                        break;
                    }
                // sinon -> Erreur
                }else{
                    $arr["success"] = false;
                    $arr["message"] = "Une erreur s'est produite lors de l'enregistrement. Veuillez ré-essayer...";
                    echo json_encode($arr);
                }
            }else{
                $arr["success"] = false;
                $arr["message"] = "Une erreur s'est produite lors de l'enregistrement. Veuillez ré-essayer...";
                echo json_encode($arr);
            }
            exit();
        }
        /**
         * Mise à jour d'une information client
         * @global wpdb $wpdb
         */
        function ajax_update_client(){
            global $wpdb;
            if(!is_admin()) die();
            if (isset($_POST["id"]) && isset($_POST["cell"]) && isset($_POST["content"])){
                $id      = intval($this->sanitize($_POST["id"]));
                $cell    = $this->sanitize($_POST["cell"]);
                $content = $this->sanitize($_POST["content"]);

                if($cell == 'url'){
                    if(!preg_match('#http://#',$content)){
                        $content = 'http://'.$content;
                    }
                }
                
                $table_name = $wpdb->prefix.'clients';
                $wpdb->update($table_name, 
                            array( 
                                $cell => $content
                            ), 
                            array( 'id' => $id )/*, 
                            array( 
                                '%s'
                            ), 
                            array( '%d' ) */
                );
                $arr["success"] = true;
                echo json_encode($arr);
            }else{
                $arr["success"] = false;
                $arr["message"] = "Une erreur s'est produite lors de l'enregistrement. Veuillez ré-essayer...";
                echo json_encode($arr);
            }
            exit();
        }
        /**
         * Suppression d'un client
         * @global wpdb $wpdb
         */
        function ajax_delete_client(){
            global $wpdb;
            if(!is_admin()) die();
            if(isset($_POST["id"]) && !empty($_POST["id"])){
                $id = intval($this->sanitize($_POST["id"]));

                $table_name = $wpdb->prefix.'clients';
                $queryDelete = $wpdb->prepare("DELETE FROM $table_name WHERE id = '%d'",$id);
                $wpdb->query($queryDelete);

                $arr["success"] = true;
                echo json_encode($arr);
            }else{
                $arr["success"] = false;
                $arr["message"] = "Une erreur s'est produite lors de l'enregistrement. Veuillez ré-essayer...";
                echo json_encode($arr);
            }
            exit();
        }
        /**
         * Suppression d'un site
         * @global wpdb $wpdb
         */
        function ajax_delete_site(){
            global $wpdb;
            if(!is_admin()) die();
            if(isset($_POST["id"]) && !empty($_POST["id"])){
                $id = intval($this->sanitize($_POST["id"]));

                $table_name = $wpdb->prefix.'api';
                $queryDelete = $wpdb->prepare("DELETE FROM $table_name WHERE id = '%d'",$id);
                $wpdb->query($queryDelete);

                $arr["success"] = true;
                echo json_encode($arr);
            }else{
                $arr["success"] = false;
                $arr["message"] = "Une erreur s'est produite lors de l'enregistrement. Veuillez ré-essayer...";
                echo json_encode($arr);
            }
            exit();
        }
         /**
         * Mise à jour des informations
         * @global wpdb $wpdb
         */
        function ajax_update_infos(){
            global $wpdb;
            if(!is_admin()) die();
            if(isset($_POST["content"]) && !empty($_POST["content"])){
                $contenu = $_POST["content"];

                update_option( 'hl_infos', $contenu );

                $arr["success"] = true;
                $arr["message"] = "Mise à jour des informations réussie !";
                echo json_encode($arr);
            }else{
                $arr["success"] = false;
                $arr["message"] = "Une erreur s'est produite lors de l'enregistrement. Veuillez ré-essayer...";
                echo json_encode($arr);
            }
            exit();
        }
        /**
         * Mise à jour de l'actualité
         * @global wpdb $wpdb
         */
        function ajax_update_actu(){
            global $wpdb;
            if(!is_admin()) die();
            if(isset($_POST["title"]) && !empty($_POST["title"]) && isset($_POST["content"]) && !empty($_POST["content"])){
                $titre = $this->sanitize($_POST["title"]);
                $contenu = $_POST["content"];

                update_option( 'hl_actu_titre', $titre );
                update_option( 'hl_actu_content', $contenu );

                $arr["success"] = true;
                $arr["message"] = "Mise à jour de l'actualité réussie !";
                echo json_encode($arr);
            }else{
                $arr["success"] = false;
                $arr["message"] = "Une erreur s'est produite lors de l'enregistrement. Veuillez ré-essayer...";
                echo json_encode($arr);
            }
            exit();
        }
        /**
         * 
         * Fonction qui renvoie l'url du favicon d'un site
         *
         */
        function getfavicon($url){
            $url = str_replace("http://",'',$url);
            return "http://www.google.com/s2/favicons?domain=".$url;
        } 
        /**
         * 
         * Fonction qui renvoie si un site est en ligne ou hors ligne
         *
         */
        function ajax_is_online(){
            // Get online or not
            $url = $this->sanitize($_POST["url"]);
            // Get favicon
            $favicon = $this->getfavicon($url);

            $arr["success"] = $this->checkOnline($url);
            $arr["favicon"] = $favicon;
            echo json_encode($arr);
            exit();
        }
        /**
         * 
         * Fonction qui revérifie l'état des mises à jour après un refresh pour un site donné
         *
         */
        function ajax_refresh_maj(){
            include_once 'php/AtmApiCaller.php';

            $clef = $this->sanitize($_POST["clef"]);
            $url = $this->sanitize($_POST["url"]);

            $apicaller = new AtmApiCaller('ATMUPDATES', $clef, $url.'/wp-content/plugins/atm_api_client/atm_api.php');
                     
            $items = $apicaller->sendRequest(array(
                'controller' => 'atmupdates',
                'action' => 'read'
            ));

            $auth_refresh = true;

            $wp_core = intval($items["wp_is_update"]);
            $wp_plugins = intval($items["wp_plugins_to_update"]);
            $wp_liste_plugins = $items["wp_plugins"];

            if($wp_core == 1){
                $r_wp_core = 'OK';
                $r_wp_core_data = 'OK';
            }else{
                $r_wp_core = '<a target="_blank" href="http://'.$url.'/wp-admin/update-core.php" title="Mettre à jour WordPress">MAJ</a>';
                $r_wp_core_data = 'MAJ';
            }
            if($wp_plugins == 0){
                $r_wp_plugins = 'OK';
                $r_wp_plugins_data = 'OK';
            }else{
                if(!is_null($wp_liste_plugins) && !empty($wp_liste_plugins) && isset($wp_liste_plugins)){
                    $liste = '<button class="display_plugins_list">Voir les plugins</button><br/><ul style="display:none;">';
                    foreach($wp_liste_plugins as $plug){
                        $liste  .= '<li data-file="'.$plug->file.'" data-zip="'.$plug->zip_url.'"><label><input type="checkbox" />'.$plug->nom.'</label></li>';
                    }
                    $liste .= '</ul>';
                    $r_wp_plugins = '<a target="_blank" href="http://'.$url.'/wp-admin/update-core.php" title="Mettre à jour les plugins">MAJ</a><div class="liste_plugins">'.$liste.'</div>';
                }else{
                    $r_wp_plugins = '<a target="_blank" href="http://'.$url.'/wp-admin/update-core.php" title="Mettre à jour les plugins">MAJ</a>';
                }
                $r_wp_plugins_data = 'MAJ';
            }

            $r_wp_plugins = '<div class="display_wp_plugins">'.$r_wp_plugins.'</div>';
            $arr["success"] = true;
            $arr["core"] = $r_wp_core;
            $arr["core_data"] = $r_wp_core_data;
            $arr["plugins"] = $r_wp_plugins;
            $arr["plugins_data"] = $r_wp_plugins_data;
            echo json_encode($arr);
            exit();
        }
        /**
         * 
         * Fonction qui lance la mise à jour des plugins pour un site donné
         *
         */
        function ajax_update_plugins(){
            include_once 'php/AtmApiCaller.php';

            $clef = $this->sanitize($_POST["clef"]);
            $url = $this->sanitize($_POST["url"]);
            $liste = $this->sanitize($_POST["plugins"]);

            $apicaller = new AtmApiCaller('ATMDOUPDATES', $clef, $url.'/wp-content/plugins/atm_api_client/update_plugins.php');
                     
            $items = $apicaller->sendRequest(array(
                'controller' => 'atmdoupdates',
                'action' => 'read',
                'req_data' => $liste
            ));

            //var_dump($items);

            $arr["success"] = true;
            $arr["reponse"] = $items;
            /*$arr["core"] = $r_wp_core;
            $arr["core_data"] = $r_wp_core_data;
            $arr["plugins"] = $r_wp_plugins;
            $arr["plugins_data"] = $r_wp_plugins_data;*/
            echo json_encode($arr);
            exit();
        }
        /*
         *
         *
         *
        */
        function ajax_get_the_table(){
            global $wpdb;
            include_once 'php/AtmApiCaller.php';
            $id = intval($this->sanitize($_POST["id"]));
            $table_name = $wpdb->prefix.'api';
            $records = $wpdb->get_results("SELECT * FROM $table_name WHERE id=$id");
            $arr = array();
            //Loop for each record
            if(!empty($records)){
                foreach($records as $rec){
                    if(preg_match('#http://#',$url)){
                        $url = preg_replace('#http://#','',$url);
                    }
                        
                    if(isset($rec->clef) && !is_null($rec->clef) && !empty($rec->clef) && intval($rec->affichage_actif) == 1 && intval($rec->desactive) == 0){
                        $pb = 'Pourtant OK';
                        $apicaller = new AtmApiCaller('ATMUPDATES', $rec->clef, $rec->url.'/wp-content/plugins/atm_api_client/atm_api.php');
                             
                        $items = $apicaller->sendRequest(array(
                            'controller' => 'atmupdates',
                            'action' => 'read'
                        ));
                            
                        $auth_refresh = true;

                        $wp_core = intval($items["wp_is_update"]);
                        $wp_plugins = intval($items["wp_plugins_to_update"]);
                        $wp_liste_plugins = $items["wp_plugins"];

                        if($wp_core == 1){
                            $r_wp_core = 'OK';
                            $r_wp_core_data = 'OK';
                        }else{
                            $r_wp_core = '<a target="_blank" href="http://'.$url.'/wp-admin/update-core.php" title="Mettre à jour WordPress">MAJ</a>';
                            $r_wp_core_data = 'MAJ';
                        }
                        if($wp_plugins == 0){
                            $r_wp_plugins = 'OK';
                            $r_wp_plugins_data = 'OK';
                        }else{
                            if(!is_null($wp_liste_plugins) && !empty($wp_liste_plugins) && isset($wp_liste_plugins)){
                                $liste = '<button class="display_plugins_list">Voir les plugins</button><br/><ul style="display:none;">';
                                foreach($wp_liste_plugins as $plug){
                                    $liste  .= '<li data-file="'.$plug->file.'" data-zip="'.$plug->zip_url.'"><label><input type="checkbox" />'.$plug->nom.'</label></li>';
                                }
                                $liste .= '</ul>';
                                $r_wp_plugins = '<a target="_blank" href="http://'.$url.'/wp-admin/update-core.php" title="Mettre à jour les plugins">MAJ</a><div class="liste_plugins">'.$liste.'</div>';
                            }else{
                                $r_wp_plugins = '<a target="_blank" href="http://'.$url.'/wp-admin/update-core.php" title="Mettre à jour les plugins">MAJ</a>';
                            }
                            $r_wp_plugins_data = 'MAJ';
                        }
                    }else{
                        $pb = 'Pas OK';
                        $r_wp_core = 'N/A';
                        $r_wp_core_data = 'N/A';
                        $r_wp_plugins = 'N/A';
                        $r_wp_plugins_data = 'N/A';
                        $auth_refresh = false;
                    }
                    $arr["pb"] = $pb;
                    $arr["col_link_wp_core"]            = $r_wp_core;
                    $arr["col_link_wp_core_data"]       = $r_wp_core_data;
                    $arr["col_link_wp_plugins"]         = $r_wp_plugins;
                    $arr["col_link_wp_plugins_data"]    = $r_wp_plugins_data;
                    
                    if($auth_refresh){
                        $arr["col_link_refresh"]        = '<button data-url="http://'.$url.'" data-clef="'.$rec->clef.'" class="refresh"><img src="/wp-content/plugins/awd_host_limits/images/refresh-icon.png" /></button><button data-url="http://'.$url.'" data-clef="'.$rec->clef.'" class="update_plugins"><img src="/wp-content/plugins/awd_host_limits/images/update-icon.png" /></button>';
                    }else{
                        $arr["col_link_refresh"]        = '';
                    }

                }
                $arr["success"] = true;
                echo json_encode($arr);
                exit();
            }else{
                $arr["success"] = false;
                echo json_encode($arr);
                exit();
            }
        }
        /*
         * Ajout des menus dans l'administration de WordPress
         *
         */
        function register_custom_menu_page() {
            add_menu_page(__('Administration des sites clients'), __('Administration des sites clients'), 'edit_themes', 'awd_host_limits_awd', array(&$this, 'printAdminGestion'), null, 100);
        }
        function register_my_custom_submenu_page() {
            add_submenu_page('awd_host_limits_awd', __('Liste des clients - Espace disque'), __('Liste des clients - Espace disque'), 'edit_themes', 'awd_host_limits_awd', array(&$this, 'printAdminGestion'));
            add_submenu_page('awd_host_limits_awd', __('Liste des clients - Espace mail'), __('Liste des clients - Espace mail'), 'edit_themes', 'md_etat_mail', array(&$this, 'printMails'));
            // add_submenu_page('awd_host_limits_awd', __('Informations sur l\'agence'), __('Informations sur l\'agence'), 'edit_themes', 'awd_informations_agence', array(&$this, 'printAdminInformations'));
            // add_submenu_page('awd_host_limits_awd', __('Actualité de l\'agence'), __('Actualité de l\'agence'), 'edit_themes', 'awd_actualites_agence', array(&$this, 'printAdminActualites'));
            add_submenu_page('awd_host_limits_awd', __('État des sites - Mises à jour'), __('État des sites - Mises à jour'), 'edit_themes', 'awd_etats_sites_clients', array(&$this, 'printAdminMaj'));
        }
    }

}
if (class_exists("awd_host_limits"))
{
    $inst_awd_host_limits = new awd_host_limits();

    if (isset($inst_awd_host_limits))
    {
        if(function_exists('add_action'))
        {
            add_action('admin_menu', array(&$inst_awd_host_limits, 'register_custom_menu_page'));
            add_action('admin_menu', array(&$inst_awd_host_limits, 'register_my_custom_submenu_page'));
        }
    }
    if(isset($_POST['action']))
    {
        switch($_POST['action'])
        {
            case 'hl_add_client':
                if(is_admin())
                    $inst_awd_host_limits->ajax_add_client();
                break;
            case 'hl_add_site':
                if(is_admin())
                    $inst_awd_host_limits->ajax_add_site();
                break;
            case 'hl_update_client':
                if(is_admin())
                    $inst_awd_host_limits->ajax_update_client();
                break;
            case 'hl_update_actu':
                if(is_admin())
                    $inst_awd_host_limits->ajax_update_actu();
                break;
            case 'hl_update_infos':
                if(is_admin())
                    $inst_awd_host_limits->ajax_update_infos();
                break;
            case 'hl_delete_client':
                if(is_admin())
                    $inst_awd_host_limits->ajax_delete_client();
                break;
            case 'hl_delete_site':
                if(is_admin())
                    $inst_awd_host_limits->ajax_delete_site();
                break;
            case 'hl_is_online':
                if(is_admin())
                    $inst_awd_host_limits->ajax_is_online();
                break;
            case 'hl_get_the_table':
                if(is_admin())
                    $inst_awd_host_limits->ajax_get_the_table();
                break;
            case 'hl_refresh':
                if(is_admin())
                    $inst_awd_host_limits->ajax_refresh_maj();
                break;
            case 'hl_update_plugins':
                if(is_admin())
                    $inst_awd_host_limits->ajax_update_plugins();
                break;
            default:
                break;
        }
    }
}
?>