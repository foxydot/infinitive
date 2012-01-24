<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class bv45v_data_json {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public static function decode($data, $legacy = false) {
		$return = json_decode ( $data, $legacy );
		if (function_exists ( 'json_last_error' )) {
			$error = null;
			switch (json_last_error ()) {
				case JSON_ERROR_NONE :
					break;
				case JSON_ERROR_DEPTH :
					$error = ' - Maximum stack depth exceeded';
					break;
				case JSON_ERROR_STATE_MISMATCH :
					$error = ' - Underflow or the modes mismatch';
					break;
				case JSON_ERROR_CTRL_CHAR :
					$error = ' - Unexpected control character found';
					break;
				case JSON_ERROR_SYNTAX :
					$error = ' - Syntax error, malformed JSON';
					break;
				case JSON_ERROR_UTF8 :
					$error = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
					break;
				default :
					$error = ' - Unknown error';
					break;
			}
			if (null !== $error) {
				$return = false;
				throw new exception ( $data . $error );
			}
		}
		return $return;
	}
}