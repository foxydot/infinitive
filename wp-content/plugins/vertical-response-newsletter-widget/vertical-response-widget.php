<?php
/*
Plugin Name: Vertical Response Widget
Plugin URI: http://www.seodenver.com/vertical-response-widget/
Description: Adds a Vertical Response signup form to your sidebar without touching code.
Author: Katz Web Services, Inc.
Version: 1.5.1
Author URI: http://www.katzwebservices.com
*/

/*
Copyright 2010 Katz Web Services, Inc.  (email: info@katzwebservices.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

global $kwd_vr_version;

$kwd_vr_version = 1.5;
if(!function_exists('in_multi_array')) {
function in_multi_array($needle, $haystack)
 {
  foreach($haystack as $pos => $val)
  {
   if (is_array($val))
   {
    if (in_multi_array($needle, $val))
     return 1;
   } else
    if ($val == $needle)
     return 1;
  }
 }
}
if(isset($_REQUEST['kwd_check_vr'])) {
		$kwd_vr_version;
		$options = $newoptions = get_option('widget_vr_options' );
		$sidebars = get_option('sidebars_widgets' );
		
		// Check whether it's active in any sidebar
		if(in_multi_array('vertical-response', $sidebars)) {
			$active = '1';
		} else {
			$active = '0';
		}
		
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$link = htmlspecialchars($options['credit']);
		if($link == 'no') { $link = false; } else { $link = true;}
		if(!$link) {
			$link = '0';
		} else {
			$link = '1';
		}
		$since = $options['since'];
		echo $active.','.$link.','.$kwd_vr_version.', '.$since;
		die();
}

function vr_widget_init() 
{
  global $kwd_vr_version;
  
  if ( !function_exists('register_sidebar_widget') )
    return;

function vr_default_string($saved,$default)
  {
    if ( empty($saved) )
        return __($default,'cc');
    else
        return $saved;
  }
  
  function kwd_process_color($colors, $default) {
  	if(isset($colors) && !empty($colors)) {
			if(substr($colors, 0, 1) == '#') {
				$color = strtolower(substr($colors, 1));
			} else {
				$color = $colors;
			}
		} else {
			$color = $default;
		}
  		return $color;
  }
  
// Vertical Response Plugin WIDGET
	function widget_vr($args = false, $echo = true) {
		global $kwd_vr_version;
		$output = '';
		if($args) { extract($args);}
		$options = get_option('widget_vr');
		$title = vr_default_string(htmlspecialchars(stripslashes($options['title'])), 'Sign up for our Newsletter');		
		
		if(empty($options['showname']) || !isset($options['showname'])) { $options['showname'] = 'full';};
		$kwd_bg_color = kwd_process_color($options['bg_color'], 'dddddd');
		$kwd_border_color = kwd_process_color($options['border_color'], '000000');
		$kwd_font_color = kwd_process_color($options['font_color'], '333333');
		$kwd_label_color = kwd_process_color($options['label_color'], '000000');
		$kwd_button_bg_color = kwd_process_color($options['button_bg_color'], '333333');
		$kwd_button_border_color = kwd_process_color($options['button_border_color'], '999999');
		$kwd_button_font_color = kwd_process_color($options['button_font_color'], '000000');
		$kwd_border_width = vr_default_string($options['border_width'], '1').'px';
		$output .= $before_widget;
			
		$title = vr_default_string(htmlspecialchars(stripslashes($options['title'])), '');
		if($title) {
			$output .=  $before_title."\n".$title."\n".$after_title;
		}
		
			$output .=  '
<form method="post" action="http://oi.vresp.com?fid='.$options['code'].'" target="vr_optin_popup" onsubmit="window.open( \'http://www.verticalresponse.com\', \'vr_optin_popup\', \'scrollbars=yes,width=600,height=450\' ); return true;" id="vr_form">';
	$style= false; $required = false;
	if($options['required'] != 'no') { $required = true;}
	$output .=  ' <div class="vr_wrapper"';
	if($options['defaultstyle'] != 'no') { $style= true;
	 $output .=  ' style="font-family: verdana; font-size: 11px; width: 160px; padding: 10px; border: '.$kwd_border_width.' solid #'.$kwd_border_color.'; background: #'.$kwd_bg_color.';"';
	}
	$output .=  '>';
	if($options['preface'] != '') {
		$output .=  '<'.vr_default_string($options['wrap'],'p');
		if($style) { $output .=  ' style="padding:0 0 0.5em 0; margin:0; color: #'.$kwd_font_color.'; line-height:1.2;"'; } $output .=  ' class="vr_preface">'.htmlspecialchars(stripslashes($options['preface'])).'</'.vr_default_string($options['wrap'],'p').'>';
	}
$output .=  '
<fieldset'; 
	if($style) { $output .=  ' style="border:none;"'; }
	$output .=  '>';

// @todo find out if people want the legend 
// if you want <legend>,  uncomment below
/* if(htmlspecialchars(stripslashes($options['legend'])) != '') { $output .=  '<legend>'.htmlspecialchars(stripslashes($options['legend'])).'</legend>'; };  */

