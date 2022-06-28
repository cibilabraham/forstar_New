<?php
require_once("libjs/xajax_core/xajax.inc.php");
$xajax = new xajax();	

//$objResponse->setReturnValue($chkRecExist); // Forretrun a value from ajax function

	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addDropDownList('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}			
  		}


		function addDropDownOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}		
	}
	
	function getModuleData()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$displayData=$manageMenuObj->displayModule();
		$objResponse->assign("dialog", "innerHTML",$displayData);
		//$objResponse->alert($displayData);
		return $objResponse;
	}

	function addModuleData()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$addData=$manageMenuObj->addModuleList();
		$objResponse->assign("menu", "innerHTML",$addData);
		//$objResponse->alert($displayData);
		return $objResponse;
	}

	function getSubModuleData()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$displayData=$manageMenuObj->getSubModuleList();
		$objResponse->assign("dialog", "innerHTML",$displayData);
		//$objResponse->alert($displayData);
		return $objResponse;
	}

	function listSubMod($moduleid)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$displaysubData=$manageMenuObj->displaySubModuleList($moduleid);
		$objResponse->assign("listSubModule", "innerHTML",$displaysubData);
		return $objResponse;
	}

	function addSubModuleData()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$displaysubData=$manageMenuObj->addSubModuleList($moduleid);
		$objResponse->assign("menu", "innerHTML",$displaysubData);
		return $objResponse;
	}

	function getMenuData()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$displayData=$manageMenuObj->getModuleList();
		$objResponse->assign("dialog", "innerHTML",$displayData);
		//$objResponse->alert($displayData);
		return $objResponse;
	}

	function selModule($moduleid)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$displaysubData=$manageMenuObj->dropSubModuleList($moduleid);
		$objResponse->assign("listSubModule", "innerHTML",$displaysubData);
		return $objResponse;
	}
	
	function selSubModule($moduleid,$submoduleid)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$displayMenu=$manageMenuObj->getMenuList($submoduleid,$moduleid);
		//$objResponse->alert($displayMenu);
		$objResponse->assign("listMenu", "innerHTML",$displayMenu);
		return $objResponse;
	}


	function updateMainModule($updateArr)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		//$objResponse->alert("xc");
		$displayMenu=$manageMenuObj->updateMainMenu($updateArr);
		//$objResponse->alert($displayMenu);
		if($displayMenu)
		{
			$objResponse->script("updateSucessMainMenu();");
		}
		else
		{
			$objResponse->script("failUpMainMenu();");
		}
		return $objResponse;
	}



$xajax->register(XAJAX_FUNCTION, 'updateMainModule', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'selSubModule', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'selModule', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getMenuData', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'addSubModuleData', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'listSubMod', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getSubModuleData', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getModuleData', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'addModuleData', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->ProcessRequest();

?>