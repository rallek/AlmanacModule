var rkalmanacmodule = function(quill, options) {
    setTimeout(function() {
        var button;

        button = jQuery('button[value=rkalmanacmodule]');

        button
            .css('background', 'url(' + Zikula.Config.baseURL + Zikula.Config.baseURI + '/web/modules/rkalmanac/images/admin.png) no-repeat center center transparent')
            .css('background-size', '16px 16px')
            .attr('title', 'Almanac')
        ;

        button.click(function() {
            RKAlmanacModuleFinderOpenPopup(quill, 'quill');
        });
    }, 1000);
};
