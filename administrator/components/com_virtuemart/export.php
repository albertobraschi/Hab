<?php

// Set flag that this is a parent file
define( '_VALID_MOS', 1 );

error_reporting( E_ERROR );
/*** access Joomla's configuration file ***/
$my_path = dirname(__FILE__);

if( file_exists($my_path."/../../../configuration.php")) {
	require_once($my_path."/../../../configuration.php");
}
elseif( file_exists($my_path."/../../configuration.php")){
	require_once($my_path."/../../configuration.php");
}
elseif( file_exists($my_path."/configuration.php")){
	require_once( $my_path."/configuration.php" );
}
else {
	die( "Joomla Configuration File not found!" );
}
if( isset($_REQUEST['mosConfig_absolute_path'])) die();

require_once( $mosConfig_absolute_path . '/includes/joomla.php' );
require_once($mosConfig_absolute_path . '/includes/database.php');
$database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );

// load Joomla Language File
if (file_exists( $mosConfig_absolute_path. '/language/'.$mosConfig_lang.'.php' )) {
	require_once( $mosConfig_absolute_path. '/language/'.$mosConfig_lang.'.php' );
}
else {
	require_once( $mosConfig_absolute_path. '/language/english.php' );
}
/*** END of Joomla config ***/

/*require_once( '../../../configuration.php' );

/*** VirtueMart part ***/
require_once($mosConfig_absolute_path.'/administrator/components/com_virtuemart/virtuemart.cfg.php');
require_once( CLASSPATH. 'ps_main.php');

require_once( CLASSPATH. "language.class.php" );

require_once( $mosConfig_absolute_path . '/includes/phpmailer/class.phpmailer.php');
$mail = new mosPHPMailer();
$mail->PluginDir = $mosConfig_absolute_path . '/includes/phpmailer/';
$mail->SetLanguage("en", $mosConfig_absolute_path . '/includes/phpmailer/language/');

/* load the VirtueMart Language File */
if (file_exists( ADMINPATH. 'languages/'.$mosConfig_lang.'.php' ))
require_once( ADMINPATH. 'languages/'.$mosConfig_lang.'.php' );
else
require_once( ADMINPATH. 'languages/english.php' );

/* Load the VirtueMart database class */
require_once( CLASSPATH. 'ps_database.php' );


/*** END VirtueMart part ***/


// Instantiate the DB class
$db = new ps_DB();

$default_vendor = 1;
$export_id = $database->getEscaped( vmGet( $_REQUEST, 'method', '' ) );
if (!empty($export_id)) {
	$q = "SELECT * FROM #__{vm}_export WHERE ";
	$q .= "export_id='$export_id'";
	$db->query($q);
	$db->next_record();
} else {
	echo 'Export Method not defined. Please define method.';
	exit;
}

if($db->f('export_enabled') != 'Y') {
	echo 'Export Method Inactive. Please activate Export Module in Admin.';
	exit;
}

$export_class = $db->f('export_class');
//if there is no export class then only process order export config from database

if(file_exists(CLASSPATH."export/$export_class.php")) {
	require_once(CLASSPATH."export/$export_class.php");
	if(file_exists(CLASSPATH."export/$export_class.cfg.php")) {
		require_once(CLASSPATH."export/$export_class.cfg.php");
	}
	eval( "\$_EXPORT = new $export_class();" );

	//Call authentication function
	if(method_exists($_EXPORT, 'process_authentication')) {
		if (!$_EXPORT->process_authentication()) {
			exit;
		}
	}
	eval($db->f('export_config'));

	if(method_exists($_EXPORT, 'process_export')) {
		$_EXPORT->process_export($db);
	}

	$_EXPORT->output_export();
} else {
	eval($db->f('export_config'));
}


?>