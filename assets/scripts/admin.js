jQuery(document).ready(function ($) {

    var queue = new Array();

    var index = 0;

    function _o(text) {
        $('#ePimResult').append(text);
    }

    var execute_queue = function (index) {
        $.ajax({
            data: queue[index],
            type: "POST",
            url: ajaxurl,
            success: function (data) {
                index++;    // going to next queue entry

                // check if it exists
                if (queue[index] != undefined) {
                    _o('Request Completed: ' + this.data + '<br>');
                    execute_queue(index);
                }
            }

        }); // end of $.ajax( {...

    }; // end of execute_queue() {...


    $('#CreateCategories').click(function () {
        $.ajax({
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