<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class wv47v_default extends wv47v_action {
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
		wp_register_style ( 'v47v_images', $this->application ()->pluginuri () . '/library/base/public/css/images.css', null, $this->application ()->version () );
		wp_register_style ( 'v47v_admin', $this->application ()->pluginuri () . '/library/base/public/css/admin.css', null, $this->application ()->version () );
		wp_register_style ( 'v47v_front', $this->application ()->pluginuri () . '/library/base/public/css/front.css', null, $this->application ()->version () );
		wp_register_style ( 'v47v_common', $this->application ()->pluginuri () . '/library/base/public/css/common.css', null, $this->application ()->version () );
		//wp_register_style ( 'jquery-ui_smoothness', $this->application ()->pluginuri () . '/library/public/css/smoothness/jquery-ui-1.8.13.custom.css', null, $this->application ()->version () );
		wp_register_script ( 'v47v_script_js', $this->application ()->pluginuri () . '/library/base/public/js/script.js', null, $this->application ()->version () );
		wv47v_data_settings::setup ($this->application(), ($this->application ()->data('_debug_settings')->debug_settings['settings']!="" || $this->dodebug()) );
		
		// finish here if the settings do not need to be copied to the db
		if($this->application()->force_to_db===false)
			return;
		$forms = $this->data()->options(false,false,true);
		$key = md5(serialize(array('v47v'=>$forms)));
		$old_key = get_option("{$this->application()->slug}_forms");
		// finish here if the version and the forms are the same as before
		if($key === $old_key && !$this->dodebug())
			return;
		foreach($forms as $form)
		{
			$data = $this->data ($form)->data();
			// check to see if data is already in the table.
			// and check it was written with the current version
			if(!isset($data['__version__v47v__']))
			{
				$this->data ($form)->writePost ( $data);
			}
			update_option("{$this->application()->slug}_forms",$key);
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function admin_enqueue_scriptsWPaction() {
		wp_enqueue_style ( 'v47v_images' );
		wp_enqueue_style ( 'v47v_admin' );
		wp_enqueue_style ( 'v47v_common' );
		$data = array ();
		$data['dodebug'] = $this->dodebug ();
		$data = apply_filters('v47v_data',$data);
		if(isset($data['tinymce']))
		{
			$data['tinymce']['plugin']['name']=$this->application()->slug;
			wp_enqueue_script ( 'jquery' );
			wp_enqueue_script ( 'v47v_script_js' );
			wp_localize_script ( 'v47v_script_js', 'v47v_data', $data );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function wp_enqueue_scriptsWPaction() {
		wp_enqueue_style ( 'v47v_images' );
		wp_enqueue_style ( 'v47v_front' );
		wp_enqueue_style ( 'v47v_common' );
	}
/*	public function wp_enqueue_scriptsWPaction() {
		wp_enqueue_style ( 'jquery-ui_smoothness' );
		wp_enqueue_style ( 'v47v_style_css' );
		wp_enqueue_script ( 'jquery' );
		wp_enqueue_script ( 'jquery-ui-sortable' );
		wp_enqueue_script ( 'jquery-form' );
		wp_enqueue_script ( 'jquery-ui-dialog' );
		wp_enqueue_script ( 'v47v_script_js' );
		$data = array ('dodebug' => $this->dodebug () );
		wp_localize_script ( 'v47v_script_js', 'v47v_data', $data );
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