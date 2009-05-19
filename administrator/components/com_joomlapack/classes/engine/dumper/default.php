<?php
/**
* @package		JoomlaPack
* @copyright	Copyright (C) 2006-2008 JoomlaPack Developers. All rights reserved.
* @version		$Id$
* @license 		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @since		1.2.1
*
* JoomlaPack is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
**/
defined('_JEXEC') or die('Restricted access');

$config =& JoomlapackModelRegistry::getInstance();
define('JPROWSPERSTEP', $config->get('mnRowsPerStep') ); // Default is dumping 100 rows per step

/**
 * A generic MySQL database dump class, using Joomla!'s JDatabase class for handling the connection.
 * Configuration parameters:
 * isJoomla		<boolean>	True to use the existing Joomla! DB connection, false to create connection to another db
 * useFilters	<string> 	Should I use db table exclusion filters? Default equals the isJoomla setting above
 * host			<string>	MySQL database server host name or IP address
 * port			<string>	MySQL database server port (optional)
 * username		<string>	MySQL user name, for authentication
 * password		<string>	MySQL password, for authentication
 * database		<string>	MySQL database
 * dumpFile		<string>	Absolute path to dump file; must be writable (optional; if left blank it is automatically calculated)
 */
class JoomlapackDumperDefault extends JoomlapackCUBEParts
{
	// **********************************************************************
	// Configuration parameters
	// **********************************************************************

	/**
	 * True to use the existing Joomla! DB connection, false to create connection to another db
	 *
	 * @var boolean
	 */
	var $_isJoomla = true;
	
	/**
	 * Should I use db table exclusion filters? Default equals the isJoomla setting above
	 *
	 * @var string
	 */
	var $_useFilters = true;
	
	/**
	 * MySQL database server host name or IP address
	 *
	 * @var string
	 */
	var $_host = '';
	
	/**
	 * MySQL database server port (optional)
	 *
	 * @var string
	 */
	var $_port = '';
	
	/**
	 * MySQL user name, for authentication
	 *
	 * @var string
	 */
	var $_username = '';
	
	/**
	 * MySQL password, for authentication
	 *
	 * @var string
	 */
	var $_password = '';
	
	/**
	 * MySQL database
	 *
	 * @var string
	 */
	var $_database = '';
	
	/**
	 * Absolute path to dump file; must be writable (optional; if left blank it is automatically calculated)
	 *
	 * @var string
	 */
	var $_dumpFile = '';

	// **********************************************************************
	// Private fields
	// **********************************************************************
	
	/**
	 * Is this a database only backup? Assigned from JoomlapackCUBE settings.
	 *
	 * @var boolean
	 */
	var $_DBOnly = false;
	
	/**
	 * The database exclusion filters, as a simple array
	 *
	 * @var array
	 */
	var $_exclusionFilters = array();	
	
	/**
	 * A simple array of table names to be included in the backup set 
	 *
	 * @var array
	 */
	var $_tables = array();
	
	/**
	 * Is JoomFish installed? If it is, we have to cope for this and modify our
	 * database calls
	 *
	 * @var boolean
	 */
	var $_hasJoomFish = false;

	/**
	 * Absolute path to the temp file
	 *
	 * @var string
	 */
	var $_tempFile = '';
	
	/**
	 * Relative path of how the file should be saved in the archive
	 *
	 * @var string
	 */
	var $_saveAsName = '';
		
	/**
	 * The next table to backup
	 *
	 * @var string
	 */
	var $_nextTable;
	
	/**
	 * The next row of the table to start backing up from 
	 *
	 * @var integer
	 */
	var $_nextRange;
	
	/**
	 * Current table's row count
	 *
	 * @var integer
	 */
	var $_maxRange;
	
	/**
	 * Implements the constructor of the class
	 *
	 * @return JoomlapackDumperDefault
	 */
	function JoomlapackDumperDefault()
	{
		$this->_DomainName = "PackDB";
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: New instance");		
	}
	
