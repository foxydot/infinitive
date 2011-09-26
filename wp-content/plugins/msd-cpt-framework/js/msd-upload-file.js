function setup_uploader(){
	var formfield;
	var ww = jQuery('#post_id_reference').attr('value');

	/* user clicks button on custom field, runs below code that opens new window */	
	jQuery('.uploadbtn').click(function() {
		formfield = jQuery(this).prev('.uploadfile');
		tb_show('','media-upload.php?post_id=' + ww + '&TB_iframe=true');
		return false;
	});

	// user inserts file into post. only run custom if user started process using the above process
	// window.send_to_editor(html) is how wp would normally handle the received data

	window.original_send_to_editor = window.send_to_editor;
	window.send_to_editor = function(html){
		if (formfield) {
			fileurl = jQuery(html).attr('href');
			jQuery(formfield).val(fileurl);

			tb_remove();
			formfield = '';
		} else {
			window.original_send_to_editor(html);
		}
	};
}

jQuery(document).ready(function($) {
	setup_uploader();
	jQuery('.docopy-docs').click(function(){
		setup_uploader();
	});
});