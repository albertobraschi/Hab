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
	'PHPSHOP_ORDER_PRINT_PAYMENT_LOG_LBL' => 'Registro de Pagamento',
	'PHPSHOP_ORDER_PRINT_SHIPPING_PRICE_LBL' => 'Valor do Envio',
	'PHPSHOP_ORDER_STATUS_LIST_CODE' => 'Código da Situação de Pedido',
	'PHPSHOP_ORDER_STATUS_LIST_NAME' => 'Nome da Situação de Pedido',
	'PHPSHOP_ORDER_STATUS_FORM_LBL' => 'Situação de Pedido',
	'PHPSHOP_ORDER_STATUS_FORM_CODE' => 'Código da Situação de Pedido',
	'PHPSHOP_ORDER_STATUS_FORM_NAME' => 'Nome da Situação de Pedido',
	'PHPSHOP_ORDER_STATUS_FORM_LIST_ORDER' => 'Ordem na Lista',
	'PHPSHOP_COMMENT' => 'Comentátio',
	'PHPSHOP_ORDER_LIST_NOTIFY' => 'Notificar Cliente?',
	'PHPSHOP_ORDER_LIST_NOTIFY_ERR' => 'Por favor modifique a Situação do Pedido primeiro!',
	'PHPSHOP_ORDER_HISTORY_INCLUDE_COMMENT' => 'Incluir este comentário?',
	'PHPSHOP_ORDER_HISTORY_DATE_ADDED' => 'Data de Inclusão',
	'PHPSHOP_ORDER_HISTORY_CUSTOMER_NOTIFIED' => 'Cliente Notificado?',
	'PHPSHOP_ORDER_STATUS_CHANGE' => 'Alteração na Situação do Pedido',
	'PHPSHOP_ORDER_LIST_PRINT_LABEL' => 'Rótulo Impressão',
	'PHPSHOP_ORDER_LIST_VOID_LABEL' => 'Rótulo Vencimento',
	'PHPSHOP_ORDER_LIST_TRACK' => 'Rastro',
	'VM_DOWNLOAD_STATS' => 'ESTATÍSTICAS DE DOWNLOAD',
	'VM_DOWNLOAD_NOTHING_LEFT' => 'nenhum download restante',
	'VM_DOWNLOAD_REENABLE' => 'Re-Habilitar Download',
	'VM_DOWNLOAD_REMAINING_DOWNLOADS' => 'Downloads Restantes',
	'VM_DOWNLOAD_RESEND_ID' => 'Reenviar ID de Download',
	'VM_EXPIRY' => 'Expira',
	'VM_UPDATE_STATUS' => 'Atualiza Situação',
	'VM_ORDER_LABEL_ORDERID_NOTVALID' => 'Por favor forneça um ID de Pedido numérico e válido, não "{order_id}"',
	'VM_ORDER_LABEL_NOTFOUND' => 'Registro do pedido não encontrado no rótulo da base de dados.',
	'VM_ORDER_LABEL_NEVERGENERATED' => 'Rótulo ainda não foi gerado',
	'VM_ORDER_LABEL_CLASSCANNOT' => 'Classe {ship_class} não pôde obter as imagens de rótulo, por isso estamos aqui?',
	'VM_ORDER_LABEL_SHIPPINGLABEL_LBL' => 'Etiqueta de Transporte',
	'VM_ORDER_LABEL_SIGNATURENEVER' => 'A assinatura nunca foi recuperada',
	'VM_ORDER_LABEL_TRACK_TITLE' => 'Trilha',
	'VM_ORDER_LABEL_VOID_TITLE' => 'Rótulo de Expiração',
	'VM_ORDER_LABEL_VOIDED_MSG' => 'Selo para waybill {tracking_number} foi anulado.',
	'VM_ORDER_PRINT_PO_IPADDRESS' => 'ENDEREÇO-IP',
	'VM_ORDER_STATUS_ICON_ALT' => 'Icone de Situação',
	'VM_ORDER_PAYMENT_CCV_CODE' => 'Código CVV',
	'VM_ORDER_NOTFOUND' => 'Pedido não encontrado! Pode ter sido deletado.',
	'PHPSHOP_ORDER_EDIT_ACTIONS' => 'Ações',
	'PHPSHOP_ORDER_EDIT' => 'Alterar Detalhes do Pedido',
	'PHPSHOP_ORDER_EDIT_ADD' => 'Adicionar',
	'PHPSHOP_ORDER_EDIT_ADD_PRODUCT' => 'Adicionar Produto',
	'PHPSHOP_ORDER_EDIT_EDIT_ORDER' => 'Mudar Pedido',
	'PHPSHOP_ORDER_EDIT_ERROR_QUANTITY_MUST_BE_HIGHER_THAN_0' => 'Quantidade deve ser maior que 0.',
	'PHPSHOP_ORDER_EDIT_PRODUCT_ADDED' => 'O Produto foi adicionado ao Pedido',
	'PHPSHOP_ORDER_EDIT_PRODUCT_DELETED' => 'O Produto foi removido deste Pedido',
	'PHPSHOP_ORDER_EDIT_QUANTITY_UPDATED' => 'Quantidade foi atualizada',
	'PHPSHOP_ORDER_EDIT_RETURN_PARENTS' => 'voltar ao Produto Pai',
	'PHPSHOP_ORDER_EDIT_CHOOSE_PRODUCT' => 'Selecione um Produto',
	'PHPSHOP_ORDER_CHANGE_UPD_BILL' => 'Mudar Endereço de Cobrança',
	'PHPSHOP_ORDER_CHANGE_UPD_SHIP' => 'Mudar Endereço de Envio',
	'PHPSHOP_ORDER_EDIT_SOMETHING_HAS_CHANGED' => ' foi modificado',
	'PHPSHOP_ORDER_EDIT_CHOOSE_PRODUCT_BY_SKU' => 'Selecione Ref'
); $VM_LANG->initModule( 'order', $langvars );
?>