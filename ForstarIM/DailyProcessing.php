<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$editFishId		=	"";
	$fishId			=	"";
	$recordsFilterId	=	0;
	$recordsDate		=	0;
	
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
	
	$lotEditId			=	$p["lotEditId"];
	$editFishId			=	$p["editFishId"];
	$editProcessId		=	$p["editProcessId"];

	$dailyLotNo			=	$p["lotNo"];
	
	
	/*foreach($p as $val =>$key)
		{
		echo "<br>$val = $key";
		}*/

	//echo "The Id is=".$p["entryId"];
	
		

	# Add Daily Pre Processor
	
	if( $p["cmdAddNew"]!="" ){
		$addMode	=	true;
		
		//echo "Current entryId=".$p["entryId"]."<br>";
	#Adding Temporary Id
	
		if($p["entryId"]=="")
			{		
			$tempdataRecIns=$dailyprocessingObj->addTempMaster();
		
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
		$delLastInsertRec=$dailyprocessingObj->delLastInsertId($entryId);
		$lastId			=	"";
		$addMode			=	false;
	}

	if( $p["cmdAddDailyProcessing"]!="" ){

		$unit				=	$p["unit"];
		$dailyLotNo			=	$p["lotNo"];
		$lastId				=	$p["entryId"];
		
		if( $dailyLotNo!="" && $unit!="")
		{
			
			$dailyProcessingRecIns	=	$dailyprocessingObj->addDailyProcessing($unit,$dailyLotNo,$lastId);

			if($dailyProcessingRecIns)
			{
				$sessObj->createSession("displayMsg",$msg_succAddDailyProcessing);
				//$sessObj->createSession("nextPage",$url_afterAddDailyProcessing);
			}
			else
			{
				$addMode	=	true;
				$err		=	$msg_failAddDailyProcessing;
			}
		$dailyProcessingRecIns	=	false;
		}
		$addMode=false;
	}


if( $p["cmdSaveChange"]!="" ){
		
		$dailyProcessingId		=	$p["hidDailyProcessingId"];
		
		$unit				=	$p["unit"];
		//$dailyLotNo			=	$p["lotNo"];
				
		
		if( $dailyLotNo!="" && $unit!="" ){
		$dailyProcessingRecUptd	=	$dailyprocessingObj->updateDailyProcessing($dailyLotNo,$unit,$dailyProcessingId);		
		}
		if($dailyProcessingRecUptd)
		{
			$sessObj->createSession("displayMsg",$msg_succUpdateDailyProcessing);
			$sessObj->createSession("nextPage",$url_afterUpdateDailyProcessing);
		}
		else
		{
			$editMode	=	true;
			$err		=	$msg_failUpdateDailyProcessing;
		}

		$dailyProcessingRecUptd = false;
	}



	# Edit Daily Pre Proces
	
	if( $p["editId"]!="" ){
	
		$editMode		=	true;
		
		$editId				=	$p["editId"];
		$dailyProcessingRec	=	$dailyprocessingObj->find($editId);

		$dailyProcessingId		=	$dailyProcessingRec[0];
		$dailyProcessingLot		=	$dailyProcessingRec[1];
		
		if($p["editSelectionChange"]=='1' || $p["lotNo"]==""){
			$dailyLotNo			=	$dailyProcessingId;
			}
			else {
				$dailyLotNo			=	$p["lotNo"];
			}
		
		
		$dailyProcessingUnit	=	$dailyProcessingRec[2];
		$array				=	explode("-",$dailyProcessingRec[3]);
		$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
		
		if($p["editSelectionChange"]=='1' || $p["selFish"]==""){
			$fishId				=	$dailyProcessingRec[8];
		}
		else {
				$fishId			=	$p["selFish"];
		}
		
		$dailyProcessingPack	=	$dailyProcessingRec[2];
		
		if($p["editSelectionChange"]=='1' || $p["packingType"]==""){
				$packingSel			=	$dailyProcessingPack;
			}
			else {
				$packingSel= $p["packingType"];
			}
		
		if($p["editSelectionChange"]=='1' || $p["processCode"]==""){
				$dailyProcessCode		=	$dailyProcessingRec[10];
			}
			else {
				$dailyProcessCode=$p["processCode"];
			}
		//$fishId				=	$dailyProcessingRec[10];
		
		$processCodeRecords	=	$processcodeObj->processCodeRecFilter($fishId);
	}	
	
	
			
	# Delete Daily Pre Process
	
	if( $p["cmdDelete"]!=""){

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$dailyProcessingId		=	$p["delId_".$i];
			//$processingLotId		=	$p["lotId_".$i];
			

			if( $dailyProcessingId!="")
			{
				$dailyProcessingRecDel =	$dailyprocessingObj->deleteDailyProcessing($dailyProcessingId);
				$dailyProcessingGradeRecDel =	$dailyprocessingObj->deleteDailyProcessingAllGrade($dailyProcessingId);
			}
		}

		if($dailyProcessingRecDel)
		{
			$sessObj->createSession("displayMsg",$msg_succDelDailyProcessing);
			$sessObj->createSession("nextPage",$url_afterDelDailyProcessing);
		}
		else
		{
			$errDel		=	$msg_failDelDailyProcessing;
		}
	}


	
