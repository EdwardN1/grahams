<?php
add_action('admin_menu', 'epim_admin_menu');

function epim_admin_menu()
{
    add_menu_page('ePim Management', 'ePim', 'manage_options', 'epim-admin-page.php', 'epim_admin_page', 'dashicons-tickets', 6);
}

function epim_admin_page()
{
    ?>

    <div class="wrap">
        <h2>ePim Management</h2>
        <!--<p>
            <strong>Make Ajax Request</strong>
        </p>
        <p>
            <input type="text" id="aRequest" style="width: 100%;"><br>
        </p>
        <p>
            <button id="btnReqest" class="button">Request</button>
        </p>-->
        <p>
            <strong>Update by product code(SKU):</strong><br>This will only update existing products. If you have added new products in ePim then you need to Create them using either of the two options below first.
        </p>
        <p>
            <input type="text" id="pCode"><br>
        </p>
        <p>
            <button id="UpdateCode" class="button">Update</button>
        </p>
        <hr>
        <p>
            or <strong>Create of Update by product added or changed since:</strong><br>NB if you have added new Categories in ePim, Create and Update those first as per below.
        </p>
        <p><input type="text" class="custom_date" name="start_date" id="#start_date" value=""/></p>
        <p>
            <button id="UpdateSince" class="button">Update</button>
        </p>
        <hr>
        <p>
            or <button id="CreateAll" class="button">Create and Update all (Ajax)</button><br>(This will take a long time)
        </p>
        <hr>
        <p>
            or <button id="CreateCategories" class="button">Create and Update Categories</button>
        </p>
        <hr>
        <div id="ePimResult"></div>
    </div>
    <?php
}

function db_create_categories_handler()
{
    create_categories();
    echo 'Categories Created and Updated <br><a href="/wp-admin/">Return to dashboard</a>';
}

function db_sort_categories_handler()
{
    sort_categories();
    echo 'Categories Sorted <br><a href="/wp-admin/admin.php?page=epim-admin-page.php">Return to dashboard</a>';
}

function db_create_products_handler()
{
    create_products();
    echo 'Products Created/Updated <br><a href="/wp-admin/admin.php?page=epim-admin-page.php">Return to dashboard</a>';
}

function db_import_product_images_handler()
{
    importProductImages();
    echo 'Product Images Created/Updated <br><a href="/wp-admin/admin.php?page=epim-admin-page.php">Return to dashboard</a>';
}

function db_import_variation_images_handler()
{
    importVariationImages();
    echo 'Variation Images Created/Updated <br><a href="/wp-admin/admin.php?page=epim-admin-page.php">Return to dashboard</a>';
}

function db_import_category_images_handler()
{
    echo importCategoryImages();
    echo 'Category Images Created/Updated <br><a href="/wp-admin/admin.php?page=epim-admin-page.php">Return to dashboard</a>';
}

function db_link_images_handler()
{
    echo linkProductImages();
    echo 'Images Linked<br><a href="/wp-admin/admin.php?page=epim-admin-page.php">Return to dashboard</a>';
}

function db_link_category_images_handler()
{
    echo linkCategoryImages();
    echo 'Images Linked<br><a href="/wp-admin/admin.php?page=epim-admin-page.php">Return to dashboard</a>';
}

add_action('admin_post_create_categories', 'db_create_categories_handler');
add_action('admin_post_sort_categories', 'db_sort_categories_handler');
add_action('admin_post_create_products', 'db_create_products_handler');
add_action('admin_post_import_product_images', 'db_import_product_images_handler');
add_action('admin_post_import_variation_images', 'db_import_variation_images_handler');
add_action('admin_post_import_category_images', 'db_import_category_images_handler');
add_action('admin_post_link_images', 'db_link_images_handler');
add_action('admin_post_link_category_images', 'db_link_category_images_handler');