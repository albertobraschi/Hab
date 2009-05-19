<?php
/**
 * @package JoomlaPack
 * @copyright Copyright (c)2006-2008 JoomlaPack Developers
 * @license GNU General Public License version 2, or later
 * @version $id$
 * @since 1.3
 */

defined('_JEXEC') or die('Restricted access');

/**
 * A class with utility functions to get the backup readiness status,
 * as well as "quirks" information. In contrast with most helper functions,
 * it has to be instanciated as an object with the getInstance() method in
 * order to work as expected.
 *
 */
class JoomlapackHelperStatus extends JObject 
{
	/** @var boolean Backup readiness status, true indicates ok */
	var $status = false;
	/** @var boolean Is output folder writable? */
	var $outputWritable = false;
	/** @var boolean Is temporary folder writable? */
	var $tempWritable = false;
	/** @var array Quirks detected (each entry contains code, severity, title, help_url keys) */
	var $quirks = array();	
	
	/**
	 * Singleton pattern
	 *
	 * @return JoomlapackHelperStatus
	 */
	function &getInstance()
	{
		static $instance;
		
		if( empty($instance) )
		{
			$instance = new JoomlapackHelperStatus();
		}
		
		return $instance;
	}
	
	/**
	 * Public contructor. Automatically initializes the object with the status and quirks.
	 * 
	 * @access public
	 * @return JoomlapackHelperStatus
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->_obtainStatus();
		$this->_obtainQuirks();
	}
	
	/**
	 * Returns the HTML for the backup status cell
	 *
	 * @return string HTML
	 */
	function getStatusCell()
	{
		if($this->status && empty($this->quirks))
		{
			$imageURL = JURI::base().'components/com_joomlapack/assets/images/ok_small.png';
			$html = '<p class="ok"><img src="' . $imageURL . '" border="0" width="16" height="16" />'.JText::_('JPSTATUSOK').'</p>';
		}
		elseif($this->status && !empty($this->quirks))
		{
			$imageURL = JURI::base().'components/com_joomlapack/assets/images/ok_small.png';
			$html = '<p class="statuswarning"><img src="' . $imageURL . '" border="0" width="16" height="16" />'.JText::_('JPSTATUSWARN').'</p>';
		}
		else
		{
			$imageURL = JURI::base().'components/com_joomlapack/assets/images/error_small.png';
			$html = '<p class="notok"><img src="' . $imageURL . '" border="0" width="16" height="16" />'.JText::_('JPSTATUSNOTOK').'</p>';
		}
		
		jpimport('helpers.utils', true);
		JoomlapackHelperUtils::getJoomlaPackVersion();
		$html .= '<p><span style="font-size: small; color: #666666">'.JText::_('JOOMLAPACK').' '._JP_VERSION.' ('._JP_DATE.')</span></p>';
		
		return $html;
	}
	
	 /**
	  * render news feed from JoomlaPack site
	  */
	 function getNewsCell() {
	 	// Fix 2.1.1: News reader is disabled by default, because it causes JP to crash on systems w/ 8M of PHP Memory Limit
	 	$registry =& JoomlapackModelRegistry::getInstance();
	 	$enabled = $registry->get('showrss', false);
	 	if(!$enabled)
	 	{
	 		return JText::_('NEWS_DISABLED');
	 	}
	 	
	 	
	 	$output = '';

		//  get RSS parsed object
		$options = array();
		$options['rssUrl']		= 'http://www.joomlapack.net/news/feed/rss.html';
		$options['cache_time']	= 86400;

		$rssDoc =& JFactory::getXMLparser('RSS', $options);

		if ( $rssDoc == false ) {
			$output = JText::_('Error: Feed not retrieved');
		} else {	
			// channel header and link
			$title 	= $rssDoc->get_title();
			$link	= $rssDoc->get_link();
			
			$output = '<table class="adminlist">';
			$output .= '<tr><th colspan="3"><a href="'.$link.'" target="_blank">'.JText::_($title) .'</th></tr>';
			$output .= '<tr><td colspan="3">'.JText::_('NEWS_INTRODUCTION').'</td></tr>';
			
			$items = array_slice($rssDoc->get_items(), 0, 3);
			$numItems = count($items);
            if($numItems == 0) {
            	$output .= '<tr><th>' .JText::_('No news items found'). '</th></tr>';
            } else {
            	$k = 0;
                for( $j = 0; $j < $numItems; $j++ ) {
                    $item = $items[$j];
                	$output .= '<tr><td class="row' .$k. '">';
                	$output .= '<a href="' .$item->get_link(). '" target="_blank">' .$item->get_title(). '</a>';
					if($item->get_description()) {
	                	$description = $this->limitText($item->get_description(), 50);
						$output .= '<br />' .$description;
					}
                	$output .= '</td></tr>';
                }
            }
			$k = 1 - $k;
						
			$output .= '</table>';
		}	 	
	 	return $output;
	 }
	 
