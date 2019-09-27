<?php
/**
 * The template for displaying all single posts and attachments
 */

get_header(); ?>


<?php $price = get_field( 'price' ); ?>
<?php $description = get_field( 'description' ); ?>
<?php $code = get_field( 'code' ); ?>
<?php $summary = get_field( 'summary' ); ?>
<?php $specifications = get_field( 'specifications' ); ?>
<?php $availability = get_field( 'availability' ); ?>

<div class="grid-container">
    <?php if ( function_exists('yoast_breadcrumb') )
    {yoast_breadcrumb('<p id="breadcrumbs">','</p>');} ?>
</div>
    <div class="grid-container graham-product">
        <div class="grid-x">
            <div class="cell large-5 images">
				<?php if ( have_rows( 'product_images' ) ) : ?>
                    <div id="product-large">
						<?php while ( have_rows( 'product_images' ) ) : the_row(); ?>
							<?php $image = get_sub_field( 'image' ); ?>
							<?php $imageURL = $image['url']; ?>
							<?php $imageALT = $image['alt']; ?>
							<?php if ( $image ) { ?>
                                <div class="slide-image" style="max-width: 580px; width:42.278vw;">
                                    <img src="<?php echo $imageURL; ?>" alt="<?php echo $imageALT; ?>" style="width: 100%;" />
                                </div>
							<?php } ?>
						<?php endwhile; ?>
                    </div>
                    <div id="product-small" style="max-width: 609px; width:42.278vw;">
	                    <?php while ( have_rows( 'product_images' ) ) : the_row(); ?>
		                    <?php $image = get_sub_field( 'image' ); ?>
		                    <?php $imageURL = $image['url']; ?>
		                    <?php $imageALT = $image['alt']; ?>
		                    <?php if ( $image ) { ?>
                                <div class="slide-image" style="max-width: 123px;width:8.542vw;">
                                    <img src="<?php echo $imageURL; ?>" alt="<?php echo $imageALT; ?>" style="width:8.542vw;max-width: 123px; height: auto;"/>
                                </div>
		                    <?php } ?>
	                    <?php endwhile; ?>
                    </div>
				<?php else : ?>
					<?php // no rows found ?>
				<?php endif; ?>
            </div>
            <div class="cell auto content" style="padding-left: 2rem;">
                <h1><?php the_title(); ?></h1>
                <div class="code">
					<?php echo $code; ?>
                </div>
                <div class="description">
					<?php echo $description; ?>
                </div>
                <div class="price">
                    <span class="only">Only</span>
					£<?php echo $price; ?>
                </div>
                <div class="buttons"><!--<a href="#" class="button lime">Enquire Now</a>--> <a href="https://www.grahamplumbersmerchant.co.uk/branch-locator/" target="_blank" class="button green">Nearest
                        Stockist</a></div>
                <ul class="accordion" data-accordion>

                    <!--<li class="accordion-item" data-accordion-item>
                        <a href="#" class="accordion-title">Summary</a>
                        <div class="accordion-content" data-tab-content>
		                    <?php /*echo $summary; */?>
                        </div>
                    </li>-->

                    <li class="accordion-item is-active" data-accordion-item>
                        <a href="#" class="accordion-title">Specifications</a>
                        <div class="accordion-content" data-tab-content>
			                <?php echo $specifications; ?>
                        </div>
                    </li>

                    <!--<li class="accordion-item" data-accordion-item>
                        <a href="#" class="accordion-title">Availability</a>
                        <div class="accordion-content" data-tab-content>
			                <?php /*echo $availability; */?>
                        </div>
                    </li>-->

                </ul>
            </div>
        </div>
    </div>

<?php get_footer(); ?>