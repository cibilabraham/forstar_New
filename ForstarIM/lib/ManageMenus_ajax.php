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
	
	###list main menu(mainMenu) in dialog box
	function getMainMenuData()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$displayData=$manageMenuObj->displayMainMenu();
		$objResponse->assign("dialog", "innerHTML",$displayData);
		//$objResponse->alert($displayData);
		return $objResponse;
	}

	###showing the field to add main menu(mainMenu) in dialog box
	function addMainMenuData()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$addData=$manageMenuObj->addMainMenuList();
		$objResponse->assign("dialog2", "innerHTML",$addData);
		//$objResponse->alert($displayData);
		return $objResponse;
	}

	###display  main menu in dropdown inside dialog box
	function getSubMenuData()
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$displayData=$manageMenuObj->getSubMenuList();
		$objResponse->assign("dialog", "innerHTML",$displayData);
		//$objResponse->alert($displayData);
		return $objResponse;
	}

	###list sub menu(sub mainMenu)  corresponding to the menu in dialog box
	function listSubMod($mainMenuid)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$displaysubData=$manageMenuObj->displaySubMenuList($mainMenuid);
		$objResponse->assign("listSubMenu", "innerHTML",$displaysubData);
		return $objResponse;
	}

	###showing the field to add main menu(mainMenu) in dialog box
	function addSubMenuData($mainMenuid)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$displaysubData=$manageMenuObj->addSubMenuList($mainMenuid);
		$objResponse->assign("dialog2", "innerHTML",$displaysubData);
		return $objResponse;
	}

	###design for sub menu and display main menu in dropdown inside dialog box
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

	###display sub menu(submainMenu) in dropdown inside dialog box
	function selModule($mainMenuid)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$displaysubData=$manageMenuObj->dropSubMenuList($mainMenuid);
		$objResponse->assign("listSubMenu", "innerHTML",$displaysubData);
		return $objResponse;
	}
	
	###design for menu listing all menus corresponding to main menu and sub menu
	function selSubMenu($mainMenuid,$submainMenuid)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$displayMenu=$manageMenuObj->getMenuList($submainMenuid,$mainMenuid);
		//$objResponse->alert($displayMenu);
		$objResponse->assign("listMenu", "innerHTML",$displayMenu);
		return $objResponse;
	}

	###update 
	function updateMainModule($updateArr)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		//$objResponse->alert("xc");
		$displayMenu=$manageMenuObj->updateMainMenu($updateArr);
		//$objResponse->alert($displayMenu);
		//$objResponse->script("updateMainMenuStat($displayMenu);");
		if($displayMenu)
		{
			$objResponse->script("updateSucess(1);");
		}
		else
		{
			$objResponse->script("failUpMainMenu();");
		}
		return $objResponse;
	}

	###add menu,submenu and menu
	function insertMainModule($mainData)
	{
	
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$addDetails=json_decode($mainData);
		$mainMenuName=$addDetails->ModuleName;
		$subModName=$addDetails->SubModName;
		$menu=$addDetails->Menu;
		$addUrl=$addDetails->AddUrl;
		$addMain=$manageMenuObj->addMainMenu($mainMenuName);
		$mainId = $databaseConnect->getLastInsertedId();
		if($addMain && $subModName!="No Sub Menu")
		{
			$subMenu=$manageMenuObj->addSubMenu($mainId,$subModName);
			$subId = $databaseConnect->getLastInsertedId();
			
		}
		if($addMain && $subId)
		{
			$menu=$manageMenuObj->addMenu($mainId,$subId,$menu,$addUrl);	
		}
		else
		{
			$menu=$manageMenuObj->addMenu($mainId,"0",$menu,$addUrl);	
		}

		if($menu)
		{
			$objResponse->script("insertSucess(1);");
		}
		else
		{
			$objResponse->script("insertfail();");
		}
		return $objResponse;
	}

	###update sub menu
	function updateSubMenu($updateArr)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		//$objResponse->alert("xc");
		$subMenuList=json_decode($updateArr);
		$mainMenuId=$subMenuList->MainMenuId;
		$displayMenu=$manageMenuObj->updateSubMenu($updateArr);
		if($displayMenu)
		{
			$objResponse->script("updateSucess(2,mainMenuId);");
		}
		else
		{
			$objResponse->script("failUpMainMenu();");
		}
		return $objResponse;
	}

	/*insert sub menu*/
	function insertSubMenu($subMenuArr)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$addDetails=json_decode($subMenuArr);
		$mainId=$addDetails->MainMenuId;
		$subName=$addDetails->SubMenu;
		$menu=$addDetails->AddMenu;
		$addUrl=$addDetails->AddUrl;
		if($mainId && $subName)
		{
			$subMenu=$manageMenuObj->addSubMenu($mainId,$subName);
			$subId = $databaseConnect->getLastInsertedId();
		}
		if($mainId && $subId)
		{
			$menu=$manageMenuObj->addMenu($mainId,$subId,$menu,$addUrl);	
		}

		if($menu)
		{
			$objResponse->script("insertSucess(2,$mainId);");
		}
		else
		{
			$objResponse->script("insertfail();");
		}
		return $objResponse;
	}
	
	/*delete sub menu*/
	function delSub($subMenuId,$moduleId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		//$objResponse->alert("xc");
		$deleteSub=$manageMenuObj->deleteSubMenu($subMenuId);
		if($deleteSub)
		{
			$objResponse->script("deleteSucess(2,$moduleId);");
		}
		else
		{
			$objResponse->alert("delete failed");
		}
		return $objResponse;
	}

	/*update Menu */
	function updateMenu($updateMenu)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$updateSub=$manageMenuObj->updateMenu($updateMenu);
		$upMenu=json_decode($updateMenu);
		$mainId=$upMenu->MainId;
		$subId=$upMenu->SubId;
		if($updateSub)
		{
			$objResponse->script("updateSucess(3,$mainId,$subId);");
		}
		else
		{
			$objResponse->script("failUpMainMenu();");
		}
		return $objResponse;
	}

	function addMenuData($mainId,$subId)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$menu=$manageMenuObj->addMenuList($mainId,$subId);
		$objResponse->assign("dialog2", "innerHTML",$menu);
		return $objResponse;
	}

	function insertMenu($menuArr)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$menuDetail=json_decode($menuArr);
		$mainId=$menuDetail->MainId;
		$subId=$menuDetail->SubId;
		$addUrl=$menuDetail->AddUrl;
		$addMenu=$menuDetail->AddMenu;
		if($mainId && $subId)
		{
			$menu=$manageMenuObj->addMenu($mainId,$subId,$addMenu,$addUrl);	
		}

		if($menu)
		{
			$objResponse->script("insertSucess(3,$mainId,$subId);");
		}
		else
		{
			$objResponse->script("insertfail();");
		}
		return $objResponse;
	}

	function delMenu($menuId,$selModule,$selSubMenu)
	{
		$objResponse 	= new NxajaxResponse();		
		$databaseConnect= new DatabaseConnect();
		$manageMenuObj	=	new ManageMenus($databaseConnect);
		$deleteMenu=$manageMenuObj->deleteMenu($menuId);
		if($deleteMenu)
		{
			$objResponse->script("deleteSucess(3,$selModule,$selSubMenu);");
		}
		else
		{
			$objResponse->alert("delete failed");
		}
		return $objResponse;
	}








$xajax->register(XAJAX_FUNCTION, 'delMenu', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'insertMenu', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'addMenuData', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'updateMenu', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'delSub', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'insertSubMenu', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'updateSubMenu', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'insertMainModule', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'updateMainModule', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'selSubMenu', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'selModule', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getMenuData', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'addSubMenuData', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'listSubMod', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getSubMenuData', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getMainMenuData', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'addMainMenuData', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->ProcessRequest();

?>