<html>
<head>
<title> Forstar </title>
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
<?php
#Server Date
$serverDate = strtotime("now");
?>
<!-- JS -->
<script language="javascript"> 	
	var servertimeOBJ=new Date(<?=$serverDate?>*1000);
</script>
<script language=JavaScript src="libjs/milonic_src.js" type=text/javascript></script>
<script language=JavaScript>
if(ns4)_d.write("<scr"+"ipt language=JavaScript src=libjs/mmenuns4.js></scr"+"ipt>");
else _d.write("<scr"+"ipt language=JavaScript src=libjs/mmenudom.js></scr"+"ipt>");
</script>
 <?php
if ($ON_LOAD_SAJAX!="") $xajax->printJavascript("libjs/");
?>
<script language="JavaScript" type="text/javascript">	
  function addOption(cId, selectId, val, txt) {
	//alert(cId+":-:"+selectId+":-:"+val+":-:"+txt);
    var objOption = new Option(txt, val);
	if (cId==val && val!="") objOption.selected=true;
     document.getElementById(selectId).options.add(objOption);
   }
  </script>
<script language="JavaScript" type="text/javascript">

function addDropDownList(cId, selectId, val, txt)
{
	//alert(cId+"-"+selectId+"-"+val+"-"+txt);
	var cVal = document.getElementById(cId).value;
    var objOption = new Option(txt, val);
	if (cVal==val  && val!="") objOption.selected=true;
	//alert(cVal+"="+val);
     document.getElementById(selectId).options.add(objOption);
   }
  

</script>
<?php
 if ($ON_LOAD_SAJAX!="") {
 ?>
	<script type="text/javascript" language="JavaScript">
	<!--
		showLoading = function() {		
		 	//alert("hello");
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
			//alert("hai1");
			xajax.$('fnLoading').style.display='block';
		}
		hideFnLoading = function() {
			//alert("hai2");
			xajax.$('fnLoading').style.display = 'none';
		}	
	// -->
	 
	</script> 
<? }?>

<script src="libjs/json2.js"></script>
<script language="JavaScript" type="text/javascript" src="libjs/generalFunctions.js"></script>

<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
<?php	
		require("libjs/config.js");
	?>
	//-->
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
//alert("4345");
<?php
if (isset($ON_LOAD_PRINT_JS)) require("$ON_LOAD_PRINT_JS");
	?>
	//-->
</SCRIPT>

	<link href="libjs/calendar-win2k-cold-1.css" type="text/css" rel="stylesheet">
	<SCRIPT src="libjs/calendar.js" type="text/javascript"></SCRIPT>
	<SCRIPT src="libjs/calendar-en.js" type="text/javascript"></SCRIPT>
	<SCRIPT src="libjs/calendar-setup_3.js" type="text/javascript"></SCRIPT>
	<?php 
		
		$displayStatus	= "";
		$nextPage	= "";
		$displayStatus	= $sessObj->getValue("displayMsg");
		$nextPage	= $sessObj->getValue("nextPage");
		if ($displayStatus!="" && $nextPage!="") {
			$sessObj->putValue("displayMsg","");
			$sessObj->putValue("nextPage","");
	?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		alert("<?=$displayStatus;?>");
		window.location="<?=$nextPage;?>";
		//-->
		</SCRIPT>
	<?php	
		}
		//No nextPage information
		if ($nextPage=="" && $displayStatus!="") {
			$sessObj->putValue("displayMsg","");
	?>
	<SCRIPT LANGUAGE="JavaScript">
		<!--
		alert("<?=$displayStatus;?>");
		//-->
	</SCRIPT>
		<? }?>	
	<?php
		/* Sub Menu Loading*/
		if ($sessObj->getValue("userId")!="")
		{
			require("SubMenu.acl");
		}
	?>

	<link rel="stylesheet" href="libjs/tab/ui.tabs1.css" type="text/css" media="print, projection, screen">
	<link rel="stylesheet" href="libjs/tab/ui.core.css" type="text/css">


	
        <script src="libjs/jquery/jquery-1.3.2.min.js" type="text/javascript"></script>
	<!--<script src="libjs/jquery/jquery-1.5.2.min.js" type="text/javascript"></script>-->

    <!--
	//commented by athira on 2-2-2015
	<link rel="stylesheet" href="libjs/tab/ui.tabs.css" type="text/css" media="print, projection, screen">
	<script src="libjs/tab/ui.core.js" type="text/javascript"></script>
     <script src="libjs/tab/ui.tabs.js" type="text/javascript"></script>-->
		
	<script src="libjs/tab/ui.core1.js" type="text/javascript"></script>
	<script src="libjs/tab/ui.tabs1.js" type="text/javascript"></script>
	<script src="libjs/tab/ui.widget.js" type="text/javascript"></script>

	

	<!-- Lightboc Form  -->
	<link rel="stylesheet" href="libjs/lightbox-form.css" type="text/css">
	<script src="libjs/lightbox-form.js" type="text/javascript"></script>


