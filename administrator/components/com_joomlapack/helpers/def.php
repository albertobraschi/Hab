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
 * HTML render class for the DEF page.
 * 
 * The static methods take care of user's choice between AJAX or JSRedirects operation
 * (per profile setting!) and produce the JavaScript necessary.
 *
 */
class JoomlapackHelperDef extends JObject 
{
	/**
	 * Outputs the AJAX-powered JavaScript to the browser. It is meant to be used
	 * by renderJavaScript()
	 *
	 * @access private
	 */
	function _renderAJAXJavaScript()
	{
		jpimport('helpers.sajax', true);
		sajax_init();
		sajax_force_page_ajax();
		sajax_export('toggle','folderpane');
?>
<script language="JavaScript" type="text/javascript">
	/*
	 * (S)AJAX Library code
	 */
	 <?php sajax_show_javascript(); ?>
 	sajax_fail_handle = SAJAXTrap;

	function SAJAXTrap( myData ) {
		alert('Invalid AJAX reponse: ' + myData);
	}

	var globRoot = '';
	
	function toggle( fileName )
	{
		x_toggle( fileName, toggle_cb );
	}
	
	function toggle_cb( myRet )
	{
		folderpane( globRoot );
	}
		
	function folderpane( path )
	{
		globRoot = path;
		document.getElementById('currentdirectory').innerHTML = globRoot;
		x_folderpane( globRoot, folderpane_cb );
	}
	
	function folderpane_cb( myRet )
	{
		document.getElementById('folderpane').innerHTML = myRet;
	}
</script>
<?php
	}
	
	/**
	 * Output the non-AJAX version of JavaScript to the browser. It is meant to be used
	 * by renderJavaScript()
	 *
	 * @access private
	 */
	function _renderRedirectsJavaScript()
	{
		// At the moment, no JavaScript is necessary
	}
	