	/**
	 * Implements the _prepare abstract method
	 *
	 */
	function _prepare()
	{
		// Process parameters, passed to us using the setup() public method
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: Processing parameters");
		if( is_array($this->_parametersArray) ) {
			$this->_isJoomla = array_key_exists('isJoomla', $this->_parametersArray) ? $this->_parametersArray['isJoomla'] : $this->_isJoomla;
			$this->_useFilters = array_key_exists('isJoomla', $this->_parametersArray) ? $this->_parametersArray['useFilters'] : $this->_isJoomla;
			$this->_host = array_key_exists('host', $this->_parametersArray) ? $this->_parametersArray['host'] : $this->_host;
			$this->_port = array_key_exists('port', $this->_parametersArray) ? $this->_parametersArray['port'] : $this->_port;
			$this->_username = array_key_exists('username', $this->_parametersArray) ? $this->_parametersArray['username'] : $this->_username;
			$this->_password = array_key_exists('password', $this->_parametersArray) ? $this->_parametersArray['password'] : $this->_password;
			$this->_dumpFile = array_key_exists('dumpFile', $this->_parametersArray) ? $this->_parametersArray['dumpFile'] : $this->_dumpFile;
			$this->_database = array_key_exists('database', $this->_parametersArray) ? $this->_parametersArray['database'] : $this->_dumpFile;
		}

		// Get DB backup only mode
		$configuration =& JoomlapackModelRegistry::getInstance();
		$this->_DBOnly = !($configuration->get('BackupType') == 'full');
		
		// Detect JoomFish
		$this->_hasJoomFish = file_exists(JPATH_SITE . '/administrator/components/com_joomfish/config.joomfish.php');
		
		// Fetch the database exlusion filters
		$this->_getExclusionFilters();
		if($this->getError()) return;
		
		// Find tables to be included and put them in the $_tables variable
		$this->_getTablesToBackup();
		if($this->getError()) return;
		
		// Find where to store the database backup files
		$this->_getBackupFilePaths();
		
		// Remove any leftovers
		$this->_removeOldFiles();
		
		// Initialize the algorithm
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: Initializing algorithm for first run");
		$this->_nextTable = array_shift( $this->_tables );
		$this->_nextRange = 0;
		
		$this->_isPrepared = true;
	}
	
