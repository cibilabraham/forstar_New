<?
	require("include/include.php");
	$selDate 			= $g["selDate"];
	$Date1				=	explode("/",$selDate);
	$selectedDate			=	$Date1[2]."-".$Date1[1]."-".$Date1[0];

	$recordedDate		= 	date("j F Y", mktime(0, 0, 0, $Date1[1], $Date1[0], $Date1[2]));
	$selectUnit			=	$g["selUnit"];
	$selLotNoId			=	$g["selLotNo"];
	$selectFish 		= 	$g["selFish"];
	$selectProcessCode	=	$g["selProcessCode"];
	// Finding Plant Record
	$plantRec			=	$plantandunitObj->find($selectUnit);
	$plantName			=	stripSlash($plantRec[2]);
	#Finding Lot No	
	$dailyProcessingRec	=	$dailyprocessingObj->find($selLotNoId);
	$dailyProcessingLot		=	$dailyProcessingRec[1];
		
#Filter all Records		
	$dailyProcessingReportRecords	=	 $dailyprocessingreportObj -> filterDailyCatchReportRecords($selectUnit,$selLotNoId,$selectFish,$selectProcessCode,$selectedDate);

?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
  <tr bgcolor=white> 
    <td colspan="17" align="LEFT" class="listing-head" ><font size="3"><?=$companyArr["Name"];?></font> </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="LEFT" class="listing-item"><font size="2">DAILY PACKING 
      REPORT </font></td>
  </tr>
   <tr bgcolor=white> 
    <td colspan="17" align="LEFT" class="listing-item">&nbsp;</td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="left" class="fieldName"><table width="90%" cellpadding="0" cellspacing="0">
        <tr> 
          <td class="fieldName" valign="top"><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap>Packed at:</td>
                <td class="listing-item" nowrap="nowrap">&nbsp; 
                  <?=$plantName?>
                </td>
              </tr>
            </table></td>
          <td class="listing-item" align="center"><table width="100" cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName">On:</td>
                <td class="listing-item" nowrap> 
                  <?=$recordedDate?>                </td>
              </tr>
            </table></td>
          <td class="fieldName" align="center"><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName">Lot No :</td>
                <td class="listing-item" nowrap="nowrap"> 
                  <?=$dailyProcessingLot?>
                </td>
              </tr>
            </table></td>
          <td class="listing-item">&nbsp; </td>
        </tr>
      </table></td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName">&nbsp;</td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName">
						<?
	
								if( sizeof($dailyProcessingReportRecords)){
									
						?>
	<table width="70%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
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
								</table>
								<? } else {?>
	  <tr bgcolor="white"> 
      <td  class="err1" height="5" align="center" colspan="17"><?=$msgNoRecords;?></td>
    </tr>
	<? }?>
	</td>
  </tr>
  
  <SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>
