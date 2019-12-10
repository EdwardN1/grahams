jQuery(document).ready(function ($) {

    function setupWishList() {
        let json_wishes = Cookies.get('wishlist');
        $('#pmEmail').val(Cookies.get('eparams_a'));
        $('#pmSubject').val(Cookies.get('eparams_b'));
        let postID = $('.wishlist.add').data('post');
        if (json_wishes) {
            let wishes = JSON.parse(json_wishes);
            if (wishes.includes(postID)) {
                $('.wishlist.add').hide();
                $('.wishlist.remove').show();
                $('.email-form').show();
            } else {
                $('.wishlist.add').show();
                $('.wishlist.remove').hide();
                $('.email-form').show();
            }
            if (wishes.length < 1) {
                $('.wishlist.remove').hide();
                $('.wishlist.add').show();
                $('.email-form').hide();
            }
        } else {
            $('.wishlist.remove').hide();
            $('.wishlist.add').show();
            $('.email-form').hide();
        }
        jQuery.ajax({
            type: "post",
            url: mailing_ajax_object.ajax_url,
            data: {action: "get_wish_list"},
            success: function (response) {
                $('.wishlist .accordion-content .list').html(response);
            }
        })
    }

    function sendmail() {
        Cookies.set('eparams_a', jQuery('#pmEmail').val());
        Cookies.set('eparams_b', jQuery('#pmSubject').val());
        jQuery.ajax({
            type: "post",
            data: {action: 'send_SMTP_wishlist', security: mailing_ajax_object.security},
            url: mailing_ajax_object.ajax_url,
            success: function (response) {
                alert(response);
            }
        })
    }

    $(document).on("click",'.wlRemove', function (e) {
        e.preventDefault();
        let postID = $(this).data('post');
        let json_wishes = Cookies.get('wishlist');
        let wishes = [];
        let newWishes = [];
        if (json_wishes) {
            wishes = JSON.parse(json_wishes);
            wishes.forEach(function (wish,index) {
                if(wish!=postID) {
                    newWishes.push(wish);
                }
            });
            json_wishes = JSON.stringify(newWishes);
            Cookies.set('wishlist', json_wishes);
            setupWishList();
        }
    });


    setupWishList();
    $('.wishlist.add').click(function (e) {
        e.preventDefault();
        //window.console.log('Add Clicked');
        let postID = $(this).data('post');
        let json_wishes = Cookies.get('wishlist');
        let wishes = [];
        if (json_wishes) {
            wishes = JSON.parse(json_wishes);
            if (!wishes.includes(postID)) {
                wishes.push(postID);
            }
        }
        json_wishes = JSON.stringify(wishes);
        Cookies.set('wishlist', json_wishes);
        setupWishList();
    });
    $('.wishlist.remove').click(function (e) {
        e.preventDefault();
        let postID = $(this).data('post');
        let json_wishes = Cookies.get('wishlist');
        let wishes = [];
        if (json_wishes) {
            wishes = JSON.parse(json_wishes);
            if (wishes.includes(postID)) {
                let index = wishes.indexOf(postID);
                wishes.splice(index, 1);
                ;
            }
        }
        json_wishes = JSON.stringify(wishes);
        Cookies.set('wishlist', json_wishes);
        setupWishList();
    });

    $('#wlForm').submit(function (e) {
        e.preventDefault();
        var v = grecaptcha.getResponse();
        if(v.length == 0)
        {
            alert("You can't leave the reCaptcha Code empty");
        }
        else
        {
            Cookies.set('eparams_c', v);
            sendmail();
        }

    })

});