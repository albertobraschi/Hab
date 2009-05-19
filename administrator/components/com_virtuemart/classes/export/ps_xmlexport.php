<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
*
* @version $Id: ps_xmlexport.php 1095 2007-12-19 20:19:16Z soeren_nb $
* @package VirtueMart
* @subpackage export
* @copyright Copyright (C) 2004-2007 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/


/**
*
* The ps_xmlexport class, containing the default export processing code
* for export methods that have no own class
*
*/
class ps_xmlexport {

	var $classname = 'ps_xmlexport';

	/**
    * Show all configuration parameters for this export method
    * @returns boolean False when the export method has no configration
    */
	function show_configuration() {
		/* ... */
	}

	function has_configuration() {
		// return false if there's no configuration
		return false;
	}

	/**
	* Returns the "is_writeable" status of the configuration file
	* @param void
	* @returns boolean True when the configuration file is writeable, false when not
	*/
	function configfile_writeable() {
		return true;
	}

	/**
	* Returns the "is_readable" status of the configuration file
	* @param void
	* @returns boolean True when the configuration file is writeable, false when not
	*/
	function configfile_readable() {
		return true;
	}

	/**
	* Writes the configuration file for this payment method
	* @param array An array of objects
	* @return boolean True when writing was successful
	*/
	function write_configuration( &$d ) {
		/* ... */
		return true;
	}
	
	/**
	 * Load default configuration for module into form-fields
	 *
	 *
	 * @param array $d
	 * @return array
	 */
	function process_installation ($d) {
		return $d;
	}
	
	/**
	 * Process authentication method
	 *
	 * @return boolean Authentication success or error
	 */
	function process_authentication () {
		//no authentication required, so simply return true;
	return true;
	}

	/**
  * process export
  * @name process_export
  * @param Filterstatement to select orders
  * @param db-object
  * @return bool true/false
  */
	function process_export(&$db) {
		return true;
	}

	/**
    * output of export
    * @return bool true/false
    */
	function output_export() {
		return true;
	}
}
