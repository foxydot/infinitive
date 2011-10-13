<?php
class wv28v_settings extends bv28v_settings {
	protected function set_lib_settings_locations()
	{
		$array = parent::set_lib_settings_locations();
		$array[]='/library/wordpress/wordpress.xml';
		return $array;
	}
	protected $options_name=null;
	public function set_options_name($sub='default',$base=null,$refresh=true)
	{
		if(null===$base)
		{
			$base='';
			if(isset($this->options_name['base']))
			{
				$base=$this->options_name['base'];
			}
			else
			{
				$base=$this->application['slug'];
			}
		}
		$this->options_name=array('base'=>$base,'sub'=>$sub);
		if($refresh)
		{
			$this->refresh();
		}
	}
	public function option($option=null,$refresh=true)
	{
		if(null!==$option)
		{
			$this->set_options_name($option,null,$refresh);
			$return = $option;	
		}
		else
		{
			$return = $this->options_name(false);
			$return = $return['sub'];
		}
		return $return;
	}
	public function options_name($flat=true)
	{
		if(null===$this->options_name)
		{
			$this->set_options_name();
		}
		$option = $this->options_name;
		if($flat)
		{
			$option=implode('_',$this->options_name);
		}
		return $option;
	}
	protected $saved_data_locations = null;
	public function special_setting($name)
	{
		return (strpos($name,'_')===0);
	}
	public function saved_data_locations($add_default=true)
	{
		if (is_null ( $this->saved_data_locations )) {
			$type = $this->options_name(false);
			$type = $type['base'];
			$options = new wv28v_table_options ($type);
			$sql = "SELECT `option_name`%s WHERE option_name LIKE  '%s_%%' AND option_value != '%s';";
			$from = $options->name ();
			$options->from ( $from );
			$sql = sprintf ( $sql, $from, $type, $type );
			$results = $options->execute ( $sql );
			$saved_options = array ();
			foreach ( $results as $key => $value ) {
				$new_form = substr ( $value ['option_name'], strlen ( $type ) + 1 );
				$saved_options [$new_form] = $new_form;
			}
			$return= array ();
			foreach($saved_options as $sub)
			{
				if(!$this->special_setting($sub) || $this->dodebug())
				{
					$return[' '.$sub]=array('name'=>$sub,'table'=>'','records'=>'','table_name'=>'');
				}
			}
			if($add_default)
			{
				$return[' default']['name']='default';
				$return[' default']['table']='';
				$return[' default']['records']='';
				$return[' default']['table_name']='';
			}
			$table = new wv28v_table (null, strtolower ( $this->settings ()->application['name'] ));
			$tables = $table->list_tables ();
			foreach($tables as $table)
			{
				//$table['url'] = $this->control_url ( '/' . $this->settings->type() . '/' . $table ['name'] . '.csv' );
				if(isset($return[' '.$table['name']]))
				{
					$return[' '.$table['name']]=$table;
					$return[' '.$table['name']]['table_name']=$table['name'];
				}			
				else
				{
					$table_name=$table['name'];
					$table['name']='';
					$return[$table['table']]=$table;
					$return[$table['table']]['table_name']=$table_name;
				}
			}
			ksort($return);
			$this->saved_data_locations=$return;
		}
		return $this->saved_data_locations;
	}
	public function all()
	{
		if(null===$this->_settings)
		{
			parent::all();
			$options_name=$this->options_name(false);
			$this->set_options_name('_global',null,false);
			$options=$this->get();
			if(is_array($options))
			{
				$this->_settings=bv28v_type_array::merge_replace_recursive($this->_settings,$options);
			}
			$this->set_options_name($options_name['sub'],null,false);
			$options=$this->get();
			if(is_array($options))
			{
				$this->_settings=bv28v_type_array::merge_replace_recursive($this->_settings,$options);
			}
			$this->_settings=$this->legacy($this->_settings);
		}
		return $this->_settings;
	}
	protected function legacy($data)
	{
		return $data;
	}
	protected function legacy_move($old,$section=null,$new=null)
	{
		$options_name = null;
		if(null!==$new)
		{
			$options_name=$this->options_name(false);
			$this->set_options_name($new);
		}
		$old_data=$this->decode(get_option ( $old ));
		if (is_array ( $old_data )) {
			$new_data=$this->decode(get_option ( $this->options_name() ));
			if(null!==$section)
			{
				if(!isset($old_data[$section]))
				{
					foreach($old_data as $key=>$value)
					{
						unset($new_data[$key]);
					}
					$old_data=array($section=>$old_data);
				}
			}
			if(!empty($new_data))
			{
				$new_data = bv28v_type_array::merge_replace_recursive($new_data,$old_data);
			}
			else
			{
				$new_data=$old_data;
			}
			if($old!= $this->options_name())
			{
				delete_option ( $old );
			}
			$this->set($new_data);
		}
		if(null!==$options_name)
		{
			$this->set_options_name($options_name['sub']);
		}
	}
	public function prepare(&$value) {
		$value = stripslashes($value);
	}
	protected function prepare_data($data)
	{
		return $data;
	}
	public function encode($data)
	{
		$data = serialize($data);
		$data = gzcompress($data,9);
		$data = base64_encode($data);
		return $data;
	}
	public function copy($to,$from=null)
	{
		if(null!==$from)
		{
			$this->set_options_name($from);
		}
		$data=$this->get();
		$this->set_options_name($to);
		$this->set($data);
	}
	public function decode($data)
	{
		if(!is_array($data) && !empty($data))
		{
			$data = base64_decode($data);
			$data = gzuncompress($data);
			$data = unserialize($data);
		}
		if(is_array($data))
		{
			array_walk_recursive ( $data, array ($this, 'prepare' ) );
		}
		return $data;
	}
	public function get()
	{
		$data = get_option($this->options_name());
		$data = $this->decode($data);
		return $data;
	}
	public function set($data,$option=null)
	{
		$olddata=$data;
		if(null!==$option)
		{
			$olddata = $this->get();
			if(!isset($data[$option]))
			{
				$data[$option]='';	
			}
			$olddata[$option]=$data[$option];
		}
		$data = $this->encode($olddata);
		update_option($this->options_name(),$data);
	}
	public function delete($options=null)
	{
		if(null===$options)
		{
			delete_option($this->options_name());
		}
		else
		{
			$options_name=$this->options_name(false);
			foreach((array)$options as $option)
			{
				$options_name['sub']=$option;
				delete_option(implode('_',$options_name));
			}
		}
		$this->refresh();
	}
	public function delete_table($tables)
	{
		$tableObj = new wv28v_table ();
		foreach((array)$tables  as $table)
		{
			$tableObj->drop($table);
		}
	}
	public function post($option=null)
	{
		if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
			$post=$this->prepare_data($_POST);
			$this->set($post,$option);
			$this->refresh();
		}
		$return = $this->all();
		if(null!==$option)
		{
			$return = $return[$option];
		}
		return $return;
	}
}