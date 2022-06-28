<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- CSS -->
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<link href="libjs/dropdown_menu_style.css" rel="stylesheet" type="text/css">
<style type="text/css">

<!-- 

body {

	behavior:url("libjs/csshover.htc");

}

-->

</style>
<!-- JS -->
<?php
#Server Date
$serverDate = strtotime("now");
?>
<!-- JS -->
<script language="javascript"> 	
	var servertimeOBJ=new Date(<?=$serverDate?>*1000);
</script>
<?php
    if ($ON_LOAD_SAJAX!="") $xajax->printJavascript("libjs/");
 ?>
<script language="JavaScript" type="text/javascript">
  function addOption(cId, selectId, val, txt) {
	//alert(cId+"-"+selectId+"-"+val+"-"+txt);
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
<?php
    if ($ON_LOAD_SAJAX!="") {
 ?>
	<script type="text/javascript" language="JavaScript">
	<!--
		showLoading = function() {		
	 //alert("hai");
			xajax.$('loading').style.display='inline';
		};
		hideLoading = function() {			
			xajax.$('loading').style.display = 'none';
		}
		showFnLoading = function() {
			// alert("hai1");
			xajax.$('fnLoading').style.display='block';
		}
		hideFnLoading = function() {			
			xajax.$('fnLoading').style.display = 'none';
		}	
	// -->
	</script> 
<? }?>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	<?php		
		require("libjs/config.js");
	?>
	//-->
</SCRIPT>
<script language="JavaScript" type="text/javascript" src="libjs/generalFunctions.js"></script>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	<?php
		if (isset($ON_LOAD_PRINT_JS)) require("$ON_LOAD_PRINT_JS");
	?>
	//-->
</SCRIPT>
<link href="libjs/calendar-win2k-cold-1.css" type=text/css rel=stylesheet>
	<SCRIPT src="libjs/calendar.js" type=text/javascript></SCRIPT>
	<SCRIPT src="libjs/calendar-en.js" type=text/javascript></SCRIPT>
	<SCRIPT src="libjs/calendar-setup_3.js" type=text/javascript></SCRIPT>
<?php
	$displayStatus	=	"";
	$nextPage	=	"";
	$displayStatus	=	$sessObj->getValue("displayMsg");
	if( $displayStatus!="" ) {
		$sessObj->putValue("displayMsg","");
?>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	alert("<?=$displayStatus;?>");
	//-->
	</SCRIPT>
<?php
	}	
?>
 <script src="libjs/jquery/jquery-1.3.2.min.js" type="text/javascript"></script>
        <script src="libjs/tab/ui.core.js" type="text/javascript"></script>
        <script src="libjs/tab/ui.tabs.js" type="text/javascript"></script>
	<!-- Lightboc Form  -->
	<link rel="stylesheet" href="libjs/lightbox-form.css" type="text/css">
	<script src="libjs/lightbox-form.js" type="text/javascript"></script>
</head>
	<?php
	$onLoad="";
	if ($ON_LOAD_FN!="") {
		$onLoad = "onLoad='".$ON_LOAD_FN."'";
	}
	?>
<body bgcolor="#FFFFFF" leftmargin="2" topmargin="0" marginwidth="0" marginheight="0" <?=$onLoad;?>>
<script type="text/javascript" src="libjs/tooltip/wz_tooltip.js"></script>
<script type="text/javascript" src="libjs/tooltip/tip_balloon.js"></script>
<script>
	function ShowTip(msg)
	{
		Tip(msg, BALLOON, true, ABOVE, true, OFFSETX, -17, FADEIN, 600, FADEOUT, 600, PADDING, 8);
	}
</script>
<div id='loading' style="display:none;" class="loading"><img src="images/loading.gif" alt="" />&nbsp;Loading.. </div>
<div id="fnLoading" class="div_overlay" align="center" style="display:none;">
	<div id="lightbox1" class="div_lightbox">
	<center><br><br><br><br><br><font class="pageLoadingHead" >Loading...Please wait...</font><br><img src='images/ajax-loader.gif' ></center>
	</div>	
</div>
<table width="100%" height="550" border="0" align="center" cellpadding="0" cellspacing="0" id="Table_01" >	
	<tr>		
		<td width="50%" height="468" valign="top" colspan="3" align="center">