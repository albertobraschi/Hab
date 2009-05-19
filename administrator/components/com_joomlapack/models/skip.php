<?php
/**
 * @package JoomlaPack
 * @version $id$
 * @license GNU General Public License, version 2 or later
 * @author JoomlaPack Developers
 * @copyright Copyright 2006-2008 JoomlaPack Developers
 * @since 2.1
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Skip Contained Files / SubDirectories Filters 
 *
 */
class JoomlapackModelSkip extends JModel
{
	/**
	 * Cache of the SkipFiles list attached to the current profile 
	 *
	 * @var array
	 */
	var $_skipFiles = null;

	/**
	 * Cache of the SkipDirectories list attached to the current profile 
	 *
	 * @var array
	 */
	var $_skipDirectories = null;
	
	/**
	 * Queries the filter database to find out if a files filter is set for a given folder
	 *
	 * @param string $filePath Relative path to a folder
	 */
	function isFilesSetFor($filePath)
	{
		if(!is_array($this->_skipFiles))
		{
			$this->_loadFilters();
		}
		
		$filePath = $this->sanitizeFilePath($filePath);
		
		return in_array($filePath, $this->_skipFiles);
	}

	/**
	 * Queries the filter database to find out if a directories filter is set for a given folder
	 *
	 * @param string $filePath Relative path to a folder
	 */
	function isDirectoriesSetFor($filePath)
	{
		if(!is_array($this->_skipDirectories))
		{
			$this->_loadFilters();
		}
		
		$filePath = $this->sanitizeFilePath($filePath);
		
		return in_array($filePath, $this->_skipDirectories);
	}
	
	function enableFilesFilter($filePath)
	{
		if(!is_array($this->_skipFiles))
		{
			$this->_loadFilters();
		}
		
		$filePath = $this->sanitizeFilePath($filePath);
		
		// Only set if it's not already set
		if(!$this->isFilesSetFor($filePath))
		{
			// Get active profile
			$session =& JFactory::getSession();
			$profile = $session->get('profile', null, 'joomlapack');
			
			$db =& $this->getDBO();
			$sql = "INSERT INTO ".$db->nameQuote('#__jp_exclusion').
				'('.$db->nameQuote('profile').', '.$db->nameQuote('class').', '
				.$db->nameQuote('value').') VALUES ('.
				$db->Quote($profile).', '.$db->Quote('Skipfiles').', '.$db->Quote($filePath).')';
			$db->setQuery($sql);
			$db->query();
			if(JError::isError($db))
			{
				$this->setError($db->getError());
			}
		}
	}

	function enableDirectoriesFilter($filePath)
	{
		if(!is_array($this->_skipDirectories))
		{
			$this->_loadFilters();
		}
		
		$filePath = $this->sanitizeFilePath($filePath);
		
		// Only set if it's not already set
		if(!$this->isDirectoriesSetFor($filePath))
		{
			// Get active profile
			$session =& JFactory::getSession();
			$profile = $session->get('profile', null, 'joomlapack');
			
			$db =& $this->getDBO();
			$sql = "INSERT INTO ".$db->nameQuote('#__jp_exclusion').
				'('.$db->nameQuote('profile').', '.$db->nameQuote('class').', '
				.$db->nameQuote('value').') VALUES ('.
				$db->Quote($profile).', '.$db->Quote('Skipdirs').', '.$db->Quote($filePath).')';
			$db->setQuery($sql);
			$db->query();
			if(JError::isError($db))
			{
				$this->setError($db->getError());
			}
		}
	}
	
	function disableFilesFilter($filePath)
	{
		if(!is_array($this->_skipFiles))
		{
			$this->_loadFilters();
		}
		
		$filePath = $this->sanitizeFilePath($filePath);
		
		// Only unset if it's already set
		if($this->isFilesSetFor($filePath))
		{
			// Get active profile
			$session =& JFactory::getSession();
			$profile = $session->get('profile', null, 'joomlapack');
			
			$db =& $this->getDBO();
			$sql = "DELETE FROM ".$db->nameQuote('#__jp_exclusion').
				' WHERE '.$db->nameQuote('profile').' = '.$db->Quote($profile).
				' AND '.$db->nameQuote('class').' = '.$db->Quote('Skipfiles').
				' AND '.$db->nameQuote('value').' = '.$db->Quote($filePath);
			$db->setQuery($sql);
			$db->query();
			if(JError::isError($db))
			{
				$this->setError($db->getError());
			}
		}
	}

