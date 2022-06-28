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
		itemwidth=79;		
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
		//bgimage="http://img.milonic.com/tab_subback.gif";
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
		//separatorimage="http://img.milonic.com/tab_subback_sep.gif";
		separatorsize=3;
	}

	var spexArr = new Array();
	spexArr['PackingCostMaster.php'] = 0;
	spexArr['PackingMatrix.php'] = 1;
	

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
			alert(indx);
    		$("#container-1").tabs("load", indx); 
	}

	function chkLogin()
	{
		window.location='Login.php';
	}