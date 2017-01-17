<?php
/*
 *
 *
 *  ADMIN "TOUS LES EXPOSANTS"
 *
 *
 *
 */
?>
<?php
class Link_List_Table_Exposants extends WP_List_Table {

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
            echo '<ul class="subsubsub">
              <li class="all"><a class="current" href="edit.php?post_type=page">Tous <span class="count">(3)</span></a></li>
            </ul>';
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
            'cb'=>'<input type="checkbox" />',
            'str_raisonsociale'=>__('Raison sociale'),
            'str_codepostal'=>__('Code Postal'),
            'str_ville'=>__('Ville'),
            'str_pays'=>__('Pays'),
            'str_fixe'=>__('Tél. fixe'),
            'str_email'=>__('Email'),
            'int_annee_participation'=>__('Première participation'),
            'bl_actif'=>__('Actif'),
            'date_creation'=>__('Date d\'inscription')
        );
    }
    /**
     * Decide which columns to activate the sorting functionality on
     * @return array $sortable, the array of columns that can be sorted by the user
     */
    public function get_sortable_columns() {
        return $sortable = array(
          'str_raisonsociale'=>array('str_nom',false),
          'str_codepostal'=>array('str_codepostal',false),
          'str_ville'=>array('str_ville',false),
          'str_pays'=>array('str_pays',false),
          'int_annee_participation'=>array('int_annee_participation',false),
          'bl_actif'=>array('bl_actif',false),
          'date_creation'=>array('date_creation',false),
        );
    }
    /**
     * Prepare the table with different parameters, pagination, columns and table elements
     */
    function prepare_items($search = NULL) {
        global $wpdb, $_wp_column_headers;
        $screen = get_current_screen();
        $table_name = $wpdb->prefix.'ins_utilisateur_exposant';

        // If the value is not NULL, do a search for it.
        if( $search != NULL ){
            // Trim Search Term
            $search = trim($search);

            // Notice how you can search multiple columns for your search term easily, and return one data set 
            $query ="SELECT * FROM $table_name WHERE ( `str_raisonsociale` LIKE '%".$search."%' OR `str_codepostal` LIKE '%".$search."%' OR `str_ville` LIKE '%".$search."%' OR `str_pays` LIKE '%".$search."%' )";
        }elseif( isset($_GET['filterby']) && !empty($_GET['filterby']) && isset($_GET['filtervalue']) ){
          $filterby = atm_sanitize($_GET['filterby']);
          $filtervalue = atm_sanitize($_GET['filtervalue']);
          $query = "SELECT * FROM $table_name WHERE $filterby = '$filtervalue'";
        }else{
        // -- Preparing your query -- 
            $query = "SELECT * FROM $table_name";
        }

        // -- Ordering parameters -- 
            //Parameters that are going to be used to order the result
            $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'DESC';
            $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
            if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

        // -- Pagination parameters -- 
            //Number of elements in your table?
            if( $search != NULL ){
                $totalitems = $wpdb->query($query); //return the total number of affected rows
            }else{
                $totalitems = $wpdb->query($query); //return the total number of affected rows
            }
            //How many to display per page?
            $perpage = 100;
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

        // -- Register the pagination -- 
            $this->set_pagination_args( array(
                "total_items" => $totalitems,
                "total_pages" => $totalpages,
                "per_page" => $perpage,
            ) );
            //The pagination links are automatically built according to those parameters

        // -- Register the Columns -- 
            //$columns = $this->get_columns();
            //$_wp_column_headers[$screen->id]=$columns;
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);

        // -- Fetch the items -- 
            if( $search != NULL ){
                $this->items = $wpdb->get_results($query);
            }else{
                $this->items = $wpdb->get_results($query);
            }
    }
    
    /**
     * Display the rows of records in the table
     * @return string, echo the markup of the rows
     */
    function display_rows() {
        //include_once 'AtmApiCaller.php';
        //Get the records registered in the prepare_items method
        $records = $this->items;

        //Get the columns registered in the get_columns and get_sortable_columns methods
        list( $columns, $hidden ) = $this->get_column_info();

        //Loop for each record
        if(!empty($records)){foreach($records as $rec){
            //Open the line
            echo '<tr'.$color_site.' id="record_'.$rec->id_utilisateur_exposant.'" data-id="'.$rec->id_utilisateur_exposant.'">';
            foreach ( $columns as $column_name => $column_display_name ) {

                //Style attributes for each col
                $class = "class='$column_name column-$column_name'";
                $style = "";
                if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
                $attributes = $class . $style;
                
                //Display the cell
                switch ( $column_name ) {
                    case "cb": echo '<td data-attr="exposant" '.$attributes.'><input type="checkbox" /></td>'; break;
                    case "str_raisonsociale": echo '<td data-attr="str_raisonsociale" '.$attributes.'><strong><a class="row-title" href="'.home_url().'/wp-admin/admin.php?page=atm_cpt_add_exposant&exposant='.$rec->id_utilisateur_exposant.'&action=edit" title="Modifier">'.stripslashes($rec->str_raisonsociale).'</a></strong><div class="row-actions"><span class="edit"><a data-id="'.$rec->id_utilisateur_exposant.'" class="submitedit" title="Modifier" href="'.home_url().'/wp-admin/admin.php?page=atm_cpt_add_exposant&exposant='.$rec->id_utilisateur_exposant.'&action=edit">Modifier</a></span> | <span class="trash"><a data-id="'.$rec->id_utilisateur_exposant.'" class="submitdelete" title="Supprimer cet exposant" href="?page=atm_cpt_exposants&amp;atm_action=delete&amp;exposant='.$rec->id_utilisateur.'&amp;_wp_nonce='.wp_create_nonce( 'delete-exposants-nonce' ).'">Supprimer</a></span></div></div></td>'; break;
                    case "str_email": echo '<td data-attr="str_email" '.$attributes.'><a href="mailto:'.stripslashes($rec->str_email).'" title="Envoyer un email à l\'exposant">'.stripslashes($rec->str_email).'</a></td>'; break;
                    case "str_fixe": echo '<td data-attr="str_fixe" '.$attributes.'>'.stripslashes($rec->str_fixe).'</td>'; break;
                    case "int_annee_participation": echo '<td data-attr="int_annee_participation" '.$attributes.'>'.stripslashes($rec->int_annee_participation).'</td>'; break;
                    case "str_codepostal": echo '<td data-attr="str_codepostal" '.$attributes.'>'.stripslashes($rec->str_codepostal).'</td>'; break;
                    case "str_ville": echo '<td data-attr="str_ville" '.$attributes.'>'.stripslashes($rec->str_ville).'</td>'; break;
                    case "str_pays": echo '<td data-attr="str_pays" '.$attributes.'>'.stripslashes($rec->str_pays).'</td>'; break;
                    case "bl_actif": echo '<td data-attr="bl_actif" '.$attributes.'>';if(intval($rec->bl_actif) == 1){echo 'OUI';}else{echo 'NON';}echo '</td>'; break;
                    case "date_creation": echo '<td data-attr="date_creation" '.$attributes.'>'.date('d/m/Y',strtotime($rec->date_creation)).'</td>'; break;
                }
            }

            //Close the line
            echo'</tr>';
        }}
    }
}
?>
<style>