<!--<script src="libjs/json/json2.js" type="text/javascript"></script>-->
<!--
<script type="text/javascript">
function add_chatinline(){var hccid=15607671;var nt=document.createElement("script");nt.async=true;nt.src="https://mylivechat.com/chatinline.aspx?hccid="+hccid;var ct=document.getElementsByTagName("script")[0];ct.parentNode.insertBefore(nt,ct);}
add_chatinline(); 
</script>-->
</head>
	<?php
	/*
	$onLoad="";	
	if ($ON_LOAD_FN!="") {
		$onLoad = "onLoad='".$ON_LOAD_FN."'";
	}
	*/	
	$onLoad = "onLoad='init();".$ON_LOAD_FN."'";
	
	?>
<body bgcolor="#FFFFFF" leftmargin="2" topmargin="0" marginwidth="0" marginheight="0" <?=$onLoad;?> >
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
<script type="text/javascript" language="JavaScript">
	function ShowTip(msg)
	{
		Tip(msg, BALLOON, true, ABOVE, true, OFFSETX, -17, FADEIN, 600, FADEOUT, 600, PADDING, 8);
		//Tip(msg, BALLOON, true, ABOVE, true, OFFSETX, -19, FADEIN, 900, FADEOUT, 900, PADDING, 8);
	}
</script>


