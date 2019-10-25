jQuery(document).ready(function ($) {

    var debug = true;
    var cMax = 1;

    var queue = new Array();

    var requests = new Array();

    var index = 0;

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
    var ProductImagesImportedLinked = false;

    function showObject(obj) {
        var result = "";
        for (var p in obj) {
            if( obj.hasOwnProperty(p) ) {
                result += p + " , " + obj[p] + "\n";
            }
        }
        return result;
    }

    function showObjectjQuery(obj) {
        var result = "";
        $.each(obj, function(k, v) {
            result += k + " , " + v + "\n";
        });
        return result;
    }

    var execute_queue = function (index) {
        var request = $.ajax({
            data: queue[index],
            type: "POST",
            url: ajaxurl,
            success: function (data) {
                var r = this.data;
                var ro = QueryStringToJSON('?' + r);
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
                if(action=='get_all_products') {
                    if ($.trim(data)) {
                        var products = $.parseJSON(data);
                        var c = 0;
                        $(products).each(function (index,product) {
                            _o('Name = ' +product.Name + ' | VariationIds = ' + product.VariationIds + ' | PictureIds = ' + product.PictureIds + ' | CategoryIds = ' + product.CategoryIds);
                            /*var v = product.VariationIds;
                            var vs = v.split(',');*/
                            $(product.VariationIds).each(function (index,variationID) {
                                queue.push({action:'create_product',productID:product.Id,variationID:variationID,bulletText:product.BulletText,productName:product.Name,categoryIDs:product.CategoryIds,pictureIDs:product.PictureIds});
                            });
                            if(debug) {
                                c++;
                                if (c >= cMax) {
                                    return false;
                                }
                            }
                        });
                    }
                }
                if(action=='get_product_images') {
                    if ($.trim(data)) {
                        var productsImageIDs = $.parseJSON(data[0]);
                        var c = 0;
                        $(productsImageIDs).each(function (index, id) {
                            _o(id);
                        });
                    }
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
                        if(!ProductsFinished) {
                            ProductsFinished = true;
                            queue = [];
                            requests = [];
                            index = 0;
                            queue.push({action: 'get_all_products'});
                            _o('<strong>Fetching all products from ePim - please be patient...</strong>')
                            execute_queue(index);
                        } else {
                            if(!ProductImagesImportedLinked) {
                                ProductImagesImportedLinked = true;
                                queue = [];
                                requests = [];
                                index = 0;
                                queue.push({action: 'get_product_images'});
                                _o('<strong>Importing Product Images...</strong>')
                                execute_queue(index);
                            } else {
                                finished = true;
                                _o('<strong>Queue Processed - Create and Update Finished OK :)</strong>');
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
                if(debug) {
                    c++;
                    if (c >= cMax) {
                        return false;
                    }
                }
            });
            /*_o('Processing Queue');*/
            execute_queue(index);
        });
        requests.push(thisRequest);

        /*queue.push({action:'sort_categories'});
        queue.push({action:'cat_image_link'});
        queue.push({action:'cat_image_link'});
        execute_queue(index);*/

    });

    //execute_queue(index); // go!

    /* var categoryPictures = new Array();
     var requests = new Array();

     function resetRequests() {
         $('#ePimResult').empty();
         categoryPictures = [];
         var numRequests = requests.length;
         for (var i = 0; i < numRequests; i++) {
             requests[i].abort();
         }
     }

     function _o(text) {
         $('#ePimResult').append(text);
     }

     function getPicture(picture) {
         _o('Getting Picture ' + picture + '<br>');

         t0 = performance.now();
         var theRequest = $.ajax({
             type: "POST",
             url: ajaxurl,
             data: {action: 'get_picture', ID: picture}
         }).done(function (data) {
             //var parsedData = $.parseJSON(data);
             t1 = performance.now();
             var p = (t1 - t0) / 1000;

             _o('<p>====================== Picture Data Received (request took ' + p.toFixed(2) + ' seconds) ====================</p>');
             _o('<pre>' + data + '</pre>');
         });
         requests.push(theRequest);
     }

     function checkCategories(categories) {
         $(categories).each(function (index, record) {
             _o('<p>Checking Category ' + record.Id + ' (' + record.Name + ')</p>');
             _o('ParentId = ' + record.ParentId + '<br>');
             var pictures = record.PictureIds;
             $(pictures).each(function (index, picture) {
                 setTimeout(100, function () {
                     getPicture(picture);
                 })
             });
         });
     }

     $('#CreateCategories').click(function () {
         resetRequests();
         _o('<strong>Requesting Category Update from ePim...</strong><br>');
         var categories;
         var t0 = performance.now();
         var thisRequest = $.ajax({
             type: "POST",
             url: ajaxurl,
             data: {action: 'get_all_categories'}
         }).done(function (data) {
             categories = $.parseJSON(data);
             var t1 = performance.now();
             var p = (t1 - t0) / 1000;
             _o('<p>Data received (request took ' + p.toFixed(2) + ' seconds):</p>');
             _o('<pre>' + data + '</pre>');
             _o('<strong>Processing ePim Data...</strong><br>');
             setTimeout(100, function () {
                 checkCategories(categories);
             });

         });
         requests.push(thisRequest);
     });*/

})
;