<?php
/**
* @package		JoomlaPack
* @copyright	Copyright (C) 2006-2008 JoomlaPack Developers. All rights reserved.
* @version		$Id$
* @license 	http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @since		1.2.1
*
* JoomlaPack is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
**/
defined('_JEXEC') or die('Restricted access');

$config =& JoomlapackModelRegistry::getInstance();
define('JPMaxFragmentSize', $config->get('mnMaxFragmentSize'));		// Maximum bytes a fragment can have (default: 1Mb)
define('JPMaxFragmentFiles', $config->get('mnMaxFragmentFiles'));	// Maximum number of files a fragment can have (default: 50 files)

/**
 * Packing engine. Takes care of putting gathered files (the file list) into
 * an archive.
 */
class JoomlapackCUBEDomainPack extends JoomlapackCUBEParts {
	/**
     * @var array Directories to exclude
     */
	var $_ExcludeDirs;
	
	/**
	 * @var array Files to exclude
	 */
	var $_ExcludeFiles;

	/**
	 * Directories to exclude their files from the backup
	 *
	 * @var array
	 */
	var $_skipContainedFiles;
	
	/**
	 * Directories to exclude their subdirectories from the backup
	 * 
	 * @var array
	 */
	var $_skipContainedDirectories;
	
	/**
	 * @var array Directories left to be scanned
	 */
	var $_directoryList;
	
	/**
	 * @var array Files left to be put into the archive
	 */
	var $_fileList;
	
	/**
	 * Operation toggle. When it is true, files are added in the archive. When it is off, the
	 * directories are scanned for files.
	 *
	 * @var bool
	 */
	var $_doneScanning = false;
	
	/**
	 * Path to add to scanned files
	 *
	 * @var string
	 */
	var $_addPath;
	
	/**
	 * Path to remove from scanned files
	 *
	 * @var string
	 */
	var $_removePath;
	
	/**
	 * An array of EFF-defined directories
	 *
	 * @var array
	 */
	var $_extraDirs = array();
	
	// ============================================================================================
	// IMPLEMENTATION OF JoomlapackEngineParts METHODS
	// ============================================================================================
	/**
	 * Public constructor of the class
	 *
	 * @return JoomlapackCUBEDomainPack
	 */
	function JoomlapackCUBEDomainPack(){
		$this->_DomainName = "Packing";
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackCUBEDomainPack :: new instance");
	}
	
	/**
	 * Implements the _prepare() abstract method
	 *
	 */
	function _prepare()
	{
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackCUBEDomainPack :: Starting _prepare()");
		
		// Grab the EFF filters
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackCUBEDomainPack :: Getting off-site directory inclusion filters (EFF)");
		jpimport('models.eff', true);
		$effModel = new JoomlapackModelEff();
		$this->_extraDirs =& $effModel->getMapping();
		
		// Add the mapping text file if there are EFFs defined!
		if(count($this->_extraDirs) > 0)
		{
			// We add a README.txt file in our virtual directory...
			JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "Creating README.txt in the EFF virtual folder");
			$virtualContents = JText::_('EFF_MAPTEXT_INTRO')."\n\n";
			foreach($this->_extraDirs as $dir)
			{
				$virtualContents .= JText::sprintf('EFF_MAPTEXT_LINE', $dir['vdir'], $dir['fsdir'])."\n";
			}
			// Add the file to our archive
			$registry =& JoomlapackModelRegistry::getInstance();
			$cube =& JoomlapackCUBE::getInstance();
			$provisioning =& $cube->getProvisioning();
			$archiver =& $provisioning->getArchiverEngine();
			$archiver->addVirtualFile('README.txt', $registry->get('effvfolder'), $virtualContents);
		}
		
		
		// Get the directory exclusion filters - this only needs to be done once
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackCUBEDomainPack :: Getting exclusion filters");
		$this->_loadAndCacheFilters();
		if($this->getError()) return false;
		
		// FIX 1.1.0 $mosConfig_absolute_path may contain trailing slashes or backslashes incompatible with exclusion filters
		// FIX 1.2.2 Some hosts yield an empty string on realpath(JPATH_SITE) 
		if( (trim(realpath(JPATH_SITE)) == '') || (trim(JPATH_SITE) == '') )
		{
			JoomlapackLogger::WriteLog(_JP_LOG_WARNING, "The normalized path to your site's root seems to be an empty string; I will attempt a workaround");
			# Fix 2.1 Since JPATH_SITE is an empty string, shouldn't I begin scanning from the FS root, for crying out loud? What was I thinking putting JPATH_SITE there?
			$this->_directoryList[] = '/'; // Start scanning from filesystem root (workaround mode)
		}
		else
		{
			$this->_directoryList[] = realpath(JPATH_SITE); // Start scanning from Joomla! root (normal mode)
		}
		$this->_doneScanning = false; // Instruct the class to scan for files
		$this->_addPath = ''; // No added path for main site
		$this->_removePath = JPATH_SITE; // Remove absolute path to site's root for main site 
		
