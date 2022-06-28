<?
	require("include/include.php");
	$editMode		=	true;
	
	//if( $p["editId"]!="" ){
		$editId				=	$g["printId"];
		$editMode			=	true;
		$catchEntryRec			=	$dailycatchentryObj->find($editId);

		$recordId			=	$catchEntryRec[0];
		
		//$recordUnit			=	$catchEntryRec[1];
		$plantRec			=	$plantandunitObj->find($catchEntryRec[1]);
		$recordUnit			=	stripSlash($plantRec[2]);
		
		$recordDate			=	$catchEntryRec[2];
		$recordVechNo		=	$catchEntryRec[3];
		$recordChallanNo	=	$catchEntryRec[4];
		$recordWeighNo		=	$catchEntryRec[5];
		
		//$recordLanding		=	$catchEntryRec[6];
		$centerRec			=	$landingcenterObj->find($catchEntryRec[6]);
		$recordLanding		=	stripSlash($centerRec[1]);
		
		//$recordMainSupply	=	$catchEntryRec[7];
		
		$supplierRec		=	$supplierMasterObj->find($catchEntryRec[7]);
		$recordMainSupply		=	stripSlash($supplierRec[2]);
		
//		$recordSubSupply	=	$catchEntryRec[8];
		
		$subsupplierRec		=	$subsupplierObj->find($catchEntryRec[8]);
		$recordSubSupply	=	($catchEntryRec[8]==0 || $catchEntryRec[8]=="" )?"SELF":stripSlash($subsupplierRec[2]);
		
//		$recordFish			=	$catchEntryRec[9];		
		
		$fishRec			=	$fishmasterObj->find($catchEntryRec[9]);
		$recordFish			=	stripSlash($fishRec[1]);
		
		$recordProcessCode	=	$catchEntryRec[10];

		$processCodeRec		=	$processcodeObj->find($recordProcessCode);
		$ProcessCode		=	stripSlash($processCodeRec[2]);
			
		$recordIceWt		=	$catchEntryRec[11];
		$recordCount		=	$catchEntryRec[12];
		$recordAverage		=	$catchEntryRec[13];
		$recordLocalQty		=	$catchEntryRec[14];
		$recordWastage		=	$catchEntryRec[15];
		$recordSoft			=	$catchEntryRec[16];
		$recordReason		=	$catchEntryRec[17];
		$recordAdjust		=	$catchEntryRec[18];
		$recordGood			=	$catchEntryRec[19];
		$recordPeeling		=	$catchEntryRec[20];
		$recordRemarks		=	$catchEntryRec[21];
		$entryActualWt		=	$catchEntryRec[22];
		$entryEffectiveWt	=	$catchEntryRec[23]-$recordAdjust;
		$netGrossWt			=	$catchEntryRec[26];
		$recordDeclWeight	=	$catchEntryRec[27];
		$recordDeclCount	=	$catchEntryRec[28];
		$recordSelectDate	=	$catchEntryRec[29];
		
		//$recordGradeId		=	$catchEntryRec[30];
		$gradeRec		=	$grademasterObj->find($catchEntryRec[30]);
		$recordGradeId		=	stripSlash($gradeRec[1]);
		
		$processBasketWt	=	$catchEntryRec[31];	
		
		$entryLocalPercent		=	number_format((($recordLocalQty*100)/$entryActualWt),2);
		$entryWastagePercent	=	number_format((($recordWastage*100)/$entryActualWt),2);
		$entrySoftPercent		=	number_format((($recordSoft*100)/$entryActualWt),2);
		
		
		
		$processCodeRec			=	$processcodeObj->find($recordProcessCode);
		$receivedBy				=	$processCodeRec[7];


