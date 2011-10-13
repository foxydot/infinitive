<?php
class muwidgetsfilters extends wv28v_controller_action_filter {
	protected function plugin_links()
	{
		$return = parent::plugin_links();
		$return['gettingstarted'] = array('url'=>$this->dashboard_url('Appearance',$this->settings()->application['name']),'text'=>'Getting Started');
		return $return;
	}
}
		