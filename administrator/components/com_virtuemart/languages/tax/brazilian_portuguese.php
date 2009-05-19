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
	'PHPSHOP_TAX_LIST_LBL' => 'Lista de Taxas de Imposto',
	'PHPSHOP_TAX_LIST_STATE' => 'Imposto por Estado ou Região',
	'PHPSHOP_TAX_LIST_COUNTRY' => 'Imposto do País',
	'PHPSHOP_TAX_FORM_LBL' => 'Adicionar Informações do Imposto',
	'PHPSHOP_TAX_FORM_STATE' => 'Imposto por Estado ou Região',
	'PHPSHOP_TAX_FORM_COUNTRY' => 'Imposto do País',
	'PHPSHOP_TAX_FORM_RATE' => 'Taxa de Imposto (para 16% => entre 0.16)'
); $VM_LANG->initModule( 'tax', $langvars );
?>