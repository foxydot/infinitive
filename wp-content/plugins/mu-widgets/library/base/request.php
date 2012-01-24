<?php
class bv44v_request extends bv44v_base {
	public function is_post()
	{
		return ($_SERVER ['REQUEST_METHOD'] == 'POST');
	}
}