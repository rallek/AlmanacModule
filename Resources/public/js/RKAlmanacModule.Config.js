'use strict';

function almaToggleShrinkSettings(fieldName) {
    var idSuffix = fieldName.replace('rkalmanacmodule_appsettings_', '');
    jQuery('#shrinkDetails' + idSuffix).toggleClass('hidden', !jQuery('#rkalmanacmodule_appsettings_enableShrinkingFor' + idSuffix).prop('checked'));
}

jQuery(document).ready(function () {
    jQuery('.shrink-enabler').each(function (index) {
        jQuery(this).bind('click keyup', function (event) {
            almaToggleShrinkSettings(jQuery(this).attr('id').replace('enableShrinkingFor', ''));
        });
        almaToggleShrinkSettings(jQuery(this).attr('id').replace('enableShrinkingFor', ''));
    });
});
