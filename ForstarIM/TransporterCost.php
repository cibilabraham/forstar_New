<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editPackingCostMasterRecId	=	"";
	$packingCostMasterId	=	"";
	$noRec			=	"";
	$editMode		=	true;
	$addMode		=	false;
	
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


	
	#----------------Rate list--------------------	
	$packingCostMaster = "PCM";
	if ($g["selRateList"]!="") $selRateList	= $g["selRateList"];
	else if($p["selRateList"]!="") $selRateList	= $p["selRateList"];
	else $selRateList = $manageRateListObj->latestRateList($packingCostMaster);			
	#--------------------------------------------

	#Update / Insert a Record
	if ($p["cmdSaveChange"]!="") 
	{
		
		$transportCostId	= $p["hidTransporterCostId"];
		
		$transportCostNSWE	= $p["transportCostNSWE"];
		$transportCostNE	= $p["transportCostNE"];
		$transportCostFRZ   = $p["transportCostFRZ"];
		
		#packing Cost Master
		if ($transportCostId!="") 
		{
			$transportCostMasterRecUptd = $transportCostMasterObj->updateTransporterCost($transportCostId, $transportCostNSWE, $transportCostNE, $transportCostFRZ);
		} 
		else 
		{	
			$transportCostMasterRecUptd = $transportCostMasterObj->addTransporterCost($transportCostNSWE, $transportCostNE, $transportCostFRZ);	
		}

	

		if ($transportCostMasterRecUptd) 
		{
			$sessObj->createSession("displayMsg",$msg_succTransportCostUpdate);
			//$sessObj->createSession("nextPage",$url_afterUpdateCompany);
		} 
		else {
			$editMode	=	true;
			$err		=	$msg_failTransportCostUpdate;
		}
		$transportCostMasterRecUptd		=	false;
	}
	
	
	# Edit 
		$getTransportCost = $transportCostMasterObj->getTransportCostDetails();
		
		$edtTransportCostId  	= 	$getTransportCost[0];
		$edtTransportCostNSWE	= 	$getTransportCost[1];
		$edtTransportCostNE		= 	$getTransportCost[2];
		$edtTransportCostFRZ    = 	$getTransportCost[3];

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$packingCostMasterId = $p["hidPackingCostMasterId"];
		if ($packingCostMasterId!="") {
			// Need to check the selected Category is link with any other process
			$prodMatrixRecDel = $packingCostMasterObj->deletePackingCostMasterRec($packingCostMasterId);
		}
		
		if ($prodMatrixRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPackingCostMaster);
			$sessObj->createSession("nextPage",$url_afterDelPackingCostMaster);
		} else {
			$errDel	=	$msg_failDelPackingCostMaster;
		}
		$prodMatrixRecDel	=	false;
	}

	# Rate List
	$pcmRateListRecords = $manageRateListObj->fetchAllRecords($packingCostMaster);

	$ON_LOAD_PRINT_JS = "libjs/TransporterCost.js";

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
<form name="frmTransporterCost" action="TransporterCost.php" method="post">
<table cellspacing="0"  align="center" cellpadding="0" width="96%">
	<tr><TD height="5"></TD></tr>
	<!--
	<tr><td height="10" align="center"><a href="PackingLabourCost.php" class="link1">Packing Labour Cost</a>&nbsp;&nbsp;<a href="PackingSealingCost.php" class="link1">Packing Sealing Cost</a>&nbsp;&nbsp;<a href="PackingMaterialCost.php" class="link1">Packing Material Cost</a></td></tr>
	-->
	<tr><TD height="5"></TD></tr>
	<? if($err!="" ){?>
	<tr>
		<td height="10" align="center" class="err1" ><?=$err;?></td>
	</tr>
	<?}?>
		
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Transporter Cost Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="60%">
	<?
		if ( $editMode || $addMode) {
	?>

	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%">
				<tr>
					<td>
						<!-- Form fields start -->
						<?php							
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<!--<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Packing Cost Master  </td>
							</tr>-->
							<tr>
								<td width="1" ></td>
							  <td colspan="2" >
							    <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
										<tr>
											<td colspan="2" height="10" ></td>
										</tr>
										<tr>
											<? if($editMode){?>
											<td colspan="2" align="center"><? if($edit==true){?>&nbsp;&nbsp;  <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateTransporterCost(document.frmTransporterCost)">	<? }?>										</td>
											
											<?} else{?>

										  <td align="center">&nbsp;&nbsp;</td>

											<?}?>
										</tr>
	<input type="hidden" name="hidTransporterCostId" value="<?=$edtTransportCostId;?>" />
	<tr>
		<td colspan="2" nowrap height="10"></td>
	</tr>
		<tr>
			<td colspan="2" nowrap style="padding-left:10px; padding-right:10px;">
	<table width="200" align="center">
          <tr>
              	<td nowrap >
		<table cellpadding="1" cellspacing="1" id="newspaper-b1">
			<TR>
				<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">TRANSPORT COST</Th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CODE</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">COST</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">UNIT</th>				
			</TR>
				
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">
				Transport Cost per Gr Kg - NSWE</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="transportCostNSWE" id="transportCostNSWE" size="5" style="text-align:right" value="<?=$edtTransportCostNSWE?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>	
			</TR>	
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">
				Transport Cost per Gr Kg - NE</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="transportCostNE" id="transportCostNE" size="5" style="text-align:right" value="<?=$edtTransportCostNE?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>	
			</TR>
<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">
				Transport Cost per Gr Kg - FRZ</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="transportCostFRZ" id="transportCostFRZ" size="5" style="text-align:right" value="<?=$edtTransportCostFRZ?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>	
			</TR>	
			
		</table>
		</td>
          </tr>
          </table></td>
	  </tr>
	<tr>
		<td colspan="4"  height="10" ></td>
	</tr>
	<tr>
	<? if($editMode){?>
  	<td colspan="2" align="center"><? if($edit==true){?>&nbsp;&nbsp;
	<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick=" return validateTransporterCost(document.frmTransporterCost);"><? }?></td>
	<?} else{?>
  	<td align="center">&nbsp;&nbsp;</td>

											<?}?>
										</tr>
										<tr>
											<td colspan="2"  height="10" ></td>
										</tr>
									</table>
							  </td>
							</tr>
					  </table>
					<?php
						require("template/rbBottom.php");
					?>
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
			</table>
		</td>
	</tr>
	<tr>
				<td height="10" align="center" ></td>
	</tr>				
	<input type='hidden' name="hidPLCRateList" value="<?=$plcRateList?>">
	<input type='hidden' name="hidPSCRateList" value="<?=$pscRateList?>">
	<input type='hidden' name="hidPMCRateList" value="<?=$pmcRateList?>">
		<tr>
			<td height="10"></td>
		</tr>		
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
  </table>
							<?php
								include "template/boxBR.php"
							?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
	<tr><TD height="10"></TD></tr>
	<!--
	<tr><td align="center"><a href="PackingLabourCost.php" class="link1">Packing Labour Cost</a>&nbsp;&nbsp;<a href="PackingSealingCost.php" class="link1">Packing Sealing Cost</a>&nbsp;&nbsp;<a href="PackingMaterialCost.php" class="link1">Packing Material Cost</a></td></tr>-->	
	</table>
<? 
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
	ensureInFrameset(document.frmTransporterCost);
	//-->
	</script>
<? 
	}
?>
</form>
<?php
# Include Template [bottomRightNav.php]
if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>