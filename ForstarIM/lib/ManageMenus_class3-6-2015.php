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
		$qry = "select id, name from submodule where module_id='$moduleId' order by id";
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
	function displayModule()
	{
		$result="";
		$module=$this->getModuleRecords();
		$result.='<table bgcolor="#999999" cellpadding="0" cellspacing="0" align="center"><tr ><td ><table cellpadding="6" cellspacing="2"><tr bgcolor="#f2f2f2" ><td class="listing-head">Module</td></tr>';	
		$i=0;
		foreach($module as $mod)
		{	$moduleId=$mod[0];
			$moduleName=$mod[1];
			$result.='<tr ><td class="listing-item" bgcolor="#e8edff"><input type="text" name="editmoduleName_'.$i.'" id="editmoduleName_'.$i.'" value="'.$moduleName.'" size="35" class="mainMenu"/><input type="hidden" name="editmoduleId_'.$i.'" id="editmoduleId_'.$i.'" value="'.$moduleId.'" size="35"/></td></tr>';	
			//$result.='<tr ><td class="listing-item" bgcolor="#e8edff">'.$moduleName.'</td><td class="listing-item" bgcolor="#e8edff">edit</td></tr>';	
			$i++;
		}
		
		$result.='<tr><td bgcolor="#f2f2f2" class="listing-item" align="center"><input class="button" type="button" name="addmodule" id="addmodule" value="Add Module" onclick="addModuleList();" />&nbsp;<input class="button" type="button" name="save" id="save" value="Save" onclick="updateMainMenu();" /><input type="hidden" name="moduleSize" id="moduleSize" value="'.$i.'"/></td></tr>';
		$result.='</table></td></tr>';
		$result.='</table>';
		return $result;
		//printr($module);

	}

	function addModuleList()
	{

		$result="";
		$result.='<table >';
		$result.='<tr><td >';
		$result.='<table  bgcolor="#999999" cellpadding="5" cellspacing="1"><tr><td bgcolor="#f2f2f2" colspan="2" class="listing-head">Add Module</td></tr>';
		$result.='<tr><td bgcolor="#e8edff" class="listing-item"><input name="moduleName" id="moduleName" size="35"/></td><td bgcolor="#e8edff" class="listing-item"><input class="button" type="button" name="addModule" id="addModule" onclick="addModule();" value="Add" /></td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='<tr><td >';
		$result.='<table id="displaySubMenu" style="display:none;"   cellpadding="0" cellspacing="0"><tr><td class="listing-item" nowrap style="border:1px #999999 solid; padding:2px;" bgcolor="#F6D8CE" id="subModMsg">You must create a sub module for this Module</td></tr><tr><td style="padding-top:5px">';
		$result.='<table   bgcolor="#999999" cellpadding="5" cellspacing="1"><tr><td bgcolor="#f2f2f2" colspan="2" class="listing-head" >Add Sub Module</td></tr>';
		$result.='<tr><td bgcolor="#e8edff" class="listing-item"><input name="subModName" id="subModName" size="35"/></td><td bgcolor="#e8edff" class="listing-item"><input class="button" type="button" name="addSubModule" id="addSubModule" value="Add" onclick="addSubModule();"/></td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='<tr><td >';
		$result.='<table  id="displayMenu"  style="display:none;"  cellpadding="0" cellspacing="0"><tr ><td class="listing-item" nowrap style="border:1px #999999 solid; padding:2px;" bgcolor="#F6D8CE" id="modeMsg">You must create a menu for this Sub Module</td></tr><tr><td  style="padding-top:5px">';
		$result.='<table bgcolor="#999999" cellpadding="5" cellspacing="1"><tr><td bgcolor="#f2f2f2" colspan="2" class="listing-head">Add  Menu</td></tr>';
		$result.='<tr><td bgcolor="#e8edff" class="listing-item"><input name="addMenu" id="addMenu" size="35"/></td><td bgcolor="#e8edff" class="listing-item"><input class="button" type="button" name="save" id="save" value="save" /></td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='</table>';
		return $result;
	}

	function getSubModuleList()
	{
		$result="";
		$module=$this->getModuleRecords();
		$result.='<table  cellpadding="0" cellspacing="0"><tr><td><table bgcolor="#999999" cellpadding="5" cellspacing="1"><tr ><td class="listing-head" bgcolor="#f2f2f2">Module</td><td class="listing-item" bgcolor="#e8edff"><select name="module" id="module" onchange="xajax_listSubMod(this.value);"><option value="" >--Select--</option>';		
		foreach($module as $mod)
		{	
			$moduleId=$mod[0];
			$moduleName=$mod[1];
			$result.='<option value="'.$moduleId.'">'.$moduleName.'</option>';
		}
		$result.='</select></td></tr>';
		$result.='</table></td></tr>';
		$result.='<tr><td>&nbsp;</td></tr>';
		$result.='<tr><td id="listSubModule"></td></tr>';
		$result.='</table>';
		return $result;
	}

	function displaySubModuleList($moduleId)
	{
		$result="";
		$subModule=$this->getSubMenus($moduleId);
		$result.='<table bgcolor="#999999" cellpadding="0" cellspacing="0"><tr ><td><table cellpadding="6" cellspacing="2"><tr bgcolor="#f2f2f2" ><td class="listing-head">Sub Module</td><td>&nbsp;</td></tr>';		
		foreach($subModule as $subMod)
		{
			$subModuleName=$subMod[1];
			$result.='<tr ><td class="listing-item" bgcolor="#e8edff"><input type="text" name="editSubModuleName" id="editSubModuleName" value="'.$subModuleName.'" size="35"/></td><td class="listing-item" bgcolor="#e8edff">delete</td></tr>';	
			//$result.='<tr ><td class="listing-item" bgcolor="#e8edff">'.$moduleName.'</td><td class="listing-item" bgcolor="#e8edff">edit</td></tr>';		
		}
		
		$result.='<tr><td bgcolor="#f2f2f2" class="listing-item"><input class="button" type="button" name="addSubModule" id="addSubModule" value="Add Sub Module" onclick="addSubModuleList();" /><td bgcolor="#f2f2f2" class="listing-item"><input class="button" type="button" name="saveSub" id="saveSub" value="Save" /></td></tr>';
		$result.='</table></td></tr>';
		$result.='</table>';
		return $result;
	}


	function addSubModuleList()
	{

		$result="";
		$result.='<table >';
		$result.='<tr><td >';
		$result.='<table id="displaySubMenu" cellpadding="0" cellspacing="0"><tr><td style="padding-top:5px">';
		$result.='<table   bgcolor="#999999" cellpadding="5" cellspacing="1"><tr><td bgcolor="#f2f2f2" colspan="2" class="listing-head" >Add Sub Module</td></tr>';
		$result.='<tr><td bgcolor="#e8edff" class="listing-item"><input name="subModName" id="subModName" size="35"/></td><td bgcolor="#e8edff" class="listing-item"><input class="button" type="button" name="addSubModule" id="addSubModule" value="Add" onclick="addSubModule();"/></td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='<tr><td >';
		$result.='<table  id="displayMenu"  style="display:none;"  cellpadding="0" cellspacing="0"><tr ><td class="listing-item" nowrap style="border:1px #999999 solid; padding:2px;" bgcolor="#F6D8CE" id="modeMsg">You must create a menu for this Sub Module</td></tr><tr><td  style="padding-top:5px">';
		$result.='<table bgcolor="#999999" cellpadding="5" cellspacing="1"><tr><td bgcolor="#f2f2f2" colspan="2" class="listing-head">Add  Menu</td></tr>';
		$result.='<tr><td bgcolor="#e8edff" class="listing-item"><input name="addMenu" id="addMenu" size="35"/></td><td bgcolor="#e8edff" class="listing-item"><input class="button" type="button" name="save" id="save" value="save" /></td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='</table>';
		$result.='</td></tr>';
		$result.='</table>';
		return $result;
	}


	function getModuleList()
	{
		$result="";
		$module=$this->getModuleRecords();
		$result.='<table  cellpadding="5" cellspacing="0"><tr><td><table bgcolor="#999999" cellpadding="5" cellspacing="1"><tr ><td class="listing-head" bgcolor="#f2f2f2">Module</td><td class="listing-item" bgcolor="#e8edff"><select name="module" id="module" onchange="xajax_selModule(this.value);"><option value="" >--Select--</option>';		
		foreach($module as $mod)
		{	
			$moduleId=$mod[0];
			$moduleName=$mod[1];
			$result.='<option value="'.$moduleId.'">'.$moduleName.'</option>';
		}
		$result.='</select></td></tr>';
		$result.='</table></td></tr>';
		$result.='<tr><td id="listSubModule" bgcolor="#fff"></td></tr>';
		$result.='<tr><td id="listMenu"  bgcolor="#fff"></td></tr>';
		$result.='</table>';
		return $result;		
	}

	function dropSubModuleList($moduleId)
	{
		$result="";
		$subModule=$this->getSubMenus($moduleId);
		$result.="<table bgcolor='#999999' cellpadding='5' cellspacing='1'><tr ><td class='listing-head' bgcolor='#f2f2f2'>Sub Module</td><td class='listing-item' bgcolor='#e8edff'><select name='subModule' id='subModule' onchange=\"xajax_selSubModule(this.value,document.getElementById('module').value);\"><option value='' >--Select--</option>";		
		foreach($subModule as $mod)
		{	
			$subModuleId=$mod[0];
			$subModuleName=$mod[1];
			$result.='<option value="'.$subModuleId.'">'.$subModuleName.'</option>';
		}
		$result.='</select></td></tr>';
		$result.='</table>';
		return $result;	
	}

	function getMenuList($selModule,$selSubModule)
	{
		$result="";
		$menus=$this->getFunctionRecords($selModule, $selSubModule);
		$result.='<table bgcolor="#999999" cellpadding="5" cellspacing="1">';		
		foreach($menus as $menu)
		{
			$menuName=$menu[1];
			$result.='<tr ><td class="listing-item" bgcolor="#e8edff"><input type="text" name="editMenuName" id="editMenuName" value="'.$menuName.'" size="35"/></td><td class="listing-item" bgcolor="#e8edff">delete</td></tr>';	
			//$result.='<tr ><td class="listing-item" bgcolor="#e8edff">'.$moduleName.'</td><td class="listing-item" bgcolor="#e8edff">edit</td></tr>';		
		}
		$result.='</table>';
		
		return $result;	
	}

	function getListing($moduleId)
	{
		$qry = "select id, name from submodule where module_id='$moduleId' order by id";
		//echo $qry."<br>";
		$result	=	array(); $subModule=[]; $subMenu=[]; $subModuleIds=[];
		$result	=	$this->databaseConnect->getRecords($qry);
		if(sizeof($result)>0)
		{	//echo "hii";
			foreach ($result as $res)
			{	
				$subModuleId=$res[0];
				$subModuleName=$res[1];
				//$subModule[]=$subModuleName;
				//$subModuleIds[]=$subModuleId;
				$subModuleIds[$subModuleId]=$subModuleName;
				$menus=$this->getFunctionRecords($moduleId, $subModuleId);
				foreach($menus as $men)
				{
					$menuName=$men[1];
					$subMenu[$subModuleId][]=$menuName;
				}
				$resultArr=array($subModuleIds,$subMenu);
			}
		}
		else
		{		//echo "hui";
				$menus=$this->getFunctionRecords($moduleId);
				foreach($menus as $men)
				{
					$subModuleIds="0";
					$subModuleId="0";
					$menuName=$men[1];
					$subMenu[$subModuleId][]=$menuName;
				}
				$resultArr=array($subModuleIds,$subMenu);
		}
		//printr($resultArr);
		return $resultArr;
	}


	####update Main menu
	function updateMainMenu($updateArr)
	{
		$up=json_decode($updateArr);
		$upSize=count($up);
		
		//for($i=0; $i<$upSize $i++)
		//{
		foreach($up as $upDet)
		{
			$mainId=$upDet->mainMenuId;
			$mainName=$upDet->mainMenuName;
			$qry = "update module set name='$mainName' where id='$mainId'";
			//echo $qry; die();
			$result = $this->databaseConnect->updateRecord($qry);
			if ($result) $this->databaseConnect->commit();
			else $this->databaseConnect->rollback();
		}
		return $result;	

	}
}	
?>