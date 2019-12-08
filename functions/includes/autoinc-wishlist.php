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
add_action('wp_ajax_send_SMTP_wishlist', 'ajax_send_SMTP_wishlist');

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
    $res = '';
    if (isset($_COOKIE['wishlist'])) {
        $wishlist = json_decode($_COOKIE['wishlist']);
        if (!empty($wishlist)) {
            if (isset($_COOKIE['eparams'])) {
                $eparam_list = json_decode(trim($_COOKIE['eparams'],'\\"'));
                if(!empty($eparam_list)) {
                    $to = $eparam_list[0];
                    $subject = 'Wishlist from Graham Direct Website';
                    if($eparam_list[1]) {
                        $subject = $eparam_list[1];
                    }
                    if(filter_var($to, FILTER_VALIDATE_EMAIL)) {
                        $mail = new PHPMailer(true);
                        try {
                            //Server settings
                            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
                            $mail->isSMTP();                                            // Send using SMTP
                            $mail->Host = get_field('smtp_server', 'option');                    // Set the SMTP server to send through
                            $mail->SMTPAuth = true;                                   // Enable SMTP authentication
                            $mail->Username = get_field('username', 'option');                     // SMTP username
                            $mail->Password = get_field('password', 'option');                               // SMTP password
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
                            $mail->Port = 587;                                    // TCP port to connect to

                            //Recipients
                            $mail->setFrom(get_field('from_address', 'option'), 'Graham Direct Website');
                            $mail->addAddress($to);     // Add a recipient
                            if (get_field('reply_to_address', 'option')) {
                                $mail->addReplyTo(get_field('reply_to_address', 'option'));
                            }

                            // Attachments

                            // Content
                            $mail->isHTML(true);                                  // Set email format to HTML
                            $mail->Subject = $subject;
                            $mail->Body = get_email_wish_list();
                            $mail->AltBody = get_plain_email_wish_list();

                            $mail->send();
                            $res = 'Wishlist has been sent';
                        } catch (Exception $e) {
                            $res = "Wishlist could not be sent. Mailer Error: {$mail->ErrorInfo}";
                        }
                    } else {
                        $res = 'Please enter a valid email address - ';
	                    error_log('Cannot eval email address - '. str_replace('\\\\','',$_COOKIE['eparams']) . ' - ' . $eparam_list[0]);
                    }
                } else {
                    $res = 'Please enter an email address and subject';
	                error_log('Cannot read eparams cookie - '. $_COOKIE['eparams'] . ' - ' . $eparam_list);
                }
            } else {
                $res = 'Please enter an email address and subject';
                error_log('Cannot get eparams cookie');
            }
        }
    } else {
        $res = 'You need to add something to your wish list before you can email it';
    }
    return $res;
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