	/**
	 * Implements the _run() abstract method
	 */
	function _run()
	{
		// Check if we are already done
		if ($this->_getState() == 'postrun') {
			JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: Already finished");
			$this->_Step = "";
			$this->_Substep = "";
			return;
		}
		
		// Mark ourselves as still running (we will test if we actually do towards the end ;) )
		$this->setState('running');
		
		// Initialize local variables
		$db =& $this->_getDB();
		if($this->getError()) return;
		
		if( !is_object($db) || ($db === false) )
		{
			$this->setError(__CLASS__.'::_run() Could not connect to database?!');
			return;
		}
		
		$outCreate	= ''; // Used for outputting CREATE TABLE commands
		$outData	= ''; // Used for outputting INSERT INTO commands
		
		$this->_enforceSQLCompatibility(); // Apply MySQL compatibility option
		if($this->getError()) return;
		
		// Get this table's canonical and abstract name
		$tableName = $this->_nextTable;
		$tableAbstract = trim($this->_getAbstract( $tableName ));
		
		// If it is the first run, find number of rows and get the CREATE TABLE command
		if( $this->_nextRange == 0 )
		{
			$this->_getRowCount( $tableAbstract );
			if($this->getError()) return;
			$outCreate = $this->_getCreateTable( $tableAbstract, $tableName );
			if($this->getError()) return;
		}
		
		// Ugly hack to make JoomlaPack skip over #__jp_temp
		if( $tableAbstract == '#__jp_temp' )
		{
			JoomlapackLogger::WriteLog(_JP_LOG_INFO, "Skipping table " . $this->_nextTable);
			$this->_nextRange = $this->_maxRange + 1;
			$numRows = 0;// joostina pach
		}
		
		// Check if we have more work to do on this table
		if( $this->_nextRange < $this->_maxRange )
		{
			// Get the number of rows left to dump from the current table
			$sql = "select * from `$tableAbstract`";
			$db->setQuery( $sql, $this->_nextRange, JPROWSPERSTEP );
			$db->query();
	
			$numRows = $db->getNumRows();
	
			JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "Dumping $numRows rows of " . $this->_nextTable);
			
			// Only dump if we have more than 0 rows to dump
			if ($numRows > 0)
			{
				$startRange = $this->_nextRange; // This is the range we start from, minus 1
				$sql = "select * from `$tableAbstract`";
				$db->setQuery( $sql, $startRange, JPROWSPERSTEP ); // 1.2a - Optimized database dump to use fewer db queries
				$dataDump = $db->loadAssocList();
				$numRows = 0;
				foreach( $dataDump as $myRow ) {
					$numRows++;
					$sql = "select * from `$tableAbstract`";
					$db->setQuery( $sql, $startRange, 1 );
					$numOfFields = count( $myRow );
					
					$outData .= "INSERT INTO `" . ($this->_DBOnly ? $tableName : $tableAbstract) . "` VALUES (";
					
					// Step through each of the row's values
					$fieldID = 0;
					
					// Used in running backup fix
					$isCurrentBackupEntry = false;

					// Fix 1.2a - NULL values were being skipped
					foreach( $myRow as $value )
					{
						// The ID of the field, used to determine placement of commas
						$fieldID++;
						
						// Fix 2.0: Mark currently running backup as succesfull in the DB snapshot
						if($tableAbstract == '#__jp_stats')
						{
							if($fieldID == 1)
							{
								// Compare the ID to the currently running
								$cube =& JoomlapackCUBE::getInstance();
								$isCurrentBackupEntry = ($cube->_statID == $value);
							}
							elseif ($fieldID == 6)
							{
								// Treat the status field
								$value = $isCurrentBackupEntry ? 'complete' : $value;
							}
						}
						
						// Post-process the value
						if( is_null($value) )
						{
							$outData .= "NULL"; // Cope with null values
						} else {
							// Accomodate if runtime magic quotes are present
							$value = get_magic_quotes_runtime() ? stripslashes( $value ) : $value;
							$outData .= $db->Quote($value);
						}
						if( $fieldID < $numOfFields ) $outData .= ', ';						
					} // foreach
				$outData .=");\n";					
				} // for (all rows left)
			} // if numRows > 0...
			
			// Advance the _nextRange pointer
			$this->_nextRange += ($numRows != 0) ? $numRows : 1;
			
			$this->_Step = $tableName;
			$this->_Substep = $this->_nextRange . ' / ' . $this->_maxRange;			
		} // if more work on the table
		else
		{
			// Tell the user we are done with the table
			JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "Done with " . $this->_nextTable);
			
