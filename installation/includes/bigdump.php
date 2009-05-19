<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * BigDump ver. 0.28b from 2007-06-08
 * Staggered import of an large MySQL Dump (like phpMyAdmin 2.x Dump)
 * Even through the webservers with hard runtime limit and those in safe mode
 * Works fine with Internet Explorer 7.0 and Firefox 2.x
 * 
 * Rewritten by Sam Moffatt from original work by Alexey Ozerov) for Joomla! 1.5
 * 
 * Rewritten by Nicholas Dionysopoulos for JoomlaPack Installer 3
 * 
 * Here are the request parameters this script cares about (must be present in $_REQUEST):
 * fn				Dump file to load
 * resdbtype		Database type
 * resdbhost		Database host name
 * resdbuser		Database user name
 * resdbpass		Database password
 * resdbname		Database name
 * resprefix		Database prefix
 */

// Some constants
define('VERSION',			'0.28b-jpi3');	// Script version; modified to indicate it's modified for JPI3
define('DATA_CHUNK_LENGTH',	65536);			// How many bytes to read per step
define('MAX_QUERY_LINES',	300);			// How many lines may be considered to be one query (except text lines)
define('LINESPERSESSION',	1000);			// Maximum lines to be executed per one import session
define('BYTESPERSESSION',	1024768);		// Maximum data to be restored per one import session
define('DELAYPERSESSION',	0);				// You can specify a sleep time in milliseconds after each session
define('TESTMODE',			false);			// Set to true to process the file without actually accessing the database

// Allowed comment delimiters: lines starting with these strings will be dropped by BigDump
$comment[] = '#'; // Standard comment lines are dropped by default
$comment[] = '-- ';
// $comment[]='---';      // Uncomment this line if using proprietary dump created by outdated mysqldump
// $comment[]='/*!';         // Or add your own string to leave out other proprietary things

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php JText::_('Database Restoration Script') ?></title>
<script type="text/javascript" src="includes/js/installation.js"></script>
</head>
<body>
<?php

// *******************************************************************************************
// If not familiar with PHP please don't change anything below this line
// *******************************************************************************************

ob_start();

