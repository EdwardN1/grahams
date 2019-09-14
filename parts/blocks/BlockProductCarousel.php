<?php $heading = get_field( 'heading' ); ?>
<?php global $post; ?>
<div class="product-carousel">
    <div class="grid-container">
        <h2 class="text-center"><?php echo $heading; ?></h2>
		<?php if ( have_rows( 'slides' ) ) : ?>
            <div data-slick-slider
                 data-slick='{"slidesToShow":5, "slidesToScroll":5, "infinite":false, "initialSlide":1, "responsive": [{"breakpoint": 915,"settings": {"slidesToShow": 2.5, "slidesToScroll": 2}}, {"breakpoint": 550, "settings": {"slidesToShow": 1.1, "slidesToScroll": 1}}]}'>
				<?php while ( have_rows( 'slides' ) ) :
				the_row(); ?>
                <div>
					<?php $post_object = get_sub_field( 'products' ); ?>
					<?php if ( $post_object ): ?>
						<?php $post = $post_object; ?>
						<?php setup_postdata( $post ); ?>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        <br> PostID = <?php echo $post->ID ?>
                        <br> Code =<?php the_field( 'code' ); ?>
                        <br> Price = <?php the_field( 'price' ); ?>
						<?php wp_reset_postdata(); ?>
					<?php endif; ?>
					<?php endwhile; ?>
                </div>
            </div>
		<?php else : ?>
			<?php // no rows found ?>
		<?php endif; ?>
    </div>
</div>


