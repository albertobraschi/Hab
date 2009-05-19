<?php

/**
 * @version		$Id: model.php 10207 2008-04-17 15:46:15Z ircmaxell $
 * @package		Joomla
 * @subpackage	Installation
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * @package		Joomla
 * @subpackage	Installation
 */

jimport('joomla.application.component.model');

class JInstallationModel extends JModel
{
	/**
	 * Array used to store data between model and view
	 *
	 * @var		Array
	 * @access	protected
	 * @since	1.5
	 */
	var	$data		= array();

	/**
	 * Array used to store user input created during the installation process
	 *
	 * @var		Array
	 * @access	protected
	 * @since	1.5
	 */
	var	$vars		= array();

	/**
	 * Constructor
	 */
	function __construct($config = array())
	{
		$this->_state = new JObject();
		//set the view name
		if (empty( $this->_name ))
		{
			if (isset($config['name']))  {
				$this->_name = $config['name'];
			}
			else
			{
				$r = null;
				if (!preg_match('/Model(.*)/i', get_class($this), $r)) {
					JError::raiseError (500, "JModel::__construct() : Can't get or parse class name.");
				}
				$this->_name = strtolower( $r[1] );
			}
		}
	}

	/**
	 * Get data for later use
	 *
	 * @return	string
	 * @access	public
	 * @since	1.5
	 */
	function & getData($key){

		if ( ! array_key_exists($key, $this->data) )
		{
			$null = null;
			return $null;
		}

		return $this->data[$key];
	}

	/**
	 * Get the configuration variables for the installation
	 *
	 * @return	Array Configuration variables
	 * @access	public
	 * @since	1.5
	 */
	function & getVars()
	{
		if ( ! $this->vars )
		{
			// get a recursively slash stripped version of post
			$post		= (array) JRequest::get( 'post' );
			$postVars	= JArrayHelper::getValue( $post, 'vars', array(), 'array' );
			$session	=& JFactory::getSession();
			$registry	=& $session->get('registry');
			$registry->loadArray($postVars, 'application');
			$this->vars	= $registry->toArray('application');
		}

		return $this->vars;
	}
	
	/**
	 * Set data for later use
	 *
	 * @param	string $key Data key
	 * @param	Mixed data
	 * @access	public
	 * @since	1.5
	 */
	function setData($key, $value){
		$this->data[$key]	= $value;
	}
	
	/**
	 * Get the local PHP settings
	 *
	 * @param	$val Value to get
	 * @return	Mixed
	 * @access	protected
	 * @since	1.5
	 */
	function getPhpSetting($val) {
		$r =  (ini_get($val) == '1' ? 1 : 0);
		return $r ? 'ON' : 'OFF';
	}


	/**
	 * Parse an INI file and return an associative array. Since PHP versions before 5.1 are
	 * bitches with regards to INI parsing, I use a PHP-only solution to overcome this
	 * obstacle.
	 *
	 * @param string $file The file to process
	 * @param bool $process_sections True to also process INI sections
	 * @return array An associative array of sections, keys and values
	 */
	function parse_ini_file( $file, $process_sections )
	{
		if( version_compare(PHP_VERSION, '5.1.0', '>=') )
		{
			return @parse_ini_file($file, $process_sections);
		} else {
			return @$this->_parse_ini_file($file, $process_sections);
		}
	}
	
