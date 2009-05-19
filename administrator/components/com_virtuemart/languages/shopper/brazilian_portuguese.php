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
	'PHPSHOP_ADMIN_CFG_PRICES_INCLUDE_TAX' => 'Mostrar Preços incluindo impostos?',
	'PHPSHOP_ADMIN_CFG_PRICES_INCLUDE_TAX_EXPLAIN' => 'Define a maneira como os compradores vêm os preços, se incluindo impostos ou excluindo impostos.',
	'PHPSHOP_SHOPPER_FORM_ADDRESS_LABEL' => 'Apelido do Endereço',
	'PHPSHOP_SHOPPER_GROUP_LIST_LBL' => 'Lista de Grupos de Cliente',
	'PHPSHOP_SHOPPER_GROUP_LIST_NAME' => 'Nome do Grupo',
	'PHPSHOP_SHOPPER_GROUP_LIST_DESCRIPTION' => 'Descrição do Grupo',
	'PHPSHOP_SHOPPER_GROUP_FORM_LBL' => 'Formulário de Grupo de Clientes',
	'PHPSHOP_SHOPPER_GROUP_FORM_NAME' => 'Nome do Grupo',
	'PHPSHOP_SHOPPER_GROUP_FORM_DESC' => 'Descrição do Grupo',
	'PHPSHOP_SHOPPER_GROUP_FORM_DISCOUNT' => 'Desconto no Preço para o Grupo de Compradores padrão (em %)',
	'PHPSHOP_SHOPPER_GROUP_FORM_DISCOUNT_TIP' => 'Uma quantidade positiva X significa: Se o Produto não tiver nenhum Preço atribuído a ESTE Grupo de Cliente, o preço padrão é diminuido em X %. Uma quantidade negativa tem o efeito oposto'
); $VM_LANG->initModule( 'shopper', $langvars );
?>