<table width="100%" height="550" border="0" align="center" cellpadding="0" cellspacing="0" id="Table_01" >
	<tr>
		<td colspan="3" height="50" width="100%">
			<table width="100%" border="0" cellpadding="1" cellspacing="0">
			<tr> 
			  <td width="36%" rowspan="2">
					<IMG SRC="images/forstarfoods.gif" WIDTH="325" HEIGHT="36" BORDER="0" ALT="">
			  </td>
			  <td width="54%" class='td' align='center' valign='top'  style="line-height:5pt; padding-left:10px; padding-right:10px;" class="listing-item;">
					<!-- Display Welcome Username Start -->
					<?php
						if ($sessObj->getValue("userId")!="") {
							$rolen = $sessObj->getValue("userRoleName");
							$lastl = $sessObj->getValue("lastLogin");

					?>
						<table cellpadding="0" cellspacing="0" align="right" width="200" border="0">
<!--Role: <?//=$rolen;?> <br>   -->
							<tr onMouseover="ShowTip('Last Login: <?=$lastl;?>');" onMouseout="UnTip();">
								<td class="welcome-text">Welcome:&nbsp;</td>
								<td width="15">&nbsp;</td>
								<td colspan="2" class="welcome-text2" >
								<?php						
								echo $sessObj->getValue("userName");
								//echo $userId;
								?>				
								</td>
								<td width="15">&nbsp;&nbsp;</td>
								<td class="listing-item" nowrap style="line-height:normal;">
									[<a href="###" class="t-l-Link" onclick="javascript:confirmLogout();">Logout</a>]
								</td>
								<td width="15">&nbsp;&nbsp;</td>
								<td class="welcome-text3" nowrap style="line-height:normal;">
								<?php 
									$cDate = explode("/",date("d/m/Y"));
									echo $currentDate = date("j M Y", mktime(0, 0, 0, $cDate[1], $cDate[0], $cDate[2]));
								?>
								</td>
								<td width="15">&nbsp;</td>
							</tr>							
						</table>
						<!--<table cellpadding="0" cellspacing="0" align="right" width="170">
							<tr onMouseover="ShowTip('Role: <?//=$rolen;?> <br> Last Login: <?//=$lastl;?>');" onMouseout="UnTip();">
								<td class="welcome-text" >Welcome:&nbsp;</td>
								<td width="15"></td>
							</tr>
							<tr>
								<td colspan="2" class="welcome-text2" >
								<?php						
								//echo $sessObj->getValue("userName");
								?>				
								</td>
							</tr>
							<tr>
							  <td colspan="2" class="listing-item">
							  <?php 
							  	//$cDate = explode("/",date("d/m/Y"));
								//echo $currentDate = date("j M Y", mktime(0, 0, 0, $cDate[1], $cDate[0], $cDate[2]));
							  ?>
							</td>
						  </tr>
						</table>-->
					<?php
						}				
					?>
					<!-- Display Welcome Username End   -->
			  </td>
			</tr>
		  </table>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="31" background="images/topBar.png" class='tabCap2'>
			<!-- Include Menu Links Start topBar.gif-->
			<?php
				if ($sessObj->getValue("userId")!="") require("MainMenu.acl");
			?>
			<!-- Include Menu Links End  --->
		</td>
	</tr>
	<?php
		if ($sessObj->getValue("userId")!="") {
	?>
	<tr>
		<TD background="images/menuPathBg.png" height="31px" colspan="3">
		<div style="float:left;">
			<?php
			# Get the Menu Display Path
			$displayMenuPath = $modulemanagerObj->getMenuPath($currentUrl);
			?>
			<table border="0">
				<TR>
					<TD class="menu-path" nowrap="true" style="padding-left:10px;padding-top:5px;">
						<?=($displayMenuPath!="")?" <b>YOU ARE HERE</b>:&nbsp;&nbsp;$displayMenuPath":"";?>
					</TD>
				</TR>
			</table>
		</div>
		<div style="float:right;padding-right:10px;">
		<?php
		if ($help_lnk!="") {
		?>		
			<a href="" onClick="wi=window.open('<?=$help_lnk?>','myWin','width=562, height=480, top=300, left=500,   status=1, scrollbars=1, resizable=1');wi.focus();return false;" class="help">Help</a>
		<?php
			}
		?>
		</div>
		</TD>
<!--		<td></td>
		<td align="right" style="padding-right:10px;">
		
		</td>-->
	</tr>
	<?php
		}	
	?>
	<tr><TD height="10px"></TD></tr>
	<?php 
	if($userId!=""){
		$rs_UBlk_chk = $CreateDBBackupObj->getUserBlock($userId);	
		$is_blk = 0;
		$notification_msg = "";
		if(isset($rs_UBlk_chk)){
			$is_blk = $rs_UBlk_chk[2];	
			$notification_msg = $rs_UBlk_chk[0]; 
		}
		//if($userId=="31"){
		if($is_blk){
		echo "<tr><TD height='50px' align='center' class='pageName' style='font-size:14px; font-color:#ff0000; line-height:normal;'>".$notification_msg." ...</TD></tr>";
			$scn_arr = explode("/",$_SERVER['SCRIPT_NAME']);
			$xx= count($scn_arr);
			$pn = $scn_arr[$xx-1];
			if($pn!='ErrorPage.php'){
				header("location:ErrorPage.php");
			}
		}
	}
	?>
	<!--<tr><TD><div id='loading' style="display:none;" class="loading"><img src="images/loading.gif" alt="" />&nbsp;Loading.. </div></TD></tr>-->
	<?php
	//if ($help_lnk!="") {
	?>	
	<!--<tr>
		<td colspan="3" align="right"><h5 class="help"><a href="" onClick="wi=window.open('<?=$help_lnk?>','myWin','width=562, height=480, top=300, left=500,   status=1, scrollbars=1, resizable=1');wi.focus();return false;">Help</a></h5></td>
	</tr>-->		
	<?php
	//	}
	?>	
	<!--- Page Contents Start -->
	<tr>		
		<td width="100%" height="468" valign="top" colspan="3" align="center">