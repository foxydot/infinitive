<?php
class bv44v_data_sqlite {
	private $_connection = null;
	public function open($db_file)
	{
		$this->_connection = new PDO ( "sqlite:" . $db_file );
	}
	public function execute($sql) {
		$result = false;
		$return = $this->_connection->query ( $sql );
		if($return !==false)
		{
			$result = array();
			foreach($return as $key=>$value)
			{
				$result[$key]=$value;
			}
		}
		return $result;
	}
}