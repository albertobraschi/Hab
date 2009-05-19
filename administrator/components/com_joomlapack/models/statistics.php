<?php
/**
 * @package JoomlaPack
 * @version $id$
 * @license GNU General Public License, version 2 or later
 * @author JoomlaPack Developers
 * @copyright Copyright 2006-2008 JoomlaPack Developers
 * @since 1.3
 */
defined('_JEXEC') or die('Restricted access');

/**
 * JoomlaPack statistics model class
 * used for all requirements of backup statistics in JP
 *
 */
class JoomlapackModelStatistics extends JModel
{
	/** @var integer Backup Statistics ID */
	var $_id;
	
	/** @var stdClass Statistics object */
	var $_stats;
	
	/** @var TableStatistics The statistics table being updated */
	var $_table;
	
	/** @var int Total number of backup statistics records */
	var $_total;
	
	/** @var array A list of backup statistics records */
	var $_list;
	
	/**
	 * Overides the JModel implementation to provide a Singleton implementation
	 *
	 * @param	string	The model type to instantiate
	 * @param	string	Prefix for the model class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JoomlapackModelStatistics	A model object, or false on failure
	 */
	function &getInstance( $type = 'model', $prefix = '', $config = array() )
	{
		static $instance;
		
		if(!is_object($instance))
		{
			$instance = new JoomlapackModelStatistics();	
		}
		
		return $instance;
	}
	
	/**
	 * Constructor. Sets the internal reference to Statistics ID.
	 *
	 */
	function __construct($id = 0)
	{
		global $mainframe;
		
		parent::__construct();
		
		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'));
		$limitstart = $mainframe->getUserStateFromRequest(JRequest::getCmd('option','com_joomlapack') .'profileslimitstart','limitstart',0);
		
		// Set the page pagination variables
		$this->setState('limit',$limit);
		$this->setState('limitstart',$limitstart);
		
		$this->setId($id);
	}
	
	/**
	 * Sets a Profile ID and resets internal data
	 *
	 * @param int $id Profile ID
	 */
	function setId($id=0)
	{
		$this->_id = $id;
		$this->_stats = null;
	}
	
	/**
	 * Returns the entry for the backup statistic record whose ID is loaded in the model
	 *
	 * @return stdClass An object representing the backup statistic record
	 */
	function &getStatistic()
	{
		if(empty($this->_stats))
		{
			$db =& $this->getDBO();
			$query = "SELECT * FROM #__jp_stats WHERE `id`".
					" = ".$this->_id;
			$db->setQuery($query);
			$db->query();
			$this->_stats = $db->loadObject();
		}
		return $this->_stats;
	}
	
	/**
	 * Returns a list of backup statistics records, respecting the pagination
	 *
	 * @return unknown
	 */
	function &getStatisticsList($overrideLimits = false)
	{
		if( empty($this->_list) )
		{
			$db =& $this->getDBO();
			$query = "SELECT * FROM ".$db->nameQuote('#__jp_stats')." ORDER BY ".$db->nameQuote('id')." DESC";
			$limitstart = $this->getState('limitstart');
			$limit = $this->getState('limit');
			if($overrideLimits)
				$this->_list = $this->_getList($query);
			else
				$this->_list = $this->_getList($query, $limitstart, $limit); 
		}
		
		return $this->_list;
	}
	
	/**
	 * Multiple backup attempts can share the same backup file name. Only
	 * the last backup attempt's file is considered valid. Previous attempts
	 * have to be deemed "obsolete". This method returns a list of backup
	 * statistics ID's with "valid"-looking names. IT DOES NOT CHECK FOR THE
	 * EXISTENCE OF THE BACKUP FILE!
	 * 
	 * @return array A list of ID's for records w/ "valid"-looking backup files 
	 *
	 */
	function &getValidLookingBackupFiles()
	{
		$db =& JFactory::getDBO();
		$query = "SELECT MAX(id) as `id` FROM #__jp_stats as j WHERE ".
			$db->nameQuote('status') ."=".$db->Quote('complete').
			" GROUP BY ".$db->nameQuote('absolute_path');
		$db->setQuery($query);
		$array = $db->loadResultArray();
		if( !is_array($array) ) $array = array();
		return $array;
	}
	
