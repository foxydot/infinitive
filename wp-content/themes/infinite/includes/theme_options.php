<?php
/*******************************
  THEME OPTIONS PAGE
********************************/

add_action('admin_menu', 'infinite_theme_page');
function infinite_theme_page ()
{
	if ( count($_POST) > 0 && isset($_POST['infinite_settings']) )
	{
		$options = array ('biz_name','street','street2','city','state','zip','phone','fax','linkedin_link','twitter_user','latest_tweet','google_link','facebook_link','flickr_link','youtube_link','landing_link','sharethis_link','salesforce_oid','salesforce_returnurl');
		
		foreach ( $options as $opt )
		{
			delete_option ( 'infinite_'.$opt, $_POST[$opt] );
			add_option ( 'infinite_'.$opt, $_POST[$opt] );	
		}			
		 
	}
	add_submenu_page('options-general.php',__('Settings'), __('Theme Social Settings'), 'administrator', 'infinite-options', 'infinite_settings');
}
function infinite_settings()
{
$states = array('ALABAMA'=>"AL",
'ALASKA'=>"AK",
'AMERICAN SAMOA'=>"AS",
'ARIZONA'=>"AZ",
'ARKANSAS'=>"AR",
'CALIFORNIA'=>"CA",
'COLORADO'=>"CO",
'CONNECTICUT'=>"CT",
'DELAWARE'=>"DE",
'DISTRICT OF COLUMBIA'=>"DC",
"FEDERATED STATES OF MICRONESIA"=>"FM",
'FLORIDA'=>"FL",
'GEORGIA'=>"GA",
'GUAM' => "GU",
'HAWAII'=>"HI",
'IDAHO'=>"ID",
'ILLINOIS'=>"IL",
'INDIANA'=>"IN",
'IOWA'=>"IA",
'KANSAS'=>"KS",
'KENTUCKY'=>"KY",
'LOUISIANA'=>"LA",
'MAINE'=>"ME",
'MARSHALL ISLANDS'=>"MH",
'MARYLAND'=>"MD",
'MASSACHUSETTS'=>"MA",
'MICHIGAN'=>"MI",
'MINNESOTA'=>"MN",
'MISSISSIPPI'=>"MS",
'MISSOURI'=>"MO",
'MONTANA'=>"MT",
'NEBRASKA'=>"NE",
'NEVADA'=>"NV",
'NEW HAMPSHIRE'=>"NH",
'NEW JERSEY'=>"NJ",
'NEW MEXICO'=>"NM",
'NEW YORK'=>"NY",
'NORTH CAROLINA'=>"NC",
'NORTH DAKOTA'=>"ND",
"NORTHERN MARIANA ISLANDS"=>"MP",
'OHIO'=>"OH",
'OKLAHOMA'=>"OK",
'OREGON'=>"OR",
"PALAU"=>"PW",
'PENNSYLVANIA'=>"PA",
'RHODE ISLAND'=>"RI",
'SOUTH CAROLINA'=>"SC",
'SOUTH DAKOTA'=>"SD",
'TENNESSEE'=>"TN",
'TEXAS'=>"TX",
'UTAH'=>"UT",
'VERMONT'=>"VT",
'VIRGIN ISLANDS' => "VI",
'VIRGINIA'=>"VA",
'WASHINGTON'=>"WA",
'WEST VIRGINIA'=>"WV",
'WISCONSIN'=>"WI",
'WYOMING'=>"WY");
	?>
<div class="wrap">
	<h2>Theme Options</h2>
	
<form method="post" action="">
	<fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px; color:#2481C6;text-transform:uppercase;"><strong>Contact Info</strong></legend>
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="biz_name">Business Name:</label></th>
			<td>
				<input name="biz_name" type="text" id="biz_name" value="<?php echo get_option('infinite_biz_name'); ?>" class="regular-text" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="street">Street Address:</label></th>
			<td>
				<input name="street" type="text" id="street" value="<?php echo get_option('infinite_street'); ?>" class="regular-text" /><br />
				<input name="street2" type="text" id="street2" value="<?php echo get_option('infinite_street2'); ?>" class="regular-text" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="city">City:</label></th>
			<td>
				<input name="city" type="text" id="city" value="<?php echo get_option('infinite_city'); ?>" class="regular-text" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="state">State:</label></th>
			<td>
				<select name="state" id="state" class="regular-text" />
					<option>Select</option>
					<?php foreach($states AS $state => $st){ ?>
						<option value="<?php print $st; ?>"<?php print get_option('infinite_state')==$st?' SELECTED':'';?>><?php print ucwords(strtolower($state)); ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="zip">Zip:</label></th>
			<td>
				<input name="zip" type="text" id="zip" value="<?php echo get_option('infinite_zip'); ?>" class="regular-text" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="phone">Phone:</label></th>
			<td>
				<input name="phone" type="text" id="phone" value="<?php echo get_option('infinite_phone'); ?>" class="regular-text" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="fax">Fax:</label></th>
			<td>
				<input name="fax" type="text" id="fax" value="<?php echo get_option('infinite_fax'); ?>" class="regular-text" />
			</td>
		</tr>
        </table>
        </fieldset><fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px; color:#2481C6;text-transform:uppercase;"><strong>Salesforce Mailer</strong></legend>
		<table class="form-table">

        <tr valign="top">
			<th scope="row"><label for="google_link">Salesforce OID</label></th>
			<td>
				<input name="salesforce_oid" type="text" id="salesforce_oid" value="<?php echo get_option('infinite_salesforce_oid'); ?>" class="regular-text" />
			</td>
		</tr>			
		<th scope="row"><label for="google_link">Return URL (Thank You Page)</label></th>
			<td>
				<input name="salesforce_returnurl" type="text" id="salesforce_returnurl" value="<?php echo get_option('infinite_salesforce_returnurl'); ?>" class="regular-text" />
			</td>
		</tr>
        </table>
        </fieldset>
	<fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px; color:#2481C6;text-transform:uppercase;"><strong>Social Links</strong></legend>
		<table class="form-table">

        <tr valign="top">
			<th scope="row"><label for="google_link">Google+ link</label></th>
			<td>
				<input name="google_link" type="text" id="google_link" value="<?php echo get_option('infinite_google_link'); ?>" class="regular-text" />
			</td>
		</tr>

        <tr valign="top">
			<th scope="row"><label for="facebook_link">Facebook link</label></th>
			<td>
				<input name="facebook_link" type="text" id="facebook_link" value="<?php echo get_option('infinite_facebook_link'); ?>" class="regular-text" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="twitter_user">Twitter Username</label></th>
			<td>
				<input name="twitter_user" type="text" id="twitter_user" value="<?php echo get_option('infinite_twitter_user'); ?>" class="regular-text" />
			</td>
		</tr>
        <tr valign="top">
			<th scope="row"><label for="linkedin_link">LinkedIn link</label></th>
			<td>
				<input name="linkedin_link" type="text" id="linkedin_link" value="<?php echo get_option('infinite_linkedin_link'); ?>" class="regular-text" />
			</td>
		</tr>
        <tr valign="top">
			<th scope="row"><label for="flickr_link">Flickr link</label></th>
			<td>
				<input name="flickr_link" type="text" id="flickr_link" value="<?php echo get_option('infinite_flickr_link'); ?>" class="regular-text" />
			</td>
		</tr>
        <tr valign="top">
			<th scope="row"><label for="youtube_link">YouTube link</label></th>
			<td>
				<input name="youtube_link" type="text" id="youtube_link" value="<?php echo get_option('infinite_youtube_link'); ?>" class="regular-text" />
			</td>
		</tr>
        <tr valign="top">
			<th scope="row"><label for="sharethis_link">ShareThis link</label></th>
			<td>
				<input name="sharethis_link" type="text" id="sharethis_link" value="<?php echo get_option('infinite_sharethis_link'); ?>" class="regular-text" />
			</td>
		</tr>
        </table>
        </fieldset>
        
        
	<fieldset style="border:1px solid #ddd; padding-bottom:20px; margin-top:20px;">
	<legend style="margin-left:5px; padding:0 5px; color:#2481C6;text-transform:uppercase;"><strong>Landing Page</strong></legend>
		<table class="form-table">

        <tr valign="top">
			<th scope="row"><label for="landing_link">Landing Page Link</label></th>
			<td>
				<input name="landing_link" type="text" id="landing_link" value="<?php echo get_option('infinite_landing_link'); ?>" class="regular-text" />
			</td>
		</tr>
        </table>
        </fieldset>
        
        
		<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		<input type="hidden" name="infinite_settings" value="save" style="display:none;" />
		</p>
</form>
</div>
<?php }
function infinite_address($print = TRUE){
	if((get_option('infinite_street')!='') || (get_option('infinite_city')!='') || (get_option('infinite_state')!='') || (get_option('infinite_zip')!='')) {
		$ret .= '<address>';
			$ret .= (get_option('infinite_street')!='')?get_option('infinite_street').' ':'';
			$ret .= (get_option('infinite_street2')!='')?get_option('infinite_street2').'<br />':'<br />';
			$ret .= (get_option('infinite_city')!='')?get_option('infinite_city').', ':'';
			$ret .= (get_option('infinite_state')!='')?get_option('infinite_state').' ':'';
			$ret .= (get_option('infinite_zip')!='')?get_option('infinite_zip').' ':'';
		$ret .= '</address>';
		}
		if((get_option('infinite_phone')!='') || (get_option('infinite_fax')!='')) {
		$ret .= '<address>';
			$ret .= (get_option('infinite_phone')!='')?'Phone: '.get_option('infinite_phone').'<br />':'';
			$ret .= (get_option('infinite_fax')!='')?'Fax: '.get_option('infinite_fax').' ':'';
			$ret .= '</address>';
		}
	if($print){
		print $ret;
	} else {
		return $ret;
	}
}
//create copyright message
function infinite_copyright($address = TRUE){
	$ret .= 'Copyright &copy;'.date('Y').' ';
	$ret .= (get_option('infinite_biz_name')!='')?get_option('infinite_biz_name'):get_bloginfo('name');
	$ret .= '. All Rights Reserved.';
	if($address){
		$ret .= infinite_address(FALSE);
	}
	print $ret;
}