<?php
/**
 *
 * Update : Apr 24 09
 *
 * Forum Thread: http://codeigniter.com/forums/viewthread/111977
 *
 *
 * Extension to fix zip file's Folder structure
 *
 * Problem faced:
 * When zipping up a folder outside my root like
 * eg:

		$path = '/path/to/your/directory/';
		$this->zip->read_dir($path);
		$this->zip->download('my_backup.zip');

	The resulting zip file had the same structure
 	eg: my_backup.zip/path/to/your/directory/

	This extention allows me to get files from a deep
	folder and rename the structure in the Zip.

	Usage:
		$path = '/path/to/your/directory/';
		$folder_in_zip = "source-code";

		$this->zip->add_dir($folder_in_zip);  // Create folder in zip
		$this->zip->get_files_from_folder($path, $folder_in_zip);

		$this->zip->download('my_backup.zip');

	Resulting Zip:
	mybackup.zip/source-code/(contents and inner of $path)


 */
class MY_Zip extends CI_Zip
{

	function get_files_from_folder($directory, $put_into) {
			if ($handle = opendir($directory)) {
				while (false !== ($file = readdir($handle))) {
					if (is_file($directory.$file)) {
						$fileContents = file_get_contents($directory.$file);

						$this->add_data($put_into.$file, $fileContents);

					} elseif ($file != '.' and $file != '..' and is_dir($directory.$file)) {

						$this->add_dir($put_into.$file.'/');

						$this->get_files_from_folder($directory.$file.'/', $put_into.$file.'/');
					}
				}
			}
			closedir($handle);
	}



	/**
	 * Mod By : louis w (http://codeigniter.com/forums/member/59541)
	 *
	 * Purpose:
	 *
		"After testing this out, I didn’t like how the data inside the
		folder I was trying to add still needed to be inside a
		directory in the archive. I checked out the CI read_dir call,
		a couple small changes you can call read_dir to create an
		archive containing all the files without any directories. Even
		works with nested folders."

	 *
	 * @param string $path
	 * @param string $base
	 * @return Boolean
	 */

	 function read_dir($path, $base=null) {

        if (is_null($base)) $base = $path;

        if ($fp = @opendir($path)) {

            while (FALSE !== ($file = readdir($fp))) {

                if (@is_dir($path.$file) && substr($file, 0, 1) != '.') {
                    $this->read_dir($path.$file."/", $base);
                } else if (substr($file, 0, 1) != ".") {
                    if (FALSE !== ($data = file_get_contents($path.$file))) {
                        $file_name = ltrim($path.$file, $base);
                        $this->add_data($file_name, $data);
                    }
                }
            }

            return TRUE;

        }
    }

	/**
	 * Mod by : John Christos
	 * Add Data to Zip With Dates To Correct no timestamp when
	 * extracted by winrar and stuffIt.
	 *
	 * The extracted files with no timestamp crashed sourceDiff & Notepad++
	 * on my system.
	 *
	 * This fix forces the files to have a timestamp.
	 *
	 * @access	private
	 * @param	string	the file name/path
	 * @param	string	the data to be encoded
	 * @return	void
	 */
	function _add_data($filepath, $data, $time = 0)
	{
		$filepath = str_replace("\\", "/", $filepath);

		// Create TimeStamp
		$dtime    = dechex($this->unix2DosTime($time));

		$hexdtime = '\x' . $dtime[6] . $dtime[7]
                  . '\x' . $dtime[4] . $dtime[5]
                  . '\x' . $dtime[2] . $dtime[3]
                  . '\x' . $dtime[0] . $dtime[1];

        eval('$hexdtime = "' . $hexdtime . '";');

        // At this point the Timestamp is in $hexdtime;



		$uncompressed_size = strlen($data);
		$crc32  = crc32($data);

		$gzdata = gzcompress($data);
		$gzdata = substr($gzdata, 2, -4);
		$compressed_size = strlen($gzdata);

	$this->zipdata .=
			"\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00" . $hexdtime
			.pack('V', $crc32)
			.pack('V', $compressed_size)
			.pack('V', $uncompressed_size)
			.pack('v', strlen($filepath)) // length of filename
			.pack('v', 0) // extra field length
			.$filepath
			.$gzdata; // "file data" segment



		$this->directory .=
			"\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00\x00\x00\x00\x00"
			.pack('V', $crc32)
			.pack('V', $compressed_size)
			.pack('V', $uncompressed_size)
			.pack('v', strlen($filepath)) // length of filename
			.pack('v', 0) // extra field length
			.pack('v', 0) // file comment length
			.pack('v', 0) // disk number start
			.pack('v', 0) // internal file attributes
			.pack('V', 32) // external file attributes - 'archive' bit set
			.pack('V', $this->offset) // relative offset of local header
			.$filepath;

		$this->offset = strlen($this->zipdata);
		$this->entries++;
		$this->file_num++;
	}

	// --------------------------------------------------------------------


	/**
	 * (This function is borrowed from http://drupal.org/node/83253)
     * Converts an Unix timestamp to a four byte DOS date and time format (date
     * in high two bytes, time in low two bytes allowing magnitude comparison).
     *
     * @param  integer  the current Unix timestamp
     *
     * @return integer  the current date in a four byte DOS format
     *
     * @access private
     */
    function unix2DosTime($unixtime = 0) {
        $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);

        if ($timearray['year'] < 1980) {
            $timearray['year']    = 1980;
            $timearray['mon']     = 1;
            $timearray['mday']    = 1;
            $timearray['hours']   = 0;
            $timearray['minutes'] = 0;
            $timearray['seconds'] = 0;
        } // end if

        return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
                ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
    } // end of the 'unix2DosTime()' method


}
?>
