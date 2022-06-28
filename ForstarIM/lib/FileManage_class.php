<?php
class FileManagement
{  
	#---------------------------------------------------------------------------|-
	# Desc: Handle file management related functions								|-
	#---------------------------------------------------------------------------|-
	var $databaseConnect; // used to store database connection reference object
    
	/**
	* constructor
	**/
	function FileManagement(&$databaseConnect)
    {
        $this->databaseConnect =&$databaseConnect; // set the database reference object
    }
	
	/**
	* Desc: Return Mime Type
	* @param $fileExt:  File Extension
	* return value: return mime type of a file, return blank otherwise 
	**/
	function getMimeType($fileExt)
	{
		// Exension to MIME-Type mapping, pretty much the same as mime.types from Apache
		$mimetypes['fla']	= 'application/application/x-shockwave-flash';
		$mimetypes["ez"]	= "application/andrew-inset";
		$mimetypes["csm"]	= "application/cu-seeme";
		$mimetypes["cu"]	= "application/cu-seeme";
		$mimetypes["xls"]	= "application/vnd.ms-excel";
		$mimetypes["hqx"]	= "application/mac-binhex40";
		$mimetypes["cpt"]	= "application/mac-compactpro";
		$mimetypes["doc"]	= "application/msword";
		$mimetypes["dot"]	= "application/msword";
		$mimetypes["wrd"]	= "application/msword";
		$mimetypes["bin"]	= "application/octet-stream";
		$mimetypes["dms"]	= "application/octet-stream";
		$mimetypes["lha"]	= "application/octet-stream";
		$mimetypes["lzh"]	= "application/octet-stream";
		$mimetypes["exe"]	= "application/octet-stream";
		$mimetypes["class"] = "application/octet-stream";
		$mimetypes["oda"]	= "application/oda";
		$mimetypes["pdf"]	= "application/pdf";
		$mimetypes["pgp"]	= "application/pgp";
		$mimetypes["ai"]	= "application/postscript";
		$mimetypes["eps"]	= "application/postscript";
		$mimetypes["ps"]	= "application/postscript";
		$mimetypes["ppt"]	= "application/vnd.ms-powerpoint";
		$mimetypes["rtf"]	= "application/rtf";
		$mimetypes["wp5"]	= "application/wordperfect5.1";
		$mimetypes["wk"]	= "application/x-123";
		$mimetypes["wz"]	= "application/x-Wingz";
		$mimetypes["bcpio"] = "application/x-bcpio";
		$mimetypes["vcd"]	= "application/x-cdlink";
		$mimetypes["pgn"]	= "application/x-chess-pgn";
		$mimetypes["z"]		= "application/x-compress";
		$mimetypes["Z"]		= "application/x-compress";
		$mimetypes["cpio"]	= "application/x-cpio";
		$mimetypes["csh"]	= "application/x-csh";
		$mimetypes["deb"]	= "application/x-debian-package";
		$mimetypes["dcr"]	= "application/x-director";
		$mimetypes["dir"]	= "application/x-director";
		$mimetypes["dxr"]	= "application/x-director";
		$mimetypes["dvi"]	= "application/x-dvi";
		$mimetypes["gtar"]	= "application/x-gtar";
		$mimetypes["tgz"]	= "application/x-gtar";
		$mimetypes["gz"]	= "application/x-gzip";
		$mimetypes["hdf"]	= "application/x-hdf";
		$mimetypes["phtml"] = "application/x-httpd-php";
		$mimetypes["pht"]	= "application/x-httpd-php";
		$mimetypes["php"]	= "application/x-httpd-php";
		$mimetypes["js"]	= "application/x-javascript";
		$mimetypes["skp"]	= "application/x-koan";
		$mimetypes["skd"]	= "application/x-koan";
		$mimetypes["skt"]	= "application/x-koan";
		$mimetypes["skm"]	= "application/x-koan";
		$mimetypes["latex"] = "application/x-latex";
		$mimetypes["frm"]	= "application/x-maker";
		$mimetypes["maker"] = "application/x-maker";
		$mimetypes["frame"] = "application/x-maker";
		$mimetypes["fm"]	= "application/x-maker";
		$mimetypes["fb"]	= "application/x-maker";
		$mimetypes["book"]	= "application/x-maker";
		$mimetypes["fbdoc"] = "application/x-maker";
		$mimetypes["mif"]	= "application/x-mif";
		$mimetypes["com"]	= "application/x-msdos-program";
		$mimetypes["exe"]	= "application/x-msdos-program";
		$mimetypes["bat"]	= "application/x-msdos-program";
		$mimetypes["nc"]	= "application/x-netcdf";
		$mimetypes["cdf"]	= "application/x-netcdf";
		$mimetypes["pac"]	= "application/x-ns-proxy-autoconfig";
		$mimetypes["pl"]	= "application/x-perl";
		$mimetypes["pm"]	= "application/x-perl";
		$mimetypes["sh"]	= "application/x-sh";
		$mimetypes["shar"]	= "application/x-shar";
		$mimetypes["sit"]	= "application/x-stuffit";
		$mimetypes["sv4cpio"] = "application/x-sv4cpio";
		$mimetypes["sv4crc"] = "application/x-sv4crc";
		$mimetypes["tar"]	= "application/x-tar";
		$mimetypes["tcl"]	= "application/x-tcl";
		$mimetypes["tex"]	= "application/x-tex";
		$mimetypes["texinfo"] = "application/x-texinfo";
		$mimetypes["texi"]	= "application/x-texinfo";
		$mimetypes["t"]		= "application/x-troff";
		$mimetypes["tr"]	= "application/x-troff";
		$mimetypes["roff"]	= "application/x-troff";
		$mimetypes["man"]	= "application/x-troff-man";
		$mimetypes["me"]	= "application/x-troff-me";
		$mimetypes["ms"]	= "application/x-troff-ms";
		$mimetypes["ustar"] = "application/x-ustar";
		$mimetypes["src"]	= "application/x-wais-source";
		$mimetypes["zip"]	= "application/zip";
		$mimetypes["au"]	= "audio/basic";
		$mimetypes["snd"]	= "audio/basic";
		$mimetypes["mid"]	= "audio/midi";
		$mimetypes["midi"]	= "audio/midi";
		$mimetypes["kar"]	= "audio/midi";
		$mimetypes["mpga"]	= "audio/mpeg";
		$mimetypes["mp2"]	= "audio/mpeg";
		$mimetypes["mp3"]	= "audio/mpeg";
		$mimetypes["aif"]	= "audio/x-aiff";
		$mimetypes["aifc"]	= "audio/x-aiff";
		$mimetypes["aiff"]	= "audio/x-aiff";
		$mimetypes["ram"]	= "audio/x-pn-realaudio";
		$mimetypes["ra"]	= "audio/x-realaudio";
		$mimetypes["wav"]	= "audio/x-wav";
		$mimetypes["pdb"]	= "chemical/x-pdb";
		$mimetypes["xyz"]	= "chemical/x-pdb";
		$mimetypes["gif"]	= "image/gif";
		$mimetypes["ief"]	= "image/ief";
		$mimetypes["jpeg"]	= "image/jpeg";
		$mimetypes["jpg"]	= "image/jpeg";
		$mimetypes["jpe"]	= "image/jpeg";
		$mimetypes["png"]	= "image/png";
		$mimetypes["tiff"]	= "image/tiff";
		$mimetypes["tif"]	= "image/tiff";
		$mimetypes["ras"]	= "image/x-cmu-raster";
		$mimetypes["pnm"]	= "image/x-portable-anymap";
		$mimetypes["pbm"]	= "image/x-portable-bitmap";
		$mimetypes["pgm"]	= "image/x-portable-graymap";
		$mimetypes["ppm"]	= "image/x-portable-pixmap";
		$mimetypes["rgb"]	= "image/x-rgb";
		$mimetypes["xbm"]	= "image/x-xbitmap";
		$mimetypes["xpm"]	= "image/x-xpixmap";
		$mimetypes["xwd"]	= "image/x-xwindowdump";
		$mimetypes["igs"]	= "model/iges";
		$mimetypes["iges"]	= "model/iges";
		$mimetypes["msh"]	= "model/mesh";
		$mimetypes["mesh"]	= "model/mesh";
		$mimetypes["silo"]	= "model/mesh";
		$mimetypes["wrl"]	= "model/vrml";
		$mimetypes["vrml"]	= "model/vrml";
		$mimetypes["css"]	= "text/css";
		$mimetypes["csv"]	= "text/x-csv";
		$mimetypes["html"]	= "text/html";
		$mimetypes["htm"]	= "text/html";
		$mimetypes["asc"]	= "text/plain";
		$mimetypes["txt"]	= "text/plain";
		$mimetypes["c"]		= "text/plain";
		$mimetypes["cc"] = "text/plain";
		$mimetypes["h"] = "text/plain";
		$mimetypes["hh"] = "text/plain";
		$mimetypes["cpp"] = "text/plain";
		$mimetypes["hpp"] = "text/plain";
		$mimetypes["java"] = "text/plain";
		$mimetypes["rtx"] = "text/richtext";
		$mimetypes["tsv"] = "text/tab-separated-values";
		$mimetypes["etx"] = "text/x-setext";
		$mimetypes["sgml"] = "text/x-sgml";
		$mimetypes["sgm"] = "text/x-sgml";
		$mimetypes["vcs"] = "text/x-vCalendar";
		$mimetypes["vcf"] = "text/x-vCard";
		$mimetypes["xml"] = "text/xml";
		$mimetypes["dtd"] = "text/xml";
		$mimetypes["dl"] = "video/dl";
		$mimetypes["fli"] = "video/fli";
		$mimetypes["gl"] = "video/gl";
		$mimetypes["mp2"] = "video/mpeg";
		$mimetypes["mpe"] = "video/mpeg";
		$mimetypes["mpeg"] = "video/mpeg";
		$mimetypes["mpg"] = "video/mpeg";
		$mimetypes["qt"] = "video/quicktime";
		$mimetypes["mov"] = "video/quicktime";
		$mimetypes["avi"] = "video/x-msvideo";
		$mimetypes["movie"] = "video/x-sgi-movie";
		$mimetypes["ice"] = "x-conference/x-cooltalk";
		return $mimetypes[$fileExt];
	}

