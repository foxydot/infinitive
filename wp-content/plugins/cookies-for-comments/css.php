<?php
if ( !isset( $_COOKIE[ $_GET[ 'k' ] ] ) || ( isset( $_COOKIE[ $_GET[ 'k' ] ] ) && $_COOKIE[ $_GET[ 'k' ] ] == 1 ) )
	@setcookie( $_GET[ 'k' ], time(), time()+604800, '/' );
if ( isset( $_GET[ 'o' ] ) ) {
	header("Content-type: image/gif");
	readfile( './blank.gif' );
} else {
	header("Content-type: text/css");
	echo "/* Page generated by Cookies for Comments at http://ocaoimh.ie/cookies-for-comments/ */";
}
die();
?>
