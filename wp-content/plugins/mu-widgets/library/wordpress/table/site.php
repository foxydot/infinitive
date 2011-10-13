<?php
class wv28v_table_site extends wv28v_table {
	public function __construct($id = null) {
		parent::__construct ();
		if (null === $id) {
			$id = $this->wpdb ()->siteid;
		}
		$this->set_id ( $id );
	}
	private $_id = null;
	protected function id() {
		return $this->_id;
	}
	protected function set_id($id) {
		$this->_id = $id;
	}
	private $_old_id = null;
	public function swap() {
		if (null !== $this->id ()) {
			if (null === $this->_old_id) {
				$this->_old_id = $this->wpdb ()->siteid;
				$this->wpdb ()->siteid = $this->id ();
			} else {
				$this->wpdb ()->siteid = $this->_old_id;
				$this->_old_id = null;
			}
		}
	}
}