function icharts_tinymce() {
};
jQuery(document).ready(function() {
	icharts_tinymce = new icharts_tinymce();
	tinyMCEPopup.executeOnLoad('icharts_tinymce.init();');
});
icharts_tinymce.prototype.init = function() {
	jQuery('[name="form"]').change(this.insert);
	//jQuery('[name="url"]').val(tinyMCEPopup.getWindowArg('selected'));
}
icharts_tinymce.prototype.insert = function(e) {
	e.preventDefault();
	if (window.tinyMCE) {
		var form=jQuery('[name="form"]').val();
		if(form=='default')
		{
			form='';
		}
		else
		{
			form = ' '+form;
		}
		var text = '['+tinyMCEPopup.getWindowArg('slug')+form+']';
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent',
				false, text);
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	tinyMCEPopup.close();
}