<?php
class wv44v_blogs extends bv44v_base {
	private $_old_id = array();
	public function swap($id = null,$validate=false) {
		if(is_multisite())
		{
			if (null !== $id) {
				if($id!='')
				{
					switch_to_blog($id,$validate);
				}
			} else {
				restore_current_blog();
			}
		}
	}
}