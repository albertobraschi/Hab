<?php

/**
 * @version		$Id: controller.php 9764 2007-12-30 07:48:11Z ircmaxell $
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

jimport('joomla.application.component.controller');
require_once( dirname(__FILE__).DS.'models'.DS.'model.php');
require_once( dirname(__FILE__).DS.'views'.DS.'install'.DS.'view.php');

class JInstallationController extends JController
{
	var $_model		= null;

	var $_view		= null;

	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		$config['name']	= 'JInstallation';
		parent::__construct( $config );
	}

	/**
	 * Initialize data for the installation
	 *
	 * @return	Boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function initialize()
	{
		return true;
	}

	/**
	 * Overload the parent controller method to add a check for configuration variables
	 *  when a task has been provided
	 *
	 * @param	String $task Task to perform
	 * @return	Boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function execute($task)
	{
		global $mainframe;
		$model	=& $this->getModel();
		$vars =& $model->getVars();

		// Sanity check
		if ( $task && (isset($vars['auto']) ? $task != 'dbconfig' : $task != 'lang') )
		{

			/**
			 * To get past this point, a cookietest must be carried in the user's state.
			 * If the state is not set, then cookies are probably disabled.
			 **/

			$goodEnoughForMe = $mainframe->getUserState('application.cookietest');

			if ( ! $goodEnoughForMe )
			{
				$model	=& $this->getModel();
				$model->setError(JText::_('WARNCOOKIESNOTENABLED'));
				$view	=& $this->getView();
				$view->error();
				return false;
			}

		}
		else
		{
			// Zilch the application registry - start from scratch
			$session	=& JFactory::getSession();
			$registry	=& $session->get('registry');
			$registry->makeNameSpace('application');

			// Set the cookie test seed
			$mainframe->setUserState('application.cookietest', 1);
		}

		parent::execute($task);
	}
	
	/**
	 * Get the model for the installer component
	 *
	 * @return	JInstallerModel
	 * @access	protected
	 * @since	1.5
	 */
	function & getModel()
	{

		if ( ! $this->_model )
		{
			$this->_model	= new JInstallationModel();
		}

		return $this->_model;
	}

	/**
	 * Get the view for the installer component
	 *
	 * @return	JInstallerView
	 * @access	protected
	 * @since	1.5
	 */
	function & getView()
	{

		if ( ! $this->_view )
		{
			$this->_view	= new JInstallationView();
			$model	=& $this->getModel();
			$model->test = "blah";
			$this->_view->setModel($model, true);
		}

		return $this->_view;
	}

