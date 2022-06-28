<?
require("include/include.php");

$err			=	"";

$valueChanged	=	false;


$landingCenterId	=	$g["landingCenterId"];
$lastId				=	$g["newId"];
$competitorId		=	$g["competitorId"];

/*foreach($p as $val =>$key)
{
echo "<br>$val = $key";
}*/
			

# Delete competitor from the list

if( $p["cmdDelete"]!=""){

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$competitorId	=	$p["delId_".$i];
			
			if( $competitorId!="" )
			{
				$competitorListRecDel =	$competitorscatchObj->deleteCompetitorFromList($competitorId);
			}
		}

		if($competitorListRecDel)
		{
			$sessObj->createSession("displayMsg",$msg_succDelCompetitorList);
			//$sessObj->createSession("nextPage",$url_afterDelCompetitorList);
		}
		else
		{
			$errDel		=	$msg_failDelCompetitorList;
		}
	}




# Edit Competitor List
	
	if( $p["editId"]!="" && $p["editId2"]!=""){
	
		//$editMode		=	true;
		$competitorEditId				=	$p["editId"];
		$catchId						=	$p["editId2"];
				
		$editCompetitorListRec		=	$competitorscatchObj->findCompetitorId($competitorEditId,$catchId);
		
		/*foreach($editCompetitorListRec as $cr)
					{
		$gradeCodeId			=	$er[0];
		$editFishId				=	$er[2];
		}*/
		$valueChanged	=	true;
}	

if($valueChanged==true){
	
		$lastId			= $p["entryId"];	
		$competitorId	= $p["compId"];
		$competitorsListRecords	=	$competitorscatchObj->fetchAllCompetitorsCatchListRecords($lastId,$competitorId);
		}
if($p["catchId"]!="" || $p["entryId"]!=""){

		if($p["catchId"]){
			$lastEditId=$p["catchId"];
		}
		else if($p["entryId"]){
			$lastEditId=$p["entryId"];
		}
		else {
			$lastEditId=$p["catchId"];
		}
			
			$competitorsListRecords	=	$competitorscatchObj->fetchAllCompetitorsCatchListRecords($lastEditId,$competitorId);
		}
		else {
			$competitorsListRecords	=	$competitorscatchObj->fetchAllCompetitorsCatchListRecords($lastId,	$competitorId);
		}
		$CompetitorsRecSize=sizeof($competitorsListRecords);
?>

<? if($p["editId"]!=""){?>
<script language="javascript">
parent.document.frmCompetitorsCatch.editCompetitor.value=<?=$competitorEditId?>;
</script>
<? }?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript" src="libjs/competitorscatch.js"></script>
<script type="text/javascript" src="libjs/generalFunctions.js"></script>
<form name="frmCompetitorsCatchList"  id="frmCompetitorsCatchList" action="CompetitorsCatchList.php" method="post">

  <table cellpadding="1"  width="50%" cellspacing="0" border="0" align="center" bgcolor="#f2f2f2">
    					<?
									if( sizeof($competitorsListRecords)>0)
											{
												$i	=	0;
								?>
    <tr  bgcolor="#f2f2f2"  > 
      <td width="24"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></td>
      <td width="95" nowrap class="listing-head">Competitor </td>
      <td class="listing-head" nowrap >Total Qty </td>
      <td class="listing-head" nowrap > </td>
      <td ></td>
    </tr>
    <? 
									foreach($competitorsListRecords as $clr)
										{
																								
										$i++;
										$listId					=	$clr[0];
										$competitorCatchId		=	$clr[1];
										$competitorId			=	$clr[3];
										$total					=	$clr[5];
										$competitor				=	$clr[11];										
										
										
										?>
    <tr  bgcolor="WHITE"  > 
      <td><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$competitorId;?>" ></td>
      <td class="listing-item" nowrap >
        <?=$competitor;?>      </td>
      <td class="listing-item" width="48"><?=$total?></td>
      <td class="listing-item" width="48"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$competitorId;?>,'editId'); assignValue(this.form,<?=$competitorCatchId;?>,'editId2');"  ></td>
      <td width="29"></td>
    </tr>
    <?
												}
										?>
    <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
    <input type="hidden" name="editId">
	<input type="hidden" name="editId2">
    <!--input type="hidden" name="editSelectionChange" value="0"-->
    <?
											}
											else
											{
										?>
    <tr bgcolor="white"> 
      <td colspan="6"  class="err1" height="10" align="center">
        <?=$msgNoRecords;?>      </td>
    </tr>
    
    <tr bgcolor="white"> 
      <td colspan="6"  class="err1" height="10" align="center">&nbsp; </td>
    </tr>
    <?
											}
										?>
    <tr bgcolor="white"> 
      <td colspan="6"  height="10" align="center" > <input type="hidden" name="fishId" value="<?=$fishId;?>"> 
	  <input type="hidden" name="landingId" value="<?=$landingCenterId;?>">
		<input type="hidden" name="entryId" value="<?=$lastId;?>">
		<input type="hidden" name="compId" value="<?=$competitorId;?>">
		<input type="hidden" name="competitor" value="<?=$competitorEditId;?>">
		<input type="hidden" name="catchId" value="<?=$catchId;?>">
		
        
       	<? if(sizeof($competitorsListRecords)>0){ ?>
        <input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$CompetitorsRecSize;?>);" ><? }?></td>
    </tr>
  </table>
  <? if($valueChanged==true){?>
  <script language="JavaScript">
  passUrlValue();
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
		parent.document.frmCompetitorsCatch.submit();
		//window.location="<?=$nextPage;?>";
		//-->
		</SCRIPT>
	<?
		}
	?>
</form>