?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td width="291" background="images/heading_bg.gif" class="pageName" >&nbsp;
                    DAILY CATCH ENTRY                  </td>
                  <td width="290" background="images/heading_bg.gif" class="pageName" >
				  <table width="100%" border="0" cellpadding="0" cellspacing="0">
                      <tr> 
                        <td width="90%" align="right"><span class="listing-item">
                          <? if ($editMode) { echo $recordDate; } else { echo $today=date("j F Y, g:i a");} ?>
                          </span></td>
                        <td width="10%" class="listing-item" align="right">&nbsp;</td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2" ><table cellpadding="0"  width="81%" cellspacing="0" border="0" align="center">
                    <tr>
                      <td width="27%" height="10" ></td>
                    </tr>
                    <tr>
                      <td colspan="4" align="center"><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:window.print();"></td>
                      <? if($editMode){?>
                      <?} else{?>
                      <?}?>
                    </tr>
                    <input type="hidden" name="hidDailyCatchId" value="<?=$recordId;?>" />
                   
                    <tr>
                      <td colspan="4" nowrap class="fieldName" ><table width="100%" border="0" cellpadding="4" cellspacing="2" align="center">
                          <tr>
                            <td width='30%'><table>
                                <tr>
                                  <td align="left"  class="fieldName" >Unit:</td>
                                  <td align="left"  class="listing-item"><?=$recordUnit?></td>
                                </tr>
                                <tr>
                                  <td class="fieldName" nowrap="nowrap">Landing Center:</td>
                                  <td class="listing-item"><?=$recordLanding?></td>
                                </tr>
                                <tr>
                                  <td class="fieldName" nowrap="nowrap">Main Supplier:</td>
                                  <td class="listing-item"><?=$recordMainSupply?></td>
                                </tr>
                                <tr>
                                  <td class="fieldName" nowrap="nowrap">Sub Supplier:</td>
                                  <td class="listing-item"><?=$recordSubSupply?></td>
                                </tr>
                            </table></td>
                            <td><table>
                                <tr>
                                  <td class="fieldName">Vehicle No:</td>
                                  <td class="listing-item"><?=$recordVechNo;?></td>
                                </tr>
                                <tr>
                                  <td class="fieldName">Suppliers Challan No:</td>
                                  <td class="listing-item"><?=$recordChallanNo?></td>
                                </tr>
                                <tr>
                                  <td class="fieldName">Weighment Challan No:</td>
                                  <td class="listing-item"><?=$recordWeighNo?></td>
                                </tr>
                                <tr>
                                  <td class="fieldName">Entry Date </td>
                                  <td class="listing-item"><?=$recordSelectDate?></td>
                                </tr>
                                <tr>
                                  <td class="fieldName">Entry Time </td>
                                  <td class="listing-item">&nbsp;</td>
                                </tr>
                            </table></td>
                            <td><table>
                                <tr>
                                  <td valign="top"><fieldset>
                                    <legend class="fieldName">Baskets/ Ice Details</legend>
                                    <table width="80%" border="0" cellpadding="0" cellspacing="0">
                                      <tr>
                                        <td class="fieldName" nowrap>Ice : </td>
                                        <td class="listing-item" nowrap><?=$recordIceWt?> Kg </td>
                                      </tr>
                                    </table>
                                  </fieldset></td>
                                </tr>
                            </table></td>
                          </tr>
                      </table></td>
                    </tr>
                    <tr>
                      <td rowspan="8" valign="top" nowrap class="fieldName" align="center" ><fieldset>
                        <legend>Count Details </legend>
                        <iframe 
