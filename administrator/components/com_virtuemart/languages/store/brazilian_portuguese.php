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
	'PHPSHOP_USER_FORM_FIRST_NAME' => 'Nome',
	'PHPSHOP_USER_FORM_LAST_NAME' => 'Sobrenome',
	'PHPSHOP_USER_FORM_MIDDLE_NAME' => 'Nome do Meio',
	'PHPSHOP_USER_FORM_COMPANY_NAME' => 'Empresa',
	'PHPSHOP_USER_FORM_ADDRESS_1' => 'Endereço',
	'PHPSHOP_USER_FORM_ADDRESS_2' => 'Bairro',
	'PHPSHOP_USER_FORM_CITY' => 'Cidade',
	'PHPSHOP_USER_FORM_STATE' => 'Estado',
	'PHPSHOP_USER_FORM_ZIP' => 'CEP (apenas números)',
	'PHPSHOP_USER_FORM_COUNTRY' => 'País',
	'PHPSHOP_USER_FORM_PHONE' => 'Telefone',
	'PHPSHOP_USER_FORM_PHONE2' => 'Celular',
	'PHPSHOP_USER_FORM_FAX' => 'Fax',
	'PHPSHOP_ISSHIP_LIST_PUBLISH_LBL' => 'Ativo',
	'PHPSHOP_ISSHIP_FORM_UPDATE_LBL' => 'Configurar Método de Envio',
	'PHPSHOP_STORE_FORM_FULL_IMAGE' => 'Imagem',
	'PHPSHOP_STORE_FORM_UPLOAD' => 'Carregar Imagem',
	'PHPSHOP_STORE_FORM_STORE_NAME' => 'Nome da Loja',
	'PHPSHOP_STORE_FORM_COMPANY_NAME' => 'Nome da Empresa',
	'PHPSHOP_STORE_FORM_ADDRESS_1' => 'Endereço',
	'PHPSHOP_STORE_FORM_ADDRESS_2' => 'Bairro',
	'PHPSHOP_STORE_FORM_CITY' => 'Cidade',
	'PHPSHOP_STORE_FORM_STATE' => 'Estado',
	'PHPSHOP_STORE_FORM_COUNTRY' => 'País',
	'PHPSHOP_STORE_FORM_ZIP' => 'CEP (apenas números)',
	'PHPSHOP_STORE_FORM_CURRENCY' => 'Moeda',
	'PHPSHOP_STORE_FORM_LAST_NAME' => 'Nome',
	'PHPSHOP_STORE_FORM_FIRST_NAME' => 'Sobrenome',
	'PHPSHOP_STORE_FORM_MIDDLE_NAME' => 'Nome do Meio',
	'PHPSHOP_STORE_FORM_TITLE' => 'Título',
	'PHPSHOP_STORE_FORM_PHONE_1' => 'Telefone 1',
	'PHPSHOP_STORE_FORM_PHONE_2' => 'Telefone 2',
	'PHPSHOP_STORE_FORM_DESCRIPTION' => 'Descrição',
	'PHPSHOP_PAYMENT_METHOD_LIST_LBL' => 'Lista de Métodos de Pagamento',
	'PHPSHOP_PAYMENT_METHOD_LIST_NAME' => 'Nome',
	'PHPSHOP_PAYMENT_METHOD_LIST_CODE' => 'Código',
	'PHPSHOP_PAYMENT_METHOD_LIST_SHOPPER_GROUP' => 'Grupo de Clientes',
	'PHPSHOP_PAYMENT_METHOD_LIST_ENABLE_PROCESSOR' => 'Tipo de método de pagamento',
	'PHPSHOP_PAYMENT_METHOD_FORM_LBL' => 'Formulário do Método de Pagamento',
	'PHPSHOP_PAYMENT_METHOD_FORM_NAME' => 'Nome do Método de Pagamento',
	'PHPSHOP_PAYMENT_METHOD_FORM_SHOPPER_GROUP' => 'Grupo de cliente',
	'PHPSHOP_PAYMENT_METHOD_FORM_DISCOUNT' => 'Desconto',
	'PHPSHOP_PAYMENT_METHOD_FORM_CODE' => 'Código',
	'PHPSHOP_PAYMENT_METHOD_FORM_LIST_ORDER' => 'Ordem na listagem',
	'PHPSHOP_PAYMENT_METHOD_FORM_ENABLE_PROCESSOR' => 'Tipo de método de pagamento',
	'PHPSHOP_PAYMENT_FORM_CC' => 'Cartão de Crédito',
	'PHPSHOP_PAYMENT_FORM_USE_PP' => 'Usar Processamento de Pagamento',
	'PHPSHOP_PAYMENT_FORM_BANK_DEBIT' => 'Débito bancário',
	'PHPSHOP_PAYMENT_FORM_AO' => 'Apenas endereço / Dinheiro na Entrega',
	'PHPSHOP_STATISTIC_STATISTICS' => 'Estatísticas',
	'PHPSHOP_STATISTIC_CUSTOMERS' => 'Clientes',
	'PHPSHOP_STATISTIC_ACTIVE_PRODUCTS' => 'Produtos ativos',
	'PHPSHOP_STATISTIC_INACTIVE_PRODUCTS' => 'Produtos inativos',
	'PHPSHOP_STATISTIC_NEW_ORDERS' => 'Novos Pedidos',
	'PHPSHOP_STATISTIC_NEW_CUSTOMERS' => 'Novos Clientes',
	'PHPSHOP_CREDITCARD_NAME' => 'Nome do Cartão de Crédito',
	'PHPSHOP_CREDITCARD_CODE' => 'Cartão de Crédito - Código Curto',
	'PHPSHOP_YOUR_STORE' => 'Sua Loja',
	'PHPSHOP_CONTROL_PANEL' => 'Painel de Controle',
	'PHPSHOP_CHANGE_PASSKEY_FORM' => 'Mostrar/Mudar a Senha/Chave de Transação',
	'PHPSHOP_TYPE_PASSWORD' => 'Por favor digite sua Senha de Usuário',
	'PHPSHOP_CURRENT_TRANSACTION_KEY' => 'Chave de Transação Atual',
	'PHPSHOP_CHANGE_PASSKEY_SUCCESS' => 'A chave de transação foi modificada com sucesso.',
	'VM_PAYMENT_CLASS_NAME' => 'Nome da classe de pagamento',
	'VM_PAYMENT_CLASS_NAME_TIP' => '(ex. <strong>ps_netbanx</strong>) :<br />
