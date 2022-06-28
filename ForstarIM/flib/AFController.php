<?php
//require_once "HTML/Template/IT.php";
require ('HTML/Template/Flexy.php');
require_once 'HTML/Template/Flexy/Element.php';
require_once 'PEAR.php';
require_once 'HTTP/Session2.php';
require_once 'flib/AFProcessor.php';

require_once 'components/base/module_model.php';
require_once 'components/base/role_model.php';
require_once 'components/base/accesscontrol_model.php';
require_once 'components/base/display_record_model.php';




class AFController
{
	private $templateRoot = "components/";
	protected $templateFolder;
	protected $tpl;	
	protected $xajax;
	public $loadAjax=false;
	public $loadJS=false;
	public $onLoadJS;
	public $showMessage=false;
	public $currentUrl;
	public $rowNum=array();
	public $limit="";
	public $argsArr=array();
	public $data=array();
	public $boxHeader = "";
	public $subBoxHeader = "";
	public $editMode 	= false;
	public $addMode 	= false;
	public $listMode  = true;	
	public $printMode = false;
	public $defaultTbleHeight = 550;

	var $serverDate;
	var $displayStatus;
	var $nextPage;
	var $olFns = array('init()');
	
	var $refreshTimeLimit=60;	
	var $template=null;
	var $mainMenuCode="";
	var $subMenuCode1="";
	var $subMenuCode2="";
	var $userId = "";
	

	function __construct($argsArr=null,$xajax=null)
	{		
		$options = array(
		    'templateDir'   => $this->templateRoot . $this->templateFolder,
		    'compileDir'    => './view_compiled',
		    'debug'	=>	0,
		'globals'	=> true,
		'globalfunctions'	=> true,
		'strict'		=> false,
		'flexyIgnore'		=> false,
		    'allowPHP'	=> true
		);
		
		$this->argsArr = $argsArr;
		$this->data = AFProcessor::preprocess($argsArr);

		// set defaults
		$this->xajax = $xajax;
		HTTP_Session2::useCookies(true);
		HTTP_Session2::start();
		
		if ($this->xajax != null ) $this->loadAjax=true;
		$this->userId = HTTP_Session2::get("userId");
		if ($this->userId!="") {
			$this->showWelcome=true;
			$this->rolen = HTTP_Session2::get("userRoleName");
			$this->lastl = HTTP_Session2::get("lastLogin");
			$cDate = explode("/",date("d/m/Y"));
			$this->currentDate = date("j M Y", mktime(0, 0, 0, $cDate[1], $cDate[0], $cDate[2]));
			$this->username = HTTP_Session2::get("userName");
			$this->userRoleId = HTTP_Session2::get("userRole");
		} else header("Location:Login.php");


		$this->help_lnk = "";

		#For taking the Main Menu
		$this->roleObj = new role_model();
		$this->distinctModuleIdRecs =	$this->roleObj->disitnctModuleIdRecs($this->userRoleId);
		
		# Set Display Limit
		$this->displayRecordObj = new display_record_model();
		$this->limit = $this->displayRecordObj->getDisplayLimit();
			
		if ( $this->username != "")	{
			$this->showSubMenu=true;
		}
		$this->copyrightYear = date("Y");
		$this->tpl = new HTML_Template_Flexy($options);
	}

	function generateSubMenu1()
	{
		$accesscontrolObj = new accesscontrol_model();
		$mainModuleId = "";
		$mainMenuName = "";
//		print_r($distinctModuleIdRecs);
		foreach ($this->distinctModuleIdRecs as $dmr) {
			$this->subMenuCode1 .= "with(milonic=new menuname('$dmr->name')){\n";
			$this->subMenuCode1 .= "overflow='scroll';\n";
			$this->subMenuCode1 .= "style=menuStyle\n;";
			$i=1;
			$target="";
			$j=0;
			$getDistinctSubModule = $this->modulemanagerObj->getDistinctSubModule($dmr->module_id, $this->userRoleId);
			if (sizeof($getDistinctSubModule)>0) {
				foreach ($getDistinctSubModule as $gdsm) {
					$subModuleId = $gdsm->pmenuid;
					if ($subModuleId!=0) {
						$subModuleName = $this->roleObj->findSubMenu($subModuleId);
						$tempVar = "text=$subModuleName;showmenu=$subModuleName;";
						$this->subMenuCode1 .= "aI('text=$subModuleName;showmenu=$subModuleName');";	
					} else {
						$getNoSubMenuRecords = $this->modulemanagerObj->getEmptyOfSubModule($dmr->module_id);
						$tempVar = "";
						$menuItems = "";
						foreach ($getNoSubMenuRecords as $nsm) {
							$functionId	=	$nsm->id;
							$moduleId	=	$nsm->module_id;
							$functionName	=	$nsm->name;
							$functionUrl	=	$nsm->url;
							$tget		=	$nsm->target;
							$accesscontrolObj->getAccessControl($this->userRoleId,$moduleId, $functionId);
							if ($accesscontrolObj->canAccess()) {
								
								if ($tget!="") $functionUrl ="javascript:mynewwindow('$functionUrl')";
								$tempVar = "text=$functionName;url=$functionUrl;";
								$this->subMenuCode1 .= "aI(\"".$tempVar . "\");";
							}
						}
					}
				}				
			}	
			$this->subMenuCode1 .= "}";
		}
	}

