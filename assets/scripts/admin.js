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


function isNotValidDate(date) {
    return date == null || isNaN(date.getTime());
}

adminJQ = jQuery.noConflict();

adminJQ(function ($) {

    let debug = false;
    let cMax = 1;

    function _o(text) {
        $('#ePimResult').prepend(text + '<br>');
    }

    let oneProductLinkImages = new ts_execute_queue('#ePimResult',function() {
        _o('Finished');
    },function (action, request, data) {
        _o('Action Completed: ' + action);
        _o('Request: ' + request);
        _o('<br>Data: ' + data);
        if(action==='product_ID_code') {
            this.queue(ajaxurl,{action: 'product_group_image_link',productID:data});
        }
    });

    let oneProductQueue = new ts_execute_queue('#ePimResult', function () {
        oneProductLinkImages.queue(ajaxurl, {action: 'product_ID_code', CODE: $('#pCode').val()});
        oneProductLinkImages.process();
    }, function (action, request, data) {
        _o('Action Completed: ' + action);
        _o('Request: ' + request);
        _o('<br>Data: ' + data);
        if (action === 'product_ID_code') {
            this.queue(ajaxurl, {action: 'get_product', ID: data});
        }
        if (action === 'get_product') {
            let product = JSON.parse(data);
            let obj = this;
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
        if (action === 'create_product') {
            let r = decodeURIComponent(request);
            let ro = QueryStringToJSON('?' + r);
            this.queue(ajaxurl,{action: 'import_single_product_images', productID: ro.productID, variationID: ro.variationID});
        }
    });


    let linkProductImages = new ts_execute_queue('#ePimResult', function () {
        _o('<strong>All Finished</strong>');
    },function (action,request,data ) {
        _o('Action Completed: ' + action);
        _o('Request: ' + request);
        _o('<br>Data: ' + data);
    });

    let processProductImages = new ts_execute_queue('#ePimResult', function(){
        _o('Checking and linking product image data - this may take a while.....');
        linkProductImages.reset();
        linkProductImages.queue(ajaxurl,{action: 'product_image_link'});
        linkProductImages.process();
    }, function (action, request, data) {
        _o('Action Completed: ' + action);
        _o('Request: ' + request);
        _o('<br>Data: ' + data);
        if(action==='get_product_images') {
            if ($.trim(data)) {
                $(data).each(function (index, productsImageID) {
                    processProductImages.queue(ajaxurl,{action: 'get_picture_web_link', ID: productsImageID.id});
                });
            }
        }
        if (action === 'get_picture_web_link') {
            if ($.trim(data)) {
                let pictures = JSON.parse(data);
                let obj = this;
                $(pictures).each(function (index, picture) {
                    obj.queue(ajaxurl,{action: 'import_picture', ID: picture.Id, weblink: picture.WebPath});
                })
            }
        }
    });

    let updateAllProducts = new ts_execute_queue('#ePimResult', function () {
        processProductImages.reset();
        processProductImages.queue(ajaxurl,{action: 'get_product_images'});
        processProductImages.process();
    }, function (action, request, data) {
        _o('Action Completed: ' + action);
        _o('Request: ' + request);
        _o('<br>Data: ' + data);
        if(action==='sort_categories') {
            updateAllProducts.queue(ajaxurl,{action: 'cat_image_link'});
        }
        if(action==='cat_image_link') {
            updateAllProducts.queue(ajaxurl,{action: 'get_all_products'});
        }
        if(action==='get_all_products') {
            if ($.trim(data)) {
                let products = JSON.parse(data);
                let c = 0;
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

    let updateSinceProducts = new ts_execute_queue('#ePimResult', function () {
        if(updateSinceProducts.processFinished) {
            processProductImages.reset();
            processProductImages.queue(ajaxurl, {action: 'get_product_images'});
            processProductImages.process();
        } else {
            _o('All Finished');
        }
    }, function (action, request, data) {
        _o('Action Completed: ' + action);
        _o('Request: ' + request);
        _o('<br>Data: ' + data);
        if(action==='sort_categories') {
            updateSinceProducts.queue(ajaxurl,{action: 'cat_image_link'});
        }
        if(action==='cat_image_link') {
            let dpDate = $('.custom_date').datepicker('getDate');
            let dateUtc = localAsUtc(dpDate);
            let iso = dateUtc.toISOString();
            updateSinceProducts.queue(ajaxurl,{action: 'get_all_changed_products_since', timeCode: iso});
        }
        if(action==='get_all_changed_products_since') {
            if( data!='[]' ) {
                //window.console.log(data);
                if ($.trim(data)) {
                    let products = JSON.parse(data);
                    let c = 0;
                    $(products).each(function (index, product) {
                        $(product.VariationIds).each(function (index, variationID) {
                            updateSinceProducts.queue(ajaxurl, {
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
                } else {
                    _o('<strong>No Products Found to Update or Create');
                    updateSinceProducts.processFinished = false;
                }
            } else {
                _o('<strong>No Products Found to Update or Create');
                updateSinceProducts.processFinished = false;
            }
            //updateAllProducts.queue(ajaxurl,{action: 'get_all_products'});
        }
    });

    let updateSinceQueue = new ts_execute_queue('#ePimResult', function () {
        _o('Category Data Imported');
        updateSinceProducts.reset();
        updateSinceProducts.queue(ajaxurl,{action: 'sort_categories'});
        updateSinceProducts.process();
    }, function (action, request, data) {
        _o('Action Completed: ' + action);
        _o('Request: ' + request);
        _o('<br>Data: ' + data);
        if(action==='get_all_categories') {
            let categories = JSON.parse(data);
            let obj = this;
            let c = 0;
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

        if (action === 'create_category') {
            let r = decodeURIComponent(request);
            let ro = QueryStringToJSON('?' + r);
            let id = ro.ID;
            this.queue(ajaxurl,{action: 'get_category_images', ID: id});
        }
        if (action === 'get_category_images') {
            if ($.trim(data)) {
                let pictures = JSON.parse(data);
                let obj = this;
                $(pictures).each(function (index, picture) {
                    obj.queue(ajaxurl,{action: 'get_picture_web_link', ID: picture});
                })
            }
        }
        if (action === 'get_picture_web_link') {
            if ($.trim(data)) {
                let pictures = JSON.parse(data);
                let obj = this;
                $(pictures).each(function (index, picture) {
                    obj.queue(ajaxurl,{action: 'import_picture', ID: picture.Id, weblink: picture.WebPath});
                })

            }
        }
    });

    let updateAllQueue = new ts_execute_queue('#ePimResult', function () {
        _o('Category Data Imported');
        updateAllProducts.reset();
        updateAllProducts.queue(ajaxurl,{action: 'sort_categories'});
        updateAllProducts.process();
    }, function (action, request, data) {
        _o('Action Completed: ' + action);
        _o('Request: ' + request);
        _o('<br>Data: ' + data);
        if(action==='get_all_categories') {
            let categories = JSON.parse(data);
            let obj = this;
            let c = 0;
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

        if (action === 'create_category') {
            let r = decodeURIComponent(request);
            let ro = QueryStringToJSON('?' + r);
            let id = ro.ID;
            this.queue(ajaxurl,{action: 'get_category_images', ID: id});
        }
        if (action === 'get_category_images') {
            if ($.trim(data)) {
                let pictures = JSON.parse(data);
                let obj = this;
                $(pictures).each(function (index, picture) {
                    obj.queue(ajaxurl,{action: 'get_picture_web_link', ID: picture});
                })
            }
        }
        if (action === 'get_picture_web_link') {
            if ($.trim(data)) {
                let pictures = JSON.parse(data);
                let obj = this;
                $(pictures).each(function (index, picture) {
                    obj.queue(ajaxurl,{action: 'import_picture', ID: picture.Id, weblink: picture.WebPath});
                })

            }
        }
    });

    $('#CreateCategories').on('click',function () {
        updateAllQueue.reset();
        updateAllQueue.queue(ajaxurl,{action: 'get_all_categories'});
        updateAllQueue.process();
    });

    $('#UpdateCode').on('click',function () {
        oneProductQueue.reset();
        oneProductQueue.queue(ajaxurl, {action: 'product_ID_code', CODE: $('#pCode').val()});
        oneProductQueue.process();
    });

    $('#UpdateSince').on('click',function () {

        /** Check Categories First
        updateSinceQueue.reset();
        updateSinceQueue.queue(ajaxurl,{action: 'get_all_categories'});
        updateSinceQueue.process();
         **/

        /** Do not check categories **/
        updateSinceProducts.reset();
        let dpDate = $('.custom_date').datepicker('getDate');
        let dateUtc = localAsUtc(dpDate);
        let iso = dateUtc.toISOString();
        updateSinceProducts.queue(ajaxurl,{action: 'get_all_changed_products_since', timeCode: iso});
        updateSinceProducts.process();

    });


    $('.custom_date').datepicker({
        dateFormat: 'yy-mm-dd'
    });

})
;