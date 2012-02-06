function v46v (data){
	this.data = jQuery.extend(v46v_data,data);
};

jQuery(document).ready(function() {
	v46v = new v46v({});
});

v46v.prototype.tinymce =  function() {
	tinymce.create('tinymce.plugins.v46v_tinymce', {init : function(ed, url) {
			jQuery.each(v46v.data.tinymce.cmds,function(idx,elm){
				ed.addCommand(idx, function() {
					if(typeof(elm.file) == 'undefined')
					{
						switch(idx)
						{
							case 'blackout':
							case 'censor':
							case 'spoiler':
								ed.selection.setContent('['+idx+']' + ed.selection.getContent() + '[/'+idx+']');
							break;
							default:
								ed.selection.setContent('['+idx+' ' + ed.selection.getContent() + ']');
						}
					}
					else
					{
						if(elm.always || ed.selection.getContent() == "")
						{
							ed.windowManager.open( elm,{slug:idx,selected:ed.selection.getContent()} );
						}
						else
						{
							ed.selection.setContent('['+idx+' ' + ed.selection.getContent() + ']');
						}
					}
				});
			});
			jQuery.each(v46v.data.tinymce.buttons,function(idx,elm){ed.addButton(idx, elm);});
		}
	});
	tinymce.PluginManager.add('v46v_tinymce', tinymce.plugins.v46v_tinymce);
}
v46v.prototype.log = function(value) {
	if(this.data.dodebug==1)
	{
		console.log(value);
	}
}
v46v.prototype.json = function(url,data,success) {
	jQuery.ajax({
			type:'POST',
			url:url,
			data:data,
			success:success,
			dataType:'json'
	});
}
v46v.prototype.void = function(value,def_value) {
	var retval =  '';
	if(typeof(def_value) == 'undefined')
	{
		retval =  typeof(value) == 'undefined';
	}
	else
	{
		if(typeof(value) == 'undefined')
		{
			retval = def_value;
		}
		else
		{
			retval = value;
		}
	}
	return retval;
}
v46v.prototype.entitydecode = function(encodedStr) {
	return jQuery("<div/>").html(encodedStr).text();
}