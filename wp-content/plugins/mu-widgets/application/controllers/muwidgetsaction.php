<?php
class muwidgetsaction extends wv44v_action {
	public function muwidgetsWPmenuMeta($return) {
		$return ['menu'] = '';
		$return ['title'] = $this->application()->name;
		return $return;
	}
	public function widgets_initWPaction() {
		$this->application ()->loader ()->load_class ( 'muwarchives' );
		register_widget ( 'muwarchives' );
		$this->application ()->loader ()->load_class ( 'muwcalendar' );
		register_widget ( 'muwcalendar' );
		$this->application ()->loader ()->load_class ( 'muwcategories' );
		register_widget ( 'muwcategories' );
		$this->application ()->loader ()->load_class ( 'muwlinks' );
		register_widget ( 'muwlinks' );
		$this->application ()->loader ()->load_class ( 'muwmeta' );
		register_widget ( 'muwmeta' );
		$this->application ()->loader ()->load_class ( 'muwnavmenuwidget' );
		register_widget ( 'muwnavmenuwidget' );
		$this->application ()->loader ()->load_class ( 'muwpages' );
		register_widget ( 'muwpages' );
		$this->application ()->loader ()->load_class ( 'muwrecentcomments' );
		register_widget ( 'muwrecentcomments' );
		$this->application ()->loader ()->load_class ( 'muwrecentposts' );
		register_widget ( 'muwrecentposts' );
		$this->application ()->loader ()->load_class ( 'muwsearch' );
		register_widget ( 'muwsearch' );
		$this->application ()->loader ()->load_class ( 'muwtagcloud' );
		register_widget ( 'muwtagcloud' );
	}
	public function widgetsActionMeta($return) {
		$return ['link_name'] = $return ['title'];
		$return ['url'] = $this->dashboard('Appearance','Widgets')->url;
		$return ['classes'] [] = 'v44v_icon16x16';
		$return ['classes'] [] = 'v44v_icon16x16_settings';
		$return ['priority'] = -1;
		return $return;
	}

}
		