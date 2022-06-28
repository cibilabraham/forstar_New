	/*pop up for list main menu(module)*/
	function getMainMenu()
	{
		xajax_getMainMenuData();
		$("#dialog").dialog({modal:true, minWidth: 380,position: {
			 my: 'top', 
            at: 'top'
		}});
	}

	/*pop up showing the field to add main menu(module)*/
	function addMainMenuList()
	{
		xajax_addMainMenuData();
		$("#dialog2").dialog({modal:true, minWidth: 380,position: {
			 my: 'top', 
            at: 'top'
		}});
	}

	/*design for add Main menu*/
	function addMnMenu()
	{
		$("#displaySubMenu").css("display", "block");
		$( "#subModName" ).focus();
		$("#subModMsg").hide(2000);
	}

	/*design for add sub Menu*/
	function addSubMenu()
	{
		$("#displayMenu").css("display","block");
		$( "#addMenu" ).focus();
		$("#modeMsg").hide(2000);
	}

	/*pop up for list sub menu(sub module)*/
	function getSubMenu()
	{
		xajax_getSubMenuData();
		$("#dialog").dialog({modal:true, minWidth: 380,position: {
			 my: 'top', 
            at: 'top'
			}
		});
	}

	/*design for add sub Menu corresponding to main menu*/
	function addSubMenuList(moduleId)
	{
		xajax_addSubMenuData(moduleId);
		$("#dialog2").dialog({modal:true, minWidth: 380,position: {
			 my: 'top', 
            at: 'top'
			}
		});
	}

	function addMenuList(moduleId,submoduleId)
	{
		xajax_addMenuData(moduleId,submoduleId);
		$("#dialog2").dialog({modal:true, minWidth: 380,position: {
			 my: 'top', 
            at: 'top'
			}
		});
	}
	/*pop up for list menu(sub module)*/
	function getMenu()
	{
		xajax_getMenuData();
		$("#dialog").dialog({modal:true, minWidth: 380,position: {
			 my: 'top', 
            at: 'top'
			}
		});
	}

	/* update main menu*/
	function updateMainMenu()
	{
		var i=0; 
		var updateArr=new Array(); 
		$('.mainMenu').each(function() {
			var mainMenuId=$("#editmoduleId_"+i).val();
			var mainMenuName=$("#editmoduleName_"+i).val();
			var Updatedata='{"MainMenuId":'+JSON.stringify(mainMenuId)+',"MainMenuName":'+JSON.stringify(mainMenuName)+'}';
			updateArr.push(Updatedata);
			//alert("hii"+i );
			i++;
		});
		if(updateArr.length>0)
		{	updateArr='['+updateArr+']';
			xajax_updateMainModule(updateArr);
			//alert(updateArr);
		}
	}

	
	/* msg after updated successfully*/
	/*function updateMainMenuStat(displayMenu)
	{
		if(displayMenu==1)
		{
			alert("Main menu updated successfully");
			getModule();
		}
		else
		{
			alert("failed to update");
		}
	}*/
	/* msg after updated successfully*/
	function updateSucess(state,mainId,subId)
	{
		alert("Updated successfully");
		if(state==1)
		{
			getModule();
		}
		else if(state==2)
		{
			xajax_listSubMod(mainId);
		}
		else if(state==3)
		{
			xajax_selSubMenu(subId,mainId);	
		}
	}

	/* msg after updated failed*/	
	function failUpMainMenu()
	{
		alert("failed to update");
	}

	/* enable sub menu */
	function enableSubMenu()
	{
		if($("#subModStatus").prop("checked") == true){
			$("#subMenus").hide();
			$("#displayMenu").show();
			$("#modeMsg").hide(2000);
		}
		else
		{
			$("#subMenus").show();
		}
	}

	/* json for add main menu, sub menu and menu*/
	function addMainMenu()
	{
		//alert("hii");
		var addMainArr=new Array();
		var moduleName=$("#moduleName").val();
		var chkSub=$("#subModStatus").prop("checked");
		if(chkSub==false)
		{
			var subModName=$("#subModName").val();
		}
		else
		{
			var subModName="No Sub Menu";
		}
		var addMenu=$("#addMenu").val();
		var addUrl=$("#addUrl").val();
		var addMainArr='{"ModuleName":'+JSON.stringify(moduleName)+',"SubModName":'+JSON.stringify(subModName)+',"Menu":'+JSON.stringify(addMenu)+',"AddUrl":'+JSON.stringify(addUrl)+'}';
		xajax_insertMainModule(addMainArr);
		//alert(mainData);
		//addMain.push

	}
	
	/* msg after insertion sucess*/
	function insertSucess(state,mainMenu,subId)
	{
		alert("Inserted successfully");
		$('#dialog2').dialog('close');
		if(state==1)
		{
			getModule();
		}
		else if(state==2)
		{
			getSubMenu();
			setTimeout(function()
			{
				$("#module").val(mainMenu)
			},1000);
			xajax_listSubMod(mainMenu);
		}
		else if(state==3)
		{
			getMenu();
			setTimeout(function()
			{
				$("#module").val(mainMenu)
				$("#subMenus").val(subId)
			},1000);
			xajax_selModule(mainMenu);
			xajax_selSubMenu(subId,mainMenu);
		}
	}

	/* msg when insertion fails*/
	function insertfail()
	{
		alert("failed to insert data");
	}

	/*update sub menu*/
	function updateSubMenu()
	{	var i=0; var subMenuArr=new Array(); var subArr=new Array();
		var hidModId=$("#hidModuleId").val();
		$(".subMenu").each(function(){
			var subMenu=$("#editSubMenuName_"+i).val();
			var subMenuId=$("#editSubMenuId_"+i).val();
			//var subMenuData='{"SubMenuId":'+JSON.stringify(subMenuId)+',"SubMenu":'+JSON.stringify(subMenu)+'}';
			var subMenuData={"SubMenuId":subMenuId,"SubMenu":subMenu};
			subMenuArr.push(subMenuData);
			i++;
		});

		if(subMenuArr.length>0)
		{
			var subArr='{"MainMenuId":'+JSON.stringify(hidModId)+',"SubMenuArr":'+JSON.stringify(subMenuArr)+'}';
			//alert(subArr);
			xajax_updateSubMenu(subArr);
		}

	}

	/*add menu*/
	function insSubMenu()
	{
		var subMenuArr=new Array();
		var hidMainmenuId=$("#hidMainmenuId").val();
		var submenu=$('#subModName').val();
		var addUrl=$('#addUrl').val();
		var addMenu=$('#addMenu').val();
		var subMenuArr='{"MainMenuId":'+JSON.stringify(hidMainmenuId)+',"SubMenu":'+JSON.stringify(submenu)+',"AddUrl":'+JSON.stringify(addUrl)+',"AddMenu":'+JSON.stringify(addMenu)+'}';
		//alert(subMenuArr);
		xajax_insertSubMenu(subMenuArr);
	}

	/* msg after delete record*/
	function deleteSucess(state,mainMenu,subId)
	{
		alert("delete successfully");
		if(state==2)
		{
			getSubMenu();
			setTimeout(function()
			{
				$("#module").val(mainMenu)
			},1000);
			xajax_listSubMod(mainMenu);
		}
		if(state==3)
		{
			getMenu();
			setTimeout(function()
			{
				$("#module").val(mainMenu)
				$("#subMenus").val(subId)
			},1000);
			xajax_selModule(mainMenu);
			xajax_selSubMenu(subId,mainMenu);
		}
	}

	/*update menu*/
	function updateMenu()
	{
		var i=0; var menuArr=new Array(); var updateMenu=new Array();
		var hidMainId=$("#hidMainId").val();
		var hidSubMainId=$("#hidSubMainId").val();
		$(".menu").each(function()
		{
			var menuName=$("#editMenuName_"+i).val();	
			var menuId=$("#editMenuId_"+i).val();	
			var menuData={"MenuId":menuId,"MenuName":menuName};
			menuArr.push(menuData);
			i++;
		});
		if(menuArr.length>0)
		{
			var updateMenu='{"MainId":'+JSON.stringify(hidMainId)+',"SubId":'+JSON.stringify(hidSubMainId)+',"MenuArr":'+JSON.stringify(menuArr)+'}';
			//alert(updateMenu);
			xajax_updateMenu(updateMenu);
		}
	}
	
	/*delete menu*/
	function insMenu(mainId,subId)
	{
		var menuArr=new Array();
		var hidMainId=$("#hidMainId").val();
		var hidSubId=$("#hidSubId").val();
		var addUrl=$('#addUrl').val();
		var addMenus=$('#addMenus').val();
		//alert(addMenus);
		var menuArr='{"MainId":'+JSON.stringify(hidMainId)+',"SubId":'+JSON.stringify(hidSubId)+',"AddUrl":'+JSON.stringify(addUrl)+',"AddMenu":'+JSON.stringify(addMenus)+'}';
		//alert(menuArr);
		xajax_insertMenu(menuArr);
	}
	
	