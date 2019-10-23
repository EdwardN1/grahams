jQuery(document).ready(function ($) {

    var queue = new Array();

    var requests = new Array();

    var index = 0;

    function _o(text) {
        $('#ePimResult').prepend(text+'<br>');
    }

    function _or(text) {
        $('#ePimResult').prepend(text);
    }

    function resetQueue() {
        if(!finished) {
            _o('<strong>Aborting unfinished requests in queue!!!</strong>');
        }
        var numRequests = requests.length;
        for (var i = 0; i < numRequests; i++) {
            requests[i].abort();
        }
        requests = [];
        queue = [];
        $('#ePimResult').empty();
    }

    var finished = false;

    var execute_queue = function (index) {
        var request = $.ajax({
            data: queue[index],
            type: "POST",
            url: ajaxurl,
            success: function (data) {
                index++;    // going to next queue entry

                // check if it exists
                if (queue[index] != undefined) {
                    _o('Request Completed: ' + this.data);
                    execute_queue(index);
                } else {
                    finished = true;
                    _o('<strong>Queue Processed</strong>');
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
            $(categories).each(function (index, record) {
                _or('<p>Checking Category ' + record.Id + ' (' + record.Name + ')</p>');
                _o('ParentId = ' + record.ParentId );
                var pictures = record.PictureIds;
                _o('Adding Pictures to the queue');
                $(pictures).each(function (index, picture) {
                    queue.push({action: 'get_picture', ID: picture});
                });

            });
            _o('Processing Queue');
            execute_queue(index);
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