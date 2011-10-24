<?php
/*
Plugin Name: Network Sitemap
Plugin URI: 
Description: 
Author: Catherine Sandrick
Version: 0.1
Author URI: http://MadScienceDept.com
*/   
   
/*  Copyright 2011  

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* Guess the wp-content and plugin urls/paths
*/
// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


if (!class_exists('msd_network_sitemap')) {
    class msd_network_sitemap {
        //This is where the class variables go, don't forget to use @var to tell what they're for/**
        /**
		 * @var string The plugin version
		 */
		var $version = '0.1';
        
        /**
        * @var string The options string name for this plugin
        */
        var $optionsName = 'msd_network_sitemap_options';
        
		/**
		 * @var string $nonce String used for nonce security
		 */
		var $nonce = 'msd_network_sitemap-update-options';
		
        /**
        * @var string $localizationDomain Domain used for localization
        */
        var $localizationDomain = "msd_network_sitemap";
        
        /**
        * @var string $pluginurl The path to this plugin
        */ 
        var $thispluginurl = '';
        /**
        * @var string $pluginurlpath The path to this plugin
        */
        var $thispluginpath = '';
            
        /**
        * @var array $options Stores the options for this plugin
        */
        var $options = array();
        
        //Class Functions
        /**
        * PHP 4 Compatible Constructor
        */
        function msd_network_sitemap(){$this->__construct();}
        
        /**
        * PHP 5 Constructor
        */        
        function __construct(){
            //Language Setup
            $locale = get_locale();
            $mo = dirname(__FILE__) . "/languages/" . $this->localizationDomain . "-".$locale.".mo";
            load_textdomain($this->localizationDomain, $mo);

            //"Constants" setup
            $this->thispluginurl = PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)).'/';
            $this->thispluginpath = PLUGIN_PATH . '/' . dirname(plugin_basename(__FILE__)).'/';
            
            //Initialize the options
            //This is REQUIRED to initialize the options when the plugin is loaded!
            $this->get_options();
            
            //Actions        
            add_action("admin_menu", array(&$this,"admin_menu_link"));

            
            add_action('wp_print_styles', array(&$this, 'add_css'));
            add_action('after_setup_theme', array(&$this, 'create_shortcodes'));
            /*
            add_action('wp_print_scripts', array(&$this, 'add_js'));
            */
            
            //Filters
            /*
            add_filter('the_content', array(&$this, 'filter_content'), 0);
            */
        }
        
    
        
        /**
        * @desc Adds the options subpanel
        */
        function admin_menu_link() {
            //If you change this from add_options_page, MAKE SURE you change the filter_plugin_actions function (below) to
            //reflect the page filename (ie - options-general.php) of the page your plugin is under!
            add_options_page('Network Sitemap', 'Network Sitemap', 10, basename(__FILE__), array(&$this,'admin_options_page'));
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'filter_plugin_actions'), 10, 2 );
        }
        
    
            
        /**
        * Adds settings/options page
        */
        function admin_options_page() { 
            if($_POST['msd_network_sitemap_save']){
                if (! wp_verify_nonce($_POST['_wpnonce'], 'msd_network_sitemap-update-options') ) die('Whoops! There was a problem with the data you posted. Please go back and try again.'); 
                $this->options['msd_network_sitemap_path'] = $_POST['msd_network_sitemap_path'];                   
                $this->options['msd_network_sitemap_allowed_groups'] = $_POST['msd_network_sitemap_allowed_groups'];
                $this->options['msd_network_sitemap_enabled'] = ($_POST['msd_network_sitemap_enabled']=='on')?true:false;
                                        
                $this->saveAdminOptions();
                
                echo '<div class="updated"><p>Success! Your changes were sucessfully saved!</p></div>';
            }
			require_once 'includes/admin_option_page.php';
        }
        
    	/**
		 * @desc Loads the Network Sitemap options. Responsible for 
		 * handling upgrades and default option values.
		 * @return array
		 */
		function check_options() {
			$options = null;
			if (!$options = get_option($this->optionsName)) {
				// default options for a clean install
				$options = array(
					'shortcut' => true,
					'theme' => 'default',
					'version' => $this->version,
					'reset' => true
				);
				update_option($this->optionsName, $options);
			}
			else {
				// check for upgrades
				if (isset($options['version'])) {
					if ($options['version'] < $this->version) {
						// post v1.0 upgrade logic goes here
					}
				}
				else {
					// pre v1.0 updates
					if (isset($options['admin'])) {
						unset($options['admin']);
						$options['shortcut'] = true;
						$options['version'] = $this->version;
						$options['reset'] = true;
						update_option($this->optionsName, $options);
					}
				}
			}

			return $options;
		}
		
		
    	/**
		 * @desc Retrieves the plugin options from the database.
		 */
		function get_options() {
			$options = $this->check_options();
			$this->options = $options;
		}
		
		/**
		 * @desc Determines if request is an AJAX call
		 * @return boolean
		 */
		function is_ajax() {
			return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
					&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
		}

		/**
		 * @desc Checks to see if the given plugin is active.
		 * @return boolean
		 */
		function is_plugin_active($plugin) {
			return in_array($plugin, (array) get_option('active_plugins', array()));
		}
    
		/**
		 * @desc Enqueue's the CSS for the specified theme.
		 */
		function add_css() {
			wp_enqueue_style('msd_network_sitemap', $this->pluginurl . "css/style.css", false, $this->version, 'screen');
		}
    
		/**
		 * @desc Responsible for loading the necessary scripts and localizing JavaScript messages
		 */
		function add_js() {
			//wp_enqueue_script('jquery-msd_network_sitemap', $this->pluginurl . 'js/jquery.msd_network_sitemap.js', array('jquery'), $this->version, true);
		}
        /**
        * Saves the admin options to the database.
        */
        function saveAdminOptions(){
            return update_option($this->optionsName, $this->options);
        }
        
        /**
        * @desc Adds the Settings link to the plugin activate/deactivate page
        */
        function filter_plugin_actions($links, $file) {
           //If your plugin is under a different top-level menu than Settiongs (IE - you changed the function above to something other than add_options_page)
           //Then you're going to want to change options-general.php below to the name of your top-level page
           $settings_link = '<a href="options-general.php?page=' . basename(__FILE__) . '">' . __('Settings') . '</a>';
           array_unshift( $links, $settings_link ); // before other links

           return $links;
        }  
        
        function create_shortcodes(){
        	add_shortcode( 'msd-network-sitemap', array(&$this,'generate_sitemap') );
        }
        
        function generate_sitemap(){
			$orig_blog_id = get_current_blog_id();
			$count = get_blog_count();
			for ($i = 1; $i < $count+1; $i++){
				switch_to_blog($i);
				$sitemap .= '<h3>'.get_blog_option( $i, 'blogname' ).'</h3>';
				//get pages
				$args = array(
					'post_type' => 'page',
					'posts_per_page' => -1,
					'order_by' => 'menu_order'
				);
				$sm_pgs = get_posts($args);
				$sitemap .= '<h4>Pages</h4>
				<ul>';
				foreach($sm_pgs AS $pg){
					$sitemap .= '<li class="sitemap '.sanitize_title(get_blog_option( $i, 'blogname' )).'">
				<a href="'.get_blog_permalink($i,$pg->ID).'">'.$pg->post_title.'</a></li>';
				}
				$sitemap .= '</ul>';
			}
			switch_to_blog($orig_blog_id);
        	return $sitemap;
        }        
  } //End Class
} //End if class exists statement

//instantiate the class
if (class_exists('msd_network_sitemap')) {
    $msd_network_sitemap_var = new msd_network_sitemap();
}
?>