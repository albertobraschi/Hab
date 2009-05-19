<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
*
* @version $Id: ps_olsi.php 1095 2007-12-19 20:19:16Z soeren_nb $
* @package VirtueMart
* @subpackage export
* @copyright Copyright (C) 2004-2007 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/


/**
*
* The ps_xmlexport class, containing the default export processing code
* for export methods that have no own class
*
*/
require_once( $mosConfig_absolute_path. '/includes/domit/xml_domit_include.php' );
require_once( $mosConfig_absolute_path. '/includes/domit/xml_domit_utilities.php');

class ps_olsi {

	var $classname = 'ps_olsi';
	var $xmldoc = null;

	/**
    * Show all configuration parameters for this export method
    * @returns boolean False when the export method has no configration
    */
	function show_configuration() {
		global $VM_LANG;
		$db = new ps_DB();

		/** Read current Configuration ***/
		include_once(CLASSPATH ."export/".$this->classname.".cfg.php");
    ?>
    <table class="adminform">
        <!--tr class="row0">
        <td><strong><?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_ENABLE_AUTORIZENET_TESTMODE') ?></strong></td>
            <td>
                <select name="PAYPAL_DEBUG" class="inputbox" >
                <option <?php if (@PAYPAL_DEBUG == '1') echo "selected=\"selected\""; ?> value="1"><?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_YES') ?></option>
                <option <?php if (@PAYPAL_DEBUG != '1') echo "selected=\"selected\""; ?> value="0"><?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_NO') ?></option>
                </select>
            </td>
            <td>
            <?php
    printf( $VM_LANG->_('VM_ADMIN_CFG_PAYPAL_NOTIFYSCRIPT_TIP'), '<pre>'. COMPONENTURL."notify.php</pre>" );
			?>            
            </td>
        </tr-->
        <tr class="row1">
        <td><strong>Shipping module mapping</strong></td>
            <td>
                <input type="text" name="SHIPPINGMODULEMAP" class="inputbox" value="<?  echo SHIPPINGMODULEMAP ?>" />
            </td>
            <td>
            </td>
        </tr>
        <tr class="row0">
            <td><strong><?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_PAYPAL_STATUS_SUCCESS') ?></strong></td>
            <td>
                <select name="PAYPAL_VERIFIED_STATUS" class="inputbox" >
                <?php
                $q = "SELECT order_status_name,order_status_code FROM #__{vm}_order_status ORDER BY list_order";
                $db->query($q);
                $order_status_code = Array();
                $order_status_name = Array();

                while ($db->next_record()) {
                	$order_status_code[] = $db->f("order_status_code");
                	$order_status_name[] =  $db->f("order_status_name");
                }
                for ($i = 0; $i < sizeof($order_status_code); $i++) {
                	echo "<option value=\"" . $order_status_code[$i];
                	if (PAYPAL_VERIFIED_STATUS == $order_status_code[$i])
                	echo "\" selected=\"selected\">";
                	else
                	echo "\">";
                	echo $order_status_name[$i] . "</option>\n";
                    }?>
                    </select>
            </td>
            <td><?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_PAYPAL_STATUS_SUCCESS_EXPLAIN') ?>
            </td>
        </tr>
        <tr class="row1">
            <td><strong><?php echo $VM_LANG->_('VM_ADMIN_CFG_PAYPAL_STATUS_PENDING') ?></strong></td>
            <td>
                <select name="PAYPAL_PENDING_STATUS" class="inputbox" >
                <?php
                for ($i = 0; $i < sizeof($order_status_code); $i++) {
                	echo "<option value=\"" . $order_status_code[$i];
                	if (PAYPAL_PENDING_STATUS == $order_status_code[$i])
                	echo "\" selected=\"selected\">";
                	else
                	echo "\">";
                	echo $order_status_name[$i] . "</option>\n";
                    } ?>
                    </select>
            </td>
            <td><?php echo $VM_LANG->_('VM_ADMIN_CFG_PAYPAL_STATUS_PENDING_EXPLAIN') ?></td>
        </tr>
        <tr class="row0">
            <td><strong><?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_PAYPAL_STATUS_FAILED') ?></strong></td>
            <td>
                <select name="PAYPAL_INVALID_STATUS" class="inputbox" >
                <?php
                for ($i = 0; $i < sizeof($order_status_code); $i++) {
                	echo "<option value=\"" . $order_status_code[$i];
                	if (PAYPAL_INVALID_STATUS == $order_status_code[$i])
                	echo "\" selected=\"selected\">";
                	else
                	echo "\">";
                	echo $order_status_name[$i] . "</option>\n";
                    } ?>
                    </select>
            </td>
            <td><?php echo $VM_LANG->_('PHPSHOP_ADMIN_CFG_PAYPAL_STATUS_FAILED_EXPLAIN') ?>
            </td>
        </tr>
      </table>
    <?php
	}

