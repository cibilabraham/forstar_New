<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	true;
	$checked		=	"";

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

	# FInd DailyProcessinjgRecords based on Date
	$selDate = $p["selDate"];
	$Date1				=	explode("/",$selDate);
	$selectedDate		=	$Date1[2]."-".$Date1[1]."-".$Date1[0];
	
	# Plant Records based on date:
	$plantRecords = $dailyprocessingreportObj->fetchDailyProcessingPlantRecords($selectedDate);
	
	#Unique Lot No
	$lotNoRecords = $dailyprocessingreportObj->fetchDailyProcessingLotNoRecords($selectedDate);
	
	#Unique Fish 
	$fishRecords	= $dailyprocessingreportObj->fetchDailyProcessingFishRecords($selectedDate);
	
	#Unique Process Code	
	$processCodeRecords	= $dailyprocessingreportObj->fetchDailyProcessingProcessCodeRecords($selectedDate);
	
	
	
	#Search Criteria

	if($p["cmdSearch"]!=""){
	
	$selectUnit				=	$p["selUnit"];
	$selLotNoId				=	$p["selLotNo"];
	$selectFish 			= 	$p["selFish"];
	$selectProcessCode		=	$p["selProcessCode"];
	
	$dailyProcessingReportRecords	=	 $dailyprocessingreportObj -> filterDailyCatchReportRecords($selectUnit,$selLotNoId,$selectFish,$selectProcessCode,$selectedDate);

	}

# Display heading
	if($editMode)	{
		$heading	=	$label_editDailyProcessingReport;
	}
	else{
		$heading	=	$label_addDailyProcessingReport;
	}

	$ON_LOAD_PRINT_JS	= "libjs/dailyprocessingreport.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

