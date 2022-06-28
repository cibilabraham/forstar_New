<?
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$fishId			=	"";
	$recordsFilterId	=	0;
	$recordsDate	= "";


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

#coming from competitorscatch list
	$catchEditId		= $p["catchEditId"];
	$editCompetitor		= $p["editCompetitor"];
	
	# Add Competitors Catch
	
	if( $p["cmdAddNew"]!="" ){
		$addMode	=	true;
		
		#Adding Temporary Id
	
		if($p["entryId"]=="")
			{		
			$tempdataRecIns=$competitorscatchObj->addTempMaster();
		
				if($tempdataRecIns!="")
					{
						$insertId=$databaseConnect->lastInserted();
						foreach($insertId as $ld)
						{
						$lastId	=	$ld[0];
						}
					}
			}
				else 
					{
					$lastId	=	$p["entryId"];
					}
	}
	
	if( $p["cmdCancel"]!="" ){
		$p["selFish"]	= 	"";
		$entryId		=	$p["entryId"];
		$delLastInsertRec	=	$competitorscatchObj->delLastInsertId($entryId);
		$delCompLastList 	=	$competitorscatchObj->delCompLastInsertId($entryId);
		$addMode			=	false;
	}
	
	if( $p["cmdAddCompetitorCatch"]!="" ){

		//$currentDate		=	$p["currentDate"];
		$landingCenterId	= 	$p["landingCenter"];
		/*$competitorId		=	$p["competitor"];
		$fishId				=	$p["selFish"];
		$quantity			=	$p["quantity"];*/
		$lastId				=	$p["entryId"];
		if( $landingCenterId!="" && $lastId!="")
		{
			
			$competitorsCatchRecIns	=	$competitorscatchObj->addCompetitorsCatch($landingCenterId,$lastId);

			if($competitorsCatchRecIns)
			{
				$sessObj->createSession("displayMsg",$msg_succAddCompetitorsCatch);
				$sessObj->createSession("nextPage",$url_afterAddCompetitorsCatch);
			}
			else
			{
				$addMode	=	true;
				$err		=	$msg_failAddCompetitorsCatch;
			}
		$competitorsCatchRecIns	=	false;
		}
	}

# Edit Competitors Catch 
	
	if( $p["editId"]!=""){
	
		$editMode		=	true;
		
		$editId			=	$p["editId"];
		$competitorsCatchRec	=	$competitorscatchObj->find($editId);

		$catchId			=	$competitorsCatchRec[0];
		$catchCenterId		=	$competitorsCatchRec[1];
		
		$array				=	explode("-",$competitorsCatchRec[2]);
		$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
	
}	
if($editMode==true){
$lastId	=	$catchId;
}
	
	
if( $p["cmdSaveChange"]!="" ){
		
		$catchId		=	$p["hidCompetitorCatchId"];
		
		$landingCenterId	= 	$p["landingCenter"];
		

		if( $catchId!="" && $landingCenterId!="" ){
		
			$competitorsCatchRecUptd	=	$competitorscatchObj->updateCompetitorsCatch($catchId,$landingCenterId);		
		}
		if($competitorsCatchRecUptd)
		{
			$sessObj->createSession("displayMsg",$msg_succUpdateCompetitorsCatch);
			$sessObj->createSession("nextPage",$url_afterUpdateCompetitorsCatch);
		}
		else
		{
			$editMode	=	true;
			$err		=	$msg_failUpdateCompetitorsCatch;
		}

		$competitorsCatchRecUptd = false;
	}

	
	

				
	# Delete Competitors Catch
	
	if( $p["cmdDelete"]!=""){

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$compCatchId	=	$p["delId_".$i];

			if( $compCatchId!="" )
			{
				$competitorsCatchRecDel =	$competitorscatchObj->deleteCompetitorsCatch($compCatchId);
				$delCompLastList 	=	$competitorscatchObj->delCompLastInsertId($compCatchId);
			}
		}

		if($competitorsCatchRecDel)
		{
			$sessObj->createSession("displayMsg",$msg_succDelCompetitorsCatch);
			$sessObj->createSession("nextPage",$url_afterDelCompetitorsCatch);
		}
		else
		{
			$errDel		=	$msg_failDelCompetitorsCatch;
		}
	}

