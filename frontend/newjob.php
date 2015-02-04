<?
$envir = 1 * $_POST['environment'];
$name = $_POST['name'];
$comment = $_POST['comments'];
$cmds = $_POST['commands'];
if (get_magic_quotes_gpc()) {
 $cmds = stripslashes($cmds);
 $name = stripslashes($name);
 $comment = stripslashes($comment);
}

if ($envir > 0) {
 $link = mysql_connect('127.0.0.1', 'Rbatch', 'Rbatch');
 if (!$link) { echo "Unable to connect to DB: " . mysql_error(); exit; }
 if (!mysql_select_db('R')) { echo "Unable to select database: " . mysql_error(); exit; }

 $query = sprintf("INSERT INTO jobs (Status, Queued, Environment, Name, Commands, Comments) VALUES ('Queued', NOW(), %d, '%s', '%s', '%s')",
    $envir,
    mysql_real_escape_string($name),
    mysql_real_escape_string($cmds),
    mysql_real_escape_string($comment));

 // Perform Query
 $result = mysql_query($query);

 // Check result
 // This shows the actual query sent to MySQL, and the error. Useful for debugging.
 if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
 }

}
header("Location: /");
?>
