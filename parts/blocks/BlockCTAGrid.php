<?php $column_count = get_field('column_count'); ?>
<?php $cellClass = ""; ?>
<?php if ($column_count == 2): ?>
    <?php $cellClass = " cell large-6 medium-6 small-12"; ?>
<?php endif; ?>
<?php if ($column_count == 3): ?>
    <?php $cellClass = " cell large-4 medium-4 small-12"; ?>
<?php endif; ?>
<?php if ($column_count == 4): ?>
    <?php $cellClass = " cell large-3 medium-3 small-12"; ?>
<?php endif; ?>
<?php if ($column_count == 5): ?>
    <?php $cellClass = " large-up-5 medium-up-3 small-up-1"; ?>
<?php endif; ?>
<?php if ($column_count == 6): ?>
    <?php $cellClass = " cell large-2 medium-4 small-12"; ?>
<?php endif; ?>
<?php $cellPadClass = ' right-cell-pad'; ?>

<?php if (have_rows('columns')) : ?>
    <div class="grid-container cta-grid">
        <div class="grid-x">
            <?php $currentCol = 1; ?>
            <?php while (have_rows('columns')) : the_row(); ?>
                <?php $image = get_sub_field('image'); ?>
                <?php $imageURL = $image['url']; ?>
                <?php $link_text = get_sub_field('link_text'); ?>
                <?php $link_type = get_sub_field('link_type'); ?>
                <?php $blank = ''; ?>
                <?php $href = ''; ?>

                <?php $external_url = get_sub_field('external_url'); ?>
                <?php echo $external_url; ?>
                <?php $text_link = get_sub_field('text_link'); ?>

                <?php $internal_page = get_sub_field('internal_page'); ?>

                <?php if ($link_type == 'External URL'): ?>
                    <?php $blank = 'target="_blank"'; ?>
                    <?php $href = $external_url; ?>
                <?php endif; ?>
                <?php if ($link_type == 'Text'): ?>
                    <?php $href = $text_link; ?>
                <?php endif; ?>
                <?php if ($link_type == 'Internal Page'): ?>
                    <?php $href = $internal_page; ?>
                <?php endif; ?>

                <div class="cta-grid-cell<?php echo $cellClass; ?>">
                    <div class="cta-grid-cell-contents<?php echo $cellPadClass; ?>">
                        <a class="cta-link-anchor" href="<?php echo $href; ?>"<?php echo $blank ?>>
                            <div class="image" style="background-image:url(<?php echo $imageURL; ?>);">
                                <?php $heading_type = get_sub_field('heading_type'); ?>
                                <?php $heading = get_sub_field('heading'); ?>
                                <?php if($heading_type=='Text'):?>
                                <div class="cta-heading"><?php echo $heading; ?></div>
                                <?php endif;?>
                                <?php if($heading_type=='H2'):?>
                                <h2 class="cta-heading"><?php echo $heading; ?></h2>
                                <?php endif;?>
                                <?php if($heading_type=='H3'):?>
                                <h3 class="cta-heading"><?php echo $heading; ?></h3>
                                <?php endif;?>
                                <div class="cta-strapline">
                                    <?php $strapline = get_sub_field('strapline'); ?>
                                    <?php echo $strapline; ?>
                                </div>
                                <div class="cta-subheading">
                                    <?php $sub_heading = get_sub_field('sub_heading'); ?>
                                    <?php echo $sub_heading; ?>
                                </div>
                                <div class="cta-description">
                                    <?php $description = get_sub_field('description'); ?>
                                    <?php echo $description; ?>
                                </div>
                                <div class="cta-button">
                                    <?php $link_text = get_sub_field('link_text'); ?>
                                    <?php echo $link_text; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <?php $currentCol++; ?>
                <?php
                if ($currentCol == $column_count) {
                    $cellPadClass = ' left-cell-pad';
                }
                else {
                    if ($currentCol == ($column_count+1)) {
                        $cellPadClass = ' right-cell-pad';
                    }
                    else {
                        $cellPadClass = ' cell-pad';
                    }
                }
                ?>
            <?php endwhile; ?>
        </div>
    </div>
<?php else : ?>
    <?php // no rows found ?>
<?php endif; ?>
