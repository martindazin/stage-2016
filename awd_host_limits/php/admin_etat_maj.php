<?php
class Link_List_Table_Etat_MAJ extends WP_List_Table {

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
            'col_link_en_ligne'=>__('État'),
            'col_link_wp_core'=>__('WP Core'),
            'col_link_wp_plugins'=>__('WP Plugins'),
            'col_link_refresh'=>__('Actions'),
            'col_link_api_key'=>__('API Key')
        );
    }
    /**
     * Decide which columns to activate the sorting functionality on
     * @return array $sortable, the array of columns that can be sorted by the user
     */
    public function get_sortable_columns() {
        return $sortable = array(
            'col_link_client'=>array('client',false)
        );
    }
    /**
     * Prepare the table with different parameters, pagination, columns and table elements
     */
    function prepare_items($search = NULL) {
        global $wpdb, $_wp_column_headers;
        $screen = get_current_screen();
        $table_name = $wpdb->prefix.'api';

        // If the value is not NULL, do a search for it.
        if( $search != NULL ){
            // Trim Search Term
            $search = trim($search);

            // Notice how you can search multiple columns for your search term easily, and return one data set 
            $query ="SELECT * FROM $table_name WHERE `client` LIKE '%".$search."%' OR `url` LIKE '%".$search."%' ORDER BY client ASC";
        }else{
        // -- Preparing your query -- 
            $query = "SELECT * FROM $table_name ORDER BY client ASC";
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
            echo '<tr'.$color_site.' id="record_'.$rec->id.'" data-id="'.$rec->id.'">';
            foreach ( $columns as $column_name => $column_display_name ) {

                //Style attributes for each col
                $class = "class='$column_name column-$column_name'";
                $style = "";
                if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
                $attributes = $class . $style;

                // Formatage de la date
                //$date = date("d/m/Y \à H\hi", strtotime(stripslashes($rec->date)));
                $url = stripslashes($rec->url);
                
                $is_online = '...';
                
                if(preg_match('#http://#',$url)){
                    $url = preg_replace('#http://#','',$url);
                }
                
                /*if(isset($rec->clef) && !is_null($rec->clef) && !empty($rec->clef) && $rec->affichage_actif == 1 && $rec->desactive == 0){
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
                }else{*/
                     $r_wp_core = '<span class="dashicons dashicons-minus"></span>';
                     $r_wp_core_data = 'N/A';
                     $r_wp_plugins = '<span class="dashicons dashicons-minus"></span>';
                     $r_wp_plugins_data = 'N/A';
                     $auth_refresh = false;
                //}
                
                //Display the cell
                switch ( $column_name ) {
                    case "col_link_client": echo '<td data-attr="client" '.$attributes.'>'.stripslashes($rec->client).'<div class="row-actions"><span class="trash"><a data-id="'.$rec->id.'" class="submitdelete" title="Supprimer ce site" href="?page=awd_host_limits_awd&amp;action=deleteSitet&amp;id='.$rec->id.'">Supprimer</a></span></div></td>'; break;
                    case "col_link_url":   echo '<td data-attr="url" '.$attributes.'><img src="" /><span><a target="_blank" href="http://'.$url.'/wp-admin/" title="Accéder à l\'admin du site">'.$url.'</a></span></td>';   break;
                    case "col_link_en_ligne": echo '<td data-attr="en_ligne" '.$attributes.'><img src="/wp-admin/images/spinner.gif" alt="..." /></td>'; break;
                    case "col_link_wp_core": echo '<td data-attr="wp_core" '.$attributes.' data-etat="'.$r_wp_core_data.'">'.$r_wp_core.'</td>'; break;
                    case "col_link_wp_plugins": echo '<td data-attr="wp_plugins" '.$attributes.' data-etat="'.$r_wp_plugins_data.'"><div class="display_wp_plugins">'.$r_wp_plugins.'</div></td>'; break;
                    case "col_link_refresh": echo '<td data-attr="refresh" '.$attributes.'>';
                        if($auth_refresh){
                            echo '<button data-url="http://'.$url.'" data-clef="'.$rec->clef.'" class="refresh"><img src="/wp-content/plugins/awd_host_limits/images/refresh-icon.png" /></button><button data-url="http://'.$url.'" data-clef="'.$rec->clef.'" class="update_plugins"><img src="/wp-content/plugins/awd_host_limits/images/update-icon.png" /></button>';
                        }else{
                            echo '</td>';
                        }
                         break;
                    case "col_link_api_key":   echo '<td data-attr="api_key" '.$attributes.'><input type="text" class="shortcode-in-list-table wp-ui-text-highlight code" value="'.$rec->clef.'" readonly="readonly" onfocus="this.select();"></td>';   break;
                }
            }

            //Close the line
            echo'</tr>';
        }}
    }
}
?>
<style>
.site,.mails{display:none;}.enregistrer_client{margin-top:1em !important;}.col_link_url img, .col_link_url span{display: inline-block;vertical-align: middle;}.col_link_url img{width:16px;height:16px;margin-right:10px;}.refresh,.update_plugins{background:none;border:none;cursor:pointer;}.update_plugins{display:none;}
#col_link_client{width:20%;}#col_link_url{width:20%;}#col_link_en_ligne{width:5%;}#col_link_wp_core{width:5%;}#col_link_wp_plugins{width:14%;}#col_link_refresh{width:10%;}#col_link_api_key{width:20%;}.shortcode-in-list-table.wp-ui-text-highlight.code{width:100%;}
#the-list tr{display:none;}
.col_link_en_ligne .dashicons, .col_link_wp_core .dashicons, .col_link_wp_plugins .dashicons {
    display: block;
    font-size: 2.5em;
    margin: auto;
    position: relative;
    text-align: left;
    width: auto;
}
</style>
<div class="wrap">
    <div id="icon-edit" class="icon32"><br></div>
    <h2>État des sites clients - Mises à jour</h2>
    <div id="poststuff">
        <div class="postbox">
            <h3 class="hndle">Ajouter un site</h3>
            <div class="inside">
                <label><span style="display:inline-block;width:100px;">Nom du client : </span><input type="text" nom="client" class="client" value="" /></label><br/>
                <label><span style="display:inline-block;width:100px;">Url du site : </span><input type="text" nom="url" class="url" value="" /></label><br/>
                <button class="button button-primary button-large enregistrer_site">Enregistrer</button>
            </div>
        </div>
        <div class="error below-h2 chargement" id="message">
            <p class="etat_des_sites">Chargement de l'état des sites : <span class="es_nb">0</span> / <span class="es_tot"></span></p>
            <p class="etat_des_updates">Chargement de l'état des mises à jour : <span class="emaj_nb">0</span> / <span class="emaj_tot"></span></p>
        </div>
        <div class="api_table">
        <?php
            //Prepare Table of elements
            $list_table_etat = new Link_List_Table_Etat_MAJ();

            echo '<form method="post">
    <input type="hidden" name="page" value="'.$_REQUEST['page'].'"/><p class="search-box">
