jQuery(document).ready(function ($) {

    var debug = false;
    var cMax = 3;

    function _o(text) {
        $('#ePimResult').prepend(text + '<br>');
    }

    function _or(text) {
        $('#ePimResult').prepend(text);
    }

    function localAsUtc(date) {
        if (isNotValidDate(date)) {
            return null;
        }

        return new Date(Date.UTC(
            date.getFullYear(),
            date.getMonth(),
            date.getDate(),
            date.getHours(),
            date.getMinutes(),
            date.getSeconds(),
            date.getMilliseconds()
        ));
    }

    function isValidDate (date) {
        return !isNotValidDate(date);
    }

    function isNotValidDate(date) {
        return date == null || isNaN(date.getTime());
    }

    var oneProductQueue = new ts_execute_queue('#ePimResult', function () {

        var oneProductLinkImages = new ts_execute_queue('#ePimResult',function() {
            _o('Finished');
        },function (action, request, data) {
            _o('Action Completed: ' + action);
            _o('Request: ' + request);
            _o('<br>Data: ' + data);
            if(action=='product_ID_code') {
                this.queue(ajaxurl,{action: 'product_group_image_link',productID:data});
            }
        });
        oneProductLinkImages.queue(ajaxurl, {action: 'product_ID_code', CODE: $('#pCode').val()});
        oneProductLinkImages.process();
    }, function (action, request, data) {
        _o('Action Completed: ' + action);
        _o('Request: ' + request);
        _o('<br>Data: ' + data);
        if (action == 'product_ID_code') {
            this.queue(ajaxurl, {action: 'get_product', ID: data});
        }
        if (action == 'get_product') {
            var product = $.parseJSON(data);
            obj = this;
            $(product.VariationIds).each(function (index, variationID) {
                obj.queue(ajaxurl, {
                    action: 'create_product',
                    productID: product.Id,
                    variationID: variationID,
                    bulletText: product.BulletText,
                    productName: product.Name,
                    categoryIDs: product.CategoryIds,
                    pictureIDs: product.PictureIds
                });
            });
        }
        if (action == 'create_product') {
            var r = decodeURIComponent(request);
            var ro = QueryStringToJSON('?' + r);
            this.queue(ajaxurl,{action: 'import_single_product_images', productID: ro.productID, variationID: ro.variationID});
        }
    });

    $('#UpdateCode').click(function () {
        oneProductQueue.reset();
        oneProductQueue.queue(ajaxurl, {action: 'product_ID_code', CODE: $('#pCode').val()});
        oneProductQueue.process();
    });

    var linkProductImages = new ts_execute_queue('#ePimResult', function () {
        _o('<strong>All Finished</strong>');
    },function (action,request,data ) {
        _o('Action Completed: ' + action);
        _o('Request: ' + request);
        _o('<br>Data: ' + data);
    });

    var processProductImages = new ts_execute_queue('#ePimResult', function(){
        _o('Checking and linking product image data - this may take a while.....')
        linkProductImages.reset()
        linkProductImages.queue(ajaxurl,{action: 'product_image_link'});
        linkProductImages.process();
    }, function (action, request, data) {
        _o('Action Completed: ' + action);
        _o('Request: ' + request);
        _o('<br>Data: ' + data);
        if(action=='get_product_images') {
            if ($.trim(data)) {
                $(data).each(function (index, productsImageID) {
                    processProductImages.queue(ajaxurl,{action: 'get_picture_web_link', ID: productsImageID.id});
                });
            }
        }
        if (action == 'get_picture_web_link') {
            if ($.trim(data)) {
                pictures = $.parseJSON(data);
                obj = this;
                $(pictures).each(function (index, picture) {
                    obj.queue(ajaxurl,{action: 'import_picture', ID: picture.Id, weblink: picture.WebPath});
                })
            }
        }
    });

    var updateAllProducts = new ts_execute_queue('#ePimResult', function () {
        processProductImages.reset();
        processProductImages.queue(ajaxurl,{action: 'get_product_images'});
        processProductImages.process();
    }, function (action, request, data) {
        _o('Action Completed: ' + action);
        _o('Request: ' + request);
        _o('<br>Data: ' + data);
        if(action=='sort_categories') {
            updateAllProducts.queue(ajaxurl,{action: 'cat_image_link'});
        }
        if(action=='cat_image_link') {
            updateAllProducts.queue(ajaxurl,{action: 'get_all_products'});
        }
        if(action=='get_all_products') {
            if ($.trim(data)) {
                var products = $.parseJSON(data);
                var c = 0;
                $(products).each(function (index, product) {
                    $(product.VariationIds).each(function (index, variationID) {
                        updateAllProducts.queue(ajaxurl,{
                            action: 'create_product',
                            productID: product.Id,
                            variationID: variationID,
                            bulletText: product.BulletText,
                            productName: product.Name,
                            categoryIDs: product.CategoryIds,
                            pictureIDs: product.PictureIds
                        });
                    });
                    if (debug) {
                        c++;
                        if (c >= cMax) {
                            return false;
                        }
                    }
                });
            }
            //updateAllProducts.queue(ajaxurl,{action: 'get_all_products'});
        }
    });

    var updateAllQueue = new ts_execute_queue('#ePimResult', function () {
        _o('Category Data Imported');
        updateAllProducts.reset();
        updateAllProducts.queue(ajaxurl,{action: 'sort_categories'});
        updateAllProducts.process();
    }, function (action, request, data) {
        _o('Action Completed: ' + action);
        _o('Request: ' + request);
        _o('<br>Data: ' + data);
        if(action=='get_all_categories') {
            categories = $.parseJSON(data);
            var obj = this;
            var c = 0;
            $(categories).each(function (index, record) {
                obj.queue(ajaxurl,{action: 'create_category', ID: record.Id, name: record.Name, ParentID: record.ParentId, picture_ids: record.PictureIds});
                if(debug) {
                    c++;
                    if (c >= cMax) {
                        return false;
                    }
                }
            });
        }

        if (action == 'create_category') {
            let r = decodeURIComponent(request);
            let ro = QueryStringToJSON('?' + r);
            let id = ro.ID;
            this.queue(ajaxurl,{action: 'get_category_images', ID: id});
        }
        if (action == 'get_category_images') {
            if ($.trim(data)) {
                pictures = $.parseJSON(data);
                obj = this;
                $(pictures).each(function (index, picture) {
                    obj.queue(ajaxurl,{action: 'get_picture_web_link', ID: picture});
                })
            }
        }
        if (action == 'get_picture_web_link') {
            if ($.trim(data)) {
                pictures = $.parseJSON(data);
                obj = this;
                $(pictures).each(function (index, picture) {
                    obj.queue(ajaxurl,{action: 'import_picture', ID: picture.Id, weblink: picture.WebPath});
                })

            }
        }
    });



    $('#CreateCategories').click(function () {
        updateAllQueue.reset();
        updateAllQueue.queue(ajaxurl,{action: 'get_all_categories'})
        updateAllQueue.process();
    });

    $('#UpdateSince').click(function () {
        updateProductsSinceQueue.reset();
        var dpDate = $('.custom_date').datepicker('getDate');
        var dateUtc = localAsUtc(dpDate);
        var iso = dateUtc.toISOString(); // returns "2016-12-06T00:00:00.000Z"
        alert(iso);
        //updateProductsSinceQueue.queue(ajaxurl,{action: 'get_all_categories'})
        //updateProductsSinceQueue.process();
    });

   /* $('#CreateCategoriesx').click(function () {
        resetQueue();
        thisRequest = $.ajax({
            type: "POST",
            url: ajaxurl,
            data: {action: 'get_all_categories'}
        }).done(function (data) {
            categories = $.parseJSON(data);
            _or('<p>Data received:</p>');
            _or('<pre>' + data + '</pre>');
            _o('<strong>Processing ePim Data...</strong>');
            var c = 0;
            $(categories).each(function (index, record) {
                queue.push({action: 'create_category', ID: record.Id, name: record.Name, ParentID: record.ParentId, picture_ids: record.PictureIds});
                if (debug) {
                    c++;
                    if (c >= cMax) {
                        return false;
                    }
                }
            });

            execute_queue(index);
        });
        requests.push(thisRequest);


    });*/

    $('.custom_date').datepicker({
        dateFormat: 'yy-mm-dd'
    });

})
;