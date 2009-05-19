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
	'PHPSHOP_NO_CUSTOMER' => 'Você não é um Cliente Registrado ainda. Por favor forneça suas Informações de Faturamento.',
	'PHPSHOP_THANKYOU' => 'Obrigado pelo seu pedido.',
	'PHPSHOP_EMAIL_SENDTO' => 'Um e-mail de confirmação foi enviado para',
	'PHPSHOP_CHECKOUT_NEXT' => 'Próximo',
	'PHPSHOP_CHECKOUT_CONF_BILLINFO' => 'Informações de Faturamento',
	'PHPSHOP_CHECKOUT_CONF_COMPANY' => 'Empresa',
	'PHPSHOP_CHECKOUT_CONF_NAME' => 'Nome',
	'PHPSHOP_CHECKOUT_CONF_ADDRESS' => 'Endereço',
	'PHPSHOP_CHECKOUT_CONF_EMAIL' => 'E-mail',
	'PHPSHOP_CHECKOUT_CONF_SHIPINFO' => 'Informações de Envio',
	'PHPSHOP_CHECKOUT_CONF_SHIPINFO_COMPANY' => 'Empresa',
	'PHPSHOP_CHECKOUT_CONF_SHIPINFO_NAME' => 'Nome',
	'PHPSHOP_CHECKOUT_CONF_SHIPINFO_ADDRESS' => 'Endereço',
	'PHPSHOP_CHECKOUT_CONF_SHIPINFO_PHONE' => 'Telefone',
	'PHPSHOP_CHECKOUT_CONF_SHIPINFO_FAX' => 'Fax',
	'PHPSHOP_CHECKOUT_CONF_PAYINFO_METHOD' => 'Método de pagamento',
	'PHPSHOP_CHECKOUT_CONF_PAYINFO_REQINFO' => 'Informação requerida quando Pagamento via cartão de crédito é selecionada',
	'PHPSHOP_PAYPAL_THANKYOU' => 'Obrigado pelo seu pagamento. A transação foi um sucesso. Você receberá um e-mail de confirmação da transação pela PayPal. Você pode agora continuar ou entrar em <a href=http://www.paypal.com>www.paypal.com</a> para ver os detalhes da transação.',
	'PHPSHOP_PAYPAL_ERROR' => 'Um erro ocorreu enquanto processava sua transação. A situação de seu pedido não pode ser atualizada.',
	'PHPSHOP_THANKYOU_SUCCESS' => 'Seu pedido foi encaminhado com sucesso!',
	'VM_CHECKOUT_TITLE_TAG' => 'Finalização: Passo %s de %s',
	'VM_CHECKOUT_ORDERIDNOTSET' => 'ID do Pedido não setado ou vazio!',
	'VM_CHECKOUT_FAILURE' => 'Falha',
	'VM_CHECKOUT_SUCCESS' => 'Sucesso',
	'VM_CHECKOUT_PAGE_GATEWAY_EXPLAIN_1' => 'Esta página está localizada na loja virtual do site.',
	'VM_CHECKOUT_PAGE_GATEWAY_EXPLAIN_2' => 'O gateway executa a página no website, e este mostra o resultado criptografado com SSL.',
	'VM_CHECKOUT_CCV_CODE' => 'Código de Validação do Cartão de Crédito',
	'VM_CHECKOUT_CCV_CODE_TIPTITLE' => 'Qual é o Código de Validação do Cartão de Crédito?',
	'VM_CHECKOUT_MD5_FAILED' => 'Verificação MD5 falhou',
	'VM_CHECKOUT_ORDERNOTFOUND' => 'Pedido não encontrado',
	'PHPSHOP_EPAY_PAYMENT_CARDTYPE' => 'O pagamento foi criado por %s
<img src="/components/com_virtuemart/shop_image/ps_image/epay_images/%s" border="0">'
); $VM_LANG->initModule( 'checkout', $langvars );
?>