<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$recUpdated 		= 	false;

	$mainSupplier	=	"";
	if ($g["sid"]=="") $supplierId=$p["hidSupplierId"];
	else $supplierId=$g["sid"];
		
	if ($g["name"]=="") $supplierName=$p["subSupplierMainSupplier"];
	else $supplierName=$g["name"];	

	#Supplier add Mode check
	/*if ($g["supplierMode"]=="") $supplierMode = $p["supplierMode"];
	else $supplierMode = $g["supplierMode"];*/

	#For Refreshing the main Window when click PopUp window
	if ($g["popupWindow"]=="") $popupWindow = $p["popupWindow"];
	else $popupWindow = $g["popupWindow"];
	

	# Add Sub Supplier Start
	if ($p["cmdAddNew"]!="") $addMode	=	true;
	
	# Add
	if ($p["cmdAddSubSupplier"]!="") {

		$Code			=	addSlash(trim($p["subSupplierCode"]));
		$Name			=	addSlash(trim($p["subSupplierName"]));
		
		$supplierId		=	$p["hidSupplierId"];
		
		$mainSupplier		=	addSlash($p["subSupplierMainSupplier"]);
		$Address		=	addSlash($p["subSupplierAddress"]);
		$Place			=	addSlash($p["subSupplierPlace"]);
		$Pincode		=	addSlash($p["subSupplierPincode"]);
		$TelNo			=	addSlash($p["subSupplierTelNo"]);
		$FaxNo			=	addSlash($p["subSupplierFaxNo"]);
		$Email			=	addSlash($p["subSupplierEmail"]);
		$LstNo			=	addSlash($p["subSupplierLstNo"]);
		$CstNo			=	addSlash($p["subSupplierCstNo"]);
		$PanNo			=	addSlash($p["subSupplierPanNo"]);

		
		if( $Code!="" && $Name!="" ) {

			$SubSupplierRecIns = $subsupplierObj->addSubSupplier($Code, $Name, $supplierId, $Address, $Place, $Pincode, $TelNo, $FaxNo, $Email, $LstNo, $CstNo, $PanNo);

			if ($SubSupplierRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddSubSupplier);
				$recUpdated = true;
				// $sessObj->createSession("nextPage",$url_afterAddSubSupplier);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddSubSupplier;
			}
			$SubSupplierRecIns		=	false;
		}
	}

	# Edit Sub Supplier 
	if ($p["editId"]!="") {
		$editIt			=	$p["editId"];
		$editMode		=	true;
		$subsupplierRec		=	$subsupplierObj->find($editIt);

		$Id			=	$subsupplierRec[0];
		$Code			=	stripSlash($subsupplierRec[1]);
		$Name			=	stripSlash($subsupplierRec[2]);
		$supplierId		=	$subsupplierRec[3];
		$Address		=	stripSlash($subsupplierRec[4]);
		$Place			=	stripSlash($subsupplierRec[5]);
		$Pincode		=	stripSlash($subsupplierRec[6]);
		$TelNo			=	stripSlash($subsupplierRec[7]);
		$FaxNo			=	stripSlash($subsupplierRec[8]);
		$Email			=	stripSlash($subsupplierRec[9]);
		$LstNo			=	stripSlash($subsupplierRec[10]);
		$CstNo			=	stripSlash($subsupplierRec[11]);
		$PanNo			=	stripSlash($subsupplierRec[12]);
	}

	# Update
	if ($p["cmdSaveChange"]!="") {
		
		$subsupplierId		=	$p["hidSubSupplierId"];
		$Code			=	addSlash(trim($p["subSupplierCode"]));
		$Name			=	addSlash(trim($p["subSupplierName"]));
		$supplierId		=	$p["hidSupplierId"];
		$Address		=	addSlash($p["subSupplierAddress"]);
		$Place			=	addSlash($p["subSupplierPlace"]);
		$Pincode		=	addSlash($p["subSupplierPincode"]);
		$TelNo			=	addSlash($p["subSupplierTelNo"]);
		$FaxNo			=	addSlash($p["subSupplierFaxNo"]);
		$Email			=	addSlash($p["subSupplierEmail"]);
		$LstNo			=	addSlash($p["subSupplierLstNo"]);
		$CstNo			=	addSlash($p["subSupplierCstNo"]);
		$PanNo			=	addSlash($p["subSupplierPanNo"]);
		
		if ($subsupplierId!="" && $Name!="" && $Code!="") {
			$subSupplierRecUptd =	$subsupplierObj->updateSubSupplier($subsupplierId, $Code, $Name, $supplierId, $Address, $Place, $Pincode, $TelNo, $FaxNo, $Email, $LstNo, $CstNo, $PanNo);
		}
	
		if ($subSupplierRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succSubSupplierUpdate);
			//$recUpdated = true;
			// $sessObj->createSession("nextPage", $url_afterUpdateSubSupplier);
		} else {
			$editMode	=	true;
			$err		=	$msg_failSubSupplierUpdate;
		}
		$subSupplierRecUptd	=	false;
	}

	# Delete Sub Supplier
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$subsupplierId	=	$p["delId_".$i];
			if ($subsupplierId!="" ) {
				if (!$subsupplierObj->checkMoreEntriesExist($subsupplierId)) {
					$subSupplierRecDel = $subsupplierObj->deleteSubSupplier($subsupplierId);
				}
			}
		}
		if ($subSupplierRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSubSupplier);
			$recUpdated = true;
			// $sessObj->createSession("nextPage",$url_afterDelSubSupplier);
		} else {
			$errDel	=	$msg_failDelSubSupplier;
		}
		$subSupplierRecDel	=	false;
	}


	#List All Sub Suppliers	
	$landingCenterId	= $p["landingCenter"];
	if ($landingCenterId!="") {
		$subSupplierRecords = $subsupplierObj->fetchAllFilterRecords($landingCenterId, $supplierId);
	} else {
		$subSupplierRecords = $subsupplierObj->filterRecords($supplierId);
	}
	$subSupplierSize	= sizeof($subSupplierRecords);
	
	if ($editMode) $heading = $label_editSubSupplier;
	else $heading = $label_addSubSupplier;
	
	#For getting Landing Center List based on supplier
	$landingcenterList = $subsupplierObj->filterLandingCenterRecords($supplierId);
	
	$ON_LOAD_PRINT_JS	= "libjs/subsupplier.js";

	# Include Template [topLeftNav.php]
	require("template/btopLeftNav.php");