	function limitText($text, $wordcount)
	{
		if(!$wordcount) {
			return $text;
		}

		$texts = explode( ' ', $text );
		$count = count( $texts );

		if ( $count > $wordcount )
		{
			$text = '';
			for( $i=0; $i < $wordcount; $i++ ) {
				$text .= ' '. $texts[$i];
			}
			$text .= '...';
		}

		return $text;
	}
	 
	/**
	 * Returns HTML for the warnings (status details)
	 *
	 * @return string HTML
	 */
	function getQuirksCell($onlyErrors = false)
	{
		$html = '';
		
		if(!empty($this->quirks))
		{
			$html = "<ul>\n";
			foreach($this->quirks as $quirk)
			{
				$html .= $this->_renderQuirk($quirk, $onlyErrors);
			}
			$html .= "</ul>\n";
		}
		else
		{
			$html = '<p>'.JText::_('QNONE').'</p>';
		}
		
		return $html;
	}
	
	/**
	 * Returns a boolean value, indicating if quirks have been detected
	 * @return bool True if there is at least one quirk detected
	 */
	function hasQuirks()
	{
		return !empty($this->quirks);
	}
	
	/**
	 * Gets the HTML for a single line of the quirks cell, based on quirks settings
	 *
	 * @param array $quirk A quirk definition array
	 */
	function _renderQuirk($quirk, $onlyErrors = false)
	{
		if( $onlyErrors && ($quirk['severity'] != 'critical') ) return '';
		$quirk['severity'] = $quirk['severity'] == 'critical' ? 'high' : $quirk['severity']; 
		$out = '<li><a class="severity-'.$quirk['severity'].'" href="'.$quirk['help_url'].'" target="_blank">'.$quirk['description'].'</a>'."\n";
		return $out;
	}
	
	/**
	 * Checks temporary and output directories for writability and sets $status,
	 * $outputWritable and $tempWritable
	 *
	 * @access private
	 */
	function _obtainStatus()
	{
		// Get output writable status
		jpimport('models.registry', true);
		$registry =& JoomlapackModelRegistry::getInstance();
		$outdir = $registry->get('OutputDirectory');
		
		jimport('joomla.filesystem.path');
		$outdir = JPath::clean($outdir,'/');
		$this->outputWritable = is_writable($outdir);

		// Get temp writable status
		$jregistry = JFactory::getConfig();
		$tempdir = $jregistry->getValue('config.tmp_path');
		$tempdir = JPath::clean($tempdir);
		
		$this->tempWritable = is_writable($tempdir);
		
		$this->status = $this->outputWritable && $this->tempWritable;
	}

	/**
	 * Runs the "quirks" detection scripts. These are potential problems related to server
	 * configuration, out of JoomlaPack's control. They are intended to give the user a
	 * chance to fix them before they cause the backup to fail, eventually saving both
	 * the user's and support personel's time.
	 * 
	 * "Quirks" numbering scheme:
	 * Q0xx No-go errors
	 * Q1xx	Critical system configuration errors
	 * Q2xx	Medium and low system configuration warnings
	 * Q3xx	Critical component configuration errors
	 * Q4xx	Medium and low component configuration warnings
	 * 
	 * It populates the $quirks array.
	 * 
	 * @access private 
	 *
	 */
	function _obtainQuirks()
	{
		$this->quirks = array();
		// @todo Execute quirks detection scripts

		$this->_getQuirk($this->quirks, '001', 'critical');
		$this->_getQuirk($this->quirks, '002', 'critical');
		
		$this->_getQuirk($this->quirks, '101', 'high');
		$this->_getQuirk($this->quirks, '102', 'high');
		$this->_getQuirk($this->quirks, '103', 'high');
		
		$this->_getQuirk($this->quirks, '201', 'low');
		$this->_getQuirk($this->quirks, '202', 'medium');
		$this->_getQuirk($this->quirks, '203', 'medium');
		$this->_getQuirk($this->quirks, '204', 'medium');

		$this->_getQuirk($this->quirks, '401', 'low');
	}
	
	/**
	 * Gets a "quirk" status and adds it to the list if it is active
	 * 
	 * @param array $quirks The quirks array
	 * @param string $code The Quirks code, without the Q
	 * @param string $severity Severity: 'low','medium','high'
	 */
	function _getQuirk(&$quirks, $code, $severity)
	{
		$methodName = '_q'.$code;
		if($this->$methodName())
		{
			$description = JText::_('Q'.$code);
			if($severity == 'high') $this->status = false;
			$quirks[] = array(
				'code'			=> $code,
				'severity'		=> $severity,
				'description'	=> $description,
				'help_url'		=> 'http://www.joomlapack.net/help-support-documentation/warnings/q'.$code.'.html'
			);			
		}
	}
	