	function has_configuration() {
		// return false if there's no configuration
		return true;
	}

	/**
	* Returns the "is_writeable" status of the configuration file
	* @param void
	* @returns boolean True when the configuration file is writeable, false when not
	*/
	function configfile_writeable() {
		return is_writeable( CLASSPATH."export/".$this->classname.".cfg.php" );
	}

	/**
	* Returns the "is_readable" status of the configuration file
	* @param void
	* @returns boolean True when the configuration file is writeable, false when not
	*/
	function configfile_readable() {
		return is_readable( CLASSPATH."export/".$this->classname.".cfg.php" );
	}

	/**
	* Writes the configuration file for this payment method
	* @param array An array of objects
	* @return boolean True when writing was successful
	*/
	function write_configuration( &$d ) {
		/* ... */
		return true;
	}
	
	/**
	 * Load default configuration for module into form-fields
	 *
	 *
	 * @param array $d
	 * @return array
	 */
	function process_installation ($d = array()) {
		echo 'yes!';
		if (!$d['export_name']) {
			$d['export_name'] = "SAGE Office Line Connector";
		}
		
		if (!$d['export_desc']) {
			$d['export_desc'] = 'Export Module for Sage Office Line, requires Office Line Shop Interface from <a href="http://www.1st-vision.de">1st Vision</a>.';
		}		
		return $d;
	}
	
	/**
	 * Process authentication method
	 *
	 * @return boolean Authentication success or error
	 */
	function process_authentication () {
		global $database, $acl;
		$usrname 	= $database->getEscaped( vmGet( $_REQUEST, 'user', '' ) );
		$pass 		= $database->getEscaped( vmGet( $_REQUEST, 'password', '' ) );

		if (!$usrname) {
			echo 'Please enter a username';
			return false;
		}

		if (!$pass) {
			echo 'Please enter a password';
			return false;
		} else {
			$pass = md5( $pass );
		}


		$query = "SELECT COUNT(*)"
		. "\n FROM #__users"
		. "\n WHERE ("
		// Administrators
		. "\n gid = 24"
		// Super Administrators
		. "\n OR gid = 25"
		. "\n )"
		;
		$database->setQuery( $query );
		$count = intval( $database->loadResult() );
		if ($count < 1) {
			echo _LOGIN_NOADMINS;
			return false;
		}

		$my = null;
		$query = "SELECT *"
		. "\n FROM #__users"
		. "\n WHERE username = '$usrname'"
		. "\n AND block = 0"
		;
		$database->setQuery( $query );
		$database->loadObject( $my );

		/** find the user group (or groups in the future) */
		if (@$my->id) {
			$grp 			= $acl->getAroGroup( $my->id );
			$my->gid 		= $grp->group_id;
			$my->usertype 	= $grp->name;

			if ( strcmp( $my->password, $pass ) || !$acl->acl_check( 'administration', 'login', 'users', $my->usertype ) ) {
				echo "Incorrect Username, Password, or Access Level.";
				return false;
			}
		} else {
			echo "Incorrect Username, Password.  Please try again";
			return false;
		}
		return true;
	}

	/**
  * process export
  * @name process_export
  * @param Filterstatement to select orders
  * @param db-object
  * @return bool true/false
  */
	function process_export(&$db) {
		global $mosConfig_absolute_path, $database;
		$xmldoc = new DOMIT_Document();
		$xmldoc->appendEntityTranslationTable(get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES)); //damit sollte htmlspecialchars nicht mehr notwendig sein
		$xmldoc->expandEmptyElementTags(false);
		$xmldoc->appendChild($xmldoc->createProcessingInstruction("xml", "version=\"1.0\" encoding=\"IS0-8859-1\""));
		$xmldoc->setDocumentElement($xmldoc->createElement('OLOrders'));
		$xmldoc->documentElement->appendChild($xmldoc->createElement('Version1.1'));


		
		$order_status = vmGet( $_REQUEST, 'status', '' );
		$order_from = vmGet( $_REQUEST, 'from', '' );
		$order_since = vmGet( $_REQUEST, 'since', '' );
		$order_since = vmGet( $_REQUEST, 'since', '' );
		$order_to = vmGet( $_REQUEST, 'to', '' );
		$order_id = vmGet( $_REQUEST, 'order_id', '' );

