jQuery(document).ready(function ($) {

    var debug = false;
    var cMax = 1;

    var queue = new Array();
    var single_queue = new Array();

    var requests = new Array();

    var index = 0;
    var single_index = 0;

    function _o(text) {
        $('#ePimResult').prepend(text + '<br>');
    }

    function _or(text) {
        $('#ePimResult').prepend(text);
    }

    function QueryStringToJSON(qs) {
        var pairs = qs.slice(1).split('&');

        var result = {};
        pairs.forEach(function (pair) {
            pair = pair.split('=');
            result[pair[0]] = decodeURIComponent(pair[1] || '');
        });

        return JSON.parse(JSON.stringify(result));
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

    function showObjectjQuery(obj) {
        var result = "";
        $.each(obj, function (k, v) {
            result += k + " , " + v + "\n";
        });
        return result;
    }

    var single_pictures_imported = false;

    var single_execute_queue = function (single_index) {
        var request = $.ajax({
            data: single_queue[single_index],
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
                /*========================*/
                try {
                    _o('Request Completed: ' + r + ' Response: ' + data);
                    var action = ro.action;
                    var id = ro.ID;
                    if (action == 'product_ID_code') {
                        var apiID = data;
                        _o('API Code = ' + apiID);
                        if (apiID) {
                            single_queue.push({action: 'get_product', ID: apiID});
                        }
                    }
                    if (action == 'get_product') {
                        _o('Product Data for: ' + apiID + ': ' + data);
                        var product = $.parseJSON(data);
                        _o('Name = ' + product.Name + ' | VariationIds = ' + product.VariationIds + ' | PictureIds = ' + product.PictureIds + ' | CategoryIds = ' + product.CategoryIds);
                        $(product.VariationIds).each(function (index, variationID) {
                           single_queue.push({
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
                    if(action=='create_product') {
                        single_queue.push({action: 'get_single_product_images',ID: ro.productID});
                        //_o('<strong>Importing Product Images...</strong>')
                    }
                    if(action=='get_single_product_images') {
                        if ($.trim(data)) {
                            $imageIDs = '';
                            _o('<strong>Importing Product Images...</strong>')
                            $(data).each(function (index, productsImageID) {
                                single_queue.push({action: 'get_picture_web_link', ID: productsImageID.id});
                                $imageIDs += productsImageID.id + ', ';
                            });
                            _o($imageIDs)
                        }
                    }
                    if(action=='get_picture_web_link') {
                        if ($.trim(data)) {
                            pictures = $.parseJSON(data);
                            $(pictures).each(function (index, picture) {
                                single_queue.push({action: 'import_picture', ID: picture.Id, weblink: picture.WebPath});
                            })
                        }
                    }
                } catch (e) {
                    _o('<span style="color: red;">' + e.message + ' data: ' + this.data + '</span>');
                    _o('<span style="color: red;">data: ' + this.data + '</span>');
                }
                /*=====================*/
                single_index++;    // going to next queue entry
                // check if it exists
                if (single_queue[single_index] != undefined) {
                    single_execute_queue(single_index);
                } else {
                    if(!single_pictures_imported) {
                        single_pictures_imported = true;
                        single_queue = [];
                        single_index = 0;
                        single_queue.push({action: 'product_image_link'});
                        _o('<strong>Linking Product Images...</strong>');
                        single_execute_queue(single_index);
                    } else {
                        _o('<strong>Product Updated</strong>');
                    }


                }
            }
        });
    };

    $('#UpdateCode').click(function () {
        single_index = 0;
        single_queue = [];
        single_pictures_imported = false;
        single_queue.push({action: 'product_ID_code', CODE: $('#pCode').val()});
        single_execute_queue(single_index);
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


    $('#CreateCategories').click(function () {
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
        dateFormat : 'yy-mm-dd'
    });

})
;