		$this->setState('prepared');

		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackCUBEDomainPack :: prepared");
	}
	
	function _run()
	{
		if ($this->_getState() == 'postrun') {
			JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackCUBEDomainPack :: Already finished");
			$this->_Step = "-";
			$this->_Substep = "";
		}
		else
		{
			if($this->_doneScanning)
			{
				$this->_packSomeFiles();
				if($this->getError()) return false;
			}
			else
			{
				$result = $this->_scanNextDirectory();
				if($this->getError()) return false;
				if(!$result)
				{
					// We have finished with our directory list. Hmm... Do we have extra directories?
					if(count($this->_extraDirs) > 0)
					{
						JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "More EFF definitions detected");
						$registry =& JoomlapackModelRegistry::getInstance();
						// Whack filters (not applicable for off-site directories)
						$this->_ExcludeDirs = array();
						$this->_ExcludeFiles = array();
						$this->_skipContainedDirectories = array();
						$this->_skipContainedFiles = array();
						// Calculate add/remove paths
						$myEntry = array_shift($this->_extraDirs);
						$this->_removePath = $myEntry['fsdir'];
						$this->_addPath = $registry->get('effvfolder').DS.$myEntry['vdir'];
						// Start the filelist building!
						$this->_directoryList[] = $this->_removePath;
						$this->_doneScanning = false; // Make sure we process this file list!
						JoomlapackLogger::WriteLog(_JP_LOG_INFO, "Including new off-site directory to ".$myEntry['vdir']);
					}
					else
						// Nope, we are completely done!
						$this->setState('postrun');
				}
			}
		}
	}
	
	/**
	 * Implements the _finalize() abstract method
	 *
	 */
	function _finalize()
	{
		JoomlapackLogger::WriteLog(_JP_LOG_INFO, "Finalizing archive");
		$cube =& JoomlapackCUBE::getInstance();
		$provisioning =& $cube->getProvisioning();
		$archive =& $provisioning->getArchiverEngine();
		$archive->finalize();
		// Error propagation
		if($archive->getError())
		{
			$this->setError($archive->getError());
			return false;
		}
		// Warning propagation
		// @todo Warning propagation

		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "Archive is finalized");
		
		$this->setState('finished');
	}
	
	// ============================================================================================
	// PRIVATE METHODS
	// ============================================================================================
	
	/**
	* Loads the exclusion filters off the db and caches them inside the object
	*/
	function _loadAndCacheFilters() {
		jpimport('core.utility.filtermanager');

		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackCUBEDomainPack :: Initializing filter manager");

		$filterManager = new JoomlapackCUBEFilterManager();
		$filterManager->init();
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackCUBEDomainPack :: Getting Directory Exclusion Filters");
		$this->_ExcludeDirs = $filterManager->getFilters('folder');
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackCUBEDomainPack :: Getting Single File Filters");
		$this->_ExcludeFiles = $filterManager->getFilters('singlefile');
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackCUBEDomainPack :: Getting Contained Files Filters");
		$this->_skipContainedFiles = $filterManager->getFilters('containedfiles');
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackCUBEDomainPack :: Getting Contained Directories Filters");
		$this->_skipContainedDirectories = $filterManager->getFilters('containeddirectories');
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "JoomlapackCUBEDomainPack :: Done with filter manager");
		unset($filterManager);
	}	
	
	/**
	 * Scans a directory for files and directories, updating the _directoryList and _fileList
	 * private fields
	 *
	 * @return bool True if more work has to be done, false if the dirextory stack is empty
	 */
	function _scanNextDirectory( )
	{
		// Are we supposed to scan for more files?
		if( $this->_doneScanning ) return true;
		// Get the next directory to scan
		if( count($this->_directoryList) == 0 )
		{
			// No directories left to scan
			return false; 
		}
		else
		{
			// Get and remove the last entry from the $_directoryList array
			$dirName = array_pop($this->_directoryList);
			$this->_Step = $dirName;
		}
		
		$cube =& JoomlapackCUBE::getInstance();
		$provisioning =& $cube->getProvisioning();
		$engine =& $provisioning->getListerEngine();
		
		// Apply DEF (directory exclusion filters)
		if (in_array( $dirName, $this->_ExcludeDirs )) {
			JoomlapackLogger::WriteLog(_JP_LOG_INFO, "Skipping directory $dirName");
			return true;
		}
		
		// Apply Skip Contained Directories Filters
		if (in_array( $dirName, $this->_skipContainedDirectories )) {
			JoomlapackLogger::WriteLog(_JP_LOG_INFO, "Skipping subdirectories of directory $dirName");
		}
		else
		{
			JoomlapackLogger::WriteLog(_JP_LOG_INFO, "Scanning directories of $dirName");
			// Get subdirectories
			$subdirs = $engine->getFolders($dirName);
			// Error propagation
			if($engine->getError())
			{
				$this->setError($engine->getError());
				return false;
			}
			
			if(!empty($subdirs) && is_array($subdirs))
			{
				foreach($subdirs as $subdir)
				{
					$this->_directoryList[] = $subdir;
				}
			}
			// Warning propagation
			// @todo Warning propagation
		}
		
		
		// Apply Skipfiles, a.k.a. CFF (Contained Files Filter)
		if (in_array( $dirName, $this->_skipContainedFiles )) {
			JoomlapackLogger::WriteLog(_JP_LOG_INFO, "Skipping files of directory $dirName");
			// Try to find and include .htaccess and index.htm(l) files
			jimport('joomla.filesystem.file');
			$checkForTheseFiles = array(
				$dirName.DS.'.htaccess',
				$dirName.DS.'index.html',
				$dirName.DS.'index.htm',
				$dirName.DS.'robots.txt'
			);
			$processedFiles = 0;
			foreach($checkForTheseFiles as $fileName)
			{
				if(JFile::exists($fileName))
				{
					$this->_fileList[] = $fileName;
					$processedFiles++;
				}	
			}
		}
		else
		{
			JoomlapackLogger::WriteLog(_JP_LOG_INFO, "Scanning files of $dirName");
			// Get file listing
			$fileList =& $engine->getFiles( $dirName );
			
			// Error propagation
			if($engine->getError())
			{
				$this->setError($engine->getError());
				return false;
			}
					
			// Warning propagation
			// @todo Warning propagation		
			$processedFiles = 0;
			
			if (($fileList === false)) {
				// A non-browsable directory; however, it seems that I never get FALSE reported here?!
				JoomlapackLogger::WriteLog(_JP_LOG_WARNING, JText::sprintf('CUBE_WARN_UNREADABLEDIR', $dirName));
			}
			else
			{
				if(is_array($fileList) && !empty($fileList))
				{
					// Scan all directory entries
					foreach($fileList as $fileName) {
						$skipThisFile = is_array($this->_ExcludeFiles) ? in_array( $fileName, $this->_ExcludeFiles ) : false;
						if ($skipThisFile) {
							JoomlapackLogger::WriteLog(_JP_LOG_INFO, "Skipping file $fileName");
						} else {
							$this->_fileList[] = $fileName;
							$processedFiles++;
						}
					} // end foreach
				} // end if
			} // end filelist not false
		}
		
		// Check to see if there were no contents of this directory added to our search list
		if ( $processedFiles == 0 ) {
			$archiver =& $provisioning->getArchiverEngine();
			$archiver->addFile($dirName, $this->_removePath, $this->_addPath);
			
			if($archiver->getError())
			{
				$this->setError($archiver->getError());
				return false;
			}
			
			// Warning propagation
			// @todo Warning propagation
			
			JoomlapackLogger::WriteLog(_JP_LOG_INFO, "Empty directory $dirName");
			unset($archiver);
			
			$this->_doneScanning = false; // Because it was an empty dir $_fileList is empty and we have to scan for more files
		}
		else
		{
			// Next up, add the files to the archive!
			$this->_doneScanning = true;
		}
		
		// We're done listing the contents of this directory
		unset($engine);
		unset($provisioning);
		unset($cube);
		
		return true;
	}
	
	/**
	 * Try to pack some files in the $_fileList, restraining ourselves not to reach the max
	 * number of files or max fragment size while doing so. If this process is over and we are
	 * left without any more files, reset $_doneScanning to false in order to instruct the class
	 * to scan for more files.
	 *
	 * @return bool True if there were files packed, false otherwise (empty filelist)
	 */
	function _packSomeFiles()
	{
		if( count($this->_fileList) == 0 )
		{
			// No files left to pack -- This should never happen! We catch this condition at the end of this method!
			$this->_doneScanning = false;
			return false;
		}
		else
		{
			$packedSize = 0;
			$numberOfFiles = 0;

			$cube =& JoomlapackCUBE::getInstance();
			$provisioning =& $cube->getProvisioning();
			$archiver =& $provisioning->getArchiverEngine();
			
			while( (count($this->_fileList) > 0) && ($packedSize <= JPMaxFragmentSize) && ($numberOfFiles <= JPMaxFragmentFiles) )
			{
				$file = @array_shift($this->_fileList);
				$size = @filesize($file);
				$packedSize += $size;
				$numberOfFiles++;
				$archiver->addFile($file, $this->_removePath, $this->_addPath);
				// Error propagation
				if($archiver->getError())
				{
					$this->setError($archiver->getError());
					return false;
				}
				// Warning propagation
				// @todo Warning propagation
			}
			
			$this->_doneScanning = count($this->_fileList) > 0;
			return true;
		}
	}

}