	/**
	* Desc: Prompt download the selected file
	* @param string $fileExt File Extension
	* @param string $path File name along with path
	* return value: returns the number of bytes read from the file if completed, return false otherwise
	**/
	function downloadFile($fileName,$fileExt,$path)
	{

		// figure out mimetype, should be done using PHP mime.magic once it's out
		$mimetype = $this->getMimeType($fileExt);
		// Apache behaviour seems to send text/plain for unknown mimetypes so that's what we do, too
		if ($mimetype == '') $mimetype = 'text/plain';
		
		header('Content-Type: '.$mimetype);
		header("Expires:".gmdate('D, d M Y H:i:s')."GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		//header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
		// the str_replace fixes quotes being escaped in the suggested filename
		//header('Content-Disposition: attachment; filename="' . str_replace('"', '\"', basename($path)) . '";');
		/*****************/
			if (strpos($_SERVER["HTTP_USER_AGENT"],'MSIE')!==false) {
				header('Content-Disposition: inline; filename="' . str_replace('"', '\"', basename($path)) . '";');					
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			} else {
				header('Content-Disposition: attachment; filename="' . str_replace('"', '\"', basename($path)) . '";');
				header('Pragma: no-cache');
			}
		/*********************/
		header("Content-Transfer-Encoding: binary");
		//header('Content-Type: ' . $mimetype);
		header('Content-Length: ' . filesize($path));
        	// write file as response
		readfile($path);
	} 

	/*
		function downloadFile($fileName,$fileExt,$path)
	{

		// figure out mimetype, should be done using PHP mime.magic once it's out
		$mimetype = $this->getMimeType($fileExt);
		// Apache behaviour seems to send text/plain for unknown mimetypes so that's what we do, too
		if ($mimetype == '') $mimetype = 'text/plain';
		header("Pragma: ");
		header("Cache-Control: ");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
		// the str_replace fixes quotes being escaped in the suggested filename
		header('Content-Disposition: attachment; filename="' . str_replace('"', '\"', basename($path)) . '";');
		header("Content-Transfer-Encoding: binary");
		header('Content-Type: ' . $mimetype);
		header('Content-Length: ' . filesize($path));
        // write file as response
		readfile($path);
	} 
	*/

	/**
	* Desc: Return file last modified date
	* @param $file: File used
	* return value: return file modified date
	**/
	function fileInfoDate($file)
	{
		global $dateFile;
		$dateFile = date("Y-m-d", filemtime($file));
		return($dateFile);
	} 

	/**
	* Desc: return file size
	* @param string $file File used
	* return value: file size, return 0 otherwise
	**/
	function fileInfoSize($file)
	{
	    return filesize($file);
	} 

	/**
	* Desc: Return file Last Modified
	* @param string $file File used
	* return value: file created date, return blank otherwise
	**/
	function fileInfoCreated($file)
	{
		return date ("Y-m-j", filectime($file));
	} 

	/**
	* Desc: Create a folder
	* @param string $path Path to the new directory
	* 
	**/
	function createDir($path,$mode)
	{
		@mkdir($path, $mode);
	} 

	/**
	 * Folder creation
	 * @param string $path Path to the new directory
	 */
	function createCustomerDirs($root,$path)
	{
		$pathNew = explode('/', $path);
		foreach ($pathNew as $dir) 
		{
			$root	.=	'/'.$dir;
			if (!is_dir($root))
			{
				 $this->createDir($root,0777);
				 @chmod($root,0777);
			}
		}
	} 

	/**
	 * Rename Folder
	 * @param string $root=the root path of the ftp root
	 * @param string $old=Old Directory Name
 	 * @param string $new=New Directory Name
	 */
	function renameCustSuffix($root,$old,$new)
	{
		if (is_dir($root.'/'.$old))
		{
			$old	=	$root.'/'.$old;
			$new	=	$root.'/'.$new;
			@rename($old, $new);
		}
		else
		{
			$this->createCustomerDirs($root,$new.'/inbox');
			$this->createCustomerDirs($root,$new.'/invoices');
			$this->createCustomerDirs($root,$new.'/outbox');
		}
	} 

	/**
	 * Upload a file to a specified destination
	 * @param string $path Path of original file
	 * @param string $source Temp file
	 * @param string $dest Destination File Name
	 */
	function uploadFile($path, $source, $dest)
	{
		$status			=	"";
		$pathNew		=	explode('/', $path);
		$destination	=	$path."/".$dest;
		//echo "$destination, $source, $dest <br>";

		if ( file_exists($destination) ) unlink($destination);
		$status = @move_uploaded_file($source,$destination);

		return $status;
	} 

	/* Delete a file with a specified path
	 * @param string $source Path of file
	 */
	function deleteFile($source)
	{
		return @unlink( $source);
	}

	/**
	* Desc: Return details of files under the path
	* @param string $root Path of public_ftp
	* @param string $dir Directory to be listed
	**/
	function listDirectory($root, $dir)
	{
		// Define the full path to your folder from root
	    $path		=	$root."/".$dir;
		$fileList	=	array();

		// Open the folder
		$dirHandle = @opendir($path);// or die("Unable to open $dir");

	    // Loop through the files
		$i=0;

		while ($fileName = @readdir($dirHandle)) 
		{
			if($fileName == "." || $fileName == "..")
				continue;

			$fileEx		=	substr($fileName,strrpos($fileName,"."),strlen($fileName));
			$fileSize	=	round($this->fileInfoSize($path."/".$fileName)/1024);
			$name		=	substr($fileName,0, strrpos($fileName,"."));
			$fileModfd	=	$this->fileInfoDate($path."/".$fileName);

			if ($name!="" && $fileEx!="")
				$fileList[]		=	array("modified" => $fileModfd, "size" => $fileSize, "fileName" => $name, "type" => $fileEx);	
	    }

		// Close
		@closedir($dirHandle);

		@rsort($fileList);
		@reset($fileList);

		return $fileList;
	}
	
	/**
	* Sort descending order 
	**/
	function sortDesc($a, $b) 
	{
		return strcmp($b["modified"], $a["modified"]);
	}

	/**
	* Description: generate a randum number that should be used as docId(an unique id)
	* Files: fileManagement.php
	**/
	function generateDocId()
	{
		$uniqueMsgId	=	mt_rand(10000000,99999999).date('jmyHis');
		return $uniqueMsgId;
	}

	/**
	* Description: Returns the current Time stamp in Y-m-d H:i:s format
	* Files: fileManagement.php
 	**/
	function getCurrDateTime()
	{
		$nowDateTime	=	date('Y-m-d H:i:s');
		return $nowDateTime;
	}

	/**
	*Description: Returns the Display date in 13-Dec-2005 2:00 PM. Receiving date format is Y-m-d H:i:s
	*Files: fileManagement.php
 	**/
	function getDisplayDate($date,$returnFormat)
	{
		$year			=	substr($date, 0, 4);
		$month			=	substr($date, 5, 2);
		$day			=	substr($date, 8, 2);
		$hour			=	substr($date, 11, 2);
		$minute			=	substr($date, 14, 2);
		$sec			=	substr($date, 17, 2);
		if (strlen($returnFormat)>0)
			$displayDate	=	date($returnFormat, mktime($hour, $minute, $sec, $month, $day, $year));
		else
			$displayDate	=	date("d-M-y g:i A", mktime($hour, $minute, $sec, $month, $day, $year));
		return $displayDate;
	}
	
	/**
	* Move file to a specific location
	* @param $src: current path 
	* @param $dst: destination path
	**/
	function moveFile($src,$dst)
	{
		if ( copy($src,$dst) )	{
			@unlink( $src);
			return true;
		}
		return false;
	}
}
?>