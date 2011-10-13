<?php
class bv28v_table extends bv28v_base {
	protected static $_instance = null;
	public function instance() {
		if (null === self::$_instance) {
			self::$_instance = new self ( );
		}
		return self::$_instance;
	}
	public function execute($sql) {
		throw new exception ( 'Not implamented yet.' );
	}
	public function like(&$like) {
		$return = "";
		if (null !== $like) {
			$return = " LIKE '%s'";
			$return = sprintf ( $return, $like );
		}
		$like = $return;
	}
	public function where(&$where = null) {
		$return = "";
		if (null !== $where) {
			$return = " WHERE %s ";
			$return = sprintf ( $return, $where );
		}
		$where = $return;
	}
	public function limit(&$limit = null) {
		$return = "";
		if (null !== $limit) {
			$this->obj_name ( $limit );
			$return = " LIMIT %s ";
			$return = sprintf ( $return, $limit );
		}
		$limit = $return;
	}
	public function from(&$from = null) {
		$return = "";
		if (null === $from) {
			$from = $this->name ();
		}
		$this->obj_name ( $from );
		$return = " FROM %s ";
		$return = sprintf ( $return, $from );
		$from = $return;
	}
	public function obj_name(&$obj) {
		$return = '`%s`';
		$return = sprintf ( $return, $obj );
		$obj = $return;
	}
	public function field(&$field = '*',$no_quote=false) {
		if ($field != '*') {
			$field = $this->swap_special ( $field );
			$field = $this->strip_tags ( $field );
			$field = $this->strip_special ( $field );
			$field = $this->special_trim ( $field );
			if(!$no_quote)
			{
				$this->obj_name ( $field );
			}
		}
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
		return trim($string);
	}
	private function strip_tags($string) {
		$pattern = '|\<.*\>|Ui';
		$string = preg_replace ( $pattern, '', $string );
		return trim($string);
	}
	private function swap_special($string) {
		$chars = array ('Ð' => '-', 'Á' => '!', 'À' => '?', 'Ò' => '"', 'Ó' => '"', 'Ô' => "'", 'Õ' => "'", 'Ç' => '"', 'È' => '"', '&' => '+', '¢' => 'c', '©' => '(c)', 'µ' => 'u', 'á' => '.', '¦' => '|', '±' => '+-', 'Û' => 'e', '¨' => '(r)', 'ª' => ' TM ', '´' => 'y', '‡' => 'a', 'ç' => 'A', 'ˆ' => 'a', 'Ë' => 'A', '‰' => 'a', 'å' => 'A', 'Œ' => 'a', '' => 'A', '‹' => 'a', 'Ì' => 'A', 'Š' => 'a', '€' => 'A', '¾' => 'ae', '®' => 'AE', '' => 'c', '‚' => 'C', 'Ž' => 'e', 'ƒ' => 'E', '' => 'e', 'é' => 'E', '' => 'e', 'æ' => 'E', '‘' => 'e', 'è' => 'E', '’' => 'i', 'ê' => 'I', '“' => 'i', 'í' => 'I', '”' => 'i', 'ë' => 'I', '•' => 'i', 'ì' => 'I', '–' => 'n', '„' => 'N', '—' => 'o', 'î' => 'O', '˜' => 'o', 'ñ' => 'O', '™' => 'o', 'ï' => 'O', '¿' => 'o', '¯' => 'O', '›' => 'o', 'Í' => 'O', 'š' => 'o', '…' => 'O', '§' => 'B', 'œ' => 'u', 'ò' => 'U', '' => 'u', 'ô' => 'U', 'ž' => 'u', 'ó' => 'U', 'Ÿ' => 'u', '†' => 'U', 'Ø' => 'u' );
		foreach ( $chars as $key => $value ) {
			$string = str_replace ( $key, $value, $string );
		}
		return trim($string);
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
			$first = trim(substr ( $first, 0, 29 ));
			$second = trim(substr ( $second, 0, 29 ));
			$string = "{$first}[{$second}]";
		} else {
			$string = substr ( $string, 0, 60 );
		}
		return trim($string);
	}
	public function show_tables($like = null) {
		$this->like ( $like );
		$sql = "SHOW TABLES%s;";
		$sql = sprintf ( $sql, $like );
		$return = $this->execute ( $sql );
		return $return;
	}
	public function exists($name=null)
	{
		if(null===$name)
		{
			$name = $this->name();
		}
		$check = $this->show_tables($name);
		return (count($check)>0);
	}
	public function show_databases($like = null) {
		$this->like ( $like );
		$sql = "SHOW DATABASES%s;";
		$sql = sprintf ( $sql, $like );
		$return = $this->execute ( $sql );
		return $return;
	}
	public function drop($table=null)
	{
		if (null === $table) {
			$table = $this->name ();
		}
		$this->obj_name ( $table );
		$sql = "DROP TABLE IF EXISTS %s;";
		$sql = sprintf ( $sql, $table);
		$return = $this->execute ( $sql );
		return $return;
	}
	public function show_columns($from = null, $like = null) {
		if (null === $from) {
			$from = $this->name ();
		}
		$this->from ( $from );
		$this->like ( $like );
		$sql = "SHOW COLUMNS%s%s;";
		$sql = sprintf ( $sql, $from, $like );
		$return = $this->execute ( $sql );
		return $return;
	}
	public function show_indexes($from) {
		$this->from ( $from );
		$sql = "SHOW INDEXES%s;";
		$sql = sprintf ( $sql, $from );
		$return = $this->execute ( $sql );
		return $return;
	}
	public function show_create_database($database) {
		$this->obj_name ( $database );
		$sql = "SHOW CREATE DATABASE %s;";
		$sql = sprintf ( $sql, $database );
		$return = $this->execute ( $sql );
		return $return;
	}
	public function count_records($field, $where = null, $from = null) {
		$this->from ( $from );
		$this->where ( $where );
		$this->field ( $field );
		$sql = "SELECT COUNT(%s) 'count'%s%s";
		$sql = sprintf ( $sql, $field, $from, $where );
		$return = $this->execute ( $sql );
		return $return [0] ['count'];
	}
	public function show_create_table($table) {
		$this->obj_name ( $table );
		$sql = "SHOW CREATE TABLE %s;";
		$sql = sprintf ( $sql, $table );
		$return = $this->execute ( $sql );
		return $return;
	}
	public function show_create_view($view) {
		$this->obj_name ( $view );
		$sql = "SHOW CREATE VIEW %s;";
		$sql = sprintf ( $sql, $view );
		$return = $this->execute ( $sql );
		return $return;
	}
	public function truncate($table) {
		$this->obj_name ( $table );
		$sql = "TRUNCATE TABLE %s";
		$sql = sprintf ( $sql, $table );
		$return = $this->execute ( $sql );
		return $return;
	}
	private $_name = null;
	public function name() {
		return $this->prefix () . $this->_name;
	}
	protected function create_table($fields, $keys) {
		$sql = "CREATE TABLE IF NOT EXISTS `" . $this->name () . "` (\n";
		$comma = "";
		foreach ( ( array ) $fields as $field_name => $field_def ) {
			$sql .= $comma;
			$sql .= "`" . $field_name . "` " . $field_def ['type'];
			if (array_key_exists ( 'null', $field_def ) && ! $field_def ['null']) {
				$sql .= " NOT NULL ";
			}
			if (array_key_exists ( 'extra', $field_def )) {
				$sql .= " " . $field_def ['extra'];
			}
			$comma = ",\n";
		}
		if (array_key_exists ( 'primary', $keys )) {
			$sql .= $comma . " PRIMARY KEY (`" . $this->keys ['primary'] . "`)";
		}
		$sql .= ")";
		$return = $this->execute ( $sql );
	}
	protected function alter_table($key, $type) {
		$this->field ( $key );
		$sql = "ALTER TABLE `%s` ADD %s %s";
		$sql = sprintf ( $sql, $this->name (), $key, $type );
		$return = $this->execute ( $sql );
	}
	protected function set_name($name = null) {
		if (null == $name) {
			$name = 'test';
		}
		$this->_name = $name;
	}
	public function insert($data) {
		$fields = array ();
		$values = array ();
		foreach ( ( array ) $data as $key => $value ) {
			$this->field ( $key );
			$fields [] = $key;
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
}