<label class="screen-reader-text" for="search_id-search-input">
search:</label> 
<input id="search_id-search-input" type="text" name="s" value="" /> 
<input id="search-submit" class="button" type="submit" name="" value="Recherche" />
</p></form>';

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
<script>
var _path = '<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php';
jQuery(function($){
    $(".wp_list_test_links").on("click",".col_link_api_key",function(e){
        e.preventDefault();
        $(this).children('code').select();
    });

    // Refresh
    $('.api_table').on('click','.refresh',function(){
        var $this = $(this);
        $this.hide();
        var clef = $this.attr('data-clef');
        var url = $this.attr('data-url');
        if(url!=='' && clef !== ''){
            jQuery.ajax({
                type: 'post',
                url: _path,
                data: {url:url,clef:clef,action:'hl_refresh'},
                success: function(_data){
                    var result = jQuery.parseJSON(_data);
                    if(result.success){
                        if(result.core == 'OK'){var content_core = '<span class="dashicons dashicons-yes"></span>';}else{var content_core = '<span class="dashicons dashicons-dismiss"></span>';}
                        if(result.plugins == 'OK'){var content_plugins = '<span class="dashicons dashicons-yes"></span>';}else{var content_plugins = '<span class="dashicons dashicons-dismiss"></span>';}
                        $this.parents('tr').children('.col_link_wp_core').html(content_core);
                        $this.parents('tr').children('.col_link_wp_core').attr('data-etat',result.core_data);

                        $this.parents('tr').children('.col_link_wp_plugins').html(content_plugins);
                        $this.parents('tr').children('.col_link_wp_plugins').attr('data-etat',result.plugins_data);

                        if($this.parents('tr').children('.col_link_wp_core').attr('data-etat') == 'OK'){
                            $this.parents('tr').children('.col_link_wp_core').css('color','#7AD03A');
                        }
                        if($this.parents('tr').children('.col_link_wp_core').attr('data-etat') == 'MAJ'){
                            $this.parents('tr').children('.col_link_wp_core').css('color','#DD3D36');
                        }
                        if($this.parents('tr').children('.col_link_wp_plugins').attr('data-etat') == 'OK'){
                            $this.parents('tr').children('.col_link_wp_plugins').css('color','#7AD03A');
                        }
                        if($this.parents('tr').children('.col_link_wp_plugins').attr('data-etat') == 'MAJ'){
                            $this.parents('tr').children('.col_link_wp_plugins').css('color','#DD3D36');
                        }
                        $this.show();
                    }else{
                        $this.show();
                    }
                }
            });
        }
    });
    
    function dump(obj) {
        var out = '';
        for (var i in obj) {
            out += i + ": " + obj[i] + "\n";
        }

        console.log(out);
    }

    function do_update(url, clef, liste, nbDone, nbPlugins, el, _path){
        jQuery.ajax({
                type: 'post',
                url: _path,
                data: {url:url,clef:clef,plugins:liste[nbDone],action:'hl_update_plugins'},
                success: function(_data){
                    var result = jQuery.parseJSON(_data);
                    if(result.success){
                        console.log(result.reponse[0].nom);
                        console.log(result.reponse[0].statut);
                        if(result.reponse[0].statut == "success"){
                            el.parents('tr').children('.col_link_wp_plugins').find('li[data-file="'+result.reponse[0].nom+'"]').remove();
                        }
                        nbDone = nbDone + 1;
                        if (nbDone < nbPlugins){
                            do_update(url, clef, liste, nbDone, nbPlugins, el, _path);
                        }else{
                            el.parents('tr').children('.col_link_wp_plugins').find('.upload_in_progress').remove();
                            if(!el.parents('tr').children('.col_link_wp_plugins').find('ul').children('li').length){
                                el.parents('tr').children('.col_link_wp_plugins').children('.display_wp_plugins').html('OK');
                                el.parents('tr').children('.col_link_wp_plugins').attr('data-etat','OK');
                                el.parents('tr').children('.col_link_wp_plugins').css('color','#7AD03A');
                                el.hide();
                            }else{
                                el.show();
                            }
                            el.parents('tr').children('.col_link_wp_plugins').children('.display_wp_plugins').show();
                        }
                    }
                }
            });
    }

    // Update
    $('.api_table').on('click','.update_plugins',function(){
        var $this = $(this);
        $this.hide();
        var clef = $this.attr('data-clef');
        var url = $this.attr('data-url');

        var html_liste = $this.parents('tr').children('.col_link_wp_plugins').find('ul');
        var liste = new Array();
        html_liste.find('input[type="checkbox"]').each(function(){
            var $el = $(this);
            if($el[0].checked){
                var arr = new Array();
                arr.push($el.parents('li').attr('data-zip'));
                arr.push($el.parents('li').attr('data-file'));
                liste.push(arr);
            }
        });
        if (liste == ''){
            alert('Vous devez sélectionner les plugins à mettre à jour...');
            $this.show();
        }else{
            $this.parents('tr').children('.col_link_wp_plugins').append('<img class="upload_in_progress" src="/wp-admin/images/spinner.gif" alt="..." />');
            $this.parents('tr').children('.col_link_wp_plugins').children('.display_wp_plugins').hide();
            dump(liste);
            var nbDone = 0;
            var nbPlugins = liste.length;
            do_update(url, clef, liste, nbDone, nbPlugins, $this, _path);
        }
    });

    // Afficher la liste des plugins à mettre à jour
    $('.api_table').on('click','.display_plugins_list',function(){
        if(!$(this).hasClass('active')){
            $(this).parent().children('ul').slideDown();
            $(this).addClass('active');
            $(this).html('Masquer les plugins');
            $(this).parents('tr').children('.col_link_refresh').children('.update_plugins').show();
        }else{
            $(this).parent().children('ul').slideUp();
            $(this).removeClass('active');
            $(this).html('Voir les plugins');
            $(this).parents('tr').children('.col_link_refresh').children('.update_plugins').hide();
        }
    });

    //
    $(document).ready(function(){
        var rowCount = $('#the-list tr').length;
        $('.emaj_tot').html(rowCount);
        $('.es_tot').html(rowCount);

        $('.col_link_url').each(function(){
            var $this = $(this);
            var url = 'http://'+$this.html();
            var id = $this.parent('tr').attr('data-id');
            if(url!=='http://'){
                jQuery.ajax({
                    type: 'post',
                    url: _path,
                    data: {url:url,action:'hl_is_online'},
                    success: function(_data){
                        var result = jQuery.parseJSON(_data);
                        if(result.success){
                            $this.parent().children('.col_link_url').children('img').attr('src',result.favicon);
                            $this.next('.col_link_en_ligne').css('color','#7AD03A');
                            $this.next('.col_link_en_ligne').html('<span class="dashicons dashicons-admin-site"></span>');
                        }else{
                            $this.next('.col_link_en_ligne').css('color','#DD3D36');
                            $this.next('.col_link_en_ligne').html('<span class="dashicons dashicons-welcome-comments"></span>');
                        }
                        var es = parseInt($('.es_nb').html()) + 1;
                        $('.es_nb').html(es);
                        $this.parents('tr').show();
                        if(es == rowCount){
                            $('.etat_des_sites').hide();
                        }
                        //hl_get_the_table
                        jQuery.ajax({
                            type: 'post',
                            url: _path,
                            data: {id:id,action:'hl_get_the_table'},
                            success: function(_data){
                                var result = jQuery.parseJSON(_data);
                                if(result.success){
                                    if(result.col_link_wp_core_data == 'OK'){
                                        $this.parents('tr').children('.col_link_wp_core').css('color','#7AD03A');
                                        var content_core = '<span class="dashicons dashicons-yes"></span>';
                                    }
                                    if(result.col_link_wp_core_data == 'MAJ'){
                                        $this.parents('tr').children('.col_link_wp_core').css('color','#DD3D36');
                                        var content_core = '<span class="dashicons dashicons-dismiss"></span>';
                                    }
                                    if(result.col_link_wp_plugins_data == 'OK'){
                                        $this.parents('tr').children('.col_link_wp_plugins').css('color','#7AD03A');
                                        var content_plugins = '<span class="dashicons dashicons-yes"></span>';
                                    }
                                    if(result.col_link_wp_plugins_data == 'MAJ'){
                                        $this.parents('tr').children('.col_link_wp_plugins').css('color','#DD3D36');
                                        var content_plugins = '<span class="dashicons dashicons-dismiss"></span>';
                                    }
                                    $this.parents('tr').children('.col_link_wp_core').html(content_core);
                                    $this.parents('tr').children('.col_link_wp_core').attr('data-etat',result.col_link_wp_core_data);
                                    $this.parents('tr').children('.col_link_wp_plugins').html(content_plugins);
                                    $this.parents('tr').children('.col_link_wp_plugins').attr('data-etat',result.col_link_wp_plugins_data);
                                    $this.parents('tr').children('.col_link_refresh').html(result.col_link_refresh);
                                    $this.parents('tr').show();
                                    var emaj = parseInt($('.emaj_nb').html()) + 1;
                                    $('.emaj_nb').html(emaj);
                                    if(emaj == rowCount){
                                        $('.chargement').hide();
                                    }
                                }
                            }
                        });
                    }
                });
            }
        });
    });
    /*
                    if(result.core == 'OK'){var content_core = '<span class="dashicons dashicons-yes"></span>';}else{var content_core = '<span class="dashicons dashicons-dismiss"></span>';}
                        if(result.plugins == 'OK'){var content_plugins = '<span class="dashicons dashicons-yes"></span>';}else{var content_plugins = '<span class="dashicons dashicons-dismiss"></span>';}
    */
    // Enregistrement d'un nouveau site
    $('.inside').on('click','.enregistrer_site',function(e){
        e.preventDefault();
        var client = $('.client').val();
        var url = $('.url').val();

        if(client == ''){
            alert('Vous devez entrer le nom du client...');
            return false;
        }

        if(url == ''){
            alert('Vous devez entrer l\'url du site client...');
            return false;
        }

        jQuery.ajax({
            type: 'post',
            url: _path,
            data: {client:client,url:url,action:'hl_add_site'},
            success: function(_data){
                var result = jQuery.parseJSON(_data);
                if(result.success){
                    window.location = '<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=awd_etats_sites_clients';
                }else{
                    if($('#message').length){
                        $('#message').fadeOut(function(){$('#message').remove();$('h2').after('<div class="error below-h2" id="message"><p>'+result.message+'</p></div>');})
                    }else{
                        $('h2').after('<div class="error below-h2" id="message"><p>'+result.message+'</p></div>');
                    }
                    alert(result.message);
                }
            }
        });
    });

    // Suppression d'un site
    $('.api_table').on('click','a.submitdelete',function(e){
        e.preventDefault();
        var id = $(this).attr("data-id");
        if(confirm('Etes-vous sûr de vouloir supprimer ce site ?')){
            jQuery.ajax({
                type: 'post',
                url: _path,
                data: {id:id,action:'hl_delete_site'},
                success: function(_data){
                    var result = jQuery.parseJSON(_data);
                    if(result.success){
                        $('.wp_list_test_links').find('#record_'+id).fadeOut('slow',function(){$(this).remove();});
                    }else{
                        if($('#message').length){
                            $('#message').fadeOut(function(){$('#message').remove();$('h2').after('<div class="error below-h2" id="message"><p>'+result.message+'</p></div>');})
                        }else{
                            $('h2').after('<div class="error below-h2" id="message"><p>'+result.message+'</p></div>');
                        }
                        alert(result.message);
                    }
                }
            });
        }
    });
});
</script>