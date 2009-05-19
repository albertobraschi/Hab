<?php
/**
 * @package JoomlaPack
 * @version $id$
 * @license GNU General Public License, version 2 or later
 * @author JoomlaPack Developers
 * @copyright Copyright 2006-2008 JoomlaPack Developers
 * @since 1.3
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Algorithm runner
 * 
 * Provides tick() functionality with different algorithms based on domain names
 *
 */
class JoomlapackCUBEAlgorunner extends JObject 
{
	/**
	 * Current domain reported by part
	 *
	 * @var string
	 */
	var $currentDomain;
	
	/**
	 * Current step reported by part
	 *
	 * @var string
	 */
	var $currentStep;
	
	/**
	 * Current substep reported by part
	 *
	 * @var string
	 */
	var $currentSubstep;
	
	/**
	 * Allowed parameters for algorithm
	 *
	 * @var array
	 */
	var $_allowedAlgorithms = array('multi', 'smart');
	
	/**
	 * Public constructor
	 *
	 */
	function __construct()
	{
		parent::__construct();

		// Import Smart algorithm magic numbers
		if(!class_exists('JoomlapackModelRegistry'))
		{
			jpimport('models.registry', true);
		}
		$configuration =& JoomlapackModelRegistry::getInstance();
		if(!defined('mnMaxExecTimeAllowed'))	define('mnMaxExecTimeAllowed',	$configuration->get('mnMaxExecTimeAllowed'));
		if(!defined('mnMinimumExectime'))		define('mnMinimumExectime',		$configuration->get('mnMinimumExectime'));
		if(!defined('mnExectimeBiasPercent'))	define('mnExectimeBiasPercent',	$configuration->get('mnExectimeBiasPercent')/100);
		if(!defined('mnMaxOpsPerStep'))			define('mnMaxOpsPerStep',		$configuration->get('mnMaxOpsPerStep'));
		unset($configuration);
	}
	
	/**
	 * Selects the algorithm to use based on the domain name
	 *
	 * @param string $domain The domain to return algorithm for
	 * @return string The algorithm to use
	 */
	function selectAlgorithm( $domain ){
		if(!class_exists('JoomlapackModelregistry'))
		{
			jpimport('models.registry', true);
		}
		$registry =& JoomlapackModelRegistry::getInstance();
		
		switch( $domain )
		{
			case "installer":
				switch ($registry->get('BackupType')) {
					case 'full':
						return 'smart';
						break;
					
					default:
						return '(null)';
						break;
				}
				break;
				
			case "PackDB":
				return $registry->get('dbAlgorithm');
				break;
			
			case "Packing":
				switch ($registry->get('BackupType')) {
					case 'full':
						return $registry->get('packAlgorithm');
						break;
					
					default:
						return '(null)';
						break;
				}
				break;
			
			default:
				return "(null)";
				break;
				
		}
	}

	/**
	 * Runs the user-selected algorithm for stepping a CUBE part
	 *
	 * @param string $algorithm multi|smart The selected algorithm
	 * @param JoomlapackCUBEParts $object The CUBE part to step
	 * @return integer 0 if more work is required, 1 if we finished, 2 on error
	 */
	function runAlgorithm( $algorithm, &$object ){
		if(!is_object($object) && (in_array($algorithm, $this->_allowedAlgorithms)))
		{
			$this->setError(__CLASS__.':: $object is not an object');
			return 2;
		}
		
		// Catch error conditions
		if($object->getError())
		{
			$this->setError($object->getError());
			return 2;
		}
		
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "Using $algorithm algorithm for ".get_class($object) );

