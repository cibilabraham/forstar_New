<?
class LanguageResource
{
    #------------------------------------------------------------------------------------|-
	# Desc:	Handle resouce bundle related functions             						 |-
	#------------------------------------------------------------------------------------|-
	
	var $rbFolder	=	""; // resouce bundle folder 
	var $rbFile	=	"";	//balance of the file name will append while calling the function. 
	var $rbExtn	=	".txt"; // resource bundle file extension			
	var $rbHandle	=	array(); // resource bundle contents array 

    /**
    * constructor
    **/
	function LanguageResource($folder,$fileName)
	{
		$this->rbFolder    =    $folder;  // set the resource bundle folder
		$this->rbFile	   =    $fileName; // set the resource bundle file name
	}
    
    /**
    * Desc: set the resouce bundle array 
    * @param $bundle: resouce bundle array 
    **/
	function setBundle($bundle)
	{
		$this->rbHandle = $bundle; // set the resource bundle 
	}

    /**
    * Desc: used to load a resouce bundle file based on the language key
    * @param $lang: language key 
    * return value: return array of contents inside a resouce bundle file, return empty array otherwise
    **/
	function loadBundle($lang)
	{
		$folder			=	$this->rbFolder;
		$fileName		=	$this->rbFile.$lang.$this->rbExtn;
		$rbContents	    =	$this->readRBFile($folder.$fileName);
		$rbContents	    =	split("\n",$rbContents);

		//print_r($rbContents);
		if (sizeof($rbContents)>0)
		{
		    while(list(,$line)=each($rbContents))
		    {
				if ($line!="")	list($key, $value) =    split("=", $line);
				if ($key!="" && ($key!=$line)) $this->rbHandle[trim($key)] =	trim($value);
			}
		}
		return $this->rbHandle;
	}
    
    /**
    * Desc: load & read a resouce bundle file 
    * @param $filename: file to load & read
    * return value: file content string, return false otherwise
    **/
    function readRBFile($filename)
 	{
		// get contents of a file into a string				
		$fp			= fopen($filename, "r");
		$contents	= fread($fp, filesize($filename));
		fclose($fp);
		return $contents;
  	}


	/**
	* Desc: Returns value from a session array 
	* @param $keyName: session key
	* return value: value string based on key, return blank otherwise
	**/
	function getValue($keyName)
	{
		return $this->rbHandle[$keyName];
	}

    /**
    * Desc: Returns formated string of quick tips
    * @param $quickTipKey: 
    * return value: return formated quick tips, return empty html otherwise
    **/
	function getQuickTips($quickTipKey)
	{
		$fTips	=	" <Ul>";	
		$i=1;
		while( ($val = $this->rbHandle["cqt.text.".$quickTipKey.$i]) != "" )
		{
			$fTips	.=	"<li>".$val."</li>";
			$i++;
		}
		$fTips		.= "</ul>";
		return $fTips;
	}
}
?>