if($options['showname'] == 'full' ||$options['showname'] == 'email') { 
$output .=  '
<label for="email_address"';
if($style) { $output .=  ' style="color: #'.$kwd_label_color.'; clear:both; width:100%; float:left;"';}
$output .=  ' id="email_address_label">Email:';
if($required) { $output .=  ' <em id="vr_email_required"'; if($style) { $output .=  ' style="font-style:normal;"';} $output .=  '><span'; if($style) { $output .=  ' style="color:red;"';} else { $output .=  ' class="red"';} $output .=  '>*</span> Required </em>';}
$output .= '
</label> 
<input type="text" id="email_address" name="email_address" size="20" value="Enter your email address"  onfocus="if (this.value == \'Enter your email address\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'Enter your email address\';}"';
	if($style) { 
		$output .=  ' style="margin-top: 5px; margin-bottom: 5px; color:#666; border: 1px solid #999; padding: 3px;"';
	}
$output .=  '/>';

if($style) { $output .=  '<div style="width:100%;"></div>';}
}
if($options['showname'] == 'full') { 
	$output .=  '
	<label for="first_name"';
	if($style) { $output .=  ' style="color: #'.$kwd_label_color.';"';}
	$output .=  ' id="first_name_label">First Name:</label> 
	<input type="text" id="first_name" name="first_name" size="15" value=""';
		if($style) { 
			$output .=  ' style="margin-top: 5px; margin-bottom: 5px; border: 1px solid #999; padding: 3px;"';
		} 
	$output .=  ' />';
	if($style) { $output .=  '<div style="width:100%;"></div>';}
	$output .=  '
	<label for="last_name"';
	if($style) { $output .=  ' style="color: #'.$kwd_label_color.';"';}
	$output .=  ' id="last_name_label">Last Name:</label> 
	<input type="text" id="last_name" name="last_name" size="15" value=""';
		if($style) { 
			$output .=  ' style="margin-top: 5px; margin-bottom: 5px; border: 1px solid #999; padding: 3px;"';
		} 
	$output .=  ' />';

}
$output .=  '<input type="hidden" name="vr_widget_version" value="'.$kwd_vr_version.'" />';
$output .=  '<input type="submit" value="'.vr_default_string(htmlspecialchars(stripslashes_deep($options['button'])), 'Subscribe').'"';
if($style) { 
 $output .=  ' style="margin-top: 5px; border: 1px solid #'.$kwd_button_border_color.'; background-color:#'.$kwd_button_bg_color.'; color:#'.$kwd_button_font_color.'; padding: 3px;"';
}
$output .= ' id="vr_submit" />';

if($options['show_vr_code'] == 'yes' || $options['show_vr_code'] == '') {
	if($style) { $output .=  '<br />'; }
	$output .=  '<span'; 
	if($style) { $output .=  ' style="color: #'.$kwd_font_color.'; line-height:1.1; display:block; padding-top:.5em;"'; } 
	$output .=  ' class="vr_link vrLink"><a href="http://snurl.com/verticalresponse" title="Email Marketing by VerticalResponse">Email Marketing</a> by VerticalResponse</span>';
}

