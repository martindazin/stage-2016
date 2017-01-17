<?php /* Chargement du header "X-UA-Compatible" pour IE */ atm_set_xua_header(); ?>
<!doctype html>
<!--[if lt IE 7]><html prefix="og: http://ogp.me/ns#" <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html prefix="og: http://ogp.me/ns#" <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html prefix="og: http://ogp.me/ns#" <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html prefix="og: http://ogp.me/ns#" <?php language_attributes(); ?> class="no-js"><!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<title><?php global $page, $paged; if(is_home()){wp_title( '|', true, 'right' ); bloginfo( 'name' );$site_description = get_bloginfo( 'description', 'display' ); if ( $site_description && ( is_home() || is_front_page() ) ) echo " | $site_description";}elseif(get_field('seo_titre',$post->ID)){echo get_field('seo_titre',$post->ID);}else{wp_title( '|', true, 'right' ); bloginfo( 'name' ); $site_description = get_bloginfo( 'description', 'display' ); if ( $site_description && ( is_home() || is_front_page() ) ) echo " | $site_description"; if ( $paged >= 2 || $page >= 2 ) echo ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );} ?></title>
		<?php
			$description = get_option( 'site_meta_description' );
			// Coupe un texte à $longueur caractères sur les espaces
			function tronque($chaine, $longueur = 120){
				if (empty ($chaine)){
					return $description;
				}
				elseif (strlen ($chaine) < $longueur){
					return $chaine;
				}
				elseif (preg_match ("/(.{1,$longueur})\s./ms", $chaine, $match)){
					return $match [1] . "...";
				}
				else{
					return substr ($chaine, 0, $longueur) . "...";
				}
			} 
			if (is_page() || is_single()){
				$page = get_page($post->ID);
				$page_content = strip_shortcodes($page->post_content);
				$content = htmlspecialchars(strip_tags(trim($page_content)));
				$desc = tronque($content,240);
			}else{ $desc = $description; } ?>
		<meta name="description" content="<?php if(get_field('seo_description',$post->ID)){echo get_field('seo_description',$post->ID);}else{echo $desc;} ?>">
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<meta name="format-detection" content="telephone=no">
		<meta property="og:title" content="<?php if(is_home()){wp_title( '|', true, 'right' );bloginfo( 'name' );}else{if(get_field('seo_titre',$post->ID)){echo get_field('seo_titre',$post->ID);}else{echo get_the_title($post->ID);}} ?>" />
		<meta property="og:description" content="<?php if(get_field('seo_description',$post->ID)){echo get_field('seo_description',$post->ID);}else{echo $desc;} ?>">
		<meta property="og:type" content="website" />
		<meta property="og:url" content="<?php echo 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]; ?>" />
		<meta property="og:image" content="<?php if (has_post_thumbnail($post->ID)) {$post_thumbnail_id = get_post_thumbnail_id( $post->ID );$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );if( $image_attributes ) { echo $image_attributes[0];} } else{echo home_url().'/wp-content/themes/atm-theme/library/images/logo.png';} ?>" />
		<meta property="og:site_name" content="<?php bloginfo( 'name' ) ?>" />
		<link rel="image_src" href="<?php if (has_post_thumbnail($post->ID)) {$post_thumbnail_id = get_post_thumbnail_id( $post->ID );$image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );if( $image_attributes ) { echo $image_attributes[0];} } else{echo home_url().'/wp-content/themes/atm-theme/library/images/logo.png';} ?>" />
		<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-touch-icon.png">
        <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
		<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
		<script>document.cookie='resolution='+Math.max(screen.width,screen.height)+'; path=/';</script>
		<?php wp_head(); ?>
		<script>
		<?php
			$site_google_ua = get_option( 'site_google_ua' );
			if(!isset($site_google_ua) || empty($site_google_ua) || is_null($site_google_ua)){$site_google_ua = 'UA-XXXXXXXX-X';}
		?>
	        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	        ga('create', '<?php echo $site_google_ua; ?>', 'auto');
	        ga('send', 'pageview');
	    </script>
	    <script>
			/*
			* Fonction de suivi des clics sur des liens sortants dans Analytics
			* Cette fonction utilise une chaîne d'URL valide comme argument et se sert de cette chaîne d'URL
			* comme libellé d'événement. Configurer la méthode de transport sur 'beacon' permet d'envoyer le clic
			* au moyen de 'navigator.sendBeacon' dans les navigateurs compatibles.
			*/
			var trackOutboundLink = function(url, nomEvenement) {
				/*
				* The ga function defined by the tracking code creates a JavaScript array ga.q
				* (if it doesn’t exist yet) and then pushes its arguments onto that array.
				* Once analytics.js is loaded, it processes the queued events and replaces ga by a completely different function,
				* so that ga.q is no longer defined. During page load at least two items are added to the queue,
				* one to create the tracker and another one to record the page view.
				* This means that ga.q is defined only if the tracking code has been executed, but analytics.js has not been loaded yet.
				* This means that it is possible to determine if Google Analytics is blocked simply by checking if ga.q is defined:
				*/
			  	if (ga.q) {
				    // Cas où Google Analytics est bloqué par des programmes tiers
				  	// tels que Ad Block, Ghostery, etc...
				    document.location = url;
			  	} else {
			  		// Cas où Google Analytics n'est pas bloqué
				    ga("send", "event", nomEvenement, "click", url, {"hitCallback":
			      	function () {
			        	document.location = url;
		      		}
			    });
			  }
			}
		</script> 
	</head>
	<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
		<noscript><?php _e("Sorry, you need to activate Javascript to browse on this website...","atm-theme"); ?></noscript>
		<!--[if lte IE 7]>
        <div style="padding: 1em; background: #900; font-size: 1.1em; color: #fff;z-index:9999;position:fixed;width:100%;">
        <?php _e("<p><strong>Warning ! </strong>Your browser (Internet Explorer 6, 7 ou 8) presents serious lacks of security and performance.<br />Please download a newer version (<a href=\"http://windows.microsoft.com/en-us/internet-explorer/products/ie/home\" style=\"color: #fff;\">Internet Explorer</a>, <a href=\"http://www.mozilla-europe.org/fr/firefox/\" style=\"color: #fff;\">Install Firefox</a>, <a href=\"http://www.google.com/chrome?hl=fr\" style=\"color: #fff;\">Install Chrome</a>, <a href=\"http://www.apple.com/fr/safari/download/\" style=\"color: #fff;\">Install Safari</a>)</p>","atm-theme"); ?></div>
        <![endif]--> 
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
		<div class="contain">
			<!-- header -->
      		<header class="clearfix">
           		<h1><a href="<?php echo home_url(); ?>" title="<?php echo get_bloginfo('name'); ?>" id="logo"><?php echo get_bloginfo('name'); ?></a></h1>
           		<div class="alignright"><?php /* Formulaire de recherche */ get_search_form(); ?></div>
            	<?php /* Affichage du menu */ echo do_shortcode('[awdsimplemenu menu_name="main" is_responsive="true"]'); ?>
            	<?php /* Menu multilingue */ //echo '<ul class="translation_flags">';pll_the_languages(array('show_flags'=>1,'show_names'=>0));echo '</ul>'; ?>
      		</header>
      		<!-- end header -->
