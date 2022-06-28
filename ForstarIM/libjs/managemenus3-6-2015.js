	function getModule()
	{
		xajax_getModuleData();
		$("#dialog").dialog({modal:true, minWidth: 380,position: {
			 my: 'top', 
            at: 'top'
		}});
	}

	function addModuleList()
	{
		xajax_addModuleData();
		$("#menu").dialog({modal:true, minWidth: 380,position: {
			 my: 'top', 
            at: 'top'
		}});
	}

	function addModule()
	{
		$("#displaySubMenu").css("display", "block");
		$( "#subModName" ).focus();
		$("#subModMsg").hide(2000);
	}

	function addSubModule()
	{
		$("#displayMenu").css("display","block");
		$( "#addMenu" ).focus();
		$("#modeMsg").hide(2000);
	}

	function getSubModule()
	{
		xajax_getSubModuleData();
		$("#dialog").dialog({modal:true, minWidth: 380});
	}


	function addSubModuleList()
	{
		xajax_addSubModuleData();
		$("#menu").dialog({modal:true, minWidth: 380});
	}

	function getMenu()
	{
		xajax_getMenuData();
		$("#dialog").dialog({modal:true, minWidth: 380});
	}

	function updateMainMenu()
	{
		var i=0; 
		var updateArr=new Array(); 
		$('.mainMenu').each(function() {
			var mainMenuId=$("#editmoduleId_"+i).val();
			var mainMenuName=$("#editmoduleName_"+i).val();
			var Updatedata='{"mainMenuId":'+JSON.stringify(mainMenuId)+',"mainMenuName":'+JSON.stringify(mainMenuName)+'}';
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

	function updateSucessMainMenu()
	{
		alert("Main menu updated successfully");
		getModule();
	}

	function failUpMainMenu()
	{
		alert("failed to update");
	}


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