<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct access to '.basename(__FILE__).' is not allowed.' );
/**
* mambo-phphop Main Module
* NOTE: THIS MODULE REQUIRES AN INSTALLED MAMBO-PHPSHOP COMPONENT!
*
* @version $Id: mod_virtuemart.php 1526 2008-09-15 19:21:43Z soeren_nb $
* @package VirtueMart
* @subpackage modules
* 
* @copyright (C) 2004-2008 soeren - All Rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/

/* Load the virtuemart main parse code */
if( !isset( $mosConfig_absolute_path ) ) {
	$mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path']	= JPATH_SITE;
}
global $mosConfig_absolute_path, $page;
require_once( $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php' );

require_once(CLASSPATH.'ps_product_category.php');
$ps_product_category =& new ps_product_category();

global $my, $root_label, $mosConfig_allowUserRegistration, $jscook_type, $jscookMenu_style, $jscookTree_style, $VM_LANG, $sess, $mm_action_url;

$category_id = vmRequest::getInt( 'category_id' );

$mod_dir = dirname( __FILE__ );

/* Get module parameters */
$show_login_form = $params->get( 'show_login_form', 'no' );
$show_categories = $params->get( 'show_categories', 'yes' );
$show_listall = $params->get( 'show_listall', 'yes' );
$show_adminlink = $params->get('show_adminlink', 'yes' );
$show_accountlink = $params->get('show_accountlink', 'yes' );
$useGreyBox_accountlink = $params->get('useGreyBox_accountlink', '0' );
$show_minicart = $params->get( 'show_minicart', 'yes' );
$useGreyBox_cartlink = $params->get( 'useGreyBox_cartlink', '0' );
$show_productsearch = $params->get( 'show_productsearch', 'yes' );
$show_product_parameter_search = $params->get( 'show_product_parameter_search', 'no' );
$menutype = $params->get( 'menutype', "links" );
$class_sfx = $params->get( 'class_sfx', '' );
$pretext = $params->get( 'pretext', '' );
$jscookMenu_style = $params->get( 'jscookMenu_style', 'ThemeOffice' );
$jscookTree_style = $params->get( 'jscookTree_style', 'ThemeXP' );
$jscook_type = $params->get( 'jscook_type', 'menu' );
$menu_orientation = $params->get( 'menu_orientation', 'hbr' );
$_REQUEST['root_label'] = $params->get( 'root_label', 'Shop' );

$class_mainlevel = "mainlevel".$class_sfx;
$db = new ps_DB();
// This is "Categories:" by default. Change it in the Module Parameters Form


// update the cart because something could have 
// changed while running a function
$cart = $_SESSION["cart"];
$auth = $_SESSION["auth"];

if( $show_categories == "yes" ) {
  
  
  if ( $menutype == 'links' ) {
	/* MENUTPYE LINK LIST */
    echo $ps_product_category->get_category_tree( $category_id, $class_mainlevel );

  } 
  elseif( $menutype == "transmenu" ) {
      /* TransMenu script to display a DHTML Drop-Down Menu */
      include_once( $mod_dir.'/vm_transmenu.php' );
    
  }
  elseif( $menutype == "dtree" ) {
      /* dTree script to display structured categories */
      include_once( $mod_dir.'/vm_dtree.php' );
    
  }
  elseif( $menutype == "jscook" ) {
      /* JSCook Script to display structured categories */
      include_once( $mod_dir.'/vm_JSCook.php' );
    
  }
  elseif( $menutype == "tigratree" ) {
      /* TigraTree script to display structured categories */
      include_once( $mod_dir . '/vm_tigratree.php' );
  }

}
?>

<?php
// "List all Products" Link
if ( $show_listall == 'yes' ) { ?>
    <tr> 
      <td colspan="2"><br />
          <a href="<?php $sess->purl($mm_action_url."index.php?page=shop.browse&category=") ?>">
          <?php echo $VM_LANG->_('PHPSHOP_LIST_ALL_PRODUCTS') ?>
          </a>
      </td>
    </tr>
  <?php
}

// Product Search Box
if ( $show_productsearch == 'yes' ) { ?>
  
  <!--BEGIN Search Box --> 
  <tr> 
    <td colspan="2">
	  <hr />
      <label for="shop_search_field"><?php echo $VM_LANG->_('PHPSHOP_PRODUCT_SEARCH_LBL') ?></label>
      <form action="<?php echo $mm_action_url."index.php" ?>" method="get">
        <input id="shop_search_field" title="<?php echo $VM_LANG->_('PHPSHOP_SEARCH_TITLE') ?>" class="inputbox" type="text" size="12" name="keyword" />
        <input class="button" type="submit" name="Search" value="<?php echo $VM_LANG->_('PHPSHOP_SEARCH_TITLE') ?>" />
		<input type="hidden" name="Itemid" value="<?php echo intval(@$_REQUEST['Itemid']) ?>" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="page" value="shop.browse" />
	  </form>
        <br />
        <a href="<?php echo $sess->url($mm_action_url."index.php?option=com_virtuemart&page=shop.search") ?>">
            <?php echo $VM_LANG->_('PHPSHOP_ADVANCED_SEARCH') ?>
        </a><?php /** Changed Product Type - Begin */
	if ( $show_product_parameter_search == 'yes' ) { ?>
        <br />
        <a href="<?php echo $sess->url($mm_action_url."index.php?option=com_virtuemart&page=shop.parameter_search") ?>" title="<?php echo $VM_LANG->_('PHPSHOP_PARAMETER_SEARCH') ?>">
            <?php echo $VM_LANG->_('PHPSHOP_PARAMETER_SEARCH') ?>
        </a>
<?php } /** Changed Product Type - End */ ?>
        <hr />
    </td>
  </tr>
  <!-- End Search Box --> 
<?php 
}
  
$perm = new ps_perm;
// Show the Frontend ADMINISTRATION Link
if ($perm->check("admin,storeadmin") 
      && ((!stristr($my->usertype, "admin") ^ PSHOP_ALLOW_FRONTENDADMIN_FOR_NOBACKENDERS == '' ) 
          || stristr($my->usertype, "admin")
      )
      && $show_adminlink == 'yes'
    ) { ?>
    <tr> 
      <td colspan="2">
      	<a class="<?php echo $class_mainlevel ?>" href="<?php $sess->purl(SECUREURL . "index2.php?page=store.index&pshop_mode=admin") ?>">
      	<?php echo $VM_LANG->_('PHPSHOP_ADMIN_MOD'); ?>
      	</a>
      </td>
    </tr>
  <?php 
}

// Show the Account Maintenance Link
if ($perm->is_registered_customer($auth["user_id"]) && $show_accountlink == 'yes') {
  ?> 
    <tr> 
      <td colspan="2"><a class="<?php echo $class_mainlevel ?>" href="<?php $sess->purl(SECUREURL . "index.php?page=account.index");?>">
      <?php echo $VM_LANG->_('PHPSHOP_ACCOUNT_TITLE') ?></a></td>
    </tr><?php 
}

// SHOW LOGOUT FORM IF USER'S LOGGED IN
if ( $show_login_form == "yes" ) {
	
    if ($my->id) {
		if( vmIsJoomla('1.5') ) {
			// Logout URL
			$action =  $mm_action_url . 'index.php?option=com_user&task=logout';

			// Logout return URL
			$uri = JFactory::getURI();
			$url = $uri->toString();
			$return = base64_encode( $url );
		} else {
			// Logout URL
			$action = $mm_action_url . 'index.php?option=logout';

			// Logout return URL
			$return = $mm_action_url . 'index.php';
		}
?>	  

<?php 
	}
	else
	{
		if( vmIsJoomla('1.5') ) {
			// Login URL
			$action =  $mm_action_url . 'index.php?option=com_user&amp;task=login';
			
			// Login return URL
			$uri = JFactory::getURI();
			$url = $uri->toString();
			$return = base64_encode( $url );
			
			// Lost password
			$reset = JRoute::_( 'index.php?option=com_user&amp;view=reset' );

			// User name reminder (Joomla 1.5 only)
			$remind_url = JRoute::_( 'index.php?option=com_user&amp;view=remind' );
		} else {
			// Login URL
			$action = $mm_action_url . 'index.php?option=login';

			// Login return URL
			$return = $sess->url( $mm_action_url . 'index.php?'. $_SERVER['QUERY_STRING'] );
			
			// Lost password url
			$reset = sefRelToAbs( 'index.php?option=com_registration&amp;task=lostPassword&amp;Itemid='.(int)vmGet($_REQUEST, 'Itemid', 0) );

			// Set user name reminder to nothing
			$remind_url = '';
		}
		?> 	  
<div>
<script type="text/javascript">
function validate_required(field,alerttxt)
{
with (field)
  {
  if (value==null||value=="")
    {
    alert(alerttxt);return false;
    }
  else
    {
    return true;
    }
  }
}
function validate_form(thisform)
{

  if (validate_required(username,"Insira seu nome de usuário")==false)
  {username.focus();return false;}

  if (validate_required(passwd,"Insira seu nome de usuário")==false)
  {passwd.focus();return false;}

}
</script>
	<form action="<?php echo $action ?>" onsubmit="return validate_form(this)"  method="post" name="login" id="login">
		<?php if( $params->get('pretext') ) : ?>
			<?php echo $params->get('pretext'); ?>
			<br />
		<?php endif; ?>
		<div style="clear:both"></div>
		<div class="input-form">
			<div class="line-input">
				<label for="username_vmlogin">Usuário: </label>
				<input class="inputbox" type="text" id="username_vmlogin" size="12" name="username" />
			</div>
			
			<div class="line-input">
				<label for="password_vmlogin">Senha: </label>
				<input type="password" class="inputbox" id="password_vmlogin" size="12" name="passwd" />
			</div>
					<input type="submit" value="Entrar" class="button float_right" name="Login" />
		</div>
		<div style="clear:both"></div>
		<input type="hidden" name="remember" value="yes" />
		<br />
		<ul>
			<li class="first"><a href="<?php echo $reset_url ?>">Esqueci minha senha</a></li>
			<li><a href="<?php echo JURI::base(); ?>index.php?option=com_virtuemart&amp;page=shop.registration">Primeiro cadastro</a></li>
			<li class="last"><a href="#">Não consigo acessar</a></li>
		</ul>
		<input type="hidden" value="login" name="op2" />
		<input type="hidden" value="<?php echo $return_url ?>" name="return" />

		<?php
			if( vmIsJoomla(1.5) ) {
				$validate = JUtility::getToken();
			}
			elseif( function_exists('josspoofvalue')) {
				$validate = josSpoofValue(1);
			} else {
			  	// used for spoof hardening
				$validate = vmSpoofValue(1);
			}
			?>
			<input type="hidden" name="<?php echo $validate; ?>" value="1" />
	</form>
</div>
<?php
	}
  }
  // ALTERNATIVE LOGOUT LINK (when Registratrion Type is NO_REGISTRATION or OPTIONAL_REGISTRATION (and no user account was created))
  if( empty( $my->id) && !empty( $auth['user_id'])) {
  		// This is the case when a customer is logged in on the store, but not into Joomla!/Mambo
	  	?>

	  <?php
  }
  

// Show DOWNLOAD Link
if (ENABLE_DOWNLOADS == '1') { ?>
  <tr> 
    <td colspan="2">
        <a class="<?php echo $class_mainlevel ?>" href="<?php $sess->purl(SECUREURL . "index.php?page=shop.downloads");?>">
        <?php echo $VM_LANG->_('PHPSHOP_DOWNLOADS_TITLE') ?>
        </a>
    </td>
  </tr><?php
}

// Show a link to the cart and show the mini cart
// Check to see if minicart module is published, if it is prevent the minicart displaying in the VM module
$q="SELECT published FROM #__modules WHERE module='mod_virtuemart_cart'";
$db->query( $q );

if (USE_AS_CATALOGUE != '1' && $show_minicart == 'yes'  && !$db->f("published")  ) {
	$_SESSION['vmMiniCart'] = true;
?>
    <tr>
        <td colspan="2">
        	<?php
	        $class_att = 'class="'. $class_mainlevel .'"';
	        $href = $sess->url($mm_action_url."index.php?page=shop.cart");
	        $href2 = $sess->url($mm_action_url."index2.php?page=shop.cart");
	        $text = $VM_LANG->_('PHPSHOP_CART_SHOW');
	        if( $useGreyBox_cartlink ) {
	        	echo vmCommonHTML::getGreyboxPopUpLink( $href2, $text, '', $text, $class_att, 500, 600, $href );
	        }
	        else {
	        	echo vmCommonHTML::hyperlink( $href, $text, '', $text, $class_att );
			}
			?>
		</td>
    </tr>
    <tr>
        <td colspan="2" class="vmCartModule">
        	<?php //				^ Do not change this class name!! It is used to update this cell after a cart action 
        	// This is the 'mini cart' file
        	include (PAGEPATH.'shop.basket_short.php');
        	?>
        </td>
    </tr>
        <?php 
} else {
	$_SESSION['vmMiniCart'] = false;
	 
}?>

<?php
// Just for SIMPLEBOARD compatibility !
if (@$_REQUEST['option'] != "com_virtuemart") $db = array(); 
?>