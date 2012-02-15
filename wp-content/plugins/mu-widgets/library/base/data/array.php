<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class bv47v_data_array {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public static function merge() {
		// get the variable number of objects to be merged
		$params = func_get_args ();
		// if the was only one parameter then that must be the list of objects to merge 
		if (count ( $params ) == 1) {
			$params = $params [0];
		}
		// pull off the first item
		$object1 = array_shift ( $params );
		// go through the other arrays and merge them in
		$is_array = is_array ( $object1 );
		foreach ( $params as $object2 ) {
			// if either object it not an object or array them you must overwrite not merge
			if (! (is_array ( $object1 ) || is_object ( $object1 )) || ! (is_array ( $object2 ) || is_object ( $object2 ))) {
				return $object2;
			}
			// check to see if you are merging into an array or an object
			// convert the second object into an array so you can loop though the properties
			if (is_object ( $object2 )) {
				$object2 = get_object_vars ( $object2 );
			}
			// build a list from the two object of things that should be overwritten not merged
			$overwrite = array ();
			if (is_object ( $object1 ) && isset ( $object1->__overwrite__ )) {
				$overwrite = array_merge ( $overwrite, ( array ) $object1->__overwrite__ );
			}
			if (is_object ( $object2 ) && isset ( $object2->__overwrite__ )) {
				$overwrite = array_merge ( $overwrite, ( array ) $object2->__overwrite__ );
			}
			if (is_array ( $object1 ) && isset ( $object1 ['__overwrite__'] )) {
				$overwrite = array_merge ( $overwrite, ( array ) $object1 ['__overwrite__'] );
			}
			if (is_array ( $object2 ) && isset ( $object2 ['__overwrite__'] )) {
				$overwrite = array_merge ( $overwrite, ( array ) $object2 ['__overwrite__'] );
			}
			foreach ( $object2 as $key => $value ) {
				if ($is_array) {
					// numeric keys should append
					if (is_numeric ( $key )) {
						$object1 [] = $value;
					} else {
						if (isset ( $object1 [$key] ) && ! in_array ( $key, $overwrite )) {
							$object1 [$key] = self::merge ( $object1 [$key], $value );
						} else {
							$object1 [$key] = $value;
						}
					}
				} else {
					if (is_numeric ( $key )) {
						$key = '_'.$key;
						$object1->$key = $value;
					} else {
						if (isset ( $object1->$key ) && ! in_array ( $key, $overwrite )) {
							$object1->$key = self::merge ( $object1->$key, $value );
						} else {
							$object1->$key = $value;
						}
					}
				}
			}
		}
		return $object1;
	}
	public static function objects_to_array(&$objects)
	{
		$objects = get_object_vars($objects);
		foreach($objects as &$object)
		{
			if(is_object($object))
			{
				bv47v_data_array::objects_to_array($object);
			}
		}
		unset($object);
	}
}