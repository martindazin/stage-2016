<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single Posts Template
 *
 *
 * @file           single.php
 * @package        Responsive
 * @author         Emil Uzelac
 * @copyright      2003 - 2014 CyberChimps
 * @license        license.txt
 * @version        Release: 1.0
 * @filesource     wp-content/themes/responsive/single.php
 * @link           http://codex.wordpress.org/Theme_Development#Single_Post_.28single.php.29
 * @since          available since Release 1.0
 */

get_header(); ?>

<div id="content" class="<?php echo esc_attr( implode( ' ', responsive_get_content_classes() ) ); ?>">

	<?php get_template_part( 'loop-header', get_post_type() ); ?>

	<?php if ( have_posts() ) : ?>

		<?php while( have_posts() ) : the_post(); ?>

			<?php responsive_entry_before(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1><?php the_field('aurelionSol'); ?></h1>
				<?php responsive_entry_top(); ?>

				<?php get_template_part( 'post-meta', get_post_type() ); ?>

				<div class="post-entry">
					<?php the_content( __( 'Read more &#8250;', 'responsive' ) ); ?>

					<?php if ( get_the_author_meta( 'description' ) != '' ) : ?>

						<div id="author-meta">
							<?php if ( function_exists( 'get_avatar' ) ) {
								echo get_avatar( get_the_author_meta( 'email' ), '80' );
							} ?>
							<div class="about-author"><?php _e( 'About', 'responsive' ); ?> <?php the_author_posts_link(); ?></div>
							<p><?php the_author_meta( 'description' ) ?></p>
						</div><!-- end of #author-meta -->

					<?php endif; // no description, no author's meta ?>

					<!-- On affiche le nom de l'article -->
					<p><?php the_field('titre'); ?></p>

					<!-- On affiche la description de l'article -->
					<p><?php the_field('description'); ?></p>								
					
					<!-- On affiche les données contenues (s'il y en a) dans le répéteur -->
					<?php if( have_rows('repeteur') ): ?>

						<!-- <ul class="slides"> -->
							<!-- On boucle sur toutes les images contenues dans le répéteur -->
							<?php while( have_rows('repeteur') ): the_row(); 
								// On affiche les données contenues dans le répéteur
								$imageRepeteur = get_sub_field('imagediaporama');
								$imageUrlRepeteur = $imageRepeteur['sizes']['atm-100-100'];
								// Vérification de l'existence d'une image
								if( isset($imageRepeteur['url']) && !empty($imageRepeteur['url']) && !is_null($imageRepeteur['url']) ):
									// Gestion de la taille de la photo et des génération des miniatures
									if( !isset($imageUrlRepeteur) || empty($imageUrlRepeteur) || is_null($imageUrlRepeteur) ){
										$imageUrlRepeteur = $imageRepeteur['url'];
									}
							?>

								<!-- On affiche l'image -->
								<!-- Attribut src = Lien où se situe l'image -->
								<!-- Attribut alt = Nom de l'image -->
								<img src="<?php echo $imageUrlRepeteur; ?>" alt="<?php echo $imageRepeteur['alt'] ?>"/>

								<?php endif;

							endwhile; ?>

						<!-- </ul> -->

					<?php endif; ?>

					<p>
					<?php
						// On regarde s'il y a des données dans le contenu flexible
						if( have_rows('flexibleContenu') ) :

						     // On boucle sur tous les blocs de contenu contenus dans le contenu flexible
						    while ( have_rows('flexibleContenu') ) : the_row();

								// Si c'est un titre
								if ( get_row_layout() == 'flexTitre' ) :

									the_sub_field('flexibleTitre');
								 	echo '<br>';

								// Si c'est une description
								elseif ( get_row_layout() == 'flexDescription' ) :

									the_sub_field('flexibleDescription');
								 	echo '<br>';

								// Si c'est un répéteur d'images
								elseif ( get_row_layout() == 'flexRepeteur' ) :

								echo '<p>';
									
									// On affiche les données contenues (s'il y en a) dans le répéteur
								 	if( have_rows('flexibleRepeteur') ):
										// <ul class="slides">
										// On boucle sur toutes les images contenues dans le répéteur
										while( have_rows('flexibleRepeteur') ): the_row(); 
											// On affiche les données contenues dans le répéteur
											$imageFlexibleRepeteur = get_sub_field('flexibleImage');
											$imageUrlFlexibleRepeteur = $imageFlexibleRepeteur['sizes']['atm-100-100'];
											// Vérification de l'existence d'une image
											if( isset($imageFlexibleRepeteur['url']) && !empty($imageFlexibleRepeteur['url']) && !is_null($imageFlexibleRepeteur['url']) ):
												// Gestion de la taille de la photo et des génération des miniatures
												if( !isset($imageUrlFlexibleRepeteur) || empty($imageUrlFlexibleRepeteur) || is_null($imageUrlFlexibleRepeteur) ){
													$imageUrlFlexibleRepeteur = $imageFlexibleRepeteur['url'];
												}
										?>

											<!-- On affiche l'image du répéteur dans le contenu flexible-->
											<img src="<?php echo $imageUrlFlexibleRepeteur; ?>" alt="<?php echo $imageFlexibleRepeteur['alt'] ?>" height="100" width="100" />
										
										<?php endif;

										endwhile;

										// </ul>

									endif;

								endif;
								
								echo '</p>';

						    endwhile;

						else :
					    	// no layouts found
						endif;
					?>
					
					</p>

					<?php
						echo dirname($_SERVER['SERVER_PROTOCOL']) . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ;
					?>


				<?php wp_link_pages( array( 'before' => '<div class="pagination">' . __( 'Pages:', 'responsive' ), 'after' => '</div>' ) ); ?>
				</div><!-- end of .post-entry -->

				<div class="navigation">
					<div class="previous"><?php previous_post_link( '&#8249; %link' ); ?></div>
					<div class="next"><?php next_post_link( '%link &#8250;' ); ?></div>
				</div><!-- end of .navigation -->

				<?php get_template_part( 'post-data', get_post_type() ); ?>

				<?php responsive_entry_bottom(); ?>
			</div><!-- end of #post-<?php the_ID(); ?> -->
			<?php responsive_entry_after(); ?>

			<?php responsive_comments_before(); ?>
			<?php comments_template( '', true ); ?>
			<?php responsive_comments_after(); ?>

		<?php
		endwhile;

		get_template_part( 'loop-nav', get_post_type() );

	else :

		get_template_part( 'loop-no-posts', get_post_type() );

	endif;
	?>

</div><!-- end of #content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