?>
	<form name="frmDailyProcessingReport" action="DailyProcessingReport.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="98%">
		<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?
			if( $editMode || $addMode )
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="60%"  bgcolor="#D3D3D3">
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
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
                      <tr> 
                        <td colspan="2" height="10" >&nbsp;</td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center"><? if($print==true){?><input type="submit" name="Submit" value=" View / Print" class="button" onClick="return printWindow('PrintDailyProcessingReport.php?selDate=<?=$selDate?>&selUnit=<?=$selectUnit?>&selLotNo=<?=$selLotNoId?>&selFish=<?=$selectFish?>&selProcessCode=<?=$selectProcessCode?>',700,600);"><? }?>
                          &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; </td>
                        <?} ?>
                      </tr>
                      <input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
                      <tr> 
                        <td class="fieldName" nowrap >&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
					  <tr>
					  <td valign="top" align="center"><table>
                                  
                                  <tr> 
                                    <td class="fieldName"> Date </td>
                                    <td> 
                                      <? $selDate = $p["selDate"];?>
                                      <input type="text" id="selDate" name="selDate" size="6" value="<?=$selDate?>" onchange="this.form.submit();"></td>
                                  </tr>
                                  
                                </table></td>
					  <td align=""><table width="200" cellpadding="0" cellspacing="0">
                                  <tr> 
                                    <td class="fieldName">Plant</td>
                                    <td> 
                                      <? $selectUnit			=	$p["selUnit"]; ?>
                                      <select name="selUnit">
                                        <option value="">-- Select --</option>
                                        <? foreach($plantRecords as $pr)
													{
														$plantId		=	$pr[0];
										$plantRec			=	$plantandunitObj->find($plantId);
										$plantName			=	stripSlash($plantRec[2]);
														//$plantName		=	stripSlash($pr[2]);
														$selected="";
														if($plantId	== $selectUnit )
														{
														$selected	=	"selected";
														}
											?>
                                        <option value="<?=$plantId?>" <?=$selected?>> 
                                        <?=$plantName?>
                                        </option>
                                        <? }?>
                                      </select> </td>
                                  </tr>
                                  <tr> 
                                    <td class="fieldName" nowrap>Lot No </td>
                                    <td> 
                                      <? $selLotNoId	=	$p["selLotNo"];?>
                                      <select name="selLotNo" id="selLotNo">
                                        <option value="">--Select--</option>
                                        <?
											foreach($lotNoRecords as $lnr)
													{
														$lotNoId	=	$lnr[0];
														$lotNo		=	$lnr[1];
														
														$selected="";
														if($lotNoId	== $selLotNoId){
															$selected	=	"selected";
														}
														
													?>
                                        <option value="<?=$lotNoId?>" <?=$selected?>> 
                                        <?=$lotNo?>
                                        </option>
                                        <? } ?>
                                      </select></td>
                                  </tr>
                                  <tr>
                                    <td class="fieldName">Fish</td>
                                    <td>
									<? $selectFish = $p["selFish"];?>
									<select name="selFish" id="selFish">
                                     <option value="" >--- Select Fish --- </option>
                              						<?
															
																foreach( $fishRecords as $fr )
																{
																	$id		=	$fr[0];
																	$name	=	$fr[1];
																	
																	$selected		=	"";
																	if(  $selectFish==$id){
																		$selected	=	" selected ";
																	}
														?>
                              <option value="<?=$id;?>" <?=$selected?> >
                              <?=$name;?>
                              </option>
                              <?
																
															}
														?>
                            </select>
                                    </td>
                                  </tr>
                                  <tr> 
                                    <td class="fieldName">Process</td>
                                    <td> 
                                      <? $selectProcessCode		=	$p["selProcessCode"];?>
                                      <select name="selProcessCode">
                                        <option value="">--select--</option>
                                        <?
											foreach($processCodeRecords as $pcr)
													{
														$processCodeId	=	$pcr[0];
														$processCode	=	stripSlash($pcr[1]);
														
														$selected	=	"";
															if( $processCodeId == $selectProcessCode){
																	$selected	=	"selected";
																}
														
													?>
                                        <option value="<?=$processCodeId?>" <?=$selected?>> 
                                        <?=$processCode?>
                                        </option>
                                        <? } ?>
                                      </select></td>
                                  </tr>
                                  <tr> 
                                    <td class="fieldName">&nbsp;</td>
                                    <td><span class="listing-item">
                                      <input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search" onclick="return validateProcessingReport(document.frmDailyProcessingReport);"/>
                                    </span></td>
                                  </tr>
                                </table></td>
					  <td>&nbsp;</td>
					  </tr>
                      <tr> 
                        <td colspan="3" nowrap class="fieldName">&nbsp;</td>
                      </tr>
                      <tr> 
                        <td colspan="3" nowrap class="fieldName" > </td>
                      </tr>
                      <tr> 
                        <td class="fieldName" nowrap > </td>
                        <td></td>
                        <td class="fieldName"></td>
                      </tr>
                      <tr> 
                        <td class="fieldName" nowrap ></td>
                        <td></td>
                        <td class="fieldName"></td>
                      </tr>
                      <tr> 
                        <td colspan="3"  height="10" class="listing-item" ><table width="100%" border="0" cellpadding="2" cellspacing="0">
                            <? if($err!="" ){ ?>
                            <? }?>
                            <?
	
								if( sizeof($dailyProcessingReportRecords)){
									$i	=	0;
						?>
                            
                            <tr bgcolor="#FFFFFF"> 
                              <td width="850%" colspan="17" align="center" class="listing-head"> 
                                <table width="80%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
                                  <tr bgcolor="#f2f2f2"> 
                                    <td width="6%" class="listing-head">&nbsp;&nbsp;Fish</td>
                                    <td width="9%" class="listing-head">&nbsp;&nbsp;Process</td>
                                    <td width="9%" class="listing-head">&nbsp;&nbsp;Packing</td>
                                    <td width="9%" class="listing-head">&nbsp;&nbsp;Grade</td>
                                    <td width="15%" class="listing-head" align="center">Quantity</td>
                                  </tr>
                                  <?
	foreach($dailyProcessingReportRecords as $dpr){
	$i++;
	
	$fishRec		=	$fishmasterObj->find($dpr[1]);
	$fishName		=	$fishRec[1];
	
	$processCodeRec		=	$processcodeObj->find($dpr[2]);
	$processCode	=	$processCodeRec[2];
	
	$packingRec		=	$packinggoodsObj->find($dpr[3]);
	$packingCode	=	stripSlash($packingRec[1]);
		
	$gradeRec			=	$grademasterObj->find($dpr[4]);
	$gradeCode			=	stripSlash($gradeRec[1]);
	
	$quantity			=	$dpr[5];
	
	
	?>
                                  <tr bgcolor="#FFFFFF"> 
                                    <td class="listing-item" nowrap>&nbsp;&nbsp; 
                                      <?=$fishName?>                                    </td>
                                    <td class="listing-item">&nbsp;&nbsp; 
                                      <?=$processCode?>                                    </td>
                                    <td class="listing-item" nowrap>&nbsp;&nbsp;<?=$packingCode?></td>
                                    <td class="listing-item" nowrap>&nbsp;&nbsp; 
                                      <?=$gradeCode?>                                    </td>
                                    <td class="listing-item" align="right"> 
                                      <? echo number_format($quantity,3);?>
                                      &nbsp;&nbsp; </td>
                                  </tr>
                                  <? }?>
								</table>							  </td>
                            </tr>
							<? } else if($p["cmdSearch"]!="") {?>
	  <tr bgcolor="white"> 
      <td  class="err1" height="5" align="center" colspan="17"><?=$msgNoRecords;?></td>
    </tr>
	<? }?>
                            
                            <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                           
                          </table></td>
                      </tr>
                      <tr> 
                        <td colspan="2" align="center">&nbsp;</td>
                        <td align="center" colspan="2">&nbsp;</td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center"><? if($print==true){?><input type="submit" name="Submit" value=" View / Print" class="button" onClick="return printWindow('PrintDailyProcessingReport.php?selDate=<?=$selDate?>&selUnit=<?=$selectUnit?>&selLotNo=<?=$selLotNoId?>&selFish=<?=$selectFish?>&selProcessCode=<?=$selectProcessCode?>',700,600);"><? }?></td>
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
		?>

			
	</table>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
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
