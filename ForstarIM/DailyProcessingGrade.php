<?
require("include/include.php");

$err				=	"";
$editChanged		=	false;
$editGradeChange	= 	false;
$saveChanged		=	false;

$fishId			=	$g["fishId"];
$lotNo			=	$g["lotId"];
$packingId		=	$g["packing"];
$processcodeId	=	$g["process"];

$codeEditId		=	$g["codeEditId"];
$lotEditId		=	$g["lotEditId"];


/*foreach($p as $val =>$key)
{
echo "<br>$val = $key";
}*/

			$fish_Id			=	$p["fishId"];
			$lot_Id				=	$p["lotId"];
			$packing_Id			=	$p["packingId"];			
			$code_Id			=	$p["codeId"];

			
## Updating the Grade quantity 

if($p["cmdSaveGradeChange"]){

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$gradeId		=	$p["gradeId_".$i];
			$quantity		=	$p["quantity_".$i];
			

			if( $gradeId!="")
			{
	$updateGradeRec		=	$dailyprocessingObj->updateprocessingGrade($gradeId,$quantity);
			}

		}
		if($updateGradeRec)
		{
			$sessObj->createSession("displayMsg",$msg_succInsProcessingGrade);
			$editGradeChange	= true;
			//$sessObj->createSession("nextPage",$url_afterDelProcessor);
		}
		else
		{
			$err	=	$msg_failInsProcessingGrade;
		}
		$updateGradeRec	=	false;
	}


if( $p["cmdSaveChange"]){

		$rowCount	=	$p["hidRowCount"];
		
		for($i=1; $i<=$rowCount; $i++)
		{
			$gradeId	=	$p["gradeId_".$i];
			$quantity		=	$p["quantity_".$i];
			$fishId			=	$p["fishId"];
			$lotId			=	$p["lotId"];
			$packingId		=	$p["packingId"];			
			$codeId			=	$p["codeId"];

			if( $gradeId!="" && $fishId!="" && $codeId!="" )
			{
	$gradeRecIns		=	$dailyprocessingObj->addProcessingGrade($gradeId,$quantity,$fishId,$lotId,$packingId,$codeId);
			}

		}
		if($gradeRecIns)
		{
			$sessObj->createSession("displayMsg",$msg_succInsProcessingGrade);
			$saveChanged=true;
			//$sessObj->createSession("nextPage",$url_afterDelProcessor);
		}
		else
		{
			$err	=	$msg_failInsProcessingGrade;
		}
		$gradeRecIns	=	false;

}

#List all Processing Grade 

$processingGradeRec	=	$dailyprocessingObj->fetchAllProcessingGradeRecords($fish_Id,$lot_Id,$packing_Id,$code_Id);

#List all grades based on fish Id
if($codeEditId!="" && $lotEditId!=""){
	$editGradeListRec		=	$dailyprocessingObj->findCodeId($codeEditId,$lotEditId);
	$editChanged	=	true;
		} 
		else 
		{
			//$gradeMasterRecords		=	$processcodeObj->processCodeRecFilter($fishId); rmoved shobu on 30-06-07
			$gradeMasterRecords		=	$processcodeObj->fetchGradeRecords($processcodeId);
		}
?>




<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript" src="libjs/dailyprocessing.js"></script>

