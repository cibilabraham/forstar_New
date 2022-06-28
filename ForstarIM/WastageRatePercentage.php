<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	
	$editWastageRatePercentId	=	"";
		
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
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	
	# Update
	if ($p["cmdSaveChange"]!="" ) {
		
		$wastageRatePercentId	=	$p["hidWastageRatePercentId"];
		
		$localQtyRatePercent		=	$p["localQtyRatePercent"];
		$wastageQtyRatePercent		=	$p["wastageQtyRatePercent"];
		$softQtyRatePercent		=	$p["softQtyRatePercent"];

		if ($wastageRatePercentId!="") {
			$wastageRatePercentRecUptd = $wastageratepercentageObj->updateWastageRatePercentRecord($wastageRatePercentId, $localQtyRatePercent, $wastageQtyRatePercent, $softQtyRatePercent);
		}
	
		if ($wastageRatePercentRecUptd) {
			$sessObj->createSession("displayMsg",$msgSuccRatePercentageRecordUpdate);
		} else {
			$editMode	=	true;
			$err		=	$msgFailRatePercentageRecordUpdate;
		}
		$wastageRatePercentRecUptd		=	false;
	}
	//list($localRatePercent, $wastageRatePercent, $softRatePercent) = $wastageratepercentageObj->getWastageRatePercentage();
	
	# Edit 
		$wastageRatePercentRec		=	$wastageratepercentageObj->find();
		$editWastageRatePercentId	=	$wastageRatePercentRec[0];
		$localRatePercentage		=	$wastageRatePercentRec[1];
		$wastageRatePercentage		=	$wastageRatePercentRec[2];
		$softRatePercentage		=	$wastageRatePercentRec[3];		

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmWastageRatePercentage" action="WastageRatePercentage.php" method="post">
<table cellspacing="0"  align="center" cellpadding="0" width="96%">
	<? if($err!="" ){?>
	<tr>
		<td height="20" align="center" class="err1" ><?=$err;?></td>
	</tr>
	<?}?>
	<tr>
		<td height="10" ></td>
	</tr>
	<?php
		if ($editMode || $addMode) {
	?>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<?php	
							$bxHeader="Wastage Rate";
							include "template/boxTL.php";
						?>
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<!--<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Wastage Rate </td>
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

											<td colspan="2" align="center"><? if($edit==true){?>&nbsp;&nbsp;
										  <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="if (!confirmSave()) return false; else return true;">	<? }?>										</td>
											
											<?} else{?>

										  <td align="center">&nbsp;&nbsp;</td>

											<?}?>
										</tr>
				<input type="hidden" name="hidWastageRatePercentId" value="<?=$editWastageRatePercentId;?>">
										
										<tr>
										  <td colspan="2" nowrap height="10"></td>
								  </tr>
										<tr>
										  <td colspan="2" nowrap align="center">
			<table width="40%" align="center" cellpadding="0" cellspacing="0">
			<TR><TD>
										<?php			
											$entryHead = "";
											require("template/rbTop.php");
										?>	
				<table align="center" cellpadding="2" cellspacing="2" style="padding-top:10px;padding-bottom:10px;">
                                <tr>
                                   <td class="fieldName" nowrap>Local Qty (Rate)</td>					
				<td class="fieldName" nowrap><INPUT type="text" size="5" maxlength="6" style="text-align:right;" name="localQtyRatePercent" value="<?=$localRatePercentage?>"> %</td>
                              </tr>
                            <tr>
                              <TD class="fieldName" nowrap>Wastage Qty (Rate)</TD>
                              <td class="fieldName" nowrap><INPUT type="text" size="5" maxlength="6" style="text-align:right;" name="wastageQtyRatePercent" value="<?=$wastageRatePercentage?>"> %</td>
                            </tr>
                            <tr>
                              <TD class="fieldName" nowrap>Soft Qty (Rate)</TD>
                              <td class="fieldName" nowrap><INPUT type="text" size="5" maxlength="6" style="text-align:right;" name="softQtyRatePercent" value="<?=$softRatePercentage?>"> %</td>
                            </tr>
                          </table>
			<?php
				require("template/rbBottom.php");
			?>
		</TD></TR>
		</table>
			</td>
			  </tr>
			<tr>
				<td colspan="4"  height="10" ></td>
			</tr>
			<tr>
				<? if($editMode){?>
				  <td colspan="2" align="center"><? if($edit==true){?>&nbsp;&nbsp;
				  <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="if (!confirmSave()) return false; else return true;"><? }?></td>
				<?} else{?>
				  <td align="center">&nbsp;&nbsp;</td>
				<?}?>
										</tr>
										<!--<tr>
											<td colspan="2"  height="10" ></td>
										</tr>-->
									</table>
							  </td>
							</tr>
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
	<?
		}
		
		# Listing LandingCenter Starts
	?>

	<!--<tr>
				<td height="10" align="center" ></td>
	</tr>-->
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%">
					<tr>
						<td   bgcolor="white">
				
							
						</td>
					</tr>
				</table>
				
			</td>
		</tr>-->		
		<!--<tr>
			<td height="10"></td>
		</tr>-->	
  </table>
</form>
<?php
# Include Template [bottomRightNav.php]
require("template/bottomRightNav.php");
?>