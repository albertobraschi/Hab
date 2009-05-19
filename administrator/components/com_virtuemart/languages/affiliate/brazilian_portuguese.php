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
	'PHPSHOP_USER_FORM_EMAIL' => 'E-mail',
	'PHPSHOP_SHOPPER_LIST_LBL' => 'Lista de Clientes',
	'PHPSHOP_SHOPPER_FORM_BILLTO_LBL' => 'Informações de Cobrança',
	'PHPSHOP_SHOPPER_FORM_USERNAME' => 'Nome de Usuário',
	'PHPSHOP_AFFILIATE_MOD' => 'Administração de Afiliados',
	'PHPSHOP_AFFILIATE_LIST_LBL' => 'Lista de Afiliados',
	'PHPSHOP_AFFILIATE_LIST_AFFILIATE_NAME' => 'Nome do Afilidado',
	'PHPSHOP_AFFILIATE_LIST_AFFILIATE_ACTIVE' => 'Ativo',
	'PHPSHOP_AFFILIATE_LIST_RATE' => 'Avaliação',
	'PHPSHOP_AFFILIATE_LIST_MONTH_TOTAL' => 'Total Mensal',
	'PHPSHOP_AFFILIATE_LIST_MONTH_COMMISSION' => 'Comissão Mensal',
	'PHPSHOP_AFFILIATE_LIST_ORDERS' => 'Listar Pedidos',
	'PHPSHOP_AFFILIATE_EMAIL_WHO' => 'E-mail para Quem (* = TODOS)',
	'PHPSHOP_AFFILIATE_EMAIL_CONTENT' => 'Seu E-mail',
	'PHPSHOP_AFFILIATE_EMAIL_SUBJECT' => 'Assunto',
	'PHPSHOP_AFFILIATE_EMAIL_STATS' => 'Incluir Estatísticas Atuais',
	'PHPSHOP_AFFILIATE_FORM_RATE' => 'Taxa de Comissão (percentual)',
	'PHPSHOP_AFFILIATE_FORM_ACTIVE' => 'Ativo?',
	'VM_AFFILIATE_SHOWINGDETAILS_FOR' => 'Exibindo Detailhes para',
	'VM_AFFILIATE_LISTORDERS' => 'Listar Pedidos',
	'VM_AFFILIATE_MONTH' => 'Mês',
	'VM_AFFILIATE_CHANGEVIEW' => 'Mudar Visualização',
	'VM_AFFILIATE_ORDERSUMMARY_LBL' => 'Sumário do Pedido',
	'VM_AFFILIATE_ORDERLIST_ORDERREF' => 'Ref do Pedido',
	'VM_AFFILIATE_ORDERLIST_DATEORDERED' => 'Data de Pedido',
	'VM_AFFILIATE_ORDERLIST_ORDERTOTAL' => 'Total do Pedido',
	'VM_AFFILIATE_ORDERLIST_COMMISSION' => 'Comissão (taxa)',
	'VM_AFFILIATE_ORDERLIST_ORDERSTATUS' => 'Situação do Pedido'
); $VM_LANG->initModule( 'affiliate', $langvars );
?>