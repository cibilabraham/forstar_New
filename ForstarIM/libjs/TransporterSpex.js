	function openIFrame(IFrameID, url)
	{
		ifId=gmobj(IFrameID);
		ifId.src=url;
		//parent.document.frmTransporterNavPage.pageNav.value = 1;
	}

	
	forgetClickValue="true";	
	with(mStyle=new mm_style()) {		
		bgimage="images/tab_on_cream.gif";
		fontfamily="Verdana, Arial, Helvetica, sans-serif";
		fontsize="58%";
		fontstyle="normal";
		fontweight="bold";
		itemheight=26;
		itemwidth=80; 
		offcolor="#000000";
		oncolor="#000000";
		overbgimage ="images/tab_on_blue.gif";		
		openonclick=1;
		subimagepadding=2;
		clickbgimage="images/tab_on_blue.gif";							
	}

	with(submenuStyle=new mm_style()) {
		styleid=1;
		align="center";		
		fontfamily="Verdana, Tahoma, Arial";
		fontsize="55%";
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
	spexArr['TransporterMaster.php'] = 0;
	spexArr['ZoneMaster.php'] = 1;
	spexArr['WeightSlabMaster.php'] = 2;
	spexArr['TransporterRateList.php'] = 3;
	spexArr['TransporterOtherCharges.php'] = 4;
	spexArr['TransporterWeightSlab.php'] = 5;	
	spexArr['TransporterRateMaster.php'] = 6;
	spexArr['TransporterStatus.php'] = 7;
	
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