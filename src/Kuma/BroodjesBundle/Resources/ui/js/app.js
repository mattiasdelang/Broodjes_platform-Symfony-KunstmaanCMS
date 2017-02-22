var KumaBroodjesBundle = KumaBroodjesBundle || {};

KumaBroodjesBundle = (function($, window, undefined) {

    var init;

    init = function() {
        cargobay.videolink.init();        
        cargobay.scrollToTop.init();
    };

    return {
        init: init
    };

}(jQuery, window));

$(function() {
    KumaBroodjesBundle.init();
});