	/**
	 * A PHP based INI file parser.
	 * 
	 * Thanks to asohn ~at~ aircanopy ~dot~ net for posting this handy function on
	 * the parse_ini_file page on http://gr.php.net/parse_ini_file
	 * 
	 * @param string $file Filename to process
	 * @param bool $process_sections True to also process INI sections
	 * @return array An associative array of sections, keys and values
	 * @access private
	 */
	function _parse_ini_file($file, $process_sections = false)
	{
		  $process_sections = ($process_sections !== true) ? false : true;

		  $ini = file($file);
		  if (count($ini) == 0) {return array();}
		
		  $sections = array();
		  $values = array();
		  $result = array();
		  $globals = array();
		  $i = 0;
		  foreach ($ini as $line) {
		    $line = trim($line);
		    $line = str_replace("\t", " ", $line);
		
		    // Comments
		    if (!preg_match('/^[a-zA-Z0-9[]/', $line)) {continue;}
		
		    // Sections
		    if ($line{0} == '[') {
		      $tmp = explode(']', $line);
		      $sections[] = trim(substr($tmp[0], 1));
		      $i++;
		      continue;
		    }
		
		    // Key-value pair
		    list($key, $value) = explode('=', $line, 2);
		    $key = trim($key);
		    $value = trim($value);
		    if (strstr($value, ";")) {
		      $tmp = explode(';', $value);
		      if (count($tmp) == 2) {
		        if ((($value{0} != '"') && ($value{0} != "'")) ||
		            preg_match('/^".*"\s*;/', $value) || preg_match('/^".*;[^"]*$/', $value) ||
		            preg_match("/^'.*'\s*;/", $value) || preg_match("/^'.*;[^']*$/", $value) ){
		          $value = $tmp[0];
		        }
		      } else {
		        if ($value{0} == '"') {
		          $value = preg_replace('/^"(.*)".*/', '$1', $value);
		        } elseif ($value{0} == "'") {
		          $value = preg_replace("/^'(.*)'.*/", '$1', $value);
		        } else {
		          $value = $tmp[0];
		        }
		      }
		    }
		    $value = trim($value);
		    $value = trim($value, "'\"");
		
		    if ($i == 0) {
		      if (substr($line, -1, 2) == '[]') {
		        $globals[$key][] = $value;
		      } else {
		        $globals[$key] = $value;
		      }
		    } else {
		      if (substr($line, -1, 2) == '[]') {
		        $values[$i-1][$key][] = $value;
		      } else {
		        $values[$i-1][$key] = $value;
		      }
		    }
		  }
		
		  for($j = 0; $j < $i; $j++) {
		    if ($process_sections === true) {
		      $result[$sections[$j]] = $values[$j];
		    } else {
		      $result[] = $values[$j];
		    }
		  }
		
		  return $result + $globals;
	}
	
	function getDatabasesINI()
	{
		$out = null;
		$filename = dirname(__FILE__).DS.'..'.DS.'..'.DS.'sql'.DS.'databases.ini';
		if(@file_exists($filename))
		{
			$databases = $this->parse_ini_file($filename, true);
			if(is_array($databases))
			{
				$out = array();
				foreach($databases as $section => $data)
				{
					$out[$section]['DBname']		= $data['dbname'];
					$out[$section]['DBhostname']	= $data['dbhost'];
					$out[$section]['DBPrefix']		= $data['prefix'];
					$out[$section]['DBuserName']	= $data['dbuser'];
					$out[$section]['DBpassword']	= $data['dbpass'];
					$out[$section]['DBfilename']	= $data['sqlfile'];
				}
			}
		}
		return $out;
	}

