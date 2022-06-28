<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- CSS -->
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<link href="libjs/dropdown_menu_style.css" rel="stylesheet" type="text/css">
<link rel ="SHORTCUT ICON" type="image/x-icon" href="images/fs.ico"/>
<style type="text/css">
<!-- 
body {
	behavior:url("libjs/csshover.htc");
}
-->
</style>
<!-- JS -->
<flexy:toJavascript serverDate="serverDate" />
<script language="javascript">		
	var servertimeOBJ=new Date(serverDate*1000);
</script>
<script language=JavaScript src="libjs/milonic_src.js" type=text/javascript></script>
<script language=JavaScript>
if(ns4)_d.write("<scr"+"ipt language=JavaScript src=libjs/mmenuns4.js></scr"+"ipt>");
else _d.write("<scr"+"ipt language=JavaScript src=libjs/mmenudom.js></scr"+"ipt>");
</script>
{printAjaxJs()}
<script language="JavaScript" type="text/javascript">	
  function addOption(cId, selectId, val, txt) {
	//alert(cId+":-:"+selectId+":-:"+val+":-:"+txt);
    var objOption = new Option(txt, val);
	if (cId==val && val!="") objOption.selected=true;
     document.getElementById(selectId).options.add(objOption);
   }
</script>
<script language="JavaScript" type="text/javascript">
  function addDropDownList(cId, selectId, val, txt) {
	//alert(cId+"-"+selectId+"-"+val+"-"+txt);
	var cVal = document.getElementById(cId).value;
    var objOption = new Option(txt, val);
	if (cVal==val) objOption.selected=true;
	//alert(cVal+"="+val);
     document.getElementById(selectId).options.add(objOption);
   }
</script>
{if:loadAjax}
	<script type="text/javascript" language="JavaScript">
	<!--
		showLoading = function() {		
			xajax.$('loading').style.display='block';
			if (navigator.appName == "Microsoft Internet Explorer") {
			            document.getElementById("loading").style.top = document.body.scrollTop;
		        } else {
			          document.getElementById("loading").style.top = window.pageYOffset;
		        }
		};
		hideLoading = function() {			
			xajax.$('loading').style.display = 'none';
		}

		showFnLoading = function() {		
			xajax.$('fnLoading').style.display='block';
		}
		hideFnLoading = function() {			
			xajax.$('fnLoading').style.display = 'none';
		}	
	// -->
	</script> 
{end:}
<flexy:toJavascript refreshTimeLimit="refreshTimeLimit" />

<script language="JavaScript" type="text/javascript" src="libjs/generalFunctions.js"></script>

<flexy:include src="../../libjs/config_n.js" />
{if:loadJS}
<flexy:include src="../../{onLoadJS}" />
{end:}
<!--<SCRIPT LANGUAGE="JavaScript" type="text/javascript" flexy:if="loadJS" src="{onLoadJS}"></script>-->
	<link href="libjs/calendar-win2k-cold-1.css" type="text/css" rel="stylesheet" />
	<SCRIPT src="libjs/calendar.js" type="text/javascript"></SCRIPT>
	<SCRIPT src="libjs/calendar-en.js" type="text/javascript"></SCRIPT>
	<SCRIPT src="libjs/calendar-setup_3.js" type="text/javascript"></SCRIPT>
	<flexy:toJavascript displayStatus="displayStatus" />
	<flexy:toJavascript nextPage="nextPage" />
	{if:showMessage}	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	alert(displayStatus);
	window.location=nextPage;
	//-->
	</SCRIPT>
	{end:}

	{if:showAlert}		
	<SCRIPT LANGUAGE="JavaScript">
		<!--
		alert(displayStatus);
		//-->
	</SCRIPT>
	{end:}

<flexy:toJavascript subMenuCode1="subMenuCode1" />
<flexy:toJavascript subMenuCode2="subMenuCode2" />

	{if:showSubMenu}<flexy:include src="../../ftemplate/SubMenu.acl" />{end:}

	<link rel="stylesheet" href="libjs/tab/ui.tabs1.css" type="text/css" media="print, projection, screen">
	<link rel="stylesheet" href="libjs/tab/ui.core.css" type="text/css">
        <!--<script src="libjs/jquery/jquery-1.3.2.min.js" type="text/javascript"></script>-->
	<script src="libjs/jquery/jquery-1.4.js" type="text/javascript"></script>
	<!--<script src="libjs/jquery/jquery-1.2.6.js" type="text/javascript"></script>-->
	<!--<script src="libjs/jquery/jquery-1.3.2.min.js" type="text/javascript"></script>--> <!--Single tab -->
        <script src="libjs/tab/ui.core1.js" type="text/javascript"></script>
        <script src="libjs/tab/ui.tabs1.js" type="text/javascript"></script>
	<script src="libjs/tab/ui.widget.js" type="text/javascript"></script>
	
	<style type="text/css">		
/* Vertical Tabs*/
.ui-tabs-vertical { width: 98%; border: 1 px;}
.ui-tabs-vertical .ui-vtabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
.ui-tabs-vertical .ui-vtabs-nav li { clear: left; width: 100%; border-bottom-width: 0px !important; border-right-width: 0 !important;}
.ui-tabs-vertical .ui-vtabs-nav li a { display:block; }
.ui-tabs-vertical .ui-vtabs-panel { padding: 0em; float: left; width: 7em; }
	</style>
