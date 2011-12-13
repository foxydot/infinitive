<?php
/**
 * infinite functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, infinite_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'infinite_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage infinite
 * @since infinite 3.1
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 1110;

/** Tell WordPress to run infinite_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'infinite_setup' );

if ( ! function_exists( 'infinite_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override infinite_setup() in a child theme, add your own infinite_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Ten 1.0
 */
function infinite_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();
	update_option('image_default_link_type','none');

	// Post Format support. You can also use the legacy "gallery" or "asides" (note the plural) categories.
	//add_theme_support( 'post-formats', array( 'aside', 'gallery' ) );

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' , array('post','page','msd_news','msd_publication','msd_casestudy'));

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'infinite', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu() in three locations.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'infinite' ),
		'header' => __( 'Header Navigation', 'infinite' ),
		'side' => __( 'Sidebar Navigation', 'infinite' ),
		'footer' => __( 'Footer Navigation', 'infinite' ),
	) );

	// This theme allows users to set a custom background
	add_custom_background();

	// Your changeable header business starts here
	if ( ! defined( 'HEADER_TEXTCOLOR' ) )
		define( 'HEADER_TEXTCOLOR', '' );

	// No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
	if ( ! defined( 'HEADER_IMAGE' ) )
		define( 'HEADER_IMAGE', '%s/images/headers/path.jpg' );
		
	// The height and width of your custom header. You can hook into the theme's own filters to change these values.
	// Add a filter to infinite_header_image_width and infinite_header_image_height to change these values.
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'infinite_header_image_width', 1060 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'infinite_header_image_height', 280 ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be 940 pixels wide by 198 pixels tall.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );
	
	//extra sizes
	define( 'FEATURE_IMAGE_WIDTH', apply_filters( 'infinite_header_image_width', 1060 ) );
	define( 'FEATURE_IMAGE_HEIGHT', apply_filters( 'infinite_header_image_height', 280 ) );
	add_image_size( 'feature', FEATURE_IMAGE_WIDTH, FEATURE_IMAGE_HEIGHT, true );
	
	define( 'SIDEBAR_IMAGE_WIDTH', apply_filters( 'infinite_header_image_width', 257 ) );
	define( 'SIDEBAR_IMAGE_HEIGHT', apply_filters( 'infinite_header_image_height', 360 ) );
	add_image_size( 'sidebar', SIDEBAR_IMAGE_WIDTH, SIDEBAR_IMAGE_HEIGHT, true );
	
	add_image_size( 'gridthumb', 228, 81, true );

	// Don't support text inside the header image.
	if ( ! defined( 'NO_HEADER_TEXT' ) )
		define( 'NO_HEADER_TEXT', true );

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See infinite_admin_header_style(), below.
	add_custom_image_header( '', 'infinite_admin_header_style' );

	// ... and thus ends the changeable header business.

		// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
		register_default_headers( array(
			'none' => array(
				'url' => '#',
				'thumbnail_url' => '#',
				/* translators: header image description */
				'description' => __( 'none', 'infinite' )
			)
		) );
	}
	endif;

