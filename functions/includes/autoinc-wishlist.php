<?php

function checkMailRequestSecure() {
    if ( ! check_ajax_referer( 'mailing-security-nonce', 'security' ) ) {
        wp_send_json_error( 'Invalid security token sent.' );
        wp_die();
    }
}

/**
 * ========================== Ajax Actions ==============================
 */

add_action('wp_ajax_nopriv_get_wish_list', 'ajax_get_wish_list');
add_action('wp_ajax_get_wish_list', 'ajax_get_wish_list');

add_action('wp_ajax_nopriv_get_email_wish_list', 'ajax_get_email_wish_list');
add_action('wp_ajax_get_email_wish_list', 'ajax_get_email_wish_list');

add_action('wp_ajax_nopriv_get_plain_email_wish_list', 'ajax_get_plain_email_wish_list');
add_action('wp_ajax_get_plain_email_wish_list', 'ajax_get_plain_email_wish_list');

add_action('wp_ajax_nopriv_send_SMTP_wishlist', 'ajax_send_SMTP_wishlist');
add_action('wp_ajax_gsend_SMTP_wishlist', 'ajax_send_SMTP_wishlist');

function ajax_send_SMTP_wishlist() {
    checkMailRequestSecure();
    echo send_SMTP_wishlist();
    exit;
}

function ajax_get_wish_list()
{
    echo get_wish_list();
    exit;
}

function ajax_get_email_wish_list()
{
    echo get_email_wish_list();
    exit;
}

function ajax_get_plain_email_wish_list()
{
    echo get_plain_email_wish_list();
    exit;
}

function send_SMTP_wishlist() {
    $mail = new PHPMailer(true);
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

function get_email_wish_list()
{
    $res = 'Nothing added yet ...';
    if (isset($_COOKIE['wishlist'])) {
        $wishlist = json_decode($_COOKIE['wishlist']);
        if (empty($wishlist)) {
            $res = 'Nothing added yet ...';
        } else {
            $res = '';
            foreach ($wishlist as $wish) {
                $res .= '<table><tr>';
                $permalink = get_the_permalink($wish);
                if (have_rows('variation_images', $wish)):
                    while (have_rows('variation_images', $wish)): the_row();
                        $image = get_sub_field('image');
                        $res .= '<td><a href="'.$permalink.'"><img src="'.$image['url'].'" style="width: auto; max-height:75px; max-width: 50px;"></a></td>';
                        break;
                    endwhile;
                endif;
                $res .= '<td style="line-height: 75px; padding-left: 1em; padding-right: 1em;"><a href="'.$permalink.'">'.get_field('code',$wish).'</a></td>';
                $res .= '<td style="line-height: 75px; padding-left: 1em; padding-right: 1em;"><a href="'.$permalink.'">'.get_the_title($wish).'</a></td>';
                $res .= '</tr></table>';
            }
        }
    }
    return $res;
}

function get_plain_email_wish_list()
{
    $res = 'Nothing added yet ...';
    if (isset($_COOKIE['wishlist'])) {
        $wishlist = json_decode($_COOKIE['wishlist']);
        if (empty($wishlist)) {
            $res = 'Nothing added yet ...';
        } else {
            $res = '';
            foreach ($wishlist as $wish) {
                $permalink = get_the_permalink($wish);
                $res .= get_field('code',$wish).' ';
                $res .= str_replace('&#8211;','-',get_the_title($wish)).' ';
                $res .= $permalink.'%0D%0A';
            }
        }
    }
    error_log($res);
    return $res;
}