			if( count($this->_tables) == 0 )
			{
				// We have finished dumping the database!
				JoomlapackLogger::WriteLog(_JP_LOG_INFO, "Database has been successfully dumped to SQL file(s)");
				$this->setState('postrun');
				$this->_Step = '';
				$this->_Substep = '';
				$this->_nextTable = '';
				$this->_nextRange = 0;
			} else {
				// Switch tables
				$this->_nextTable = array_shift( $this->_tables );
				$this->_nextRange = 0;				
				$this->_Step = $this->_nextTable;
				$this->_Substep = '';			
			}
		}
		
		$this->_writeDump( $outCreate, $outData, $tableAbstract );
		if($this->getError()) return;
		$null = null;
		$this->_writeline($null);
	}
	
	/**
	 * Implements the _finalize() abstract method
	 *
	 */
	function _finalize()
	{
		// Add Extension Filter SQL statements (if any), only for the MAIN DATABASE!!!
		if($this->_isJoomla)
		{
			jpimport('models.extfilter',true);
			$extModel = new JoomlapackModelExtfilter;
			$extraSQL =& $extModel->getExtraSQL();
			$this->_writeline($extraSQL);
			unset($extraSQL);
			unset($extModel);
		}
		
		// If we are not just doing a main db only backup, add the SQL file to the archive
		$configuration =& JoomlapackModelRegistry::getInstance();
		if( $configuration->get('BackupType') != 'dbonly' )
		{
			JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "Adding the SQL dump to the archive");
			
			$cube =& JoomlapackCUBE::getInstance();
			$provisioning =& $cube->getProvisioning();
			$archiver =& $provisioning->getArchiverEngine();
			$archiver->addFileRenamed( $this->_tempFile, $this->_saveAsName );
			unset($archiver);
			if($this->getError()) return;
			
			JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "Removing temporary file");
			JoomlapackCUBETempfiles::unregisterAndDeleteTempFile( $this->_tempFile, true );
			if($this->getError()) return;
		}		
		$this->_isFinished = true;
	}
	
	/**
	 * Gets the database exclusion filters through the Filter Manager class
	 */
	function _getExclusionFilters()
	{
		if( $this->_useFilters )
		{
			JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: Retrieving db exclusion filters");
			jpimport('core.utility.filtermanager');
			$filterManager = new JoomlapackCUBEFiltermanager();
			if(!is_object($filterManager))
			{
				$this->setError(__CLASS__.'::_getExclusionFilters() FilterManager is not an object');
				return false;
			}
			$filterManager->init();
			$this->_exclusionFilters = $filterManager->getFilters('database');
			JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: Retrieved db exclusion filters, OK.");
		} else {
			JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: Skipping filters");
			$this->_exclusionFilters = array();
		}
	}
	
	/**
	 * Finds the table names to be included in the backup set and puts them in the
	 * $this->_tables array.
	 */
	function _getTablesToBackup()
	{
		$jregistry =& JFactory::getConfig();
		$prefix = $jregistry->getValue('config.dbprefix');
		
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: Finding tables to include in the backup set");
		$db =& $this->_getDB();
		if($this->getError()) return;
		
		$sql = "show tables";
		$db->setQuery( $sql );
		$db->query();
		
		$allTables = $db->loadResultArray();
		
		if( count($this->_exclusionFilters) > 0 )
		{
			// If we have filters, make sure the tables pass the filtering
			$this->_tables = array();
			foreach( $allTables as $myTable )
			{
				$tableAbstract = str_replace($prefix, '#__', $myTable);
				if( !in_array( $tableAbstract, $this->_exclusionFilters ) )
					if(!(substr($tableAbstract,0,4) == 'bak_')) // Skip backup tables
						$this->_tables[] = $myTable;
				
			}
		} else {
			// If no filters are set, just exclude any backup tables
			$this->_tables = array();
			foreach( $allTables as $myTable )
			{
				$tableAbstract = str_replace($prefix, '#__', $myTable);
				if(!(substr($tableAbstract,0,4) == 'bak_')) // Skip backup tables
					$this->_tables[] = $myTable;				
			}
		}
	}
	
	/**
	 * Find where to store the backup files
	 */
	function _getBackupFilePaths()
	{
		$configuration =& JoomlapackModelRegistry::getInstance();
		
		switch($configuration->get('BackupType'))
		{
			case 'dbonly':
				// On DB Only backups we use different naming, no matter what's the setting
				JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: Only dump database mode detected");
				// Fix 2.0: Backup file name MUST be taken from the statitics record!
				$cube =& JoomlapackCUBE::getInstance();
				$statID = $cube->_statID;
				$statModel = new JoomlapackModelStatistics($statID);
				$statModel->setId($statID);
				$statRecord =& $statModel->getStatistic();
				$this->_tempFile = $statRecord->absolute_path;
				$this->_saveAsName = '';
				break;
				
			case 'full':
				if( $this->_dumpFile != '' )
				{
					JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: Forced filename using dumpFile found.");
					// If the dumpFile was set, forcibly use this value
					$this->_tempFile = JoomlapackCUBETempfiles::registerTempFile( dechex(crc32(microtime().$this->_dumpFile)) );
					$this->_saveAsName = 'installation/sql/'.$this->_dumpFile;				
				} else {
					if( $this->_isJoomla )
					{
						// Joomla! Core Database, use the JoomlaPack way of figuring out the filenames
						JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: Core database");
						$this->_tempFile = JoomlapackCUBETempfiles::registerTempFile( dechex(crc32(microtime().'joomla.sql')) );
						$this->_saveAsName = 'installation/sql/joomla.sql';				
					} else {
						// External databases, we use the database's name
						JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: External database");
						$this->_tempFile = JoomlapackCUBETempfiles::registerTempFile( dechex(crc32(microtime().$this->_database.'.sql')) );
						$this->_saveAsName = 'installation/sql/'.$this->_database.'.sql';				
					}
				}
				break;
				
			case 'extradbonly':
				if( $this->_dumpFile != '' )
				{
					JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: Forced filename using dumpFile found.");
					// If the dumpFile was set, forcibly use this value
					$this->_tempFile = JoomlapackCUBETempfiles::registerTempFile( dechex(crc32(microtime().$this->_dumpFile)) );
					$this->_saveAsName = $this->_dumpFile;				
				} else {
					if( $this->_isJoomla )
					{
						// Joomla! Core Database, use the JoomlaPack way of figuring out the filenames
						JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: Core database");
						$this->_tempFile = JoomlapackCUBETempfiles::registerTempFile( dechex(crc32(microtime().'joomla.sql')) );
						$this->_saveAsName = 'joomla.sql';				
					} else {
						// External databases, we use the database's name
						JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDumperDefault :: External database");
						$this->_tempFile = JoomlapackCUBETempfiles::registerTempFile( dechex(crc32(microtime().$this->_database.'.sql')) );
						$this->_saveAsName = $this->_database.'.sql';				
					}
				}
				break;
		}
		
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDomainDBBackup :: SQL temp file is " . $this->_tempFile);
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDomainDBBackup :: SQL file location in archive is " . $this->_saveAsName);
	}
	
	/**
	 * Deletes any leftover files from previous backup attempts
	 *
	 */
	function _removeOldFiles()
	{
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackDomainDBBackup :: Deleting leftover files, if any");
		if( file_exists( $this->_tempFile ) ) @unlink( $this->_tempFile );
	}
	
	/**
	 * Applies the SQL compatibility setting
	 */
	function _enforceSQLCompatibility()
	{
		$configuration =& JoomlapackModelRegistry::getInstance();
		$db =& $this->_getDB();
		if($this->getError()) return;
		
		switch( $configuration->get('MySQLCompat') )
		{
			case 'compat':
				$sql = "SET SESSION sql_mode='HIGH_NOT_PRECEDENCE,NO_TABLE_OPTIONS'";
				break;
			
			default:
				$sql = "SET SESSION sql_mode=''";
				break;
		}
		
		$db->setQuery( $sql );
		$db->query();
	}
	
	/**
	 * Returns a table's abstract name (replacing the prefix with the magic #__ string)
	 *
	 * @param string $tableName The canonical name, e.g. 'jos_content'
	 * @return string The abstract name, e.g. '#__content'
	 */
	function _getAbstract( $tableName )
	{
		// FIX 2.0.b1 - Don't return abstract names for non-core tables
		if(!$this->_isJoomla) return $tableName;
		
		// FIX 1.2 Stable - Handle (very rare) cases with an empty db prefix
		$jregistry =& JFactory::getConfig();
		$prefix = $jregistry->getValue('config.dbprefix');

		switch( $prefix )
		{
			case '':
				// This is more of a hack; it assumes all tables are Joomla! tables if the prefix is empty.
				return '#__' . $tableName;
				break;

			default:
				// Normal behaviour for 99% of sites
				return str_replace( $prefix, "#__", $tableName );
				break;
		}
	}
	
	/**
	 * Gets the row count for table $tableAbstract. Also updates the $this->_maxRange variable.
	 *
	 * @param string $tableAbstract The abstract name of the table (works with canonical names too, though)
	 * @return integer Row count of the table
	 */
	function _getRowCount( $tableAbstract )
	{
		$db =& $this->_getDB();
		if($this->getError()) return;

		$sql = "SELECT COUNT(*) FROM `$tableAbstract`";
		$db->setQuery( $sql );
		$this->_maxRange = $this->_hasJoomFish ? $db->loadResult(false) : $db->loadResult();
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "Rows on " . $this->_nextTable . " : " . $this->_maxRange);
		
		return $this->_maxRange;
	}
	
	/**
	 * Gets the CREATE TABLE command for a given table
	 *
	 * @param string $tableAbstract The abstract name of the table (works with canonical names too, though)
	 * @param string $tableName The canonical name of the table
	 * @return string The CREATE TABLE command, w/out newlines
	 */
	function _getCreateTable( $tableAbstract, $tableName )
	{
		$db =& $this->_getDB();
		if($this->getError()) return;

		$sql = "SHOW CREATE TABLE `$tableAbstract`";
		$db->setQuery( $sql );
		$db->query();
		$temp = $db->loadAssocList();
		$tablesql = $temp[0]['Create Table'];
		unset( $temp );
		
		// Replace table prefix with the #__ placeholder, but do not replace it if this
		// is a database only backup
		if(!$this->_DBOnly)
		{
			// This replacing algorithm takes into account empty prefixes.
			$tablesql = str_replace( $tableName , $tableAbstract, $tablesql );
		}

		// Replace newlines with spaces 
		$tablesql = str_replace( "\n", " ", $tablesql ) . ";\n";
		
		if( $this->_DBOnly )
		{
			$drop = "DROP TABLE IF EXISTS `$tableName`;\n";
			$tablesql = $drop . $tablesql;
		}

		return $tablesql;
	}
	
	/**
	 * Writes the SQL dump into the output files. If it fails, it sets the error
	 *
	 * @param string $outCreate Any CREATE TABLE / DROP TABLE commands
	 * @param string $outData Any INSERT INTO commands
	 * @param string $tableAbstract The current table's abstract name
	 * @return boolean TRUE on successful write, FALSE otherwise
	 */
	function _writeDump( &$outCreate, &$outData, $tableAbstract )
	{
		$result = ($outCreate != '') ? $this->_writeline( $outCreate ) : true;
		if( !$result )
		{
			$errorMessage = 'Writing to ' . $this->_tempFile . ' has failed. Check permissions!';
			$this->setError($errorMessage);
			return false;
		}
		
		$result = ($outData != '') ? $this->_writeline( $outData ) : true;
		if( !$result )
		{
			$errorMessage = 'Writing to ' . $this->_tempFile . ' has failed. Check permissions!';
			$this->setError($errorMessage);
			return false;
		}
		
		return true;
	}
	
	/**
	* Saves the string in $fileData to the file $backupfile. Returns TRUE. If saving
	* failed, return value is FALSE.
	* @param string $fileData Data to write. Set to null to close the file handle.
	* @return boolean TRUE is saving to the file succeeded
	*/
	function _writeline(&$fileData) {
		static $fp;

		if(!$fp)
		{
			$fp = @fopen($this->_tempFile, 'a');
			if($fp === false)
			{
				$this->setError('Could not open '.$this->_tempFile.' for append, in DB dump.');
				return;
			}
		}
		
		if(is_null($fileData))
		{
			if($fp) @fclose($fp);
			$fp = null;
			return true;
		}
		else
		{
			if ($fp) {
				fwrite($fp, $fileData);
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Return an instance of JDatabase
	 *
	 * @return JDatabase
	 */
	function &_getDB()
	{
		if( $this->_isJoomla )
		{
			// Core Joomla! database, get the existing instance
			$db =& JFactory::getDBO();
			return $db;
		}
		else
		{
			// Joomla! 1.5.x
			jimport('joomla.database.database');
			jimport('joomla.database.table');
			
			$conf =& JFactory::getConfig();
			
			$host 		= $this->_host . ($this->_port != '' ? ':' . $this->_port : '');
			$user 		= $this->_username;
			$password 	= $this->_password;
			$database	= $this->_database;
			
			$prefix 	= '';
			$driver 	= $conf->getValue('config.dbtype');
			$debug 		= $conf->getValue('config.debug');
			
			$options	= array ( 'driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );
			
			$db =& JDatabase::getInstance( $options );
			
			if ( JError::isError($db) ) {
				$errorMessage = "JoomlapackDumperDefault :: Database Error:" . $db->toString();
				$this->setError($errorMessage);
				return false;
			}
			
			if ($db->getErrorNum() > 0) {
				$errorMessage = 'JDatabase::getInstance: Could not connect to database <br/>' . 'joomla.library:'.$db->getErrorNum().' - '.$db->getErrorMsg();
				$this->setError($errorMessage);
				return false;
			}
			
			$db->debug( $debug );
			return $db;
		}
	}
	
}