<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Vous êtes maintenant abonné !</title>
    </head>
    <body>

        <?php
            // Permet de charger toutes les fonctionnalités de WP dpour un document externe
            require($_SERVER['DOCUMENT_ROOT'] . 'test/wp-load.php' );

            // Fonctions PHP //
            include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/fonctions.php');

            // Requêtes SQL //
            include(ABSPATH.'wp-content/plugins/atm_newsletter_md/views/others/requetes.php');

            global $wpdb;
            $table_subscriber =  $wpdb->prefix.'atm_nl_subscriber';

            if(true === array_key_exists('key', $_GET))
            {
                $wpdb->update($table_subscriber,
                                array('subscriber_status' => 'C'), 
                                array('subscriber_email' => $_GET['key'])
                );
            }
        ?>

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
                                        <td colspan="8" width="100%" style="font-size: 25px; text-align: center;">Logo de l'entreprise</a></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" width="100%" style="padding: 15px 25px;" bgcolor="#FFFFFF"></td>
                                    </tr>

                                    <!-- BODY -->
                                    <tr>
                                        <td colspan="8" width="100%" style="text-align: center; padding: 5px 25px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold; color: #0AA657;" bgcolor="#FFFFFF">CONFIRMATION DE VOTRE ADRESSE</td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" width="100%" style="padding: 5px" bgcolor="#0AA657"> </td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" width="100%" style="text-align: center; padding: 20px; font-family: Arial, Helvetica, sans-serif;">
                                            <b style="font-size: 15px; color: #0AA657">Vous êtes bien enregistré <?php echo $_GET['key']; ?> !</b>
                                        </td>
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
                                            <!-- RÉSEAUX SOCIAUX -->
                                            RÉSEAUX SOCIAUX
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
                                            <!-- NOM DE L'ENTREPRISE -->
                                            &copy; NOM DE L'ENTREPRISE
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
</html>