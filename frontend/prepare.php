<?
$link = mysql_connect('127.0.0.1', 'Rbatch', 'Rbatch');
if (!$link) { echo "Unable to connect to DB: " . mysql_error(); exit; }
if (!mysql_select_db('R')) { echo "Unable to select database: " . mysql_error(); exit; }

$id = 1 * $_REQUEST['preload'];

if ($id > 0) {
 $sql = "SELECT Name, Environment, Commands, Comments FROM jobs WHERE ID = $id";
 $result = mysql_query($sql);
 if (!$result) { echo "Could not select jobs" . mysql_error(); exit; }
 if (mysql_num_rows($result) == 0) { echo "Job not found"; exit; }
 $preparedata = mysql_fetch_assoc($result);
 mysql_free_result($result);
}

?>
<html><head><title>MySQL-RMySQL-R</title>
<style type="text/css">
 body { font-size: 12pt; }
 .job { border: 1px solid #888; padding: 8px; margin-bottom: 1em; }
 .jobid { font-size:120%; text-decoration: none; }
 .jobno { font-weight: bold; }
 .jobstatus { font-weight: bold; }
 .jobstatus.success { color: #080; }
 .jobstatus.failure { color: #A00; }
 .jobstatus.preparing { color: #888; }
 .jobstatus.running { color: #F0F; }
 .jobstatus.queued { color: #00F; }
 .jobname { font-size: 120%; font-weight: bold; }
 .result { border: 1px dotted #000; background-color: #FFFFF0; padding: 8px; margin: 8px; width: auto; max-height: 300px; overflow-y: scroll; }
 .result pre { margin: 0; padding: 0; }
 .images { }
 .image { width: 150px; border: 1px dotted #FCC; float: left; margin: 0.5em; }
 .imgname { margin: 0; padding: 0; text-align: center; font-weight: normal; }
 .image .preview { width: 150px; height: 150px; vertical-align: middle; text-align: center; border: 0; display: table-cell; }
 .image .preview img { vertical-align: bottom; border: 0; }
 .image .download { margin: 0; padding: 0; text-align: center; }
 .image .download a { text-decoration: none; }
 .clear { clear: both; }
</style>
</head>
<body>

<div class="job new">
<form action="newjob.php" method="post">
 Name: <input name="name" type="text" size="90" maxlength="255" value="<?= htmlentities($preparedata['Name']) ?>">
 Environment:
 <select name="environment" size="1"> 
<?
$envirs = mysql_query('SELECT ID, Name FROM environment WHERE Obsolete="N" ORDER BY Name ASC');
if (!$envirs) { echo "Could not select environments" . mysql_error(); exit; }
if (mysql_num_rows($envirs) == 0) { echo "No jobs found"; exit; }
while ($row = mysql_fetch_assoc($envirs)) {
 ?><option value="<?= $row['ID'] ?>"<?= ($row['ID'] == $preparedata['Environment'] ? 'selected' : '') ?>><?= $row['Name'] ?></option><?
}
mysql_free_result($envirs);

?>
 </select><br/>
 <textarea name="comments" cols="120" rows="5"><?= $id == 0 ? '' :
  '-- based on #'. $id ."\n" . trim(preg_replace('/^-- based on #[0-9]+/i', '', $preparedata['Comments']))
 ?></textarea><br/>
 <textarea name="commands" cols="120" rows="25"><?= $preparedata['Commands'] != '' ? $preparedata['Commands'] : "
graph.out('title', size = c(400, 400),
 function() {
  boxplot(...)
 }
)" ?></textarea><br/>
 <input type="submit" value=" Create Job ">
</form>
</div>
</body></html>
