<? 
require("include/include.php");

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
<form name="frmEntryQuality" action="CatchEntryQuality.php" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
    </tr>
  <tr>
    <td class="fieldName">Quality</td>
    <td><span class="fieldName">Percent</span></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="fieldName"><select name="entryQuality" style="width:90%;">
      <option value="">-- Select --</option>
      <option value="1">Europe</option>
      <option value="2">Grade A</option>
      <option value="3">Grade A - China</option>
      <option value="4">Grade A - Korea</option>
      <option value="5">Grade B</option>
      <option value="6">Grade B -China</option>
    </select></td>
    <td class="listing-item" nowrap><input name="qualityPercent" type="text" id="qualityPercent" size="2"  style="text-align:right;" />
%</td>
    <td class="listing-item"><input type="hidden" name="curEntryId" value="<?=$currentId?>" />
      <input type="submit" name="cmdQuality" class=button value="Add" /></td>
  </tr>
  
  
  <tr>
    <td colspan="3" class="fieldName"><table width="100%" border="0" cellpadding="0" cellspacing="0">
	<?
	
								if( sizeof($qualityRecords)){
										
											$i	=	0;
											?>
      <tr>
        <td colspan="3" align="center">&nbsp;&nbsp;</td>
        </tr>
      <tr bgcolor="#f2f2f2" class="listing-head">
        <td nowrap>&nbsp;</td>
        <td nowrap>Quality </td>
        <td nowrap>Percent(%) </td>
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
      <tr>
        <td class="listing-item" nowrap><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$qualityId;?>" ></td>
        <td class="listing-item" nowrap><input type="hidden" name="qualityId_<?=$i;?>" value="<?=$qualityId?>" /><input type="text" name="entryQuality_<?=$i;?>" value="<?=$quality?>" size="15" readonly style="border:none" ></td>
        <td class="listing-item" nowrap><input name="qualityPercent_<?=$i;?>" type="text" style="width:30%;" value="<?=$percent?>" size="3" maxlength="3" /></td>
      </tr>
      
	  <? }?>
	  <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	  
	  <? }?>
	  <tr>
	    <td colspan="3" align="center">&nbsp;</td>
	    </tr>
	  <tr>
	    <td colspan="3" align="center"><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$qualityRecordSize;?>);">
	      &nbsp;&nbsp;
	      <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " /></td>	    </tr>
	  
	  
	  
	  
    </table></td>
    </tr>
</table>
<script language="javascript">
function getValueEntryId(){
var curr_Id	= parent.document.frmDailyCatch.entryId.value;
//alert(curr_Id);
document.frmEntryQuality.curEntryId.value	=	curr_Id;

}
getValueEntryId();
//document.frmEntryQuality.submit();
</script>
</form>
