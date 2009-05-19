<?php
/**
* @version $Id: toolbar.contact.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Contact
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access 2' );

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );

switch ( $task ) {
	
	case 'updatecheck':
		TOOLBAR_jcrawler::_UPDATECHECK();
		break;
	
	default:
		TOOLBAR_jcrawler::_DEFAULT();
		break;
}
?>