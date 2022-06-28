<?php
	require("include/include.php");
	
	
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	/*rekha added code */
/*
	if($roleId=1){
		$editMode		=	true;	
	}
*/	
	/*end code */


	$addMode		=	false;

	$selection 		=	"?pageNo=".$p["pageNo"];

	//------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
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

	# Add a Plant
	if ($p["cmdAddNew"]!="") $addMode = true;

	# Insert
	if ($p["cmdAddPlant"]!="") 
	{
		$no	=	$p["plantNo"];
		$company	=$p["company"];
		$name	=	addSlash($p["plantName"]);
		$plant_code	=	addSlash($p["plantcode"]);
		$stdProduction	=	addSlash($p["stdProduction"]);
		$basedOn	=	addSlash($p["basedOn"]);
		$address=$p["address"];
		$alphaCode=$billingCompanyObj->getBillingCompanyAlphaCode($company);
		$unitName=$alphaCode.$name;
		if($company!="" && $no!="" && $unitName!="")
		{
			$chkStatus=$plantandunitObj->checkDuplicate($unitName);
			if($chkStatus)
			{
				$plantRecIns	=	$plantandunitObj->addPlant($company,$no, $unitName,$stdProduction,$basedOn,$address,$plant_code);
			}
			if ($plantRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddPlant);
				$sessObj->createSession("nextPage",$url_afterAddPlant.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddPlant;
			}
			$plantRecIns	=	false;
		}
	}

	# Edit a Plant
	if ($p["editId"]!="") {
		$editIt		=	$p["editId"];
		$editMode	=	true;
		$plantRec	=	$plantandunitObj->find($editIt);
		$plantId	=	$plantRec[0];
		$no		=	$plantRec[1];
		$company=$plantRec[2];
		$name		=	stripSlash($plantRec[3]);
		//plant_code
		$stdProduction		=	stripSlash($plantRec[4]);
		$basedOn		=	stripSlash($plantRec[5]);
		if($basedOn!="") { 
			($basedOn=="date")?$selDate="selected":$selMonth="selected"; 
		}
		$address=$plantRec[6];
		$plant_code=$plantRec[7];
		//echo $selDate;
	}
	
	# Update
	if ($p["cmdSaveChange"]!="") {
		$plantId	=	$p["hidPlantId"];
		$company	=$p["company"];
		$no			=	$p["plantNo"];
		$name		=	addSlash($p["plantName"]);
		$stdProduction	=	addSlash($p["stdProduction"]);
		$basedOn	=	addSlash($p["basedOn"]);
		$address=$p["address"];
		$plant_code=$p["plantcode"];
		if ($plantId!="" && $no!="" && $name!="") {
			$chkStatus=$plantandunitObj->checkDuplicate($name,$plantId);
			if($chkStatus)
			{
				$plantRecUptd	= $plantandunitObj->updatePlant($plantId,$company, $no, $name,$stdProduction,$basedOn,$address,$plant_code);
			}
		}
	
		if ($plantRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succPlantUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePlant.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failPlantUpdate;
		}
		$plantRecUptd	=	false;
	}

	# Delete Plant
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$plantId	=	$p["delId_".$i];

			if ($plantId!="") {
				# Checking the selected plantNUnit is link with any other process
				$plantNUnitRecInUse = $plantandunitObj->plantNUnitRecInUse($plantId);
				if (!$plantNUnitRecInUse) {
					$plantRecDel = $plantandunitObj->deletePlant($plantId);	
				}
			}
		}
		if ($plantRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPlant);
			$sessObj->createSession("nextPage",$url_afterDelPlant.$selection);
		} else {
			$errDel	=	$msg_failDelPlant;
		}
		$plantRecDel	=	false;
	}




