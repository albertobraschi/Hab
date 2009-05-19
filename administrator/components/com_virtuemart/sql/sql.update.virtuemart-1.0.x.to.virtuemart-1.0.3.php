<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
*
* @version $Id: sql.update.virtuemart-1.0.x.to.virtuemart-1.0.3.php 1336 2008-03-31 17:06:23Z soeren_nb $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2007 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_phpshop/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/

// http://virtuemart.net/index.php?option=com_flyspray&Itemid=83&do=details&id=521
$db->setQuery( 'ALTER IGNORE TABLE `#__{vm}_product_mf_xref` CHANGE `product_id` `product_id` INT( 11 ) NULL DEFAULT NULL');
$db->query();

$db->setQuery( 'ALTER IGNORE TABLE `#__{vm}_orders` ADD `order_tax_details` TEXT NOT NULL AFTER `order_tax`');
$db->query();