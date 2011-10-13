String.prototype.endsWith = function (needle) {
	if ((this.length == 0) || (needle.length == 0)) {
		return false;
	}
	pos = this.lastIndexOf(needle);
	if (pos == -1) {
		return false;
	}
	if (pos == (this.length - needle.length)) {
		return true;
	}

	return false;
}

jQuery(document).ready(function($){

	$('select.super-widget-blog-select').live('change', function(e) {
		var target = $(e.target);
		widgetDiv = target.parents('div.widget').first();
		$('select.super-widget-object-type-select', widgetDiv).val('');
	});

	$('select.super-widget-select').live('change', function(e) {
		var target = $(e.target);
		widgetDiv = target.parents('div.widget').first();
		super_widget_handle_ajax(widgetDiv);
	});

	$('div.posttypediv').live('click', function(e) {
		var target = $(e.target);
		widgetDiv = target.parents('div.widget').first();

		if ( target.hasClass('nav-tab-link') ) {

			panelId = /#(.*)$/.exec(e.target.href);

			if ( panelId && panelId[1] )
				panelId = panelId[1];
			else
				return false;

			tabName = 'most-recent';

			if (panelId.endsWith('-all')) tabName = 'all';
			else if (panelId.endsWith('-search')) tabName = 'search';

			widgetDiv.find('input.hiddentab').first().val(tabName);

			wrapper = target.parents('.posttypediv').first();

			$('.tabs-panel-active', wrapper).removeClass('tabs-panel-active').addClass('tabs-panel-inactive');
			$('#' + panelId, wrapper).removeClass('tabs-panel-inactive').addClass('tabs-panel-active');

			$('.tabs', wrapper).removeClass('tabs');
			target.parent().addClass('tabs');

			return false;
		} else if ( target.hasClass('page-numbers') ) {
			targetPage = target.text();
			currentPage = $('input.hiddenpaged', widgetDiv).first();

			if (isNaN(parseInt(targetPage))) {
				if ('«' == targetPage) {
					currentPage.val(parseInt(currentPage.val()) - 1);
				} else if ('»' == targetPage) {
					currentPage.val(parseInt(currentPage.val()) + 1);
				}
			} else {
				currentPage.val(targetPage);
			}

			super_widget_handle_ajax(widgetDiv);

			return false;

		} else if ( target.hasClass('quick-search-submit') ) {
			if (widgetDiv.find('input.quick-search').first().val() != '') {
				super_widget_handle_ajax(widgetDiv);
			}
			return false;
		}
	});
});

function super_widget_handle_ajax(widgetDiv) {
	var widgetDivId = widgetDiv.attr('id');
	var widgetInputBase = '#' + jQuery('input.widget_id_base', widgetDiv).first().val();
	var widgetNumber = jQuery('input.widget_number', widgetDiv).first().val();
	var currentTab = jQuery(widgetInputBase + 'tab').val();
	var currentPage = jQuery('input.hiddenpaged', widgetDiv).first().val();

	jQuery('.ajax-feedback', widgetDiv).css('visibility', 'visible');

	var theArgs = {
		action: jQuery('input.widget_class:hidden', widgetDiv).first().val() + '-get-metabox',
		widget: widgetDivId,
		number: widgetNumber,
		blog_id: jQuery(widgetInputBase + 'blog_id').val(),
		object_type: jQuery(widgetInputBase + 'object_type').val(),
		tab: currentTab,
		paged: currentPage,
		object_to_use: jQuery('input[type=radio][name$="[object_to_use]['+currentTab+']"]:checked', widgetDiv).first().val(),
		title_override: jQuery(widgetInputBase + 'title_override').val(),
		excerpt_override: jQuery(widgetInputBase + 'excerpt_override').val(),
		searched: ('search' == currentTab) ? jQuery('input.quick-search', widgetDiv).first().val() : ''
	};

	// From Nav Menu JS
	jQuery.post(
		ajaxurl,
		theArgs,
		function( r ) {
			jQuery('.ajax-feedback').css('visibility', 'hidden');
			if ( r && r.length > 2 ) {
				jQuery('div.widget-content', widgetDiv).html(r);
			}
		}
	);
}