/*-------------------------------------------------------------------------------------------------
  Task Handling
  -------------------------------------------------------------------------------------------------*/

	/**
	 * Present a choice of languages
	 *
	 * Step One!
	 *
	 * @return	Boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function lang()
	{
		$model	=& $this->getModel();
		$view	=& $this->getView();

		if ( ! $model->chooseLanguage() )
		{
			$view->error();
			return false;
		}

		$view->chooseLanguage();

		return true;
	}
	
	/**
	 * Present a preinstall check
	 *
	 * Step Two!
	 *
	 * @return	Boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function preinstall()
	{
		$model	=& $this->getModel();
		$view	=& $this->getView();

		if ( ! $model->preInstall() )
		{
			$view->error();
			return true;
		}

		$view->preInstall();

		return true;
	}

	/**
	 * Present license information
	 *
	 * @return	Boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function license()
	{
		$model	=& $this->getModel();
		$view	=& $this->getView();

		if ( ! $model->license() )
		{
			$view->error();
			return false;
		}

		$view->license();

		return true;
	}

	/**
	 *
	 *
	 * @return	Boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function dbconfig()
	{
		$model	=& $this->getModel();
		$view	=& $this->getView();

		if ( ! $model->dbConfig() )
		{
			$view->error();
			return false;
		}

		$view->dbConfig();

		return true;
	}

	/**
	 * Restore the main database, holding the data for the Joomla! site itself (first entry
	 * in databases.ini). In fact, it shows a page inside which the restoration script (bigdump.php)
	 * will be ran.
	 *
	 */
	function mainDatabaseRestore()
	{
		$model =& $this->getModel();
		$view =& $this->getView();
		
		$model->setData('back', 'dbconfig');
		$model->setData('next', 'ftpconfig');
		
		// Are there extra databases in databases.ini?
		$databases = $model->getDatabasesINI();
		if(is_array($databases))
		{
			if(count(array_keys($databases)) > 1)
			{
				// There are extra databases. Modify the next button accordingly
				$model->setData('next', 'extradbconfig');
			}
		}
		
		// Try to create the database
		if(!$model->makeDB())
		{
			// If the database is not created, show an error message
			$view->error();
			return false;			
		}

		// Database created; restore the data
		$view->mainDatabaseRestore();			
		
		return true;
	}
	
	/**
	 * Configure and restore extra databases (MultiDB feature of JoomlaPack)
	 *
	 */
	function extradbconfig()
	{
		$model	=& $this->getModel();
		$view	=& $this->getView();

		$model->setData('back', 'extradbconfig');
		$model->setData('next', 'extrarestore');
		
		$vars =& $model->getVars();
		
		// Load databases.ini
		$databases = $model->getDatabasesINI();
		$sections = is_array($databases) ? array_keys($databases) : null;

		// Check if we've got at least one key to process, or we should skip to ftp config
		if(is_null($sections))
		{
			// No databases.ini detected. Skip to ftp configuration
			return $this->ftpconfig(1);
		}
		else
		{
			// Make sure we've got a key to process
			if( !isset($vars['extradbsection']) || empty($vars['extradbsection']) )
			{
				if(count($sections) > 1)
				{
					$_REQUEST['vars[extradbsection]'] = $sections[1];
					$vars['extradbsection'] = $sections[1];
				}
				else
				{
					// There was only the main database information present; skip to ftp config
					return $this->ftpconfig(1);
				}
			}
		}
		
		if ( ! $model->extradbConfig() )
		{
			$view->error();
			return false;
		}

		$view->extradbConfig();

		return true;
	}
	
	function extrarestore()
	{
		$model =& $this->getModel();
		$view =& $this->getView();

		$vars =& $model->getVars();
		
		$model->setData('back', 'extradbconfig');
		$model->setData('next', 'extradbconfig');
		
		// 1. Get databases.ini
		$databases = $model->getDatabasesINI();
		$sections = is_array($databases) ? array_keys($databases) : null;
		
		// 2. Check for last section
		$lastsection = array_pop($sections);
		array_push($sections, $lastsection);
		if($vars['extradbsection'] == $lastsection)
		{
			// It is. Next step = ftp config
			$model->setData('next', 'ftpconfig');
			$vars['nextextradbsection'] = '';
		}
		else
		{
			// Otherwise, provide the next section in the nextextradbsection parameter
			$found = false;
			$gotit = false;
			foreach($sections as $thissection)
			{
				if(!$gotit && $found)
				{
					$nextsection = $thissection;
					$gotit = true;
				}
				if($thissection == $vars['extradbsection']) $found = true;
			}
			
			// Degenerate case; should be caught in the previous if-block
			if(!$gotit)
			{
				$model->setData('next', 'ftpconfig');
				$vars['nextextradbsection'] = '';				
			}
			else
			{
				$model->setData('next', 'extradbconfig');			
				$vars['nextextradbsection'] = $nextsection;			
			}
		}
		
		// 3. Try to create the database
		$newvars = array();
		$newvars['DBtype']		= $vars['extraDBtype'];
		$newvars['DBhostname']	= $vars['extraDBhostname'];
		$newvars['DBuserName']	= $vars['extraDBuserName'];
		$newvars['DBpassword']	= $vars['extraDBpassword'];
		$newvars['DBname']		= $vars['extraDBname'];
		$newvars['DBPrefix']	= $vars['extraDBPrefix'];
		$newvars['extradbsection']	= $vars['extradbsection'];
		
		// Try to create the database
		if(!$model->makeDB($newvars, false))
		{
			// If the database is not created, show an error message
			$view->error();
			return false;			
		}
		
		// 4. Database created; restore the data
		$view->extraDatabaseRestore();			
		
		return true;		
	}
	
	/**
	 * Loads a database dump file using bigdump.php
	 *
	 */
	function dumpLoad() {
		$model	=& $this->getModel();
		$model->dumpLoad();
	}
	
	/**
	 * Present form for FTP information
	 *
	 * @return	Boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function ftpconfig()
	{
		$model	=& $this->getModel();
		$view	=& $this->getView();

		if ( ! $model->ftpConfig() )
		{
			$view->error();
			return false;
		}

		$view->ftpConfig();

		return true;
	}
	
	/**
	 * Present the main configuration options
	 *
	 * @return	Boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function mainconfig()
	{
		//$this->dumpLoad();
		$model	=& $this->getModel();
		$view	=& $this->getView();

		if ( ! $model->mainConfig() )
		{
			$view->error();
			return false;
		}

		$view->mainConfig();

		return true;
	}

	/**
	 *
	 *
	 * @return	Boolean True if successful
	 * @access	public
	 * @since	1.5
	 */
	function saveconfig()
	{
		$model	=& $this->getModel();
		$view	=& $this->getView();

		if ( ! $model->saveConfig() )
		{
			$view->error();
			return false;
		}

		if ( ! $model->finish() )
		{
			$view->error();
			return false;
		}

		$view->finish();

		return true;
	}
}