	/**
	 * Gets the parameters for database creation
	 *
	 * @return	boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function makeDB($vars = false, $checkprefix=true)
	{
		global $mainframe;

		// Initialize variables
		if ($vars === false) {
			$vars	= $this->getVars();
		}
		
		// Is this an extra DB restoration?
		$isextradb = JArrayHelper::getValue($vars,'extradbsection','') != '';
		if($isextradb)
		{
			$onErrorBack = 'extradbConfig';
		}
		else
		{
			$onErrorBack = 'dbconfig';
		}

		$errors 	= null;
		$lang 		= JArrayHelper::getValue($vars, 'lang', 'en-GB');
		$DBcreated	= JArrayHelper::getValue($vars, 'DBcreated', '0');
		$DBtype 	= JArrayHelper::getValue($vars, 'DBtype', 'mysql');
		$DBhostname = JArrayHelper::getValue($vars, 'DBhostname', '');
		$DBuserName = JArrayHelper::getValue($vars, 'DBuserName', '');
		$DBpassword = JArrayHelper::getValue($vars, 'DBpassword', '');
		$DBname 	= JArrayHelper::getValue($vars, 'DBname', '');
		$DBPrefix 	= JArrayHelper::getValue($vars, 'DBPrefix', 'jos_');
		$DBOld 		= JArrayHelper::getValue($vars, 'DBOld', 'bu');
		$DBversion 	= JArrayHelper::getValue($vars, 'DBversion', '');

		// these 3 errors should be caught by the javascript in dbConfig
		if ($DBtype == '')
		{
			$this->setError(JText::_('validType'));
			$this->setData('back', $onErrorBack);
			$this->setData('errors', $errors);
			return false;
			//return JInstallationView::error($vars, JText::_('validType'), $onErrorBack);
		}
		if (!$DBhostname || !$DBuserName || !$DBname)
		{
			$this->setError(JText::_('validDBDetails'));
			$this->setData('back', $onErrorBack);
			$this->setData('errors', $errors);
			return false;
			//return JInstallationView::error($vars, JText::_('validDBDetails'), $onErrorBack);
		}
		if ($DBname == '')
		{
			$this->setError(JText::_('emptyDBName'));
			$this->setData('back', $onErrorBack);
			$this->setData('errors', $errors);
			return false;
			//return JInstallationView::error($vars, JText::_('emptyDBName'), $onErrorBack);
		}
		if($checkprefix)
		{
			if (!preg_match( '#^[a-zA-Z]+[a-zA-Z0-9_]*$#', $DBPrefix )) {
				$this->setError(JText::_('MYSQLPREFIXINVALIDCHARS'));
				$this->setData('back', $onErrorBack);
				$this->setData('errors', $errors);
				return false;
			}
			if (strlen($DBPrefix) > 15) {
				$this->setError(JText::_('MYSQLPREFIXTOOLONG'));
				$this->setData('back', $onErrorBack);
				$this->setData('errors', $errors);
				return false;
			}
		}
		
		if (strlen($DBname) > 64) {
			$this->setError(JText::_('MYSQLDBNAMETOOLONG'));
			$this->setData('back', $onErrorBack);
			$this->setData('errors', $errors);
			return false;
		}

		if (!$DBcreated)
		{
			$DBselect	= false;
			$db = & JInstallationHelper::getDBO($DBtype, $DBhostname, $DBuserName, $DBpassword, null, $DBPrefix, $DBselect);

			if ( JError::isError($db) ) {
				// connection failed
				$this->setError(JText::sprintf('WARNNOTCONNECTDB', $db->toString()));
				$this->setData('back', $onErrorBack);
				$this->setData('errors', $db->toString());
				return false;
			}

			if ($err = $db->getErrorNum()) {
				// connection failed
				$this->setError(JText::sprintf('WARNNOTCONNECTDB', $db->getErrorNum()));
				$this->setData('back', $onErrorBack);
				$this->setData('errors', $db->getErrorMsg());
				return false;
			}

			//Check utf8 support of database
			$DButfSupport = $db->hasUTF();

			// Try to select the database
			if ( ! $db->select($DBname) )
			{
				if (JInstallationHelper::createDatabase($db, $DBname, $DButfSupport))
				{
					$db->select($DBname);
					/*
					// make the new connection to the new database
					$db = NULL;
					$db = & JInstallationHelper::getDBO($DBtype, $DBhostname, $DBuserName, $DBpassword, $DBname, $DBPrefix);
					*/
				} else {
					$this->setError(JText::sprintf('WARNCREATEDB', $DBname));
					$this->setData('back', $onErrorBack);
					$this->setData('errors', $db->getErrorMsg());
					return false;
					//return JInstallationView::error($vars, array (JText::sprintf('WARNCREATEDB', $DBname)), $onErrorBack, $error);
				}
			} else {

				// pre-existing database - need to set character set to utf8
				// will only affect MySQL 4.1.2 and up
				JInstallationHelper::setDBCharset($db, $DBname);
			}

			$db = & JInstallationHelper::getDBO($DBtype, $DBhostname, $DBuserName, $DBpassword, $DBname, $DBPrefix);

			if ($DBOld == 'rm') {
				if (JInstallationHelper::deleteDatabase($db, $DBname, $DBPrefix, $errors)) {
					$this->setError(JText::_('WARNDELETEDB'));
					$this->setData('back', $onErrorBack);
					$this->setData('errors', $errors);
					return false;
					//return JInstallationView::error($vars, , $onErrorBack, JInstallationHelper::errors2string($errors));
				}
			}
			else
			{
				/*
				 * We assume since we aren't deleting the database that we need
				 * to back it up :)
				 */
				if (JInstallationHelper::backupDatabase($db, $DBname, $DBPrefix, $errors)) {
					$this->setError(JText::_('WARNBACKINGUPDB'));
					$this->setData('back', $onErrorBack);
					$this->setData('errors', JInstallationHelper::errors2string($errors));
					return false;
					//return JInstallationView::error($vars, JText::_('WARNBACKINGUPDB'), 'dbconfig', JInstallationHelper::errors2string($errors));
				}
			}
		}

		return true;
	}