		switch( $algorithm ){
			case "slow":
				// Multi-step algorithm - slow but most compatible
				return $this->_algoMultiStep( $object );
				break;
			case "smart":
				// SmartStep algorithm - best compromise between speed and compatibility
				return $this->_algoSmartStep( $object );
				break;
			default:
				// No algorithm (null algorithm) for "init" and "finale" domains. Always returns success.
				return 1;
		} // switch
	}
	
	/**
	 * Multi-step algorithm. Runs the tick() function of the $object once and returns.
	 *
	 * @param JoomlapackCUBEParts $object The CUBE part to step
	 * @return integer
	 * @see runAlgorithm()
	 */
	function _algoMultiStep( &$object )
	{
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "Multiple Stepping (Slow algorithm)");
		
		// Catch potential errors
		if($object->getError())
		{
			$this->setError($object->getError());
			return 2;
		}

		$result =$object->tick();
		
		$this->currentDomain = $result['Domain'];
		$this->currentStep = $result['Step'];
		$this->currentSubstep = $result['Substep'];
		
		// Catch any errors
		
		if($object->getError())
		{
			$error = true;
			$this->setError($object->getError());
		}
		else
		{
			$error = false;
		}
		$finished = $error ? true : !($result['HasRun']);

		if (!$error) {
			JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "Successful Slow algorithm on ".get_class($object));
		} else {
			JoomlapackLogger::WriteLog(_JP_LOG_ERROR, "Failed Slow algorithm on ".get_class($object));
		}
		
		// @todo Warnings propagation
		
		return $error ? 2 : ( $finished ? 1 : 0 );
	}

	/**
	* Smart step algorithm. Runs the tick() function until we have consumed 75%
	* of the maximum_execution_time (minus 1 seconds) within this procedure. If
	* the available time is less than 1 seconds, it defaults to multi-step.
	* @param JoomlapackCUBEParts $object The CUBE part to step
	* @return integer 0 if more work is to be done, 1 if we finished correctly,
	* 2 if error eccured.
	* @access private
	*/
	function _algoSmartStep( &$object )
	{
		JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "Smart Stepping");

		// Get the maximum execution time
		$maxExecTime = ini_get("maximum_execution_time");
		$startTime = $this->_microtime_float();
		if ( ($maxExecTime == "") || ($maxExecTime == 0) ) {
			// If we have no time limit, set a hard limit of about 10 seconds
			// (safe for Apache and IIS timeouts, verbose enough for users)
			$maxExecTime = 14;
		}

		if ( $maxExecTime <= mnMinimumExectime ) {
			// If the available time is less than the trigger value, switch to
			// multi-step
			return $this->_algoMultiStep($object);
		} else {
			// All checks passes, this is a SmartStep-enabled case
			$maxRunTime = ($maxExecTime - 1) * mnExectimeBiasPercent;
			$maxRunTime = max(array(mnMaxExecTimeAllowed, $maxRunTime));
			$runTime = 0;
			$finished = false;
			$error = false;

			$opsRemaining = max(1, mnMaxOpsPerStep); // Run at least one step, even if mnMaxOpsPerStep=0
			
			// Loop until time's up, we're done or an error occured
			while( ($runTime <= $maxRunTime) && (!$finished) && (!$error) && ($opsRemaining > 0) ){
				$opsRemaining--; // Decrease the number of possible available operations count
				$result = $object->tick();
				$this->currentDomain = $result['Domain'];
				$this->currentStep = $result['Step'];
				$this->currentSubstep = $result['Substep'];
				$error = false;
				if($object->getError())
				{
					$error = true;
					$this->setError($object->getError());
					$result['Error'] = $this->getError();
				}
				$finished = $error ? true : !($result['HasRun']);

				$endTime = $this->_microtime_float();
				$runTime = $endTime - $startTime;
				//JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "--- Running time: ".$runTime);
			} // while

			// Return the result
			if (!$error) {
				JoomlapackLogger::WriteLog(_JP_LOG_DEBUG, "Successful Smart algorithm on ".get_class($object));
			} else {
				JoomlapackLogger::WriteLog(_JP_LOG_ERROR, "Failed Smart algorithm on ".get_class($object));
			}
			
			// @todo Warnings propagation
		
			return $error ? 2 : ( $finished ? 1 : 0 );
		}
	}
	
	function _microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}