<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
*
* ================================================================================
* CORRIGIDO PARA O PORTUGUЪS DO BRASIL - CORRECTED TO BRAZILIAN PORTUGUESE
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
	'PHPSHOP_MANUFACTURER_LIST_LBL' => 'Lista de Fabricantes',
	'PHPSHOP_MANUFACTURER_LIST_MANUFACTURER_NAME' => 'Nome de Fabricante',
	'PHPSHOP_MANUFACTURER_FORM_LBL' => 'Adiocionar Informaчуo',
	'PHPSHOP_MANUFACTURER_FORM_CATEGORY' => 'Categoria de Fabricante',
	'PHPSHOP_MANUFACTURER_FORM_EMAIL' => 'E-mail',
	'PHPSHOP_MANUFACTURER_CAT_LIST_LBL' => 'Lista de Categorias de Fabricante',
	'PHPSHOP_MANUFACTURER_CAT_NAME' => 'Nome da Categoria',
	'PHPSHOP_MANUFACTURER_CAT_DESCRIPTION' => 'Descriчуo da Categoria',
	'PHPSHOP_MANUFACTURER_CAT_MANUFACTURERS' => 'Fabricantes',
	'PHPSHOP_MANUFACTURER_CAT_FORM_LBL' => 'Formulсrio da Categoria de Fabricante',
	'PHPSHOP_MANUFACTURER_CAT_FORM_INFO_LBL' => 'Informaчѕes da Categoria',
	'PHPSHOP_MANUFACTURER_CAT_FORM_NAME' => 'Nome da Categoria',
	'PHPSHOP_MANUFACTURER_CAT_FORM_DESCRIPTION' => 'Descriчуo da Categoria'
); $VM_LANG->initModule( 'manufacturer', $langvars );
?>