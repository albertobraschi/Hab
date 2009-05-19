<?php
/**
 * JoomlaPack Archive extraction class
 * Implements the JoomlaPack Archive format version 1.0
 */
class CUnJPA
{
	/**
	 * File pointer of the archive opened
	 * @var integer
	 */
	var $_fp = FALSE;
	
	/**
	 * Absolute pathname to archive being operated upon
	 * @var string
	 */
	var $_filename;

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
	 * Data read from archive's header
	 * @var array
	 */
	var $headerData = array();
	
	/**
	 * The last offset in the archive file we read data from
	 *
	 * @var unknown_type
	 */
	var $lastOffset = 0;
	
	/**
	 * Opens a JPA archive for reading
	 * @param string $filename The absolute pathname to the file being operated upon
	 * @return CUnJPA
	 */
	function CUnJPA( $filename )
	{
		// Store the filename
		$this->_filename = $filename;
		
		// Try opening the file
		$this->_fp = $this->_getFP();
		
		// Read the header
		$this->_ReadHeader();
	}
	
	function Close()
	{
		@fclose( $this->_fp );
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
			"compression"		=> "none",	// Compression type (none | gzip | bzip2)
			"archiveoffset"		=> 0,		// Offset in ZIP file
			"permissions"		=> 0,		// UNIX permissions stored in the archive
			"done"				=> false	// Are we done with extracting files?
		);
		
		$offset = $offset == 0 ? null : $offset;
		
		// Get file pointer to the ZIP file
		$fp = $this->_getFP();
		
		// If we can't open the file, return an error condition
		if( $fp === false ) return false;
		
		// Go to the offset specified, if specified. Otherwise, it will continue reading from current position
		if( is_null($offset) ) $offset = $this->lastOffset;
		if( !is_null($offset) )	fseek( $fp, $offset );
				
		// Get and decode Entity Description Block
		$signature = fread($fp, 3);
		
		// Check signature
		if( $signature == 'JPF' )
		{
			// This a JPA Entity Block. Process the header.
			
			// Read length of EDB and of the Entity Path Data
			$length_array = unpack('vblocksize/vpathsize', fread($fp, 4));
			// Read the path data
			$file = fread( $fp, $length_array['pathsize'] );
			
			// .htaccess handling magic!
			if($file == 'htaccess.bak') $file='htaccess.bak~';
			if($file == '.htaccess') $file='htaccess.bak';
			
			// Read and parse the known data portion
			$bin_data = fread( $fp, 14 );
			$header_data = unpack('Ctype/Ccompression/Vcompsize/Vuncompsize/Vperms', $bin_data);
			// Read any unknwon data
			$restBytes = $length_array['blocksize'] - (21 + $length_array['pathsize']);
			if( $restBytes > 0 ) $junk = fread($fp, $restBytes);
			
			$compressionType = $header_data['compression'];
			
			// Populate the return array
			$retArray['file'] = $file;
			$retArray['compressed'] = $header_data['compsize'];
			$retArray['uncompressed'] = $header_data['uncompsize'];
			$retArray['type'] = ($header_data['type'] == 0 ? "dir" : "file");
			switch( $compressionType )
			{
				case 0:
					$retArray['compression'] = 'none';
					break;
				case 1:
					$retArray['compression'] = 'gzip';
					break;
				case 2:
					$retArray['compression'] = 'bzip2';
					break;
			}
			$retArray['permissions'] = $header_data['perms'];
			
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
			
			switch( $retArray['type'] )
			{
				case "dir":
					$result = $this->_createDirRecursive($addPath.$dirName);
					if( !$result ) {
						return false;
					}
					break;
					
				case "file":
					switch( $compressionType )
					{
						case 0: // No compression
							$outfp = @fopen( $addPath.$retArray['file'], 'w' );						
							if( $outfp === false ) {
								// An error occured
								$this->_isError = true;
								$this->_error = "Could not open " . $retArray['file'] . " for writing.";
								return false;
							} 
							
							if( $retArray['uncompressed'] > 0 )
							{

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
							}
							
							fclose($outfp);
							
							break;
							
						case 1: // GZip compression
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
							}
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
							break;
							
						case 2: // BZip2 compression
							$zipData = fread( $fp, $retArray['compressed'] );
							$unzipData = bzdecompress( $zipData );
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
							break;
					}
					break;
			}

			$retArray['archiveoffset'] = ftell( $fp );
			$this->lastOffset = $retArray['archiveoffset']; 
			return $retArray;
		} else {
			// This is not a file header. This means we are done.
			$retArray['done'] = true;
			return $retArray;
		}
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
		if ( $this->_fp !== FALSE ) return $this->_fp;
		
		if( file_exists($this->_filename) )
		{
			$fp = @fopen( $this->_filename, 'r');
			if( $fp === false )
			{
				$this->_error = "Could not open " . $this->_filename . " for reading. Check permissions?";
				$this->_isError = true;
				return false; 
			} else {
				$this->_fp = $fp;
				return $this->_fp;
			}
		} else {
			$this->_error = "File " . $this->_filename . " does not exist.";
			$this->_isError = true;
			return false;
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
	
	/**
	 * Reads the files header
	 * @access private
	 * @return boolean TRUE on success
	 */
	function _ReadHeader()
	{
		// Initialize header data array
		$this->headerData = array();
		
		// Get filepointer
		$fp = $this->_getFP();
		
		// Fail for unreadable files
		if( $fp === false ) return false;

		// Go to the beggining of the file
		rewind( $fp );
		
		// Read the signature
		$sig = fread( $fp, 3 );
		
		if ($sig != 'JPA') return false; // Not a JoomlaPack Archive?
		
		// Read and parse header length
		$header_length_array = unpack( 'v', fread( $fp, 2 ) );
		$header_length = $header_length_array[1];
		
		// Read and parse the known portion of header data (14 bytes)
		$bin_data = fread($fp, 14);
		$header_data = unpack('Cmajor/Cminor/Vcount/Vuncsize/Vcsize', $bin_data);
		
		// Load any remaining header data (forward compatibility)
		$rest_length = $header_length - 19;
		if( $rest_length > 0 ) $junk = fread($fp, $rest_length);
		
		$this->headerData = array(
			'signature' => 			$sig,
			'length' => 			$header_length,
			'major' => 				$header_data['major'],
			'minor' => 				$header_data['minor'],
			'filecount' => 			$header_data['count'],
			'uncompressedsize' => 	$header_data['uncsize'],
			'compressedsize' => 	$header_data['csize'],
			'unknowndata' => 		$junk
		);
		
		$this->lastOffset = ftell( $fp );
		
		return true;
	}

}
?>