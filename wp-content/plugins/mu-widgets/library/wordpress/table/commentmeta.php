<?php
class wv28v_table_commentmeta extends wv28v_table {
	public function name() {
		return $this->wpdb ()->commentmeta;
	}
}