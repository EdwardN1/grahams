jQuery(document).ready(function ($) {

    var debug = true;
    var cMax = 3;

    var queue = new Array();

    var requests = new Array();

    var index = 0;

    function _o(text) {
        $('#ePimResult').prepend(text + '<br>');
    }

    function _or(text) {
        $('#ePimResult').prepend(text);
    }

    function resetQueue() {
        if (!finished) {
            _o('<strong>Aborting unfinished requests in queue!!!</strong>');
        }
        var numRequests = requests.length;
        for (var i = 0; i < numRequests; i++) {
            requests[i].abort();
        }
        requests = [];
        queue = [];
        index = 0;
        $('#ePimResult').empty();
    }

    var finished = false;
    var CatagoriesFinished = false;
    var ProductsFinished = false;
    var ProductImagesImported = false;
    var ProductImagesLinked = false;

    function showObject(obj) {
        var result = "";
        for (var p in obj) {
            if (obj.hasOwnProperty(p)) {
                result += p + " , " + obj[p] + "\n";
            }
        }
        return result;
    }

    /*var requestQueue = new ts_execute_queue('#ePimResult', function () {
        _o(Finished);
    }, function (action, request, data) {
        _o('Action Comleted = ' + action);
        _o('Request = ' + request);
        _o('<br>Data = ' + data);
    });*/

    /*$('#btnReqest').click(function () {
        requestQueue.reset()
        requestQueue.queue(ajaxurl,{action: 'image_imported', ID: 50983});
        requestQueue.process();
    });*/

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

    var execute_queue = function (index) {
        var request = $.ajax({
            data: queue[index],
            type: "POST",
            url: ajaxurl,
            success: function (data) {

                try {
                    var r = decodeURIComponent(this.data);
                    var ro = QueryStringToJSON('?' + r);
                } catch (e) {
                    _o('<span style="color: orange;">' + e.message + ' data: ' + this.data + '</span>');
                    _o('<span style="color: orange;">data: ' + this.data + '</span>');
                    var r = this.data;
                    var ro = QueryStringToJSON('?' + r);
                }

                try {
                    _o('Request Completed: ' + r + ' Response: ' + data);
                    var action = ro.action;
                    var id = ro.ID;
                    if (action == 'create_category') {
                        queue.push({action: 'get_category_images', ID: id});
                    }
                    if (action == 'get_category_images') {
                        if ($.trim(data)) {
                            pictures = $.parseJSON(data);
                            $(pictures).each(function (index, picture) {
                                queue.push({action: 'get_picture_web_link', ID: picture});
                            })
                        }
                    }
                    if (action == 'get_picture_web_link') {
                        if ($.trim(data)) {
                            pictures = $.parseJSON(data);
                            $(pictures).each(function (index, picture) {
                                queue.push({action: 'import_picture', ID: picture.Id, weblink: picture.WebPath});
                            })

                        }
                    }
                    if (action == 'get_all_products') {
                        if ($.trim(data)) {
                            var products = $.parseJSON(data);
                            var c = 0;
                            $(products).each(function (index, product) {
                                _o('Name = ' + product.Name + ' | VariationIds = ' + product.VariationIds + ' | PictureIds = ' + product.PictureIds + ' | CategoryIds = ' + product.CategoryIds);
                                /*var v = product.VariationIds;
                                var vs = v.split(',');*/
                                $(product.VariationIds).each(function (index, variationID) {
                                    queue.push({
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
                    }
                    if (action == 'get_product_images') {
                        if ($.trim(data)) {

                            $(data).each(function (index, productsImageID) {
                                queue.push({action: 'get_picture_web_link', ID: productsImageID.id});
                            });
                        }
                    }
                } catch (e) {
                    _o('<span style="color: red;">' + e.message + ' data: ' + this.data + '</span>');
                    _o('<span style="color: red;">data: ' + this.data + '</span>');
                }
                index++;    // going to next queue entry
                // check if it exists
                if (queue[index] != undefined) {
                    execute_queue(index);
                } else {
                    if (!CatagoriesFinished) {
                        CatagoriesFinished = true;
                        queue = [];
                        requests = [];
                        index = 0;
                        queue.push({action: 'sort_categories'});
                        queue.push({action: 'cat_image_link'});
                        execute_queue(index);
                    } else {
                        if (!ProductsFinished) {
                            ProductsFinished = true;
                            queue = [];
                            requests = [];
                            index = 0;
                            queue.push({action: 'get_all_products'});
                            _o('<strong>Fetching all products from ePim - please be patient...</strong>')
                            execute_queue(index);
                        } else {
                            if (!ProductImagesImported) {
                                ProductImagesImported = true;
                                queue = [];
                                requests = [];
                                index = 0;
                                queue.push({action: 'get_product_images'});
                                //_o('<strong>Importing Product Images...</strong>')
                                execute_queue(index);
                            } else {
                                if (!ProductImagesLinked) {
                                    ProductImagesLinked = true;
                                    queue = [];
                                    requests = [];
                                    index = 0;
                                    queue.push({action: 'product_image_link'});
                                    _o('<strong>Linking Product Images...</strong>');
                                    execute_queue(index);
                                } else {
                                    finished = true;
                                    _o('<strong>Queue Processed - Create and Update Finished OK :)</strong>');
                                }
                            }

                        }
                    }
                }


            }


        }); // end of $.ajax( {...
        requests.push(request);
    }; // end of execute_queue() {...


    var updateAllQueue = new ts_execute_queue('#ePimResult', function () {
        _o('Category Data Imported');
        var updateAllProducts = new ts_execute_queue('#ePimResult', function () {
            var processProductImages = new ts_execute_queue('#ePimResult', function(){
                var linkProductImages = new ts_execute_queue('#ePimResult', function () {
                    _o('<strong>All Finished</strong>');
                },function (action,request,data ) {
                    _o('Action Completed: ' + action);
                    _o('Request: ' + request);
                    _o('<br>Data: ' + data);
                });
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

    $('#CreateCategoriesx').click(function () {
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


    });

    $('.custom_date').datepicker({
        dateFormat: 'yy-mm-dd'
    });

})
;