</style>
<div class="wrap">
  <h2>Tous les exposants <a class="add-new-h2" href="/wp-admin/admin.php?page=atm_cpt_add_exposant">Ajouter</a></h2>
  <?php
    /*
     *
     * Suppression d'un exposant
     *
     */
    if(isset($_GET['atm_action']) && !empty($_GET['atm_action']) && !is_null($_GET['atm_action']) && $_GET['atm_action'] == 'delete'){
      // Verify that the nonce is valid.
      if ( ! wp_verify_nonce( $_GET['_wp_nonce'], 'delete-exposants-nonce' ) && is_admin()) {
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
          echo '<div id="message" class="updated below-h2"><p>L\'exposant a bien été supprimé !</p></div>';
        }else{
          echo '<div id="message" class="error below-h2"><p>Erreur lors de la suppression de l\'exposant !</p></div>';
        }
      }
    }
  ?>
  <div id="poststuff">
    <div class="liste-exposants">
    <?php
      //Prepare Table of elements
      $list_table_etat = new Link_List_Table_Exposants();

      echo '<form method="post">
              <input type="hidden" name="page" value="'.$_REQUEST['page'].'"/>
              <p class="search-box">
                <label class="screen-reader-text" for="search_id-search-input">Recherche : </label> 
                <input id="search_id-search-input" type="text" name="s" value="" /> 
                <input id="search-submit" class="button" type="submit" name="" value="Recherche" />
              </p>
            </form>';
    ?>
    <?php
      if( isset($_POST['s']) ){
            $list_table_etat->prepare_items($_POST['s']);
          } else {
            $list_table_etat->prepare_items();
          }
      
      //Table of elements
      $list_table_etat->display();
    ?>
    </div>
</div>