	function disableDirectoriesFilter($filePath)
	{
		if(!is_array($this->_skipDirectories))
		{
			$this->_loadFilters();
		}
		
		$filePath = $this->sanitizeFilePath($filePath);
		
		// Only unset if it's already set
		if($this->isDirectoriesSetFor($filePath))
		{
			// Get active profile
			$session =& JFactory::getSession();
			$profile = $session->get('profile', null, 'joomlapack');
			
			$db =& $this->getDBO();
			$sql = "DELETE FROM ".$db->nameQuote('#__jp_exclusion').
				' WHERE '.$db->nameQuote('profile').' = '.$db->Quote($profile).
				' AND '.$db->nameQuote('class').' = '.$db->Quote('Skipdirs').
				' AND '.$db->nameQuote('value').' = '.$db->Quote($filePath);
			$db->setQuery($sql);
			$db->query();
			if(JError::isError($db))
			{
				$this->setError($db->getError());
			}
		}
	}
	
	function toggleFilesFilter($filePath)
	{
		if($this->isFilesSetFor($filePath))
		{
			$this->disableFilesFilter($filePath);
		}
		else
		{
			$this->enableFilesFilter($filePath);
		}
	}

	function toggleDirectoriesFilter($filePath)
	{
		if($this->isDirectoriesSetFor($filePath))
		{
			$this->disableDirectoriesFilter($filePath);
		}
		else
		{
			$this->enableDirectoriesFilter($filePath);
		}
	}
	
	/**
	 * Loads all relative filters off the database
	 *
	 */
	function _loadFilters()
	{
		$this->_loadFileFilters();
		$this->_loadDirectoriesFilters();
	}
	
	/**
	 * Fetches the Skip Contained Files Filters off the database
	 *
	 */
	function _loadFileFilters()
	{
		// Get active profile
		$session =& JFactory::getSession();
		$profile = $session->get('profile', null, 'joomlapack');
		
		$db =& $this->getDBO();
		$sql = "SELECT * FROM ".$db->nameQuote('#__jp_exclusion').
			' WHERE '.$db->nameQuote('profile').' = '.$db->Quote($profile).
			' AND '.$db->nameQuote('class').' = '.$db->Quote('Skipfiles');
		$db->setQuery($sql);
		$temp = $db->loadAssocList();
		
		$this->_skipFiles = array();
		if(is_array($temp))
		{
			foreach($temp as $entry)
			{
				$this->_skipFiles[] = $entry['value'];
			}
		}
	}

	/**
	 * Fetches the Skip Contained Files Filters off the database
	 *
	 */
	function _loadDirectoriesFilters()
	{
		// Get active profile
		$session =& JFactory::getSession();
		$profile = $session->get('profile', null, 'joomlapack');
		
		$db =& $this->getDBO();
		$sql = "SELECT * FROM ".$db->nameQuote('#__jp_exclusion').
			' WHERE '.$db->nameQuote('profile').' = '.$db->Quote($profile).
			' AND '.$db->nameQuote('class').' = '.$db->Quote('Skipdirs');
		$db->setQuery($sql);
		$temp = $db->loadAssocList();
		
		$this->_skipDirectories = array();
		if(is_array($temp))
		{
			foreach($temp as $entry)
			{
				$this->_skipDirectories[] = $entry['value'];
			}
		}
	}
	
	/**
	 * Converts a potential Windows-style path to UNIX-style
	 *
	 * @param string $filePath The filepath
	 * @return string The sanitized filepath
	 */
	function sanitizeFilePath($filePath)
	{
		if(!class_exists('JoomlapackHelperUtils'))
		{
			jpimport('helpers.utils', true);
		}
		
		return JoomlapackHelperUtils::TranslateWinPath($filePath);
	}
}