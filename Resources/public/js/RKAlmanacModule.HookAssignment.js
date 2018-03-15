'use strict';

/**
 * Adds a hook assignment for a certain object.
 */
function rKAlmanacAttachHookObject(attachLink, entityId) {
    jQuery.ajax({
        method: 'POST',
        url: Routing.generate('rkalmanacmodule_ajax_attachhookobject'),
        data: {
            owner: attachLink.data('owner'),
            areaId: attachLink.data('area-id'),
            objectId: attachLink.data('object-id'),
            url: attachLink.data('url'),
            assignedEntity: attachLink.data('assigned-entity'),
            assignedId: entityId
        },
        success: function (data) {
            window.location.reload();
        }
    });
}

/**
 * Removes a hook assignment for a certain object.
 */
function rKAlmanacDetachHookObject() {
    jQuery.ajax({
        method: 'POST',
        url: Routing.generate('rkalmanacmodule_ajax_detachhookobject'),
        data: {
            id: jQuery(this).data('assignment-id')
        },
        success: function (data) {
            window.location.reload();
        }
    });
}

jQuery(document).ready(function () {
    jQuery('.detach-rkalmanacmodule-object')
        .click(rKAlmanacDetachHookObject)
        .removeClass('hidden');
});
