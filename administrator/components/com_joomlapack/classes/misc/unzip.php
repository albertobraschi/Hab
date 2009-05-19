<?php
// ==========================================================================================
// KS: Simple Unzip Class
// ==========================================================================================

class CSimpleUnzip
{
	/**
	 * Absolute file name and path of the ZIP file
	 *
	 * @var string
	 */
	var $_filename = '';
	
	/**
	 * Read only file pointer to the ZIP file
	 *
	 * @var unknown_type
	 */
	var $_filepointer = false;
	
	/**
	 * Did we encounter an error?
	 *
	 * @var boolean
	 */
	var $_isError = false;
	
	/**
	 * Error description
	 *
	 * @var string
	 */
	var $_error = '';
	
	/**
	 * Creates the CSimpleUnzip object and tries to open the file specified in $filename
	 *
	 * @param string $filename Absolute path and file name of the ZIP file to open
	 * @return CSimpleUnzip
	 */
	function CSimpleUnzip( $filename )
	{
		// Set the stored filename to the defined parameter
		$this->_filename = $filename;
		
		// Try opening the file
		$this->_filepointer = $this->_getFP();
	}
	
	/**
	 * Returns a file pointer to the ZIP file, or tries to open the file.
	 *
	 * @return integer|boolean File pointer or FALSE when there's an error
	 * @access private
	 */
	function _getFP()
	{
		// If the file is open, return the existing file pointer
		if ( $this->_filepointer !== FALSE ) return $this->_filepointer;
		
		if( file_exists($this->_filename) )
		{
			$fp = @fopen( $this->_filename, 'r');
			if( $fp === false )
			{
				$this->_error = "Could not open " . $this->_filename . " for reading. Check permissions?";
				$this->_isError = true;
				return false; 
			} else {
				$this->_filepointer = $fp;
				return $this->_filepointer;
			}
		} else {
			$this->_error = "File " . $this->_filename . " does not exist.";
			$this->_isError = true;
			return false;
		}
	}
	
	/**
	 * Returns the error message for the current error state, or FALSE if no error has occured
	 *
	 * @return string|boolean Error message or FALSE for no errors
	 */
	function getError()
	{
		if( !$this->_isError ) {
			return false;
		} else {
			return $this->_error;
		}
	}
	
	/**
	 * Extracts a file from the ZIP archive
	 *
	 * @param integer $offset The offset to start extracting from. If ommited, or set to null,
	 * it continues from the ZIP file's current location. 
	 * @return array|boolean A return array or FALSE if an error occured
	 */
	function ExtractFile( $offset = null, $addPath='' )
	{
		// Generate a return array
		$retArray = array(
			"file"				=> '',		// File name extracted
			"compressed"		=> 0,		// Compressed size
			"uncompressed"		=> 0,		// Uncompressed size
			"type"				=> "file",	// File type (file | dir)
			"zipoffset"			=> 0,		// Offset in ZIP file
			"done"				=> false	// Are we done with extracting files?
		);
		
		// Get file pointer to the ZIP file
		$fp = $this->_getFP();
		
		// If we can't open the file, return an error condition
		if( $fp === false ) return false;
		
		// Go to the offset specified, if specified. Otherwise, it will continue reading from current position
		if( !is_null($offset) )	fseek( $fp, $offset );
				
		// Get and decode Local File Header
		$headerBinary = fread($fp, 30);
		$headerData = unpack('Vsig/C2ver/vbitflag/vcompmethod/vlastmodtime/vlastmoddate/Vcrc/Vcompsize/Vuncomp/vfnamelen/veflen', $headerBinary);
		// Check signature
		if( $headerData['sig'] == 0x04034b50 )
		{
			// This is a file header. Get basic parameters.
			$retArray['compressed']		= $headerData['compsize'];
			$retArray['uncompressed']	= $headerData['uncomp'];
			$nameFieldLength			= $headerData['fnamelen'];
			$extraFieldLength			= $headerData['eflen'];

			// Read filename field
			$retArray['file']			= fread( $fp, $nameFieldLength );
			
			// .htaccess handling magic!
			if($retArray['file'] == 'htaccess.bak') $retArray['file']='htaccess.bak~';
			if($retArray['file'] == '.htaccess') $retArray['file']='htaccess.bak';
			
			// Read extra field if present
			if($extraFieldLength > 0) $extrafield = fread( $fp, $extraFieldLength );
			
			if( strrpos($retArray['file'], '/') == strlen($retArray['file']) - 1 ) $retArray['type'] = 'dir';
			
			// Do we need to create the directory?
			if(strpos($retArray['file'], '/') > 0) {
				$lastSlash = strrpos($retArray['file'], '/');
				$dirName = substr( $retArray['file'], 0, $lastSlash);
				if( $this->_createDirRecursive($addPath.$dirName) == false ) {
					$this->_isError = true;
					$this->_error = "Could not create $dirName folder";
					return false; 
				}	
			}
			
			if( $headerData['compmethod'] == 8 )
			{
				// DEFLATE compression
			
				$zipData = fread( $fp, $retArray['compressed'] );
				$unzipData = gzinflate( $zipData );
				unset($zipData);
				// Try writing to the output file
				$outfp = @fopen( $addPath.$retArray['file'], 'w' );						
				
				if( $outfp === false ) {
					// An error occured
					$this->_isError = true;
					$this->_error = "Could not open " . $retArray['file'] . " for writing.";
					return false; 
				} else {
					// No error occured. Write to the file.
					fwrite( $outfp, $unzipData, $retArray['uncompressed'] );
					fclose( $outfp );
				}
				unset($unzipData);
			} else {
				if( $retArray['type'] == "file" )
				{
					// No compression
					if( $retArray['uncompressed'] > 0 )
					{
						$outfp = @fopen( $addPath.$retArray['file'], 'w' );						
						if( $outfp === false ) {
							// An error occured
							$this->_isError = true;
							$this->_error = "Could not open " . $retArray['file'] . " for writing.";
							return false; 
						} else {
							$readBytes = 0;
							$toReadBytes = 0;
							$leftBytes = $retArray['compressed'];
							
							while( $leftBytes > 0)
							{
								$toReadBytes = ($leftBytes > 102476) ? 102476 : $leftBytes;
								$leftBytes -= $toReadBytes;
								$data = fread( $fp, $toReadBytes );
								fwrite( $outfp, $data );
							}
							fclose($outfp);
						}
					} else {
						// 0 byte file, just touch it
						$outfp = @fopen( $addPath.$retArray['file'], 'w' );						
						if( $outfp === false ) {
							// An error occured
							$this->_isError = true;
							$this->_error = "Could not open " . $retArray['file'] . " for writing.";
							return false; 
						} else {
							fclose($outfp);
						}
					}
				} else {
					$result = $this->_createDirRecursive($dirName);
				}
			}
			
			$retArray['zipoffset'] = ftell( $fp ); 
			return $retArray;
		} else {
			// This is not a file header. This means we are done.
			$retArray['done'] = true;
			return $retArray;
		}
	}
	
	/**
	 * Tries to recursively create the directory $dirName
	 *
	 * @param string $dirName The directory to create 
	 * @return boolean TRUE on success, FALSE on failure
	 * @access private
	 */
	function _createDirRecursive( $dirName )
	{
		$dirArray = explode('/', $dirName);
		$path = '';
		foreach( $dirArray as $dir )
		{
			$path .= $dir . '/';
			$ret = is_dir($path) ? true : @mkdir($path);
			if( !ret ) {
				$this->_isError = true;
				$this->_error = "Could not create $path folder.";
				return false;
			}
		}
		return true;
	}
}
?>