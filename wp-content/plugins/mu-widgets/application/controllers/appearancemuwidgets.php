<?php
class appearancemuwidgets extends wv28v_controller_action_adminmenu {
	public function controller_meta()
	{
		$return = parent::controller_meta();
		$return['menu'] = 'Appearance';
		return $return;
	}
	public function getting_startedAction($content)
	{
		return $content;
	}
}
		