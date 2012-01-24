<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class bv45v_data_form extends bv45v_base {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private static $_active = 'default';
	public function active($name=null)
	{
		if(null!==$name)
		{
			self::$_active = $name;
		}
		return self::$_active;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __construct($application,$name=null)
	{
		if(null===!$name)
		{
			$name='default';
		}
		$this->_form = $name;
		parent::__construct($application);
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_form = null;
	public function form()
	{
		return $this->_form;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function forms()
	{
		$options = $this->data()->options();
		$tables = $this->table()->show_tables("{$this->application()->slug}_%");
		$forms = array();
		$new = array('name'=>null,'table'=>null,'count'=>'');
		foreach($options as $option)
		{
			$forms[$option] = $new;
			$forms[$option]['name'] = $option;
		}
		foreach($tables as $table)
		{
			$option = $this->table_name_to_option($table);
			$forms[$option] = $new;
			$forms[$option]['table'] = $table;
			$forms[$option]['count'] = $this->table($table)->count();
		}
		return $forms;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function table_name_to_option($table)
	{
		$return = explode("{$this->application()->slug}_",$table);
		return $return[1];
	}
}