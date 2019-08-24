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
    <div class="grid-container image-link-grid">
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

                <div class="image-link-grid-cell<?php echo $cellClass; ?>">
                    <div class="image-link-grid-cell-contents<?php echo $cellPadClass; ?>">
                        <a class="image-grid-link-anchor" href="<?php echo $href; ?>"<?php echo $blank ?>>
                            <div class="image"
                                 style="background-image:url(<?php echo $imageURL; ?>);">
                            </div>
                            <div class="description">
                                <?php echo $link_text; ?>
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