<?
$link = mysql_connect('127.0.0.1', 'Rbatch', 'Rbatch');
if (!$link) { echo "Unable to connect to DB: " . mysql_error(); exit; }
if (!mysql_select_db('R')) { echo "Unable to select database: " . mysql_error(); exit; }

$id = 1 * $_REQUEST['ID'];

if ($id < 1) {
 header('Location: /');
 exit();
}

$sql = "UPDATE jobs SET Retired = 'Y', RetiredDate = NOW()
	 WHERE ID = ". $id. " AND Retired != 'Y' LIMIT 1";

$result = mysql_query($sql);
if (!$result) { echo "Could not select jobs" . mysql_error(); exit; }
header('Location: /');
?>