/* ============================================================================================= */

	/**
	 * Generate a panel of language choices for the user to select their language
	 *
	 * @return	boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function chooseLanguage()
	{
		global $mainframe;

		$vars	=& $this->getVars();

		jimport('joomla.language.helper');
		$native = JLanguageHelper::detectLanguage();
		$forced = $mainframe->getLocalise();

		if ( !empty( $forced['lang'] ) ){
			$native = $forced['lang'];
		}

		$lists = array ();
		$lists['langs'] = JLanguageHelper::createLanguageList($native);

		$this->setData('lists', $lists);

		return true;
	}

	/**
	 * Perform a preinstall check
	 *
	 * @return	boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function preInstall()
	{
		$vars	=& $this->getVars();
		$lists	= array ();

		$phpOptions[] = array (
			'label' => JText::_('PHP version').' >= 4.3.10',
			'state' => phpversion() < '4.3.10' ? 'No' : 'Yes'
		);
		$phpOptions[] = array (
			'label' => '- '.JText::_('zlib compression support'),
			'state' => extension_loaded('zlib') ? 'Yes' : 'No'
		);
		$phpOptions[] = array (
			'label' => '- '.JText::_('XML support'),
			'state' => extension_loaded('xml') ? 'Yes' : 'No',
			'statetext' => extension_loaded('xml') ? 'Yes' : 'No'
		);
		$phpOptions[] = array (
			'label' => '- '.JText::_('MySQL support'),
			'state' => (function_exists('mysql_connect') || function_exists('mysqli_connect')) ? 'Yes' : 'No'
		);
		if (extension_loaded( 'mbstring' )) {
			$mbDefLang = strtolower( ini_get( 'mbstring.language' ) ) == 'neutral';
			$phpOptions[] = array (
				'label' => JText::_( 'MB language is default' ),
				'state' => $mbDefLang ? 'Yes' : 'No',
				'notice' => $mbDefLang ? '' : JText::_( 'NOTICEMBLANGNOTDEFAULT' )
			);
			$mbOvl = ini_get('mbstring.func_overload') != 0;
			$phpOptions[] = array (
				'label' => JText::_('MB string overload off'),
				'state' => !$mbOvl ? 'Yes' : 'No',
				'notice' => $mbOvl ? JText::_('NOTICEMBSTRINGOVERLOAD') : ''
			);
		}
		$sp = '';
		/*$phpOptions[] = array (
			'label' => JText::_('Session path set'),
			'state' => ($sp = ini_get('session.save_path')) ? 'Yes' : 'No'
			);
			$phpOptions[] = array (
			'label' => JText::_('Session path writable'),
			'state' => is_writable($sp) ? 'Yes' : 'No'
			);*/
		$cW = (@ file_exists('../configuration.php') && @ is_writable('../configuration.php')) || is_writable('../');
		$phpOptions[] = array (
			'label' => 'configuration.php '.JText::_('writable'),
			'state' => $cW ? 'Yes' : 'No',
			'notice' => $cW ? '' : JText::_('NOTICEYOUCANSTILLINSTALL')
		);
		$lists['phpOptions'] = & $phpOptions;

		$phpRecommended = array (
		array (
			JText::_('Safe Mode'),
			'safe_mode',
			'OFF'
			),
		array (
			JText::_('Display Errors'),
			'display_errors',
			'OFF'
			),
		array (
			JText::_('File Uploads'),
			'file_uploads',
			'ON'
			),
		array (
			JText::_('Magic Quotes Runtime'),
			'magic_quotes_runtime',
			'OFF'
			),
		array (
			JText::_('Register Globals'),
			'register_globals',
			'OFF'
			),
		array (
			JText::_('Output Buffering'),
			'output_buffering',
			'OFF'
			),
		array (
			JText::_('Session auto start'),
			'session.auto_start',
			'OFF'
			),
		);

		foreach ($phpRecommended as $setting)
		{
			$lists['phpSettings'][] = array (
				'label' => $setting[0],
				'setting' => $setting[2],
				'actual' => $this->getPhpSetting( $setting[1] ),
				'state' => $this->getPhpSetting($setting[1]) == $setting[2] ? 'Yes' : 'No'
			);
		}

		$this->setData('lists', $lists);

		return true;
	}
	
	/**
	 * Gets the parameters for database creation
	 *
	 *
	 * @return	boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function license()
	{
		return true;
	}

	/**
	 * Gets the parameters for database creation
	 *
	 * @return	boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function dbConfig()
	{
		$vars	=& $this->getVars();
		
		// JPI3 MOD ----------------------------------------
		// Try to find databases.ini and read data from the first section (core database)
		$databases = $this->getDatabasesINI();
		if(is_array($databases))
		{
			$sections = array_keys($databases);
			$core = $databases[$sections[0]];
			
			$vars['DBname'] = isset($vars['DBname']) ? $vars['DBname'] : $core['DBname'];
			$vars['DBhostname'] = isset($vars['DBhostname']) ? $vars['DBhostname'] : $core['DBhostname'];
			$vars['DBPrefix'] = isset($vars['DBPrefix']) ? $vars['DBPrefix'] : $core['DBPrefix'];
			$vars['DBuserName'] = isset($vars['DBuserName']) ? $vars['DBuserName'] : $core['DBuserName'];
			$vars['DBpassword'] = isset($vars['DBpassword']) ? $vars['DBpassword'] : $core['DBpassword'];
			$vars['DBfilename'] = $core['DBfilename'];
			$_REQUEST['vars[DBfilename]'] = $core['DBfilename'];
		}
		// ---------------------------------------- JPI3 MOD

		if (!isset ($vars['DBPrefix'])) {
			$vars['DBPrefix'] = 'jos_';
		}

		$lists	= array ();
		$files	= array ('mysql', 'mysqli',);
		$db		= JInstallationHelper::detectDB();
		foreach ($files as $file)
		{
			$option = array ();
			$option['text'] = $file;
			if (strcasecmp($option['text'], $db) == 0)
			{
				$option['selected'] = 'selected="true"';
			}
			$lists['dbTypes'][] = $option;
		}

		$doc =& JFactory::getDocument();

		$this->setData('lists', $lists);

		return true;
	}

	/**
	 * Gets the parameters for extra database creation
	 *
	 */
	function extradbConfig()
	{
		$vars		=& $this->getVars();
		$section	=  $vars['extradbsection'];
		
		// Try to find databases.ini and read data from the relevant section
		$databases = $this->getDatabasesINI();
		if(is_array($databases))
		{
			$core = $databases[$section];
			$vars['extraDBname'] = $core['DBname'];
			$vars['extraDBhostname'] = $core['DBhostname'];
			$vars['extraDBPrefix'] = $core['DBPrefix'];
			$vars['extraDBuserName'] = $core['DBuserName'];
			$vars['extraDBpassword'] = $core['DBpassword'];
			$vars['extraDBfilename'] = $core['DBfilename'];
			$_REQUEST['vars[DBfilename]'] = $core['DBfilename'];
		}
		else
		{
			return false; // This should NOT hapen!
		}
		
		$lists	= array ();
		$files	= array ('mysql', 'mysqli',);
		$db		= JInstallationHelper::detectDB();
		foreach ($files as $file)
		{
			$option = array ();
			$option['text'] = $file;
			if (strcasecmp($option['text'], $db) == 0)
			{
				$option['selected'] = 'selected="true"';
			}
			$lists['dbTypes'][] = $option;
		}

		$doc =& JFactory::getDocument();

		$this->setData('lists', $lists);

		return true;
	}
	
	
	function dumpLoad() {
		include (JPATH_BASE . '/includes/bigdump.php');
	}

	/**
	 * Gets ftp configuration parameters
	 *
	 * @return	boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function ftpConfig($DBcreated = '0')
	{
		global $mainframe;

		$vars	=& $this->getVars();

		// Require the xajax library
		require_once( JPATH_BASE.DS.'includes'.DS.'xajax'.DS.'xajax.inc.php' );

		// Instantiate the xajax object and register the function
		$xajax = new xajax(JURI::base().'installer/jajax.php');
		$xajax->registerFunction(array('getFtpRoot', 'JAJAXHandler', 'ftproot'));
		$xajax->registerFunction(array('FTPVerify', 'JAJAXHandler', 'ftpverify'));
		//$xajax->debugOn();

		//$vars['DBcreated'] = JArrayHelper::getValue($vars, 'DBcreated', $DBcreated);
		$vars['DBcreated'] = 1;

		$strip = get_magic_quotes_gpc();

		// Try preloading configuration.php in case we need it to populate variables...
		$originalVars = array();
		if (!isset ($vars['ftpHost'])) {
			if(file_exists('../configuration.php'))
			{
				include_once '../configuration.php';
				if(class_exists('JConfig'))
				{
					$originalVars = get_class_vars('JConfig');										
				}
			}
		}
		
		if (!isset ($vars['ftpEnable'])) {
			if(isset($originalVars['ftp_enable']))
			{
				$vars['ftpEnable'] = $originalVars['ftp_enable'];
			}
			else
			{
				$vars['ftpEnable'] = '1';				
			}
		}
		if (!isset ($vars['ftpHost'])) {
			if(isset($originalVars['ftp_host']))
			{
				$vars['ftpHost'] = $originalVars['ftp_host'];
			}
			else
			{
				$vars['ftpHost'] = '127.0.0.1';				
			}			
		}
		if (!isset ($vars['ftpPort'])) {
			if(isset($originalVars['ftp_port']))
			{
				$vars['ftpPort'] = $originalVars['ftp_port'];
			}
			else
			{
				$vars['ftpPort'] = '21';	
			}
		}
		if (!isset ($vars['ftpUser'])) {
		if(isset($originalVars['ftp_user']))
			{
				$vars['ftpUser'] = $originalVars['ftp_user'];
			}
			else
			{
				$vars['ftpUser'] = '';	
			}
		}
		if (!isset ($vars['ftpPassword'])) {
		if(isset($originalVars['ftp_pass']))
			{
				$vars['ftpPassword'] = $originalVars['ftp_pass'];
			}
			else
			{
				$vars['ftpPassword'] = '';	
			}			
		}

		$doc =& JFactory::getDocument();
		$doc->addCustomTag($xajax->getJavascript('', 'includes/js/xajax.js', 'includes/js/xajax.js'));

		return true;
	}

	/**
	 * Finishes configuration parameters
	 *
	 * @return	boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function mainConfig()
	{
		global $mainframe;

		$vars	=& $this->getVars();

		// get ftp configuration into registry for use in case of safe mode
		if($vars['ftpEnable']) {
			JInstallationHelper::setFTPCfg( $vars );
		}

		$doc =& JFactory::getDocument();

		if (isset ($vars['siteName']) && ($vars['siteName'] != ''))
		{
			$vars['siteName'] = stripslashes(stripslashes($vars['siteName']));
		}
		else
		{
			$old_configuration = realpath( dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'configuration.php' );
			if(file_exists($old_configuration))
			{
				$oldconfig = $this->_getConfigurationPHP($old_configuration);
				$vars['siteName'] = stripslashes(stripslashes($oldconfig['sitename']));
				$vars['fromname'] = stripslashes(stripslashes($oldconfig['mailfrom']));
			}
		}

		$folders = array (
			'administrator/backups',
			'administrator/cache',
			'administrator/components',
			'administrator/language',
			'administrator/modules',
			'administrator/templates',
			'cache',
			'components',
			'images',
			'images/banners',
			'images/stories',
			'language',
			'plugins',
			'plugins/content',
			'plugins/editors',
			'plugins/search',
			'plugins/system',
			'tmp',
			'modules',
			'templates',
		);

		// Now lets make sure we have permissions set on the appropriate folders
		//		foreach ($folders as $folder)
		//		{
		//			if (!JInstallationHelper::setDirPerms( $folder, $vars ))
		//			{
		//				$lists['folderPerms'][] = $folder;
		//			}
		//		}

		return true;
	}

	function _getConfigurationPHP($filename)
	{
		require_once($filename);
		return get_class_vars('JConfig');
	}
	
	function _mergeConfigVar( &$oldconfig, $key, $default )
	{
		if(isset($oldconfig[$key]))
		{
			return $oldconfig[$key];
		}
		else
		{
			return $default;
		}
		
	}
	
	/**
	 * Save the configuration information
	 *
	 * @return	boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function saveConfig()
	{
		global $mainframe;

		// Try to load an existing configuration.php
		$old_configuration = realpath( dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'configuration.php' );
		if(file_exists($old_configuration))
		{
			$oldconfig = $this->_getConfigurationPHP($old_configuration);
			$isDist = false;
		}
		else
		{
			$old_configuration = realpath( dirname(__FILE__).DS.'..'.DS.'..'.DS.'configuration.php-dist' );
			$oldconfig = $this->_getConfigurationPHP($old_configuration);
			$isDist = true;
		}
		
		$vars	=& $this->getVars();
		$lang	=& JFactory::getLanguage();

		// Import authentication library
		jimport( 'joomla.user.helper' );

		// Modify necessary values in $oldConfig if we are processing the configuration.php-dist file
		if($isDist)
		{
			$oldconfig['secret']			= JUserHelper::genRandomPassword(16);
			$oldconfig['offline_message']	= JText::_( 'STDOFFLINEMSG' );
			$oldconfig['MetaDesc']			= JText::_( 'STDMETADESC' );
			$oldconfig['MetaKeys']			= JText::_( 'STDMETAKEYS' );
			$oldconfig['MetaKeys']			= JText::_( 'STDMETAKEYS' );
		}

		// Modify temp and log path
		$oldconfig['tmp_path']			= JPATH_ROOT.DS.'tmp';
		$oldconfig['log_path']			= JPATH_ROOT.DS.'logs';			
		
		// If FTP has not been enabled, set the value to 0
		if (!isset($vars['ftpEnable']))
		{
			$oldconfig['ftpEnable'] = 0;
		}
		else
		{
			if($vars['ftpEnable']) {
			// Set FTP parameters
				$oldconfig['ftp_host'] = $vars['ftpHost'];
				$oldconfig['ftp_port'] = $vars['ftpPort'];
				$oldconfig['ftp_user'] = $vars['ftpUser'];
				$oldconfig['ftp_pass'] = $vars['ftpPassword'];
				$oldconfig['ftp_root'] = $vars['ftpRoot'];
				$oldconfig['ftp_enable'] = 1;
				/*
				 * Trim the last slash from the FTP root, as the FTP root usually replaces JPATH_ROOT.
				 * If the path had a trailing slash, this would lead to double slashes, like "/joomla//configuration.php"
				 */
				$oldconfig['ftp_root'] = rtrim($oldconfig['ftp_root'], '/');
			}
			else
			{
				$oldconfig['ftp_enable'] = 0;				
			}
			
		}

		
		// Set db parameters
		$oldconfig['dbtype']	= $vars['DBtype'];
		$oldconfig['host']		= $vars['DBhostname'];
		$oldconfig['user']		= $vars['DBuserName'];
		$oldconfig['password']	= $vars['DBpassword'];
		$oldconfig['db']		= $vars['DBname'];
		$oldconfig['dbprefix']	= $vars['DBPrefix'];
		
		// Other parameters
		$oldconfig['mailfrom']	= $vars['adminEmail'];
		$oldconfig['fromname']	= $vars['siteName'];
		$oldconfig['sitename']	= $vars['siteName'];
		
		// Create user
		switch ($vars['DBtype']) {

			case 'mssql' :
				$vars['ZERO_DATE'] = '1/01/1990';
				break;

			default :
				$vars['ZERO_DATE'] = '0000-00-00 00:00:00';
				break;
		}
		
		// If the live_site variable was set, update it with the detected live site's URL
		if($oldconfig['live_site'] != '')
		{
			$port = ( $_SERVER['SERVER_PORT'] == 80 ) ? '' : ":".$_SERVER['SERVER_PORT'];
			$root = $_SERVER['SERVER_NAME'] . $port . $_SERVER['PHP_SELF'];
			$upto = strpos( $root, "/installation" );
			$root = substr( $root, 0, $upto );
			$oldconfig['live_site'] = "http://".$root;;
		}

		// Update the super administrator user (but only if his id is 62 - default super administrator user)
		JInstallationHelper::createAdminUser($vars);

		/**
		 * Write the configuration file
		 */
		$buffer = '<?php'."\n";
		$buffer .= 'class JConfig {'."\n";
		foreach($oldconfig as $key => $value)
		{
			//$buffer .= "\t".'var $'.$key.' = \''.addslashes($value).'\';'."\n";
			if (is_array($value))
			{
				$buffer .= "\t".'var $'.$key.' = array('."\n";
				$max_count = sizeof( $value );
				$index = 0;
				foreach($value as $k2 => $v2)
				{
					$index++;
					$buffer .= "\t\t'$key' => '".addslashes($value)."'";
					$buffer .= ($index < $max_count) ? ",\n" : "\n";
				}
				$buffer .= "\t\t);\n";
			}
			else
				$buffer .= "\t".'var $'.$key.' = \''.addslashes($value).'\';'."\n";
		}
		$buffer .= '}'."\n";

		$path = JPATH_CONFIGURATION.DS.'configuration.php';

		if (file_exists($path)) {
			$canWrite = is_writable($path);
		} else {
			$canWrite = is_writable(JPATH_CONFIGURATION.DS);
		}

		/*
		 * If the file exists but isn't writable OR if the file doesn't exist and the parent directory
		 * is not writable we need to use FTP
		 */
		$ftpFlag = false;
		if ((file_exists($path) && !is_writable($path)) || (!file_exists($path) && !is_writable(dirname($path).'/'))) {
			$ftpFlag = true;
		}

		// Check for safe mode
		if (ini_get('safe_mode'))
		{
			$ftpFlag = true;
		}

		// Enable/Disable override
		if (!isset($vars['ftpEnable']) || ($vars['ftpEnable'] != 1))
		{
			$ftpFlag = false;
		}

		if ($ftpFlag == true)
		{
			// Connect the FTP client
			jimport('joomla.client.ftp');
			jimport('joomla.filesystem.path');

			$ftp = & JFTP::getInstance($vars['ftpHost'], $vars['ftpPort']);
			$ftp->login($vars['ftpUser'], $vars['ftpPassword']);

			// Translate path for the FTP account
			$file = JPath::clean(str_replace(JPATH_CONFIGURATION, $vars['ftpRoot'], $path), '/');

			// Use FTP write buffer to file
			if (!$ftp->write($file, $buffer)) {
				$this->setData('buffer', $buffer);
				return false;
			}

			$ftp->quit();

		}
		else
		{
			if ($canWrite) {
				file_put_contents($path, $buffer);
			} else {
				$this->setData('buffer', $buffer);
				return true;
			}
		}

		return true;
	}

	/**
	 * Displays the finish screen
	 *
	 * @return	boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function finish()
	{
		global $mainframe;

		$vars	=& $this->getVars();

		$vars['siteurl']	= JURI::root();
		$vars['adminurl']	= $vars['siteurl'].'administrator/';

		return true;
	}	
}
