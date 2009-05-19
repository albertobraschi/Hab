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
	'PHPSHOP_BROWSE_LBL' => 'Procurar Artigos',
	'PHPSHOP_FLYPAGE_LBL' => 'Detalhes do Produto',
	'PHPSHOP_PRODUCT_FORM_EDIT_PRODUCT' => 'Editar este produto',
	'PHPSHOP_DOWNLOADS_START' => 'Iniciar Download',
	'PHPSHOP_DOWNLOADS_INFO' => 'Por favor digite o ID de Download que você recebeu em seu e-mail e clique em "Iniciar Download".',
	'PHPSHOP_WAITING_LIST_MESSAGE' => 'Por favor intruduza seu e-mail abaixo para ser notificado quando este produto voltar ao estoque. 
Nós não compartilhamos, arrendamos, vendemos ou usamos este endereço de e-mail para qualquer outra finalidade que não seja informar-lhe quando o produto está de volta ao estoque. <br /> <br />Muito obrigado!',
	'PHPSHOP_WAITING_LIST_THANKS' => 'Obrigado por aguardar! <br />Vamos informá-lo assim que chegar em nosso estoque.',
	'PHPSHOP_WAITING_LIST_NOTIFY_ME' => 'Notifique-me!',
	'PHPSHOP_SEARCH_ALL_CATEGORIES' => 'Pesquisar em Todas as Categorias',
	'PHPSHOP_SEARCH_ALL_PRODINFO' => 'Pesquisar todas as informações do produto',
	'PHPSHOP_SEARCH_PRODNAME' => 'Apenas nome do produto',
	'PHPSHOP_SEARCH_MANU_VENDOR' => 'Apenas Fabricante/Vendedor',
	'PHPSHOP_SEARCH_DESCRIPTION' => 'Apenas descrição do produto',
	'PHPSHOP_SEARCH_AND' => 'e',
	'PHPSHOP_SEARCH_NOT' => 'não',
	'PHPSHOP_SEARCH_TEXT1' => 'A primeira lista permite selecionar uma categoria a qual limitar a pesquisa. A segunda lista permite limitar a sua pesquisa a uma determinada parte das informações do produto (ex. Nome).
Uma vez selecionadas (ou deixadas no padrão), introduza a palavra-chave a ser pesquisada.',
	'PHPSHOP_SEARCH_TEXT2' => 'Você pode refinar ainda mais a sua pesquisa, adicionando uma segunda palavra-chave e selecionando os operadores AND (e) ou NOT (não). Selecionando AND significa que ambas as palavras devem estar presentes para o produto ser exibido.
Selecionando NOT significa que o produto será exibido somente se a primeira palavra-chave estiver presente e a segunda não.',
	'PHPSHOP_CONTINUE_SHOPPING' => 'Continuar Comprando',
	'PHPSHOP_AVAILABLE_IMAGES' => 'Imagens Disponíveis para',
	'PHPSHOP_BACK_TO_DETAILS' => 'Retorna para detalhes do produto',
	'PHPSHOP_IMAGE_NOT_FOUND' => 'A image não foi encontrada!',
	'PHPSHOP_PARAMETER_SEARCH_TEXT1' => 'Você deseja procurar produtos segundo parâmetros técnicos?<BR>Você pode utilizar qualquer formulário já preparado:',
	'PHPSHOP_PARAMETER_SEARCH_NO_PRODUCT_TYPE' => 'Lamento. Não há nenhuma categoria para pesquisar.',
	'PHPSHOP_PARAMETER_SEARCH_BAD_PRODUCT_TYPE' => 'Lamento. Não existe nenhum Tipo de Produto publicado com esse nome.',
	'PHPSHOP_PARAMETER_SEARCH_IS_LIKE' => 'É Como',
	'PHPSHOP_PARAMETER_SEARCH_IS_NOT_LIKE' => 'NÃO É Como',
	'PHPSHOP_PARAMETER_SEARCH_FULLTEXT' => 'Pesquisar texto completo',
	'PHPSHOP_PARAMETER_SEARCH_FIND_IN_SET_ALL' => 'Todos os selecionados',
	'PHPSHOP_PARAMETER_SEARCH_FIND_IN_SET_ANY' => 'Qualquer Selecionado',
	'PHPSHOP_PARAMETER_SEARCH_RESET_FORM' => 'Redefinir o Formulário',
	'PHPSHOP_PRODUCT_NOT_FOUND' => 'Desculpe, mas o Produto solicitado não foi encontrado!',
	'PHPSHOP_PRODUCT_PACKAGING1' => 'Número {unit}s na embalagem:',
	'PHPSHOP_PRODUCT_PACKAGING2' => 'Número {unit}s na caixa:',
	'PHPSHOP_CART_PRICE_PER_UNIT' => 'Preço Unitário',
	'VM_PRODUCT_ENQUIRY_LBL' => 'Pergunte sobre este produto',
	'VM_RECOMMEND_FORM_LBL' => 'Recomende este produto a um amigo',
	'PHPSHOP_EMPTY_YOUR_CART' => 'Esvaziar Carrinho',
	'VM_RETURN_TO_PRODUCT' => 'Retornar ao produto',
	'EMPTY_CATEGORY' => 'Esta Categoria está atualmente vazia.',
	'ENQUIRY' => 'Indagação',
	'NAME_PROMPT' => 'Digite seu Nome',
	'EMAIL_PROMPT' => 'Endereço de E-mail',
	'MESSAGE_PROMPT' => 'Digite sua Menssagem',
	'SEND_BUTTON' => 'Enviar',
	'THANK_MESSAGE' => 'Obrigado pela sua Indagação. Nós entraremos em contato com você o mais breve possível.',
	'PROMPT_CLOSE' => 'Fechar',
	'VM_RECOVER_CART_REPLACE' => 'Substituir Carrinho pelo Carrinho Salvo',
	'VM_RECOVER_CART_MERGE' => 'Adicionar Carrinho Salvo ao Carrinho Atual',
	'VM_RECOVER_CART_DELETE' => 'Deletar Carrinho Salvo',
	'VM_EMPTY_YOUR_CART_TIP' => 'Limpar o carrinho de todos os conteúdos',
	'VM_SAVED_CART_TITLE' => 'Cesto Salvo',
	'VM_SAVED_CART_RETURN' => 'Retornar'
); $VM_LANG->initModule( 'shop', $langvars );
?>