<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class bv45v_loader extends bv45v_base {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function sanitize_path($path) {
		return rtrim ( $path, DIRECTORY_SEPARATOR );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function includepath($folders = null, $reverse = false) {
		$return = array ();
		if (null !== $folders) {
			$dirs = array();
			foreach ( $this->application()->folders as $path ) {
				foreach ( ( array ) $folders as $folder ) {
					$newfolder = $path . DIRECTORY_SEPARATOR . $this->sanitize_path ( $folder );
					if (is_dir ( $newfolder )) {
						$dirs [] = $newfolder;
					}
				}
			}
			return $dirs;
		}
		else
		{
			$return = get_object_vars($this->application()->folders);
		}
		if ($return) {
			$return = array_reverse ( $return );
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function load_class($class) {
		if (class_exists ( $class, false )) {
			return;
		}
		$file = str_replace ( '_', DIRECTORY_SEPARATOR, $class ) . '.php';
		$found = false;
		foreach ( $this->application()->folders as $key => $value ) {
			if (strpos($key,'_')!==0) {
				$start = "{$key}v45v";
				if (strpos ( $file, $start ) === 0) {
					$file = str_replace ( $start, $value, $file );
					$found = true;
					break;
				}
			}
		}
		if(!$found)
		{
			$file = $this->application()->directory.'/application/models/'.$file;
		}
		if(file_exists($file))
		{
			//echo "{$file}<br/>";
			@include_once $file;
		}
		if (! class_exists ( $class, false )) {
			throw new Exception ( "File \"{$file}\" does not exist or class \"{$class}\" was not found in the file" );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/	
	public function find_file($filename, $quiet = false, $include_path = null) {
		if (null === $include_path) {
			$include_path = $this->includepath ();
		}
		if (file_exists ( $filename )) {
			return $filename;
		}
		foreach ( $include_path as $dir ) {
			if (file_exists ( $this->sanitize_path ( $dir ) . DIRECTORY_SEPARATOR . $filename )) {
				return $this->sanitize_path ( $dir ) . DIRECTORY_SEPARATOR . $filename;
			}
		}
		if (! $quiet) {
			throw new Exception ( $filename . ' Not Found ' . print_r ( $include_path, true ) );
		}
		return false;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function file($filename) {
		$filename = $this->find_file ( $filename );
		return file_get_contents ( $filename );
	}
}

