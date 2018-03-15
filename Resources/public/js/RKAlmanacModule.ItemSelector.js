'use strict';

var rKAlmanacModule = {};

rKAlmanacModule.itemSelector = {};
rKAlmanacModule.itemSelector.items = {};
rKAlmanacModule.itemSelector.baseId = 0;
rKAlmanacModule.itemSelector.selectedId = 0;

rKAlmanacModule.itemSelector.onLoad = function (baseId, selectedId) {
    rKAlmanacModule.itemSelector.baseId = baseId;
    rKAlmanacModule.itemSelector.selectedId = selectedId;

    // required as a changed object type requires a new instance of the item selector plugin
    jQuery('#rKAlmanacModuleObjectType').change(rKAlmanacModule.itemSelector.onParamChanged);

    jQuery('#' + baseId + '_catidMain').change(rKAlmanacModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + '_catidsMain').change(rKAlmanacModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'Id').change(rKAlmanacModule.itemSelector.onItemChanged);
    jQuery('#' + baseId + 'Sort').change(rKAlmanacModule.itemSelector.onParamChanged);
    jQuery('#' + baseId + 'SortDir').change(rKAlmanacModule.itemSelector.onParamChanged);
    jQuery('#rKAlmanacModuleSearchGo').click(rKAlmanacModule.itemSelector.onParamChanged);
    jQuery('#rKAlmanacModuleSearchGo').keypress(rKAlmanacModule.itemSelector.onParamChanged);

    rKAlmanacModule.itemSelector.getItemList();
};

rKAlmanacModule.itemSelector.onParamChanged = function () {
    jQuery('#ajaxIndicator').removeClass('hidden');

    rKAlmanacModule.itemSelector.getItemList();
};

rKAlmanacModule.itemSelector.getItemList = function () {
    var baseId;
    var params;

    baseId = rKAlmanacModule.itemSelector.baseId;
    params = {
        ot: baseId,
        sort: jQuery('#' + baseId + 'Sort').val(),
        sortdir: jQuery('#' + baseId + 'SortDir').val(),
        q: jQuery('#' + baseId + 'SearchTerm').val()
    }
    if (jQuery('#' + baseId + '_catidMain').length > 0) {
        params[catidMain] = jQuery('#' + baseId + '_catidMain').val();
    } else if (jQuery('#' + baseId + '_catidsMain').length > 0) {
        params[catidsMain] = jQuery('#' + baseId + '_catidsMain').val();
    }

    jQuery.getJSON(Routing.generate('rkalmanacmodule_ajax_getitemlistfinder'), params, function (data) {
        var baseId;

        baseId = rKAlmanacModule.itemSelector.baseId;
        rKAlmanacModule.itemSelector.items[baseId] = data;
        jQuery('#ajaxIndicator').addClass('hidden');
        rKAlmanacModule.itemSelector.updateItemDropdownEntries();
        rKAlmanacModule.itemSelector.updatePreview();
    });
};

rKAlmanacModule.itemSelector.updateItemDropdownEntries = function () {
    var baseId, itemSelector, items, i, item;

    baseId = rKAlmanacModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id');
    itemSelector.length = 0;

    items = rKAlmanacModule.itemSelector.items[baseId];
    for (i = 0; i < items.length; ++i) {
        item = items[i];
        itemSelector.get(0).options[i] = new Option(item.title, item.id, false);
    }

    if (rKAlmanacModule.itemSelector.selectedId > 0) {
        jQuery('#' + baseId + 'Id').val(rKAlmanacModule.itemSelector.selectedId);
    }
};

rKAlmanacModule.itemSelector.updatePreview = function () {
    var baseId, items, selectedElement, i;

    baseId = rKAlmanacModule.itemSelector.baseId;
    items = rKAlmanacModule.itemSelector.items[baseId];

    jQuery('#' + baseId + 'PreviewContainer').addClass('hidden');

    if (items.length === 0) {
        return;
    }

    selectedElement = items[0];
    if (rKAlmanacModule.itemSelector.selectedId > 0) {
        for (var i = 0; i < items.length; ++i) {
            if (items[i].id == rKAlmanacModule.itemSelector.selectedId) {
                selectedElement = items[i];
                break;
            }
        }
    }

    if (null !== selectedElement) {
        jQuery('#' + baseId + 'PreviewContainer')
            .html(window.atob(selectedElement.previewInfo))
            .removeClass('hidden');
        rKAlmanacInitImageViewer();
    }
};

rKAlmanacModule.itemSelector.onItemChanged = function () {
    var baseId, itemSelector, preview;

    baseId = rKAlmanacModule.itemSelector.baseId;
    itemSelector = jQuery('#' + baseId + 'Id').get(0);
    preview = window.atob(rKAlmanacModule.itemSelector.items[baseId][itemSelector.selectedIndex].previewInfo);

    jQuery('#' + baseId + 'PreviewContainer').html(preview);
    rKAlmanacModule.itemSelector.selectedId = jQuery('#' + baseId + 'Id').val();
    rKAlmanacInitImageViewer();
};

jQuery(document).ready(function () {
    var infoElem;

    infoElem = jQuery('#itemSelectorInfo');
    if (infoElem.length == 0) {
        return;
    }

    rKAlmanacModule.itemSelector.onLoad(infoElem.data('base-id'), infoElem.data('selected-id'));
});