padrão: ps_payment<br />
<i>Deixe em branco se você não tem certeza do que deve preencher!</i>',
	'VM_PAYMENT_EXTRAINFO' => 'Informações Extra do Pagamento',
	'VM_PAYMENT_EXTRAINFO_TIP' => 'São exibidas na Página de Confirmação de Pedido. Pode ser: Formulário em Código HTML do seu Provedor de Serviços de Pagamento, Sugestões ao cliente, etc.',
	'VM_PAYMENT_ACCEPTED_CREDITCARDS' => 'Tipos de Cartão de Crédito Aceitos',
	'VM_PAYMENT_METHOD_DISCOUNT_TIP' => 'Para transformar o desconto em uma taxa, use um valor negativo aqui (Exemplo: <strong>-2.00</strong>).',
	'VM_PAYMENT_METHOD_DISCOUNT_MAX_AMOUNT' => 'Quantia máxima de desconto',
	'VM_PAYMENT_METHOD_DISCOUNT_MIN_AMOUNT' => 'Quantia mínima de desconto',
	'VM_PAYMENT_FORM_FORMBASED' => 'Baseado em formulário HTML (ex. PayPal)',
	'VM_ORDER_EXPORT_MODULE_LIST_LBL' => 'Lista de Módulos de Exportação',
	'VM_ORDER_EXPORT_MODULE_LIST_NAME' => 'Nome',
	'VM_ORDER_EXPORT_MODULE_LIST_DESC' => 'Descrição',
	'VM_STORE_FORM_ACCEPTED_CURRENCIES' => 'Lista de moedas aceitas',
	'VM_STORE_FORM_ACCEPTED_CURRENCIES_TIP' => 'Esta lista define todas as moedas que você aceita quando as pessoas estão comprando qualquer coisa na sua loja. <strong>Nota:</strong> Todas as moedas selecionadas aqui podem ser usadas na finalização! Se você não deseja isso, basta selecionar a moeda de seu país (=padrão).',
	'VM_EXPORT_MODULE_FORM_LBL' => 'Formulário do Módulo de Exportação',
	'VM_EXPORT_MODULE_FORM_NAME' => 'Nome do Módulo de Exportação',
	'VM_EXPORT_MODULE_FORM_DESC' => 'Descrição',
	'VM_EXPORT_CLASS_NAME' => 'Nome da Classe de Exportação',
	'VM_EXPORT_CLASS_NAME_TIP' => '(ex. <strong>ps_orders_csv</strong>) :<br /> padrão: ps_xmlexport<br /> <i>Deixe em branco se você não tem certeza do que deve preencher!</i>',
	'VM_EXPORT_CONFIG' => 'Configuração dos Extras da Exportação',
	'VM_EXPORT_CONFIG_TIP' => 'Define a configuração de expotação para módulos de exportação definidos pelo usuário ou define configurações adicionais de configuração. O código  precisa ser um código PHP válido.',
	'VM_SHIPPING_MODULE_LIST_NAME' => 'Nome',
	'VM_SHIPPING_MODULE_LIST_E_VERSION' => 'Versão',
	'VM_SHIPPING_MODULE_LIST_HEADER_AUTHOR' => 'Autor',
	'PHPSHOP_STORE_ADDRESS_FORMAT' => 'Formato do Endereço da Loja',
	'PHPSHOP_STORE_ADDRESS_FORMAT_TIP' => 'Você pode usar os seguintes coringas aqui',
	'PHPSHOP_STORE_DATE_FORMAT' => 'Formato de Data da Loja',
	'VM_PAYMENT_METHOD_ID_NOT_PROVIDED' => 'Erro: ID do Método de Pagamento não fornecido.',
	'VM_SHIPPING_MODULE_CONFIG_LBL' => 'Configuração do Módulo de Envio',
	'VM_SHIPPING_MODULE_CLASSERROR' => 'Não foi possível instanciar a Classe {shipping_module}'
); $VM_LANG->initModule( 'store', $langvars );
?>