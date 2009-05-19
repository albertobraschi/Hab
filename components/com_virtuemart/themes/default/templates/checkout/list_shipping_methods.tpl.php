<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
*
* @version $Id: list_shipping_methods.tpl.php 1095 2007-12-19 20:19:16Z soeren_nb $
* @package VirtueMart
* @subpackage templates
* @copyright Copyright (C) 2007 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/

foreach( $PSHOP_SHIPPING_MODULES as $shipping_module ) {
    $vmLogger->debug( 'Starting Shipping module: '.$shipping_module );
    include_once( CLASSPATH. "shipping/".$shipping_module.".php" );
    eval( "\$SHIPPING =& new ".$shipping_module."();");
    $SHIPPING->list_rates( $vars );
    echo "<br /><br />";
}

?>