<?php
/**
 * The template for displaying search form
 */
?>

    <form role="search" method="get" action="<?php echo home_url('/'); ?>">
        <div class="search-box">
            <input type="search" class="search-field" placeholder="<?php echo esc_attr_x('What are you looking for...', 'jointswp') ?>" value="<?php echo get_search_query() ?>" name="s"
                   title="<?php echo esc_attr_x('Search for:', 'jointswp') ?>"/>
            <input type="submit" class="search-submit" value="<?php echo esc_attr_x(' ', 'jointswp') ?>"/>
        </div>
    </form>