# List Daily Processing 

	$recordsDate	=	$p["selDate"];		
	$Date			=	explode("/",$recordsDate);
	$enterDate		=	$Date[2]."-".$Date[0]."-".$Date[1];
	
	if($recordsDate!=""){	
	
	$dailyProcessingRecords		=	$dailyprocessingObj->dailyProcessingRecFilter($enterDate);
		
	}
	else if ($recordsDate==""){
		$CurrentDay = date("m/d/Y");
		$Date			=	explode("/",$CurrentDay);
		$enterDate		=	$Date[2]."-".$Date[0]."-".$Date[1];
		$dailyProcessingRecords		=	$dailyprocessingObj->dailyProcessingRecFilter($enterDate);
	}
	else {
	$dailyProcessingRecords	=	$dailyprocessingObj->fetchAllRecords();
	}
	
	
	$dailyProcessingSize		=	sizeof($dailyProcessingRecords);


	

# Returns all fish master records 
	$fishMasterRecords		=	$fishmasterObj->fetchAllRecords();

#List All Plants
$plantRecords	=	$plantandunitObj->fetchAllRecords();

#List All Packing Goods

$packingGoodsRecords		=	$packinggoodsObj->fetchAllRecords();


	# Display heading
	if($editMode)	{
		$heading	=	$label_editDailyProcessing;
	}
	else{
		$heading	=	$label_addDailyProcessing;
	}


	$ON_LOAD_PRINT_JS	= "libjs/dailyprocessing.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

