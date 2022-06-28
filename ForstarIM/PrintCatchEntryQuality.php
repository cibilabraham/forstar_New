<? 
require("include/include.php");
//require("template/topLeftNav.php");
$err			=	"";

if($p["cmdQuality"]!=""){
$currentId		=	$p["curEntryId"];
$quality		=	$p["entryQuality"];
$percentage		=	$p["qualityPercent"];

if( $currentId!="" && $quality!="" && $percentage!="" )
		{
    		$qualityRecIns=$dailycatchentryObj->addQuality($currentId,$quality,$percentage);
		}
	
		if($qualityRecIns!="")
			{
				//$addMode	=	true;
				/*$sessObj->createSession("displayMsg",$msg_succAddDailyGrossWt);
				$sessObj->createSession("nextPage",$url_afterAddDailyGrossWt);*/
			}
			else
			{
				//$addMode	=	true;
				$err		=	$msg_failAddEntryQuality;
			}
			$dailyGrossRecIns		=	false;
}

if( $p["cmdSaveChange"]!=""){

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$qualityId	=	$p["qualityId_".$i];
			$percentage		=	$p["qualityPercent_".$i];
			$currentId		=	$p["curEntryId"];

			if( $qualityId!="" )
			{
			$qualityUpdateRec		=	$dailycatchentryObj->updateQuality($qualityId,$percentage,$currentId);	
			}

		}
		if($qualityUpdateRec)
		{
			//$sessObj->createSession("displayMsg",$msg_succUpdateQuality);
			//$sessObj->createSession("nextPage",$url_afterDelProcessor);
		}
		else
		{
			$err	=	$msg_failUpdateQuality;
		}
		$qualityUpdateRec	=	false;
	}


if( $p["cmdDelete"]!=""){

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$qualityId	=	$p["delId_".$i];

			if( $qualityId!="" )
			{
				// Need to check the selected Processor is link with any other process 
				$qualityRecDel		=	$dailycatchentryObj->deleteQuality($qualityId);	
			}

		}
		/*if($processorRecDel)
		{
			$sessObj->createSession("displayMsg",$msg_succDelProcessor);
			$sessObj->createSession("nextPage",$url_afterDelProcessor);
		}
		else
		{
			$errDel	=	$msg_failDelProcessor;
		}
		$processorRecDel	=	false;*/
	}


#fetch all quality records
if($p["curEntryId"]==""){
$currentId		=	$g["entryId"];
}
else {
$currentId		=	$p["curEntryId"];
}
$qualityRecords	=	$dailycatchentryObj->fetchAllQualityRecords($currentId);
$qualityRecordSize	=	sizeof($qualityRecords);
?>


<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript" src="libjs/dailycatchentry.js"></script>
<script type="text/javascript" src="libjs/generalFunctions.js"></script>
<form name="frmPrintEntryQuality" action="PrintCatchEntryQuality.php" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
    </tr>
  

  
  <tr>
    <td colspan="3" class="fieldName"><table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC">
	<?
	
								if( sizeof($qualityRecords)){
										
											$i	=	0;
											?>
      
      <tr bgcolor="#f2f2f2" class="listing-head">
        <td nowrap>&nbsp;&nbsp;Quality </td>
        <td nowrap>&nbsp;&nbsp;Percent(%) </td>
      </tr>
	  <?

										foreach($qualityRecords as $qr)
											{
											$i++;
											$qualityId		=	$qr[0];
											$quality		=	$qr[1];
											if($quality	== 1){
											$quality	=	"Europe";
											}
											else if($quality==2){
											$quality	=	"Grade A";
											}
											else if($quality==3){
											$quality	=	"Grade A - China";
											}
											else if($quality==4){
											$quality	=	"Grade A - Korea";
											}
											else if($quality==5){
											$quality	=	"Grade B";
											}
											else if($quality==6){
											$quality	=	"Grade B - China";
											}
   										
											$percent	=	$qr[2];
																								
											?>
      <tr bgcolor="#FFFFFF">
        <td class="listing-item" nowrap>&nbsp;&nbsp;<?=$quality?></td>
        <td class="listing-item" nowrap>&nbsp;&nbsp;<?=$percent?></td>
      </tr>
      
	  <? }?>
	  <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	  
	  <? }?>

    </table></td>
    </tr>
</table>

</form>
