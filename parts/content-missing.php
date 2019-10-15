<?php
/**
 * The template part for displaying a message that posts cannot be found
 */
?>

<div class="post-not-found">
	
	<?php if ( is_search() ) : ?>
		
		<header class="article-header">
			<h1><?php _e( 'Sorry, No Results.', 'jointswp' );?></h1>
		</header>
		
		<section class="entry-content">
			<p><?php _e( 'Try your search again.', 'jointswp' );?></p>
		</section>
		
		<section class="search">
		    <p><?php get_search_form(); ?></p>
		</section> <!-- end search section -->
		
		<footer class="article-footer">
			<p><?php _e( 'This is the error message in the parts/content-missing.php template.', 'jointswp' ); ?></p>
		</footer>
		
	<?php else: ?>
	
		<header class="article-header">
			<h1><?php _e( 'No products found', 'jointswp' ); ?></h1>
		</header>
		
		<!--<section class="entry-content">
			<p><?php /*_e( 'Try searching:', 'jointswp' ); */?></p>
		</section>
		
		<section class="search">
		    <p><?php /*get_search_form(); */?></p>
		</section> -->
		
		<footer class="article-footer">
		  <p><?php /*_e( 'This is the error message in the parts/content-missing.php template.', 'jointswp' ); */?></p>
		</footer>
			
	<?php endif; ?>
	
</div>
