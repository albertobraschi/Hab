<?php
/**
 * @package JoomlaPack
 * @copyright Copyright (c)2006-2008 JoomlaPack Developers
 * @license GNU General Public License version 2, or later
 * @version $id$
 * @since 2.1
 * 
 * Configuration page for Easy Mode
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

jpimport('helpers.sajax', true);
sajax_init();
sajax_force_page_ajax();
// Hack our AJAX library so that it uses the view=config instead of view=configeasy
global $sajax_remote_uri_params;
$sajax_remote_uri_params = "option=com_joomlapack&view=config&format=raw";
sajax_export('getDefaultOutputDirectory');

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

function getDefaultOutputDirectory()
{
	x_getDefaultOutputDirectory( getDefaultOutputDirectory_cb );
}

function getDefaultOutputDirectory_cb( myRet )
{
	document.getElementById("outdir").value = myRet;
} 
</script>

<form name="adminForm" id="adminForm">
<input type="hidden" name="option" value="com_joomlapack" />
<input type="hidden" name="view" value="configeasy" />
<input type="hidden" name="task" value="" />

<table cellpadding="4" cellspacing="0" border="0" width="95%" class="adminform">
	<tr align="center" valign="middle">
		<th width="20%">&nbsp;</th>
		<th width="20%"><?php echo JText::_('CONFIG_OPTION'); ?></th>
		<th width="60%"><?php echo JText::_('CONFIG_CURSETTINGS'); ?></th>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><?php echo JText::_('CONFIG_LABEL_OUTPUTDIRECTORY'); ?></td>
		<td><input type="text" name="var[OutputDirectory]" id="outdir" size="40" value="<?php echo $this->OutputDirectory; ?>" />
		<input type="button" value="<?php echo JText::_('CONFIG_ACTION_DEFAULTDIR'); ?>" onclick="getDefaultOutputDirectory();" />
		</td>
	</tr>
	<?php JoomlapackHelperConfig::renderEditBoxRow('CONFIG_LABEL_ARCHIVENAME', 'TarNameTemplate', $this->TarNameTemplate); ?>
	<?php JoomlapackHelperConfig::renderSelectionBoxRow('CONFIGEZ_LABEL_SETTINGSMODE', 'settingsmode', $this->settingsmode, $this->settingsmodelist); ?>
	<?php JoomlapackHelperConfig::renderSelectionBoxRow('CONFIG_LABEL_LOGLEVEL', 'logLevel', $this->logLevel, $this->actionlogginglist); ?>	
</table>

</form>
<?php echo JoomlapackHelperUtils::getFooter(); ?>