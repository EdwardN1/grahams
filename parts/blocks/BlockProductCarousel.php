<?php $heading = get_field( 'heading' ); ?>
<?php global $post; ?>
<div class="product-carousel">
    <div class="grid-container">
        <h2 class="text-center"><?php echo $heading; ?> - for PostID <?php echo $post->ID; ?></h2>
        <div data-slick-slider
             data-slick='{"slidesToShow":5, "slidesToScroll":5, "infinite":false, "initialSlide":1, "responsive": [{"breakpoint": 915,"settings": {"slidesToShow": 2.5, "slidesToScroll": 2}}, {"breakpoint": 550, "settings": {"slidesToShow": 1.1, "slidesToScroll": 1}}]}'>
			<?php $post_objects = get_field( 'products' ); ?>
			<?php if ( $post_objects ): ?>
				<?php foreach ( $post_objects as $post ): ?>
                    <div>
					<?php setup_postdata( $post ); ?>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        <br> Code = <?php the_field('code',$post->ID);?>
                        <br> Price = <?php the_field('price',$post->ID);?>
                    </div>
				<?php endforeach; ?>

				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
        </div>
    </div>


