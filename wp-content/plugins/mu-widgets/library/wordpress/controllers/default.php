<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class wv45v_default extends wv45v_action {
	/*******************************************************************
	 * Default actions of all types 
	 *******************************************************************/
	/*******************************************************************
	 * Routines used by the default actions
	 *******************************************************************/
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function plugins_loadedWPaction() {
		load_plugin_textdomain ( get_class ( $this ), false, dirname ( plugin_basename ( $this->application()->filename ) ) . "/languages/" );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function initWPaction() {
		wp_register_style ( 'v45v_images', $this->application ()->pluginuri () . '/library/base/public/css/images.css', null, $this->application ()->version () );
		wp_register_style ( 'v45v_admin', $this->application ()->pluginuri () . '/library/base/public/css/admin.css', null, $this->application ()->version () );
		wp_register_style ( 'v45v_front', $this->application ()->pluginuri () . '/library/base/public/css/front.css', null, $this->application ()->version () );
		wp_register_style ( 'v45v_common', $this->application ()->pluginuri () . '/library/base/public/css/common.css', null, $this->application ()->version () );
		//wp_register_style ( 'jquery-ui_smoothness', $this->application ()->pluginuri () . '/library/public/css/smoothness/jquery-ui-1.8.13.custom.css', null, $this->application ()->version () );
		//wp_register_style ( 'v45v_style_css', $this->application ()->pluginuri () . '/library/public/css/style.css', null, $this->application ()->version () );
		//wp_register_script ( 'v45v_script_js', $this->application ()->pluginuri () . '/library/public/js/script.js', null, $this->application ()->version () );
		wv45v_data_settings::setup ( ($this->application ()->data('_debug_settings')->debug_settings['settings']!="" || $this->dodebug()) );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function admin_enqueue_scriptsWPaction() {
		wp_enqueue_style ( 'v45v_images' );
		wp_enqueue_style ( 'v45v_admin' );
		wp_enqueue_style ( 'v45v_common' );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function wp_enqueue_scriptsWPaction() {
		wp_enqueue_style ( 'v45v_images' );
		wp_enqueue_style ( 'v45v_front' );
		wp_enqueue_style ( 'v45v_common' );
	}
/*	public function wp_enqueue_scriptsWPaction() {
		wp_enqueue_style ( 'jquery-ui_smoothness' );
		wp_enqueue_style ( 'v45v_style_css' );
		wp_enqueue_script ( 'jquery' );
		wp_enqueue_script ( 'jquery-ui-sortable' );
		wp_enqueue_script ( 'jquery-form' );
		wp_enqueue_script ( 'jquery-ui-dialog' );
		wp_enqueue_script ( 'v45v_script_js' );
		$data = array ('dodebug' => $this->dodebug () );
		wp_localize_script ( 'v45v_script_js', 'v45v_data', $data );
	}
*/
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function user_can_richeditWPaction($value) {
		if (get_post_type () == 'dcoda_settings') {
			$value = false;
		}
		return $value;
	}
}