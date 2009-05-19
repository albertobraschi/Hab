<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
*
* ================================================================================
* CORRIGIDO PARA O PORTUGUКS DO BRASIL - CORRECTED TO BRAZILIAN PORTUGUESE
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
	'PHPSHOP_ZONE_ASSIGN_COUNTRY_LBL' => 'Paнs',
	'PHPSHOP_ZONE_ASSIGN_ASSIGN_LBL' => 'Atribuir а Zona',
	'PHPSHOP_ASSIGN_ZONE_PG_LBL' => 'Atribuir Zonas',
	'PHPSHOP_ZONE_FORM_NAME_LBL' => 'Nome da Zona',
	'PHPSHOP_ZONE_FORM_DESC_LBL' => 'Descriзгo da Zona',
	'PHPSHOP_ZONE_FORM_COST_PER_LBL' => 'Custo por Item na Zona',
	'PHPSHOP_ZONE_FORM_COST_LIMIT_LBL' => 'Limite de Custo na Zona',
	'PHPSHOP_ZONE_LIST_LBL' => 'Lista de Zonas',
	'PHPSHOP_ZONE_LIST_NAME_LBL' => 'Nome da Zona',
	'PHPSHOP_ZONE_LIST_DESC_LBL' => 'Descriзгo da Zona',
	'PHPSHOP_ZONE_LIST_COST_PER_LBL' => 'Custo por Item na Zona',
	'PHPSHOP_ZONE_LIST_COST_LIMIT_LBL' => 'Limite de Custo na Zona',
	'VM_ZONE_ASSIGN_PERITEM' => 'Por Item',
	'VM_ZONE_ASSIGN_LIMIT' => 'Limite',
	'VM_ZONE_EDITZONE' => 'Editar esta Zona'
); $VM_LANG->initModule( 'zone', $langvars );
?>