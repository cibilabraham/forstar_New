<?php
	require("include/include.php");
	$err			= "";
	$errDel			= "";	

	$editTransporterOthersRecId	= "";
	$transporterOthersRecId		= "";
	$baseCst		= "";
	
	$editMode		= true;
	$addMode		= false;

	$userId	= $sessObj->getValue("userId");	

	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------
	
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	# Update
	if( $p["cmdSaveChange"]!="" ) {
		$transporterOthersRecId	= $p["hidTransporterOthersRecId"];
		$fovCharge	= $p["fovCharge"];
		$docketCharge	= $p["docketCharge"];
		$serviceTax	= $p["serviceTax"];
		$octroiServiceCharge = $p["octroiServiceCharge"];
		
		if ($transporterOthersRecId!="") {
			$taxRecUptd	= $transporterOthersObj->updateTransporterOthersRec($transporterOthersRecId, $fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $userId);
		} else {
			$taxRecUptd	= $transporterOthersObj->addTransporterOthersRec($fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $userId);
		}	
		if ($taxRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succTransporterOthersUpdate);
			//$sessObj->createSession("nextPage",$url_afterUpdateCompany);
		} else {
			$editMode	=	true;
			$err		=	$msg_failTransporterOthersUpdate;
		}
		$taxRecUptd		=	false;
	}
		
	# Edit 
	$transporterOthersRec		= $transporterOthersObj->find();
	$editTransporterOthersRecId	= $transporterOthersRec[0];
	$fovCharge	= $transporterOthersRec[1];
	$docketCharge	= $transporterOthersRec[2];
	$serviceTax	= $transporterOthersRec[3];
	$octroiServiceCharge = $transporterOthersRec[4];
	

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/TransporterOthers.js"; 

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
<form name="frmTransporterOthers" action="TransporterOthers.php" method="post">
<table cellspacing="0"  align="center" cellpadding="0" width="59%">
	<tr>
		<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
	</tr>
	<?
		if ($editMode || $addMode) {
	?>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Transporter Others</td>
							</tr>
							<tr>
								<td width="1" ></td>
							  <td colspan="2" >
							    <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
										<tr>
											<td colspan="2" height="10" ></td>
										</tr>
										<tr>
			<? if($editMode){?>
			<td colspan="2" align="center"><? if($edit==true && $isAdmin==true){?>&nbsp;&nbsp;
			 <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateTransporterOthers(document.frmTransporterOthers)">	
			<? }?>
			</td>
			<?} else{?>
			  <td align="center">&nbsp;&nbsp;</td>
			<?}?>
			</tr>
			<input type="hidden" name="hidTransporterOthersRecId" value="<?=$editTransporterOthersRecId;?>">
										<tr>
										  <td colspan="2" nowrap class="fieldName" height="5"></td>
								  </tr>
										<tr>
										  <td colspan="2" nowrap>
				  <table width="200" align="center">
                                            <tr>
                                              <td class="fieldName" nowrap>*FOV Charge: </td>
						<td class="listing-item" nowrap="true">
							<INPUT NAME="fovCharge" TYPE="text" id="fovCharge" value="<?=$fovCharge;?>" size="4" style="text-align:right;">&nbsp;%
						</td>
                                            </tr>
					    <tr>
                                              <td class="fieldName" nowrap>*Docket Charge (Rs.): </td>
						<td class="listing-item" nowrap="true">
							<INPUT NAME="docketCharge" TYPE="text" id="docketCharge" value="<?=$docketCharge;?>" size="4" style="text-align:right;">
						</td>
                                            </tr>
					    <tr>
                                              <td class="fieldName" nowrap>*Service Tax Rate: </td>
						<td class="listing-item" nowrap="true">
							<INPUT NAME="serviceTax" TYPE="text" id="serviceTax" value="<?=$serviceTax;?>" size="4" style="text-align:right;">&nbsp;%
						</td>
                                            </tr>
					   <tr>
                                              <td class="fieldName" nowrap>*Octroi Service charge: </td>
						<td class="listing-item" nowrap="true">
							<INPUT NAME="octroiServiceCharge" TYPE="text" id="octroiServiceCharge" value="<?=$octroiServiceCharge;?>" size="4" style="text-align:right;">&nbsp;%
						</td>
                                            </tr>		
               </table></td>
	</tr>
	<tr>
		<td colspan="4"  height="10" ></td>
	</tr>
	<tr>
		<? if($editMode){ ?>
		<td colspan="2" align="center"><? if($edit==true && $isAdmin==true){?>&nbsp;&nbsp;
		  <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick=" return validateTransporterOthers(document.frmTransporterOthers);"><? }?></td>
		<? } else{ ?>
		  <td align="center">&nbsp;&nbsp;</td>
		<? } ?>
	</tr>
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	</table>
  	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	<!-- Form fields end   -->
		</td>
	</tr>	
	<?
		}
		
		# Listing LandingCenter Starts
	?>

	<tr>
				<td height="10" align="center" ></td>
	</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>	
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
  </table>
	<?php 
	if ($iFrameVal=="") { 
	?>
	<script language="javascript">
	<!--
	function ensureInFrameset(form)
	{		
		var pLocation = window.parent.location ;	
		var cLocation = window.location.href;			
		if (pLocation==cLocation) {		// Same Location
			document.getElementById("inIFrame").value = 'N';
			form.submit();		
		} else if (pLocation!=cLocation) { // Not in IFrame
			document.getElementById("inIFrame").value = 'Y';
		}
	}
	ensureInFrameset(document.frmTransporterOthers);
	//-->
	</script>
<?php 
	}
?>
</form>
	<?
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
	?>