<form name="frmDailyProcessingGrade"  id="frmDailyProcessingGrade" action="DailyProcessingGrade.php" method="post">
<table width="100%" border="0" cellpadding="2" cellspacing="1">

							
      
      <tr class="listing-head">
        <td colspan="3" nowrap align="center" class="err1"><? if($err!="" ){?><?=$err;?><?}?></td>
      </tr>
	  <?
	  	if($editChanged==true){
		
						if( sizeof($editGradeListRec)){
							$i	=	0;
				
			?>
      <tr bgcolor="#f2f2f2" class="listing-head">
        
        <td nowrap>Grade</td>
        <td nowrap>Quantity<br />(Nos)</td>
      </tr>
	  		<?
	  				foreach($editGradeListRec as $er)
					{
					$i++;
					$gradeCodeId			=	$er[0];
					$gradeName				=	$er[13];
					$gradeUnit				=	$er[14];
					$gradeQuantity			=	$er[6];
					$total	+= $gradeQuantity;
					$displayGrade	=	$gradeName;			
				?>
      <tr>
    <tr>
        
        <td class="listing-item" nowrap><input type="hidden" name="gradeId_<?=$i;?>" value="<?=$gradeCodeId?>" /><?=$displayGrade?></td>
        <td class="listing-item" nowrap><input name="quantity_<?=$i;?>" type="text" id="quantity_<?=$i;?>"  onKeyUp="return totalGrade(document.frmDailyProcessingGrade);" value="<?=$gradeQuantity;?>" size="3" /></td>
    </tr>
      
	  <? }?>
	  <input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i;?>" >
	  
	  <? }   ?>
	  <tr>
	    
	    <td align="center">&nbsp;</td>
	    <td align="center">&nbsp;</td>
	  </tr>
	  <tr>
	    
        <td align="center" class="listing-head">Total</td>
        <td align="left"><input name="totalQuantity" type="text" id="totalQuantity" size="5" value="<?=$total?>" readonly></td>
    </tr>
	
	  <tr>
	    <td colspan="2" align="center"><input type="submit" name="cmdSaveGradeChange" class="button" value=" Save Changes "></td>
    </tr>
	  
	  <? } else {	
			if( sizeof($gradeMasterRecords)){
			$i	=	0;
				
			?>
      <tr bgcolor="#f2f2f2" class="listing-head">
        <td nowrap>Grade</td>
        <td nowrap>Quantity(Nos)</td>
      </tr>
	  <?
	  
										foreach( $gradeMasterRecords as $gl )
												{
												$i++;
												$id			=	$gl[3];
												$code		=	$gl[4];
												$displayGrade	=	$code;
																											
											?>
      <tr>
        
        <td class="listing-item" nowrap><input type="hidden" name="gradeId_<?=$i;?>" value="<?=$id	?>" /><?=$displayGrade?></td>
        <td class="listing-item" nowrap><input name="quantity_<?=$i;?>" type="text" id="quantity_<?=$i;?>"  onKeyUp="return totalGrade(document.frmDailyProcessingGrade);" value="0" size="3" /></td>
      </tr>
      
	  <? }?>
	  <input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i;?>" >
	  
	  <? } ?>
	  <tr>
	     <td align="center">&nbsp;</td>
	    <td align="center">&nbsp;</td>
	  </tr>
	  <tr>
	    <td align="center" class="listing-head">Total</td>
        <td align="left"><input name="totalQuantity" type="text" id="totalQuantity" size="5" readonly></td>
    </tr>
	
	  <tr>
	    <td colspan="2" align="center"><input type="submit" name="cmdSaveChange" class="button" value=" Save " onClick="return validateProcessingGrade(document.frmDailyProcessingGrade); this.form.submit();"/></td>
    </tr>
	<? }?>
	<tr><td><input type="hidden" name="fishId" value="<?=$fishId;?>">
	<input type="hidden" name="lotId" value="<?=$lotNo;?>">
	<input type="hidden" name="packingId" value="<?=$packingId;?>">
	<input type="hidden" name="codeId" value="<?=$processcodeId;?>">
	<input name="editId" type="hidden" id="editId">
	<input type="hidden" name="editId2">
	</td></tr>
  </table>
  
  <!--Reload iframe 2-->
	<? if($editGradeChange==true || $saveChanged==true){?>
	<script language="javascript">
	updateGradeListFrame();
	//alert("Hai");
	</script>
	<? }?>
	
	<?
		$displayStatus	=	"";
		$nextPage		=	"";
		$displayStatus	=	$sessObj->getValue("displayMsg");
		$nextPage		=	$sessObj->getValue("nextPage");
		if( $displayStatus!="" ) 
		{
			$sessObj->putValue("displayMsg","");
			$sessObj->putValue("nextPage","");
	?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		alert("<?=$displayStatus;?>");
		//window.location="<?=$nextPage;?>";
		//-->
		</SCRIPT>
	<?
		}
	?>
		
</form>