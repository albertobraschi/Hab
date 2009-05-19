<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
* VirtueMart Search Module
* NOTE: THIS MODULE REQUIRES THE PHPSHOP COMPONENT FOR MOS!
*
* @version $Id: mod_virtuemart_search.php 1159 2008-01-14 20:30:30Z soeren_nb $
* @package VirtueMart
* @subpackage modules
*
* @copyright (C) 2004-2007 soeren
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/

// Load the virtuemart main parse code
if( file_exists(dirname(__FILE__).'/../../components/com_virtuemart/virtuemart_parser.php' )) {
	require_once( dirname(__FILE__).'/../../components/com_virtuemart/virtuemart_parser.php' );
} else {
	require_once( dirname(__FILE__).'/../components/com_virtuemart/virtuemart_parser.php' );
}

global $VM_LANG, $mm_action_url, $sess;

?>
<!--BEGIN Search Box -->
<form action="<?php $sess->purl( $mm_action_url."index.php?page=shop.browse" ) ?>" method="post">

		<input name="keyword" type="text" size="12" onfocus="if(this.value=='Pesquisar...') this.value='';"  onblur="if(this.value=='') this.value='Pesquisar...';" title="<?php echo $VM_LANG->_('PHPSHOP_SEARCH_TITLE') ?>" class="inputbox" id="keyword" value="Pesquisar..."  />
		<input class="button" type="submit" name="Search" value="" />
</form>
<!-- End Search Box -->