<!-- Lightboc Form  -->
	<link rel="stylesheet" href="libjs/lightbox-form.css" type="text/css">
	<script src="libjs/lightbox-form.js" type="text/javascript"></script>

</head>
<body bgcolor="#FFFFFF" leftmargin="2" topmargin="0" marginwidth="0" marginheight="0" onLoad='{onBodyLoad}' >
<div id='loading' style="display:none;" class="loading"><img src="images/loading.gif" alt="" />&nbsp;Loading...</div>
<div id="fnLoading" class="div_overlay" align="center" style="display:none;">
	<div id="lightbox1" class="div_lightbox">
	<center><br><br><br><br><br><font class="pageLoadingHead" >Loading...Please wait...</font><br><img src='images/ajax-loader.gif' ></center>
	</div>	
</div>
<div id="pageLoading" class="div_overlay" align="center">
	<div id="lightbox1" class="div_lightbox">
	<center><br><br><br><br><br><font class="pageLoadingHead" >Page Loading...Please wait...</font><br><img src='images/ajax-loader.gif' ></center>
	</div>	
</div>
<script type="text/javascript" language="JavaScript">
		var ld=(document.all);
		var ns4=document.layers;
		var ns6=document.getElementById&&!document.all;
		var ie4=document.all;
		if (ns4) ld=document.pageLoading;
		else if (ns6) ld=document.getElementById("pageLoading").style;
		else if (ie4) ld=document.all.pageLoading.style;
		
		function init()
		{
			if(ns4){ld.visibility="hidden";}
			else if (ns6||ie4) ld.display="none";

			// Readonly change style  starts here
			//init_fields();
		}
	</script>
<!-- Include Files for Tooltip -->
<script type="text/javascript" src="libjs/tooltip/wz_tooltip.js"></script>
<script type="text/javascript" src="libjs/tooltip/tip_balloon.js"></script>
<script>
	function ShowTip(msg)
	{
		Tip(msg, BALLOON, true, ABOVE, true, OFFSETX, -17, FADEIN, 600, FADEOUT, 600, PADDING, 8);
	}
</script>
<table width="100%" height="{defaultTbleHeight}" border="0" align="center" cellpadding="0" cellspacing="0" id="Table_01" >
	<tr flexy:if="!printMode">
		<td colspan="3" height="50" width="100%">
			<table width="100%" border="0" cellpadding="1" cellspacing="0">
			<tr> 
			  <td width="36%" rowspan="2">
					<IMG SRC="images/forstarfoods.gif" WIDTH="325" HEIGHT="36" BORDER="0" ALT="">
			  </td>
			  <td width="54%" class='td' align='center' valign='top'  style="line-height:5pt; padding-left:10px; padding-right:10px;" class="listing-item;">
					<!-- Display Welcome Username Start 'Role: {rolen} <br> -->
					{if:showWelcome}
						<table cellpadding="0" cellspacing="0" align="right" width="200" border="0">
							<tr onMouseover="ShowTip('Last Login: {lastl}');" onMouseout="UnTip();">
								<td class="welcome-text">Welcome:&nbsp;</td>
								<td width="15">&nbsp;</td>
								<td colspan="2" class="welcome-text2" >
								{username}				
								</td>
								<td width="15">&nbsp;&nbsp;</td>
								<td class="listing-item" nowrap style="line-height:normal;">
									[<a href="###" class="t-l-Link" onclick="javascript:confirmLogout();">Logout</a>]
								</td>
								<td width="15">&nbsp;&nbsp;</td>
								<td class="welcome-text3" nowrap style="line-height:normal;">
								{currentDate}
								</td>
								<td width="15">&nbsp;</td>
							</tr>							
						</table>
						{end:}
					<!-- Display Welcome Username End   -->
			  </td>
			</tr>
		  </table>
		</td>
	</tr>
	<tr flexy:if="!printMode">
		<td colspan="3" height="31" background="images/topBar.png" class='tabCap2'>
			<!-- Include Menu Links Start topBar.gif-->
			{if:username} <flexy:include src="../../ftemplate/MainMenu.acl" /> {end:}
			<!-- Include Menu Links End  --->
		</td>
	</tr>		
	<tr flexy:if="!printMode">
		<TD background="images/menuPathBg.png" height="31px" colspan="3">
		{if:username}
		<div style="float:left;">			
			<table border="0">
				<TR>
					<TD class="menu-path" nowrap="true" style="padding-left:10px;padding-top:5px;">
						{if:displayMenuPath}<b>YOU ARE HERE</b>:&nbsp;&nbsp;{displayMenuPath:h}{end:}
					</TD>
				</TR>
			</table>
		</div>
		{end:}
		<div style="float:right;padding-right:10px;">
		{if:help_lnk}
		?>		
			<a href="" onClick="wi=window.open('{help_lnk}','myWin','width=562, height=480, top=300, left=500,   status=1, scrollbars=1, resizable=1');wi.focus();return false;" class="help">Help</a>
		{end:}
		</div>
		</TD>
<!--		<td></td>
		<td align="right" style="padding-right:10px;">-->
		
		</td>
	</tr>		
	<tr flexy:if="!printMode"><TD height="10px"></TD></tr>
	<!--<tr><TD><div id='loading' style="display:none;" class="loading"><img src="images/loading.gif" alt="" />&nbsp;Loading.. </div></TD></tr>-->	
	<!--- Page Contents Start -->
	<tr>		
		<td width="100%"  valign="top" colspan="3" align="center">