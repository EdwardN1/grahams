<?php $heading = get_field( 'heading' ); ?>
<?php global $post; ?>
<div class="product-carousel">
    <div class="grid-container">
        <h2 class="text-center"><?php echo $heading; ?></h2>
        <div class="slider-container">
			<?php $post_objects = get_field( 'products' ); ?>
			<?php if ( $post_objects ): ?>
			<?php foreach ( $post_objects as $post ): ?>
                <div class="carousel-slide">
					<?php setup_postdata( $post ); ?>
					<?php if ( have_rows( 'product_images', $post->ID ) ) : ?>
						<?php $imageFirstURL = ''; ?>
						<?php while ( have_rows( 'product_images', $post->ID ) ) : the_row(); ?>
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
							<?php the_field( 'code', $post->ID ); ?>
                        </div>
                        <div class="only">
                            Only
                        </div>
                        <div class="slide-price">
							£<?php the_field( 'price', $post->ID ); ?>
                        </div>
                    </a>
                </div>
				<?php wp_reset_postdata(); ?>
			<?php endforeach; ?>
        </div>

		<?php endif; ?>
    </div>
</div>


