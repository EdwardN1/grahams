<?php

/*
Template Name: API Debug
*/
get_header(); ?>

    <div class="content grid-container">

        <div class="inner-content grid-x">

            <main class="main small-12 large-12 medium-12 cell" role="main">

				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

                    <ul class="tabs" data-tabs id="example-tabs">
                        <li class="tabs-title is-active"><a href="#panel1" aria-selected="true">Categories</a></li>
                        <li class="tabs-title"><a data-tabs-target="panel2" href="#panel2">Products</a></li>
                        <li class="tabs-title"><a data-tabs-target="panel3" href="#panel3">Attributes</a></li>
                    </ul>

                    <div class="tabs-content" data-tabs-content="example-tabs">
                        <div class="tabs-panel is-active" id="panel1">
							<?php
							$jsonCategories = get_api_all_categories();
							//displayJSON($jsonCategories);
							$categories = json_decode( $jsonCategories );


							usort( $categories, "sortCategoriesByID_cmp" );

							function getChildren( $id, $categories ) {
								$children = array();
								foreach ( $categories as $category ) {
									if ( $category->ParentId == $id ) {
										$children[] = $category->Id;
									}
								}

								return $children;
							}

							foreach ( $categories as $category ) {
								$categoryID   = $category->Id;
								$categoryName = $category->Name;
								?>
                                <div>API data for CategoryID:<?php echo $categoryID; ?>
                                    <strong>Name:<?php echo $categoryName; ?></strong></div>
                                <div style="font-size: 10px;">What's in the object: <?php print_r( $category ); ?></div>
								<?php
								$children = getChildren( $categoryID, $categories );
								if ( $children ) {
									?>
                                    <div style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                                        <strong>Child Categories:</strong><br>
										<?php
										foreach ( $children as $child ) {
											$childCat = returnCategory( $child, $categories );
											echo 'ID: ' . $childCat->Id . ' | Name: ' . $childCat->Name . '</br>';
										}
										?>
                                    </div>
									<?php
								}

								?>
                                <hr>
								<?php
							}
							?>
                        </div>

                        <div class="tabs-panel" id="panel2">
							<?php
							$jsonProducts   = get_api_all_products();
							$products       = json_decode( $jsonProducts );
							$jsonAttributes = get_api_all_attributes();
							$attributes     = json_decode( $jsonAttributes );
							//displayJSON($jsonProducts);
							foreach ( $products->Results as $product ) {
								$productName = $product->Name;
								if ( $productName == '' ) {
									$productName = '<span style="color: red">No name specified for this product - cannot import</span>';
								}
								?>
                                <div>API Data for ProductId: <?php echo $product->Id; ?>
                                    <strong>Name: <?php echo $productName; ?></strong></div>
                                <div style="font-size: 10px;">What's in the object: <?php print_r( $product ); ?></div>
                                <div>
                                    BulletText:<br>
                                    <?php echo $product->BulletText;?>
                                </div>
                                <div>CategoryIds: <br><?php
									foreach ( $product->CategoryIds as $categoryId ) {
										echo $categoryID . '</br>';
									}
									?>
                                </div>
                                <div>
                                    Picture URIS:<br>
									<?php
									foreach ( $product->PictureIds as $picture_id ) {
										$jsonPicture = get_api_picture( $picture_id );
										$picture     = json_decode( $jsonPicture );
										?>
                                        <div style="font-size: 10px;">What's in the object: <?php print_r( $picture ); ?></div>
                                        <div><?php echo $picture->WebPath ?></div>
										<?php
									}
									?>
                                </div>
                                <div style="padding-left: 2rem;">
                                    Variations:<br>
									<?php
									foreach ( $product->VariationIds as $variation_id ) {
										$jsonVariation = get_api_variation( $variation_id );
										$variation     = json_decode( $jsonVariation );
										?>
                                        <div>
                                            "Id": <?php echo $variation->Id; ?><br>
                                            "ProductId": <?php echo $variation->ProductId; ?><br>
                                            "Name": <?php echo $variation->Name; ?><br>
                                            "Part_Code": <?php echo $variation->Part_Code; ?><br>
                                            "SKU": <?php echo $variation->SKU; ?><br>
                                            "Price": <?php echo $variation->Price; ?><br>
											<?php
											foreach ( $variation->PictureIds as $picture_id ) {
												$jsonPicture = get_api_picture( $picture_id );
												$picture     = json_decode( $jsonPicture );
												?>
                                                "Picture": <?php echo $picture->WebPath ?><br>
												<?php
											}
											?>
                                            <div style="padding-left: 2rem;">AttributeValues: <br>
												<?php
												foreach ( $variation->AttributeValues as $attribute_value ) {
                                                    $aName = getAttributeNameFromID($attribute_value->AttributeId,$attributes);
                                                    echo $attribute_value->AttributeId.' | '.$aName.': '.$attribute_value->Value.'<br>';
												}
												?>
                                            </div>
                                        </div>
										<?php
									}
									?>
                                </div>
                                <hr>
								<?php
							}
							?>
                            <hr>
                        </div>

                        <div class="tabs-panel" id="panel3">
                            <?php
                            //displayJSON($jsonAttributes);
                            foreach ($attributes as $attribute) {
                                print_r($attribute);
                                echo '<br>';
                            }
                            ?>
                        </div>

                    </div>

				<?php endwhile; endif; ?>

            </main> <!-- end #main -->

        </div> <!-- end #inner-content -->

    </div> <!-- end #content -->

<?php get_footer(); ?>