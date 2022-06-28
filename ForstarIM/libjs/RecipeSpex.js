	function openIFrame(IFrameID, url)
	{
		ifId=gmobj(IFrameID);
		ifId.src=url;
	}
	
	forgetClickValue="true";
	
	with(mStyle=new mm_style()) {		
		bgimage="images/tab_on_cream.gif";
		fontfamily="Verdana, Arial, Helvetica, sans-serif";
		fontsize="65%";
		fontstyle="normal";
		fontweight="bold";
		itemheight=26;
		itemwidth=81;		
		offcolor="#000000";
		oncolor="#000000";		
		overbgimage ="images/tab_on_blue.gif";		
		openonclick=1;
		subimagepadding=2;
		clickbgimage="images/tab_on_blue.gif";							
	}

	with(submenuStyle=new mm_style()){
		styleid=1;
		align="center";		
		fontfamily="Verdana, Tahoma, Arial";
		fontsize="65%";
		fontstyle="normal";
		fontweight="bold";
		itemheight=29;
		offbgcolor="#006699";
		offcolor="#ffffff";
		oncolor="#ffffff";
		ondecoration="underline";
		openonclick=1;
		padding=6;		
		separatorsize=3;
	}


	var spexArr = new Array();
	spexArr['RecipeMainCategory.php'] = 0;
	spexArr['RecipeMaster.php'] = 1;
	spexArr['RecipeRateList.php'] = 2;
	spexArr['RecipeRateMaster.php'] = 3;
	spexArr['SupplierRecipe.php'] = 4;
	//spexArr['SupplierIngredient.php'] = 5;	
	
	function openTab(url)
	{		
		var $tabs = $('#container-1').tabs(); // first tab selected
		$tabs.tabs('select', spexArr[url]); // switch to third tab
	}

	function moveTab(url)
	{
		var $tabs = $("#container-1").tabs();
		$tabs.tabs('url',spexArr[url],url+'?mode=AddNew');
		$tabs.tabs('select',spexArr[url]);
		return false;
	}

	function refreshTab()
	{
		var indx = $("#container-1").tabs("option", "selected");		
    		$("#container-1").tabs("load", indx); 
	}

	function chkLogin()
	{
		window.location='Login.php';
	}