#List all Competitors Catch Records

	$recordsDate	=	$p["selDate"];		
	$Date			=	explode("/",$recordsDate);
	$enterDate		=	$Date[2]."-".$Date[0]."-".$Date[1];
	
	
	if($recordsDate!=""){	
	
	$competitorsCatchRecords	=	$competitorscatchObj->competitorsCatchRecFilter($enterDate);
		
	}
	else if ($recordsDate==""){
		$CurrentDay = date("m/d/Y");
		$Date			=	explode("/",$CurrentDay);
		$enterDate		=	$Date[2]."-".$Date[0]."-".$Date[1];
		$competitorsCatchRecords	=	$competitorscatchObj->competitorsCatchRecFilter($enterDate);
	}
	else {
	$competitorsCatchRecords	=	$competitorscatchObj->fetchAllRecords();
	}


$competitorsCatchRecordsSize		=	sizeof($competitorsCatchRecords);

#List all Landing Centers
	$landingCenterRecords	=	$landingcenterObj->fetchAllRecordsActiveLanding();

# Returns all fish master records 
	$fishMasterRecords		=	$fishmasterObj->fetchAllRecordsFishactive();
	
	
#List all Landing Centers
	//$landingCenterRecords	=	$landingcenterObj->fetchAllRecords();

# Returns all fish master records 
	//$fishMasterRecords		=	$fishmasterObj->fetchAllRecords();
	
#List All Competitors

	
	
	//$competitorRecords	=	$competitorObj->fetchAllRecords();
	$competitorSize		=	sizeof($competitorRecords);
	
	# Display heading
	if($editMode)	{
		$heading	=	$label_editCompetitorsCatch;
	}
	else{
		$heading	=	$label_addCompetitorsCatch;
	}

	$ON_LOAD_PRINT_JS	= "libjs/competitorscatch.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

