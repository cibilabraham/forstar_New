<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- CSS -->
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<link href="libjs/dropdown_menu_style.css" rel="stylesheet" type="text/css">
<link rel ="SHORTCUT ICON" type="image/x-icon" href="images/fs.ico"/>
<?php
#Server Date
 $serverDate = strtotime("now");
?>
<!-- JS -->
<script language="javascript"> var servertimeOBJ=new Date(<?=$serverDate?>*1000);</script>
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
<script language="JavaScript" type="text/JavaScript" src="libjs/menu.js"></script>
<script language="JavaScript" type="text/javascript" src="libjs/generalFunctions.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/user.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/fishmaster.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/grademaster.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/qualitymaster.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/landingcenter.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/preprocessor.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/supplier.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/subsupplier.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/process.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/processcode.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/competitor.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/plantsandunits.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/dailycatchentry.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/dailyrates.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/dailypreprocess.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/dailyprocessing.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/competitorscatch.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/supplieraccount.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/supplierpayments.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/processorsaccounts.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/processorspayments.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/fishcategory.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/unitmaster.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/dailycatchreport.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/dailycatchsummary.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/dailyprocessingreport.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/preprocessingreport.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/manageusers.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/managerole.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/eucode.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/glaze.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/customer.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/brand.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/freezing.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/mcpacking.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/freezingstage.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/frozenpacking.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/paymentterms.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/status.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/dailyfrozenpacking.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/purchaseorder.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/orderprocessing.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/labellingstage.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/usdvalue.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/manageipaddress.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/packagingstructure.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/settlementsummary.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/processratelist.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/processorsettlementsummary.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/container.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/invoice.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/displayrecord.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/repacking.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/dailyfrozenrepacking.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/processingactivities.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/processingrestriction.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/frozenpackingreport.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/manageconfirm.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/rmsupplycost.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/dailythawing.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/freezercapacity.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/dailyactivitychart.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/dailyfreezingchart.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/purchasereport.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ManageChallan.js"></script>
<!--  Inventory Script-->
<script language="JavaScript" type="text/JavaScript" src="libjs/category.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/subcategory.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/stockentry.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/SupplierInventory.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/supplierstock.js"></script>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	<?php		
		require("libjs/config.js");
	?>
	//-->
</SCRIPT>
<SCRIPT language="JavaScript" type="text/javascript" src="libjs/department.js" ></SCRIPT>
<script language="JavaScript" type="text/JavaScript" src="libjs/StockReport.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/StockConsumption.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/StockPurchaseReject.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/PurchaseOrderReport.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/StockSummary.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/UnitGroup.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/StockItemUnit.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/SupplierRateList.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ImportStock.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/RevisePurchaseOrder.js"></script>
<!--  Inventory End Here-->
<SCRIPT LANGUAGE="JavaScript">
	<!--
	<?php
		if (isset($ON_LOAD_PRINT_JS)) require("$ON_LOAD_PRINT_JS");
	?>
	//-->
</SCRIPT>
<!--  RTE Starts Here-->
<script language="JavaScript" type="text/JavaScript" src="libjs/IngredientRateList.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/IngredientReceipt.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ProductBatch.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ProductDetails.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ProductCategory.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ProductState.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ProductGroup.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/PackingCategory.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ProductionMatrixMaster.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ProductionMatrix.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/PackingCostMaster.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/PackingMatrix.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ProductMatrix.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/StateMaster.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/CityMaster.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/DistMarginRateList.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/DistMarginStructure.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/OrderDispatched.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/RetailCounterMaster.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ProductConversion.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ClaimProcessing.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/MarginStructure.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ProductPricing.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ProductPriceRateList.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/DistributorProductPrice.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/RetailCounterStock.js"></script>
<!--  RTE Ends Here-->
<script language="JavaScript" type="text/JavaScript" src="libjs/ManageID.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/StockHoldingCostReport.js"></script>
	<link href="libjs/calendar-win2k-cold-1.css" type=text/css rel=stylesheet>
	<SCRIPT src="libjs/calendar.js" type=text/javascript></SCRIPT>
	<SCRIPT src="libjs/calendar-en.js" type=text/javascript></SCRIPT>
	<SCRIPT src="libjs/calendar-setup_3.js" type=text/javascript></SCRIPT>
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
		