<?php

function checkMailRequestSecure() {
	if ( ! check_ajax_referer( 'mailing-security-nonce', 'security' ) ) {
		wp_send_json_error( 'Invalid security token sent.' );
		wp_die();
	}
}

/**
 * ========================== Shortcode ==============================
 */

add_shortcode( 'wishlist', 'sc_wishlist' );

function sc_wishlist( $atts, $content = null ) {
	$res = '<div class="grid-container">' . get_wish_list() . '</div>';
	ob_start();
	?>
    <div class="grid-container">
        <div class="email-form">
            <strong>Email wishlist:</strong><br>
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <form name="wlForm" method="post" id="wlForm">
                <label>
                    email: <input name="pmEmail" id="pmEmail" type="text" maxlength="64" style="width:98%;"/>
                </label>
                <label>
                    subject:
                    <input name="pmSubject" id="pmSubject" type="text" maxlength="64" style="width:98%;"/>
                </label>
                <div class="g-recaptcha" data-sitekey="<?php the_field( 'site_key', 'option' ); ?>"></div>
                <br>
                <input name="pmSubmit" type="submit" value="Send"/>
            </form>
        </div>
    </div>
	<?php
	$res .= ob_get_clean();

	return $res;
}

/**
 * ========================== Ajax Actions ==============================
 */

add_action( 'wp_ajax_nopriv_get_wish_list', 'ajax_get_wish_list' );
add_action( 'wp_ajax_get_wish_list', 'ajax_get_wish_list' );

add_action( 'wp_ajax_nopriv_get_email_wish_list', 'ajax_get_email_wish_list' );
add_action( 'wp_ajax_get_email_wish_list', 'ajax_get_email_wish_list' );

add_action( 'wp_ajax_nopriv_get_plain_email_wish_list', 'ajax_get_plain_email_wish_list' );
add_action( 'wp_ajax_get_plain_email_wish_list', 'ajax_get_plain_email_wish_list' );

add_action( 'wp_ajax_nopriv_send_SMTP_wishlist', 'ajax_send_SMTP_wishlist' );
add_action( 'wp_ajax_send_SMTP_wishlist', 'ajax_send_SMTP_wishlist' );

function ajax_send_SMTP_wishlist() {
	checkMailRequestSecure();
	echo send_SMTP_wishlist();
	exit;
}

function ajax_get_wish_list() {
	echo get_wish_list();
	exit;
}

function ajax_get_email_wish_list() {
	echo get_email_wish_list();
	exit;
}

function ajax_get_plain_email_wish_list() {
	echo get_plain_email_wish_list();
	exit;
}

function send_SMTP_wishlist() {
	require_once( ABSPATH . '/wp-includes/class-smtp.php' );
	require_once( ABSPATH . '/wp-includes/class-phpmailer.php' );
	$res = '';
	if ( isset( $_COOKIE['wishlist'] ) ) {
		$wishlist = json_decode( $_COOKIE['wishlist'] );
		if ( ! empty( $wishlist ) ) {
			if ( isset( $_COOKIE['eparams_a'] ) ) {
				$to = $_COOKIE['eparams_a'];
				if ( $to != '' ) {
					$subject = 'Wishlist from Graham Direct Website';
					if ( isset( $_COOKIE['eparams_b'] ) ) {
						$subject = $_COOKIE['eparams_b'];
					}
					if ( filter_var( $to, FILTER_VALIDATE_EMAIL ) ) {
						if ( isset( $_COOKIE['eparams_c'] ) ) {
							$reCapResponse = $_COOKIE['eparams_c'];
							if ( $reCapResponse != '' ) {
								$secret = get_field( 'secret_key', 'option' );
								$url    = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $reCapResponse;
								$ch     = curl_init();
								curl_setopt( $ch, CURLOPT_URL, $url );
								curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
								$apiCall      = curl_exec( $ch );
								$responseData = json_decode( $apiCall );
								curl_close( $ch );
								$reCapVerify = $responseData->success;
								if ( $reCapVerify ) {
									$mail = new PHPMailer( true );
									try {
										//Server settings
										$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
										$mail->isSMTP();                                            // Send using SMTP
										$mail->Host       = get_field( 'smtp_server', 'option' );
										if(get_field('require_login','option')) {
											$mail->SMTPAuth   = true;
											$mail->Username   = get_field( 'username', 'option' );                     // SMTP username
											$mail->Password   = get_field( 'password', 'option' );                               // SMTP password
											$mail->SMTPSecure = "tls";         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
											$mail->Port       = 587;                                    // TCP port to connect to
										} else {
											$mail->SMTPAuth   = false;
											$mail->Port       = 25;                                    // TCP port to connect to
										}
										//Recipients
										$mail->setFrom( get_field( 'from_address', 'option' ), 'Graham Direct Website' );
										$mail->addAddress( $to );     // Add a recipient
										if ( get_field( 'reply_to_address', 'option' ) ) {
											$mail->addReplyTo( get_field( 'reply_to_address', 'option' ) );
										}

										// Attachments

										// Content
										$mail->isHTML( true );                                  // Set email format to HTML
										$mail->Subject = $subject;
										$mail->Body    = get_email_wish_list();
										$mail->AltBody = get_plain_email_wish_list();
										ob_start();
										$mail->send();
										ob_get_clean();
										$res = 'Wishlist has been sent';
									} catch ( Exception $e ) {
										$res = "Wishlist could not be sent. Mailer Error: {$mail->ErrorInfo}";
										error_log($mail->ErrorInfo);
									}
								} else {
									$res = 'Google reCapture Error. Message not sent';
								}
							} else {
								$res = 'You can not leave the reCaptcha Code empty';
							}
						} else {
							$res = 'You can not leave the reCaptcha Code empty';
						}
					} else {
						$res = 'Please enter a valid email address - ';
						error_log( 'Cannot eval email address - ' . $to );
					}
				} else {
					$res = 'Please enter an email address and subject';
				}
			} else {
				$res = 'Please enter an email address and subject';
				error_log( 'Cannot get eparams cookie' );
			}
		}
	} else {
		$res = 'You need to add something to your wish list before you can email it';
	}

	return $res;
}