		$where = array();
		if (!$order_status && !$order_from && !$order_since && !$order_to && !$order_id){
			$order_status = 'P';
		}
		if ($order_status) {
			$where[] = "order_status = '" . $db->getEscaped( $order_status ) . "'";
		}
		if($order_from) {
			$where[] = "order_id >= '" . $db->getEscaped($order_from) . "'";
		} elseif ($order_since) {
			$where[] = "order_id > '" . $db->getEscaped($order_since) . "'";
		} elseif ($order_id) {
			$where[] = "order_id = '" . $db->getEscaped($order_id) . "'";
		}

		if($order_to && !$order_id) {
			$where[] = "order_id <= '" . $db->getEscaped($order_to) . "'";
		}
		
		//select the orders to export
		$q = 'SELECT * FROM #__{vm}_orders WHERE vendor_id=\'' . $db->f('vendor_id') . '\' AND ';
		$q .= implode($where);
		$db->setQuery($q);
		$orders = $db->loadAssocList();

		for($i=0; $i < count($orders); $i++ ) {
			//get billing and shipping address
			$q = "SELECT * FROM #__{vm}_order_user_info WHERE order_id='". $orders[$i]['order_id'] ."'";
			$db->setQuery($q);
			$orders[$i]['user_info'] = $db->loadAssocList();
			//get shipping address
			$q = "SELECT * FROM #__{vm}_order_item WHERE order_id='". $orders[$i]['order_id'] ."'";
			$db->setQuery($q);
			$orders[$i]['item'] = $db->loadAssocList();
			//get payment info
			$q = "SELECT * FROM #__{vm}_order_payment WHERE order_id='". $orders[$i]['order_id'] ."'";
			$db->setQuery($q);
			$orders[$i]['payment'] = $db->loadAssocList();
		}


