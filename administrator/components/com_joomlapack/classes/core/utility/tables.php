<?php
/**
* @package		JoomlaPack
* @copyright	Copyright (C) 2006-2008 JoomlaPack Developers. All rights reserved.
* @version		$Id$
* @license 		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @since		1.2.1
* @version		1.3
*
* JoomlaPack is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
**/

// Ensure this file is being included by a parent file - Joomla! 1.0.x and 1.5 compatible
(defined( '_VALID_MOS' ) || defined('_JEXEC')) or die( 'Direct Access to this location is not allowed.' );

/**
 * Provides static functions to reading / writing data to & from JoomlaPack's database table
 * (the #__jp_temp table)
 */
class JoomlapackCUBETables
{
	/**
	 * Writes a variable to the database (#__jp_temp)
	 *
	 * @param string $varName The name of the variable (must be unique; if it exists it gets overwritten)
	 * @param string $value The value to store
	 * @static
	 */
	function WriteVar( $varName, $value ){
		$db =& JFactory::getDBO();

		// Hopefully fixes the 'MySQL server has gone away' errors by testing MySQL
		// connection status and reconnecting if necessary.
		//$db->connected();			
		
		// Kill exisiting variable (if any)
		JoomlapackCUBETables::DeleteVar( $varName );

		// Base64-encode the value
		$value2 = JoomlapackCUBETables::_getBase64() ? base64_encode($value) : $value;

		// Create variable
		// OMG! $db-Quote DOES NOT encode 'key' into `key`, thus causing havoc. I have
		// to use backticks manually. BUMMER! AAARGH!
		$sql = 'INSERT INTO #__jp_temp (`key`,`value`)'.
				' VALUES ('.$db->Quote( $varName ).', '.$db->Quote( $value2 ).')';
		$db->setQuery( $sql );
		if($db->query() === false)
		{
			JoomlapackLogger::WriteLog(_JP_LOG_ERROR,JText::sprintf('CUBE_TABLES_FAILEDSTORE', $varName));
			JoomlapackLogger::WriteLog(_JP_LOG_ERROR,'Error: '.$db->getErrorMsg());
			JoomlapackLogger::WriteLog(_JP_LOG_ERROR,'Value: '.$value);
			return false;
		}
		
		return true;
	}

	/**
	 * Reads a variable out of #__jp_packvars
	 *
	 * @param string $key The name of the variable to read
	 * @param bool $boolLongText True if you want to store a large string; deprecated since 1.1.2b
	 * @return string
	 * @static
	 */
	function ReadVar( $key, $boolLongText = false ) {
		$db =& JFactory::getDBO();
		
		$sql = 'SELECT `value` FROM '.$db->nameQuote('#__jp_temp').
				' WHERE `key` = '.$db->Quote($key);
		$db->setQuery( $sql );
		$db->query();
		$value2 = $db->loadResult();
		return JoomlapackCUBETables::_getBase64() ? base64_decode($value2) : $value;
	}
	
	/**
	 * Removes a variable from #__jp_packvars
	 *
	 * @param string $varName The variable to remove
	 * @static
	 */
	function DeleteVar( $varName )
	{
		$db = JFactory::getDBO();
		$sql = 'DELETE FROM '.$db->nameQuote('#__jp_temp').' WHERE '.
				'`key` = '.$db->Quote($varName);
		$db->setQuery($sql);
		$db->query();
	}
	
	/**
	 * Removes all variables matching a pattern from #__jp_packvars
	 *
	 * @param string $keyPattern The name pattern the variables to be removed must follow
	 * @static
	 */
	function DeleteMultipleVars( $keyPattern )
	{
		$db = JFactory::getDBO();
		$sql = 'DELETE FROM '.$db->nameQuote('#__jp_temp').' WHERE '.
				'`key` LIKE '.$db->Quote($keyPattern);
		$db->setQuery($sql);
		$db->query();
	}
	
	/**
	 * Counts the number of instances for a specific variable
	 *
	 * @param string $key The varaible's name
	 * @return string
	 */
	function CountVar( $key )
	{
		$db =& JFactory::getDBO();
		$sql = 'SELECT `key` FROM '.$db->nameQuote('#__jp_temp').
				' WHERE `key` = '.$db->Quote($key);
		$db->setQuery( $sql );
		$db->query();
		$numRows = $db->getNumRows();
		return $numRows;		
	}

	/**
	 * Reads and unserializes a packvar variable (combo function)
	 *
	 * @param string $varName The variable name to read
	 * @param mixed $default The default unserialized data to return if the $varName doesn't exist
	 * @return mixed The unserialized value read from database
	 */
	function UnserializeVar( $varName, $default = null )
	{
		$count = JoomlapackCUBETables::CountVar($varName);
		if( $count >=1 )
		{
			$serialized = JoomlapackCUBETables::ReadVar($varName);
			return unserialize($serialized);
		}
		else
		{
			return $default;
		}
	}
	
	/**
	 * Writes a serialized copy of the $contentVariable to the database, under the packvar
	 * variable name of $varName.
	 *
	 * @param string $varName The packvar to create
	 * @param mixed $contentVariable Any variable to serialize (e.g. object, array, other variables, etc) 
	 */
	function SerializeVar( $varName, &$contentVariable )
	{
		$serialized = serialize($contentVariable);
		JoomlapackCUBETables::WriteVar($varName, $serialized);
	}
	
	function _getBase64()
	{
		static $_hasBase64 = null;
		
		if(is_null($_hasBase64))
		{
			$_hasBase64 = function_exists('base64_encode') && function_exists('base64_decode');
		}

		return $_hasBase64;
	}
}