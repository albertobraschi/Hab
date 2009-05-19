<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
* This file is called after the order has been placed by the customer
*
* @version $Id: checkout.thankyou.php 1364 2008-04-09 16:44:28Z soeren_nb $
* @package VirtueMart
* @subpackage html
* @copyright Copyright (C) 2004-2008 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
mm_showMyFileName( __FILE__ );

require_once(CLASSPATH.'ps_product.php');
$ps_product= new ps_product;
$Itemid = $sess->getShopItemid();

global $vendor_currency, $user;

// Order_id is returned by checkoutComplete function
$order_id = $db->getEscaped(vmGet($vars, 'order_id' ) );

$print = vmRequest::getInt('print', 0);

/** Retrieve User Email **/
$q  = "SELECT * FROM `#__{vm}_order_user_info` WHERE `order_id`='$order_id' AND `address_type`='BT'";
$db->query( $q );
$db->next_record();
$old_user = '';
if( !empty( $user ) && is_object($user)) {
	$old_user = $user;
}
$user = $db->record[0];
$dbbt = $db->_clone( $db );

$user->email = $db->f("user_email");

/** Retrieve Order & Payment Info **/
$db = new ps_DB;
$q  = "SELECT * FROM (`#__{vm}_order_payment` LEFT JOIN `#__{vm}_payment_method` ";
$q .= "ON `#__{vm}_payment_method`.`payment_method_id`  = `#__{vm}_order_payment`.`payment_method_id`), `#__{vm}_orders` ";
$q .= "WHERE `#__{vm}_order_payment`.`order_id`='$order_id' ";
$q .= "AND `#__{vm}_orders`.`user_id`=" . $auth["user_id"] . " ";
$q .= "AND `#__{vm}_orders`.`order_id`='$order_id' ";
$db->query($q);
	
$tpl = new $GLOBALS['VM_THEMECLASS']();
$tpl->set( 'order_id', $order_id );
$tpl->set( 'ps_product', $ps_product );
$tpl->set( 'vendor_currency', $vendor_currency );
$tpl->set( 'user', $user );
$tpl->set( 'dbbt', $dbbt );
$tpl->set( 'db', $db );
	
echo $tpl->fetch( "pages/$page.tpl.php" );


if( !empty($old_user) && is_object($old_user)) {
	$user = $old_user;
}
?>