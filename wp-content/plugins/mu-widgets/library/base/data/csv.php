<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class bv47v_data_csv {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function load($file, $firstLineKeys = false,$header_lines = 0) {
		if(!file_exists($file))
		{
			$file = dirname(dirname(dirname(dirname(__FILE__)))).'/application/data/'.$file;
		}
		$array = array ();
		$keys = null;
		if (($handle = fopen ( $file , "r" )) !== false) {
			while ( ($data = fgetcsv ( $handle, 1000, "," )) !== false ) {
				if ($header_lines > 0) {
					$header_lines --;
				} else {
					$subarray = array ();
					if (is_null ( $keys ) && $firstLineKeys) {
						$keys = $data;
					} else {
						$key = 0;
						foreach ( $data as $datum ) {
							if ($firstLineKeys) {
								$subarray [$keys [$key]] = $datum;
							} else {
								$subarray [] = $datum;
							}
							$key ++;
						}
						$array [] = $subarray;
					}
				}
			}
			fclose ( $handle );
		}
		return $array;
	}
}