if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$plantId	=	$p["confirmId"];


			if ($plantId!="") {
				// Checking the selected fish is link with any other process
				$plantRecConfirm = $plantandunitObj->updatePlantconfirm($plantId);
			}

		}
		if ($plantRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmplant);
			$sessObj->createSession("nextPage",$url_afterAddPlant.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}

		}


	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$plantId = $p["confirmId"];

			if ($plantId!="") {
				#Check any entries exist
				
					$plantRecConfirm = $plantandunitObj->updatePlantReleaseconfirm($plantId);
				
			}
		}
		if ($plantRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmplant);
			$sessObj->createSession("nextPage",$url_afterAddPlant.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	$plantRecords	= $plantandunitObj->fetchPagingRecords($offset, $limit);
	$plantSize	= sizeof($plantRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	= sizeof($plantandunitObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	//$companyRecords	= $plantandunitObj->getCompanyUser($userId);
	$companyRecords	=$billingCompanyObj->fetchAllRecordsActivebillingCompany();
	if ($editMode)	$heading	=	$label_editPlant;
	else		$heading	=	$label_addPlant;
	
	$help_lnk="help/hlp_PlantsandUnits.html";

	$ON_LOAD_PRINT_JS	= "libjs/plantsandunits.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmPlant" action="PlantsandUnits.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>
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
								$bxHeader="PLANT/UNIT MASTER";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;PLANT/UNIT MASTER </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>
									<td colspan="3" align="center">
										<table width="50%" align="center">
										<?
										if ($editMode || $addMode) {
										?>
											<tr>
												<td>
													<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="55%">
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
																			<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
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

																						<td colspan="2" align="center">
																						<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('PlantsandUnits.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatepuCompetitor(document.frmPlant);">
																						</td>
																						
																						<?} else{?>

																						
																						<td  colspan="2" align="center">
																						<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('PlantsandUnits.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAddPlant" class="button" value=" Add" onClick="return validatepuCompetitor(document.frmPlant);">
																						</td>

																						<?}?>
																						
																					</tr>
																					<input type="hidden" name="hidPlantId" value="<?=$plantId;?>">
																					<tr>
																						<td colspan="2"  height="10" ></td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap >*Company</td>
																						<td>
																							<select name="company" id="company">
																								<option value="">--Select--</option>
																								<? foreach($companyRecords as $comp)
																								{
																									$cmp= $comp[0];
																									$cmpName= $comp[9];
																								($company==$cmp)?$selected="selected":$selected="";
																								?>
																								<option value="<?=$cmp?>" <?=$selected?>><?=$cmpName?></option>
																								<? } ?>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap >*Plant No </td>
																						<td><INPUT TYPE="text" NAME="plantNo" size="3" value="<?=$no;?>"></td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap >*Plant Name</td>
																						<td >
																						<INPUT TYPE="text" NAME="plantName" size="20" value="<?=$name;?>">
																						</td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap >*Alpha Code</td>
																						<td >
																							<INPUT TYPE="text" NAME="plantcode" size="20" value="<?=$plant_code;?>">
																						</td>
																					</tr>																					
																					<tr>
																						<td class="fieldName" nowrap >STD Production</td>
																						<td >
																						<INPUT TYPE="text" NAME="stdProduction" id="stdProduction" size="9" value="<?=$stdProduction;?>">
																						</td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap >Based On</td>
																						<td >
																							<select name="basedOn" id="basedOn">
																								<option value="">--Select--</option>
																								<option value="date" <?=$selDate?>>Date</option>
																								<option value="month" <?=$selMonth?>>Month</option>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td class="fieldName" nowrap >Address</td>
																						<td>
																						<textarea name="address" id="address"><?=$address?></textarea>
																						</td>
																					</tr>
																					<tr>
																						<td colspan="2"  height="10" ></td>
																					</tr>
																					<tr>
																						<? if($editMode){?>
																						<td colspan="2" align="center">
																							<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PlantsandUnits.php');">&nbsp;&nbsp;
																							<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatepuCompetitor(document.frmPlant);">
																						</td>
																						<?} else{?>
																						<td  colspan="2" align="center">
																							<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PlantsandUnits.php');">&nbsp;&nbsp;
																							<input type="submit" name="cmdAddPlant" class="button" value=" Add " onClick="return validatepuCompetitor(document.frmPlant);">
																						</td>
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
													# Listing Fish Starts
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
										<td colspan="3">
											<table cellpadding="0" cellspacing="0" align="center">
												<tr>
													<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$plantSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintPlantsandUnits.php',700,600);"><? }?></td>
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
										<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											<?
											if( sizeof($plantRecords) > 0 )
											{
												$i	=	0;
											?>
											<thead>
											<? if($maxpage>1){?>
												<tr>
													<td colspan="4" style="padding-right:10px" class="navRow">
														<div align="right">
														<?php
														$nav  = '';
														for($page=1; $page<=$maxpage; $page++)
														{
															if ($page==$pageNo)
															{
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page			
															}
															else
															{
																$nav.= " <a href=\"PlantsandUnits.php?pageNo=$page\" class=\"link1\">$page</a> ";
															}
														}
														if ($pageNo > 1)
														{
															$page  = $pageNo - 1;
															$prev  = " <a href=\"PlantsandUnits.php?pageNo=$page\"  class=\"link1\"><<</a> ";
														}
														else
														{
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}
														if ($pageNo < $maxpage)
														{
															$page = $pageNo + 1;
															$next = " <a href=\"PlantsandUnits.php?pageNo=$page\"  class=\"link1\">>></a> ";
														}
														else
														{
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
													<th width="30"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
													<th style="padding-left:10px; padding-right:10px;">Sr. No.</th>
													<th style="padding-left:10px; padding-right:10px;">Company</th>
													<th style="padding-left:10px; padding-right:10px;">Name</th>
													<th style="padding-left:10px; padding-right:10px;">Alpha Code</th>
													<th nowrap style="padding-left:10px; padding-right:10px;">Plant No</th>
													
													<? if($edit==true){?>
													<th width="70">&nbsp;</th>
													<? }?>
													<? if($confirm==true){?>
													<th width="70">&nbsp;</th>
													<? }?>
												</tr>
											</thead>
											<tbody>
											<?
											$prev_comp_nm="";
											$j=1;
											foreach($plantRecords as $pr)
											{
												$i++;
												$plantId		=	$pr[0];
												$plantNo		=	stripSlash($pr[1]);
												$plantName		=	stripSlash($pr[2]);
												//$company_id		=	stripSlash($pr[3]);
												$company_name		=	stripSlash($pr[3]);
														//$qry	=	"select name from m_billing_company where id='$company_id'";
														
														//$result	= databaseConnect->getRecords($qry);
												$active=$pr[6];
												$alpha_code=$pr[7];
												//$existingcount=$pr[6];
												$existingcount=$pr[8];
											?>
												<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
													<td width="30" align="center">
													<?php
													if($existingcount==0){
													?>
													<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$plantId;?>" class="chkBox"></td>
													<?php 
													}
													?>
													<? if($prev_comp_nm==$company_name){?>
													<td class="listing-item" style="padding-left:10px; padding-right:10px;"></td>
													<td class="listing-item" style="padding-left:10px; padding-right:10px;"></td>
													<? }
													else{
														
														?>
														<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$j.")."?></td>
														<td class="listing-item" style="padding-left:10px; padding-right:10px;"><strong><?=$company_name;?></strong></td>
														
													<?
														$j=$j+1;
													}
													?>
									
													<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$plantName;?></td>
													<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$alpha_code;?></td>
													<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$plantNo;?></td>
													
													<? if($edit==true){?>
													<td class="listing-item" width="70" align="center">
													<? if ($active!='1'){?>
													<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$plantId;?>,'editId'); this.form.action='PlantsandUnits.php';">
													<? }?>
													</td>
													<? }?>
													<? 
													if ($confirm==true){
													?>
													<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
													<?php 
													
													if ($confirm==true){	
													if ($active==0){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$plantId;?>,'confirmId');"  >
													<?php } 
													else if ($active==1)
													{ 
												?>
												<input type="submit" value=" <?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$plantId;?>,'confirmId');"  >
												<?
													
													//if ($existingcount==0) {?>
													<!--<input type="submit" value="<?//=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?//=$plantId;?>,'confirmId');"  >-->
													<?php 
													
													//}
													} }?>
													</td>
													<? }?>
												</tr>
												<?
												$prev_comp_nm = $company_name;
												}
												?>
													<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
													<input type="hidden" name="editId" value="">
													<input type="hidden" name="confirmId" value="">
												<? if($maxpage>1){?>
												<tr>
													<td colspan="4" style="padding-right:10px" class="navRow">
														<div align="right">
														<?php
														$nav  = '';
														for($page=1; $page<=$maxpage; $page++)
														{
															if ($page==$pageNo)
															{
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page			
															}
															else
															{
																$nav.= " <a href=\"PlantsandUnits.php?pageNo=$page\" class=\"link1\">$page</a> ";
															}
														}
														if ($pageNo > 1)
														{
															$page  = $pageNo - 1;
															$prev  = " <a href=\"PlantsandUnits.php?pageNo=$page\"  class=\"link1\"><<</a> ";
														 }
														else
														{
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

														if ($pageNo < $maxpage)
														{
															$page = $pageNo + 1;
															$next = " <a href=\"PlantsandUnits.php?pageNo=$page\"  class=\"link1\">>></a> ";
														 }
														else
														{
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
												else
												{
												?>
												<tr>
													<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
												</tr>	
												<?
												
												$prev_comp_nm = "fkkf";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$plantSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintPlantsandUnits.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
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
			<td height="10"></td>
		</tr>	
	</table>
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>