$output .=  '
</fieldset>
</div>
</form>'."\n";
			$output .=  $after_widget;
		
		$output = apply_filters('vr_widget_form', $output);
		if($echo) {
			echo $output;
		} else {
			return $output;
		}
		// This simply tells the widget author where the widget has been installed.
		if(!function_exists('kwd_rss_output')){
		function kwd_rss_output($rss = '', $default = '') {
				require_once(ABSPATH . WPINC . '/rss.php');
				if ( !$rss = fetch_rss($rss) )
					return;
				
				$items = 1;
				if ( is_array( $rss->items ) && !empty( $rss->items ) ) {
					$rss->items = array_slice($rss->items, 0, $items);
					foreach ($rss->items as $item ) {
						if ( isset( $item['description'] ) && is_string( $item['description'] ) )
							$summary = $item['description'];
						$desc = str_replace(array("\n", "\r"), ' ', $summary);
						$summary = '';
						return $desc;
					}
				} else {
					return $default;
				}
		} // end kwd_rss_output
		}
		if(!function_exists('get_vr_attr')) {
		function get_vr_attr() { 
				global $post, $kwd_vr_version;// prevents calling before <HTML>
				if($post && !is_admin()) {
					$site = 'http://www.katzwebservices.com/development/attribution.php?site='.htmlentities(substr(get_bloginfo('url'), 7)).'&from=vr_widget&version='.$kwd_vr_version;
					$output = kwd_rss_output($site, $default);
					return $output;
				}
		}
		}
		if(!function_exists('vr_attr')) {
			function vr_attr() { 
				global $post, $kwd_vr_version; // prevents calling before <HTML>
				echo get_vr_attr();
			}
		}
	
	// If you have chosen to show the attribution link, add the link
	if($options['credit'] != 'no') {
		if(function_exists('add_action') && function_exists('vr_attr')) { add_action('wp_footer', 'vr_attr'); }
	} else {
		if(function_exists('add_action') && function_exists('vr_attr')) { add_action('wp_footer', 'get_vr_attr'); }
	} // End Credit
			
}
  
