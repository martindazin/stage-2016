<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<?php
    // Fonctions PHP //
    include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

    // Requêtes SQL //
    include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');

    // Bouton supprimer
    if(isset($_POST['newsletter']) && !empty($_POST['newsletter']) && !is_null($_POST['newsletter']) && is_numeric($_POST['newsletter'])){
        $suppressionNl = $wpdb->delete($table_nl, array('newsletter_showed_id' => $_POST['newsletter']));
    }

   	echo '<table>';
        echo '<thead>';
            echo '<tr>';
                echo '<th>Nom</th>';
                echo '<th>Statut</th>';
                echo '<th>Nombre d\'abonnés</th>';
                echo '<th>Date</th>';
                echo '<th>&nbsp;</th>';
                echo '<th>&nbsp;</th>';
                echo '<th>&nbsp;</th>';
                echo '<th>&nbsp;</th>';
                echo '<th>&nbsp;</th>';
            echo '</tr>';
        echo '</thead>';

        echo '<tbody>';
            if(isset($toutesLesNlSansCDec) && !empty($toutesLesNlSansCDec) && !is_null($toutesLesNlSansCDec) && is_array($toutesLesNlSansCDec) && $toutesLesNlSansCDec !== false) {
                foreach ($toutesLesNlSansCDec as $nl) {
                    $nlSubjectSansCDec = $nl->newsletter_subject;
                    // On convertit le type de date retourné par la BDD
                    $nlDateSansCDec = date('d\-m\-Y H\:i\:s', strtotime($nl->newsletter_date));
                    $nlStatusSansCDec = $nl->newsletter_status;
                    $nlTotalSansCDec = $nl->newsletter_total;

                    // On récupère la nl Créée correspondante avec le même newsletter_showed_id
                    $nlShowedIdSansC = $nl->newsletter_showed_id;

                    $ligneNlC = $wpdb->get_row("SELECT *
                                               FROM ".$table_nl.
                                               " WHERE `newsletter_showed_id` LIKE ".$nlShowedIdSansC.
                                               " AND `newsletter_status` = 'C'");

                    $nlDateC = date('d\-m\-Y H\:i\:s', strtotime($ligneNlC->newsletter_date));

                    if ($nlStatusSansCDec == 'T') {
                        $nlStatusSansCDec = "Créée";
                        echo '<tr>';
                            echo '<td>'.$nlSubjectSansCDec.'</td>';
                            echo '<td>'.$nlStatusSansCDec.'</td>';
                            echo '<td> 0 sur '.$nombreDeConfirmes.' </td>';
                            echo '<td>'.$nlDateC.'</td>';
                            echo '<td>
                                    <form method="post" action="'.esc_url(home_url('/')).'wp-content/plugins/atm_newsletter_md/views/nl/nl_boutons.php">
                                        <input type="hidden" name="newsletter" value="'.$nl->newsletter_showed_id.'"/>
                                        <button type="submit" name="event" value="Visualiser">Visualiser</button>
                                    </form>
                                </td>';
                            echo '<td>
                                <form method="post" action="'.esc_url(home_url('/')).'wp-content/plugins/atm_newsletter_md/views/nl/nl_boutons.php">
                                    <input type="hidden" name="newsletter" value="'.$nl->newsletter_showed_id.'"/>
                                    <button type="submit" name="event" value="EnvoyerTest">Envoyer un test</button>
                                </form>
                            </td>';
                            echo '<td>
                                    <form method="post" action="'.esc_url(home_url('/')).'wp-content/plugins/atm_newsletter_md/views/nl/nl_boutons.php">
                                        <input type="hidden" name="newsletter" value="'.$nl->newsletter_showed_id.'"/>
                                        <button type="submit" name="event" value="EnvoyerReel">Envoyer réellement</button>
                                    </form>
                                </td>';
                            echo '<td>
                                    <form method="post" action="'.esc_url(home_url('/')).'wp-content/plugins/atm_newsletter_md/views/nl/nl_boutons.php">
                                        <input type="hidden" name="newsletter" value="'.$nl->newsletter_showed_id.'"/>
                                        <button type="submit" name="event" value="Statistiques">Statistiques</button>
                                    </form>
                                </td>';
                            echo '<td>
                                    <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
                                        <input type="hidden" name="newsletter" value="'.$nl->newsletter_showed_id.'"/>
                                        <button type="submit" name="event" value="Supprimer">Supprimer</button>
                                    </form>
                                </td>';
                        echo '</tr>';
                    } else if ($nlStatusSansCDec == 'E') {
                        $nlStatusSansCDec = "Envoyée";
                        echo '<tr>';
                            echo '<td>'.$nlSubjectSansCDec.'</td>';
                            echo '<td>'.$nlStatusSansCDec.'</td>';
                            echo '<td>'.$nlTotalSansCDec." sur ".$nlTotalSansCDec.'</td>';
                            echo '<td>'.$nlDateSansCDec.'</td>';
                            echo '<td>
                                    <form method="post" action="'.esc_url(home_url('/')).'wp-content/plugins/atm_newsletter_md/views/nl/nl_boutons.php">
                                        <input type="hidden" name="newsletter" value="'.$nl->newsletter_showed_id.'"/>
                                        <button type="submit" name="event" value="Visualiser">Visualiser</button>
                                    </form>
                                </td>';
                            echo '<td colspan="2">
                                    Vous ne pouvez plus envoyer cette newsletter car elle a déjà été envoyée !
                                </td>';
                            echo '<td>
                                    <form method="post" action="'.esc_url(home_url('/')).'wp-content/plugins/atm_newsletter_md/views/nl/nl_boutons.php">
                                        <input type="hidden" name="newsletter" value="'.$nl->newsletter_showed_id.'"/>
                                        <button type="submit" name="event" value="Statistiques">Statistiques</button>
                                    </form>
                                </td>';
                            echo '<td>
                                    <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
                                        <input type="hidden" name="newsletter" value="'.$nl->newsletter_showed_id.'"/>
                                        <button type="submit" name="event" value="Supprimer">Supprimer</button>
                                    </form>
                                </td>';
                        echo '</tr>';
                    }  
                }
            }
        echo '</tbody>';
        echo '<tfoot>';
            echo '<tr>';
                echo '<td colspan="9">Le bouton "Envoyer un test" enverra la newsletter sélectionnée à toutes les adresses de tests.</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td colspan="9">Le bouton "Envoyer réellement" enverra la newsletter sélectionnée à tous les utilisateurs qui ont confirmés leur abonnement.</td>';
            echo '</tr>';
        echo '</tfoot>';
    echo '</table>';
?>