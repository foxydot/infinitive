<?php
abstract class wv28v_controller_action_filter extends wv28v_controller_action_abstract {
	public function __construct($application) {
		$this->set_type ( self::WP_FILTER );
		parent::__construct ( $application );
	}
	public function plugin_action_linksAction($links, $file) {
		if ($file != plugin_basename ( $this->application ()->filename () )) {
			return $links;
		}
		foreach($this->plugin_links() as $link)
		{
			if(null!==$link)
			{
				$title = $link['text'];
				if(isset($link['title']))
				{
					$title = $link['title'];
				}
				$link_url = '<a href="'.$link['url'] . '" title="'.$title.'">'.$link['text'].'</a>';
				array_unshift ( $links, $link_url );
			}
		}
		return $links;
	}
	protected function plugin_links()
	{
		$options = $this->settings ()->application;
		$return = array();
		$menu = $this->find_submenu('Settings',$options['name']);
		if(null!==$menu)
		{
			$url = $this->dashboard_url('Settings',$options['name']);
			$return['settings'] = array('url'=>$url,'text'=>$menu['3']);
		}
		if (! empty ( $options ['donate_link'] )) {
			$return['donate'] = array('url'=>$options ['donate_link'],'title'=>"Help support the development of this plugin",'text'=>'Donate');
		}
		return $return;
	}
	public function setup() {
		foreach ( ( array ) $this->actions () as $action ) {
			$numargs = 5;
			add_filter ( $action ['raw_title'], array ($this, "controller" ), $action ['priority'], $numargs );
		}
	}
}