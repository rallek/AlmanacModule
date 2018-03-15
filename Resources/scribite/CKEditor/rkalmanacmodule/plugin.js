CKEDITOR.plugins.add('rkalmanacmodule', {
    requires: 'popup',
    init: function (editor) {
        editor.addCommand('insertRKAlmanacModule', {
            exec: function (editor) {
                RKAlmanacModuleFinderOpenPopup(editor, 'ckeditor');
            }
        });
        editor.ui.addButton('rkalmanacmodule', {
            label: 'Almanac',
            command: 'insertRKAlmanacModule',
            icon: this.path.replace('scribite/CKEditor/rkalmanacmodule', 'images') + 'admin.png'
        });
    }
});
