<?php
/**
 * The template part for displaying a grid of posts
 *
 * For more info: http://jointswp.com/docs/grid-archive/
 */

// Adjust the amount of rows in the grid
$grid_columns = 6; ?>

<?php if( 0 === ( $wp_query->current_post  )  % $grid_columns ): ?>

    <div class="grid-x grid-margin-x grid-padding-x archive-grid" data-equalizer> <!--Begin Grid--> 

<?php endif; ?> 

		<!--Item: -->
		<div class="small-6 medium-4 large-2 cell panel product-carousel no-border" data-equalizer-watch>
		
			<article id="post-<?php the_ID(); ?>" <?php post_class('carousel-slide'); ?> role="article">
			
				<!--<section class="featured-image" itemprop="text">
					<?php /*the_post_thumbnail('full'); */?>
				</section>
			
				<header class="article-header">
					<h3 class="title"><a href="<?php /*the_permalink() */?>" rel="bookmark" title="<?php /*the_title_attribute(); */?>"><?php /*the_title(); */?></a></h3>
					<?php /*get_template_part( 'parts/content', 'byline' ); */?>
				</header>
								
				<section class="entry-content" itemprop="text">
					<?php /*the_content('<button class="tiny">' . __( 'Read more...', 'jointswp' ) . '</button>'); */?>
				</section> -->

                <?php if ( have_rows( 'product_images' ) ) : ?>
                    <?php $imageFirstURL = ''; ?>
                    <?php while ( have_rows( 'product_images' ) ) : the_row(); ?>
                        <?php $image = get_sub_field( 'image' ); ?>
                        <?php $imageURL = $image['url']; ?>
                        <?php $imageALT = $image['alt']; ?>
                        <?php if ( $image ) { ?>
                            <?php if ( $imageFirstURL == '' ):$imageFirstURL = $imageURL;endif; ?>
                        <?php } ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <?php // no rows found ?>
                <?php endif; ?>
                <a href="<?php the_permalink(); ?>" class="carousel-link-container">
                    <div class="slide-image">
                        <img src="<?php echo $imageFirstURL; ?>">
                    </div>
                    <div class="slide-title">
                        <?php the_title(); ?>
                    </div>
                    <div class="slide-code">
                        <?php the_field( 'code' ); ?>
                    </div>
                    <div class="only">
                        Only
                    </div>
                    <div class="slide-price">
                        £<?php the_field( 'price' ); ?>
                    </div>
                </a>
								    							
			</article> <!-- end article -->
			
		</div>

<?php if( 0 === ( $wp_query->current_post + 1 )  % $grid_columns ||  ( $wp_query->current_post + 1 ) ===  $wp_query->post_count ): ?>

   </div>  <!--End Grid --> 

<?php endif; ?>

