<?php
class wv44v_data_table extends bv44v_data_table {
	private $blog_id = null;
	public function __construct($tbl = null, $blog_id = null) {
		global $wpdb;
		$this->blog_id = $blog_id;
		$this->set_host ( DB_HOST );
		$this->set_db ( DB_NAME );
		$this->set_user ( DB_USER );
		$this->set_pwd ( DB_PASSWORD );
		parent::__construct ( $tbl );
	}
	protected function prefix() {
		if (null === parent::prefix ()) {
			global $wpdb;
			$this->set_prefix ( $wpdb->prefix );
			//$this->set_prefix ( $wpdb->get_blog_prefix ( $blog_id ) );
		}
		return parent::prefix ();
	}
	
	protected function connect() {
	}
	public function name() {
		global $wpdb;
		$tbl = $this->tbl ();
		if (in_array ( $tbl, $wpdb->tables ) || in_array ( $tbl, $wpdb->global_tables ) || in_array ( $tbl, $wpdb->ms_global_tables )) {
			$return = $wpdb->$tbl;
		} else {
			$return = parent::name ();
		}
		return $return;
	}
}