	//http://www.joomlapack.net/help-support-documentation/warnings/
	
	/**
	 * Q001 - HIGH - Output directory unwritable
	 *
	 * @return bool
	 */
	function _q001()
	{
		return !$this->outputWritable;
	}
	
	/**
	 * Q002 - HIGH - Temporary directory unwritable
	 *
	 * @return bool
	 */
	function _q002()
	{
		return !$this->tempWritable;
	}
	
	/**
	 * Q101 - HIGH - open_basedir on output directory
	 *
	 * @return bool
	 */
	function _q101()
	{
		jpimport('models.registry', true);
		$registry =& JoomlapackModelRegistry::getInstance();
		$outdir = $registry->get('OutputDirectory');
		return $this->_checkOpenBasedirs($outdir);
	}
	
	/**
	 * Q102 - HIGH - open_basedir on temporary directory (is it necessary?)
	 *
	 * @return bool
	 */
	function _q102()
	{
		$jregistry = JFactory::getConfig();
		$tempdir = $jregistry->getValue('config.tmp_path');
		return $this->_checkOpenBasedirs($tempdir);
	}
	
	/**
	 * Q103 - HIGH - Less than 10" of max_execution_time with PHP Safe Mode enabled
	 *
	 * @return bool
	 */
	function _q103()
	{
		$exectime = ini_get('max_execution_time');
		$safemode = ini_get('safe_mode');
		if(!$safemode) return false;
		if(!is_numeric($exectime)) return false;
		if($exectime <= 0) return false;
		return $exectime < 10;
	}
	
	/**
	 * Q201 - LOW  - PHP4 detected
	 *
	 * @return bool
	 */
	function _q201()
	{
		return version_compare(PHP_VERSION,'5.0.0') < 0;
	}
	
	/**
	 * Q202 - MED  - CRC problems with hash extension not present
	 *
	 * @return bool
	 */
	function _q202()
	{
		jpimport('models.registry', true);
		$registry =& JoomlapackModelRegistry::getInstance();
		$archiver = $registry->get('packerengine');
		if($archiver != 'zip') return false;
		return !function_exists('hash_file');
	}
	
	/**
	 * Q203 - MED  - Default output directory in use
	 *
	 * @return bool
	 */
	function _q203()
	{
		jpimport('models.registry', true);
		$registry =& JoomlapackModelRegistry::getInstance();
		$outdir = $registry->get('OutputDirectory');
		jimport('joomla.filesystem.path');
		$outdir = JPath::clean(realpath($outdir));
		$default = JPath::clean(realpath(JPATH_COMPONENT_ADMINISTRATOR.DS.'backup'));
		return $outdir == $default;
	}
	
	/**
	 * Q204 - MED  - Disabled functions may affect operation
	 *
	 * @return bool
	 */
	function _q204()
	{
		$disabled = ini_get('disabled_functions');
		return (!empty($disabled));
	}
	
	/**
	 * Q401 - LOW  - ZIP format selected
	 *
	 * @return bool
	 */
	function _q401()
	{
		jpimport('models.registry', true);
		$registry =& JoomlapackModelRegistry::getInstance();
		$archiver = $registry->get('packerengine');
		return $archiver == 'zip';
	}
	/**
	 * Checks if a path is restricted by open_basedirs
	 *
	 * @param string $check The path to check
	 * @return bool True if the path is restricted (which is bad)
	 */
	function _checkOpenBasedirs($check)
	{
		static $paths;
		
		if(empty($paths))
		{
			$open_basedir = ini_get('open_basedir');
			if(empty($open_basedir)) return false;
			$delimiter = strpos($open_basedir, ':') !== false ? ':' : ';';
			$paths = explode($delimiter, $open_basedir);
		}
		
		if(empty($paths))
		{
			return false; // no restrictions
		}
		else
		{
			jimport('joomla.filesystem.path');
			$check = JPath::clean(realpath($check)); // Resolve symlinks, like PHP does
			$included = false;
			foreach($paths as $path)
			{
				$path = JPath::clean(realpath($path));
				if(strlen($check) >= strlen($path))
				{
					// Only check if the path to check is longer than the inclusion path.
					// Otherwise, I guarantee it's not included!!
					// If the path to check begins with an inclusion path, it's permitted. Easy, huh?
					if(substr($check,0,strlen($path)) == $path) $included = true;
				}
			}
			
			return !$included;
		}
	}
}