		foreach($orders as $order) {

			//print_r($order);
			$OLWebOrder['OLWebOrder']['TransactionHeader']['TransactionID'] = $order['order_id'];
			$OLWebOrder['OLWebOrder']['OrderHeader']['ProcessingOptions']['OrderDate'] = date('d.m.Y H:i:s', $order['cdate']);
			$OLWebOrder['OLWebOrder']['OrderHeader']['HeaderText'] = $order['customer_note'];
			$OLWebOrder['OLWebOrder']['OrderHeader']['ProcessingOptions']['RefNo'] = str_pad($order['order_id'], 8, 0,STR_PAD_LEFT);
			for($i = 0; $i < count($order['user_info']) ; $i++) {
				switch ($order['user_info'][$i]['address_type']) {
					case 'BT':
					default:

						if($order['company']) {
							$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['Name1'] = $order['user_info'][$i]['company'];
							$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['Name2'] = $order['user_info'][$i]['first_name'] . (($order['user_info'][$i]['middle_name']) ? ' ' . $order['user_info'][$i]['middle_name'] . ' ' : ' ') . $order['user_info'][$i]['last_name'];
							$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['Matchcode'] = $order['user_info'][$i]['company'] . ', ' .$order['user_info'][$i]['customers_city'] ;
						} else {
							$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['Name1'] = $order['user_info'][$i]['last_name'];
							$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['Name2'] = $order['user_info'][$i]['first_name'] . (($order['user_info'][$i]['middle_name']) ? ' ' . $order['user_info'][$i]['middle_name'] : '');
							$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['Matchcode'] = $order['user_info'][$i]['last_name'] . ', ' .$order['user_info'][$i]['city'] ;
						}
						$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['Title'] = 	$order['user_info'][$i]['title'];
						$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['Street'] = $order['user_info'][$i]['address_1'] . (($order['user_info'][$i]['address_2']) ? ', ' . $order['user_info'][$i]['address_2'] : '');
						$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['ZIPCode'] = $order['user_info'][$i]['zip'];
						$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['City'] = $order['user_info'][$i]['city'];
						$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['State'] = $order['user_info'][$i]['state'];
						$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['Country'] = $this->getISOCode2($order['user_info'][$i]['country']);
						$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['EMail'] = $order['user_info'][$i]['user_email'];

						$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['AddrTelefon'] = $order['user_info'][$i]['phone_1'];
						$OLWebOrder['OLWebOrder']['OrderHeader']['Address']['AddrFax'] = $order['user_info'][$i]['fax'];

						break;

					case 'ST':

						if($order['company']) {
							$OLWebOrder['OLWebOrder']['OrderHeader']['DeliveryAddress']['DAName1'] = $order['user_info'][$i]['company'];
							$OLWebOrder['OLWebOrder']['OrderHeader']['DeliveryAddress']['DAName2'] = $order['user_info'][$i]['first_name'] . (($order['user_info'][$i]['middle_name']) ? ' ' . $order['user_info'][$i]['middle_name'] . ' ' : ' ') . $order['user_info'][$i]['last_name'];
						} else {

							$OLWebOrder['OLWebOrder']['OrderHeader']['DeliveryAddress']['DAName1'] = $order['user_info'][$i]['last_name'];
							$OLWebOrder['OLWebOrder']['OrderHeader']['DeliveryAddress']['DAName2'] = $order['user_info'][$i]['first_name'] . (($order['user_info'][$i]['middle_name']) ? ' ' . $order['user_info'][$i]['middle_name'] : '');
						}
						$OLWebOrder['OLWebOrder']['OrderHeader']['DeliveryAddress']['DATitle'] = $order['user_info'][$i]['title'];
						$OLWebOrder['OLWebOrder']['OrderHeader']['DeliveryAddress']['DAStreet'] = $order['user_info'][$i]['address_1'] . (($order['user_info'][$i]['address_2']) ? ', ' . $order['user_info'][$i]['address_2'] : '');
						$OLWebOrder['OLWebOrder']['OrderHeader']['DeliveryAddress']['DAZIPCode'] = $order['user_info'][$i]['zip'];
						$OLWebOrder['OLWebOrder']['OrderHeader']['DeliveryAddress']['DACity'] = $order['user_info'][$i]['city'];
						$OLWebOrder['OLWebOrder']['OrderHeader']['DeliveryAddress']['DAState'] = $order['user_info'][$i]['state'];
						$OLWebOrder['OLWebOrder']['OrderHeader']['DeliveryAddress']['DACountry'] = $this->getISOCode2($order['user_info'][$i]['country']);

						break;
				}
			}
			$OLWebOrder['OLWebOrder']['OrderHeader']['ProcessingOptions']['CarrierCode'] = $this->getCarrierCode($order[$i]['ship_method_id']);
			$OLWebOrder['OLWebOrder']['OrderHeader']['ProcessingOptions']['DeliveryPriceGross'] = 0;
			$OLWebOrder['OLWebOrder']['OrderHeader']['Payment']['Currency'] = $order['order_currency'];
			$OLWebOrder['OLWebOrder']['OrderHeader']['Payment']['BelOLPayCond'] = $this->getPaymentCode($order['payment'][0]['payment_method_id']);

			$this->OLWebOrder[] = $OLWebOrder;
			$OLWebOrder = array();
		}
		$this->xmldoc = &$xmldoc;
		return true;
	}

	/**
    * output of export
    * @return bool true/false
    */
	function output_export() {
		header ("Last-Modified: ". gmdate ("D, d M Y H:i:s"). " GMT");  // immer ge�ndert
		header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header ("Pragma: no-cache"); // HTTP/1.0
		header ("Content-type: text/xml");

		DOMIT_Utilities::fromArray($this->xmldoc, $this->OLWebOrder);

		echo $this->xmldoc->toString(false, true);
		/*echo '<pre>';
		print_r($orders);
		echo '</pre>';*/
		return true;
	}

	function getISOCode2($isocode3) {
		return $isocode3;
	}

	function getCarrierCode($ship_method_id) {
		return $ship_method_id;
	}

	function getPaymentCode($payment_method_id) {
		return $payment_method_id;
	}
}