function get_wish_list() {
	$res = 'Nothing added yet ...';
	if ( isset( $_COOKIE['wishlist'] ) ) {
		$wishlist = json_decode( $_COOKIE['wishlist'] );
		if ( empty( $wishlist ) ) {
			$res = 'Nothing added yet ...';
		} else {
			$res = '';
			foreach ( $wishlist as $wish ) {
				$res       .= '<div class="wish-row grid-x">';
				$permalink = get_the_permalink( $wish );
				if ( have_rows( 'variation_images', $wish ) ):
					while ( have_rows( 'variation_images', $wish ) ): the_row();
						$image = get_sub_field( 'image' );
						$res   .= '<div class="cell shrink"><a href="' . $permalink . '" style="line-height:75px;"><img src="' . $image['url'] . '" style="width: auto; max-height:75px; max-width: 50px;"></a></div>';
						break;
					endwhile;
				endif;
				$res .= '<div class="cell shrink" style="line-height: 75px; padding-left: 1em; padding-right: 1em;"><a href="' . $permalink . '">' . get_field( 'code', $wish ) . '</a></div>';
				$res .= '<div class="cell shrink" style="line-height: 75px; padding-left: 1em; padding-right: 1em;"><a href="' . $permalink . '">' . get_the_title( $wish ) . '</a></div>';
				$res .= '<div class="cell shrink" style="line-height: 75px; padding-left: 1em; padding-right: 1em;"><a href="#" class="wlRemove" data-post="'.$wish.'" style="color: red;">x</a>'.'</div>';
				$res .= '</div>';
			}
		}
	}

	return $res;
}

function get_email_wish_list() {
	$res = 'Nothing added yet ...';
	if ( isset( $_COOKIE['wishlist'] ) ) {
		$wishlist = json_decode( $_COOKIE['wishlist'] );
		if ( empty( $wishlist ) ) {
			$res = 'Nothing added yet ...';
		} else {
			$res = '';
			foreach ( $wishlist as $wish ) {
				$res       .= '<table><tr>';
				$permalink = get_the_permalink( $wish );
				if ( have_rows( 'variation_images', $wish ) ):
					while ( have_rows( 'variation_images', $wish ) ): the_row();
						$image = get_sub_field( 'image' );
						$res   .= '<td><a href="' . $permalink . '"><img src="' . $image['url'] . '" style="width: auto; max-height:75px; max-width: 50px;"></a></td>';
						break;
					endwhile;
				endif;
				$res .= '<td style="line-height: 75px; padding-left: 1em; padding-right: 1em;"><a href="' . $permalink . '">' . get_field( 'code', $wish ) . '</a></td>';
				$res .= '<td style="line-height: 75px; padding-left: 1em; padding-right: 1em;"><a href="' . $permalink . '">' . get_the_title( $wish ) . '</a></td>';
				$res .= '</tr></table>';
			}
		}
	}

	return $res;
}

function get_plain_email_wish_list() {
	$res = 'Nothing added yet ...';
	if ( isset( $_COOKIE['wishlist'] ) ) {
		$wishlist = json_decode( $_COOKIE['wishlist'] );
		if ( empty( $wishlist ) ) {
			$res = 'Nothing added yet ...';
		} else {
			$res = '';
			foreach ( $wishlist as $wish ) {
				$permalink = get_the_permalink( $wish );
				$res       .= get_field( 'code', $wish ) . ' ';
				$res       .= str_replace( '&#8211;', '-', get_the_title( $wish ) ) . ' ';
				$res       .= $permalink . '%0D%0A';
			}
		}
	}
	error_log( $res );

	return $res;
}