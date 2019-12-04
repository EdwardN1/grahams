<?php


/**
 * ========================== Ajax Actions ==============================
 */

add_action('wp_ajax_nopriv_get_wish_list', 'ajax_get_wish_list');
add_action('wp_ajax_get_wish_list', 'ajax_get_wish_list');

function ajax_get_wish_list()
{
    echo get_wish_list();
    exit;
}

function get_wish_list()
{
    $res = 'Nothing added yet ...';
    if (isset($_COOKIE['wishlist'])) {
        $wishlist = json_decode($_COOKIE['wishlist']);
        if (empty($wishlist)) {
            $res = 'Nothing added yet ...';
        } else {
            $res = '';
            foreach ($wishlist as $wish) {
                $res .= '<div class="wish-row grid-x">';
                $permalink = get_the_permalink($wish);
                if (have_rows('variation_images', $wish)):
                    while (have_rows('variation_images', $wish)): the_row();
                        $image = get_sub_field('image');
                        $res .= '<div class="cell shrink"><a href="'.$permalink.'"><img src="'.$image['url'].'" style="width: auto; max-height:75px; max-width: 50px;"></a></div>';
                        break;
                    endwhile;
                endif;
                $res .= '<div class="cell shrink" style="line-height: 75px; padding-left: 1em; padding-right: 1em;"><a href="'.$permalink.'">'.get_field('code',$wish).'</a></div>';
                $res .= '<div class="cell shrink" style="line-height: 75px; padding-left: 1em; padding-right: 1em;"><a href="'.$permalink.'">'.get_the_title($wish).'</a></div>';
                $res .= '</div>';
            }
        }
    }
    return $res;
}