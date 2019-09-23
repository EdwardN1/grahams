<?php

/*
Template Name: API Debug
*/
get_header(); ?>

    <div class="content grid-container">

        <div class="inner-content grid-x">

            <main class="main small-12 large-12 medium-12 cell" role="main">

                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                    <ul class="tabs" data-tabs id="example-tabs">
                        <li class="tabs-title is-active"><a href="#panel1" aria-selected="true">Categories</a></li>
                        <li class="tabs-title"><a data-tabs-target="panel2" href="#panel2">Products</a></li>
                    </ul>

                    <div class="tabs-content" data-tabs-content="example-tabs">
                        <div class="tabs-panel is-active" id="panel1">
                            <?php
                            $jsonCategories = get_api_all_categories();
                            //displayJSON($jsonCategories);
                            $categories = json_decode($jsonCategories);

                            function sortCategoried($a, $b)
                            {
                                $res = 0;
                                if ($a->Id < $b->Id) $res = -1;
                                if ($a->Id > $b->Id) $res = 1;
                                return $res;
                            }

                            usort($categories, "sortCategoried");

                            function returnCategory($id, $categories)
                            {
                                $res = false;
                                foreach ($categories as $category) {
                                    if ($category->Id == $id) {
                                        $res = clone $category;
                                        break;
                                    }
                                }
                                return $res;
                            }

                            /*$catSortOrder = array();
                            $parents = array();
                            $orphans = array();
                            $children = array();

                            foreach ($categories as $category) {
                                if($category->ParentId) {
                                    if(!in_array($category->ParentId,$parents)) {
                                        $parents[]=$category->ParentId;
                                        $parent = array("ID"=>$category->ParentId,"Child"=>$category->Id);
                                        if(!in_array($parent,$children)) {
                                            $children[]=$parent;
                                        }
                                    }
                                } else {
                                    if(!in_array($category->ParentId,$parents)) {
                                        $orphans[]=$category->ParentId;
                                    }
                                }
                            }*/

                            function getChildren($id, $categories)
                            {
                                $children = array();
                                foreach ($categories as $category) {
                                    if ($category->ParentId == $id) {
                                        $children[] = $category->Id;
                                    }
                                }
                                return $children;
                            }

                            foreach ($categories as $category) {
                                $categoryID = $category->Id;
                                $categoryName = $category->Name;
                                ?>
                                <div>API data for CategoryID:<?php echo $categoryID; ?>
                                    <strong>Name:<?php echo $categoryName; ?></strong></div>
                                <div style="font-size: 10px;">What's in the object: <?php print_r($category); ?></div>
                                <?php
                                $children = getChildren($categoryID, $categories);
                                if ($children) {
                                    ?>
                                    <div style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                                        <strong>Child Categories:</strong><br>
                                        <?php
                                        foreach ($children as $child) {
                                            $childCat = returnCategory($child, $categories);
                                            echo 'ID: ' . $childCat->Id . ' | Name: ' . $childCat->Name . '</br>';
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                /*$jsonProducts = get_api_products_in_category($categoryID);
                                $products = json_decode($jsonProducts);
                                if (count((array)$products) < 1) {
                                    */?><!--
                                    <div style="color: red;">No products returned in this Category</div>
                                    <?php
/*                                }
                                foreach ($products as $product) {
                                    */?>
                                    <div style="padding-left: 2rem">
                                        <div>Product Name: <strong><?php /*echo $product->Name; */?></strong></div>
                                        <div><strong>BulletText:</strong><br><?php /*echo $product->BulletText; */?></div>
                                        <hr>
                                    </div>
                                    --><?php
/*                                }*/
                                ?>
                                <hr>
                                <?php
                            }
                            ?>
                        </div>

                        <div class="tabs-panel" id="panel2">
                            <?php
                            $jsonProducts = get_api_all_products();
                            $products = json_decode($jsonProducts);
                            //displayJSON($jsonProducts);
                            foreach ($products->Results as $product) {
                                $productName = $product->Name;
                                if ($productName == '') {
                                    $productName = '<span style="color: red">No name specified for this product - cannot import</span>';
                                }
                                ?>
                                <div>API Data for ProductId: <?php echo $product->Id; ?>
                                    <strong>Name: <?php echo $productName; ?></strong></div>
                                <div style="font-size: 10px;">What's in the object: <?php print_r($product);?></div>
                                <div>CategoryIds: <br><?php
                                    foreach ($product->CategoryIds as $categoryId) {
                                        echo $categoryID . '</br>';
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>
                        </div>

                    </div>

                <?php endwhile; endif; ?>

            </main> <!-- end #main -->

        </div> <!-- end #inner-content -->

    </div> <!-- end #content -->

<?php get_footer(); ?>