	function generateSubMenu2()
	{
		$accesscontrolObj = new accesscontrol_model();
		$mainModuleId = "";
		foreach ($this->distinctModuleIdRecs as $dmr) {
			//print_r($dmr);
			$mainModuleId = $dmr->module_id;
			$getDistinctSubModule = $this->modulemanagerObj->getDistinctSubModule($mainModuleId, $this->userRoleId);
			foreach ($getDistinctSubModule as $gdsm) {
				//print_r($gdsm);
				$subModuleId = $gdsm->pmenuid;
				if ($subModuleId!=0) {
					$subModuleName = $this->roleObj->findSubMenu($subModuleId);
					//echo "RRR $subModuleName";
					$getSubMenuRecords = $this->modulemanagerObj->getSubmenus($subModuleId);
					if (sizeof($getSubMenuRecords)>0) {					
						$this->subMenuCode2 .= "with(milonic=new menuname('$subModuleName')){overflow='scroll';style=menuStyle;";
						$menuItems = "";
						$functionName = "";
						$functionUrl = "";
							foreach ($getSubMenuRecords as $gs) {
//print_r($gs);
								$subMenuModuleId   = $gs->id;
								$subMenuFunctionId = $gs->fnid; 
								$functionName	=	$gs->fnname;
								$functionUrl	=	$gs->fnurl;
//echo ">>$subMenuModuleId, $subMenuFunctionId";
								$accesscontrolObj->getAccessControl($this->userRoleId,$subMenuModuleId, $subMenuFunctionId);
								$tempVar = "";
								
								if ($accesscontrolObj->canAccess()) {
//echo "RRR";
									$tempVar = "text=$functionName;url=$functionUrl;";
									$this->subMenuCode2 .= "aI(\"".$tempVar . "\");";
								}
							}
						$this->subMenuCode2 .=  "}";
					}
				}
				
			}
		}
		//echo "<pre>".$this->subMenuCode2."</pre>";
	}	

	function useTemplate($template)
	{
		$this->template = $template;
	}

	function render($elements=null) {
		$this->doPreRender();
		$this->tpl->compile($this->template);
		if ($elements!=null) $this->tpl->outputObject($this, $elements);
		else $this->tpl->outputObject($this);
	}
	

	function printAjaxJs()
	{
		if ( $this->xajax != null ) $this->xajax->printJavascript("libjs");
	}

	function setOnLoadJavascript($onLoadJS)
	{
		$this->loadJS = true;
		$this->onLoadJS = $onLoadJS;
	}

	function addOnLoadFn($fn)
	{
		array_push($this->olFns,$fn);
	}

	function doPreRender()
	{
		if ($this->printMode) $this->defaultTbleHeight = 0;
		$this->serverDate =  strtotime("now");
		$this->displayStatus	= HTTP_Session2::get("displayMsg");
		$this->nextPage	= HTTP_Session2::get("nextPage");
		
		if ($this->displayStatus!="" && $this->nextPage!="") {
			HTTP_Session2::set("displayMsg","");
			HTTP_Session2::set("nextPage","");
			$this->showMessage=true;
		}

		if ($this->nextPage=="" && $this->displayStatus!="") {
			$this->showAlert=true;
			HTTP_Session2::set("displayMsg","");
		}

		$this->chkAccess();
		foreach($this->olFns as $fn)
		{
			$this->onBodyLoad.=$fn.";";
		}
		
		$this->displayMenuPath = $this->modulemanagerObj->getMenuPath($this->currentUrl);
		foreach ($this->distinctModuleIdRecs as $dmr) {
			$this->mainMenuCode .= "aI('text=".$dmr->name.";showmenu=".$dmr->name.";');";
		}
		
		$this->generateSubMenu1();
		$this->generateSubMenu2();
	}