header("Expires: Mon, 1 Dec 2003 01:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Clean and strip anything we don't want from user's input [0.27b]
/*
foreach ($_REQUEST as $key => $val) {
	$val = preg_replace("/[^_A-Za-z0-9-\.&=]/i", '', $val);
	$_REQUEST[$key] = $val;
}
*/

// Initialization
$error = false;
$file = false;

// Treat auto-mode flag
if(!isset($_REQUEST['auto']))
{
	$_REQUEST['auto'] = false;
}

// Open the file

if (!$error && isset ($_REQUEST["fn"])) {

	$_REQUEST["fn_original"] = $_REQUEST["fn"];
	$_REQUEST["fn"] = dirname(__FILE__).DS.'..'.DS.'sql'.DS.$_REQUEST["fn"];
	
	if ( !$file = fopen($_REQUEST["fn"], "rt") ) {
		echo ("<p class=\"error\">". JText::sprintf("Cant open file for import", $_REQUEST["fn"]) ."</p>\n");
		echo ("<p>". JText::_('CHECKDUMPFILE')."</p>\n");
		$error = true;
	}
	else
	{
		// Get the file size
		if (fseek($file, 0, SEEK_END) == 0) {
			$filesize = ftell($file);
		} else {
			echo ("<p class=\"error\">". JText::_('FILESIZEUNKNOWN') . $_REQUEST["fn"] . "</p>\n");
			$error = true;
		}		
	}
}

// *******************************************************************************************
// START IMPORT SESSION HERE
// *******************************************************************************************
if (!$error && isset ($_REQUEST["start"]) && isset ($_REQUEST["foffset"]) && eregi("(\.(sql))$", $_REQUEST["fn"])) {

	// Check start and foffset are numeric values
	if (!is_numeric($_REQUEST["start"]) || !is_numeric($_REQUEST["foffset"])) {
		echo ("<p class=\"error\">". JText::_('NONNUMERICOFFSET') ."</p>\n");
		$error = true;
	}

	if (!$error) {
		$_REQUEST["start"] = floor($_REQUEST["start"]);
		$_REQUEST["foffset"] = floor($_REQUEST["foffset"]);
	}

	// Check $_REQUEST["foffset"] upon $filesize
	if (!$error && $_REQUEST["foffset"] > $filesize) {
		echo ("<p class=\"error\">".JText::_('POINTEREOF')."</p>\n");
		$error = true;
	}

	// Set file pointer to $_REQUEST["foffset"]
	if (!$error && (fseek($file, $_REQUEST["foffset"]) != 0)) {
		echo ("<p class=\"error\">". JText::_('UNABLETOSETOFFSET') . $_REQUEST["foffset"] . "</p>\n");
		$error = true;
	}

	// Start processing queries from $file
	if (!$error) {
		$query = "";
		$queries = 0;
		$totalqueries = $_REQUEST["totalqueries"];
		$linenumber = $_REQUEST["start"];
		$querylines = 0;
		$inparents = false;
		$totalsizeread = 0;

		// Stay processing as long as the LINESPERSESSION is not reached or the query is still incomplete
		//while (($linenumber < ($_REQUEST["start"] + LINESPERSESSION) || $query != "") && ($totalsizeread <= BYTESPERSESSION)) {
		while (($linenumber < ($_REQUEST["start"] + LINESPERSESSION)) && ($totalsizeread <= BYTESPERSESSION)) {
			// Read the whole next line
			$dumpline = "";
			while (!feof($file) && substr($dumpline, -1) != "\n") {
				$dumpline .= fgets($file, DATA_CHUNK_LENGTH);
			}
			if ($dumpline === "")
				break;

			// Handle DOS and Mac encoded linebreaks (I don't know if it will work on Win32 or Mac Servers)
			$dumpline = str_replace("\r\n", "\n", $dumpline);
			$dumpline = str_replace("\r", "\n", $dumpline);

			// Skip comments and blank lines only if NOT in parents
			if (!$inparents) {
				$skipline = false;
				reset($comment);
				foreach ($comment as $comment_value) {
					if (!$inparents && (trim($dumpline) == "" || strpos($dumpline, $comment_value) === 0)) {
						$skipline = true;
						break;
					}
				}
				if ($skipline) {
					$linenumber++;
					continue;
				}
			}

			// Remove double back-slashes from the dumpline prior to count the quotes ('\\' can only be within strings)
			$dumpline_deslashed = str_replace("\\\\", "", $dumpline);

			// Count ' and \' in the dumpline to avoid query break within a text field ending by ;
			// Please don't use double quotes ('"')to surround strings, it wont work
			$parents = substr_count($dumpline_deslashed, "'") - substr_count($dumpline_deslashed, "\\'");
			if ($parents % 2 != 0)
				$inparents = !$inparents;

			// Add the line to query
			$query .= $dumpline;

			// Don't count the line if in parents (text fields may include unlimited linebreaks)
			if (!$inparents)
				$querylines++;

			// Stop if query contains more lines as defined by MAX_QUERY_LINES
			if ($querylines > MAX_QUERY_LINES) {
				echo ("<p class=\"error\">". JText::_('STOPPEDATLINE') ." $linenumber. </p>");
				echo ("<p>". JText::sprintf('TOOMANYLINES',MAX_QUERY_LINES)."</p>");
				$error = true;
				break;
			}
						
			// Use the variables I mentioned in this file's header to connect to db server
			$DBtype 	= $_REQUEST['resdbtype'];
			$DBhostname = $_REQUEST['resdbhost'];
			$DBuserName = $_REQUEST['resdbuser'];
			$DBpassword = $_REQUEST['resdbpass'];
			$DBname 	= $_REQUEST['resdbname'];
			$DBPrefix 	= $_REQUEST['resprefix'];

			$db = & JInstallationHelper::getDBO($DBtype, $DBhostname, $DBuserName, $DBpassword, $DBname, $DBPrefix);
			if(JError::isError($db)) jexit(JText::_('CONNECTION FAIL'));

			// Execute query if end of query detected (; as last character) AND NOT in parents
			if (ereg(";$", trim($dumpline)) && !$inparents) {
				if (!TESTMODE) {
					// For extra databases (with empty prefix) try to DROP a table if its CREATE TABLE command is present here
					// FIX 2.0.b1 : Does the same if the table name IS NOT abstract (i.e. it doesn't start with #__)
					if( substr($query, 0, 12) == 'CREATE TABLE')
					{
						// Yes, try to get the table name
						$restOfQuery = trim(substr($query, 12, strlen($query)-12 )); // Rest of query, after CREATE TABLE
						// Is there a backtick?
						if(substr($restOfQuery,0,1) == '`')
						{
							// There is... Good, we'll just find the matching backtick
							$pos = strpos($restOfQuery, '`', 1);
							$tableName = substr($restOfQuery,1,$pos - 1);
						}
						else
						{
							// Nope, let's assume the table name ends in the next blank character
							$pos = strpos($restOfQuery, ' ', 1);
							$tableName = substr($restOfQuery,1,$pos - 1);
						}
						unset($restOfQuery);
						
						// If the db has no prefix OR the table doesn't have a prefix, DROP it!
						if( ($DBPrefix == '') || (strpos($tableName, '#__', 0) !== 0) )
						{
							$dropQuery = 'DROP TABLE IF EXISTS `'.$tableName.'`;';
							$db->setQuery(trim($dropQuery));
							if (!$db->Query()) {
								echo ("<p class=\"error\">".JText::_('Error at the line') ." $linenumber: ". trim($dumpline) . "</p>\n");
								echo ("<p>".JText::_('Query:') .  trim(nl2br(htmlentities($dropQuery))) ."</p>\n");
								echo ("<p>MySQL: " . mysql_error() . "</p>\n");
								$error = true;
								break;
							}
						}
					}
					
					$db->setQuery(trim($query));
					if (!$db->Query()) {
						echo ("<p class=\"error\">".JText::_('Error at the line') ." $linenumber: ". trim($dumpline) . "</p>\n");
						echo ("<p>".JText::_('Query:') .  trim(nl2br(htmlentities($query))) ."</p>\n");
						echo ("<p>MySQL: " . mysql_error() . "</p>\n");
						$error = true;
						break;
					}
					$totalsizeread += strlen($query);
					$totalqueries++;
					$queries++;
					$query = "";
					$querylines = 0;
				}
			}
			$linenumber++;
		}
	}

	// Get the current file position

	if (!$error) {
		$foffset = ftell($file);
		if (!$foffset) {
			echo ("<p class=\"error\">".JText::_('CANTREADPOINTER')."</p>\n");
			$error = true;
		}
	}

	// Print statistics
	if (!$error) {
		$lines_this = $linenumber - $_REQUEST["start"];
		$lines_done = $linenumber -1;
		$lines_togo = ' ? ';
		$lines_tota = ' ? ';

		$queries_this = $queries;
		$queries_done = $totalqueries;
		$queries_togo = ' ? ';
		$queries_tota = ' ? ';

		$bytes_this = $foffset - $_REQUEST["foffset"];
		$bytes_done = $foffset;
		$kbytes_this = round($bytes_this / 1024, 2);
		$kbytes_done = round($bytes_done / 1024, 2);
		$mbytes_this = round($kbytes_this / 1024, 2);
		$mbytes_done = round($kbytes_done / 1024, 2);

		$bytes_togo = $filesize - $foffset;
		$bytes_tota = $filesize;
		$kbytes_togo = round($bytes_togo / 1024, 2);
		$kbytes_tota = round($bytes_tota / 1024, 2);
		$mbytes_togo = round($kbytes_togo / 1024, 2);
		$mbytes_tota = round($kbytes_tota / 1024, 2);

		$pct_this = ceil($bytes_this / $filesize * 100);
		$pct_done = ceil($foffset / $filesize * 100);
		$pct_togo = 100 - $pct_done;
		$pct_tota = 100;

		if ($bytes_togo == 0) {
			$lines_togo = '0';
			$lines_tota = $linenumber -1;
			$queries_togo = '0';
			$queries_tota = $totalqueries;
		}

		$pct_bar = "<div style=\"height:15px;width:$pct_done%;background-color:#000080;margin:0px;\"></div>";

		// Finish message and restart the script
		if (($linenumber < $_REQUEST["start"] + LINESPERSESSION) && ($totalsizeread < BYTESPERSESSION)) {
			echo ("<div id=\"installer\"><p class=\"successcentr\">".JText::_('CONGRATSEOF')."</p>\n");
			echo '<br />'. JText::_('CONTINUERESTORATION').'</div>';
			if($_REQUEST['auto'])
			{
				// Javascript call for restorations 
?>
<script type="text/javascript" language="Javascript">
	window.parent.autoNext();
</script>
<?php
			}
			$error = true;
		} else {
			if (DELAYPERSESSION != 0)
				echo ("<p class=\"centr\">".JText::sprintf('DELAYMSG',DELAYPERSESSION)."</p>\n");
			?><script language="JavaScript" type="text/javascript">window.setTimeout('submitForm(this.document.migrateForm,"dumpLoad")',500);</script>
			<div id="installer"><p><?php echo JText::_('LOADSQLFILE') ?></p></div>
			<?php echo $pct_bar; ?>
			<p>
				<?php echo $kbytes_done ?> / <?php echo $kbytes_tota ?> kB (<?php echo $queries?> queries)
			</p> 

			<form action="index.php" method="post" name="migrateForm" id="migrateForm" class="form-validate" target="restorationtarget">
				<input type="hidden" name="task" value="dumpLoad" />
				<input type="hidden" name="fn" value="<?php echo $_REQUEST["fn_original"]; ?>" />
			  	<input type="hidden" name="resdbtype" value="<?php echo $DBtype ?>" />
			  	<input type="hidden" name="resdbhost" value="<?php echo $DBhostname ?>" />
			  	<input type="hidden" name="resdbuser" value="<?php echo $DBuserName ?>" />
			  	<input type="hidden" name="resdbpass" value="<?php echo $DBpassword ?>" />
			  	<input type="hidden" name="resdbname" value="<?php echo $DBname ?>" />
			  	<input type="hidden" name="resprefix" value="<?php echo $DBPrefix ?>" />
			  	<input type="hidden" name="start" value="<?php echo $linenumber ?>" />
				<input type="hidden" name="foffset" value="<?php echo $foffset ?>" />
				<input type="hidden" name="totalqueries" value="<?php echo $totalqueries ?>" />
				<input type="hidden" name="auto" value="<?php echo $_REQUEST["auto"] ?>" />
			</form>
  <?php
		}
	} else
		echo ("<p class=\"error\">".JText::_('STOPPEDONERROR')."</p>\n");

}

if ($file)
	fclose($file);
?>