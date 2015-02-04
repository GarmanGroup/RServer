<?
$link = mysql_connect('127.0.0.1', 'Rbatch', 'Rbatch');
if (!$link) { echo "Unable to connect to DB: " . mysql_error(); exit; }
if (!mysql_select_db('R')) { echo "Unable to select database: " . mysql_error(); exit; }

$id = 1 * $_REQUEST['ID'];

if ($id < 1) {
 header('Location: /');
 exit();
}

$sql = 'SELECT jobs.*, environment.Name AS EnvName, environment.Commands AS EnvCmd,
	       UNIX_TIMESTAMP(Completed) - UNIX_TIMESTAMP(Started) AS Runtime,
               UNIx_TIMESTAMP(Started) - UNIX_TIMESTAMP(Queued) AS Queuetime
	  FROM jobs
	  JOIN environment ON (jobs.Environment = environment.ID)
	 WHERE jobs.ID = '. $id;

$result = mysql_query($sql);
if (!$result) { echo "Could not select jobs" . mysql_error(); exit; }
if (mysql_num_rows($result) == 0) { echo "No jobs found"; exit; }

$job = mysql_fetch_assoc($result);
mysql_free_result($result);

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
 .jobstatusbox { width: 20em; padding: 0.8em 0; clear: right; float: right; text-align: right; }
 .jobstatusbox pre { margin: 0; padding: 0; }
 .jobname { font-size:120%; font-weight: bold; }
 .comment { background-color: #F8F8F8; margin: 1em 0 0 0; width: 500px; }
 .comment pre {
	margin: 0; padding: 0.8em; font-size: 10pt;
	width: 500px;                          /* specify width  */
	white-space: pre-wrap;                 /* CSS3 browsers  */
	white-space: -moz-pre-wrap !important; /* 1999+ Mozilla  */
	white-space: -pre-wrap;                /* Opera 4 thru 6 */
	white-space: -o-pre-wrap;              /* Opera 7 and up */
	word-wrap: break-word;                 /* IE 5.5+ and up */
	/* overflow-x: auto; */                /* Firefox 2 only */
	/* width: 99%; */		       /* only if needed */
	}
 .environment { background-color: #FFFFC8; margin: 0.5em 0 0 0; height: 3.1em; overflow: hidden; }
 .environment:hover { height: auto; overflow: auto; }
 .environment pre {
	margin: 0; padding: 0.8em; font-size: 9pt;
	white-space: pre-wrap;                 /* CSS3 browsers  */
	white-space: -moz-pre-wrap !important; /* 1999+ Mozilla  */
	white-space: -pre-wrap;                /* Opera 4 thru 6 */
	white-space: -o-pre-wrap;              /* Opera 7 and up */
	word-wrap: break-word;                 /* IE 5.5+ and up */
	/* overflow-x: auto; */                /* Firefox 2 only */
	/* width: 99%; */		       /* only if needed */
	}

 .commands { background-color: #EFE; margin: 0.5em 0 0 0; }
 .commands pre {
	margin: 0; padding: 0.8em; font-size: 9pt;
	white-space: pre-wrap;                 /* CSS3 browsers  */
	white-space: -moz-pre-wrap !important; /* 1999+ Mozilla  */
	white-space: -pre-wrap;                /* Opera 4 thru 6 */
	white-space: -o-pre-wrap;              /* Opera 7 and up */
	word-wrap: break-word;                 /* IE 5.5+ and up */
	/* overflow-x: auto; */                /* Firefox 2 only */
	/* width: 99%; */		       /* only if needed */
	}

 .result { background-color: #EEF; margin: 0.5em 0 0 0; }
 .result pre {
	margin: 0; padding: 0.8em; font-size: 9pt;
	white-space: pre-wrap;                 /* CSS3 browsers  */
	white-space: -moz-pre-wrap !important; /* 1999+ Mozilla  */
	white-space: -pre-wrap;                /* Opera 4 thru 6 */
	white-space: -o-pre-wrap;              /* Opera 7 and up */
	word-wrap: break-word;                 /* IE 5.5+ and up */
	/* overflow-x: auto; */                /* Firefox 2 only */
	/* width: 99%; */		       /* only if needed */
	}

 .images { clear: both; }
 .image { width: 250px; border: 1px dotted #DDD; float: left; margin: 0.3em; }
 .imgname { margin: 0; padding: 0; text-align: center; font-weight: normal; }
 .image .preview { width: 250px; height: 175px; vertical-align: middle; text-align: center; border: 0; display: table-cell; }
 .image .preview img { vertical-align: bottom; border: 0; }
 .image .download { margin: 0; padding: 0; text-align: center; }
 .image .download a { text-decoration: none; }
 .clear { clear: both; }
</style>
</head>
<body>
<div class="job">
<?
 print '<span class="jobid '. $job['Status'] .'"><small>#</small><span class="jobno">'.$job['ID'].'</span> &ndash; <span class="jobname">'.htmlentities($job['Name']).'</span> &ndash; '.$job['Status'].'</span>';
 print '<div class="jobtools">';
 print '<a href="prepare.php?preload='.$job['ID'].'">copy</a>';
 print '<a href="dropjob.php?ID='.$job['ID'].'">delete</a>';
 print '</div>';

 $job['Queued']      = $job['Queued']      == '' ? '---------- --:--:--' : $job['Queued'];
 $job['Started']     = $job['Started']     == '' ? '---------- --:--:--' : $job['Started'];
 $job['Completed']   = $job['Completed']   == '' ? '---------- --:--:--' : $job['Completed'];
 $job['RetiredDate'] = $job['RetiredDate'] == '' ? '---------- --:--:--' : $job['RetiredDate'];

?>
<div class="jobstatusbox"><pre>Queued:    <?= $job['Queued'] ?><br/>Started:   <?= $job['Started'] ?><br/>Completed: <?= $job['Completed'] ?><br/>Retired:   <?= $job['RetiredDate'] ?><br/>(<?= $job['Runtime'] ?>s runtime, <?= $job['Queuetime'] ?>s queuetime)</pre></div>
<?
 if (trim($job['Comments']) != '') {
  print '<div class="comment"><pre>';
  print htmlentities(trim($job['Comments']));
  print '</pre></div>';
 }

 $sql2 = 'SELECT ID, FilenameInternal FROM results WHERE JobID = ' . $job['ID'];
 $result2 = mysql_query($sql2);
 if (!$result2) { echo "Could not select results" . mysql_error(); exit; }
 if (mysql_num_rows($result2) > 0) {
  print '<div class="images">';
  while ($resrow = mysql_fetch_assoc($result2)) {
   print '<div class="image">';
   print '<p class="imgname">'.htmlentities($resrow['FilenameInternal']).'</p>';
   print '<a href="data/'. $resrow['ID'].'.png" class="preview"><img src="data/'. $resrow['ID'].'-pre250.png"></a>';
   print '<p class="download">[<a href="data/' . $resrow['ID'] .'.pdf">PDF</a>]</p>';
   print '</div>';
  }
  print '</div><div class="clear"></div>';
 }

 mysql_free_result($result2);

 if (trim($job['Result']) != '') {
  print '<div class="result"><pre>';
  print "<b># Output</b>\n\n";
  print htmlentities(trim($job['Result']));
  print '</pre></div>';
 }
 
 if (trim($job['EnvCmd']) != '') {
  print '<div class="environment"><pre>';
  print '<b># Environment: '. htmlentities($job['EnvName'])."</b>\n\n";
  print htmlentities(trim($job['EnvCmd']));
  print '</pre></div>';
 }

 if (trim($job['Commands']) != '') {
  print '<div class="commands"><pre>';
  print htmlentities(trim($job['Commands']));
  print '</pre></div>';
 }

 print '</div>';

?>
</body></html>
