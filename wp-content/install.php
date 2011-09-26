<?php 
/* CUSTOM INSTALLER */
/**
 * Installs the blog
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param string $blog_title Blog title.
 * @param string $user_name User's username.
 * @param string $user_email User's email.
 * @param bool $public Whether blog is public.
 * @param null $deprecated Optional. Not used.
 * @param string $user_password Optional. User's chosen password. Will default to a random password.
 * @return array Array keys 'url', 'user_id', 'password', 'password_message'.
 */
function wp_install( $blog_title, $user_name, $user_email, $public, $deprecated = '', $user_password = '' , $dealer_info = array()) {
	global $wp_rewrite;

	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '2.6' );

	wp_check_mysql_version();
	wp_cache_flush();
	make_db_current_silent();
	populate_options();
	populate_roles();

	update_option('blogname', $blog_title);
	update_option('admin_email', $user_email);
	update_option('blog_public', $public);

	$guessurl = wp_guess_url();

	update_option('siteurl', $guessurl);
	
	// Some Options
	update_option('gmt_offset', '-5');
	update_option('start_of_week', '0');
	update_option('permalink_structure', '/%postname%/');
	update_option('active_plugins', $plugins);
	update_option('home', $guessurl);
	update_option('template', 'msd_base_theme');
	update_option('stylesheet', 'msd_base_theme');
	update_option('show_on_front', 'page');
	update_option('page_on_front', '1'); //may need to be added after the page is actually created to get the page number
	update_option('page_for_posts', '0'); //may need to be added after the page is actually created to get the page number
	update_option('default_comment_status', 'closed');
	update_option('default_ping_status', 'closed');
	$menu_opt['0'] = '';
	$menu_opt['auto_add'] = array();
	update_option('nav_menu_options', $menu_opt);
	update_option('blogdescription', '');

	// If not a public blog, don't ping.
	if ( ! $public )
		update_option('default_pingback_flag', 0);

	// Create default user.  If the user already exists, the user tables are
	// being shared among blogs.  Just set the role in that case.
	$user_id = username_exists($user_name);
	$user_password = trim($user_password);
	$email_password = false;
	if ( !$user_id && empty($user_password) ) {
		$user_password = wp_generate_password();
		$message = __('<strong><em>Note that password</em></strong> carefully! It is a <em>random</em> password that was generated just for you.');
		$user_id = wp_create_user($user_name, $user_password, $user_email);
		update_user_option($user_id, 'default_password_nag', true, true);
		$email_password = true;
	} else if ( !$user_id ) {
		// Password has been provided
		$message = '<em>'.__('Your chosen password.').'</em>';
		$user_id = wp_create_user($user_name, $user_password, $user_email);
	} else {
		$message =  __('User already exists. Password inherited.');
	}

	$user = new WP_User($user_id);
	$user->set_role('administrator');

	wp_install_defaults($user_id, $dealer_info);

	$wp_rewrite->flush_rules();

	wp_new_blog_notification($blog_title, $guessurl, $user_id, ($email_password ? $user_password : __('The password you chose during the install.') ) );

	wp_cache_flush();

	return array('url' => $guessurl, 'user_id' => $user_id, 'password' => $user_password, 'password_message' => $message);
}

/**
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param int $user_id User ID.
 */
