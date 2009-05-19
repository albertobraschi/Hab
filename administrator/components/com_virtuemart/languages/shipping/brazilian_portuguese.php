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
	'PHPSHOP_CARRIER_LIST_LBL' => 'Lista de Transportadores',
	'PHPSHOP_RATE_LIST_LBL' => 'Lista de Taxas de Envio',
	'PHPSHOP_CARRIER_LIST_NAME_LBL' => 'Nome',
	'PHPSHOP_CARRIER_LIST_ORDER_LBL' => 'Ordem na lista',
	'PHPSHOP_CARRIER_FORM_LBL' => 'Editar/Criar Transportador',
	'PHPSHOP_RATE_FORM_LBL' => 'Editar/Criar uma Taxa de Envio',
	'PHPSHOP_RATE_FORM_NAME' => 'Descrição da Taxa de Envio',
	'PHPSHOP_RATE_FORM_CARRIER' => 'Transportador',
	'PHPSHOP_RATE_FORM_COUNTRY' => 'País',
	'PHPSHOP_RATE_FORM_ZIP_START' => 'Início do intervalo de CEP´s',
	'PHPSHOP_RATE_FORM_ZIP_END' => 'Final do intervalo de CEP´s',
	'PHPSHOP_RATE_FORM_WEIGHT_START' => 'Peso Mínimo',
	'PHPSHOP_RATE_FORM_WEIGHT_END' => 'Peso Máximo',
	'PHPSHOP_RATE_FORM_PACKAGE_FEE' => 'Sua taxa de pacote',
	'PHPSHOP_RATE_FORM_CURRENCY' => 'Moeda',
	'PHPSHOP_RATE_FORM_LIST_ORDER' => 'Ordem de Lista',
	'PHPSHOP_SHIPPING_RATE_LIST_CARRIER_LBL' => 'Transportador',
	'PHPSHOP_SHIPPING_RATE_LIST_RATE_NAME' => 'Descrição da Taxa de Envio',
	'PHPSHOP_SHIPPING_RATE_LIST_RATE_WSTART' => 'Peso de ...',
	'PHPSHOP_SHIPPING_RATE_LIST_RATE_WEND' => '... até',
	'PHPSHOP_CARRIER_FORM_NAME' => 'Empresa Transportadora',
	'PHPSHOP_CARRIER_FORM_LIST_ORDER' => 'Ordem da lista'
); $VM_LANG->initModule( 'shipping', $langvars );
?>