// VR Plugin OPTIONS
	function widget_vr_options() {
		global $kwd_vr_version;
		$options = get_option('widget_vr');
		if (!is_array($options)) {
			$options = array('title' => __('Vertical Response', 'vertical-response'), 'code' => 'ab1c2def3g', 'button' => 'Subscribe', 'wrap' => 'p', 'showname' => 'full', 'show_vr_code' => 'yes', 'credit' => 'yes', 'border_color'=>'#999999', 'bg_color'=>'#dddddd', 'font_color'=>'black', 'label_color'=>'#333333', 'button_border_color'=>'#999999','button_bg_color'=>'#c0c0c0','button_font_color'=>'#333333','border_width'=>'1');
			
		}
		if ($_POST['vr_form_submit']) {
			$options['title']=strip_tags($_POST["vr_form_title"]);
			$options['code']=strip_tags($_POST["vr_form_code"]);
		//	$options['legend']=strip_tags(stripslashes($_POST["vr_form_legend"]));
			$options['showlegend']=strip_tags($_POST["vr_display_legend"]);
			$options['preface']=strip_tags($_POST["vr_form_preface"]);
			$options['button']=strip_tags($_POST["vr_form_button"]);
			$options['wrap']=strip_tags($_POST["vr_form_wrap"]);
			$options['defaultstyle'] = strip_tags($_POST["vr_defaultstyle"]);
			$options['showname'] = strip_tags($_POST["vr_showname"]);
			$options['required'] = strip_tags($_POST["vr_required"]);
			$options['show_vr_code'] = strip_tags($_POST["vr_show_vr_code"]);
			$options['credit'] = strip_tags(stripslashes($_POST["vr_credit"]));
			$options['border_color'] = strip_tags(stripslashes($_POST["vr_border_color"]));
			$options['bg_color'] = strip_tags(stripslashes($_POST["vr_bg_color"]));
			$options['font_color'] = strip_tags(stripslashes($_POST["vr_font_color"]));
			$options['label_color'] = strip_tags(stripslashes($_POST["vr_label_color"]));
			$options['button_bg_color'] = strip_tags(stripslashes($_POST["vr_button_bg_color"]));
			$options['button_font_color'] = strip_tags(stripslashes($_POST["vr_button_font_color"]));
			$options['button_border_color'] = strip_tags(stripslashes($_POST["vr_button_border_color"]));
			$options['border_width'] = strip_tags(stripslashes($_POST["vr_border_width"]));
			$options['since'] = strip_tags(stripslashes($_POST["vr_since"]));
			update_option('widget_vr', $options);
		}
		if(isset($options['since'])) { 
		?> 
		<input type="hidden" name="vr_since" id="vr_since" value="<?php echo htmlspecialchars(stripslashes($options['since'])); ?>" />
		<?php } else { ?>
		<input type="hidden" name="vr_since" id="vr_since" value="<?php echo time('d M Y'); ?>" />
		<?php } ?>
		<h3>Required Settings</h3>
		<p> <strong>Don't have Vertical Response account?</strong> An account is required to use this plugin. <a href="http://bit.ly/vertical-response">Set up a free trial</a> now</strong>.</p>
		<div><p>
			 <strong>If you have an account:</strong></p>
			 <ul style="list-style:disc outside; margin-left:2em;">
			 	<li>Get your code here: <a href="https://app.verticalresponse.com/app/optin_form/list" target="_blank">Lists &gt; Opt-In Forms</a> and create a form.</li>
			 	<li>Go to the "Publish!" tab</li>
			 	<li>Find the Form ID Code can be found by looking for <code>&lt;form method=&quot;post&quot; action=&quot;http://oi.vresp.com?fid=<strong>ab1c2def3g</strong>&quot;</code>&hellip;, where <code><strong>ab1c2def3g</strong></code> is the code you want to enter below.</li>
			 	<li>Enter the code below</li>
			 </ul>
		
			<p><label for="vr_form_code">
			<strong> <?php _e('Unique Form ID Code:'); ?></strong>
			<br /><small><em>This is required for the widget to work.</em></small>
			 <input style="width: 100%;" id="vr_form_code" name="vr_form_code" type="text" value="<?php echo $options['code']; ?>" /></label>
		</p>
		</div>
		<hr />
		<h3>Text Settings</h3>
		 <p>
			 <label for="vr_form_title">
			  <strong><?php _e('Title:'); ?></strong>
			</label>
			  <input style="width: 100%; margin-bottom:1em;" id="vr_form_title" name="vr_form_title" type="text" value="<?php echo htmlspecialchars(stripslashes($options['title'])); ?>" />
			  
			 <label for="vr_form_preface">
			  <strong><?php _e('Text:'); ?></strong>
			  <br />Displayed below the Title
			</label><br />
			  <textarea style="width: 100%; margin-bottom:1em;" id="vr_form_preface" name="vr_form_preface" rows="5"><?php echo htmlspecialchars(stripslashes($options['preface'])); ?></textarea>
			 
			 <label for="vr_form_wrap">
			  <strong><?php _e('Wrap Text in:'); ?></strong><br />
			  By default, the text will be wrapped in a paragraph (<code>&lt;p&gt;</code>). <em>Note: Do not include brackets</em>.
			</label>
			  <input style="width: 100%; margin-bottom:1em;" id="vr_form_wrap" name="vr_form_wrap" type="text" value="<?php echo htmlspecialchars(stripslashes($options['wrap'])); ?>" /> 

<?php 
// if you want <legend>,  uncomment below
/*			
 <label for="vr_form_legend">
			  <strong><?php _e('Form Name'); ?></strong><br />
			  Leave empty to hide. Otherwise, it will be a form <code>&lt;legend&gt;</code>
			</label>
			  <input style="width: 100%; margin-bottom:1em;" id="vr_form_legend" name="vr_form_legend" type="text" value="<?php echo htmlspecialchars(stripslashes($options['legend'])); ?>" />			 
*/ ?>
			 
			<label for="vr_form_button">
			  <strong><?php _e('Submit Button Text:'); ?></strong>
			</label>
			  <input style="width: 100%; margin-bottom:1em;" id="vr_form_button" name="vr_form_button" type="text" value="<?php echo htmlspecialchars(stripslashes($options['button'])); ?>" />
			<hr />  
			<h3>Style Settings</h3>
			  <strong><?php _e('Show Widget Style:'); ?></strong><br />
			  The default style is a gray box. You can customize the colors using the options below (<em>Recommended for basic users</em>), or if you want to customize using your own external CSS, choose <code>No</code>.<br />
			
			<label for="vr_style_yes">Yes
			</label>
			  <input type="radio" id="vr_defaultstyle_yes" name="vr_defaultstyle" value="yes" <?php if(htmlspecialchars(stripslashes($options['defaultstyle'])) == 'yes' || htmlspecialchars(stripslashes($options['defaultstyle'])) == '') { echo 'checked="checked"';}; ?> />
			<label for="vr_style_no">No
			</label>
			  <input type="radio" id="vr_style_no" name="vr_defaultstyle" value="no" <?php if(htmlspecialchars(stripslashes($options['defaultstyle'])) == 'no') { echo 'checked="checked"';}; ?> />
			<br />
		<br />
			<fieldset>
			<legend><strong style="font-size:110%">Form Style &amp; Colors</strong></legend>
			If you know the <a href="http://en.wikipedia.org/wiki/List_of_colors" target="_blank" title="Go to a Wikipedia article with a list of colors. Opens in new window.">HEX value</a> or <a href="http://en.wikipedia.org/wiki/X11_color_names#Color_names_identical_between_X11_and_HTML.2FCSS" title="Go to a Wikipedia article with a list of X11 colors. Opens in new window.">X11 value</a> for the colors you want your widget to be, enter them below. Ex: <code>#3a3a3a</code>, <code>F4C2C2</code>, <code>blue</code> or <code>darkblue</code>.
			<br />
			<label for="vr_bg_color"><strong><?php _e('Background Color:'); ?></strong><br />
			  Change the widget's background color.
			</label>
			  <input style="width: 100%; margin-bottom:1em;" id="vr_bg_color" name="vr_bg_color" type="text" value="<?php echo htmlspecialchars(stripslashes($options['bg_color'])); ?>" />
			   <br /> 
			 <label for="vr_border_color"><strong><?php _e('Border Color:'); ?></strong><br />
			  Change the widget's border color.
			</label>
			  <input style="width: 100%; margin-bottom:1em;" id="vr_border_color" name="vr_border_color" type="text" value="<?php echo htmlspecialchars(stripslashes($options['border_color'])); ?>" />
			  			<br />
			
			<label for="vr_border_width">Widget Border Width</label>
				<div class="input"><select id="vr_border_width" name="vr_border_width">
				  <option value="0">No Border</option>
				  <option value="1" selected="selected">1 px</option>
				  <option value="2">2 px</option>
				  <option value="3">3 px</option>
				  <option value="4">4 px</option>
				  <option value="5">5 px</option>
				  <option value="6">6 px</option>
				  <option value="7">7 px</option>
				  <option value="8">8 px</option>
				  <option value="9">9 px</option>
				  <option value="10">10 px</option>
				  <option value="11">11 px</option>
				  <option value="12">12 px</option>
				  <option value="13">13 px</option>
				  <option value="14">14 px</option>
				  <option value="15">15 px</option>
				  <option value="16">16 px</option>
				  <option value="17">17 px</option>
				  <option value="18">18 px</option>
				  <option value="19">19 px</option>
				  <option value="20">20 px</option>
				</select></div>
			  <br /> 
			 <label for="vr_font_color"><strong><?php _e('Text Color:'); ?></strong><br />
			  Change the color of widget text.
			</label>
			  <input style="width: 100%; margin-bottom:1em;" id="vr_font_color" name="vr_font_color" type="text" value="<?php echo htmlspecialchars(stripslashes($options['font_color'])); ?>" />
			
			  <br /> 
			 <label for="vr_label_color"><strong><?php _e('Form Label Color:'); ?></strong><br />
			  Change the color of widget labels (next to input fields).
			</label>
			  <input style="width: 100%; margin-bottom:1em;" id="vr_label_color" name="vr_label_color" type="text" value="<?php echo htmlspecialchars(stripslashes($options['label_color'])); ?>" />
			  
			  <br /> 
			 <label for="vr_button_bg_color"><strong><?php _e('Button Color:'); ?></strong><br />
			 Change the color of the button's background.
			</label>
			  <input style="width: 100%; margin-bottom:1em;" id="vr_button_bg_color" name="vr_button_bg_color" type="text" value="<?php echo htmlspecialchars(stripslashes($options['button_bg_color'])); ?>" />
			  
			  <br /> 
			 <label for="vr_button_font_color"><strong><?php _e('Button Text Color:'); ?></strong><br />
			  Change the color for the button's text.
			</label>
			  <input style="width: 100%; margin-bottom:1em;" id="vr_button_font_color" name="vr_button_font_color" type="text" value="<?php echo htmlspecialchars(stripslashes($options['button_font_color'])); ?>" />
			  
			   <br /> 
			 <label for="vr_button_border_color"><strong><?php _e('Button Border Color:'); ?></strong><br />
			  Change the color of the button's border.
			</label>
			  <input style="width: 100%; margin-bottom:1em;" id="vr_button_border_color" name="vr_button_border_color" type="text" value="<?php echo htmlspecialchars(stripslashes($options['button_border_color'])); ?>" />
			</fieldset>
			
			<hr />
			<h3>Additional Settings</h3>
			  <strong><?php _e('Type of Form:'); ?></strong><br />


			  <input type="radio" id="vr_showname_yes" name="vr_showname" value="full" <?php if(htmlspecialchars(stripslashes($options['showname'])) == 'full' || htmlspecialchars(stripslashes($options['showname'])) == '') { echo 'checked="checked"';}; ?> />
			  <label for="vr_showname_yes"><strong>Full Optin</strong> <br />(will display First Name, Last Name, Email, and subscribe button)</label>
			
			<br />
			  <input type="radio" id="vr_showname_email" name="vr_showname" value="email" <?php if(htmlspecialchars(stripslashes($options['showname'])) == 'email') { echo 'checked="checked"';}; ?> />
			  <label for="vr_showname_email"><strong>Only Email</strong> <br />(will show Email & subscribe button)
			</label>
			<br />
			  <input type="radio" id="vr_showname_button" name="vr_showname" value="button" <?php if(htmlspecialchars(stripslashes($options['showname'])) == 'button') { echo 'checked="checked"';}; ?> />
			  <label for="vr_showname_button"><strong>Only Subscribe</strong> <br />(will only show the subscribe button)
			</label>
			<br />
			<br />
			  <strong><?php _e('Show "Required <span style="color:red;">*</span>":'); ?></strong><br />
			  Let users know the email is required. Even if the email input is empty, they'll be able to enter it after submitting the form.<br />
			<label for="vr_required_yes">Yes
			</label>
			  <input type="radio" id="vr_required_yes" name="vr_required" value="yes" <?php if(htmlspecialchars(stripslashes($options['required'])) == 'yes' || htmlspecialchars(stripslashes($options['required'])) == '') { echo 'checked="checked"';}; ?> />
			<label for="vr_style_no">No
			</label>
			  <input type="radio" id="vr_required_no" name="vr_required" value="no" <?php if(htmlspecialchars(stripslashes($options['required'])) == 'no') { echo 'checked="checked"';}; ?> />
			
			
			<br />
			<br />
			  <strong><?php _e('Show Vertical Response Link:'); ?></strong><br />
			<label for="vr_show_vr_code_yes">Yes
			</label>
			  <input type="radio" id="vr_show_vr_code_yes" name="vr_show_vr_code" value="yes" <?php if(htmlspecialchars(stripslashes($options['show_vr_code'])) == 'yes' || htmlspecialchars(stripslashes($options['show_vr_code'])) == '') { echo 'checked="checked"';}; ?> />
			<label for="vr_show_vr_code_no">No
			</label>
			  <input type="radio" id="vr_show_vr_code_no" name="vr_show_vr_code" value="no" <?php if(htmlspecialchars(stripslashes($options['show_vr_code'])) == 'no') { echo 'checked="checked"';}; ?> />
			  
			<br />
			<br />
			  <strong><?php _e('Give Author Credit:'); ?></strong><br />
			  If you want to give credit to the widget author, this will add a text link to your website's footer. It would be much appreciated, and you can always turn it off.<br />
			
			<label for="vr_credit_yes">Yes
			</label>
			  <input type="radio" id="vr_credit_yes" name="vr_credit" value="yes" <?php if(htmlspecialchars(stripslashes($options['credit'])) == 'yes' || htmlspecialchars(stripslashes($options['credit'])) == '' || !$options['credit']) { echo 'checked="checked"';}; ?> />
			<label for="vr_credit_no">No
			</label>
			  <input type="radio" id="vr_credit_no" name="vr_credit" value="no" <?php if(htmlspecialchars(stripslashes($options['credit'])) == 'no') { echo 'checked="checked"';}; ?> />
		</p>
		<p> <input type="hidden" name="vr_form_submit" value="Submit" /></p>
		
<?php		}

register_sidebar_widget(array('Vertical Response', 'vertical-response'), 'widget_vr');
register_widget_control(array('Vertical Response', 'vertical-response'), 'widget_vr_options', '500');

	function kwd_vr_shortcode() {
		global $post, $kwd_cc_version;
		if(!is_admin()) { return widget_vr(false, false); }
	}
	add_shortcode('verticalresponse', 'kwd_vr_shortcode');
	add_shortcode('VerticalResponse', 'kwd_vr_shortcode');
	
}

add_action('widgets_init', 'vr_widget_init');

?>