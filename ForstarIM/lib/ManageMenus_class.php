<?php
Class ManageMenus
{

	/****************************************************************
	This class deals with all the operations relating to Challan Verification
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ManageMenus(&$databaseConnect)
	{
		$this->databaseConnect =&$databaseConnect;
	}
	
	# Get Module Records
	function getModuleRecords()
	{
		$qry = "select id, name from module order by id";
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get Sub Menus
	function getSubMenus($moduleId)
	{
		//$qry = "select id, name from submodule where module_id='$moduleId' order by id";
		//Rekha updated 
		$qry = "select id, name from submodule where module_id='$moduleId' order by order_by";
		//echo $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Get Function Records
	function getFunctionRecords($selModule, $selSubModule)
	{
		$whr  = " module_id='$selModule' and group_main_id=0";
		
		if ($selSubModule!="") $whr .= " and pmenu_id='$selSubModule'";
		else $whr .= " and pmenu_id=0";
		
		$orderBy = " menu_order asc";

		$qry = "select id, name, menu_order,extraflag from function ";

		if ($whr!="") 		$qry .= " where".$whr ;
		if ($orderBy!="") 	$qry .= " order by".$orderBy ;
		//echo  $qry."<br>";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	/************************ Display Order Starts Here ******************************/
	/*
		$recId = FunctionId:MenuOrderId; FunctionId:MenuOrderId;
	*/
	function changeMenuOrder($recId)
	{
		$splitRec = explode(";",$recId);
		$changeMenuF = $splitRec[0];
		$changeMenuS = $splitRec[1];
		list($functionIdF, $menuOrderF) = $this->getFunctionRec($changeMenuF);
		list($functionIdS, $menuOrderS) = $this->getFunctionRec($changeMenuS);
		if ($functionIdF!="") {
			$updateMenuRecF = $this->updateMenuOrder($functionIdF, $menuOrderF);
		}

		if ($functionIdS!="") {
			$updateMenuRecS = $this->updateMenuOrder($functionIdS, $menuOrderS);
		}
		return ($updateMenuRecF || $updateMenuRecS)?true:false;		
	}
	# Split Function Rec and Return Function Id and Menu Order
	function getFunctionRec($rec)
	{
		$splitRec = explode("-",$rec);
		return (sizeof($splitRec)>0)?array($splitRec[0], $splitRec[1]):"";
	}

	# update Menu Order
	function updateMenuOrder($functionId, $menuOrder)
	{
		 $qry = "update function set menu_order='$menuOrder' where id='$functionId'";
		$result = $this->databaseConnect->updateRecord($qry);

		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updateextraflagvalue($idValue,$flagValue)
	{
		$qry = "update function set extraflag='$flagValue' where id='$idValue'";
		$result = $this->databaseConnect->updateRecord($qry);
		//echo $qry;
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	/********************* Display Order End Here****************************/


	/***menu*/


	/*display main menu(module) in dialog box*/
	function displayMainMenu()
	{
		$result="";
		$module=$this->getModuleRecords();
		$result.='<table bgcolor="#999999" cellpadding="0" cellspacing="0" align="center"><tr ><td ><table cellpadding="6" cellspacing="2"><tr bgcolor="#f2f2f2" ><td class="listing-head">Main Menu</td></tr>';	
		$i=0;
		foreach($module as $mod)
		{	$moduleId=$mod[0];
			$moduleName=$mod[1];
			$result.='<tr ><td class="listing-item" bgcolor="#e8edff"><input type="text" name="editmoduleName_'.$i.'" id="editmoduleName_'.$i.'" value="'.$moduleName.'" size="35" class="mainMenu"/><input type="hidden" name="editmoduleId_'.$i.'" id="editmoduleId_'.$i.'" value="'.$moduleId.'" size="35"/></td></tr>';	
			//$result.='<tr ><td class="listing-item" bgcolor="#e8edff">'.$moduleName.'</td><td class="listing-item" bgcolor="#e8edff">edit</td></tr>';	
			$i++;
		}
		
		$result.='<tr><td bgcolor="#f2f2f2" class="listing-item" align="center"><input class="button" type="button" name="addmodule" id="addmodule" value="Add Module" onclick="addMainMenuList();" />&nbsp;<input class="button" type="button" name="save" id="save" value="Save" onclick="updateMainMenu();" /><input type="hidden" name="moduleSize" id="moduleSize" value="'.$i.'"/></td></tr>';
		$result.='</table></td></tr>';
		$result.='</table>';
		return $result;
		//printr($module);
	}

	###design for adding the main menu(module) in dialog box
	function addMainMenuList()
	{

		$result="";
		$result.='<table >';
		$result.='<tr><td >';
		$result.='<table  bgcolor="#999999" cellpadding="5" cellspacing="1"><tr><td bgcolor="#f2f2f2" colspan="2" class="listing-head">Add Main Menu</td></tr>';
		$result.='<tr><td bgcolor="#e8edff" class="listing-item"><input name="moduleName" id="moduleName" size="35"/></td><td bgcolor="#e8edff" class="listing-item"><input class="button" type="button" name="addModule" id="addModule" onclick="addMnMenu();" value="Add" /></td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='<tr><td >';
		$result.='<table id="displaySubMenu" style="display:none;"   cellpadding="0" cellspacing="0">
		<tr><td class="listing-item" nowrap style="border:1px #999999 solid; padding:2px;" bgcolor="#F6D8CE" id="subModMsg">You must create a sub module for this Module</td></tr><tr><td style="padding-top:5px">';
		$result.='<table id="subMenus"   bgcolor="#999999" cellpadding="5" cellspacing="1"><tr><td bgcolor="#f2f2f2" colspan="2" class="listing-head" >Add Sub Menu</td></tr>';
		$result.='<tr><td bgcolor="#e8edff" class="listing-item"><input name="subModName" id="subModName" size="35"/></td><td bgcolor="#e8edff" class="listing-item"><input class="button" type="button" name="addSubMenu" id="addSubMenu" value="Add" onclick="addSubMenu();"/></td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		//$result.='<tr><td class="listing-item"><input type="checkbox" name="subModStatus" id="subModStatus" value="1" onclick="enableSubMenu();";/>&nbsp;No sub menu</td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='<tr><td >';
		$result.='<table  id="displayMenu"  style="display:none;"  cellpadding="0" cellspacing="0"><tr ><td class="listing-item" nowrap style="border:1px #999999 solid; padding:2px;" bgcolor="#F6D8CE" id="modeMsg">You must create a menu for this Sub Module</td></tr><tr><td  style="padding-top:5px">';
		$result.='<table bgcolor="#999999" cellpadding="5" cellspacing="1"><tr><td bgcolor="#f2f2f2" colspan="3" class="listing-head">Add  Menu</td></tr>';
		$result.='<tr><td bgcolor="#e8edff" class="listing-item">Menu:-</td><td bgcolor="#e8edff" class="listing-item"><input name="addMenu" id="addMenu" size="30"/></td><td bgcolor="#e8edff">&nbsp;</td></tr>';
		$result.='<tr><td bgcolor="#e8edff" class="listing-item">Url:-</td><td bgcolor="#e8edff" class="listing-item"><input name="addUrl" id="addUrl" size="30"/></td><td bgcolor="#e8edff" class="listing-item"><input class="button" type="button" name="save" id="save" value="save" onclick="addMainMenu();" /></td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='</table>';
		return $result;
	}

	###display all menu in drop down to add sub module
	function getSubMenuList()
	{
		$result="";
		$module=$this->getModuleRecords();
		$result.='<table  cellpadding="0" cellspacing="0"><tr><td><table bgcolor="#999999" cellpadding="5" cellspacing="1"><tr ><td class="listing-head" bgcolor="#f2f2f2">Main Menu</td><td class="listing-item" bgcolor="#e8edff"><select name="module" id="module" onchange="xajax_listSubMod(this.value);"><option value="" >--Select--</option>';		
		foreach($module as $mod)
		{	
			$moduleId=$mod[0];
			$moduleName=$mod[1];
			$result.='<option value="'.$moduleId.'">'.$moduleName.'</option>';
		}
		$result.='</select></td></tr>';
		$result.='</table></td></tr>';
		$result.='<tr><td>&nbsp;</td></tr>';
		$result.='<tr><td id="listSubMenu"></td></tr>';
		$result.='</table>';
		return $result;
	}

	###display all sub menu 
	function displaySubMenuList($moduleId)
	{
		$result="";
		$subMenu=$this->getSubMenus($moduleId);
		$result.='<table bgcolor="#999999" cellpadding="0" cellspacing="0"><tr ><td><table cellpadding="6" cellspacing="2"><tr bgcolor="#f2f2f2" ><td class="listing-head">Sub Module</td><td>&nbsp;</td></tr>';
		$i=0;
		foreach($subMenu as $subMod)
		{
			$subMenuId=$subMod[0];
			$subMenuName=$subMod[1];
			$result.='<tr ><td class="listing-item" bgcolor="#e8edff"><input type="text" class="subMenu" name="editSubMenuName_'.$i.'" id="editSubMenuName_'.$i.'" value="'.$subMenuName.'" size="35"/><input type="hidden" name="editSubMenuId_'.$i.'" id="editSubMenuId_'.$i.'" value="'.$subMenuId.'" size="35"/></td><td class="listing-item" bgcolor="#e8edff"><input class="button" type="button" name="delSub" id="delSub" value="delete" onclick="xajax_delSub('.$subMenuId.','.$moduleId.');"/></td></tr>';	
			//$result.='<tr ><td class="listing-item" bgcolor="#e8edff">'.$moduleName.'</td><td class="listing-item" bgcolor="#e8edff">edit</td></tr>';	
			$i++;
		}
		
		$result.='<tr><td bgcolor="#f2f2f2" class="listing-item"><input class="button" type="button" name="addSubMenu" id="addSubMenu" value="Add Sub Module" onclick="addSubMenuList('.$moduleId.');" /><input name="hidModuleId" id="hidModuleId" value="'.$moduleId.'" type="hidden"/><input name="subModSize" id="subModSize" value="'.$i.'" type="hidden"/></td><td bgcolor="#f2f2f2" class="listing-item"><input class="button" type="button" name="saveSub" id="saveSub" value="Save" onclick="updateSubMenu();"/></td></tr>';
		$result.='</table></td></tr>';
		$result.='</table>';
		return $result;
	}

	### design for add sub menu	
	function addSubMenuList($moduleId)
	{

		$result="";
		$result.='<table >';
		$result.='<tr><td >';
		$result.='<table id="displaySubMenu" cellpadding="0" cellspacing="0"><tr><td style="padding-top:5px">';
		$result.='<table   bgcolor="#999999" cellpadding="5" cellspacing="1"><tr><td bgcolor="#f2f2f2" colspan="2" class="listing-head" >Add Sub Module</td></tr>';
		$result.='<tr><td bgcolor="#e8edff" class="listing-item"><input name="subModName" id="subModName" size="35"/></td><td bgcolor="#e8edff" class="listing-item"><input class="button" type="button" name="addSubMenu" id="addSubMenu" value="Add" onclick="addSubMenu();"/></td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='<tr><td >';
		$result.='<table  id="displayMenu"  style="display:none;"  cellpadding="0" cellspacing="0"><tr ><td class="listing-item" nowrap style="border:1px #999999 solid; padding:2px;" bgcolor="#F6D8CE" id="modeMsg">You must create a menu for this Sub Module</td></tr><tr><td  style="padding-top:5px">';
		$result.='<table bgcolor="#999999" cellpadding="5" cellspacing="1"><tr><td bgcolor="#f2f2f2" colspan="3" class="listing-head">Add  Menu</td></tr>';
		$result.='<tr><td bgcolor="#e8edff" class="listing-item">Menu:-</td><td bgcolor="#e8edff" class="listing-item"><input name="addMenu" id="addMenu" size="30"/></td><td bgcolor="#e8edff">&nbsp;</td></tr>';
		$result.='<tr><td bgcolor="#e8edff" class="listing-item">Url:-</td><td bgcolor="#e8edff" class="listing-item"><input name="addUrl" id="addUrl" size="30"/></td><td bgcolor="#e8edff" class="listing-item"><input class="button" type="button" name="save" id="save" value="save" onclick="insSubMenu();" /><input name="hidMainmenuId" id="hidMainmenuId" value="'.$moduleId.'" type="hidden"/><input name="subModSize" id="subModSize" value="'.$i.'" type="hidden"/></td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='</table>';
		return $result;
	}

	### design for add menu
	function getModuleList()
	{
		$result="";
		$module=$this->getModuleRecords();
		$result.='<table  cellpadding="5" cellspacing="0"><tr><td><table bgcolor="#999999" cellpadding="5" cellspacing="1"><tr ><td class="listing-head" bgcolor="#f2f2f2">Main Menu</td><td class="listing-item" bgcolor="#e8edff"><select name="module" id="module" onchange="xajax_selModule(this.value);"><option value="" >--Select--</option>';		
		foreach($module as $mod)
		{	
			$moduleId=$mod[0];
			$moduleName=$mod[1];
			$result.='<option value="'.$moduleId.'">'.$moduleName.'</option>';
		}
		$result.='</select></td></tr>';
		$result.='</table></td></tr>';
		$result.='<tr><td id="listSubMenu" bgcolor="#fff"></td></tr>';
		$result.='<tr><td id="listMenu"  bgcolor="#fff"></td></tr>';
		$result.='</table>';
		return $result;		
	}

	###display submodule in drop down
	function dropSubMenuList($moduleId)
	{
		$result="";
		$subMenu=$this->getSubMenus($moduleId);
		$result.="<table bgcolor='#999999' cellpadding='5' cellspacing='1'><tr ><td class='listing-head' bgcolor='#f2f2f2'>Sub Module</td><td class='listing-item' bgcolor='#e8edff'><select name='subMenus' id='subMenus' onchange=\"xajax_selSubMenu(this.value,document.getElementById('module').value);\"><option value='' >--Select--</option>";	
		if(sizeof($subMenu)>0)
		{
			foreach($subMenu as $mod)
			{	
				$subMenuId=$mod[0];
				$subMenuName=$mod[1];
				$result.='<option value="'.$subMenuId.'">'.$subMenuName.'</option>';
			}
		}
		else
		{
			$result.='<option value="">No sub module</option>';
		}
		$result.='</select></td></tr>';
		$result.='</table>';
		return $result;	
	}

	###list all menus corresponding to main menu and sub menu
	function getMenuList($selModule,$selSubMenu)
	{
		$result="";
		$menus=$this->getFunctionRecords($selModule, $selSubMenu);
		$result.='<table bgcolor="#999999" cellpadding="5" cellspacing="1">';
		$i=0;
		foreach($menus as $menu)
		{	
			$menuId=$menu[0];
			$menuName=$menu[1];
			$result.='<tr ><td class="listing-item" bgcolor="#e8edff"><input type="text" name="editMenuName_'.$i.'" id="editMenuName_'.$i.'" value="'.$menuName.'" size="35" class="menu"/><input type="hidden" name="editMenuId_'.$i.'" id="editMenuId_'.$i.'" value="'.$menuId.'" size="35"/></td><td class="listing-item" bgcolor="#e8edff"><input type="button" name="delmenu" id="delmenu" value="delete" onclick="xajax_delMenu('.$menuId.','.$selModule.','.$selSubMenu.');" class="button"></td></tr>';	
			//$result.='<tr ><td class="listing-item" bgcolor="#e8edff">'.$moduleName.'</td><td class="listing-item" bgcolor="#e8edff">edit</td></tr>';	
			$i++;
		}
		$result.='<tr><td bgcolor="#f2f2f2" class="listing-item"><input class="button" type="button" name="addMenu" id="addMenu" value="Add Menu" onclick="addMenuList('.$selModule.','.$selSubMenu.');" /><input name="hidMainId" id="hidMainId" value="'.$selModule.'" type="hidden"/><input name="hidSubMainId" id="hidSubMainId" value="'.$selSubMenu.'" type="hidden"/><input name="menuSize" id="menuSize" value="'.$i.'" type="hidden"/></td><td bgcolor="#f2f2f2" class="listing-item"><input class="button" type="button" name="saveMenu" id="saveMenu" value="Save" onclick="updateMenu();"/></td></tr>';
		$result.='</table>';
		
		return $result;	
	}

	### return all records with limits
	function getListing( $offset, $limit)
	{
		$qry = "select mainid,mainmenu,subid,submenu,menuid,menu from ((select a.id as mainid,a.name as mainmenu,b.id as subid,b.name as submenu,c.id as menuid,c.name as menu from module a left join submodule b on a.id=b.module_id left join function c on (c.pmenu_id=b.id)) union all (select a.id as mainid,a.name as mainmenu,'0'as subid,'0' as submenu,c.id as menuid,c.name as menu from module a  left join function c on (c.pmenu_id=0) where a.id=c.module_id and c.pmenu_id=0))  dum  limit $offset, $limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}

	### return all records
	function getAllListing()
	{
		$qry = "select mainid,mainmenu,subid,submenu,menuid,menu from ((select a.id as mainid,a.name as mainmenu,b.id as subid,b.name as submenu,c.id as menuid,c.name as menu from module a left join submodule b on a.id=b.module_id left join function c on (c.pmenu_id=b.id)) union all (select a.id as mainid,a.name as mainmenu,'0'as subid,'0' as submenu,c.id as menuid,c.name as menu from module a  left join function c on (c.pmenu_id=0) where  a.id=c.module_id and c.pmenu_id=0)) dum ";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	/*function getListing($moduleId)
	{
		$qry = "select id, name from submodule where module_id='$moduleId' order by id";
		//echo $qry."<br>";
		$result	=	array(); $subMenu=[]; $subMenu=[]; $subMenuIds=[];
		$result	=	$this->databaseConnect->getRecords($qry);
		if(sizeof($result)>0)
		{	//echo "hii";
			foreach ($result as $res)
			{	
				$subMenuId=$res[0];
				$subMenuName=$res[1];
				//$subMenu[]=$subMenuName;
				//$subMenuIds[]=$subMenuId;
				$subMenuIds[$subMenuId]=$subMenuName;
				$menus=$this->getFunctionRecords($moduleId, $subMenuId);
				foreach($menus as $men)
				{
					$menuName=$men[1];
					$subMenu[$subMenuId][]=$menuName;
				}
				$resultArr=array($subMenuIds,$subMenu);
			}
		}
		else
		{		//echo "hui";
				$menus=$this->getFunctionRecords($moduleId);
				foreach($menus as $men)
				{
					$subMenuIds="0";
					$subMenuId="0";
					$menuName=$men[1];
					$subMenu[$subMenuId][]=$menuName;
				}
				$resultArr=array($subMenuIds,$subMenu);
		}
		//printr($resultArr);
		return $resultArr;
	}*/


	####update Main menu
	function updateMainMenu($updateArr)
	{
		$up=json_decode($updateArr);
		$upSize=count($up);
		
		//for($i=0; $i<$upSize $i++)
		//{
		foreach($up as $upDet)
		{
			$mainId=$upDet->MainMenuId;
			$mainName=$upDet->MainMenuName;
			$qry = "update module set name='$mainName' where id='$mainId'";
			//echo $qry; die();
			$result = $this->databaseConnect->updateRecord($qry);
			if ($result) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
		}
		return $result;	
	}

	###add main menu
	function addMainMenu($moduleName)
	{
		$qry	= "insert into  module(name) values('$moduleName')";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	###add sub menu
	function addSubMenu($moduleId,$subMenu)
	{
		$qry	= "insert into  submodule(module_id,name) values('$moduleId','$subMenu')";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		//echo $qry; die();
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	###add menu
	function addMenu($mainId,$subId,$menu,$addUrl)
	{
		$qry	= "insert into function(module_id,pmenu_id,name,url) values('$mainId','$subId','$menu','$addUrl')";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	function updateSubMenu($subArr)
	{
		$subMenuList=json_decode($subArr);
		$mainMenuId=$subMenuList->MainMenuId;
		$subMenuRecords=$subMenuList->SubMenuArr;
		//printr($subMenuRecords);
		foreach($subMenuRecords as $subMenuRec)
		{
			$subMenuId=$subMenuRec->SubMenuId;
			$subMenu=$subMenuRec->SubMenu;
			$qry = "update submodule set name='$subMenu' where id='$subMenuId' and module_id='$mainMenuId'";
			//echo $qry; die();
			$result = $this->databaseConnect->updateRecord($qry);
			if ($result) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
		}
		//echo $moduleId;
		return $result;
	}

	function deleteSubMenu($subMenuId)
	{
		$qry = " delete from submodule where id='$subMenuId'";
		//echo $qry; die();
		$result	=	$this->databaseConnect->delRecord($qry);
		//echo $qry; die();
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	function updateMenu($updateMenu)
	{
		$upMenu=json_decode($updateMenu);
		$mainId=$upMenu->MainId;
		$subId=$upMenu->SubId;
		$menuArrRecords=$upMenu->MenuArr;
		//printr($subMenuRecords);
		foreach($menuArrRecords as $menuRec)
		{
			$menuId=$menuRec->MenuId;
			$menuName=$menuRec->MenuName;
			$qry = "update function set name='$menuName' where id='$menuId' and module_id='$mainId' and pmenu_id='$subId'";
			//echo $qry; die();
			$result = $this->databaseConnect->updateRecord($qry);
			if ($result) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
		}
		return $result;
	}

	function addMenuList($mainId,$subId)
	{
		$result="";
		$result.='<table bgcolor="#999999" cellpadding="5" cellspacing="1"><tr><td bgcolor="#f2f2f2" colspan="3" class="listing-head">Add  Menu</td></tr>';
		$result.='<tr><td bgcolor="#e8edff" class="listing-item">Menu:-</td><td bgcolor="#e8edff" class="listing-item"><input name="addMenus" id="addMenus" size="30"/></td><td bgcolor="#e8edff">&nbsp;</td></tr>';
		$result.='<tr><td bgcolor="#e8edff" class="listing-item">Url:-</td><td bgcolor="#e8edff" class="listing-item"><input name="addUrl" id="addUrl" size="30"/></td><td bgcolor="#e8edff" class="listing-item"><input class="button" type="button" name="save" id="save" value="save" onclick="insMenu('.$mainId.','.$subId.');" /><input name="hidMainId" id="hidMainId" value="'.$mainId.'" type="hidden"/><input name="hidSubId" id="hidSubId" value="'.$subId.'" type="hidden"/></td></tr>';
		$result.='</table>';
		return $result;
	}

	function deleteMenu($menuId)
	{
		$qry = "delete from function where id='$menuId'";
		//echo $qry; die();
		$result	=	$this->databaseConnect->delRecord($qry);
		//echo $qry; die();
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
}	
?>