src ="PrintCatchEntryGrosswt.php?lastId=<?=$recordId?>&newWt=<?=$resetBasketWt?>&basketWt=<?=$processBasketWt?>" width="275" frameborder="0" height="400" marginwidth="2" ></iframe>
                        </fieldset>                        �</td>
                      <td colspan="3" valign="top" ><table width="100%" cellpadding="0" cellspacing="0">
                          <tr>
                            <td height="79" valign="top"><table>
                                <tr>
                                  <td class="fieldName">*Fish: </td>
                                  <td class="listing-item"><?=$recordFish?></td>
                                  <td class="fieldName" nowrap="nowrap">&nbsp;&nbsp;*Raw Material Code </td>
                                  <td class="listing-item"><?=$ProcessCode?></td>
                                </tr><input type="hidden" name="hidReceived" value="<?=$receivedBy?>">
								<? 
									if($receivedBy=='C' ){
								?>
                                <tr>
								  <td class="fieldName">Count:</td>
                                  <td class="listing-item"><?=$recordCount?></td>
                                  <td class="fieldName">Average:</td>
                                  <td class="listing-item"><?=$recordAverage?></td>
                                </tr>
								<? 
								} else if ($receivedBy=='G' ) {
								?>
                                <tr>
                                  <td class="fieldName">Grade</td>
                                  <td class="listing-item"><?=$recordGradeId?></td>
                                  <td class="fieldName">&nbsp;</td>
                                  <td>&nbsp;</td>
                                </tr>
                               <? }?>
                            </table></td>
                            <td valign="top"><table>
                                <tr>
                                  <td class="fieldName" valign="top"><fieldset>
                                    <legend class="fieldName">Declared</legend>
                                    <table border="0" cellpadding="0" cellspacing="0">
                                      <tr>
                                        <td class="fieldName" nowrap>Weight:</td>
                                        <td class="listing-item" nowrap><?=$recordDeclWeight?></td>
                                      </tr>
                                      <tr>
                                        <td class="fieldName" nowrap>Count:</td>
                                        <td class="listing-item" nowrap><?=$recordDeclCount?></td>
                                      </tr>
                                    </table>
                                  </fieldset></td>
                                </tr>
                            </table></td>
                          </tr>
                      </table></td>
                    </tr>
                    <tr>
                      <td width="1%" rowspan="2" class="listing-item"><fieldset>
                        <legend>Adjustments</legend>
                        <table>
                          <tr>
                            <td class="fieldName">&nbsp;</td>
                            <td colspan="2" class="fieldName"> Basket Weight: </td>
                            <td class="listing-item" nowrap align="right">&nbsp;</td>
                            <td class="listing-item" nowrap align="right"><?=$processBasketWt?> Kg </td>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td class="fieldName">&nbsp;</td>
                            <td colspan="2" class="fieldName"> Net Wt: </td>
                            <td class="listing-item" nowrap align="right">&nbsp;</td>
                            <td class="listing-item" nowrap align="right"><?=$netGrossWt?> Kg </td>
                          </tr>
                          <tr>
                            <td class="fieldName">&nbsp;</td>
                            <td colspan="2" class="fieldName"> Adjustment: </td>
                            <td class="listing-item" nowrap align="right">&nbsp;</td>
                            <td class="listing-item" nowrap align="right"><?=$recordAdjust?> Kg </td>
                          </tr>
                          <tr>
                            <td class="fieldName" nowrap>&nbsp;</td>
                            <td colspan="4" nowrap class="fieldName">Reason: &nbsp;&nbsp;&nbsp;<span class="listing-item"><?=$recordReason?></span></td>
                          </tr>
                          <tr>
                            <td class="fieldName" nowrap>&nbsp;</td>
                            <td colspan="2" nowrap class="fieldName">Actual Wt: </td>
                            <td class="listing-item" align="right">&nbsp;</td>
                            <td class="listing-item" align="right" nowrap="nowrap">
							<?=$entryActualWt?>
                              Kg</td>
                          </tr>
                          <tr>
                            <td colspan="6"><table>
                                <tr>
                                  <td class="fieldName" nowrap>Local Quantity:</td>
                                  <td class="listing-item" nowrap="nowrap"><?=$entryLocalPercent?> % </td>
                                  <td class="listing-item"><?=$recordLocalQty?> Kg</td>
                                </tr>
                                <tr>
                                  <td class="fieldName" nowrap>Wastage:</td>
                                  <td class="listing-item"><?=$entryWastagePercent?> % </td>
                                  <td class="listing-item"><?=$recordWastage?> Kg</td>
                                </tr>
                                <tr>
                                  <td class="fieldName" nowrap>Soft:</td>
                                  <td class="listing-item"><?=$entrySoftPercent?> %</td>
                                  <td class="listing-item"><?=$recordSoft?> Kg </td>
                                </tr>
                            </table></td>
                          </tr>
                          <tr>
                            <td nowrap class="fieldName">&nbsp;</td>
                            <td nowrap class="fieldName" align="left">&nbsp;</td>
                            <td nowrap class="fieldName" align="left">Effective Weight</td>
                            <td align="center" class="listing-item">&nbsp;</td>
                            <td colspan="1" align="right" class="listing-item" nowrap="nowrap"><?=$entryEffectiveWt?> Kg</td>
                          </tr>
                        </table>
                      </fieldset></td>
                      <td nowrap class="listing-item" valign="top"><fieldset>
                        <legend>Quality</legend>
                        <iframe 
src ="PrintCatchEntryQuality.php?entryId=<? if($editMode) {echo $recordId;} else { echo $lastId;}?>" width="250" frameborder="0"></iframe>
                      </fieldset></td>
                    </tr>
                    <tr>
                      <td nowrap class="listing-item" valign="top"><table>
                          <tr>
                            <td class="fieldName" nowrap>Good for Packing:</td>
                            <td class="listing-item"><?=$recordGood?> %</td>
                          </tr>
                          <tr>
                            <td class="fieldName">For Peeling: </td>
                            <td class="listing-item"><?=$recordPeeling?> %</td>
                          </tr>
                          <tr>
                            <td class="fieldName">Remarks:</td>
                            <td class="listing-item"><?=$recordRemarks?></td>
                          </tr>
                      </table></td>
                    </tr>
                    <tr>
                      <td colspan="3"><table width="100%" border="0" cellpadding="3" cellspacing="2">
                          <tr>
                            <td width="40%" nowrap class="fieldName" valign="top"></td>
                          </tr>
                      </table></td>
                    </tr>
                    <tr>
                      <td colspan="4"  height="10" ></td>
                    </tr>
                    <tr>
                      <? if($editMode){?>
                      <td colspan="3" align="center" nowrap><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:window.print();"></td>
                      <?} else{?>
                      <input type="hidden" name="cmdAddNew" value="1" />
                      <?}?>
                    </tr>
                    
                  </table></td>
                </tr>
              </table>