<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C'est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'devatmospherec');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'devatmospherec');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'Qr1nGPWS');

/** Adresse de l'hébergement MySQL. */
define('DB_HOST', 'mysql.link');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');

/** Type de collation de la base de données.
  * N'y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/** Increasing memory allocated to PHP */
define('WP_MEMORY_LIMIT', '64M');

/** Définir le thème "atm-theme" par défaut */
define('WP_DEFAULT_THEME', 'atm-theme');

/**#@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '?O3=HXnJ%2)>H(YSS7SRV%Lx?T}z$)V}@Y;+V3cA|mAU!%6{]lY|cEQ%HGLbUOFY');
define('SECURE_AUTH_KEY',  '4,pp--0Jf::+S-Q+Y]ytRm4F:7-:F(l+vez.ZEwj 4+H$8!Oyt1B:e9#l<fzAB$o');
define('LOGGED_IN_KEY',    '4BQ/P@]<>5!-[wzKFVa.-RwTTutPV&38g!5J%IF|P0!B9=a?vVxo6Df7mg-Y(IPH');
define('NONCE_KEY',        'COGG<wWC^Gc33JY&Af<fB`,k4_k]N8swyNsY-8fa^n}LE-kwY?j$PeUqfX|,QxX_');
define('AUTH_SALT',        '7/wW3PHlfsIK%-RR^>6jppqNP*J*>(wLByDYK=L;H ksyu8_Iny=V=|sK2#0,/^p');
define('SECURE_AUTH_SALT', '31c]D0G{DXux)l!2{p|tmQ2)$#RWf]%XNFkAxRe?-j.&?[Q-g@h6`7+oNaqvb!)@');
define('LOGGED_IN_SALT',   '.=l%~&I/5GV,oy)*Ztb=HIB#!m:6~j=AvQ9T3r6Cp;f-o]KNBU1{=_VeZ`5>p-<f');
define('NONCE_SALT',       'P[vjJTw|Ho~#w||ad#k*GUh<eG)Y1M!zx.~sYf{gt;zq2IX8`rzsoG|JYxL]|^T|');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'test_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 */
define('WP_DEBUG', false);

/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');