<?
require("include/include.php");

$err			=	"";
$valueChanged	=	false;

$mainId			=	$g["mainId"];

/*$fishId		=	$g["fishId"];
$lotNo		=	$g["lotId"];
$packingId	=	$g["packing"];
$processcodeId	=	$g["process"];*/

/*foreach($p as $val =>$key)
{
echo "<br>$val = $key";
}*/

/*			

# Edit Daily Processing Grade List
	
	if( $p["editId"]!="" && $p["editId2"]!=""){
	
		//$editMode		=	true;
		$codeEditId				=	$p["editId"];
		$lotEditId				=	$p["editId2"];
		
		$editGradeListRec		=	$dailyprocessingObj->findCodeId($codeEditId,$lotEditId);
		
		foreach($editGradeListRec as $er)
					{
		$gradeCodeId			=	$er[0];
		$editFishId				=	$er[2];
		$process				=	$er[4];
		$gradeName				=	$er[13];
		$gradeQuantity			=	$er[6];			
		}
		$valueChanged	=	true;
		if($p["editSelectionChange"]=='1'||$p["selFish"]==""){
			$fishId				=	$dailyProcessingRec[8];
		}
		else {
				$fishId			=	$p["selFish"];
		}

	}	


if( $p["cmdDelete"]!=""){

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$processingCodeId	=	$p["delId_".$i];
			$lotNo				=	$p["lotId"];

			if( $processingCodeId!="" && $lotNo!="" )
			{
				$dailyProcessingGradeRecDel =	$dailyprocessingObj->deleteDailyProcessingGrade($processingCodeId,$lotNo);
			}
		}

		if($dailyProcessingGradeRecDel)
		{
			$sessObj->createSession("displayMsg",$msg_succDelDailyProcessingGrade);
			//$deleted		=	true;
			//$sessObj->createSession("nextPage",$url_afterDelDailyProcessingGrade);
		}
		else
		{
			$errDel		=	$msg_failDelDailyProcessingGrade;
		}
	}


if($valueChanged==true){
			$fishId				=	$p["fishId"];
			$lotNo				=	$p["lotId"];
			$packingId			=	$p["packingId"];			
			$processcodeId		=	$p["codeId"];
}

	
#List all Processing Grade List
 if($valueChanged==true){
		$frozenPackingGradeRecords	=	$dailyprocessingObj->fetchAllProcessingGradeRecords($fish_Id,$lotNo,$packing_Id,$code_Id);
	} 
if ($p["lotEditId"]!="" || $p["lotId"]!=""){
	
	if($p["lotEditId"]){
		$lastEditLotId	= $p["lotEditId"];
	}
	else if($p["lotId"]){
		$lastEditLotId	= $p["lotId"];
	}
	else {
		$lastEditLotId	= $p["lotEditId"];
	}
	
	$frozenPackingGradeRecords	=	$dailyprocessingObj->fetchAllProcessingGradeRecords($fish_Id,$lastEditLotId,$packing_Id,$code_Id);
	} 
else {
$frozenPackingGradeRecords	=	$dailyprocessingObj->fetchAllProcessingGradeRecords($fish_Id,$lotNo,$packing_Id,$code_Id);
}

*/
$frozenPackingGradeRecords	=	$dailyfrozenpackingObj->fetchAllFrozenPackingGradeRecords($mainId);
$dailyFrozenPackingGradeListRecSize=sizeof($frozenPackingGradeRecords);

?>

<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript" src="libjs/dailyprocessing.js"></script>
<script type="text/javascript" src="libjs/generalFunctions.js"></script>
<form name="frmDailyFrozenPackingGradeList"  id="frmDailyFrozenPackingGradeList" action="DailyFrozenPackingGradeList.php" method="post">

  <input type="hidden" name="codeId" value="<?=$processcodeId;?>" />
  <table cellpadding="1"  width="37%" cellspacing="1" border="0" align="center" bgcolor="#999999">
    					<?
									if( sizeof($frozenPackingGradeRecords)>0)
											{
												$i	=	0;
								?>
    <tr  bgcolor="#f2f2f2" align="center"> 
      <td width="24" align="center"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></td>
      <td nowrap class="listing-head">Fish </td>
      <td nowrap class="listing-head">Process </td>
      <td nowrap class="listing-head">Grade</td>
      <td nowrap class="listing-head">No.MC</td>
      <td nowrap class="listing-head">No.L.Slab</td>
      <td class="listing-head" nowrap > </td>
    </tr>
    <? 
		foreach($frozenPackingGradeRecords as $fgr)
			{
				$i++;
				$frozenPackingGradeId	=	$fgr[0];
				$numMC					=	$fgr[4];
				$numLooseSlab			=	$fgr[5];
				$fish					=	$fgr[7];
				$processCode			=	$fgr[8];
				$grade					=	$fgr[9];
	?>
    <tr  bgcolor="WHITE"  > 
      <td align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$frozenPackingGradeId;?>"></td>
      <td class="listing-item" nowrap><?=$fish;?></td>
      <td class="listing-item" nowrap><?=$processCode;?></td>
      <td class="listing-item"><?=$grade?></td>
      <td class="listing-item"><?=$numMC?></td>
      <td class="listing-item"><?=$numLooseSlab?></td>
      <td class="listing-item" width="48" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$frozenPackingGradeId;?>,'editId'); this.form.action='DailyFrozenPackingGradeList.php';"  ></td>
    </tr>
    	<?
			}
		?>
    <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
    <input type="hidden" name="editId">
    <!--input type="hidden" name="editId2">
    <input type="hidden" name="editSelectionChange" value="0"-->
    	<?	} else	{?>
    <tr bgcolor="white"> 
      <td colspan="8"  class="err1" height="10" align="center"><?=$msgNoGradeRecords;?></td>
    </tr>
        <?	}?>
    <tr bgcolor="white"> 
      <td colspan="8"  height="10" align="center" > 
	  	<!--input type="hidden" name="fishId" value="<?=$fishId;?>"> 
        <input type="hidden" name="lotId" value="<?=$lotNo;?>"> 
		<input type="hidden" name="packingId" value="<?=$packingId;?>">
        <input type="hidden" name="codeEditId" id="codeEditId" value="<?=$codeEditId;?>"> 
        <input type="hidden" name="lotEditId" id="lotEditId" value="<?=$lotEditId;?>"> 
        <input type="hidden" name="editFishId" value="<?=$editFishId;?>"> 
		<input type="hidden" name="editProcessId" value="<?=$process;?>"-->
		
		<? if(sizeof($frozenPackingGradeRecords)>0){ ?>
        <input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$dailyFrozenPackingGradeListRecSize;?>);" ><? }?></td>
    </tr>
  </table>
  <? if($valueChanged==true){?>
  <script language="JavaScript">
  passValue();
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
		parent.document.frmDailyProcessing.submit();
		//window.location="<?=$nextPage;?>";
		//-->
		</SCRIPT>
	<?
		}
	?>
</form>