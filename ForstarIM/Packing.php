<?
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$unit			=	"Kg";
	$unit			=	"";
	$packingCode	=	"";
	
//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if(!$accesscontrolObj->canAccess())
			{ 
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
	
	# Add Packing Code
	
	if( $p["cmdAddNew"]!="" ){
		$addMode	=	true;
	}

	if( $p["cmdAddPacking"]!="" ){
	
		$code			=	addSlash($p["packingCode"]);
		$descr			=	addslash($p["packingDescr"]);
		$weight			=	$p["packingWeight"];		
		$unit			=	$p["selUnit"];
		
		if( $code!="" )
		{
			$packingRecIns	=	$packinggoodsObj->addPacking($code,$descr,$weight,$unit);
			if($packingRecIns)
			{
				$sessObj->createSession("displayMsg",$msg_succAddPackingGoods);
				$sessObj->createSession("nextPage",$url_afterAddPackingGoods);
			}
			else
			{
				$addMode		=	true;
				$err			=	$msg_failAddPackingGoods;
			}
			$packingRecIns	=	false;
		}
	}
	
	
	# Edit Packing
	
	if( $p["editId"]!="" ){
		$editIt			=	$p["editId"];
		$editMode		=	true;
		$packingRec		=	$packinggoodsObj->find($editIt);
		
		$packingId		=	$packingRec[0];
		$packingCode	=	stripSlash($packingRec[1]);
		$description	=	stripSlash($packingRec[2]);
		$weight			=	$packingRec[3];
		$unit			=	$packingRec[4];
	
	}

	if( $p["cmdSaveChange"]!="" ){
		
		$packingId		=	$p["hidPackingId"];
		$code			=	addSlash($p["packingCode"]);
		$descr			=	addslash($p["packingDescr"]);
		$weight			=	$p["packingWeight"];		
		$unit			=	$p["selUnit"];
		
		if( $packingId!="" && $code!="" )
		{
			$packingRecUptd	=	$packinggoodsObj->updatePacking($packingId,$code,$descr,$weight,$unit);
		}
	
		if($packingRecUptd)
		{
			$sessObj->createSession("displayMsg",$msg_succUpdatePackingGoods);
			$sessObj->createSession("nextPage",$url_afterUpdatePackingGoods);
		}
		else
		{
			$editMode	=	true;
			$err		=	$msg_failUpdatePackingGoods;
		}
		$packingRecUptd	=	false;
	}


	# Delete Grade

	if( $p["cmdDelete"]!=""){

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$packingId	=	$p["delId_".$i];

			if( $packingId!="" )
			{
				// Need to check the selected grade is link with any other process - confirm

				$packingRecDel		=	$packinggoodsObj->deletePacking($packingId);
				
			}

		}
		if($packingRecDel)
		{
			$sessObj->createSession("displayMsg",$msg_succDelPackingGoods);
			$sessObj->createSession("nextPage",$url_afterDelPackingGoods);
		}
		else
		{
			$errDel	=	$msg_failDelPackingGoods;
		}

		$packingRecDel	=	false;

	}


	#List All Packing Goods

	$packingGoodsRecords		=	$packinggoodsObj->fetchAllRecords();
	$packingGoodsRecordSize	=	sizeof($packingGoodsRecords);
	


	if($editMode)	{
		$heading	=	$label_editPackingGoods;
	}
	else{
		$heading	=	$label_addPackingGoods;
	}

	$help_lnk="help/hlp_Packing.html";
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

?>
	<form name="frmPackingGoods" action="Packing.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="50%">
			<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
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

												<td colspan="2" align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('Packing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddPacking(document.frmPackingGoods);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('Packing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddPacking" class="button" value=" Add " onClick="return validateAddPacking(document.frmPackingGoods);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidPackingId" value="<?=$packingId;?>">
											<tr>
												<td class="fieldName" nowrap >*Code</td>
												<td><INPUT TYPE="text" NAME="packingCode" size="20" value="<?=$packingCode;?>"></td>
											</tr>
											<tr>
												<td class="fieldName">Description</td>
											    <td><input type="text" name="packingDescr" size="25" value="<?=$description;?>" /></td>
											</tr>
											<tr>
												<td class="fieldName" nowrap >*Packed Weight </td>
												<td >
												<INPUT TYPE="text" NAME="packingWeight" size="10" value="<?=$weight;?>">
												&nbsp;<select name="selUnit" id="selUnit">
												<option value="Kg" <?if($unit=="Kg") echo "selected";?>>Kg</option>
												<option value="Lb" <?if($unit=="Lb") echo "selected";?> >Lb</option>
												</select>												</td>
											</tr>
											
											<tr>
												<td colspan="2"  height="10" >&nbsp;</td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Packing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddPacking(document.frmPackingGoods);">												</td>
												<? } else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Packing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddPacking" class="button" value=" Add " onClick="return validateAddPacking(document.frmPackingGoods);">												</td>

												<? }?>
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
			
			# Listing Grade Starts
		?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;FINISHED GOODS MASTER</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$packingGoodsRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintPackingGoods.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
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
										<table cellpadding="1"  width="85%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($packingGoodsRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2"  >
												<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></td>
												<td class="listing-head" nowrap>&nbsp;&nbsp;Code</td>
												<td class="listing-head" >&nbsp;&nbsp;Description</td>
												<td class="listing-head" align="center" >Packed Weight&nbsp;&nbsp; </td>
												<td class="listing-head" align="center" width="45">Unit</td>
												<? if($edit==true){?>
												<td class="listing-head" width="45"></td>
												<? }?>
											</tr>
											<?

													foreach($packingGoodsRecords as $pg)
													{
														$i++;
														$packingId		=	$pg[0];
														$packingCode	=	stripSlash($pg[1]);
														$description	=	stripSlash($pg[2]);
														$weight			=	$pg[3];
														$unit			=	$pg[4];
														
											?>
											<tr  bgcolor="WHITE"  >
												<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$packingId;?>" ></td>
												<td class="listing-item" nowrap >&nbsp;&nbsp;<?=$packingCode;?></td>
												<td class="listing-item" nowrap="nowrap" >&nbsp;&nbsp;<?=$description;?>&nbsp;</td>
												<td class="listing-item" align="right"><?=$weight;?>&nbsp;&nbsp;&nbsp;</td>
												<td class="listing-item" align="center"><?=$unit;?></td>
												<? if($edit==true){?>
											  <td class="listing-item" width="45" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$packingId;?>,'editId'); this.form.action='Packing.php';"></td>
											  <? }?>
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
											<tr bgcolor="white">
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$packingGoodsRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintPackingGoods.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>	
	</table>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>