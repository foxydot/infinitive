<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class bv46v_data_table {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_tbl = null;
	public function where($where)
	{
		if(!empty($where))
		{
			return " WHERE {$where}";
		}
		return '';
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function order_by($by)
	{
		if(!empty($by))
		{
			return " ORDER BY {$by}";
		}
		return '';
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function date($time = null) {
		if (null === $time) {
			$time = time ();
		}
		return date ( 'Y-m-d G:i:s', $time );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function alter_table($key, $type) {
		$sql = "ALTER TABLE `%s` ADD COLUMN `%s` %s";
		$sql = sprintf ( $sql, $this->name (), $key, $type );
		$return = $this->execute ( $sql );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function field_name(&$field,$size=60) {
		$this->object_name($field,$size);
	}
	public function object_name(&$field,$size=60) {
		$field = $this->swap_special ( $field );
		$field = $this->strip_tags ( $field );
		$field = $this->strip_special ( $field );
		$field = substr($field,0,$size);
		//$field = $this->special_trim ( $field );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function addslashes(&$data) {
		array_walk_recursive ( $data, array ($this, 'addslashes_callback' ) );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function addslashes_callback(&$value, $key) {
		if (is_string ( $value )) {
			$value = addslashes ( $value );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function stripslashes(&$data) {
		array_walk_recursive ( $data, array ($this, 'stripslashes_callback' ) );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function stripslashes_callback(&$value, $key) {
		if (is_string ( $value )) {
			$value = stripslashes ( $value );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function set_tbl($value) {
		$prefix = $this->prefix ();
		// allow for passing of a full table name
		if (! empty ( $prefix ) && strpos ( $value, $prefix ) === 0) {
			$value = substr ( $value, strlen ( $prefix ) );
		}
		$this->_tbl = $value;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected function tbl() {
		return $this->_tbl;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_host = null;
	protected function set_host($value) {
		$this->_host = $value;
	}
	protected function host() {
		return $this->_host;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_user = null;
	protected function set_user($value) {
		$this->_user = $value;
	}
	protected function user() {
		return $this->_user;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_pwd = null;
	protected function set_pwd($value) {
		$this->_pwd = $value;
	}
	protected function pwd() {
		return $this->_pwd;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_db = null;
	protected function set_db($value) {
		$this->_db = $value;
	}
	protected function db() {
		return $this->_db;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_prefix = null;
	protected function set_prefix($value) {
		$this->_prefix = $value;
	}
	protected function prefix() {
		return $this->_prefix;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_server_connection = null;
	private $_db_connection = null;
	protected function connect() {
		if (null === $this->_server_connection) {
			$this->_server_connection = mysql_connect ( $this->host (), $this->user (), $this->pwd () );
			if (! $this->_server_connection) {
				throw new exception ( 'Could not connect: ' . mysql_error () );
			}
		}
		if (null !== $this->db ()) {
			if (null === $this->_db_connection) {
				$this->_db_connection = mysql_select_db ( $this->db (), $this->_server_connection );
				if (! $this->_db_connection) {
					throw new exception ( "Can't use {$this->_connected_db} : " . mysql_error () );
				}
			}
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function name() {
		return $this->prefix () . $this->tbl ();
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __construct($tbl = null) {
		$this->set_tbl ( $tbl );
		$this->connect ();
	}
	// not null operator
	// quick check to see if a value was passed and if so inculde the operator before it
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function nn_operator($operator, $value) {
		$return = '';
		if (null !== $value) {
			$return = " {$operator} '$value'";
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function strip_special($string) {
		$string = urlencode ( $string );
		$pattern = '|%[0-9a-fA-F][0-9a-fA-F]|Ui';
		$safe = array ('[', ']' ,'$');
		foreach ( $safe as $value ) {
			$string = str_replace ( urlencode ( $value ), $value, $string );
		}
		$string = preg_replace ( $pattern, '', $string );
		$string = urldecode ( $string );
		return trim ( $string );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function strip_tags($string) {
		$pattern = '|\<.*\>|Ui';
		$string = preg_replace ( $pattern, '', $string );
		return trim ( $string );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function swap_special($string) {
		$chars = array ('Ð' => '-', 'Á' => '!', 'À' => '?', 'Ò' => '"', 'Ó' => '"', 'Ô' => "'", 'Õ' => "'", 'Ç' => '"', 'È' => '"', '&' => '+', '¢' => 'c', '©' => '(c)', 'µ' => 'u', 'á' => '.', '¦' => '|', '±' => '+-', 'Û' => 'e', '¨' => '(r)', 'ª' => ' TM ', '´' => 'y', '‡' => 'a', 'ç' => 'A', 'ˆ' => 'a', 'Ë' => 'A', '‰' => 'a', 'å' => 'A', 'Œ' => 'a', '' => 'A', '‹' => 'a', 'Ì' => 'A', 'Š' => 'a', '€' => 'A', '¾' => 'ae', '®' => 'AE', '' => 'c', '‚' => 'C', 'Ž' => 'e', 'ƒ' => 'E', '' => 'e', 'é' => 'E', '' => 'e', 'æ' => 'E', '‘' => 'e', 'è' => 'E', '’' => 'i', 'ê' => 'I', '“' => 'i', 'í' => 'I', '”' => 'i', 'ë' => 'I', '•' => 'i', 'ì' => 'I', '–' => 'n', '„' => 'N', '—' => 'o', 'î' => 'O', '˜' => 'o', 'ñ' => 'O', '™' => 'o', 'ï' => 'O', '¿' => 'o', '¯' => 'O', '›' => 'o', 'Í' => 'O', 'š' => 'o', '…' => 'O', '§' => 'B', 'œ' => 'u', 'ò' => 'U', '' => 'u', 'ô' => 'U', 'ž' => 'u', 'ó' => 'U', 'Ÿ' => 'u', '†' => 'U', 'Ø' => 'u' );
		foreach ( $chars as $key => $value ) {
			$string = str_replace ( $key, $value, $string );
		}
		return trim ( $string );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
// redundent
/*
	private function special_trim($string) {
		$pattern = '|\[.*\]|Ui';
		preg_match_all ( $pattern, $string, $matches, PREG_SET_ORDER );
		$second = "";
		if (count ( $matches ) > 0) {
			$second = $matches [count ( $matches ) - 1] [0];
			$second = ltrim ( $second, '[' );
			$second = rtrim ( $second, ']' );
			$second = trim ( $second );
		}
		if ($second != '') {
			$first = substr ( $string, 0, strrpos ( $string, $second ) - 1 );
			$first = trim ( substr ( $first, 0, 29 ) );
			$second = trim ( substr ( $second, 0, 29 ) );
			$string = "{$first}[{$second}]";
		} else {
			$string = substr ( $string, 0, 60 );
		}
		return trim ( $string );
	}*/
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function nn_from($value) {
		$return = '';
		if (null === $value) {
			$value = $this->name ();
		}
		$return = " FROM `$value`";
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function execute($sql, $field = null) {
		$result = mysql_query ( $sql );
		if (! $result) {
			$message = 'Invalid query: ' . mysql_error () . "\n";
			$message .= 'Whole query: ' . $sql;
			die ( $message );
		}
		if (gettype ( $result ) == 'resource') {
			$return = array ();
			while ( $row = mysql_fetch_array ( $result, MYSQL_ASSOC ) ) {
				if (null !== $field) {
					if (is_numeric ( $field )) {
						$keys = array_keys ( $row );
						$field = $keys [$field];
					}
					$return [] = $row [$field];
				} else {
					$return [] = $row;
				}
			}
			mysql_free_result ( $result );
			return $return;
		}
		return $result;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function show_tables($like = null, $add_prefix = true) {
		if ($add_prefix) {
			$like = $this->prefix () . $like . '%%';
		}
		$like = $this->nn_operator ( 'LIKE', $like );
		$sql = "SHOW TABLES%s;";
		$sql = sprintf ( $sql, $like );
		$return = $this->execute ( $sql, 0 );
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function show_columns($from = null, $like = null) {
		$from = $this->nn_from ( $from );
		$like = $this->nn_operator ( 'LIKE', $like );
		$sql = "SHOW COLUMNS%s%s;";
		$sql = sprintf ( $sql, $from, $like );
		$return = $this->execute ( $sql );
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function create_table($fields, $keys) {		
		$sql = "CREATE TABLE IF NOT EXISTS %s (\n\t%s \n)";
		$lines = array ();
		foreach ( ( array ) $fields as $field_name => $field_def ) {
			$line = "`%s` %s %s %s";
			$null = 'NULL';
			if (array_key_exists ( 'null', $field_def ) && ! $field_def ['null']) {
				$null = "NOT NULL";
			}
			$extra = '';
			if (array_key_exists ( 'extra', $field_def )) {
				$extra = $field_def ['extra'];
			}
			$line = sprintf ( $line, $field_name, $field_def ['type'], $null, $extra );
			$lines [] = trim ( $line );
		}
		if (array_key_exists ( 'primary', $keys )) {
			$lines [] = sprintf ( "PRIMARY KEY (`%s`)", $keys ['primary'] );
		}
		$lines = implode ( ",\n\t", $lines );
		$sql = sprintf ( $sql, $this->name (), $lines );
		$this->execute ( $sql );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function exists($name = null) {
		if (null === $name) {
			$name = $this->name ();
		}
		$check = $this->show_tables ( $name, false );
		return (count ( $check ) > 0);
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function count($name = null) {
		if (null === $name) {
			$name = $this->name ();
		}
		$sql = "SELECT count(*) as 'count' FROM `%s`;";
		$sql = sprintf ( $sql, $name );
		$return = $this->execute ( $sql );
		foreach ( $return as $return ) {
			return $return ['count'];
		}
		return null;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function drop($table = null) {
		if (null === $table) {
			$table = $this->name ();
		}
		$sql = "DROP TABLE IF EXISTS `%s`;";
		$sql = sprintf ( $sql, $table );
		$return = $this->execute ( $sql );
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function truncate($table = null) {
		if (null === $table) {
			$table = $this->name ();
		}
		$sql = "TRUNCATE TABLE `%s`;";
		$sql = sprintf ( $sql, $table );
		$return = $this->execute ( $sql );
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function insert($data) {
		$fields = array ();
		$values = array ();
		foreach ( ( array ) $data as $key => $value ) {
			$fields [] = "`$key`";
			$values [] = "'" . addslashes ( $value ) . "'";
		}
		$sql = "INSERT INTO `" . $this->name () . "`\n";
		$fields = implode ( ',', $fields );
		if ($fields != '') {
			$values = implode ( ',', $values );
			$sql .= "(" . $fields . ")\n";
			$sql .= "values (" . $values . ")\n";
			$return = $this->execute ( $sql );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function bulk_insert($data) {
		set_time_limit ( 500 );
		foreach ( $data as $line ) {
			$this->insert ( $line );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function first_column($data) {
		$return = array ();
		foreach ( $data as $datum ) {
			foreach ( $datum as $value ) {
				$return [] = $value;
				break;
			}
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function first_row($data) {
		$return = false;
		foreach ( $data as $datum ) {
			$return = $datum;
		}
		return $return;
	}
}