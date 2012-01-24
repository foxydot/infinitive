<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class wv45v_data_form extends wv45v_action {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private static $_active = 'default';
	public function active($name = null) {
		if (null !== $name) {
			self::$_active = $name;
		}
		return self::$_active;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __construct($application, $name = null) {
		if (null === $name) {
			$name = 'default';
		}
		$this->_form = $name;
		parent::__construct ( $application );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_form = null;
	public function form() {
		return $this->_form;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function &data() {
		return parent::data ( $this->form () );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function filter_name($name)
	{
		$name = strtolower($name);
		$return = '';
		for($i=0;$i<strlen($name);$i++)
		{
			$letter = substr($name,$i,1);
			if($letter == '_' || ($i==0 && $letter=='$'))
			{
			}
			elseif(strpos('abcdefghijklmnopqrstuvwxyz1234567890',$letter)===false || $letter=='-')
			{
				$letter = '_';
			}
			$return.=$letter;
		}
		$return = trim($return,'_');
		if(!$this->dodebug())
		{
			$return = trim($return,'$');
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function forms($show_hidden = false) {
		if ($this->request ()->is_post ()) {
			$src = '';
			$dst = '';
			$del = array ();
			$del_table = array ();
			if (isset ( $_POST ['source_setting'] )) {
				$src = $_POST ['source_setting'];
			}
			if (isset ( $_POST ['new_form'] )) {
				$dst = $_POST ['new_form'];
				$dst = $this->filter_name($dst);
			}
			if (isset ( $_POST ['delete_setting'] )) {
				$del = $_POST ['delete_setting'];
			}
			if (isset ( $_POST ['delete_table'] )) {
				$del_table = $_POST ['delete_table'];
			}
			if (! empty ( $src ) && ! empty ( $dst )) {
				$this->data ()->copy ( $dst, $src );
			}
			foreach ( $del as $d ) {
				$this->data ()->delete ( $d );
			}
			foreach ( $del_table as $d ) {
				$this->table ($d)->drop (  );
			}
		}
		$options = $this->data ()->options ( $show_hidden );
		$tables = $this->table ()->show_tables ( "{$this->application()->slug}_%" );
		$forms = array ();
		$new = array ('name' => null, 'table' => null, 'count' => '' );
		foreach ( $options as $option ) {
			$forms [$option] = $new;
			$forms [$option] ['name'] = $option;
		}
		foreach ( $tables as $table ) {
			$option = $this->table_name_to_option ( $table );
			if (! isset ( $forms [$option] )) {
				$forms [$option] = $new;
			}
			$forms [$option] ['table'] = $table;
			$forms [$option] ['count'] = $this->table ( $table )->count ();
		}
		return $forms;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function table_name_to_option($table) {
		$return = explode ( "{$this->application()->slug}_", $table );
		return $return [1];
	}
}