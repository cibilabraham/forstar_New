<?php
	require("include/include.php");
	$editMode		=	false;
	$exceptionMode		=	false;
	$saveChanged		=	false;
	$editExceptionCenterId	=	"";
	$yieldJan		=	"";
	$yieldFeb		=	"";
	$yieldMar		=	"";
	$yieldApr		=	"";
	$yieldMay		=	"";
	$yieldJun		=	"";
	$yieldJul		=	"";
	$yieldAug		=	"";
	$yieldSep		=	"";
	$yieldOct		=	"";
	$yieldNov		=	"";
	$yieldDec		=	"";
	
	if ($p["processMainId"]=="") {
		$processMainId	=	$g["lastId"];
	} else {
		$processMainId	=	$p["processMainId"];
	}
	
	$editModeId	=	($g["editId"]=="")?$p["editModeId"]:$g["editId"];
	
	if ($p["cmdException"]!="") {
		$selExceptionLanding		=	$p["selExceptionLanding"];
		if ($selExceptionLanding!="") {
			$insertExceptionCenter		=	$processObj->addExceptionCenter($selExceptionLanding,$processMainId);
			
			if ($insertExceptionCenter) {
				
				$lastAddedExceptionId	=	$databaseConnect->getLastInsertedId();
				$exceptionMode		=	true;
			}
		}
	}
	
	if ($p["cmdSaveYield"]!="") {
	
		$selLandingCenter		=	$p["selLandingCenter"];
		
		$lastAddedExceptionId		=	$p["lastAddedExceptionId"];
		
			$Jan				=	$p["processYieldJan"];
			$Feb				=	$p["processYieldFeb"];
			$Mar				=	$p["processYieldMar"];
			$Apr				=	$p["processYieldApr"];
			$May				=	$p["processYieldMay"];
			$Jun				=	$p["processYieldJun"];
			$Jul				=	$p["processYieldJul"];
			$Aug				=	$p["processYieldAug"];
			$Sep				=	$p["processYieldSep"];
			$Oct				=	$p["processYieldOct"];
			$Nov				=	$p["processYieldNov"];
			$Dec				=	$p["processYieldDec"];
		
		
		$uniqueRecords		=	$processObj->fetchAllUniqueRecords($processMainId,$selLandingCenter);
		if(sizeof($uniqueRecords)==0 && $lastAddedExceptionId==""){
		
			$landingYieldRecIns	=	$processObj->addYieldItem($selLandingCenter,$Jan,$Feb,$Mar,$Apr,$May,$Jun,$Jul,$Aug,$Sep,$Oct,$Nov,$Dec,$processMainId);		
		}
		
		
		if($lastAddedExceptionId!=""){
	
			$processYieldRecUptd	=	$processObj->updateProcessYieldMonths($lastAddedExceptionId,$Jan,$Feb,$Mar,$Apr,$May,$Jun,$Jul,$Aug,$Sep,$Oct,$Nov,$Dec);
		}
		
	}
	
	#Edit a Exception Landing Center
	if ($p["selLandingCenter"]!="" || $editModeId==1){
	
		if ($editModeId==1 && $p["selLandingCenter"]==0) {
			$processYieldId	= 0;
		} else {
			$processYieldId		=	$p["selLandingCenter"];
		}
		
		$editMode			=	true;
	
		if($processYieldId==0){
			$processYieldRec	=	$processObj->findAllCenterRec($processYieldId,$processMainId);
		} else {
			$processYieldRec	=	$processObj->findExceptionCenterRec($processYieldId);
		}
		
			$editProcessYieldId		=	$processYieldRec[0];
			$editExceptionCenterId		=	$processYieldRec[1];
			$yieldJan			=	$processYieldRec[3];
			$yieldFeb			=	$processYieldRec[4];
			$yieldMar			=	$processYieldRec[5];
			$yieldApr			=	$processYieldRec[6];
			$yieldMay			=	$processYieldRec[7];
			$yieldJun			=	$processYieldRec[8];
			$yieldJul			=	$processYieldRec[9];
			$yieldAug			=	$processYieldRec[10];
			$yieldSep			=	$processYieldRec[11];
			$yieldOct			=	$processYieldRec[12];
			$yieldNov			=	$processYieldRec[13];
			$yieldDec			=	$processYieldRec[14];
	
	}
	
	if( $p["cmdSaveChange"]!="" ){
			
			
			$processYieldId			=	$p["hidProcessYieldId"];
					
			$selLandingCenterId		=	$p["selLandingCenter"];
			
			$Jan				=	$p["processYieldJan"];
			$Feb				=	$p["processYieldFeb"];
			$Mar				=	$p["processYieldMar"];
			$Apr				=	$p["processYieldApr"];
			$May				=	$p["processYieldMay"];
			$Jun				=	$p["processYieldJun"];
			$Jul				=	$p["processYieldJul"];
			$Aug				=	$p["processYieldAug"];
			$Sep				=	$p["processYieldSep"];
			$Oct				=	$p["processYieldOct"];
			$Nov				=	$p["processYieldNov"];
			$Dec				=	$p["processYieldDec"];
			
			
			
			if($processYieldId!="")
			{
				$processYieldRecUptd	=	$processObj->updateProcessYieldMonths($processYieldId,$Jan,$Feb,$Mar,$Apr,$May,$Jun,$Jul,$Aug,$Sep,$Oct,$Nov,$Dec);
			}
		
			if($processYieldRecUptd)
			{
				$saveChanged=true;
				$editModeId=1;
			}
			else
			{
				$editMode	=	true;
				$err		=	$msg_failProcessUpdate;
			}
			$processYieldRecUptd	=	false;
		}
	
	#List All Exception Landing Centers
	
		$exceptionLandingCenterRecords	=	$processObj->fetchAllExceptionCenterRecords($processMainId);
		
		
		if($exceptionMode==	true) {
			$editExceptionCenterId		=	$exceptionLandingCenterRecords[0][0];
			$processYieldId	= 0;
		}
		
		
	if( $p["cmdDelete"]!=""){
	
		$processYieldId			=	$p["hidProcessYieldId"];
		
		if ($processYieldId!="") {	
			$processYieldRecDel		=	$processObj->deleteProcessYield($processYieldId);	
		}
		if ($processYieldRecDel) {
			$processYieldId = 0;
			$editMode	=	true;	
			$editModeId	=	1;
		}
	}
	
	#List All Landing Centers
		$landingCenterRecords	=	$landingcenterObj->fetchAllRecords();
		
	#Fetch All Exception list
	
	$fetchAllLandingCenterException	= $processObj->filterAllExceptionLandingCenter($processMainId);
	$exceptionListRecSize	=	sizeof($fetchAllLandingCenterException);
