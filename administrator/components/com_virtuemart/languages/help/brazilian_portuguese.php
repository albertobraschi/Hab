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
	'VM_HELP_YOURVERSION' => 'Sua versão do {product}',
	'VM_HELP_ABOUT' => '<span style="font-weight: bold;">
		VirueMart</span> é a solução completa de E-Commerce de Código Aberto para Mambo e Joomla!. 
		Ele é uma Aplicação a qual vem como um Componente, mais de 8 Módulos e Mambots/Plugins.
		Ele tem suas raízes no Script de Carrinho de Compras chamado "phpShop" (Autores: Edikon Corp. & a <a href="http://www.virtuemart.org/" target="_blank">phpShop</a> comunidade).',
	'VM_HELP_LICENSE_DESC' => 'VirtueMart é licensiado sob Licença <a href="{licenseurl}" target="_blank">{licensename}</a>.',
	'VM_HELP_TEAM' => 'Existe uma pequena equipe de Programadores que ajudam na evolução deste Script de carrinho de compras.',
	'VM_HELP_PROJECTLEADER' => 'Líder do Projeto',
	'VM_HELP_HOMEPAGE' => 'Página Inicial',
	'VM_HELP_DONATION_DESC' => 'Por favor considere uma pequena doação ao Projeto VirtueMart para nos ajudar a manter o trabalho neste componente e criar novos Recursos.',
	'VM_HELP_DONATION_BUTTON_ALT' => 'Faça pagamentos com PayPal - ele é rápido, livre e seguro!'
); $VM_LANG->initModule( 'help', $langvars );
?>