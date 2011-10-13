<?php
class wv28v_table_comments extends wv28v_table {
	public function name() {
		return $this->wpdb ()->comments;
	}
}