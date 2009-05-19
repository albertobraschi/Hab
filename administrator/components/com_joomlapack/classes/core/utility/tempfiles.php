<?php
/**
* @package		JoomlaPack
* @copyright	Copyright (C) 2006-2008 JoomlaPack Developers. All rights reserved.
* @version		$Id$
* @license 		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @since		1.3
*/

// ensure this file is being included by a parent file - Joomla! 1.0.x and 1.5 compatible
defined('_JEXEC') or die('Restricted access');

/**
 * CUBE temporary files management class
 *
 */
class JoomlapackCUBETempfiles extends JObject 
{
	/**
	 * Registers a temporary file with the CUBE object, storing them in the #__jp_temp
	 * table.
	 *
	 * @param string $fileName The path of the file, relative to the temporary directory
	 * 
	 * @return string The absolute path to the temporary file, for use in file operations
	 */
	function registerTempFile( $fileName )
	{
		$tempFiles = JoomlapackCUBETables::UnserializeVar('CUBETempFiles', array());
		
		if(!in_array($fileName, $tempFiles))
		{
			$tempFiles[] = $fileName;
			JoomlapackCUBETables::SerializeVar('CUBETempFiles', $tempFiles);
		}
		
		$configuration =& JoomlapackModelRegistry::getInstance();
		return $configuration->getTemporaryDirectory().DS.$fileName;
	}
	
	function unregisterAndDeleteTempFile( $fileName, $removePrefix = false )
	{
		$configuration =& JoomlapackModelRegistry::getInstance();
		
		if($removePrefix)
		{
			$fileName = str_replace( $configuration->getTemporaryDirectory() , '', $fileName);
			if( (substr($fileName, 1, 1) == '/') || (substr($fileName, 1, 1) == '\\') )
			{
				$fileName = substr($fileName, 2, strlen($fileName) - 1 );			
			}
		}
		
		
		if( JoomlapackCUBETables::CountVar('CUBETempFiles') >= 1 )
		{
			$serialized = JoomlapackCUBETables::ReadVar('CUBETempFiles');
			$tempFiles = unserialize($serialized);
			$newTempFiles = array();
			
			if(is_array($tempFiles))
			{
				$aFile = array_shift($tempFiles);
				while( !is_null($aFile) )
				{
					if($aFile != $fileName) $newTempFiles[] = $aFile;
					$aFile = array_shift($tempFiles);
				}
			}
			
			
			if( count($newTempFiles) == 0 )
			{
				JoomlapackCUBETables::DeleteVar('CUBETempFiles');
			}
			else
			{
				$serialized = serialize($newTempFiles);
				JoomlapackCUBETables::WriteVar('CUBETempFiles', $serialized);
			}
		}
		
		$file = $configuration->getTemporaryDirectory().DS.$fileName;
		return file_exists($file) ? @unlink($file) : false;
	}
	
	
	function deleteTempFiles()
	{
		$configuration =& JoomlapackModelRegistry::getInstance();
		
		$tempFiles = JoomlapackCUBETables::UnserializeVar('CUBETempFiles', array());
		foreach($tempFiles as $fileName)
		{
			$file = $configuration->getTemporaryDirectory().DS.$fileName;
			if(file_exists($file)) @unlink($file);
		}

		JoomlapackCUBETables::DeleteVar('CUBETempFiles');		
	}
}