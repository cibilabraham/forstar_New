<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	
	$packingMaterialCost = "PMC";
	$selection 	= "?pageNo=".$p["pageNo"]."&selRateList=".$p["selRateList"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/



	

	#Update a Record
	if ($p["cmdSaveChange"]!="") {	

		$rateSize     = $p["rateSize"];
		
		for($i=0; $i<$rateSize; $i++)
		{
			$rateId=$p["rateId_".$i];
			$stockId=$p["stockId_".$i];
			$rate=$p["rate_".$i];
			//echo "sdfg".$rateId.'--'.$stockId.'--'.$rate;
			if($stockId!="" && $rate!="" && $rateId!="") 
			{
				$packingMaterialRecUptd = $packingMaterialObj->updatePackingMaterialRec($rateId, $stockId, $rate,$userId);
			}
			else if($stockId!="" && $rate!="" && $rateId=="") 
			{
				$packingMaterialRecUptd = $packingMaterialObj->addPackingMaterialRec($stockId, $rate,$userId);
			}
	
		}

		//die();
		if ($packingMaterialRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succPackingMaterialUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePackingMaterial.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failPackingMaterialUpdate;
		}
		$packingMaterialRecUptd	=	false;
	}


	


	
	/*
	#----------------Rate list--------------------	
	if ($g["selRateList"]!="") $selRateList	= $g["selRateList"];
	else if($p["selRateList"]!="") $selRateList	= $p["selRateList"];
	else $selRateList = $manageRateListObj->latestRateList($packingMaterialCost);			
	#--------------------------------------------
	*/

	


	
	$stockRecords	= $packingMaterialObj->getStockRTE();
	#heading Section
	if ($editMode) $heading	=	$label_editPackingMaterialCost;
	
	$ON_LOAD_PRINT_JS = "libjs/PackingMaterialCost.js";

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
<form name="frmPackingMaterial" action="PackingMaterial.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr>
			<TD height="10"></TD>
		</tr>
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
							$bxHeader = "Packing Material Master";
							include "template/boxTL.php";
						?>
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td colspan="3" align="center">
									<Table width="40%">	
									<?
									//	if ( $editMode || $addMode) {
									?>
										<tr>
											<td>
												<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
													<tr>
														<td>
															<!-- Form fields start -->
															<?php							
																$entryHead = "Packing Material Cost ";;
																require("template/rbTop.php");
															?>
															<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
																<!--<tr>
																	<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
																	<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
																</tr>-->
																<tr><TD height="10"></TD></tr>
																<tr>
																	<td width="1" ></td>
																	<td colspan="2" >
																	
																		<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
																			<tr>
																				<td colspan="2" height="10" ></td>
																			</tr>
																			
																			<tr>
																				
																				<td colspan="2" align="center">
																					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes "  />
																				</td>
																				
																			</tr>
																			<tr>
																				<td colspan="2"  height="10" ></td>
																			</tr>
																		
																			<tr>
																				<td colspan="2" nowrap align="center">
																				<?php							
																					$entryHead = "";
																					require("template/rbTop.php");
																				?>
																					<table  id="newspaper-b1" cellspacing="1" cellpadding="1">
																						<? 
																						if(sizeof($stockRecords)>0)
																						{
																						?>
																							<tr bgcolor="white">
																								<td class="listing-head">
																								Stock Name
																								</td>
																								<td class="listing-head">
																								Last Purchase Order
																								</td>
																								
																								<td class="listing-head">
																								 Maximum Rate
																								</td>
																								<td class="listing-head">
																								 Minimum Rate
																								</td>
																								<td class="listing-head">
																								Rate
																								</td>
																							</tr>
																							<?
																							$i=0;
																							foreach($stockRecords as $sR)
																							{
																								$stockId=$sR[0];
																								$stockName=$sR[1];
																								$supplierCnt=$packingMaterialObj->getSupplierCount($stockId);
																								$purchasePrice=$packingMaterialObj->lastPurchasedPrice($stockId);
																								list($rateId,$rate)=$packingMaterialObj->getPackingMaterialRate($stockId);
																							?>
																							<tr>
																								<td class="listing-item">
																								<?=$stockName?>
																								<input type="hidden" name="stockId_<?=$i?>" id="stockId_<?=$i?>" value="<?=$stockId?>">
																								</td>
																								<td>
																								<?=$purchasePrice?>
																								</td>
																								<td>
																								<? 
																								if($supplierCnt>1)
																								{
																									$maxRate=$packingMaterialObj->getMaxPrice($stockId);
																									($maxRate!="")?$maxRate=$maxRate:$maxRate="";
																									echo $maxRate;
																								}
																								?>
																								</td>
																								<td>
																								<? 
																								if($supplierCnt>1)
																								{
																									$minRate=$packingMaterialObj->getMinPrice($stockId);
																									($minRate!="")?$minRate=$minRate:$minRate="";
																									echo $minRate;
																								}
																								?>
																								</td>
																								
																								<td>
																								<input type="hidden" name="rateId_<?=$i?>" id="rateId_<?=$i?>" value="<?=$rateId?>" size="10"/>
																								<input type="text" name="rate_<?=$i?>" id="rate_<?=$i?>" value="<?=$rate?>" size="10"/>
																							
																								</td>
																							</tr>
																							<?
																							$i++;
																							}
																							
																						}
																						?>
																					</table>
																				<?php
																				require("template/rbBottom.php");
																				?>
																				</td>
																			</tr>
																			<tr>
																				<td colspan="2"  height="10" >
																				<input type="hidden" id="rateSize" name="rateSize" value="<?=$i?>"/>
																				</td>
																			</tr>
																			<tr>
																				<td colspan="2"  height="5" ></td>
																			</tr>
																			<tr>
																				<td colspan="2" align="center">
																					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes "  />
																				</td>
																				<input type="hidden" name="cmdAddNew" value="1">
																			
																			</tr>
																			
																		</table>
																		
																	</td>
																</tr>
																<tr><TD height="10"></TD></tr>
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
									//	}			
										# Listing Category Starts
										?>	
									</table>
								</td>
							</tr>	
							<tr>
								<td height="10" align="center" ></td>
							</tr>
							<tr>
								<td colspan="3" align="center">

									<table width="30%">
										<TR>
											<TD>
											<?php
											/*
												$entryHead = "";
												require("template/rbTop.php");
											?>
												<table cellpadding="4" cellspacing="0">
													<tr>
														<td nowrap="nowrap" style="padding:5px;">
															<table width="200" border="0">
															  <tr>
																<td class="fieldName" nowrap>Rate List </td>
																<td>
																	<select name="selRateList" id="selRateList" onchange="this.form.submit();">
																		<option value="">-- Select --</option>
																		<?php
																		foreach ($pmcRateListRecords as $prl) {
																			$mRateListId	= $prl[0];
																			$rateListName	= stripSlash($prl[1]);
																			$startDate	= dateFormat($prl[2]);
																			$displayRateList = $rateListName."&nbsp;(".$startDate.")";
																			$selected =  ($selRateList==$mRateListId)?"Selected":"";
																		?>
																		<option value="<?=$mRateListId?>" <?=$selected?>><?=$displayRateList?></option>
																		 <? }?>
																	</select>
																</td>
																<? if($add==true){?>
																<td>
																	<input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value=" Add New Rate List" onclick="this.form.action='ManageRateList.php?mode=AddNew&selPage=<?=$packingMaterialCost?>'">
																</td>
																<? }?>
															</tr>
														</table>
													</td>
												</tr>
											</table>
											<?php
												require("template/rbBottom.php");
												*/
											?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="3" height="10" ></td>
						</tr>
						<!--
						<tr>	
							<td colspan="3">
								<table cellpadding="0" cellspacing="0" align="center">
									<tr>
										<td>
											<? if($del==true){?>
											<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$packingMaterialCostRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPackingMaterialCost.php?selRateList=<?=$selRateList?>',700,600);"><? }?>
										</td>
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
							<td colspan="2" style="padding-left:10px;padding-right:10px;" >
 								<table cellpadding="2"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
								<?
								if ($packingMaterialCostRecordSize) {
									$i	=	0;
								?>
									<thead>
									<? if($maxpage>1){?>
										<tr>
											<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
												<div align="right">
												<?php
												$nav  = '';
												for ($page=1; $page<=$maxpage; $page++) {
													if ($page==$pageNo) {
															$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
													} else {
															$nav.= " <a href=\"PackingMaterialCost.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
														//echo $nav;
													}
												}
												if ($pageNo > 1) {
													$page  = $pageNo - 1;
													$prev  = " <a href=\"PackingMaterialCost.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
												} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage) {
													$page = $pageNo + 1;
													$next = " <a href=\"PackingMaterialCost.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
												} else {
													$next = '&nbsp;'; // we're on the last page, don't print next link
													$last = '&nbsp;'; // nor the last page link
												}
												// print the navigation link
												$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
												echo $first . $prev . $nav . $next . $last . $summary; 
												?>	
												<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
												</div> 
											</td>
										</tr>
										<? }?>
										<tr align="center">
											<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></th>
											<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
											<th class="listing-head" style="padding-left:10px; padding-right:10px;">Code</th>
											<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Cost</th>	
											<? if($edit==true){?>
											<th class="listing-head">&nbsp;</th>
											<? }?>
											<? if($confirm==true){?>
											<th class="listing-head">&nbsp;</th>
											<? }?>
										</tr>
									</thead>
									<tbody>		
									<?php
									while ($pmcr=$packingMaterialCostResultSetObj->getRow()) {
										$i++;
										$packingMaterialCostRecId = $pmcr[0];				
										$materialName		  = $pmcr[4];
										$materialCode		  = $pmcr[5];
										$materialCost		  = $pmcr[3]; 
										$active=$pmcr[8];
									?>
										<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?> >
											<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$packingMaterialCostRecId;?>" class="chkBox"></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$materialName?></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$materialCode?></td>
											<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$materialCost?></td>
											<? if($edit==true){?>
											<td class="listing-item" width="60" align="center">
											<?php if ($active==0){ ?>
												<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$packingMaterialCostRecId;?>,'editId');assignValue(this.form,'1','editSelectionChange'); this.form.action='PackingMaterialCost.php';" >
											<? } ?>
											</td>
											<? }?>
											<? if ($confirm==true){?>
											<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
												<?php if ($active==0){ ?>
												<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$packingMaterialCostRecId;?>,'confirmId');" >
												<?php } else if ($active==1){ if ($existingcount==0) {?>
												<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$packingMaterialCostRecId;?>,'confirmId');" >
												<?php } ?>
												<?php }?>
												<? }?>
											</td>
										</tr>
										<?
										}
										?>
										<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
										<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
										<input type="hidden" name="editSelectionChange" value="0">
										<? if($maxpage>1){?>
										<tr>
											<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
												<div align="right">
												<?php
												$nav  = '';
												for ($page=1; $page<=$maxpage; $page++) {
													if ($page==$pageNo) {
															$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
													} else {
															$nav.= " <a href=\"PackingMaterialCost.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
														//echo $nav;
													}
												}
												if ($pageNo > 1) {
													$page  = $pageNo - 1;
													$prev  = " <a href=\"PackingMaterialCost.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
												} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage) {
													$page = $pageNo + 1;
													$next = " <a href=\"PackingMaterialCost.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
												} else {
													$next = '&nbsp;'; // we're on the last page, don't print next link
													$last = '&nbsp;'; // nor the last page link
												}
												// print the navigation link
												$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
												echo $first . $prev . $nav . $next . $last . $summary; 
												?>	
												<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
												</div> 
											</td>
										</tr>
										<? }?>
										<?
										}
										else {
										?>
										<tr>
											<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
										<td>
										<? if($del==true){?>
											<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$packingMaterialCostRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPackingMaterialCost.php?selRateList=<?=$selRateList?>',700,600);"><? }?>
										</td>
									</tr>
								</table>									
							</td>
						</tr>
						<tr>
							<td colspan="3" height="5" ></td>
						</tr>
						-->
					</table>						
				</td>
			</tr>
		</table>
					<!-- Form fields end   -->			
		</td>
	</tr>	
	<input type="hidden" name="hidSupplierRateListId" value="<?=$supplierRateListId?>">
	<tr>
		<td height="10"></td>
	</tr>
		<!--tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr-->
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
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
	ensureInFrameset(document.frmPackingMaterialCost);
	//-->
	</script>
<? 
	}
?>
</form>
<?
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>
