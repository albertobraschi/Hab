<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
*
* ================================================================================
* CORRIGIDO PARA O PORTUGUÊS DO BRASIL - CORRECTED TO BRAZILIAN PORTUGUESE
* v.1.9 - Fernando Soares - http://www.fernandosoares.com.br - 16-Fev-2009
* Para (To): VirtueMart 1.1.x
* ================================================================================
*
* @package VirtueMart
* @subpackage languages
* @copyright Copyright (C) 2004-2008 soeren - All rights reserved.
* @translator soeren
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
global $VM_LANG;
$langvars = array (
	'CHARSET' => 'ISO-8859-1',
	'PHPSHOP_COUPON_EDIT_HEADER' => 'Atualizar Cupom',
	'PHPSHOP_COUPON_CODE_HEADER' => 'Código',
	'PHPSHOP_COUPON_PERCENT_TOTAL' => 'Percentual ou Total',
	'PHPSHOP_COUPON_TYPE' => 'Tipo de Cupom',
	'PHPSHOP_COUPON_TYPE_TOOLTIP' => 'Um Cupom Presente é deletado após ser usado para descontar um pedido. Um cupom permanente pode ser usado quantas vezes o cliente quiser.',
	'PHPSHOP_COUPON_TYPE_GIFT' => 'Cupom Presente',
	'PHPSHOP_COUPON_TYPE_PERMANENT' => 'Cupom Permanente',
	'PHPSHOP_COUPON_VALUE_HEADER' => 'Valor',
	'PHPSHOP_COUPON_PERCENT' => 'Percentual',
	'PHPSHOP_COUPON_TOTAL' => 'Total'
); $VM_LANG->initModule( 'coupon', $langvars );
?>