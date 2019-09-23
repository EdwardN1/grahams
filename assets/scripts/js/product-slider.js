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
        slidesToShow: 6,
        slidesToScroll: 2,
        infinite: true,
        dots: true,
        arrows: false

    });
});