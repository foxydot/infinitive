<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class wv45v_data_legacy extends bv45v_base {
	public function __construct(&$application) {
		parent::__construct ( $application );
		if (count ( $application->data ()->options ( false, true ) ) == 0) {
			$this->move ();
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function move() {
	
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function data() {
		$data = array ();
		if(get_option('updated_'.$this->application()->slug)!='done')
		{
			$sql = "SELECT `option_name`,`option_value` from `%s` WHERE option_name LIKE  '%s_%%';";
			$sql = sprintf ( $sql, $this->table ( 'options' )->name (), $this->application ()->slug );
			$results = $this->table ()->execute ( $sql );
			$len = strlen ( $this->application ()->slug ) + 1;
			foreach ( $results as $key => $value ) {
				$new_key = substr ( $value ['option_name'], $len );
				$data [$new_key] = $value ['option_value'];
				$data [$new_key] = base64_decode ( $data [$new_key] );
					$data [$new_key] = @gzuncompress ( $data [$new_key] );
				$data [$new_key] = unserialize ( $data [$new_key] );
				unset ( $results [$key] );
			}
			update_option('updated_'.$this->application()->slug,'done');
		}
		return $data;
	}
}