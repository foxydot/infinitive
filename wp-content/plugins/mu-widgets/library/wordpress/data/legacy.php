<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class wv46v_data_legacy extends bv46v_base {
	private function update_done()
	{
		$sql = "
SELECT count(*) AS 'count' FROM `%s` WHERE `post_type` = 'dc_%s';
";
		$sql = sprintf($sql,$this->table('posts')->name(),$this->application()->slug);
		$data = $this->table()->execute($sql);
		$done=false;
		foreach($data as $datum)
		{
			if($datum['count']>0)
			{
				$done = true;
			}
			break;
		}
		return $done;
	}
	public function get_post_id()
	{
		$sql = "
SELECT `ID` FROM `%s` WHERE `post_type` = 'dcoda_settings' AND `post_title` = '%s';
";
		$sql = sprintf($sql,$this->table('posts')->name(),$this->application()->slug);
		$data = $this->table()->execute($sql);
		$id=false;
		foreach($data as $datum)
		{
			$id = $datum['ID'];
			break;
		}
		return $id;

	}
	public function get_metas($post_id)
	{
		$sql = "
SELECT `meta_key` FROM `%s` WHERE `post_id` = %s AND `meta_key` != '_edit_lock' AND `meta_key` != '_edit_last';
";
		$sql = sprintf($sql,$this->table('postmeta')->name(),$post_id);
		$data = $this->table()->execute($sql);
		$metas = array();
		foreach($data as $datum)
		{
			$metas[]=$datum['meta_key'];
		}
		return $metas;
	}
	public function update()
	{
		if($this->update_done())
			return;
		$this->old_update();
		$post_id = $this->get_post_id();
		if($post_id===false)
			return;
		$metas = $this->get_metas($post_id);
		$data = array();
		$post = get_post($post_id);
		$data['default']=json_decode($post->post_content);
		foreach($metas as $meta)
		{
			$data[$meta] = get_post_meta($post_id,$meta,true);
		}
		$post = array();
		foreach($data as $key=>$value)
		{
			$post ['post_title'] = $key;
			$post ['post_name'] = md5($key.$this->application()->slug);
			$post ['post_content'] = addslashes ( json_encode ( $value ) );
			$post ['post_status'] = 'publish';
			$post ['post_type'] = 'dc_'.$this->application()->slug;
			@wp_insert_post ( $post );
		}
	}
	public function old_update() {
		if (count ( $this->application()->data ()->options ( false, true ) ) == 0) {
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