?>
	<form name="frmDailyProcessing" id="frmDailyProcessing" action="DailyProcessing.php" method="Post">
	
  <table cellspacing="0"  align="center" cellpadding="0" width="70%">
    <tr> 
      <td height="40" align="center" class="err1" >
        <? if($err!="" ){?>
        <?=$err;?>
        <?}?>
      </td>
    </tr>
    <?
			if( $editMode || $addMode )
			{
		?>
    <tr> 
      <td> <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
          <tr> 
            <td   bgcolor="white"> 
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;
                    <?=$heading;?>
                  </td>
                </tr>
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2"  align="center"> <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
                      <tr> 
                        <td width="51%" height="10" colspan="3" ></td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <td colspan="3" align="center" nowrap> <input type="submit" name="cmdSaveCancel" class="button" value=" Cancel " onClick="return cancel('DailyProcessing.php');">
                          &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDailyProcessing(document.frmDailyProcessing);">                        </td>
                        <?} else{?>
                        <td align="center" colspan="3" nowrap> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyProcessing.php');">
                          &nbsp;&nbsp; <input type="submit" name="cmdAddDailyProcessing" class="button" value=" Add " onClick="return validateAddDailyProcessing(document.frmDailyProcessing);">                        </td>
                        <?} ?>
                      </tr>
                      <input type="hidden" name="hidDailyProcessingId" value="<?=$dailyProcessingId;?>">
                      <!--input type="hidden" name="entryId" value="<?=$lastId?>"-->
                      <tr>
                        <td colspan="5" nowrap class="fieldName" align="center" >
						<table width="80%">
						<tr>
						<td class="fieldName">Date</td>
						<td><input name="currentDate" type="text" id="currentDate" size="7" value="<? if($editMode==true) { echo $enteredDate; } else { echo date("d/m/Y");}?>" readonly/></td>
						<td class="fieldName">Lot No </td>
						<td><? if($addMode==true) {?>
                          <input name="lotNo" type="text" id="lotNo" size="5" value="<?=$dailyLotNo;?>" />
                          <? } else {
											 	
												if($p["lotNo"]!=""){
											 		$dailyProcessingLot	=	$p["lotNo"];
											 	}
											  ?>
                          <input name="lotNo" type="text" id="lotNo" size="5" value="<?=$dailyProcessingLot;?>" />
                          <? }?></td>
						<td class="fieldName">unit</td>
						<td><? $dailyUnit=$p["unit"];?>
                          <select name="unit">
                            <option value="">-- Select --</option>
                            <? foreach($plantRecords as $pr)
													{
														$i++;
														$plantId		=	$pr[0];
														$plantNo		=	stripSlash($pr[1]);
														$plantName		=	stripSlash($pr[2]);
														$selected="";
														if($plantId	== $dailyUnit || $dailyProcessingUnit==$plantId)
														{
														$selected	=	"selected";
														}
											?>
                            <option value="<?=$plantId?>" <?=$selected?>>
                            <?=$plantName?>
                            </option>
                            <? }?>
                          </select></td>
						</tr>
						<tr>
						  <td class="fieldName">*Fish</td>
						  <td><? if($addMode==true){
												$fishId		=	$p["selFish"];
												if($fishId!="" || $editFishId!=""){
													if ($editFishId!="")
													$fishId	=	$editFishId ;
							$processCodeRecords	=	$processcodeObj->processCodeRecFilter($fishId);
											}
												?>
                            <select name="selFish" id="selFish" onchange="this.form.submit();">
                              <? } else {?>
                              <select name="selFish" id="selFish" onchange="this.form.editId.value=<?=$editId?>;this.form.submit();">
                              <? }?>
                              <option value="" >--- Select Fish --- </option>
                              <?
															if( sizeof($fishMasterRecords)> 0 )
															{
																foreach( $fishMasterRecords as $fl )
																{
																	$id		=	$fl[0];
																	$name	=	$fl[1];
																	
																	$selected		=	"";
																	if( $fishId==$id || $id ==$editFishId){
																		$selected	=	" selected ";
																	}
														?>
                              <option value="<?=$id;?>" <?=$selected?> >
                              <?=$name;?>
                              </option>
                              <?
																}
															}
														?>
                            </select></td>
						  <td class="fieldName">*Process</td>
						  <td><? if($addMode==true) { $dailyProcessCode=$p["processCode"];?>
                            <select name="processCode" onchange="this.form.submit();">
                              <? } else { ?>
                              <select name="processCode" onchange="this.form.editId.value=<?=$editId?>;this.form.submit();">
                              <? }?>
                              <option value="">-- Select --</option>
                              <?
											if( sizeof($processCodeRecords)>0 )
														{
															foreach ($processCodeRecords as $fl)
															{
																$processCodeId		=	$fl[0];
																$processCode		=	$fl[2];
																//$quality			=	$fl[7];
																//$displayCode	= $processCode."&nbsp;"."(".$quality.")";
																
																$selected	=	"";
															if( $dailyProcessCode == $processCodeId || $processCodeId==$editProcessId){
																	$selected	=	"selected";
																}
															
													?>
                              <option value="<?=$processCodeId;?>" <?=$selected;?> >
                              <?=$processCode;?>
                              </option>
                              <?
															}
														}
													?>
                            </select></td>
						  <td class="fieldName">Packing</td>
						  <td><? if($addMode==true) {$packingSel= $p["packingType"];?>
                            <select name="packingType" id="packingType" onchange="this.form.submit();">
                              <? } else {?>
                              <select name="packingType" id="packingType" onchange="this.form.editId.value=<?=$editId?>;this.form.submit();">
                              <? }?>
                              <option value="">--select--</option>
                              <?
											  foreach($packingGoodsRecords as $pg)
													{
														
														$i++;
														$packingId		=	$pg[0];
														$packingCode	=	stripSlash($pg[1]);
														$description	=	stripSlash($pg[2]);
														$weight			=	$pg[3];
														$unit			=	$pg[4];
														
														$selected		=	"";
											  			if( $packingId == $packingSel){
														$selected	=	" selected ";
														}
														?>
                              <option value="<?=$packingId;?>" <?=$selected?> >
                              <?=$packingCode;?>
                              </option>
                              <?
															}
														?>
                            </select></td>
						  </tr>
						</table>						</td>
                      </tr>
                      
                      <tr> 
                        <td colspan="5" nowrap class="fieldName" ><table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr> 
                              <td class="listing-item"><fieldset>
                                <legend>Grade</legend>
                                <? if($editFishId==""){?>
                                <iframe id="iFrame1" 
src ="DailyProcessingGrade.php?fishId=<?=$fishId?>&lotId=<? if($addMode==true){ echo $lastId;} else {echo $dailyProcessingId;}?>&packing=<?=$packingSel;?>&process=<?=$dailyProcessCode?>" width="260" frameborder="0" height="300"></iframe>
                                <? } else {?>
                                <iframe id="iFrame1" 
src ="DailyProcessingGrade.php?codeEditId=<?=$editProcessId?>&lotEditId=<?=$lotEditId;?>" width="260" frameborder="0" height="300"></iframe>
                                <? }?>
                                </fieldset></td>
                              <td>&nbsp;</td>
                              <td class="listing-item"><fieldset>
                                <legend>Grade List</legend>
                                <iframe id="iFrame2" name="iFrame2"
