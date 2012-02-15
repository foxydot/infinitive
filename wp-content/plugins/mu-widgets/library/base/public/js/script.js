function v47v (data){
	this.data = jQuery.extend(v47v_data,data);
};

jQuery(document).ready(function() {
	v47v = new v47v({});
});

v47v.prototype.tinymce =  function() {
	tinymce.create('tinymce.plugins.v47v_tinymce', {init : function(ed, url) {
			jQuery.each(v47v.data.tinymce.cmds,function(idx,elm){
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
			jQuery.each(v47v.data.tinymce.buttons,function(idx,elm){ed.addButton(idx, elm);});
		}
	});
	tinymce.PluginManager.add('v47v_tinymce', tinymce.plugins.v47v_tinymce);
}
v47v.prototype.log = function(value) {
	if(this.data.dodebug==1)
	{
		console.log(value);
	}
}
v47v.prototype.json = function(url,data,success) {
	jQuery.ajax({
			type:'POST',
			url:url,
			data:data,
			success:success,
			dataType:'json'
	});
}
v47v.prototype.void = function(value,def_value) {
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
v47v.prototype.entitydecode = function(encodedStr) {
	return jQuery("<div/>").html(encodedStr).text();
}