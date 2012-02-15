<?php
class muwidgets_actions extends wv47v_action {
	public function widgets_initWPaction() {
		if(is_multisite())
		{
			$this->application ()->loader ()->load_class ( 'muwidgetsarchives' );
			register_widget ( 'muwidgetsarchives' );
			$this->application ()->loader ()->load_class ( 'muwidgetscalendar' );
			register_widget ( 'muwidgetscalendar' );
			$this->application ()->loader ()->load_class ( 'muwidgetscategories' );
			register_widget ( 'muwidgetscategories' );
			$this->application ()->loader ()->load_class ( 'muwidgetslinks' );
			register_widget ( 'muwidgetslinks' );
			$this->application ()->loader ()->load_class ( 'muwidgetsmeta' );
			register_widget ( 'muwidgetsmeta' );
			$this->application ()->loader ()->load_class ( 'muwidgetsnavmenu' );
			register_widget ( 'muwidgetsnavmenu' );
			$this->application ()->loader ()->load_class ( 'muwidgetspages' );
			register_widget ( 'muwidgetspages' );
			$this->application ()->loader ()->load_class ( 'muwidgetsrecentcomments' );
			register_widget ( 'muwidgetsrecentcomments' );
			$this->application ()->loader ()->load_class ( 'muwidgetsrecentposts' );
			register_widget ( 'muwidgetsrecentposts' );
			$this->application ()->loader ()->load_class ( 'muwidgetssearch' );
			register_widget ( 'muwidgetssearch' );
			$this->application ()->loader ()->load_class ( 'muwidgetstagcloud' );
			register_widget ( 'muwidgetstagcloud' );
		}
		elseif(!$this->dodebug())
		{
			if (! function_exists ( 'deactivate_plugins' )) {
				@include_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			if (function_exists ( 'deactivate_plugins' )) {
				deactivate_plugins ( $this->application()->file () );
			}
		}
	}
}
		