	/**
	 * Returns the HTML for the folder pane of the specified folder
	 *
	 * @param string $folder The folder for which to return a folder pane
	 */
	function getFolderPane($folder)
	{
		// Get the "backup method"
		jpimport('models.registry', true);
		
		$profile =& JoomlapackModelRegistry::getInstance();
		$method = $profile->get('backupMethod');
		
		$folder = trim($folder, DS);
		$folder = trim($folder, '/');
		
		// Load the model
		if(!class_exists('JoomlapackModelDef'))
		{
			jpimport('models.def',true);
		}
		$model = new JoomlapackModelDef();
		
		// Import Joomla! folder utility functions
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.path');
		
		// Construct the fully qualified pathname
		$folder = trim($folder, DS);
		$folder = trim($folder, '/');
		$path = JPATH_ROOT.DS.$folder;
		//$path = JFolder::makeSafe($path);
		if(!JFolder::exists($path))
		{
			return "<p>".JText::_('SFF_ERROR_INVALIDFOLDER');
		}
		
		// Get the subfolders of this folder
		$folders = JFolder::folders($path,'.',false,false,array('.','..'));
		
		// Calculate parent folder
		$thisPath = JPath::clean($path); // Cleanup current path
		$thisPath = rtrim($thisPath, DS); // Trim trailing slashes
		$lastDS = strrpos($thisPath, DS); // Find last occurence of DS
		$upPath = substr($thisPath,0,$lastDS); // Copy the part up to the previous directory
		$aboveRoot = strlen($upPath) < strlen(JPath::clean(JPATH_ROOT)); // We shouldn't go ABOVE the root! 
		$upPath = str_replace(JPath::clean(JPATH_ROOT),DS,$upPath);
		$upPath = DS.trim($upPath, DS);
				
		// Initialize output
		$txt_folders = JText::_('DEF_LABEL_SUBDIRECTORIES');
		$out = <<<ENDHTML
<table class="adminlist">
	<thead>
		<tr>
			<th>$txt_folders</th>
		</tr>
	</thead>
	<tbody>
ENDHTML;

			// Add a parent folder entry if it's not the root folder
		if(!$aboveRoot)
		{
			$urlbase = JURI::base().'/index.php?option=com_joomlapack&view=def&folder=';
			if($method == 'ajax')
			{
				$href = 'javascript:folderpane(\''.addslashes($upPath).'\');';
			}
			else
			{
				$href = $urlbase.htmlentities($upPath);
			}
			$htmlDir = JText::_('DEF_LABEL_GOUP');
			$out .= <<<ENDHTML
		<tr>
			<td>
				<a href="$href">$htmlDir</a>
			</td>
		</tr>
ENDHTML;
		}
		// Do we have files?
		if(empty($folders))
		{
			// No, warn user
			$out .= "<tr><td><p>".JText::_('DEF_ERROR_EMPTYDIR').'</p></td></tr>';
		}
		else
		{
			$imageURL = JURI::base().'/components/com_joomlapack/assets/images/arrow_small.png' ;
			$go_txt = JText::_('DEF_LABEL_TOGGLE');
			$urlbase = JURI::base().'/index.php?option=com_joomlapack&view=def&folder='.htmlentities($folder).'&task=toggle&item=' ;
			foreach($folders as $thisEntry)
			{
				$myPath = trim($folder.DS.$thisEntry, DS);
				if($method == 'ajax')
				{
					$href = 'javascript:toggle(\''.addslashes($myPath).'\');';
					$href2 = 'javascript:folderpane(\''.addslashes($myPath).'\');';
				}
				else
				{
					$href = $urlbase.htmlentities($thisEntry);
					$href2 = JURI::base().'/index.php?option=com_joomlapack&view=def&folder='.htmlentities($myPath);
				}
				$htmlFolder = htmlentities($thisEntry);
				// Make excluded files red and bold
				if($excluded = $model->isSetFor($myPath))
				{
					$style = 'style="color:red; font-weight: bold; text-decoration: none"';
					$out .= <<<ENDHTML
		<tr>
			<td>
				<a href="$href" style="color: green;">[ $go_txt<img src="$imageURL" width="10" height="10" border="0"> ]</a>
				<span $style>$htmlFolder</span>
			</td>
		</tr>
ENDHTML;
				}
				else
				{
					$style = 'style="text-decoration: none"';
					$out .= <<<ENDHTML
		<tr>
			<td>
				<a href="$href">[ $go_txt<img src="$imageURL" width="10" height="10" border="0"> ]</a>
				<a href="$href2" $style>$htmlFolder</a>
			</td>
		</tr>
ENDHTML;
				}
			}
		}
		
		$out .= <<<ENDHTML
	</tbody>
</table>
ENDHTML;

		return $out;		
	}
	
	/**
	 * Outputs the folder and file panes to the browser
	 *
	 */
	function renderPageArea()
	{
		// Get the "backup method"
		jpimport('models.registry', true);
		
		$profile =& JoomlapackModelRegistry::getInstance();
		$method = $profile->get('backupMethod');
		
		// Decide what the panes' initial content is going to be
		if($method == 'ajax')
		{
			$folderContent = "&nbsp;";
			$fileContent = "&nbsp;";
			$folder = '/';
		}
		else
		{
			$folder = JRequest::getVar('folder','');
			$folderContent = JoomlapackHelperDef::getFolderPane($folder);
		}
		
		// Render the table
?>
<h4>
	<?php echo JText::_('DEF_LABEL_CURRENTDIRECTORY'); ?>
	<span id="currentdirectory"><?php echo $folder; ?></span>
</h4>
<table border="0" cellspacing="10" width="100%">
<tr>
	<td id="folderpane" valign="top">
		<?php echo $folderContent; ?>
	</td>
</tr>
</table>
<?php
		// If we are on AJAX mode, include a startup script to populate the panes
		if($method == 'ajax')
		{
?>
<script language="JavaScript" type="text/javascript">
	folderpane('/');
</script>
<?php
		}
	}
	
	/**
	 * Outputs the necessary JavaScript to the browser
	 *
	 */
	function renderJavaScript()
	{
		jpimport('models.registry', true);
		
		$profile =& JoomlapackModelRegistry::getInstance();
		$method = $profile->get('backupMethod');
		if($method == 'ajax')
		{
			JoomlapackHelperDef::_renderAJAXJavaScript();
		}
		else
		{
			JoomlapackHelperDef::_renderRedirectsJavaScript();
		}	
	}
}
