<?php 
// this page should really only be loaded once, flash does the posting
// therefore a unique session id is required if one was previously set
session_start();
if (!empty($_SESSION['start'])) {
	session_regenerate_id();
}
$_SESSION['start'] = time();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>Infinitive Analytics - Web Analytics Maturity Meter</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<script type="text/javascript" src="js/swfobject2_1.js"></script>
		<script type="text/javascript">
			var flashvars = {
					key: '<?=session_id()?>',
					xml_path: 'xml/',
					share_url:'<?='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>'
			};
			var params = {
					bgcolor: '#FFFFFF'
			};
				
			swfobject.embedSWF("infinitive_analytics.swf", "flashContent", "800", "800", "9.0.28", "expressInstall.swf", flashvars, params);
		</script>
		<script type="text/javascript">
		</script>
	</head>
	<body bgcolor="#ffffff">
		<div id="flashContent">
			<h1>Alternative content</h1>
			<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
		</div>
	</body>
</html>
