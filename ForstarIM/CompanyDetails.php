<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	
	$name		=	"";
	$address		=	"";
	$place		=	"";
	$pinCode	=	"";
	$country	 	=	"";
	$telNo		=	"";
	$faxNo		=	"";

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
	$bankRecSize = 0;

	# Edit Company Details 
	if ($p["editId"]=="") {
		$editIt		=	$p["editId"];
		$editMode	=	true;
		$companyRec	=	$companydetailsObj->find($editIt);
		//print_r($companyRec);
		$companyId	=	$companyRec[0];
		$name			=	stripSlash($companyRec[1]);
		$address			=	stripSlash($companyRec[2]);
		$place			=	stripSlash($companyRec[3]);
		$pinCode		=	stripSlash($companyRec[4]);
		$country			=	stripSlash($companyRec[5]);
		$telNo			=	stripSlash($companyRec[6]);
		$faxNo			=	stripSlash($companyRec[7]);
		$vatTin			=	stripSlash($companyRec[8]);
		$cstTin			= 	stripSlash($companyRec[9]);
		
		$range			=	stripSlash($companyRec[10]);
		$division		=	stripSlash($companyRec[11]);
		$commissionerate	=	stripSlash($companyRec[12]);
		$exciseNo		=	stripSlash($companyRec[13]);
		$notificationDetails	=	stripSlash($companyRec[14]);
		$panNo			= 	$companyRec[15];
		$emailid=$companyRec[16];
		$state=$companyRec[17];
		$phoneno2=$companyRec[18];

		
	}

	# Update Company Rec
	if ($p["cmdSaveChange"]!="") {
		
		$companyId			=	$p["hidCompanyId"];
		$name					=	addSlash(trim($p["companyName"]));
		$address					=	addSlash(trim($p["companyAddress"]));
		$place					=	addSlash(trim($p["companyPlace"]));
		$pinCode				=	addSlash(trim($p["companyPinCode"]));
		$country					=	addSlash(trim($p["companyCountry"]));
		$telNo					=	addSlash(trim($p["companyTelNo"]));
		$faxNo					=	addSlash(trim($p["companyFaxNo"]));
		$vatTin					=	addSlash(trim($p["vatTin"]));
		$cstTin					= 	addSlash(trim($p["cstTin"]));

		$range					=	addSlash(trim($p["range"]));
		$divisio				=	addSlash(trim($p["division"]));
		$commissionerate		=	addSlash(trim($p["commissionerate"]));
		$exciseNo				=	addSlash(trim($p["exciseNo"]));
		$notificationDetails	=	addSlash(trim($p["notificationDetails"]));
		$panNo					= trim($p["panNo"]);
		$tblRowCount			= $p["hidTableRowCount"];
		$emailid=$p["emailid"];
		$state=$p["companyState"];
		$phoneno2=$p["companyTelNo2"];
				
		if ($name!="") {
			$companyRecUptd = $companydetailsObj->updateCompany($companyId, $name, $address, $place, $pinCode, $country, $telNo, $faxNo, $vatTin, $cstTin, $range,$division,$commissionerate,$exciseNo,$notificationDetails, $panNo,$emailid,$state,$phoneno2);

			# bank AC
			if ($tblRowCount>0) {		
				for ($i=0; $i<$tblRowCount; $i++) {
					$status 	= $p["status_".$i];
					$bankACEntryId	= $p["bankACEntryId_".$i];					
					
					if ($status!='N') {
						$accountNo	= trim($p["accountNo_".$i]);
						$bankName	= trim($p["bankName_".$i]);							
						$bankAddr	= addSlash(trim($p["bankAddr_".$i]));
						$bankADCode = addSlash(trim($p["bankADCode_".$i]));

						if ($bankName!="" && $bankACEntryId=="") {
							# Add Bank AC
							$cmpnyBankACRecIns = $companydetailsObj->addCompanyBankAC($companyId, $accountNo, $bankName, $bankAddr, $bankADCode);				
						} else if ($bankName!="" && $bankACEntryId!="") {
							# update Bank AC
							$updateCmpnyBankACRec = $companydetailsObj->updateCompanyBankAC($bankACEntryId, $accountNo, $bankName, $bankAddr, $bankADCode);				
						}
					} // Status Ends here
					
					# Need to check bank ac in use
					if ($status=='N' && $bankACEntryId!="") {
						$bankAcInUse   = $companydetailsObj->chkBankAcInUse($bankACEntryId);
						if (!$bankAcInUse) $delBankACRec = $companydetailsObj->delBankAC($bankACEntryId);
					} 
				} // For loop ends here
			} // Tble row ends here
		}
	
		if ($companyRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succCompanyUpdate);
			//$sessObj->createSession("nextPage",$url_afterUpdateCompany);
		} else {
			$editMode	=	true;
			$err		=	$msg_failCompanyUpdate;
		}
		$companyRecUptd		=	false;
	}
	
	# bank Recs
	$bankACRecs		= $companydetailsObj->getCompanyBankACRecs($companyId);
	$bankRecSize	= sizeof($bankACRecs);



	# For Printing JS in Head section
	$ON_LOAD_PRINT_JS = "libjs/CompanyDetails.js"; 
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmCompanyDetails" action="CompanyDetails.php" method="post">
<table cellspacing="0"  align="center" cellpadding="0" width="96%">
	<? if($err!="" ){?>	
	<tr>
		<td height="10" align="center" class="err1" ><?=$err;?></td>
	</tr>
	<?}?>
	<?
		if ($editMode || $addMode) {
	?>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<?php	
							$bxHeader="COMPANY DETAILS";
							include "template/boxTL.php";
						?>
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td align="center" colspan="2">
									<? if($edit){?>
										<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateCompanyDetails(document.frmCompanyDetails);">
									<? }?>		
								</td>
							</tr>
							<tr>
							<td width="50%">
							<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">

										<tr>
											<td colspan="2" height="10" ></td>
										</tr>
										<tr>											
											<td colspan="2" align="center">
											<? if($editMode){?>
											<? if($edit){?>
												<!--input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateCompanyDetails(document.frmCompanyDetails);"-->
											<?}?>		
											<?}?>
										</td>
										</tr>
										<input type="hidden" name="hidCompanyId" value="<?=$companyId;?>">
										<tr>
											<td colspan="2"  height="10" ></td>
										</tr>
	<tr>
	<td colspan="2" align="center">
		<table align="center" width="70%">
			<TR>
			  <TD align="center">
				<?php			
					$entryHead = "";
					require("template/rbTop.php");
				?>
				<table align="center" style="padding-top:10px; padding-bottom:10px;">
				<tr>
											<td class="fieldName" nowrap >*Name</td>
											<td >
											<INPUT TYPE="text" NAME="companyName" size="40" value="<?=$name;?>"></td>
										</tr>
										<tr>
											<td class="fieldName" nowrap >*Address</td>
											<td ><textarea name="companyAddress" cols="27" rows="4"><?=$address;?></textarea></td>
										</tr>
										
										<tr>
											<td class="fieldName" nowrap >*Place</td>
											<td >
											<INPUT TYPE="text" NAME="companyPlace" size="30" value="<?=$place;?>"></td>
										<td nowrap class="fieldName" > </td>
										</tr>
										<tr>
										<td class="fieldName" nowrap >*Pin Code</td>
										<td ><input type="text" name="companyPinCode" size="10" value="<?=$pinCode;?>" /></td>
								</tr>
								  <tr>
										  <td class="fieldName" nowrap >*State</td>
										  <td ><input name="companyState" type="text" id="companyState" value="<?=$state;?>" size="10" /></td>
								  </tr>
										<tr>
										<td class="fieldName" nowrap >*Country</td>
										<td ><input type="text" name="companyCountry" size="10" value="<?=$country;?>" /></td>
								</tr>
										<tr>
											<td class="fieldName" nowrap >*Tel.No1</td>
											<td >
											<INPUT TYPE="text" NAME="companyTelNo" size="30" value="<?=$telNo;?>"></td>
										</tr>
								  <tr>
										  <td class="fieldName" nowrap >*Tel.No2</td>
										  <td ><input type="text" name="companyTelNo2" size="30" value="<?=$phoneno2;?>" /></td>
								  </tr>
										<tr>
											<td class="fieldName" nowrap >Fax No</td>
											<td >
											<INPUT TYPE="text" NAME="companyFaxNo" size="30" value="<?=$faxNo;?>">
											</td>
										</tr>
										<tr>
										  <td class="fieldName" nowrap >Email Id</td>
										  <td ><label for="emailid"></label>
									      <input type="text" name="emailid" id="emailid" value="<?=$emailid;?>" /></td>
								  </tr>
										<tr>
											<td class="fieldName" nowrap >VAT TIN</td>
											<td >
											<INPUT TYPE="text" NAME="vatTin" size="30" value="<?=$vatTin;?>">
											</td>
										</tr>
										<tr>
											<td class="fieldName" nowrap >CST TIN</td>
											<td >
											<INPUT TYPE="text" NAME="cstTin" size="30" value="<?=$cstTin;?>">
											</td>
										</tr>
				</table>
			<?php
				require("template/rbBottom.php");
			?>
			</td></tr>
		</table>
	</td>
</tr>
<!-- vineeth 20/04/11 starts--------- -->
										<tr>
											<td colspan="2" align="center">
												<table align="center" width="70%">
													<TR><TD align="center">
														<?php			
															$entryHead = "";
															require("template/rbTop.php");
														?>
														<table align="center" style="padding-top:10px; padding-bottom:10px;">
		<!--tr>
				<td class="fieldName" nowrap >*Chapter No./Heading/Subheading</td>
				<td >
						<INPUT TYPE="text" NAME="chapNo" size="30" value="<?=$chapterNo;?>">
				</td>
				<td nowrap class="fieldName" > </td>
		</tr>
		<tr>
				<td class="fieldName" nowrap>Name of Excisable goods
				</td>
				<td >
						<INPUT TYPE="text" NAME="excGoodsName" size="30" value="<?=$excGoodsName;?>">
				</td>

		</tr-->
																<tr>
																		<td class="fieldName" nowrap>Notification Details
																		</td>
																		<td >
																				<INPUT TYPE="text" NAME="notificationDetails" size="30" value="<?=$notificationDetails;?>">
																		</td>

																</tr>
																<tr>
																		<td class="fieldName" nowrap>Range
																		</td>
																		<td >
																				<INPUT TYPE="text" NAME="range" size="30" value="<?=$range;?>">
																		</td>

																</tr>
																<tr>
																		<td class="fieldName" nowrap>Division
																		</td>
																		<td >
																				<INPUT TYPE="text" NAME="division" size="30" value="<?=$division;?>">
																		</td>

																</tr>
																<tr>
																		<td class="fieldName" nowrap>Commissionerate
																		</td>
																		<td >
																				<INPUT TYPE="text" NAME="commissionerate" size="30" value="<?=$commissionerate;?>">
																		</td>

																</tr>
																<tr>
																		<td class="fieldName" nowrap>ECC NO
																		</td>
																		<td >
																				<INPUT TYPE="text" NAME="exciseNo" size="30" value="<?=$exciseNo;?>">
																		</td>
																</tr>
	<tr>
			<td class="fieldName" nowrap>PAN NO</td>
			<td >
					<INPUT TYPE="text" NAME="panNo" size="30" value="<?=$panNo;?>">
			</td>
	</tr>
														</table>
													<?php
														require("template/rbBottom.php");
													?>
													</td></tr>
												</table>
											</td>
										</tr>			
<!--vineeth 20/04/11 ends----------- -->										
										<tr>
											<td colspan="2"  height="10" ></td>
										</tr>
										<tr>
										<td colspan="2" align="center">
										<? if($edit==true){?><!--input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateCompanyDetails(document.frmCompanyDetails);"--><? }?></td>	
										</tr>
										<tr>
											<td colspan="2"  height="10" ></td>
										</tr>
									</table>
							</td>
							<!--  BANK AC -->
							<td valign="top" nowrap style="padding-top:40px;" width="50%">
									<table width="70%">
											<tr>
		<TD colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top">
		<?php	
		$entryHead = "BANK ACCOUNT";
		require("template/rbTop.php");
		?>
		<table>
			<TR>
			<TD style="padding:15px;">
				<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblBankAc" class="newspaperType">
				<tr align="center">
					<th nowrap style="text-align:center;">Account No.</th>
					<th nowrap style="text-align:center;">Bank Name</th>
					<th nowrap style="text-align:center;">Bank Address</th>					
					<th nowrap style="text-align:center;" title="Authorised Foreign Exchange Dealer Code no alloted to the bank by RBI">AD code</th>
					<th>&nbsp;</th>
				</tr>
	<?php
	if ($bankRecSize>0) {
		$j = 0;
		foreach ($bankACRecs as $bcb) {
			$bankACEntryId 	= $bcb[0];
			$accountNo		= $bcb[1];
			$bankName 		= $bcb[2];			
			$bankAddress	= $bcb[3];
			$bankADCode		= $bcb[4];
	?>
	<tr align="center" class="whiteRow" id="row_<?=$j?>">
		<td align="center" class="listing-item">
			<input type="text" autocomplete="off" size="24" id="accountNo_<?=$j?>" name="accountNo_<?=$j?>" value="<?=$accountNo?>" >
		</td>
		<td align="center" class="listing-item">
			<input type="text" size="24" id="bankName_<?=$j?>" name="bankName_<?=$j?>" value="<?=$bankName?>">
		</td>
		<td align="center" class="listing-item">
			<textarea col="4" rows="4" id="bankAddr_<?=$j?>" name="bankAddr_<?=$j?>"><?=$bankAddress?></textarea>
		</td>
		<td align="center" class="listing-item">
			<input type="text" size="24" id="bankADCode_<?=$j?>" name="bankADCode_<?=$j?>" value="<?=$bankADCode?>">
		</td>
		<td align="center" class="listing-item">
			<a onclick="setCOBankACItemStatus('<?=$j?>');" href="###">
				<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item"></a>
				<input type="hidden" value="" id="status_<?=$j?>" name="status_<?=$j?>">
				<input type="hidden" value="N" id="IsFromDB_<?=$j?>" name="IsFromDB_<?=$j?>">
				<input type="hidden" value="<?=$bankACEntryId?>" id="bankACEntryId_<?=$j?>" name="bankACEntryId_<?=$j?>">
		</td>
	</tr>	
	<?php
				$j++;
			} // Loop ends here
		}
	?>	
	</table>
	<!--  Hidden Fields-->
	<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$bankRecSize?>" readonly="true">
	</TD>
	</TR>
	<tr><TD height="5"></TD></tr>
	<tr>
		<TD>
			<a href="###" id='addRow' onclick="javascript:addNewCOBankACItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New</a>
					</TD>
				</tr>
			</table>
		<?php
		require("template/rbBottom.php");
		?>
		</TD>
	</tr>
									</table>
							</td>
							<!--  BANK AC ENDS HERE-->
							</tr>
							<tr>
								<td align="center" colspan="2">
									<? if($edit){?>
										<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateCompanyDetails(document.frmCompanyDetails);">
									<? }?>		
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
		<tr>
			<td height="10"></td>
		</tr>	
</table>
<script language="JavaScript" type="text/javascript">
		function addNewCOBankACItem()
		{
			addNewCOBankAC('tblBankAc');
		}

		<?php
			if ($bankRecSize==0) {
		?>
			$(document).ready(function() {
				addNewCOBankACItem();
			});
		<?php
			} else if ($bankRecSize>0) {
		?>		
			fieldId = '<?=$bankRecSize?>';		
		<?php
			}
		?>
</script>
</form>
<?php
# Include Template [bottomRightNav.php]
require("template/bottomRightNav.php");
?>