	/**
	 * Returns the same list as getStatisticsList(), but includes an extra field
	 * named 'meta' which categorises attempts based on their backup archive status
	 *
	 * @return array An object array of backup attempts
	 */
	function &getStatisticsListWithMeta($overrideLimits = false)
	{
		$allStats =& $this->getStatisticsList($overrideLimits);
		$valid =& $this->getValidLookingBackupFiles();
		
		jimport('joomla.filesystem.file');
		
		if(!empty($allStats))
		{
			reset($allStats);
			while(list($key, $value) = each($allStats))
			{
				$stat =& $allStats[$key];
				if(in_array($stat->id, $valid))
				{
					$archiveFile = $stat->absolute_path;
					if(JFile::exists($archiveFile))
					{
						// In valid list, archive exists
						$stat->meta = 'ok';
						$stat->size = filesize($archiveFile);				
					}
					else
					{
						// In valid list, archive does not exist
						$stat->meta = 'obsolete';
					}
				}
				else
				{
					switch($stat->status)
					{
						case 'run':
							$stat->meta = 'pending';
							break;
							
						case 'fail':
							$stat->meta = 'fail';
							break;
							
						default:
							$stat->meta = 'obsolete';
							break;
					}
				}
			}
		}
			
		unset($valid);
		return $allStats;
	}
	
	function getLatestBackupFilename()
	{
		$db =& $this->getDBO();
		$query = 'SELECT max(id) FROM #__jp_stats';
		$db->setQuery($query);
		$id = $db->loadResult();
		
		if(empty($id)) return '';
		
		$this->setId($id);
		$record =& $this->getStatistic();
		return $record->absolute_path;
	}
	
	/**
	 * Returns the details of the latest backup as HTML
	 *
	 * @return string HTML
	 * 
	 * @todo Move this into a helper class
	 */
	function getLatestBackupDetails()
	{
		$db =& $this->getDBO();
		$query = 'SELECT max(id) FROM #__jp_stats';
		$db->setQuery($query);
		$id = $db->loadResult();
		
		if(empty($id)) return '<p>'.JText::_('BACKUP_STATUS_NONE').'</p>';
		
		$this->setId($id);
		$record =& $this->getStatistic();

		jimport('joomla.utilities.date');
		
		switch($record->status)
		{
			case 'run':
				$status = JText::_('STATS_LABEL_STATUS_PENDING');
				break;

			case 'fail':
				$status = JText::_('STATS_LABEL_STATUS_FAIL');
				break;
				
			case 'complete':
				$status = JText::_('STATS_LABEL_STATUS_OK');
				break;
		}
		
		switch($record->origin)
		{
			case 'frontend':
				$origin = JText::_('STATS_LABEL_ORIGIN_FRONTEND');
				break;
				
			case 'backend':
				$origin = JText::_('STATS_LABEL_ORIGIN_BACKEND');
				break;
		}
		
		switch($record->type)
		{
			case 'full':
				$type = JText::_('STATS_LABEL_TYPE_FULL');
				break;
				
			case 'dbonly':
				$type = JText::_('STATS_LABEL_TYPE_DBONLY');
				break;
				
			case 'extradbonly':
				$type = JText::_('STATS_LABEL_TYPE_EXTRADBONLY');
				break;
		}

		$startTime = new JDate($record->backupstart);
		
		$html = '<table>';
		$html .= '<tr><td>'.JText::_('STATS_LABEL_START').'</td><td>'.$startTime->toFormat(JText::_('DATE_FORMAT_LC4')).'</td></tr>';
		$html .= '<tr><td>'.JText::_('STATS_LABEL_DESCRIPTION').'</td><td>'.$record->description.'</td></tr>';
		$html .= '<tr><td>'.JText::_('STATS_LABEL_STATUS').'</td><td>'.$status.'</td></tr>';
		$html .= '<tr><td>'.JText::_('STATS_LABEL_ORIGIN').'</td><td>'.$origin.'</td></tr>';
		$html .= '<tr><td>'.JText::_('STATS_LABEL_TYPE').'</td><td>'.$type.'</td></tr>';
		$html .= '</table>';
		
		return $html;
	}
	
