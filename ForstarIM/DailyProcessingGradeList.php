<?
require("include/include.php");

$err			=	"";
//$codeEditId		=	"";
//$lotEditId		=	"";
$valueChanged	=	false;
//$deleted		=	false;


$fishId		=	$g["fishId"];
$lotNo		=	$g["lotId"];
$packingId	=	$g["packing"];
$processcodeId	=	$g["process"];

//$currentLotNo				=	$p["lotId"];

/*foreach($p as $val =>$key)
{
echo "<br>$val = $key";
}*/

			

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
		$processingGradeRecords	=	$dailyprocessingObj->fetchAllProcessingGradeRecords($fish_Id,$lotNo,$packing_Id,$code_Id);
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
	
	$processingGradeRecords	=	$dailyprocessingObj->fetchAllProcessingGradeRecords($fish_Id,$lastEditLotId,$packing_Id,$code_Id);
	} 
else {
$processingGradeRecords	=	$dailyprocessingObj->fetchAllProcessingGradeRecords($fish_Id,$lotNo,$packing_Id,$code_Id);
}

$dailyProcessingGradeListRecSize=sizeof($processingGradeRecords);


?>





<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript" src="libjs/dailyprocessing.js"></script>
<script type="text/javascript" src="libjs/generalFunctions.js"></script>
<form name="frmDailyProcessingGradeList"  id="frmDailyProcessingGradeList" action="DailyProcessingGradeList.php" method="post">

  <input type="hidden" name="codeId" value="<?=$processcodeId;?>" />
  <table cellpadding="1"  width="37%" cellspacing="0" border="0" align="center" bgcolor="#f2f2f2">
    					<?
									if( sizeof($processingGradeRecords)>0)
											{
												$i	=	0;
								?>
    <tr  bgcolor="#f2f2f2"  > 
      <td width="24"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></td>
      <td width="95" nowrap class="listing-head">Fish </td>
      <td width="55" nowrap class="listing-head">Process </td>
      <td width="53" nowrap class="listing-head">Total </td>
      <td class="listing-head" nowrap > </td>
      <td ></td>
    </tr>
    <? 
									foreach($processingGradeRecords as $gr)
										{
																								
										$i++;
										$processingGradeId		=	$gr[0];
										$GradesLotId			=	$gr[1];
										$processcodeId			=	$gr[4];
										$fish					=	$gr[9];
										$processCode			=	$gr[12];
										//$quality				=	$gr[13];
										//$displayCode			= $processCode."&nbsp;"."(".$quality.")";
										$gradesProcessCode		=	$gr[4];
										$total					=	$gr[7];													
										?>
    <tr  bgcolor="WHITE"  > 
      <td><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$processcodeId;?>" ></td>
      <td class="listing-item" nowrap >
        <?=$fish;?>
      </td>
      <td class="listing-item" nowrap >
        <?=$processCode;?>
      </td>
      <td class="listing-item" >
        <?=$total?>
      </td>
      <td class="listing-item" width="48"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$processcodeId;?>,'editId'); assignValue(this.form,<?=$GradesLotId;?>,'editId2'); this.form.action='DailyProcessingGradeList.php';"  ></td>
      <td width="29"></td>
    </tr>
    <?
												}
										?>
    <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
    <input type="hidden" name="editId">
    <input type="hidden" name="editId2">
    <input type="hidden" name="editSelectionChange" value="0">
    <?
											}
											else
											{
										?>
    <tr bgcolor="white"> 
      <td colspan="7"  class="err1" height="10" align="center">
        <?=$msgNoGradeRecords;?>
      </td>
    </tr>
    <tr bgcolor="white">
      <td colspan="7"  class="err1" height="10" align="center">&nbsp;</td>
    </tr>
    <tr bgcolor="white"> 
      <td colspan="7"  class="err1" height="10" align="center">&nbsp; </td>
    </tr>
    <?
											}
										?>
    <tr bgcolor="white"> 
      <td colspan="7"  height="10" align="center" > <input type="hidden" name="fishId" value="<?=$fishId;?>"> 
        <input type="hidden" name="lotId" value="<?=$lotNo;?>"> <input type="hidden" name="packingId" value="<?=$packingId;?>">
        <input type="hidden" name="codeEditId" id="codeEditId" value="<?=$codeEditId;?>"> 
        <input type="hidden" name="lotEditId" id="lotEditId" value="<?=$lotEditId;?>"> 
        <input type="hidden" name="editFishId" value="<?=$editFishId;?>"> <input type="hidden" name="editProcessId" value="<?=$process;?>">
		
		<? if(sizeof($processingGradeRecords)>0){ ?>
        <input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$dailyProcessingGradeListRecSize;?>);" ><? }?></td>
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