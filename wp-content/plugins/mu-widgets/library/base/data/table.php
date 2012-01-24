<?php
class bv44v_data_table {
	private $_tbl = null;
	public function where($where)
	{
		if(!empty($where))
		{
			return " WHERE {$where}";
		}
		return '';
	}
	public function order_by($by)
	{
		if(!empty($by))
		{
			return " ORDER BY {$by}";
		}
		return '';
	}
	public function date($time = null) {
		if (null === $time) {
			$time = time ();
		}
		return date ( 'Y-m-d G:i:s', $time );
	}
	public function alter_table($key, $type) {
		$sql = "ALTER TABLE `%s` ADD `%s` %s";
		$sql = sprintf ( $sql, $this->name (), $key, $type );
		$return = $this->execute ( $sql );
	}
	public function field_name(&$field) {
		$field = $this->swap_special ( $field );
		$field = $this->strip_tags ( $field );
		$field = $this->strip_special ( $field );
		$field = $this->special_trim ( $field );
	}
	public function addslashes(&$data) {
		array_walk_recursive ( $data, array ($this, 'addslashes_callback' ) );
	}
	public function addslashes_callback(&$value, $key) {
		if (is_string ( $value )) {
			$value = addslashes ( $value );
		}
	}
	public function stripslashes(&$data) {
		array_walk_recursive ( $data, array ($this, 'stripslashes_callback' ) );
	}
	public function stripslashes_callback(&$value, $key) {
		if (is_string ( $value )) {
			$value = stripslashes ( $value );
		}
	}
	protected function set_tbl($value) {
		$prefix = $this->prefix ();
		// allow for passing of a full table name
		if (! empty ( $prefix ) && strpos ( $value, $prefix ) === 0) {
			$value = substr ( $value, strlen ( $prefix ) );
		}
		$this->_tbl = $value;
	}
	protected function tbl() {
		return $this->_tbl;
	}
	private $_host = null;
	protected function set_host($value) {
		$this->_host = $value;
	}
	protected function host() {
		return $this->_host;
	}
	private $_user = null;
	protected function set_user($value) {
		$this->_user = $value;
	}
	protected function user() {
		return $this->_user;
	}
	private $_pwd = null;
	protected function set_pwd($value) {
		$this->_pwd = $value;
	}
	protected function pwd() {
		return $this->_pwd;
	}
	private $_db = null;
	protected function set_db($value) {
		$this->_db = $value;
	}
	protected function db() {
		return $this->_db;
	}
	private $_prefix = null;
	protected function set_prefix($value) {
		$this->_prefix = $value;
	}
	protected function prefix() {
		return $this->_prefix;
	}
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
	public function name() {
		return $this->prefix () . $this->tbl ();
	}
	public function __construct($tbl = null) {
		$this->set_tbl ( $tbl );
		$this->connect ();
	}
	// not null operator
	// quick check to see if a value was passed and if so inculde the operator before it
	public function nn_operator($operator, $value) {
		$return = '';
		if (null !== $value) {
			$return = " {$operator} '$value'";
		}
		return $return;
	}
	private function strip_special($string) {
		$string = urlencode ( $string );
		$pattern = '|%[0-9a-fA-F][0-9a-fA-F]|Ui';
		$safe = array ('[', ']' );
		foreach ( $safe as $value ) {
			$string = str_replace ( urlencode ( $value ), $value, $string );
		}
		$string = preg_replace ( $pattern, '', $string );
		$string = urldecode ( $string );
		return trim ( $string );
	}
	private function strip_tags($string) {
		$pattern = '|\<.*\>|Ui';
		$string = preg_replace ( $pattern, '', $string );
		return trim ( $string );
	}
	private function swap_special($string) {
		$chars = array ('Ð' => '-', 'Á' => '!', 'À' => '?', 'Ò' => '"', 'Ó' => '"', 'Ô' => "'", 'Õ' => "'", 'Ç' => '"', 'È' => '"', '&' => '+', '¢' => 'c', '©' => '(c)', 'µ' => 'u', 'á' => '.', '¦' => '|', '±' => '+-', 'Û' => 'e', '¨' => '(r)', 'ª' => ' TM ', '´' => 'y', '‡' => 'a', 'ç' => 'A', 'ˆ' => 'a', 'Ë' => 'A', '‰' => 'a', 'å' => 'A', 'Œ' => 'a', '' => 'A', '‹' => 'a', 'Ì' => 'A', 'Š' => 'a', '€' => 'A', '¾' => 'ae', '®' => 'AE', '' => 'c', '‚' => 'C', 'Ž' => 'e', 'ƒ' => 'E', '' => 'e', 'é' => 'E', '' => 'e', 'æ' => 'E', '‘' => 'e', 'è' => 'E', '’' => 'i', 'ê' => 'I', '“' => 'i', 'í' => 'I', '”' => 'i', 'ë' => 'I', '•' => 'i', 'ì' => 'I', '–' => 'n', '„' => 'N', '—' => 'o', 'î' => 'O', '˜' => 'o', 'ñ' => 'O', '™' => 'o', 'ï' => 'O', '¿' => 'o', '¯' => 'O', '›' => 'o', 'Í' => 'O', 'š' => 'o', '…' => 'O', '§' => 'B', 'œ' => 'u', 'ò' => 'U', '' => 'u', 'ô' => 'U', 'ž' => 'u', 'ó' => 'U', 'Ÿ' => 'u', '†' => 'U', 'Ø' => 'u' );
		foreach ( $chars as $key => $value ) {
			$string = str_replace ( $key, $value, $string );
		}
		return trim ( $string );
	}
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
	}
	public function nn_from($value) {
		$return = '';
		if (null === $value) {
			$value = $this->name ();
		}
		$return = " FROM `$value`";
		return $return;
	}
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
	public function show_columns($from = null, $like = null) {
		$from = $this->nn_from ( $from );
		$like = $this->nn_operator ( 'LIKE', $like );
		$sql = "SHOW COLUMNS%s%s;";
		$sql = sprintf ( $sql, $from, $like );
		$return = $this->execute ( $sql );
		return $return;
	}
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
	public function exists($name = null) {
		if (null === $name) {
			$name = $this->name ();
		}
		$check = $this->show_tables ( $name, false );
		return (count ( $check ) > 0);
	}
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
	public function drop($table = null) {
		if (null === $table) {
			$table = $this->name ();
		}
		$sql = "DROP TABLE IF EXISTS `%s`;";
		$sql = sprintf ( $sql, $table );
		$return = $this->execute ( $sql );
		return $return;
	}
	public function truncate($table = null) {
		if (null === $table) {
			$table = $this->name ();
		}
		$sql = "TRUNCATE TABLE `%s`;";
		$sql = sprintf ( $sql, $table );
		$return = $this->execute ( $sql );
		return $return;
	}
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
	public function bulk_insert($data) {
		set_time_limit ( 500 );
		foreach ( $data as $line ) {
			$this->insert ( $line );
		}
	}
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
	public function first_row($data) {
		$return = false;
		foreach ( $data as $datum ) {
			$return = $datum;
		}
		return $return;
	}
}