if ( ! function_exists( 'infinite_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in infinite_setup().
 *
 * @since Twenty Ten 1.0
 */
function infinite_admin_header_style() {
?>
<style type="text/css">
/* Shows the same border as on front end */
#headimg {
	border-bottom: 1px solid #000;
	border-top: 4px solid #000;
}
/* If NO_HEADER_TEXT is false, you would style the text with these selectors:
	#headimg #name { }
	#headimg #desc { }
*/
</style>
<?php
}
endif;

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 * @since Twenty Ten 1.0
 */
function infinite_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'infinite_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since Twenty Ten 1.0
 * @return int
 */
function infinite_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'infinite_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since Twenty Ten 1.0
 * @return string "Continue Reading" link
 */
function infinite_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'infinite' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and infinite_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string An ellipsis
 */
function infinite_auto_excerpt_more( $more ) {
	return ' &hellip;' . infinite_continue_reading_link();
}
add_filter( 'excerpt_more', 'infinite_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function infinite_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= infinite_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'infinite_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in Twenty Ten's style.css. This is just
 * a simple filter call that tells WordPress to not use the default styles.
 *
 * @since Twenty Ten 1.2
 */
add_filter( 'use_default_gallery_style', '__return_false' );

/**
 * Deprecated way to remove inline styles printed when the gallery shortcode is used.
 *
 * This function is no longer needed or used. Use the use_default_gallery_style
 * filter instead, as seen above.
 *
 * @since Twenty Ten 1.0
 * @deprecated Deprecated in Twenty Ten 1.2 for WordPress 3.1
 *
 * @return string The gallery style filter, with the styles themselves removed.
 */
function infinite_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
// Backwards compatibility with WordPress 3.0.
if ( version_compare( $GLOBALS['wp_version'], '3.1', '<' ) )
	add_filter( 'gallery_style', 'infinite_remove_gallery_css' );

if ( ! function_exists( 'infinite_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own infinite_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Ten 1.0
 */
function infinite_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'infinite' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'infinite' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'infinite' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'infinite' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'infinite' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'infinite' ), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * To override infinite_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @since Twenty Ten 1.0
 * @uses register_sidebar
 */
function infinite_widgets_init() {	
	global $blog_id;
	// Area 6, located in the footer. Empty by default.
	if($blog_id == 1){
		register_sidebar( array(
			'name' => __( 'Main Homepage Feature Area', 'infinite' ),
			'id' => 'main-feature-area',
			'description' => __( 'The feature area on the homepage. One slideshow is recommended.', 'infinite' ),
			'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
		// Area 3, located in the footer. Empty by default.
		register_sidebar( array(
			'name' => __( 'Main Homepage Footer Widget Area', 'infinite' ),
			'id' => 'main-footer-widget-area',
			'description' => __( 'The main homepage footer widget area. Three widgets are recommended.', 'infinite' ),
			'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h4 class="widget-title">',
			'after_title' => '</h4>',
		) );
	}
	// Area 3, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Homepage Footer Widget Area', 'infinite' ),
		'id' => 'homepage-footer-widget-area',
		'description' => __( 'The homepage footer widget area. Three widgets are recommended.', 'infinite' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );

	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'infinite' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'infinite' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Secondary Widget Area', 'infinite' ),
		'id' => 'secondary-widget-area',
		'description' => __( 'The secondary widget area', 'infinite' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Blog Widget Area', 'infinite' ),
		'id' => 'blog-widget-area',
		'description' => __( 'The blog widget area', 'infinite' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );
	// Area 3, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Footer Widget Area', 'infinite' ),
		'id' => 'footer-widget-area',
		'description' => __( 'The footer widget area. Two widgets are recommended.', 'infinite' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>',
	) );
}
/** Register sidebars by running infinite_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'infinite_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * To override this in a child theme, remove the filter and optionally add your own
 * function tied to the widgets_init action hook.
 *
 * This function uses a filter (show_recent_comments_widget_style) new in WordPress 3.1
 * to remove the default style. Using Twenty Ten 1.2 in WordPress 3.0 will show the styles,
 * but they won't have any effect on the widget in default Twenty Ten styling.
 *
 * @since Twenty Ten 1.0
 */
function infinite_remove_recent_comments_style() {
	add_filter( 'show_recent_comments_widget_style', '__return_false' );
}
add_action( 'widgets_init', 'infinite_remove_recent_comments_style' );

if ( ! function_exists( 'infinite_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 * @since Twenty Ten 1.0
 */
function infinite_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'infinite' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'infinite' ), get_the_author() ),
			get_the_author()
		)
	);
}
endif;

if ( ! function_exists( 'infinite_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since Twenty Ten 1.0
 */
function infinite_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'infinite' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'infinite' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'infinite' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;

add_action('wp_print_scripts', 'add_scripts');
 
function add_scripts() {
	if(!is_admin()){
    	wp_enqueue_script('theme_jquery', get_bloginfo('template_url').'/js/theme_jquery.js', array('jquery'),NULL,TRUE);    
		wp_enqueue_script('sharethis','http://w.sharethis.com/button/buttons.js',FALSE,NULL,TRUE);
		wp_enqueue_script('sharethis_creds',get_bloginfo('template_url').'/js/sharethis.js',FALSE,NULL,TRUE);
		if (is_front_page()) {
	    	wp_enqueue_script('home_jquery', get_bloginfo('template_url').'/js/homepage_jquery.js', array('jquery'),NULL,TRUE);    
		}
	}
}

//add_action('wp_print_scripts', 'infinite_cufon');

// add classes for subdomain
add_filter('wp_print_styles','add_styles');
function add_styles() {
	$site = get_current_site()->domain;
	$url = get_bloginfo('url');
	$sub = preg_replace('@http://@i','',$url);
	$sub = preg_replace('@'.$site.'@i','',$sub);
	$sub = preg_replace('@\.@i','',$sub);
	wp_enqueue_style( $sub.'-style', get_bloginfo('template_url').'/css/'.$sub.'-style.css', FALSE, '0.1', 'all' );
}

function infinite_cufon(){
	if(!is_admin()){
		$my_cufon = get_bloginfo('template_url')."/js/cufon-yui.js";
		wp_enqueue_script('my_cufon',$my_cufon,array('jquery'));

		$my_cufon_font = get_bloginfo('template_url')."/js/delicious.font.js";
		wp_enqueue_script('my_cufon_font',$my_cufon_font,array('jquery'));

		$my_cufon_load = get_bloginfo('template_url')."/js/cufon-load.js";
		wp_enqueue_script('my_cufon_load',$my_cufon_load,array('jquery'));
	}
}
/* Add scripts and styles for shooting gallery ONLY on the page that needs it. Adjsut the slug for additional pages/to change the page name. */
add_action('wp_print_scripts', 'shooting_gallery_js');
function shooting_gallery_js(){
	global $blog_id;
	if($blog_id == '1' && is_page('our-experts')){
		wp_enqueue_script('prototype', get_bloginfo('template_url').'/js/legacy_prototype/prototype.js', NULL, NULL, FALSE);  
		wp_enqueue_script('scriptaculous', get_bloginfo('template_url').'/js/legacy_prototype/scriptaculous.js', array('prototype'), NULL, FALSE);  
		wp_enqueue_script('scriptaculous_effect', get_bloginfo('template_url').'/js/legacy_prototype/effects.js', array('prototype','scriptaculous'), NULL, FALSE);  
		wp_enqueue_script('prototype_tabs', get_bloginfo('template_url').'/js/legacy_prototype/tabs.js', array('prototype'), NULL, FALSE);  
		wp_enqueue_script('prototype_tabmanager', get_bloginfo('template_url').'/js/legacy_prototype/tabmanager.js', array('prototype', 'prototype_tabs'), NULL, TRUE);    
	}
}
add_action('wp_print_styles', 'shooting_gallery_css');
function shooting_gallery_css(){
	global $blog_id;
	if($blog_id == '1' && is_page('our-experts')){
		wp_enqueue_style( 'shooting-gallery', get_bloginfo('template_url').'/css/shooting-gallery.css', FALSE, '0.1', 'all' );	
	}
}
/* End shooting gallery scripts/styles */
// cleanup tinymce for SEO
function fb_change_mce_buttons( $initArray ) {
	//@see http://wiki.moxiecode.com/index.php/TinyMCE:Control_reference
	$initArray['theme_advanced_blockformats'] = 'p,address,pre,code,h3,h4,h5,h6';
	$initArray['theme_advanced_disable'] = 'forecolor';

	return $initArray;
	}
	add_filter('tiny_mce_before_init', 'fb_change_mce_buttons');
	
// add classes for various browsers
add_filter('body_class','browser_body_class');
function browser_body_class($classes) {
    global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
 
    if($is_lynx) $classes[] = 'lynx';
    elseif($is_gecko) $classes[] = 'gecko';
    elseif($is_opera) $classes[] = 'opera';
    elseif($is_NS4) $classes[] = 'ns4';
    elseif($is_safari) $classes[] = 'safari';
    elseif($is_chrome) $classes[] = 'chrome';
    elseif($is_IE) $classes[] = 'ie';
    else $classes[] = 'unknown';
 
    if($is_iphone) $classes[] = 'iphone';
    return $classes;
}

// add classes for subdomain
add_filter('body_class','subdomain_body_class');
function subdomain_body_class($classes) {
	global $subdomain;
	$site = get_current_site()->domain;
	$url = get_bloginfo('url');
	$sub = preg_replace('@http://@i','',$url);
	$sub = preg_replace('@'.$site.'@i','',$sub);
	$sub = preg_replace('@\.@i','',$sub);
    $classes[] = $sub;
    $subdomain = $sub;
    return $classes;
}

function infinite_check_site_path($url,$match){
	$site = get_current_site()->domain;
	$test = preg_replace('@http://@i','',$url);
	$test = preg_replace('@'.$site.'/@i','',$test);
	if($test == $match){
		return TRUE;
	} else {
		return FALSE;
	}
}

add_filter('body_class','section_body_class');
function section_body_class($classes) {
    global $post;
	$post_data = get_post(get_topmost_parent($post->ID));
	$classes[] = $post_data->post_name;
    return $classes;
}
add_action('template_redirect','set_section');
function set_section(){
	global $post, $section;
	$post_data = get_post(get_topmost_parent($post->ID));
	$section = $post_data->post_name;
}
function get_topmost_parent($post_id){
	$parent_id = get_post($post_id)->post_parent;
	if($parent_id == 0){
		$parent_id = $post_id;
	}else{
		$parent_id = get_topmost_parent($parent_id);
	}
	return $parent_id;
}

include_once('includes/theme_widgets.php');
include_once('includes/theme_options.php');
include_once('includes/image_handling.php');
include_once('includes/page_taxonomy.php');
include_once('includes/highlights.php');
include_once('includes/analytics.php');

	/*
	 * A useful troubleshooting function.
	 */
	function ts_data($data){
		$ret = '<textarea cols="100" rows="20" class="troubleshoot">';
		$ret .= print_r($data,true);
		$ret .= '</textarea>';
		print $ret;
	}