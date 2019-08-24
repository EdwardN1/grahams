jQuery(document).ready(function ($) {

    $('[data-waypoint]').each(function () {
        var $element = $(this);
        var wpOffset = $element.data('waypoint-offset');
        var wpDirection = $element.data('waypoint-direction');
        var wpClass = $element.data('waypoint-class');

        $element.waypoint(function (direction) {
            if(direction==wpDirection) {
                if(!$element.hasClass(wpClass)) {
                    $element.addClass(wpClass);
                }
            }
            this.destroy();
        }, {
            offset: wpOffset
        });
    });

    $('[data-slick-slider]').slick();

});