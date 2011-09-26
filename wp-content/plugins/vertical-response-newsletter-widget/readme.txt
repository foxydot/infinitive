=== Vertical Response Newsletter Widget ===
Contributors: katzwebdesign
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=zackkatz%40gmail%2ecom&item_name=Vertical%20Response%20Widget&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: vertical response,verticalresponse, newsletter widget, email newsletter form, newsletter form, newsletter signup, email widget, email marketing, newsletter, form, signup
Requires at least: 2.5
Tested up to: 3.0.3
Stable tag: trunk

Add a Vertical Response signup form to your sidebar. Lots of configuration options. Now with custom colors and shortcode support!

== Description ==

<h3>Add a Vertical Response signup form to your sidebar without touching code.</h3> 

> <strong>Note: This great plugin requires a <a href="http://www.verticalresponse.com/landing/cj/g2/?sisearchengine=186&siproduct=CJ+%2D+100+Free&clearppc=1&AID=10683714&PID=3923826" rel="nofollow">Vertical Response account</a></strong>. Sign up for free today!

This widget includes many configuration options:

* __Custom widget title__ (if empty, shows no title)
* __Define your own Button Text__ (Subscribe, Submit, Add Me!, Go, etc.)
* __Show an intro paragraph__ and define your own wrapping tag (defaults to a paragraph tag). 
* __Choose from two styles:__
  1. Default Vertical Response style (gray)
  2. Choose your own colors
  3. No style (make your own using CSS)
* __Choose from three types of forms:__
  1. Full Name, Email, Subscribe button
  2. Email, Subscribe button
  3. Subscribe button only

== Screenshots ==

1. How the widget appears in the Widgets panel

== Installation ==

Follow the following steps to install this plugin.

1. Download plugin to the `/wp-content/plugins/` folder
1. Upload the plugin to your web host
1. Activate the plugin through the Plugins page (`wp-admin/plugins.php`)
1. Add the widget to your sidebar on the Widgets page (`wp-admin/widgets.php`)
   * For 2.7, the widgets page is here: 'Appearance > Widgets'
   * Otherwise, the widgets page is here: 'Design > Widgets'
   * Configure the widget and save the settings

You can also embed in your website by configuring above, then removing the widget from your sidebar. Then:

* Add `<?php widget_vr(); ?>` in your template code
* Write `[verticalresponse]` in your post's or page's content.

== Frequently Asked Questions ==

= Do I need to use Vertical Response to use this widget? =

Yes, this widget requires a Vertical Response account. <a href="http://www.verticalresponse.com/landing/cj/g2/?sisearchengine=186&siproduct=CJ+%2D+100+Free&clearppc=1&AID=10683714&PID=3923826" rel="nofollow" title="Try out VerticalResponse for free">Try out VerticalResponse for free</a>.

Looking for a good email marketing option to integrate into your website or blog? If you haven't tried VerticalResponse, you'll be impressed with their website's sleek administration and contact management capabilities. <a href="http://www.verticalresponse.com/landing/cj/g2/?sisearchengine=186&siproduct=CJ+%2D+100+Free&clearppc=1&AID=10683714&PID=3923826" rel="nofollow">Sign up for free today</a>

= How do I use the new `add_filters()` functionality? (Added 1.1) =
If you want to change some code in the widget, you can use the WordPress `add_filter()` function to achieve this.

You can add code to your theme's `functions.php` file that will modify the widget or shortcode output. Here's an example:
<pre>
function my_example_function($widget) { 
	// The $widget variable is the output of the widget
	// This will replace 'this word' with 'that word' in the widget output.
	$widget = str_replace('this word', 'that word', $widget);
	// Make sure to return the $widget variable, or it won't work!
	return $widget;
}
add_filter('vr_widget_form', 'my_example_function');
</pre>

= What is the license? =

This plugin is released under a GPL license.

== Changelog == 

= 1.5.1 = 
* Fixed `[verticalresponse]` shortcode bug where form would be shown at the beginning of content rather than embedded inside it (as reported in <a href="http://wordpress.org/support/topic/438919" rel="nofollow">issue #438919</a>)

= 1.5 = 
* Dropped a decimal place in the version numbers
* If you want to modify widget or shortcode output, there's now an `add_filters` method to do so.
* Improved widget controls configuration

= .1482 = 
* Fixed `. />` code validation errors on line 183 and 193. Thanks to [Doug Ng](http://design-ng.com) for pointing this out

= .1481 = 
* Added GPL notice

= .148 = 
* Added shortcode

= .147 = 
* Added color options for backgrounds (form, button), borders (form, button), text (main, labels, button)
* Updated versions

= .146 = 
* Fixed error on line 146
* Added give credit option

= .145 = 
* Minor update to fix fatal error, changing <? to <?php
* Added id's and classes to improve CSS customization capabilities

= .144 = 
* Removed white space at end of widget to possibly fix fatal error

= .143 = 
* Updated stripslashes() to better handle apostrophes in fields

= .142	= 
* Fixed tag closure order error. Thanks patrickrileydesign.com!

= .141	= 
* Updated Plugin URI link to go to widget's page

= .14 = 
* Fixed bug that showed the Vertical Response form instead of saving the widget options or removing the widget.

== Upgrade Notice ==

= 1.5.1 = 
* Fixed `[verticalresponse]` shortcode bug where form would be shown at the beginning of content rather than embedded inside it (as reported in <a href="http://wordpress.org/support/topic/438919" rel="nofollow">issue #438919</a>)

= 1.5 = 
* If you want to modify widget or shortcode output, there's now an `add_filters` method to do so.
* Improved widget controls configuration

= .1482 = 
* Fixed XHTML validation errors caused by lines 183 and 193. No major functionality improvements.
