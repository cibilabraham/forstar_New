<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- CSS -->
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<link href="libjs/dropdown_menu_style.css" rel="stylesheet" type="text/css">
<link rel ="SHORTCUT ICON" type="image/x-icon" href="images/fs.ico"/>
<?php
 # Server Date
 $serverDate = strtotime("now");
?>
<!-- JS -->
<script language="javascript"> var servertimeOBJ=new Date(<?=$serverDate?>*1000);</script>
<script language="JavaScript" src="libjs/milonic_src.js" type="text/javascript"></script>
<script language="JavaScript">
if(ns4)_d.write("<scr"+"ipt language=JavaScript src=libjs/mmenuns4.js></scr"+"ipt>");
else _d.write("<scr"+"ipt language=JavaScript src=libjs/mmenudom.js></scr"+"ipt>");
</script>
 <?php
    if ($ON_LOAD_SAJAX!="") $xajax->printJavascript("libjs/");
 ?>
<script language="JavaScript" type="text/javascript">
  	function addOption(cId, selectId, val, txt) 
	{
		//alert(cId+"-"+selectId+"-"+val+"-"+txt);
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
		if (cVal==val) objOption.selected=true;
		//alert(cVal+"="+val);
     		document.getElementById(selectId).options.add(objOption);
   	}
</script>
<?php
	$latestVal = "";
	$host  = $_SERVER['HTTP_HOST'];	
	$baseDir        = explode("/",$_SERVER['SCRIPT_NAME']);
	//echo sizeof($baseDir)."<=>".$_SERVER['SCRIPT_NAME'];
	$ROOT_PATH	= "http://".$host;
	if (sizeof($baseDir)>2) {
		$INSTALLATION_PATH = dirname($_SERVER['SCRIPT_NAME']);
		$ROOT_PATH 	.= $INSTALLATION_PATH;
	}	
	$latestVal = file_get_contents($ROOT_PATH.'/libjs/JSFiles.php');	
?>
<script type="text/javascript" src="libjs/JSFiles.php?version=<?=$latestVal?>"></script>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	<?php		
		require("libjs/config.js");
	?>
	//-->
</SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
	<!--
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
		$displayStatus	=	"";
		$nextPage		=	"";
		$displayStatus	=	$sessObj->getValue("displayMsg");
		$nextPage		=	$sessObj->getValue("nextPage");
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
</head>
	<?php
		$onLoad="";
		if ($ON_LOAD_FN!="") {
			$onLoad = "onLoad='".$ON_LOAD_FN."'";
		}
	?>
<body bgcolor="#FFFFFF" leftmargin="2" topmargin="0" marginwidth="0" marginheight="0" <?=$onLoad;?>>
<table width="100%" height="550" border="0" align="center" cellpadding="0" cellspacing="0" id="Table_01" >
	<tr>
		<td colspan="3" height="50">
			<table width="100%" border="0" cellpadding="1" cellspacing="0">
			<tr> 
			  <td width="36%" rowspan="2">
					<IMG SRC="images/forstarfoods.gif" WIDTH="325" HEIGHT="36" BORDER="0" ALT="<?=$companyArr["Name"];?>">
			  </td>
			  <td width="64%" class='td' align='right' valign='bottom'  style='line-height: 5pt;'>
					<!-- Display Welcome Username Start -->
					<?php
						if ($sessObj->getValue("userId")!="")
						{
					?>
						<table cellpadding="0" cellspacing="0" align="right" width="170">
							<tr>
								<td class="welcome-text" >Welcome:&nbsp;</td>
								<td width="15"></td>
							</tr>
							<tr>
								<td colspan="2" class="welcome-text2" >
								<?
						
								echo $sessObj->getValue("userName"); 
								
								?>								</td>
							</tr>
							<tr>
							  <td colspan="2" class="listing-item">
							  <?php 
							  	$cDate = explode("/",date("d/m/Y"));
								echo $currentDate = date("j M Y", mktime(0, 0, 0, $cDate[1], $cDate[0], $cDate[2]));
							  ?>
							</td>
						  </tr>
						</table>
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
		<td colspan="3" height="31" background="images/topBar.gif" class='tabCap2'>
			<!-- Include Menu Links Start -->
			<?php
				if ($sessObj->getValue("userId")!="")
				{
					require("menu.acl");
				}
			?>
			<!-- Include Menu Links End  --->
		</td>
	</tr>
	<?php
		if ($sessObj->getValue("userId")!="") {
	?>
	<tr>
		<TD>
		<?php
			# Get the Menu Display Path
			$displayMenuPath = $modulemanagerObj->getMenuPath($currentUrl);
		?>
			<table>
				<TR>
					<TD class="menu-path" nowrap="true" style="padding-left:10px;padding-top:5px;">
						<?=($displayMenuPath!="")?" YOU ARE HERE : $displayMenuPath":"";?>
					</TD>
				</TR>
			</table>
		</TD>
	</tr>
	<?php
		}	
	?>
	<?php
	if ($help_lnk!="") {
	?>	
	<tr>
		<td colspan="3" align="right"><h5 class="help"><a href="" onClick="wi=window.open('<?=$help_lnk?>','myWin','width=562, height=480, top=300, left=500,   status=1, scrollbars=1, resizable=1');wi.focus();return false;">Help</a></td>
	</tr>		
	<?php
		}
	?>	
	<!--- Page Contents Start -->
	<tr>		
		<td width="100%" height="468" valign="top" colspan="3" align="center">
		