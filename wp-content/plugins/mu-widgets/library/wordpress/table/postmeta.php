<?php
class wv28v_table_postmeta extends wv28v_table {
	public function __construct($blog_id = null) {
		parent::__construct ( $blog_id, 'postmeta' );
	}
}
