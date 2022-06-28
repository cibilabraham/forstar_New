<?
require ('HTML/Template/Flexy.php');
require_once 'PEAR.php';
require_once 'HTTP/Session2.php';

class code_creator
{
	protected $templateFolder = "code_creator";
	var $args;
	protected $tpl;	

	function __construct($args)
	{	
		$cwd = getcwd();
		$this->options = array(
		    'templateDir'   => $this->templateFolder,
		    'compileDir'    => './view_compiled',
		    'debug'	=>	0,
			'globals'	=> true,
			'globalfunctions'	=> true,
			'strict'		=> false,
			'flexyIgnore'		=> false,
		    'allowPHP'	=> true
		);		
		$this->args = $args;
	}
	
	function create_component()
	{
		$this->functionalityName=$this->args['functionalityName'];	// comes from user
		$this->modelName=$this->args['modelName'];			// comes from user
		$this->tableName=$this->args['tableName'];			// comes from user

		$this->modelShortName=$this->args['modelShortName'];
		$this->routingPagePrefix=$this->args['routingPagePrefix'];
		$this->paginationLine = "{printPagination(maxpage,pageNo,#".$this->routingPagePrefix.".php?#):h}";

		$fb = getcwd()."/".$this->templateFolder."/";
		
		$cfolder = "components";		
		$baseFolder = $cfolder."/base";
		$compFolder = $cfolder."/".$this->modelName;
		$pageFolder = ".";

		if ( file_exists($compFolder) )	{
			echo "FOLDER $compFolder exists already. Pl rename and run creation again to prevent overwrite.";
			return;
		}

		mkdir($compFolder);		

		// create & move controller
		ob_start();
		$this->tpl = new HTML_Template_Flexy($this->options);
		$this->tpl->compile("sample_controller.tpl");
		$this->tpl->outputObject($this);
		$fileContent = ob_get_contents();
		ob_end_clean();		
				
		$file = fopen($fb.$this->modelName."_controller.php","w");
		fwrite($file,"<?php\n". $fileContent ."\n?>");
		fclose($file);		
		rename($fb.$this->modelName."_controller.php",$compFolder."/".$this->modelName."_controller.php");

		// create model
		ob_start();
		$this->tpl = new HTML_Template_Flexy($this->options);
		$this->tpl->compile("sample_model.tpl");
		$this->tpl->outputObject($this);
		$fileContent = ob_get_contents();
		ob_end_clean();

		$file = fopen($fb.$this->modelName."_model.php","w");
		fwrite($file,"<?php\n". $fileContent ."\n?>");
		fclose($file);
		rename($fb.$this->modelName."_model.php",$baseFolder."/".$this->modelName."_model.php");
	
		//template file
		$file = fopen($fb."design.tpl","r");
		$fileContent = fread($file,filesize($fb."design.tpl"));
		fclose($file);

		$fileContent = str_replace("{modelName}",$this->modelName,$fileContent);
		$fileContent = str_replace("{functionalityName}",$this->functionalityName,$fileContent);
		$fileContent = str_replace("{tableName}",$this->tableName,$fileContent);
		$fileContent = str_replace("{modelShortName}",$this->modelShortName,$fileContent);
		$fileContent = str_replace("{routingPagePrefix}",$this->routingPagePrefix,$fileContent);

		$file = fopen($fb.$this->modelName.".html","w");
		fwrite($file,$fileContent);
		fclose($file);
		rename($fb.$this->modelName.".html",$compFolder."/".$this->modelName.".html");

		// create routing page
		ob_start();
		$this->tpl = new HTML_Template_Flexy($this->options);
		$this->tpl->compile("routerPage.tpl");
		$this->tpl->outputObject($this);
		$fileContent = ob_get_contents();
		ob_end_clean();

		$file = fopen($fb.$this->routingPagePrefix.".php","w");
		fwrite($file,"<?php\n". $fileContent ."\n?>");		
		fclose($file);
		rename($fb.$this->routingPagePrefix.".php",$pageFolder."/".$this->routingPagePrefix.".php");

		// create ajax file
		$file = fopen($fb.$this->modelName."_ajax.php","w");
		fwrite($file,"<?php\n\n?>");
		fclose($file);		
		rename($fb.$this->modelName."_ajax.php",$compFolder."/".$this->modelName."_ajax.php");

		// create js file
		$file = fopen($fb."sample.js","r");
		$fileContent = fread($file,filesize($fb."sample.js"));
		fclose($file);

		$fileContent = str_replace("{modelName}",$this->modelName,$fileContent);
		$fileContent = str_replace("{functionalityName}",$this->functionalityName,$fileContent);
		$fileContent = str_replace("{tableName}",$this->tableName,$fileContent);
		$fileContent = str_replace("{modelShortName}",$this->modelShortName,$fileContent);
		$fileContent = str_replace("{routingPagePrefix}",$this->routingPagePrefix,$fileContent);

		$file = fopen($fb.$this->routingPagePrefix.".js","w");
		fwrite($file,$fileContent);
		fclose($file);
		rename($fb.$this->routingPagePrefix.".js",$compFolder."/".$this->routingPagePrefix.".js");

		echo "Component ".$this->functionalityName . " created.";
		//create template file
		// $this->useTemplate("{modelName}.html");
		//create js file
		// $this->useTemplate("{pageNamePrefix}.js");
	}

}
?>