?>
<html>
<head>
<TITLE></TITLE>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!-- 
body {
	behavior:url("libjs/csshover.htc");
}
-->
</style>
<script language="JavaScript" type="text/JavaScript" src="libjs/process.js"></script>
<script type="text/javascript" src="libjs/generalFunctions.js"></script>
</head>
<body marginheight="0" marginwidth="0" bgcolor="#e8edff">
<form name="frmProcessYield" id="frmProcessYield" action="ProcessYield.php" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0" >
  <tr><td align="center">
  <table width="100%">
    <tr><td>
  <table width="200">
    <tr>
     <td  height="10" class="fieldName" nowrap="nowrap">Landing Center:</td>
		<td  height="10" >
		<select name="selLandingCenter" id="selLandingCenter" onchange="this.form.submit();">
                <option value="0">-All Landing Centers-</option>
                <?
		foreach($exceptionLandingCenterRecords as $elcr)
		{
			$centerId	=	$elcr[0];
			$centerName	=	stripSlash($elcr[3]);
			$selected="";
			if(($editExceptionCenterId==$centerId) || $processYieldId==$centerId){
			$selected="Selected";
		}
		?>
                <option value="<?=$centerId?>" <?=$selected?>><?=$centerName;?></option>
                <? }?>
                </select></td></tr>
  </table>
  </td>
  <td align="right">
   <table width="200">
     <tr>
      <td><select name="selExceptionLanding" id="selExceptionLanding">
                                            <option value="">-- Select --</option>
                                            <?
										  foreach($landingCenterRecords as $fr)
													{
														$centerId	=	$fr[0];
														$centerName	=	stripSlash($fr[1]);
											?>
                                           <option value="<?=$centerId?>"><?=$centerName;?></option>
                                            <? }?>
                                          </select></td>
      <td><input name="cmdException" type="submit" class="button" id="cmdException" style="width:90px;" value="Add Exception"></td>
    </tr>
  </table></td>
  </tr></table>
  </td></tr>
	 <tr>
                       <td align="center" class="listing-head">MONTHWISE % YIELD </td>
         </tr>
  <tr><td style="padding-left:10px; padding-right:10px;">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" id="newspaper-b1">
                                      <!-- <tr>
                                              <td colspan="12" align="center" class="listing-head">MONTHWISE % YIELD </td>
                                          </tr>-->
					<thead>
                                            <tr align="center" >
                                              <th>JAN</th>
                                              <th>FEB</th>
                                              <th>MAR</th>
                                              <th>APR</th>
                                              <th>MAY</th>
                                              <th>JUN</th>
                                              <th>JUL</th>
                                              <th>AUG</th>
                                              <th>SEP</th>
                                              <th>OCT</th>
                                              <th>NOV</th>
                                              <th>DEC</th>
                                            </tr>
					</thead>
					<tbody>
                                            <tr  class="listing-item" align="center">
                                              <td><? if($editMode){
											 if($yieldJan==0 || $exceptionMode==true) $yieldJan=100;
											  ?>
											  <input name="processYieldJan" type="text" value="<?=$yieldJan?>" size="2" maxlength="5" style="text-align:center">
                                              <? } else {?>
                                              <input name="processYieldJan" type="text" value="100" size="2" align="middle" maxlength="5" style="text-align:center">                                              <? }?></td>
                                              <td>
											  <? if($editMode){
											 if($yieldFeb==0 || $exceptionMode==true) $yieldFeb=100;
											  ?><input name="processYieldFeb" type="text" value="<?=$yieldFeb?>" size="2" maxlength="5" style="text-align:center">
                                              <? } else {?>
                                              <input name="processYieldFeb" type="text" value="100" size="2" align="middle" maxlength="5" style="text-align:center">                                              <? }?></td>
                                              <td>
											  <? if($editMode){
											 if($yieldMar==0 || $exceptionMode==true) $yieldMar=100;
											  ?><input name="processYieldMar" type="text" value="<?=$yieldMar?>" size="2" maxlength="5" style="text-align:center">
                                              <? } else {?>
                                              <input name="processYieldMar" type="text" value="100" size="2" align="middle" maxlength="5" style="text-align:center">                                              <? }?></td>
                                              <td>
											  <? if($editMode){
											 if($yieldApr==0 || $exceptionMode==true) $yieldApr=100;
											  ?><input name="processYieldApr" type="text" value="<?=$yieldApr?>" size="2" maxlength="5" style="text-align:center">
                                              <? } else {?>
                                              <input name="processYieldApr" type="text" value="100" size="2" align="middle" maxlength="5" style="text-align:center">                                              <? }?></td>
                                              <td><? if($editMode){
											  if($yieldMay==0 || $exceptionMode==true) $yieldMay=100;
											  ?><input name="processYieldMay" type="text" value="<?=$yieldMay?>" size="2"  maxlength="5" style="text-align:center">
                                                <? } else {?>
												<input name="processYieldMay" type="text" value="100" size="2" align="middle" maxlength="5" style="text-align:center">                                              <? }?></td>
                                              <td><? if($editMode){
											  if($yieldJun==0 || $exceptionMode==true) $yieldJun=100;
											  ?><input name="processYieldJun" type="text" value="<?=$yieldJun?>" size="2" maxlength="5" style="text-align:center">
                                                <? } else {?>
                                              <input name="processYieldJun" type="text" value="100" size="2" align="middle" maxlength="5" style="text-align:center">                                              <? }?></td>
                                              <td><? if($editMode){
											  if($yieldJul==0 || $exceptionMode==true) $yieldJul=100;
											  ?><input name="processYieldJul" type="text" value="<?=$yieldJul?>" size="2" maxlength="5" style="text-align:center">
                                              <? } else {?>
                                              <input name="processYieldJul" type="text" value="100" size="2" align="middle" maxlength="5" style="text-align:center">                                              <? }?></td>
                                              <td><? if($editMode){
											  if($yieldAug==0 || $exceptionMode==true) $yieldAug=100;
											  ?><input name="processYieldAug" type="text" value="<?=$yieldAug?>" size="2" maxlength="5" style="text-align:center">
                                              <? } else {?>
                                              <input name="processYieldAug" type="text" value="100" size="2" align="middle" maxlength="5" style="text-align:center">                                              <? }?></td>
                                              <td><? if($editMode){
											  if($yieldSep==0 || $exceptionMode==true) $yieldSep=100;
											  ?><input name="processYieldSep" type="text" value="<?=$yieldSep?>" size="2" maxlength="5" style="text-align:center">
                                              <? } else {?>
                                              <input name="processYieldSep" type="text" value="100" size="2" align="middle" maxlength="5" style="text-align:center">                                              <? }?></td>
                                              <td><? if($editMode){
											  if($yieldOct==0 || $exceptionMode==true) $yieldOct=100;
											  ?><input name="processYieldOct" type="text" value="<?=$yieldOct?>" size="2" maxlength="5" style="text-align:center">
                                              <? } else {?>
                                              <input name="processYieldOct" type="text" value="100" size="2" align="middle" maxlength="5" style="text-align:center">                                              <? }?></td>
                                              <td><? if($editMode){
											  if($yieldNov==0 || $exceptionMode==true) $yieldNov=100;
											  ?><input name="processYieldNov" type="text" value="<?=$yieldNov?>" size="2" maxlength="5" style="text-align:center">
                                              <? } else {?>
                                              <input name="processYieldNov" type="text" value="100" size="2" align="middle" maxlength="5" style="text-align:center">                                              <? }?></td>
                                              <td><? if($editMode){
											  if($yieldDec==0 || $exceptionMode==true) $yieldDec=100;
											  ?><input name="processYieldDec" type="text" value="<?=$yieldDec?>" size="2" maxlength="5" style="text-align:center">
                                              <? } else {?>
                                              <input name="processYieldDec" type="text" value="100" size="2" align="middle" maxlength="5" style="text-align:center">                                              <? }?></td>
                                            </tr>
                                            
                                            
                                          </table></td></tr>
										  <tr><td align="center"><table width="200">
                                            <tr>
                                              <td align="center"><input type="hidden" name="processMainId" value="<?=$processMainId?>"><input type="hidden" name="lastAddedExceptionId" value="<?=$lastAddedExceptionId?>" /><? if($editMode==true && $editProcessYieldId!=""){?><input name="cmdSaveChange" type="submit" class="button" id="cmdSaveChange" value="Save Change"> &nbsp;&nbsp;<? if($p["selLandingCenter"]!=0){?><input type="submit" value=" Delete " name="cmdDelete" class="button" onclick="return confirmDeleteException();"><? } } else {?><input name="cmdSaveYield" type="submit" class="button" id="cmdSaveYield" value="Save Yield"><? }?>
											  
											  <? if($exceptionMode==true){?>
                                              <input type="hidden" name="hidProcessYieldId" value="<?=$lastAddedExceptionId;?>">
											  <? } else {?>
											  
											  <input type="hidden" name="hidProcessYieldId" value="<?=$editProcessYieldId;?>">
											  <? }?>
											  </td>
                                            </tr>
					</tbody>
                                          </table>
					<INPUT type="hidden" name="editModeId" value="<?=$editModeId?>">
				</td></tr>
	 </table>
  <?
  	if($processYieldRecDel || $p["cmdSaveChange"]!=""){
  ?>
  <script type="text/javascript">
  parent.iFrame1.document.frmProcessYield.submit();
  </script>
  <?
  }
  ?>
</form>
</body>
</html>