	/**
	 * Returns a list of (the absolute paths to) old backup files which should be deleted,
	 * based on user's quota settings
	 *
	 * @return array
	 */
	function getOldBackupsToDelete()
	{
		// If no quota settings are enabled, quit
		$registry =& JoomlapackModelRegistry::getInstance();
		$useCountQuotas = $registry->get('enableCountQuotas');
		$useSizeQuotas = $registry->get('enableSizeQuotas');
		if(! ($useCountQuotas || $useSizeQuotas) )
		{
			JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "No quotas were defined; old backup files will be kept intact" );
			return array(); // No quota limits were requested
		}
		
		$latestBackup = $this->getLatestBackupFilename();
		
		// Get quota values
		$countQuota = $registry->get('countQuota');
		$sizeQuota = $registry->get('sizeQuota');
		
		// Get valid-looking backup ID's
		$validIDs =& $this->getValidLookingBackupFiles();
		
		// Create a list of valid files
		$allFiles = array();
		foreach($validIDs as $id)
		{
			$filename = $this->getFilename($id);
			if(!is_null($filename))
			{
				// Only process existing files
				$filesize = @filesize($filename);
				$allFiles[] = array('filename' => $filename, 'size' => $filesize);
			}
		}
		unset($validIDs);
		
		// If there are no files, exit early
		if(count($allFiles) == 0)
		{
			JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "There were no old backup files to apply quotas on" );
			return array();
		}
		
		// Init arrays
		$ret = array();
		$leftover = array();
		
		// Do we need to apply count quotas?
		if($useCountQuotas && is_numeric($countQuota) && !($countQuota <= 0) )
		{
			// Are there more files than the quota limit?
			if( !(count($allFiles) > $countQuota) )
			{
				// No, effectively skip the quota checking
				$leftover =& $allFiles;
			}
			else
			{
				JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "Processing count quotas" );
				// Yes, aply the quota setting. Add to $ret all entries minus the last
				// $countQuota ones.
				$count = 0;
				$checkLimit = count($allFiles) - $countQuota;
				// Only process if at least one file (current backup!) is to be left
				if(!($checkLimit == 0)) foreach($allFiles as $def)
				{
					$count++;
					if($count <= $checkLimit)
					{
						if($latestBackup == $def['filename'])
						{
							$count--;
						}
						else
							$ret[] = $def['filename'];
					}
					else
					{
						$leftover[] = $def;
					}
				}
				unset($allFiles);
			}
		}
		else
		{
			// No count quotas are applied
			$leftover =& $allFiles;
		}
		
		// Do we need to apply size quotas?
		if( $useSizeQuotas && is_numeric($sizeQuota) && !($sizeQuota <= 0) && (count($leftover) > 0) )
		{
			JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "Processing size quotas" );
			// OK, let's start counting bytes!
			$sizeQuota = $sizeQuota * 1024 * 1024; // Convert size quota from Mb to bytes
			$runningSize = 0;
			while(count($leftover) > 0)
			{
				// Each time, remove the last element of the backup array and calculate
				// running size. If it's over the limit, add the archive to the return array.
				$def = array_pop($leftover);
				$runningSize += $def['size'];
				if($runningSize >= $sizeQuota)
				{
					if($latestBackup == $def['filename'])
					{
						$runningSize -= $def['size'];
					}
					else
						$ret[] = $def['filename'];
				}
			}
			
		}
		
		
		// Return the rest of the entries, if any
		return $ret;
	}
	
	/**
	 * Returns the filename of the backup archive for the specified backup ID, or null
	 * if the backup type is wrong or the file doesn't exist
	 *
	 * @param int $id The backup ID
	 * @return string|null The filename or null if it's not applicable
	 */
	function getFilename($id)
	{
		$this->setId($id);
		$stat =& $this->getStatistic();
		$filename = $stat->absolute_path;
		// Do not return filename for invalid backups
		if($stat->status != 'complete')
		{
			return null;
		}
		
		return $filename;
	}
	
	/**
	 * Gets a list of stats entries marked as "running". Used to detect failed
	 * backup attempts
	 *
	 * @return array
	 */
	function &getRunningStatisticsList()
	{
		$db =& $this->getDBO();
		$query = "SELECT * FROM ".$db->nameQuote('#__jp_stats') .
				' WHERE '.$db->nameQuote('status').' = '.$db->Quote('run');
		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');
		$list = $this->_getList($query); 
		return $list;
	}
	
	/**
	 * Saves a statistics record
	 *
	 * @param object|array $data The data to be bound and saved
	 * @return bool True on success
	 */
	function save(&$data)
	{
		// Get the table
		$this->_table =& $this->getTable('Statistic');
		// Try to save the data
		if(!$this->_table->save($data))
		{
			// Oops... Something wrong happened
			$this->setError($this->_table->getError());
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * Returns the last saved table
	 *
	 * @return JTable
	 */
	function &getSavedTable()
	{
		return $this->_table;
	}
	
	/**
	 * Delete the stats record whose ID is set in the model
	 *
	 * @return bool True on success
	 */
	function delete()
	{
		$db =& $this->getDBO();
		
		if( (!is_numeric($this->_id)) || ($this->_id <= 0) )
		{
			$this->setError(JText::_('STATS_ERROR_INVALIDID'));
			return false;
		}
		
		// Try to delete file (don't die if you can't, though!)
		$record =& $this->getStatistic();
		$filename = $record->absolute_path;
		if(!empty($filename)) @unlink($filename);
		unset($record);
		
		// Delete record
		$sql = 'DELETE FROM '.$db->nameQuote('#__jp_stats').' WHERE '.
				$db->nameQuote('id').' = '.$this->_id;
		$db->setQuery($sql);
		if(!$db->query())
		{
			$this->setError($db->getError());
			return false;
		}
		
		return true;
	}

	/**
	 * Delete the backup file of the stats record whose ID is set in the model
	 *
	 * @return bool True on success
	 */
	function deleteFile()
	{
		$db =& $this->getDBO();
		
		if( (!is_numeric($this->_id)) || ($this->_id <= 0) )
		{
			$this->setError(JText::_('STATS_ERROR_INVALIDID'));
			return false;
		}
		
		// Try to delete file (don't die if you can't, though!)
		$record =& $this->getStatistic();
		$filename = $record->absolute_path;
		if(!empty($filename)) return @unlink($filename);
	}

	/**
	 * Get a pagination object
	 * 
	 * @access public
	 * @return JPagination
	 *
	 */
	function &getPagination()
	{
		if( empty($this->_pagination) )
		{
			// Import the pagination library
			jimport('joomla.html.pagination');
			
			// Prepare pagination values
			$total = $this->getTotal();
			$limitstart = $this->getState('limitstart');
			$limit = $this->getState('limit');
			
			// Create the pagination object
			$this->_pagination = new JPagination($total, $limitstart, $limit);
		}
		
		return $this->_pagination;
	}
	
	/**
	 * Get number of profile items
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		if( empty($this->_total) )
		{
			$db =& $this->getDBO();
			$query = "SELECT * FROM ".$db->nameQuote('#__jp_stats');
			$this->_total = $this->_getListCount($query);
		}
		
		return $this->_total;
	}

}