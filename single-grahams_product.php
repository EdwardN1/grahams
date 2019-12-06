<?php
/**
 * The template for displaying all single posts and attachments
 */

get_header(); ?>


<?php $price = number_format( get_field( 'price' ), 2 ); ?>
<?php $description = get_field( 'description' ); ?>
<?php $code = get_field( 'code' ); ?>
<?php $summary = get_field( 'summary' ); ?>
<?php $specifications = get_field( 'specifications' ); ?>
<?php $availability = get_field( 'availability' ); ?>

    <div class="grid-container">
		<?php /*if ( function_exists('yoast_breadcrumb') )
    {yoast_breadcrumb('<p id="breadcrumbs">','</p>');} */ ?>
		<?php
		//$thisTerm = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
		$theTerms = get_the_terms( get_the_ID(), 'grahamscat' );
		$thisTerm = $theTerms[0];

		if ( $thisTerm->taxonomy == 'grahamscat' ) {
			?>
            <div class="grid-container text-center breadcrumbs">
				<?php
				$divider = '';
				if ( $thisTerm->parent > 0 ) {
					$parentTerm = get_term_by( "id", $thisTerm->parent, "grahamscat" );
					echo '<a href="/product-category/' . $parentTerm->slug . '" class="green">' . '«  Back to  ' . $parentTerm->name . '</a>';
					$divider = ' | ';
				}
				foreach ( get_terms( 'grahamscat', array( 'hide_empty' => false, 'parent' => $thisTerm->parent ) ) as $child_term ) {
					echo $divider . '<a href="/product-category/' . $child_term->slug . '" class="green">' . $child_term->name . '</a>';
					$divider = ' | ';
				}

				?>
            </div>
			<?php
		}
		?>
    </div>
    <div class="grid-container graham-product">
        <div class="grid-x">
            <div class="cell small-12 large-5 images">
				<?php if ( have_rows( 'variation_images' ) ) : ?>
                    <div id="product-large">
						<?php while ( have_rows( 'variation_images' ) ) : the_row(); ?>
							<?php $image = get_sub_field( 'image' ); ?>
							<?php $imageURL = $image['url']; ?>
							<?php //$imageURL = $image['sizes']['medium']; ?>
							<?php $imageALT = $image['alt']; ?>
							<?php if ( $image ) { ?>
                                <div class="slide-image" style="max-width: 580px; width:42.278vw;">
                                    <img src="<?php echo $imageURL; ?>" alt="<?php echo $imageALT; ?>" style="width: 100%;"/>
                                </div>
							<?php } ?>
						<?php endwhile; ?>
                    </div>
                    <div id="product-small">
						<?php while ( have_rows( 'variation_images' ) ) : the_row(); ?>
							<?php $image = get_sub_field( 'image' ); ?>
							<?php $imageURL = $image['url']; ?>
							<?php $imageALT = $image['alt']; ?>
							<?php if ( $image ) { ?>
                                <div class="slide-image" style="max-width: 123px;width:8.542vw;">
                                    <img src="<?php echo $imageURL; ?>" alt="<?php echo $imageALT; ?>"/>
                                </div>
							<?php } ?>
						<?php endwhile; ?>
                    </div>
				<?php else : ?>
					<?php // no rows found ?>
				<?php endif; ?>
            </div>
            <div class="cell large-auto small-12 content">
                <h1><?php the_title(); ?></h1>
                <div class="code">
					<?php echo $code; ?>
                </div>
                <div class="description">
					<?php echo $description; ?>
                </div>
                <div class="price">
                    <span class="only">Only</span>
                    £<?php echo $price; ?><span class="only"> Ex VAT</span>
                </div>
                <div class="buttons"><a href="#" class="button lime wishlist add" data-post="<?php the_ID();?>">Add to wishlist</a> <a href="#" class="button lime wishlist remove" data-post="<?php the_ID();?>">Remove from wishlist</a> <a href="/store-locator/" class="button green">Nearest
                        stockist</a> </div>
                <ul class="accordion" data-accordion data-allow-all-closed="true">

					<?php if ( $summary ): ?>
                        <li class="accordion-item" data-accordion-item>
                            <a href="#" class="accordion-title">Summary</a>
                            <div class="accordion-content" data-tab-content>
								<?php echo $summary; ?>
                            </div>
                        </li>
					<?php endif; ?>
					<?php if ( $specifications ): ?>
                        <li class="accordion-item" data-accordion-item>
                            <a href="#" class="accordion-title">Specifications</a>
                            <div class="accordion-content" data-tab-content>
								<?php echo $specifications; ?>
                            </div>
                        </li>
					<?php endif; ?>

					<?php if ( have_rows( 'downloads' ) ) : ?>
                        <li class="accordion-item" data-accordion-item>
                            <a href="#" class="accordion-title">Downloads</a>
                            <div class="accordion-content" data-tab-content>
								<?php while ( have_rows( 'downloads' ) ) : the_row(); ?>
									<?php $description = get_sub_field( 'description' ); ?>

									<?php $download = get_sub_field( 'download' ); ?>
									<?php if ( $download ) { ?>
                                        <a href="<?php echo $download['url']; ?>" target="_blank"><?php echo $description; ?></a><br>
									<?php } ?>
								<?php endwhile; ?>
                            </div>
                        </li>
					<?php endif; ?>


                    <li class="accordion-item wishlist display" data-accordion-item>
                        <a href="#" class="accordion-title">Wishlist</a>
                        <div class="accordion-content" data-tab-content>
                            <div class="list">
                            <?php
                            echo get_wish_list();
                            ?>
                            </div>
                            <div class="email-form">
                                <script>
                                    function sendmail() {
                                        let email = [];
                                        email.push(jQuery('#pmEmail').val());
                                        email.push(jQuery('#pmSubject').val());

                                        let json_email = JSON.stringify(email);
                                        Cookies.set('eparams',json_email);

                                        jQuery.ajax({
                                            type : "post",
                                            data: {action: 'send_SMTP_wishlist', security: mailing_ajax_object.security},
                                            url : mailing_ajax_object.ajax_url,
                                            success: function(response) {
                                                alert(response);
                                            }
                                        })

                                        /*jQuery.ajax({
                                            type : "get",
                                            dataType : 'text',
                                            url : mailing_ajax_object.ajax_url,
                                            data : {action: "get_plain_email_wish_list"},
                                            success: function(response) {
                                                window.open('mailto:'+jQuery('#pmEmail').val()+'?subject='+jQuery('#pmSubject').val()+'&body='+response);
                                            }
                                        })*/

                                    }
                                </script>
                                <strong>Email wishlist:</strong><br>
                                <form action="javascript:sendmail();" name="wlForm" method="post">
                                    <label>
                                        email: <input name="pmEmail" id="pmEmail" type="text" maxlength="64" style="width:98%;" />
                                    </label>
                                    <label>
                                        subject:
                                        <input name="pmSubject" id="pmSubject" type="text" maxlength="64" style="width:98%;" />
                                    </label>
                                    <input name="pmSubmit" type="submit" value="Send" />
                                </form>
                            </div>

                        </div>
                    </li>

                </ul>

                <div class="wishlist">
                    <script>
                        jQuery(document).ready(function ($) {

                            function setupWishList() {
                                let json_wishes = Cookies.get('wishlist');
                                let postID = $('.wishlist.add').data('post');
                                //window.console.log('posts in wishlist: ' + json_wishes);
                                //window.console.log('This post is: ' + postID);
                                if(json_wishes) {
                                    let wishes = JSON.parse(json_wishes);
                                    if(wishes.includes(postID)) {
                                        //window.console.log('Thinks that postID is in wishlist');
                                        $('.wishlist.add').hide();
                                        $('.wishlist.remove').show();
                                        $('.email-form').show();
                                    } else {
                                        //window.console.log('Thinks that postID is not in wishlist');
                                        $('.wishlist.add').show();
                                        $('.wishlist.remove').hide();
                                        $('.email-form').show();
                                    }
                                    if(wishes.length < 1) {
                                        //window.console.log('Thinks wishlist is empty');
                                        $('.wishlist.remove').hide();
                                        $('.wishlist.add').show();
                                        $('.email-form').hide();
                                    }
                                } else {
                                    //window.console.log('Thinks wishlist not created yet');
                                    $('.wishlist.remove').hide();
                                    $('.wishlist.add').show();
                                    $('.email-form').hide();
                                }
                                jQuery.ajax({
                                    type : "post",
                                    url : mailing_ajax_object.ajax_url,
                                    data : {action: "get_wish_list"},
                                    success: function(response) {
                                        $('.wishlist .accordion-content .list').html(response);
                                    }
                                })
                            }
                            setupWishList();
                            $('.wishlist.add').click(function (e) {
                                e.preventDefault();
                                //window.console.log('Add Clicked');
                                let postID = $(this).data('post');
                                let json_wishes = Cookies.get('wishlist');
                                let wishes = [];
                                if(json_wishes) {
                                    wishes = JSON.parse(json_wishes);
                                    if(!wishes.includes(postID)) {
                                        wishes.push(postID);
                                    }
                                }
                                json_wishes = JSON.stringify(wishes);
                                Cookies.set('wishlist',json_wishes);
                                setupWishList();
                            });
                            $('.wishlist.remove').click(function (e) {
                                e.preventDefault();
                                let postID = $(this).data('post');
                                let json_wishes = Cookies.get('wishlist');
                                let wishes = [];
                                if(json_wishes) {
                                    wishes = JSON.parse(json_wishes);
                                    if(wishes.includes(postID)) {
                                        let index = wishes.indexOf(postID);
                                        wishes.splice(index, 1);;
                                    }
                                }
                                json_wishes = JSON.stringify(wishes);
                                Cookies.set('wishlist',json_wishes);
                                setupWishList();
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>

<?php get_footer(); ?>