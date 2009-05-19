<?php
/**
 * @package JoomlaPack
 * @copyright Copyright (c)2006-2008 JoomlaPack Developers
 * @license GNU General Public License version 2, or later
 * @version $id$
 * @since 1.3
 * 
 * The main page of the JoomlaPack component is where all the fun takes place :)
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

jpimport('helpers.sajax', true);
sajax_init();
sajax_export('getDefaultOutputDirectory');
$null = null;
sajax_handle_client_request( $null );

function getDefaultOutputDirectory()
{
	return JPATH_COMPONENT_ADMINISTRATOR.DS.'backup';
}