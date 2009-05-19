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
	'PHPSHOP_RB_INDIVIDUAL' => 'Listas Individuais de Produto',
	'PHPSHOP_RB_SALE_TITLE' => 'Relatório de Vendas',
	'PHPSHOP_RB_SALES_PAGE_TITLE' => 'Visão Geral da Atividade das Vendas',
	'PHPSHOP_RB_INTERVAL_TITLE' => 'Setar intervalo',
	'PHPSHOP_RB_INTERVAL_MONTHLY_TITLE' => 'Mensal',
	'PHPSHOP_RB_INTERVAL_WEEKLY_TITLE' => 'Semanal',
	'PHPSHOP_RB_INTERVAL_DAILY_TITLE' => 'Diário',
	'PHPSHOP_RB_THISMONTH_BUTTON' => 'Este Mês',
	'PHPSHOP_RB_LASTMONTH_BUTTON' => 'Último Mês',
	'PHPSHOP_RB_LAST60_BUTTON' => 'Últimos 60 dias',
	'PHPSHOP_RB_LAST90_BUTTON' => 'Últimos 90 dias',
	'PHPSHOP_RB_START_DATE_TITLE' => 'Começar em',
	'PHPSHOP_RB_END_DATE_TITLE' => 'Terminar em',
	'PHPSHOP_RB_SHOW_SEL_RANGE' => 'Mostrar esta faixa selecionada',
	'PHPSHOP_RB_REPORT_FOR' => 'Relatório para ',
	'PHPSHOP_RB_DATE' => 'Data',
	'PHPSHOP_RB_ORDERS' => 'Pedidos',
	'PHPSHOP_RB_TOTAL_ITEMS' => 'Total de itens vendidos',
	'PHPSHOP_RB_REVENUE' => 'Rendimento',
	'PHPSHOP_RB_PRODLIST' => 'Listagem de Produtos'
); $VM_LANG->initModule( 'reportbasic', $langvars );
?>