?>
<form name="frmSubSupplier" action="SubSuppliers.php" method="post">
<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<? if($err!="" ){?>
	<tr>
		<td align="center" class="err1" ><?=$err;?></td>
	</tr>
	<?}?>
	<tr>
		<td height="10" align="center" ></td>
	</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="SHIP OWNERS/SUB SUPPLIERS";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
	<tr>
		<td colspan="3" align="center">
		<table width="90%" align="center">
<?
		if ($editMode || $addMode) {
	?>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%">
				<tr>
					<td>
						<!-- Form fields start -->
						<?php			
							$entryHead = $heading;
							require("template/rbTop.php");
						?>
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<!--<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="461" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
							</tr>-->
							<tr>
								<td width="1" ></td>
							  <td colspan="2" >
							    <table cellpadding="2"  width="90%" cellspacing="0" border="0" align="center">
										<tr>
											<td colspan="2" height="10" ></td>
										</tr>
										<tr>
											<? if($editMode){?>

											<td colspan="2" align="center">
											<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('SubSuppliers.php');">&nbsp;&nbsp;
											<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddSubSupplier(document.frmSubSupplier);">											</td>
											
											<?} else{?>

											<td  colspan="2" align="center">
											<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('SubSuppliers.php');">&nbsp;&nbsp;
											<input type="submit" name="cmdAddSubSupplier" class="button" value=" Add " onClick="return validateAddSubSupplier(document.frmSubSupplier);">											</td>

											<?}?>
										</tr>
										<input type="hidden" name="hidSubSupplierId" value="<?=$Id;?>">
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<td colspan="2" align="center">
		<table cellpadding="2" cellspacing="0">
		<TR>
		<TD valign="top">
		<table>
			<tr>
											<td class="fieldName" nowrap align="right" >*Code</td>
											<td><INPUT TYPE="text" NAME="subSupplierCode" size="15" value="<?=$Code;?>"></td>
										</tr>
										<tr>
											<td class="fieldName" nowrap align="right">*Name</td>
											<td ><INPUT TYPE="text" NAME="subSupplierName" size="30"  maxlength="30" value="<?=$Name;?>">											</td>
										</tr>
										<tr>
										  <td class="fieldName" nowrap align="right" >Main Supplier </td>			
										  <td class="listing-item"><?=$supplierName;?></td>
										  </tr>
										<tr>
											<td class="fieldName" nowrap align="right">Address</td>
											<td ><textarea name="subSupplierAddress" cols="27" rows="4"><?=$Address;?></textarea></td>
										</tr>								
										<tr>
											<td class="fieldName" nowrap align="right">*Place</td>
											<td >
	<select name="subSupplierPlace" title="select Main Supplier Landing Center"><option value="">--Select--</option>
											<?
											  foreach($landingcenterList as $landingcenterRecord)
											  {
											  	$select="";
												if($Place == $landingcenterRecord[0])$select="Selected"
											  	?>
												<option value="<?=$landingcenterRecord[0];?>" <?=$select?>><?=$landingcenterRecord[1];?></option>
												<?
												}
												?>
											</select>		
									</td>
										</tr>
										<tr>
										  <td class="fieldName" nowrap align="right">Pin Code </td>
										  <td ><input type="text" name="subSupplierPincode" size="10" value="<?=$Pincode;?>" /></td>
								  </tr>
		</table>
		</TD>
		<TD>&nbsp;</TD>
		<TD valign="top">
		<table>
			<tr>
											<td class="fieldName" nowrap align="right">*Tel.No</td>
											<td ><INPUT TYPE="text" NAME="subSupplierTelNo" size="30" value="<?=$TelNo;?>">				</td>
										</tr>
										<tr>
											<td class="fieldName" nowrap align="right">Fax No</td>
											<td ><input type="text" name="subSupplierFaxNo" size="30" value="<?=$FaxNo;?>" /></td>
										</tr>
										<tr>
											<td class="fieldName" nowrap align="right">Email</td>
											<td ><INPUT TYPE="text" NAME="subSupplierEmail" size="40" value="<?=$Email;?>">				</td>
										</tr>
										<tr>
											<td class="fieldName" nowrap align="right">LST No</td>
											<td ><INPUT TYPE="text" NAME="subSupplierLstNo" size="30" value="<?=$LstNo;?>">				</td>
										</tr>
										<tr>
										  <td class="fieldName" nowrap align="right">CST No</td>
										  <td ><input type="text" name="subSupplierCstNo" size="10" value="<?=$CstNo;?>" /></td>
								  </tr>
										<tr>
											<td class="fieldName" nowrap align="right">PAN No</td>
											<td ><INPUT TYPE="text" NAME="subSupplierPanNo" size="30" value="<?=$PanNo;?>">	
											</td>
										</tr>
		</table>
		</TD>	
		</TR>
		</table>
		</td>
	</tr>
										
										
										<tr>
											<td colspan="5"  height="10" ></td>
										</tr>
										<tr>
											<? if($editMode){?>

											<td colspan="2" align="center">
											<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('LandingCenter.php');">&nbsp;&nbsp;
											<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddSubSupplier(document.frmSubSupplier);">											</td>
											
											<?} else{?>

											<td  colspan="2" align="center">
											<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('LandingCenter.php');">&nbsp;&nbsp;
											<input type="submit" name="cmdAddSubSupplier" class="button" value=" Add " onClick="return validateAddSubSupplier(document.frmSubSupplier);">											</td>

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
	<?php 
		if ($addMode || $editMode) {
	?>
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	<?php
		}
	?>
<tr>
	<td colspan="3" align="center">
		<table width="20%" align="center">
		<TR><TD align="center">
			<?php			
				$entryHead = "";
				require("template/rbTop.php");
			?>
			<table width="70%" align="center" cellpadding="4" cellspacing="0" style="padding-top:5px; padding-bottom:5px;" border="0">	
				<tr>
					<td class="listing-item" nowrap align="right">Landing Center </td>
					<td>
						<select name="landingCenter" onchange="this.form.submit();">
						<option value="">Select All</option>
						<?
						  foreach($landingcenterList as $landingcenterRecord) {
						  	$select="";
							if($landingCenterId == $landingcenterRecord[0])$select="Selected"
					  	?>
						<option value="<?=$landingcenterRecord[0];?>" <?=$select?>><?=$landingcenterRecord[1];?></option>
						<?
							}
						?>
						</select>
					</td>				
				</tr>
			  </table>
			<?php
				require("template/rbBottom.php");
			?>
		</td>
		</tr>
		</table>	
	</td>
</tr>								
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap="nowrap">&nbsp;SHIP OWNERS/SUB SUPPLIERS </td>
								    <td background="images/heading_bg.gif" class="pageName" ><table width="90%" align="right" cellpadding="0" cellspacing="0">							
								  </table></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$subSupplierSize;?>);" >&nbsp;<input type="submit" value=" Add New " name="cmdAddNew" class="button"  >&nbsp;<input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintSubSupplier.php?supplierId=<?=$supplierId?>',700,600);"></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if($errDel!="")
									{
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
							<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
												if( sizeof($subSupplierRecords) > 0 )
												{
													$i	=	0;
											?>
							<thead>
											<tr>
												<th width="21"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
												<th nowrap >Code</th>
												<th>Name</th>
												<th align="center">Main Supplier</th>
												<th align="center">Landing Center </th>
												<th>&nbsp;</th>
											</tr>
							</thead>
							<tbody>
											<?

													foreach($subSupplierRecords as $fr)
													{
														$i++;
														$subSupplierId	=	$fr[0];
														$subSupplierName	=	stripSlash($fr[1]);
														$subSupplierCode	=	stripSlash($fr[2]);
														$mainSupplierCode	=	stripSlash($fr[3]);
														//landingCenterId		=	$fr[4];
														
														$centerRec =$landingcenterObj->find($fr[4]);
														$landingCenterName=stripSlash($centerRec[1]);
											?>
											<tr>
											<td width="21" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$subSupplierId;?>" class="chkBox"></td>
											<td class="listing-item" nowrap><?=$subSupplierCode;?></td>
											<td class="listing-item" nowrap><?=$subSupplierName;?></td>
										        <td class="listing-item" nowrap><?=$supplierName;?></td>
									            	<td class="listing-item" nowrap><?=$landingCenterName;?></td>
								              		<td class="listing-item" width="45" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$subSupplierId;?>,'editId'); this.form.action='SubSuppliers.php';"  ></td>
											</tr>
											<?
													}
											?>
												
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="">
											<?
												}
												else
												{
											?>
											<tr>
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
								</tbody>
								</table>									
								</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$subSupplierSize;?>);" >&nbsp;<input type="submit" value=" Add New " name="cmdAddNew" class="button" >&nbsp;<input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintSubSupplier.php?supplierId=<?=$supplierId?>',700,600);"></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
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
		
		<tr>
			<td height="30"></td>
		</tr>
  </table>
<input type="hidden" name="hidSupplierId" value="<?=$supplierId;?>" />
  <input type="hidden" name="subSupplierMainSupplier" id="subSupplierMainSupplier" value="<?=$supplierName;?>">
<!--input type="hidden" name="supplierMode" value="<?=$supplierMode?>"-->
<!--input type="text" name="hidSubSupplierSize" value="<?=$subSupplierSize?>"-->
<input type="hidden" name="popupWindow" value="<?=$popupWindow?>">

<? if ($recUpdated==true && $popupWindow!="") {?>
<script>
closeWindow();
function closeWindow()
{
	var myParentWindow = opener.document.forms.frmSupplier;
	myParentWindow.submit();
	//alert (myParentWindow);
}
</script>
<? }?>
</form>
	<?
	# Include Template [bottomRightNav.php]
	//require("template/bbottomRightNav.php");
	?>
