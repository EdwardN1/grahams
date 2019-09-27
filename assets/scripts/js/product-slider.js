jQuery(document).ready(function ($) {
    $('#product-large').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: false,
        asNavFor: '#product-small'
    });
    $('#product-small').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '#product-large',
        dots: false,
        /*centerMode: true,*/
        focusOnSelect: true
    });
    $('.product-carousel .slider-container').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        fade: false,
        dots: true,
        arrows: false,
        mobileFirst: true,
        fade: false,
        responsive: [
            {
                breakpoint: 640,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 860,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 1280,
                settings: {
                    slidesToShow: 5,
                    slidesToScroll: 2,
                }
            }, {
                breakpoint: 1440,
                settings: {
                    slidesToShow: 6,
                    slidesToScroll: 2,
                }
            }

        ],
        /* responsive: [{
             breakpoint: 1024,
             settings: {
                 slidesToShow: 4,
                 slidesToScroll: 1,
                 infinite: true,
                 fade: false,
                 dots: true,
                 arrows: false
             }
         }],*/
        /*responsive: [{

            breakpoint: 1440,
            settings: {
                slidesToShow: 6,
                slidesToScroll: 2,
                infinite: true,
                fade: false,
                dots: true,
                arrows: false
            },

            breakpoint: 1280,
            settings: {
                slidesToShow: 5,
                slidesToScroll: 2,
                infinite: true,
                fade: false,
                dots: true,
                arrows: false
            },

            breakpoint: 1024,
            settings: {
                slidesToShow: 4,
                slidesToScroll: 1,
                infinite: true,
                fade: false,
                dots: true,
                arrows: false
            },

            breakpoint: 860,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 1,
                infinite: true,
                fade: false,
                dots: true,
                arrows: false
            },

            breakpoint: 640,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
                infinite: true,
                fade: false,
                dots: true,
                arrows: false
            },

            breakpoint: 300,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                infinite: true,
                fade: false,
                dots: true,
                arrows: false
            },



        }]*/
    });
});