function wp_install_defaults($user_id, $dealer_info) {
	global $wpdb, $wp_rewrite, $current_site, $table_prefix;
	// Get the dealer info
	extract($dealer_info);
	// Default category
	$cat_name = __('News');
	/* translators: Default category slug */
	$cat_slug = sanitize_title(_x('news', 'Default category slug'));

	if ( global_terms_enabled() ) {
		$cat_id = $wpdb->get_var( $wpdb->prepare( "SELECT cat_ID FROM {$wpdb->sitecategories} WHERE category_nicename = %s", $cat_slug ) );
		if ( $cat_id == null ) {
			$wpdb->insert( $wpdb->sitecategories, array('cat_ID' => 0, 'cat_name' => $cat_name, 'category_nicename' => $cat_slug, 'last_updated' => current_time('mysql', true)) );
			$cat_id = $wpdb->insert_id;
		}
		update_option('default_category', $cat_id);
	} else {
		$cat_id = 1;
	}

	$wpdb->insert( $wpdb->terms, array('term_id' => $cat_id, 'name' => $cat_name, 'slug' => $cat_slug, 'term_group' => 0) );
	$wpdb->insert( $wpdb->term_taxonomy, array('term_id' => $cat_id, 'taxonomy' => 'category', 'description' => '', 'parent' => 0, 'count' => 1));
	$cat_tt_id = $wpdb->insert_id;

	// Default link category
	$cat_name = __('Links');
	/* translators: Default link category slug */
	$cat_slug = sanitize_title(_x('links', 'Default link category slug'));

	if ( global_terms_enabled() ) {
		$blogroll_id = $wpdb->get_var( $wpdb->prepare( "SELECT cat_ID FROM {$wpdb->sitecategories} WHERE category_nicename = %s", $cat_slug ) );
		if ( $blogroll_id == null ) {
			$wpdb->insert( $wpdb->sitecategories, array('cat_ID' => 0, 'cat_name' => $cat_name, 'category_nicename' => $cat_slug, 'last_updated' => current_time('mysql', true)) );
			$blogroll_id = $wpdb->insert_id;
		}
		update_option('default_link_category', $blogroll_id);
	} else {
		$blogroll_id = 2;
	}

	$wpdb->insert( $wpdb->terms, array('term_id' => $blogroll_id, 'name' => $cat_name, 'slug' => $cat_slug, 'term_group' => 0) );
	$wpdb->insert( $wpdb->term_taxonomy, array('term_id' => $blogroll_id, 'taxonomy' => 'link_category', 'description' => '', 'parent' => 0, 'count' => 1));
	$blogroll_tt_id = $wpdb->insert_id;

	// Now drop in some default links
	$default_links = array();
	$default_links[] = array(	'link_url' => 'http://madsciencedept.com/',
								'link_name' => 'Mad Science Department',
								'link_rss' => '',
								'link_notes' => 'Custom web application design and development');

	foreach ( $default_links as $link ) {
		$wpdb->insert( $wpdb->links, $link);
		$wpdb->insert( $wpdb->term_relationships, array('term_taxonomy_id' => $blogroll_tt_id, 'object_id' => $wpdb->insert_id) );
	}

	
	$now = date('Y-m-d H:i:s');
	$now_gmt = gmdate('Y-m-d H:i:s');
	// First Page
	$first_page = __('');
	$first_post_guid = get_option('home') . '/?page_id=1';
	$wpdb->insert( $wpdb->posts, array(
								'post_author' => $user_id,
								'post_date' => $now,
								'post_date_gmt' => $now_gmt,
								'post_content' => $first_page,
								'post_excerpt' => '',
								'post_title' => __('Home Page'),
								'post_name' => _x('home', 'Default page slug'),
								'comment_status' => 'closed',
								'post_modified' => $now,
								'post_modified_gmt' => $now_gmt,
								'guid' => $first_post_guid,
								'post_type' => 'page',
								'to_ping' => '',
								'pinged' => '',
								'post_content_filtered' => ''
								));
	$wpdb->insert( $wpdb->postmeta, array( 'post_id' => 1, 'meta_key' => '_wp_page_template', 'meta_value' => 'home.php' ) );
	
	
	$cat_tt_id = 1;
	// First post
	$first_post_guid = get_option('home') . '/?p=2';

	if ( is_multisite() ) {
		$first_post = get_site_option( 'first_post' );

		if ( empty($first_post) )
			$first_post = __('This is your first post.');
		} else {
			$first_post = stripslashes( __( '' ) );
					
	}

	$wpdb->insert( $wpdb->posts, array(
								'post_author' => $user_id,
								'post_date' => $now,
								'post_date_gmt' => $now_gmt,
								'post_content' => $first_post,
								'post_excerpt' => '',
								'post_title' => __('Welcome to the new site'),
								'post_name' => sanitize_title( _x('welcome', 'Default post slug') ),
								'comment_status' => 'closed',
								'post_modified' => $now,
								'post_modified_gmt' => $now_gmt,
								'guid' => $first_post_guid,
								'comment_count' => 0,
								'to_ping' => '',
								'pinged' => '',
								'post_content_filtered' => ''
								));
	$wpdb->insert( $wpdb->term_relationships, array('term_taxonomy_id' => $cat_tt_id, 'object_id' => 2) );	
	
	
	
	//add menu items
	
	$wpdb->insert( $wpdb->terms, array('term_id' => 3, 'name' => 'Primary Links', 'slug' => 'primary-links', 'term_group' => 0) );
	$wpdb->insert( $wpdb->term_taxonomy, array('term_id' => 3, 'taxonomy' => 'nav_menu', 'description' => '', 'parent' => 0, 'count' => 6));
	
	//Menuitem1
	$this_post_guid = get_option('home') . '/?p=3';

	if ( is_multisite() ) {
		$this_post = get_site_option( 'this_post' );

		if ( empty($this_post) )
			$this_post = '';
		} else {
			$this_post = stripslashes( __( 'Home Menu Item' ) );
	}

	$wpdb->insert( $wpdb->posts, array(
								'post_author' => $user_id,
								'post_date' => $now,
								'post_date_gmt' => $now_gmt,
								'post_content' => '',
								'post_excerpt' => '',
								'post_title' => __(''),
								'post_name' => sanitize_title( _x('3', 'Default post slug') ),
								'post_modified' => $now,
								'post_modified_gmt' => $now_gmt,
								'guid' => $this_post_guid,
								'menu_order' => '2',
								'post_type' => 'nav_menu_item',
								'comment_status' => 'closed',
								'comment_count' => 0,
								'ping_status' => 'closed',
								'to_ping' => '',
								'pinged' => '',
								'post_content_filtered' => ''
								));
	$wpdb->insert( $wpdb->postmeta, array( 'post_id' => 3, 'meta_key' => '_menu_item_type', 'meta_value' => 'post_type' ) );	
	$wpdb->insert( $wpdb->postmeta, array( 'post_id' => 3, 'meta_key' => '_menu_item_menu_item_parent', 'meta_value' => '0' ) );	
	$wpdb->insert( $wpdb->postmeta, array( 'post_id' => 3, 'meta_key' => '_menu_item_object_id', 'meta_value' => '1' ) );	
	$wpdb->insert( $wpdb->postmeta, array( 'post_id' => 3, 'meta_key' => '_menu_item_object', 'meta_value' => 'page' ) );	
	$wpdb->insert( $wpdb->postmeta, array( 'post_id' => 3, 'meta_key' => '_menu_item_target', 'meta_value' => '' ) );	
	$wpdb->insert( $wpdb->postmeta, array( 'post_id' => 3, 'meta_key' => '_menu_item_classes', 'meta_value' => 'a:1:{i:0;s:0:"";}' ) );
	$wpdb->insert( $wpdb->postmeta, array( 'post_id' => 3, 'meta_key' => '_menu_item_xfn', 'meta_value' => '' ) );	
	$wpdb->insert( $wpdb->postmeta, array( 'post_id' => 3, 'meta_key' => '_menu_item_url', 'meta_value' => '' ) );	
			
		$wpdb->insert( $wpdb->term_relationships, array('term_taxonomy_id' => 3, 'object_id' => 3) );	
	
	//Make sure the permalinks will work
	$home_path = get_home_path();
	$iis7_permalinks = iis7_supports_permalinks();
	$prefix = $blog_prefix = '';
	$permalink_structure = get_option('permalink_structure');
	$wp_rewrite->set_permalink_structure( $permalink_structure );
		
	$wp_rewrite->flush_rules();
	
	if ( is_multisite() ) {
		// Flush rules to pick up the new page.
		$wp_rewrite->init();
		$wp_rewrite->flush_rules();

		$user = new WP_User($user_id);
		$wpdb->update( $wpdb->options, array('option_value' => $user->user_email), array('option_name' => 'admin_email') );

		// Remove all perms except for the login user.
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id != %d AND meta_key = %s", $user_id, $table_prefix.'user_level') );
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id != %d AND meta_key = %s", $user_id, $table_prefix.'capabilities') );

		// Delete any caps that snuck into the previously active blog. (Hardcoded to blog 1 for now.) TODO: Get previous_blog_id.
		if ( !is_super_admin( $user_id ) && $user_id != 1 )
			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id = %d AND meta_key = %s", $user_id, $wpdb->base_prefix.'1_capabilities') );
	}
}
