<?php
class wv28v_controller_action_pluginsandbox extends wv28v_controller_action_adminmenu {
	public function controller_meta() {
		$return = parent::controller_meta();
		$return['menu'] = 'Sandbox';
		return $return;
	}
	public function DiagActionMeta($return)
	{
		if(isset($_GET['setting']))
		{
			$return['title'] = 'Settings&raquo;Diagnostics&raquo;'.$_GET['setting'];
		}
		$return['hide'] = true;
		$return['priority']=99;
		return $return;
	}
	private function new_optionsAction($content)
	{
		$this->debug($this->settings()->option());
		$this->debug($this->settings()->option('copy'));
		$this->debug($this->settings()->option());
		return $content;
	}
	public function DiagAction($content)
	{
		$page='';
		$this->settings()->set_options_name($_GET['setting']);
		$diag=array();
		$option='generate';
		if(isset($_POST['select']))
		{
			$option=$_POST['select'];
		}
		$diag=array();
		switch($option)
		{
			case 'generate':
				$t=mysql_query("select version() as ve");
				echo mysql_error();
				$r=mysql_fetch_object($t);
				$diag['application']['name']=$this->settings()->application['name'];
				$diag['application']['version']=$this->settings()->application['version'];
				$diag['application']['option_info']=$this->settings()->options_name(false);
				$diag['php']['version']=phpversion();
				$diag['wp']['version']=get_bloginfo( 'version' );
				$diag['mysql']['version']=$r->ve;
				if(isset($_POST['settings']['changed']))
				{
					$diag['saved_changes']=$this->settings()->get();
				}
				if(isset($_POST['settings']['all']))
				{
					$diag['full_settings']=$this->settings()->all();
				}
				if(isset($_POST['settings']['php']))
				{
					ob_start();
					phpinfo();
					$diag['phpinfo'] = ob_get_contents ();
					ob_end_clean ();
				}
				break;
			case 'decode':
				$tag = bv28v_tag::instance ();
				$diags = $tag->get('diag',$_POST ['diag_data'],true);
				if(count($diags)>0)
				{
					foreach($diags as $diag)
					{
						$diag=$this->settings()->decode($diag['innerhtml']);
						break;
					}
				}
				break;
			case 'import':
				$tag = bv28v_tag::instance ();
				$diags = $tag->get('diag',$_POST ['diag_data'],true);
				if(count($diags)>0)
				{
					foreach($diags as $diag)
					{
						$diag=$this->settings()->decode($diag['innerhtml']);
						break;
					}
				}
				if(isset($diag['saved_changes']))
				{
					$this->settings()->set_options_name($diag['application']['option_info']['sub']);
					$this->settings()->set($diag['saved_changes']);
				}
				break;
		}
		$this->view->diag_info="[diag]".$this->settings()->encode($diag)."[/diag]";
		$this->view->diag_show=print_r($diag,true);
		$this->view->settings = array();
		$page = $this->render_script ( 'common/diag.phtml',false );
		return $content.$page;
	}
	public function settings_InstructionTitle() {
	}
	public function settings_InstructionColumn() {
	}
	public function settings_actionTitle() {
	}
	public function settings_actionColumn() {
	}
	public function SettingsAction($content)
	{
		if(!isset($this->view->obj_type))
		{
			$this->view->obj_type='setting';
		}
		if(isset($_POST['delete_setting']))
		{
			$this->settings()->delete($_POST['delete_setting']);
		}
		if(isset($_POST['delete_table']))
		{
			$this->settings()->delete_table($_POST['delete_table']);
		}
		if(isset($_POST['new']) && !empty($_POST['new']))
		{
			$this->settings()->copy($_POST['new'],$_POST['source_setting']);
		}
		$url_parts = $this->view->get_url_parts();
		$this->view->table_url = $this->control_url ( '/' . $this->settings()->application['name'] . '/');
		$base_parts = $url_parts;
		unset($base_parts['query']['page2']);
		$this->view->base_url=$this->view->url($base_parts);
		$this->view->locations=$this->settings()->saved_data_locations();	
		$page = $this->render_script ( 'common/saveddata.phtml' );
		$page =  $this->updated ( 'Settings Updated' ) . $page;
		return $content.$page;
	}
}
		