?>
	<form name="frmCompetitorsCatch" action="CompetitorsCatch.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%">
		<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?
			if( $editMode || $addMode )
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
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
									<td colspan="2"  align="center">
										<table cellpadding="0"  width="75%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('CompetitorsCatch.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddCompetitorsCatch(document.frmCompetitorsCatch);">												</td>
												
												<?} else{?>

												<td align="center" colspan="3">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CompetitorsCatch.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddCompetitorCatch" class="button" value=" Add " onClick="return validateAddCompetitorsCatch(document.frmCompetitorsCatch);">												</td>
											
												<?} ?>
											</tr>
											<input type="hidden" name="hidCompetitorCatchId" value="<?=$catchId;?>"><!--input type="hidden" name="entryId" value="<?=$lastId?>"-->
				
											<tr>
											  <td class="fieldName" nowrap >&nbsp;</td>
											  <td>&nbsp;</td>
											  <td>&nbsp;</td>
											  <td>&nbsp;</td>
										  </tr>
											<tr>
											  <td class="fieldName" nowrap >Date</td>
											  <td><input name="currentDate" type="text" id="currentDate" size="7" value="<? if($editMode==true) { echo $enteredDate; } else { echo date("d/m/Y");}?>" readonly/></td>
											  <td><span class="fieldName">*Landing Center</span></td>
											  <td><? $landingCenterId	= $p["landingCenter"];?>
											    <select name="landingCenter" id="landingCenter">
                                                  <option value="">--Select--</option>
                                                  <?
											foreach($landingCenterRecords as $fr)
													{
														$i++;
														$centerId	=	$fr[0];
														$centerName	=	stripSlash($fr[1]);
														$centerCode	=	stripSlash($fr[2]);
														$centerDesc =	stripSlash($fr[3]);
														$selected="";
														if($centerId	== $catchCenterId || $centerId==$landingCenterId)
														{
														$selected	=	"selected";
														}
													?>
                                                  <option value="<?=$centerId?>" <?=$selected?>>
                                                  <?=$centerName?>
                                                  </option>
                                                  <? } ?>
                                                </select></td>
										  </tr>
											
											
											<tr>
											  <td class="fieldName" nowrap >*Competitor</td>
											  <td>
											  <? $selCompetitor=$p["competitor"];
											  if($addMode==true){
											  ?>
				<select name="competitor" onchange="this.form.submit();">
				<? } else {?>
<select name="competitor" onchange="this.form.editId.value=<?=$editId?>;this.form.submit();"><? }?>
                                                <option value="">--select--</option>
                                                <?
											foreach($competitorRecords as $cr)
													{
														$competitorId		=	$cr[0];
														$competitorCode		=	stripSlash($cr[1]);
														$competitorName		=	stripSlash($cr[2]);
														$selected	=	"";
															if( $competitor==$competitorId ||$selCompetitor==$competitorId || $editCompetitor==$competitorId){
																	$selected	=	"selected";
																}														
													?>
                                                <option value="<?=$competitorId?>" <?=$selected?>>
                                                <?=$competitorName?>
                                                </option>
                                                <? } ?>
                                              </select></td>
											  <td>&nbsp;</td>
											  <td>&nbsp;</td>
										  </tr>
											
											
											<tr>
											  <td colspan="4"  height="10" >
											  <table width="100%" cellpadding="0" cellspacing="0">
											  <tr>
											  <td class="fieldName">
											  <? if($p["competitor"]!="" || $catchId!=""){?>
											  <fieldset>
                                <legend>List Items</legend>
									<? //if($editCompetitor==""){?>
                                 <iframe id="iFrame1" 
src ="CompetitorsCatchFishList.php?landingCenterId=<?=$landingCenterId?>&newId=<?=$lastId?>&competitorId=<?=$selCompetitor?>&catchEditId=<?=$catchEditId?>&competitorEditId=<?=$editCompetitor?>&competitorId=<?=$selCompetitor?>&newCompetitor=<?=$selCompetitor?>" width="260" frameborder="0" height="300"></iframe>
<? // }?>
</fieldset>	<? }?>
											  <td>&nbsp;</td>	
											  <td class="fieldName">
											  <? if($p["competitor"]!="" || $catchId!=""){?>
											  <fieldset>
                                <legend>Competitor List</legend>
                                <iframe id="iFrame2" name="iFrame2" src ="CompetitorsCatchList.php?landingCenterId=<?=$landingCenterId?>&newId=<?=$lastId?>&competitorId=<?=$selCompetitor?>" width="275" frameborder="0" height="300"></iframe>
                                </fieldset>
								<? }?>
								</td>								  
											  </tr>
											   </table>											  </td>
										  </tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center"><input type="hidden" name="catchEditId">
												<input type="hidden" name="editCompetitor">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CompetitorsCatch.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddCompetitorsCatch(document.frmCompetitorsCatch);">												</td>
												
												<?} else{?>

												<td align="center" colspan="3">
												<input type="hidden" name="catchEditId">
												<input type="hidden" name="editCompetitor">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CompetitorsCatch.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAddCompetitorCatch" class="button" value=" Add " onClick="return validateAddCompetitorsCatch(document.frmCompetitorsCatch);">												</td>
<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
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
		<tr>
			<td height="10" ></td>
		</tr>
		<?
			}
			
			# Listing Fish-Grade Starts
		?>
		<tr>
								<td colspan="3" height="10" align="center" class="listing-item"><img src="images/x.gif" width="20" height="20"> - The entire transactions are not completed.</td>
							</tr>
		<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="91%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Competitors Catch </td>
								<td background="images/heading_bg.gif"  >
									<!--table cellpadding="0" cellspacing="0" align="right">	
											<tr>
											
											<td class="listing-item" nowrap > Select a fish to view Daily Rate &nbsp;</td>
											<td>
												<select name="selFilter" onChange="validateSelect(document.frmCompetitorsCatch); this.form.submit();">
													<option value="0"> All Fish </option>
													<? 
														if( sizeof($fishMasterRecords)>0 )
														{
															foreach ($fishMasterRecords as $fl)
															{
																$fishId		=	$fl[0];
																$fishName	=	$fl[1];
																
																$selected	=	"";
																if( $fishId == $recordsFilterId ){
																	$selected	=	"selected";
																}
															
													?>
													<option value="<?=$fishId;?>" <?=$selected;?> ><?=$fishName;?> </option>
													<?
															}
														}
													?>
												</select>											</td>
											<td class="listing-item" nowrap>&nbsp;&nbsp;Date</td>
											<td nowrap>
										<select name="selDate" onChange="validateSelect(document.frmCompetitorsCatch); this.form.submit();">
													<option value="0"> --Select Date-- </option>
													<? 
												if( sizeof($dailyRateDateRecords) > 0 )
														{
														
												foreach($dailyRateDateRecords as $dr)
												{
												
													
													$i++;
													$dailyRateId	=	$dr[0];
													$Date			=	explode("-",$dr[0]);
													$enterDate		=	$Date[2]."/".$Date[1]."/".$Date[0];
																
																$selected	=	"";
																if( $dailyRateId == $recordsDate ){
																	$selected	=	"selected";
																}
															
													?>
													<option value="<?=$dailyRateId;?>" <?=$selected;?> ><?=$enterDate;?> </option>
													<?
															}
														}
													?>
												</select>	
											</td>
										</tr>
									</table-->
									<table cellpadding="0" cellspacing="0" align="right">
                      <tr> 
                        <td class="listing-item" nowrap >&nbsp;</td>
                        <td class="listing-item" nowrap>&nbsp;&nbsp;Date</td>
                        <td nowrap>&nbsp;&nbsp; 
						<? 
						if($recordsDate==""){
						$recordsDate	=	date("m/d/Y");
						}
						
						?>
						<input type="text" id="selDate" name="selDate" size="10" value="<?=$recordsDate?>" onchange="this.form.submit();" /></td>
                      </tr>
                    </table>
									
								</td>
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
							</tr>
							
							
							<tr>	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$competitorsCatchRecordsSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintCompetitorsCatch.php',700,600);"><? }?></td>
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
									<table cellpadding="1"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999"><?
									if( sizeof($competitorsCatchRecords) > 0 )
											{
												$i	=	0;
									
									?>
										
										<tr  bgcolor="#f2f2f2"  >
											<td width="20" align="center"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></td>
											<td width="190" nowrap class="listing-head">&nbsp;&nbsp;Landing Center </td>
											<td width="120" class="listing-head" nowrp>&nbsp;&nbsp;Date</td>
											<? if($edit==true){?>
											<td class="listing-head" nowrap></td>
											<? }?>
										</tr>
										<?
											

												foreach($competitorsCatchRecords as $ccr)
												{
												
													
												$i++;
												$competitorsCatchId	=	$ccr[0];
												$Date				=	explode("-",$ccr[2]);
												$enterDate			=	$Date[2]."/".$Date[1]."/".$Date[0];
												$landingCenterId	=	stripSlash($ccr[1]);
										$landingCenterFilterRecords	=	$landingcenterObj->filterRecord($landingCenterId);
											$landingCenterName	=	$landingCenterFilterRecords[0][1];
											$catchEntryFlag		=	$ccr[3];
																								
										?>
										<tr  bgcolor="WHITE"  >

											<td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$competitorsCatchId;?>" ></td>
											<td class="listing-item" nowrap >&nbsp;&nbsp;
											<? if($landingCenterName==""){?>
                          <img src="images/x.gif" width="20" height="20"> 
                          <? } else { echo $landingCenterName; };?>											</td>
											<td class="listing-item" >&nbsp;&nbsp;<?=$enterDate?></td>
											<? if($edit==true){?>
											<td class="listing-item" width="58" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$competitorsCatchId;?>,'editId'); this.form.action='CompetitorsCatch.php';"></td>
											<? }?>
										</tr>
										<?
												}
										?>
											
										<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
										<input type="hidden" name="editId" value="">
										<? if($catchEntryFlag==0){?>
					  <input type="hidden" name="entryId" id="entryId" value="<?=$competitorsCatchId?>">
					  <? } else {?>
					  <input type="hidden" name="entryId" id="entryId" value="<?=$lastId?>">
					  <? } ?>
										
										<?
											}
											else
											{
										?>
										<tr bgcolor="white">
											<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$competitorsCatchRecordsSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintCompetitorsCatch.php',700,600);"><? }?></td>
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
<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selDate", 
			ifFormat    : "%m/%d/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