	function chkAccess()
	{
		$accesscontrolObj = new accesscontrol_model();
		$this->modulemanagerObj = new module_model();
		//------------  Checking Access Control Level  ----------------
		$this->add	=false;
		$this->edit	=false;
		$this->del	=false;
		$this->print	=false;
		$this->confirm	=false;
		$this->reEdit 	=false;
		
		list($moduleId,$functionId) = $this->modulemanagerObj->resolveIds($this->currentUrl);
		if ($moduleId=="" || $functionId=="") die("Unable to resolve the module. Pl make sure the current URL is correct.");
		//echo "($moduleId,$functionId)";
		$accesscontrolObj->getAccessControl($this->userRoleId, $moduleId, $functionId);
		if (!$accesscontrolObj->canAccess()) { 
			//echo "ACCESS DENIED";
			header ("Location: ErrorPage.php");
			die();	
		}	
		
		if($accesscontrolObj->canAdd()) $this->add=true;
		if($accesscontrolObj->canEdit()) $this->edit=true;
		if($accesscontrolObj->canDel()) $this->del=true;
		if($accesscontrolObj->canPrint()) $this->print=true;
		if($accesscontrolObj->canConfirm()) $this->confirm=true;
		if ($accesscontrolObj->canReEdit()) $this->reEdit=true;
		//----------------------------------------------------------	

		#-------------------Admin Checking--------------------------------------
		$this->isAdmin 	= false;		
		if (strtolower($this->rolen)=="admin" || strtolower($this->rolen)=="administrator") {
			$this->isAdmin = true;
		}
		#-----------------------------------------------------------------

	}

	function setRow($row,$pos=0)
	{
		$this->rowNum[$pos]=$row;
	}

	function getRow($pos=0)
	{
		return $this->rowNum[$pos];
	}

	function incrementRow($pos=0)
	{
		$this->rowNum[$pos]++;
	}

	
	function setBoxHeader($header)
	{	
		$this->boxHeader = $header;
	}	

	function setSubBoxHeader($header)
	{	
		$this->subBoxHeader = $header;
	}

	function printPagination($maxpage,$pageNo,$url)
	{	
		if ($maxpage>1) {
			$sep="&";	
			if ( $this->endsWith($url,"?") ) $sep="";
			
			$nav  = '';
			for ($page=1; $page<=$maxpage; $page++) {
				if ($page==$pageNo) {
				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page			
				} else {
					$nav.= " <a href=\"" . $url . $sep . "pageNo=$page\" class=\"link1\">$page</a> ";
				}
			}
			if ($pageNo > 1) {
				$page  = $pageNo - 1;
				$prev  = " <a href=\"" . $url . $sep . "pageNo=$page\"  class=\"link1\"><<</a> ";
			} else {
				$prev  = '&nbsp;'; // we're on page one, don't print previous link
				$first = '&nbsp;'; // nor the first page link
			}
	
			if ($pageNo < $maxpage) {
				$page = $pageNo + 1;
				$next = " <a href=\"" . $url . $sep . "pageNo=$page\"  class=\"link1\">>></a> ";
			} else {
				$next = '&nbsp;'; // we're on the last page, don't print next link
				$last = '&nbsp;'; // nor the last page link
			}
			// print the navigation link
			$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
			return $first . $prev . $nav . $next . $last . $summary;
		} else return;
	}

	function endsWith($haystack,$needle,$case=true) {
    		if($case){return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);}
    		return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
	}

	function startsWith($haystack,$needle,$case=true) {
    		if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
    		return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
	}

	function showEntry()
	{
		if ($this->addMode || $this->editMode) return true;
		return false;
	}

	function createVars($modelName, $rec)
	{
		foreach ($rec as $key=>$value) {
			//echo "$key=>$value";
		 //eval('\$this->$key=$value');
			if (!isset($this->elements['data['.$modelName.']['.$key.']'])) $this->elements['data['.$modelName.']['.$key.']'] = new HTML_Template_Flexy_Element;
			$this->elements['data['.$modelName.']['.$key.']']->setValue($value);	
		}
		
	}

	#Return the Ordinary date format
	function dateFormat($selectedDate)
	{
		$sDate		= explode("-", $selectedDate);
		$formatedDate	= $sDate[2]."/".$sDate[1]."/".$sDate[0];
		return ($selectedDate!="")?$formatedDate:"";
	}

	#Return the date in database Format(ie.YYYY-MM-DD)
	function mysqlDateFormat($selectedDate)
	{
		$sDate		= explode("/", $selectedDate);
		$mysqlDate	= $sDate[2]."-".$sDate[1]."-".$sDate[0];
		return ($selectedDate!="")?$mysqlDate:"";
	}

	
	
}
?>
