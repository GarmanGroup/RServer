<?
$link = mysql_connect('127.0.0.1', 'Rbatch', 'Rbatch');
if (!$link) { echo "Unable to connect to DB: " . mysql_error(); exit; }
if (!mysql_select_db('R')) { echo "Unable to select database: " . mysql_error(); exit; }

#$sql = 'SELECT jobs.*, environment.Name AS EnvName, environment.Commands AS EnvCmd FROM jobs
#	  JOIN environment ON (jobs.Environment = environment.ID)
#	 ORDER BY Queued DESC
#	 LIMIT 10';
$sql = "SELECT ID, Name, Status, Result, UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(IF(Status IN ('Parsing-Fail','Success','Failure'), Completed, IF(Status = 'Running', Started, Queued))) AS LastChange FROM jobs
	 WHERE Retired = 'N'
	 ORDER BY (Status = 'Running') DESC, (Status = 'Queued') DESC, LastChange ASC
	 LIMIT 3000";

$result = mysql_query($sql);
if (!$result) { echo "Could not select jobs" . mysql_error(); exit; }
if (mysql_num_rows($result) == 0) { echo "No jobs found"; exit; }

?>
<html><head><title>MySQL-RMySQL-R</title>
<style type="text/css">
 body { font-size: 12pt; }
 .job { border: 1px solid #888; padding: 8px; margin-bottom: 1em; }
 .jobtools { float: right; border: 1px solid red; }
 .jobid { text-decoration: none; }
 .jobno { font-size:120%; font-weight: bold; }
 .jobstatus { }
 .preparing { color: #888; }
 .queued { color: #00F; }
 .running { color: #F0F; }
 .parsing-fail { color: #F80; }
 .success { color: #080; }
 .failure { color: #A00; }
 .jobname { font-size:120%; font-weight: bold; }
 .result { border: 1px dotted #000; background-color: #FFFFF0; padding: 5px; margin: 8px; width: auto; max-height: 150px; overflow-y: scroll; font-size: 80% }
 .result pre { margin: 0; padding: 0; }
 .images { clear: both; }
 .image { width: 100px; border: 1px dotted #DDD; float: left; margin: 0.3em; }
 .imgname { margin: 0; padding: 0; text-align: center; font-weight: normal; }
 .image .preview { width: 100px; height: 100px; vertical-align: middle; text-align: center; border: 0; display: table-cell; }
 .image .preview img { vertical-align: bottom; border: 0; }
 .image .download { margin: 0; padding: 0; text-align: center; }
 .image .download a { text-decoration: none; }
 .clear { clear: both; }
</style>
</head>
<body>
<?

// function time_ago based on time_ago by Matt Jones
// http://www.mdj.us/web-development/php-programming/another-variation-on-the-time-ago-php-function-use-mysqls-datetime-field-type/
// DISPLAYS COMMENT POST TIME AS "1 year, 1 week ago" or "5 minutes, 7 seconds ago", etc...
function time_ago($difference,$granularity=1) {
# $date = strtotime($date);
# $difference = time() - $date;
 $periods = array('decade' => 315360000,
  'year' => 31536000,
  'month' => 2628000,
  'week' => 604800,
  'day' => 86400,
  'hour' => 3600,
  'minute' => 60,
  'second' => 1);
 if ($difference < 20) { // less than 5 seconds ago, let's say "just now"
  $retval = " just now";
  return $retval;
 } else {
  $retval = '';
  foreach ($periods as $key => $value) {
   if ($difference >= $value) {
    $time = floor($difference/$value);
    $difference %= $value;
    $retval .= ($retval ? ' ' : '').$time.' ';
    $retval .= (($time > 1) ? $key.'s' : $key);
    $granularity--;
   }
   if ($granularity == '0') { break; }
  }
  return ' '.$retval.' ago';
 }
}


while ($row = mysql_fetch_assoc($result)) {

 print '<div class="job">';
 print '<a href="showjob.php?ID='.$row['ID'].'" class="jobid '. $row['Status'] .'"><small>#</small><span class="jobno">'.$row['ID'].'</span> &ndash; <span class="jobname">'.htmlentities($row['Name']).'</span> &ndash; <span class="jobstatus">'. $row['Status'] .'</span>, '. time_ago($row['LastChange']). '</a>';
 print '<div class="jobtools">';
 print '<a href="showjob.php?ID='.$row['ID'].'">view</a>';
 print '<a href="prepare.php?preload='.$row['ID'].'">copy</a>';
 print '<a href="dropjob.php?ID='.$row['ID'].'">delete</a>';
 print '</div>';
 print '<br/>';

 if (trim($row['Result']) != '') {
  print 'Output:<br/>';
  print '<div class="result"><pre>';
  print htmlentities($row['Result']);
  print '</pre></div>';
 }
 
 $sql2 = 'SELECT ID FROM results WHERE JobID = ' . $row['ID'] . ' LIMIT 16';
 $result2 = mysql_query($sql2);
 if (!$result2) { echo "Could not select results" . mysql_error(); exit; }
 if (mysql_num_rows($result2) > 0) {
  print '<div class="images">';
  while ($resrow = mysql_fetch_assoc($result2)) {
   print '<div class="image">';
   print '<a href="data/'. $resrow['ID'].'.png" class="preview"><img src="data/'. $resrow['ID'].'-pre100.png"></a>';
   print '</div>';
  }
  print '</div><div class="clear"></div>';
 }
 print '</div>';

 mysql_free_result($result2);
}

mysql_free_result($result);

?>
</body></html>
