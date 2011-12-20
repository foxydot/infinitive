<?php 
$campaign = 1;  // to be changed so meter can be reused

if ($_GET['test']>0) header('Content-type: text/plain');
session_start();

// for testing
if ($_GET['test']==1) {
	session_regenerate_id();
	$_SESSION['start'] = time();
	$_POST = array(
		'key'=>session_id(), 
		'final_score'=>'143', 
		'technology_score'=>'20', 
		'process_score'=>'21', 
		'people_score'=>'60', 
		'data_score'=>'42', 
		'tech3'=>'2', 
		'tech2'=>'6', 
		'tech1'=>'12', 
		'process3'=>'4.5', 
		'process2'=>'3', 
		'process1'=>'3', 
		'people3'=>'2', 
		'people2'=>'6', 
		'people1'=>'12', 
		'data3'=>'9', 
		'data2'=>'4', 
		'data1'=>'1', 
		'industry'=>'Aerospace & Defense', 
		'role'=>'Compliance', 
		'title'=>'C-Level Executive'
	);
}
if ($_GET['test']==2) {
	$_POST = array(
		'key'=>session_id(), 
		'email'=>'tom@bytepro.net', 
		'name'=>'tom barrons'
	);
}

if (empty($_SESSION['start'])) {
	trigger_error('missing session data');
	die();
}

// capture CYA log
$saveString = http_build_query($_POST)."\n";
file_put_contents('../../data/infinitive-log.txt',$saveString,FILE_APPEND);

// these fields shouldn't have any decimals
$intKeysArray = array(
	'final_score', 
	'technology_score', 
	'process_score', 
	'people_score', 
	'data_score', 
	'tech3', 
	'tech2', 
	'tech1', 
	'process3', 
	'process2', 
	'process1', 
	'people3', 
	'people2', 
	'people1', 
	'data3', 
	'data2', 
	'data1'
);
// pre-process vars
foreach ($_POST as $key => $val) {
	if (in_array($key,$intKeysArray)) $val = (int) round($val);
	if (is_string($val)) $val = "'".sqlite_escape_string($val)."'";
	//var_dump($val);
	$saveArr[$key] = $val;
	if ($key=='email') $update = true;
}
$saveArr['campaign'] = $campaign;
$saveArr['timestamp'] = $_SESSION['start'];
if ($_GET['test']>0)  print_r($saveArr);

if ($update == true) {
	foreach ($saveArr as $key => $val) {
		if ($key!='key') $colValArr[] = $key.'='.$val;
	}
	$colValStr = implode(",",$colValArr);
	$sql = "UPDATE meterdata SET ".$colValStr." WHERE key =".$saveArr['key'].";";
} else {
	$colStr = implode(",",array_keys($saveArr));
	$valStr = implode(",",$saveArr);
	$sql = "INSERT INTO meterdata (".$colStr.") VALUES (".$valStr.");";
}
if ($_GET['test']>0)  echo $sql."\n"; 

if ($db = sqlite_open('../../data/meterdata.db', 0666, $sqliteerror)) { 
    //sqlite_query($db, 'CREATE TABLE meterdata (key varchar(32))'); // issue this then edit with navicat
    sqlite_query($db, $sql);
} else {
    die($sqliteerror);
}

echo 'true';
?>