src ="DailyProcessingGradeList.php?fishId=<?=$fishId?>&lotId=<? if($addMode==true){ echo $lastId;} else {echo $dailyProcessingId;}?>&packing=<?=$packingSel;?>&process=<?=$dailyProcessCode?>" width="275" frameborder="0" height="300"></iframe>
                                </fieldset></td>
                            </tr>
                          </table></td>
                      </tr>
                      
                      <tr> 
                        <td colspan="3"  height="10" ></td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <td colspan="3" align="center"> 
						<input type="hidden" name="lotEditId"> 
                        <input type="hidden" name="editFishId">
						<input type="hidden" name="editProcessId"> 
						<input type="submit" name="cmdSaveCancel" class="button" value=" Cancel " onClick="return cancel('DailyProcessing.php');">
                          &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddDailyProcessing(document.frmDailyProcessing);">                        </td>
                        <?} else{?>
                        <td align="center" colspan="3"><input type="hidden" name="lotEditId"> 
                          <input type="hidden" name="editFishId"> <input type="hidden" name="editProcessId"> 
                          <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DailyProcessing.php');">
                          &nbsp;&nbsp; <input type="submit" name="cmdAddDailyProcessing" class="button" value=" Add " onClick="return validateAddDailyProcessing(document.frmDailyProcessing);">                        </td>
                        <input type="hidden" name="cmdAddNew" value="1">
                        <?}?>
                      </tr>
                      <tr> 
                        <td colspan="3"  height="10" ></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
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
			
			# Listing Processing Starts
		?>
    <tr>
      <td align="center" class="listing-item" valign="top"><img src="images/x.gif" width="20" height="20"> - The entire transactions are not completed.</td>
    </tr>
    <tr> 
      <td> <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
          <tr> 
            <td   bgcolor="white"> 
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Daily 
                    Processing </td>
                  <td background="images/heading_bg.gif"  > <table cellpadding="0" cellspacing="0" align="right">
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
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="10" > </td>
                </tr>
                <tr> 
                  <td colspan="3"> 
				  <table cellpadding="0" cellspacing="0" align="center">
                      <tr> 
                        <td><? if($del==true){?><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$dailyProcessingSize;?>);" ><? }?> 
                          &nbsp; <? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?> 
                          &nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyProcessing.php',700,600);"><? }?></td>
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
                  <td colspan="3" height="15" align="center" class="err1"> 
                    <?=$errDel;?>
                  </td>
                </tr>
                		<?
								}
							?>
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2" > <table cellpadding="1"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
                      <?
										if( sizeof($dailyProcessingRecords) > 0 )
											{
												$i	=	0;
								?>
                      <tr  bgcolor="#f2f2f2"  > 
                        <td width="20" align="center"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></td>
                        <td width="138" nowrap class="listing-head" >&nbsp;&nbsp;Lot No </td>
                        <td width="131" nowrap class="listing-head" >&nbsp;&nbsp;Unit</td>
                        <td width="108" class="listing-head" nowrp>&nbsp;&nbsp;Date</td>
						<? if($edit==true){?>
                        <td class="listing-head" nowrap > </td>
						<? }?>
                      </tr>
                      <? 
											foreach($dailyProcessingRecords as $pr)
												{
																								
										$i++;
										$dailyProcessingId		=	$pr[0];
										$dailyLotNo				=	$pr[1];
										//$dailyProcessingUnit	=	$pr[6];
										$dailyProcessingUnit	=	$pr[2];
						$plantRecords	=	$plantandunitObj->filterAllPlantRecords($dailyProcessingUnit);
										$unitName	=	$plantRecords[0][2];
										
										$Date			=	explode("-",$pr[3]);
										$enterDate		=	$Date[2]."/".$Date[1]."/".$Date[0];
										$catchEntryFlag		=	$pr[4];
						//<img src="images/x.gif" width="20" height="20">																												
										?>
                      <tr  bgcolor="WHITE"  > 
                        <td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dailyProcessingId;?>" ></td>
                        <td class="listing-item" nowrap >&nbsp;&nbsp;<input type="hidden" name="lotId_<?=$i;?>" value="<?=$dailyProcessingId;?>" /> 
                          <? if($dailyLotNo==""){?>
                          <img src="images/x.gif" width="20" height="20"> 
                          <? } else { echo $dailyLotNo; };?>                        </td>
                        <td class="listing-item" nowrap >&nbsp;&nbsp;<?=$unitName;?>                        </td>
                        <td class="listing-item" >&nbsp;&nbsp;<?=$enterDate?>                        </td>
						<? if($edit==true){?>
                        <td class="listing-item" width="45" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$dailyProcessingId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='DailyProcessing.php';"></td>
						<? }?>
                      </tr>
                      <?
												} 
										?>
                      <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      <input type="hidden" name="editId" value="">
                      <input type="hidden" name="editSelectionChange" value="0">
					  <? if($catchEntryFlag==0){?>
					  <input type="hidden" name="entryId" id="entryId" value="<?=$dailyProcessingId?>">
					  <? } else {?>
					  <input type="hidden" name="entryId" id="entryId" value="<?=$lastId?>">
					  <? } ?>
                      <?
											}
											else
											{
										?>
                      <tr bgcolor="white"> 
                        <td colspan="6"  class="err1" height="10" align="center"> 
                          <?=$msgNoRecords;?>                        </td>
                      </tr>
                      <?
											}
										?>
                      
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
                <tr > 
                  <td colspan="3"> <table cellpadding="0" cellspacing="0" align="center">
                      <tr> 
                        <td><? if($del==true){?><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$dailyProcessingSize;?>);" ><? }?> 
                          &nbsp; <? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?> 
                          &nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintDailyProcessing.php',700,600);"><? }?></td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
              </table></td>
          </tr>
        </table>
        <!-- Form fields end   -->
      </td>
    </tr>
    <tr> 
      <td height="10"> </td>
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
