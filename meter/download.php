<?php
$username='infi';
$password='m3t3r';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if ($_POST['username']==$username && $_POST['password']==$password) {
		$_SESSION['authenticated']=true;
	}
}

if ($_SESSION['authenticated']==true && $_GET['download']=='true') {
	  
	$dbhandle = sqlite_open('../../data/meterdata.db');
	$query = sqlite_query($dbhandle, 'SELECT * FROM meterdata');
	$result = sqlite_fetch_all($query, SQLITE_ASSOC);
	
	/*
	header('Content-type: text/plain');
	foreach ($result as $entry) {
		print_r($entry);
	}
	*/
	
	header("Content-type: application/vnd.ms-excel");
	header('Content-Disposition: attachment; filename="export_'.date("YmdHis").'.csv"');
	
	// header row
	$entry = $result[0];
	foreach ($entry as $key => $val) {
		if ($key!='key') {
			$keysArray[] = '"'.$key.'"';
		}
	}	
	echo implode(",",$keysArray)."\r\n";
	
	// data rows
	foreach ($result as $entry) {
		//mysql_data_seek($result,0);
		unset($valArray);
		foreach ($entry as $key => $val) {
			if ($key!='key') {
				if ($key == 'timestamp') $val = date('m/d/Y',$val);
				$valArray[] = '"'.$val.'"';
			}
		}
		echo implode(",",$valArray)."\r\n";
	}
	exit;
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>meter data download</title>
</head>
<body>
<? if ($_SESSION['authenticated']==true) :?>
    <p><a href="?download=true">Click here to start download.</a></p>
<? else:?>
    <form name="form1" method="post" action="">
        <table width="100%" border="0" cellspacing="2" cellpadding="3">
            <tr>
                <th align="right" nowrap>Username</th>
                <td><input type="text" name="username" id="username"></td>
            </tr>
            <tr>
                <th align="right" nowrap>Password</th>
                <td><input name="password" type="password" id="password"></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" name="button" id="button" value="Submit"></td>
            </tr>
        </table>
    </form>
<? endif;?>
</body>
</html>
