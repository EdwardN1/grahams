<?php $id = get_field( 'id' ); ?>
<div id="<?php echo $id; ?>" class="hero-section">
	<?php $section_type = get_field( 'section_type' ); ?>
	<?php if ( $section_type == 'image' ): ?>
		<?php $image = get_field( 'image' ); ?>
		<?php $imageURL = $image['url']; ?>
		<?php $imageALT = $image['alt']; ?>
		<?php if ( $image ) { ?>
            <img src="<?php echo $imageURL; ?>" alt="<?php echo $imageALT; ?>"/>
		<?php } ?>
		<?php $image_title = get_field( 'image_title' ); ?>
		<?php echo $image_title; ?>
		<?php $image_link_title = get_field( 'image_link_title' ); ?>
		<?php echo $image_link_title; ?>
		<?php $image_link_type = get_field( 'image_link_type' ); ?>
		<?php $image_external_url = get_field( 'image_external_url' ); ?>
		<?php echo $image_external_url; ?>
		<?php $image_text_link = get_field( 'image_text_link' ); ?>
		<?php echo $image_text_link; ?>
		<?php the_field( 'image_internal_page' ); ?>
	<?php else: ?>
		<?php if ( have_rows( 'slides' ) ) : ?>
            <div data-slick-slider class="hero-slides">
				<?php while ( have_rows( 'slides' ) ) : the_row(); ?>
					<?php $slide_image = get_sub_field( 'slide_image' ); ?>
					<?php $slide_imageURL = $slide_image['url']; ?>
					<?php $slide_imageALT = $slide_image['alt']; ?>
					<?php if ( $slide_image ) { ?>
                        <div class="hero-slide" style="background-image: url(<?php echo $slide_imageURL; ?>)">
                            <div class="hero-slide-description">
								<?php $slide_title = get_sub_field( 'slide_title' ); ?>
								<?php $slide_title_type = get_sub_field( 'slide_title_type' ); ?>

								<?php if ( $slide_title_type == 'H1 Heading' ): ?>
                                    <h1><?php echo $slide_title; ?></h1>
								<?php else: ?>
									<?php if ( $slide_title_type == 'H2 Heading' ): ?>
                                        <h2><?php echo $slide_title; ?></h2>
									<?php else: ?>
                                        <span><?php echo $slide_title; ?></span>
									<?php endif; ?>
								<?php endif; ?>
								<?php $slide_link_title = get_sub_field( 'slide_link_title' ); ?>
								<?php $slide_link_type = get_sub_field( 'slide_link_type' ); ?>
								<?php if ( $slide_link_type == 'External URL' ): ?>
									<?php $slide_external_url = get_sub_field( 'slide_external_url' ); ?>
                                    <a href="<?php echo $slide_external_url; ?>"
                                       target="_blank" class="button"><?php echo $slide_link_title; ?></a>
								<?php endif; ?>
								<?php if ( $slide_link_type == 'Text' ): ?>
									<?php $slide_text_link = get_sub_field( 'slide_text_link' ); ?>
                                    <a href="<?php echo $slide_text_link; ?>"
                                       class="button"><?php echo $slide_link_title; ?></a>
								<?php endif; ?>
								<?php if ( $slide_link_type == 'Internal Page' ): ?>
									<?php $slide_internal_page = get_sub_field( 'slide_internal_page' ); ?>
                                    <a href="<?php echo $slide_internal_page; ?>"
                                       class="button"><?php echo $slide_link_title; ?></a>
								<?php endif; ?>
                            </div>
                        </div>
					<?php } ?>


				<?php endwhile; ?>
            </div>
		<?php else : ?>
			<?php // no rows found ?>
		<?php endif; ?>
	<?php endif; ?>
</div>
