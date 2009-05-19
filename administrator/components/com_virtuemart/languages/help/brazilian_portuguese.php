<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
*
* ================================================================================
* CORRIGIDO PARA O PORTUGU�S DO BRASIL - CORRECTED TO BRAZILIAN PORTUGUESE
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
	'VM_HELP_YOURVERSION' => 'Sua vers�o do {product}',
	'VM_HELP_ABOUT' => '<span style="font-weight: bold;">
		VirueMart</span> � a solu��o completa de E-Commerce de C�digo Aberto para Mambo e Joomla!. 
		Ele � uma Aplica��o a qual vem como um Componente, mais de 8 M�dulos e Mambots/Plugins.
		Ele tem suas ra�zes no Script de Carrinho de Compras chamado "phpShop" (Autores: Edikon Corp. & a <a href="http://www.virtuemart.org/" target="_blank">phpShop</a> comunidade).',
	'VM_HELP_LICENSE_DESC' => 'VirtueMart � licensiado sob Licen�a <a href="{licenseurl}" target="_blank">{licensename}</a>.',
	'VM_HELP_TEAM' => 'Existe uma pequena equipe de Programadores que ajudam na evolu��o deste Script de carrinho de compras.',
	'VM_HELP_PROJECTLEADER' => 'L�der do Projeto',
	'VM_HELP_HOMEPAGE' => 'P�gina Inicial',
	'VM_HELP_DONATION_DESC' => 'Por favor considere uma pequena doa��o ao Projeto VirtueMart para nos ajudar a manter o trabalho neste componente e criar novos Recursos.',
	'VM_HELP_DONATION_BUTTON_ALT' => 'Fa�a pagamentos com PayPal - ele � r�pido, livre e seguro!'
); $VM_LANG->initModule( 'help', $langvars );
?>