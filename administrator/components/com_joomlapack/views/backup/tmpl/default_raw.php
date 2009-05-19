<?php
/**
 * @package JoomlaPack
 * @copyright Copyright (c)2006-2008 JoomlaPack Developers
 * @license GNU General Public License version 2, or later
 * @version $id$
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

jpimport('helpers.sajax',true);
sajax_init();
sajax_force_page_ajax('backup');
sajax_export('start', 'tick', 'renderProgress');
$null = null;
sajax_handle_client_request( $null );

function start($description, $comment)
{
	jpimport('core.cube');
	JoomlapackCUBE::reset();
	$cube =& JoomlapackCUBE::getInstance();
	$cube->start($description, $comment);
	$cube->save();
	return _processCUBE();	
}

function tick()
{
	jpimport('core.cube');
	$cube =& JoomlapackCUBE::getInstance();
	$cube->tick();
	$cube->save();
	return _processCUBE();
}

function renderProgress()
{
	jpimport('core.cube');
	jpimport('helpers.backup', true);
	
	$cube =& JoomlapackCUBE::getInstance();
	$array = $cube->getCUBEArray();
	return JoomlapackHelperBackup::getBackupProcessHTML($array);
}

function _processCUBE()
{
	jpimport('core.cube');
	$cube =& JoomlapackCUBE::getInstance();
	$array = $cube->getCUBEArray();
	
	if($array['Error'] != '')
	{
		$action = 'error';
		$message = $array['Error'];
	}
	elseif($array['HasRun'] == 1)
	{
		$action = 'finished';
		$message = '';
	}
	else
	{
		$action = 'step';
		$message = '';
	}

	return array($action, $message);
}