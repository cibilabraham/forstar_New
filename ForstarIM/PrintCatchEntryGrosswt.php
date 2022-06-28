<?
require("include/include.php");

	$numEntries	=	"";
		
	if($p["entryId"] ==""){
		$lastId	=	$g["lastId"];
	}
	else {
		$lastId	=	$p["entryId"];
	}

	if($p["next"]!="" )	{
		$pageno=$p["pageno1"];
	}
	else if($p["previous"]!="" )	{
		$pageno=$p["pageno2"];
	}
	else if ( $p["pageno"] != "" )	{
		$pageno=$p["pageno"];
	}
	
#paging variables 

if(empty($pageno)){
	$pageno=1;
	}
	
if($p["curBasketWt"]!=""){
	$basketWt =	$p["curBasketWt"];
	}
	else {
	$basketWt		=	$g["basketWt"];
	}


#Add new entry 
	$count = $p["hidTotalCount"];
	
	if($p["cmdShowAll"]!=""){
	
		$entryId				=	$p["entryId"];
				
		$countGrossRecords	=	$dailycatchentryObj->fetchAllGrossRecords($entryId);
		$numEntries		=	sizeof($countGrossRecords)+5;
		$limit=$numEntries;	
		$offset = 0;
		$pageno	=	1;
		}
		else {
		$numEntries=15;
		$limit=$numEntries;
		$offset = ($pageno - 1) * $limit; 
	}
	
	/*if($p["nextEntry"]!=""){
		$limit=0;
	}
	else {*/

//$limit=$numEntries;
	
//$offset = ($pageno - 1) * $limit; 

	

	
#Gross Weight Add and Save changes   -------------
	
if( $p["cmdSaveChange"]!=""){

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$grossId		=	$p["grossId_".$i];
			$grossWt		=	$p["grossWt_".$i];
			$basketWt		=	$p["grossBasketWt_".$i];
			$entryId		=	$p["entryId"];
			
			if($grossId	=="" && $entryId!="" && $grossWt!="")
			{
				$dailyGrossRecIns=$dailycatchentryObj->addGrossWt($grossWt,$basketWt,$entryId);
				$saveChanges	=	$p["countSaved"];
			}
			else if($grossId!="" && $entryId!="" && $grossWt!="") 
			{
				$grossUpdateRec		=	$dailycatchentryObj->updateGrossWt($grossId,$grossWt,$basketWt,$entryId);	
				$saveChanges	=	$p["countSaved"];
			}
			

		}
		if($grossUpdateRec)
		{
			//$sessObj->createSession("displayMsg",$msg_succUpdateQuality);
			//$sessObj->createSession("nextPage",$url_afterDelProcessor);
		}
		else
		{
			$err	=	$msg_failUpdateGross;
		}
		$grossUpdateRec	=	false;
	}


#Delete gross List

if( $p["cmdDelete"]!=""){

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$grossId	=	$p["delId_".$i];

			if( $grossId!="" )
			{
				// Need to check the selected Processor is link with any other process 
				$grossRecDel		=	$dailycatchentryObj->deleteGrossEntryWt($grossId);	
			}
		}
}	
		
if( $g["newWt"]!="" ){	
	
	
	$resetBasketWt			=	$g["newWt"];
	$entryId				=	$p["entryId"];
	if($resetBasketWt!="" && $entryId!=""){
		$updateBasketWtRec	=	$dailycatchentryObj->updateBasketWt($resetBasketWt,$entryId);
	}
	
}		
		
		
if($p["entryId"]==""){
	$entryId=$g["lastId"];
}


#List All Gross Wt based on paging
$grossRecords	=	$dailycatchentryObj->fetchAllPagingRecords($entryId,$offset,$limit);
$grossRecSize	=	sizeof($grossRecords);

#count all Gross Records
$countGrossRecords	=	$dailycatchentryObj->fetchAllGrossRecords($entryId);
foreach ($countGrossRecords as $cgr){
			$countGrossWt			=	$cgr[1];
			$totalWt				=	$totalWt+$countGrossWt;
			$countGrossBasketWt		=	$cgr[2];
			$grandTotalBasketWt		=	$grandTotalBasketWt + $countGrossBasketWt;
			$netGrossWt				=	$totalWt - $grandTotalBasketWt;
}
$numrows	=	sizeof($countGrossRecords);
$maxpage	=	ceil($numrows/$limit);
?>

<!--script language="javascript">
	parent.document.frmDailyCatch.entryActualWt.value='<?=$netGrossWt?>';
	parent.document.frmDailyCatch.entryGrossNetWt.value='<?=$netGrossWt?>';
	parent.document.frmDailyCatch.entryTotalGrossWt.value='<?=$totalWt?>';
	parent.document.frmDailyCatch.entryTotalBasketWt.value='<?=$grandTotalBasketWt?>';
</script-->


<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript" src="libjs/dailycatchentry.js"></script>
<script type="text/javascript" src="libjs/generalFunctions.js"></script>

<form name="frmPrintEntryGrossWt" action="PrintCatchEntryGrosswt.php" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0" height="100%">
                                              
                                              
                                              <tr>
                                                <td colspan="2" class="fieldName" valign="top">
						 <table width="100%" border="0" cellpadding="0" cellspacing="0">
  <!--tr>
    <td class="fieldName">Gross Wt:</td>
    <td><input name="grossWeight" type="text" size="4" style=" text-align:right"/></td>
    <td class="listing-item">
	</td>
	<td>
	<input type="submit" name="cmdAddDaily" value="Add"  class="button" onclick=" return validateGrossEntry(document.frmPrintEntryGrossWt);">
	</td>
  </tr-->
  <tr>
    <td colspan="2" class="fieldName"><input type="hidden" name="entryId" value="<?=$lastId?>" /><input name="dailyBasketWt" type="hidden" size="3" /><input name="curBasketWt" type="hidden" size="3" value="<?=$basketWt?>" />
	<table width="178" height="100%" border="0" cellpadding="0" cellspacing="0">
	<?	
										//if( sizeof($grossRecords) > 0 )
											//	{
																						
											//		$i	=	0;
											?>
      <tr bgcolor="#f2f2f2" class="listing-head">
        <td width="12%">No</td>
        <td width="35%" nowrap>&nbsp;Gross Wt </td>
        <td width="42%" nowrap>Basket Wt </td>
      </tr>
	  <?

										/*foreach($grossRecords as $gr)
											{
											$i++;
											$grossId		=	$gr[0];
											$grossWt		=	$gr[1];
											$totalGrossWt	=	$totalGrossWt+$grossWt;
											$grossBasketWt	=	$gr[2];
											$totalBasketWt	=	$totalBasketWt + $grossBasketWt;
											$netWt	=	$totalGrossWt - $totalBasketWt*/
										
											?>
		<? 
			$count=0;			
			for($i=1;$i<=$numEntries;$i++)
			{
				$hidId="";
				$gwt="";
				$bwt="";
			
				if ( $i <= sizeof($grossRecords) )	{
					$rec = $grossRecords[$i-1];
					$hidId=$rec[0];
					$gwt=$rec[1];
					$bwt=$rec[2];
					if($bwt==""){
						$basketWt		=	$g["basketWt"];
					}
					else if($bwt!="") {
					$basketWt			=	$bwt;
					}
					else {
					$basketWt =	$p["curBasketWt"];	
					}
					
					$totalGrossWt	=	$totalGrossWt+$gwt;
					$totalBasketWt	=	$totalBasketWt + $bwt;
					$netWt	=	$totalGrossWt - $totalBasketWt;
					$count++;
				}			
				
				if ( $i < $numEntries ) $nextControl = "grossWt_".($i+1);
				else $nextControl = "cmdSaveChange";
				
//				echo "nextControl=$nextControl<br>";
		?>
      <tr>
        <td class="listing-item" nowrap align="right"><?=(($pageno-1)*15)+$i?>&nbsp;&nbsp;</td>
        <td class="listing-item" align="right" style="padding-right:30px;"><?=$gwt?>&nbsp;</td>
        <td class="listing-item" align="right" style="padding-right:40px;"><?=$basketWt; ?></td>
      </tr>
      <? 
	  	if ( ( ($i % 15) == 0 ) || $i==$numEntries )	{
		?>
		 <tr>
  	<td colspan="4">
		<table>
		<tr>
    		<td ><span class="fieldName">Gross:</span>&nbsp;<span class="listing-item"><?=$totalGrossWt?>&nbsp;Kg</span></td>
	   		<td ><span class="fieldName">Basket:</span>&nbsp;<span class="listing-item"><?=$totalBasketWt?>&nbsp;Kg</span></td>
    		<td ><span class="fieldName">Net:</span>&nbsp;<span class="listing-item"><?=$netWt?>&nbsp;Kg</span></td>
		</tr>
		</table>	</td>
  </tr>
		<?
			$totalGrossWt=0;
			$totalBasketWt=0;
			$netWt=0;
		}
	  }  
	  
	  ?>
	  
	  <!--tr>
        <td class="listing-item"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$grossId;?>" ></td>
        <td class="listing-item"><?=$i?></td>
        <td class="listing-item"><input type="hidden" name="grossId_<?=$i;?>" value="<?=$grossId?>" /><input type="text" name="grossWt_<?=$i;?>" value="<?=$grossWt?>" size="3" style="text-align:right" /></td>
        <td class="listing-item"><input type="text" name="grossBasketWt_<?=$i;?>" value="<?=$grossBasketWt?>" size="3" style="text-align:right" /></td>
      </tr-->
	  <? //}?>
	  <input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>" >
	  <? // }?>
	  <tr>
	    <td colspan="4" align="center"><? if($count==15){  ?>	
	<!-- input type='submit' class=button name='nextEntry' value='Next 15' / -->
	<input type="hidden" name="hidTotalCount" id="hidTotalCount" value="<?=$count?>" >
	<input name="curBasketWt" type="hidden" size="3" value="<?=$basketWt?>" />
	<? }?>	  </td>
	   </tr>  
	  <tr>
	    <td colspan="2" align="left" class="listing-item">
		<? $nav  = '';
		$page			=	"";
		if ($pageno > 1)	{
			$page  = $pageno - 1;
		?>
   			<input type='hidden' value='<?=$page?>' name='pageno2'> <input type='submit' name='previous' class='button' value='Previous 15'>
		<?
		}
		else	{
		?>&nbsp;<?   			
   		}
		?>		</td>
		<td colspan="2" align="right" class="listing-item">
		<?		
		if ( $pageno < $maxpage || $count == 15 && $count==$numEntries || $p["cmdShowAll"]!="")	{
   			$page = $pageno + 1;
		?>
   		<input type='hidden' value='<?=$page?>' name='pageno1'> <input type='submit' class=button name='next' value=' Next 15'>
		<?
		}
		else	{
		?>&nbsp;<?
   		}
	  ?>
	  <input type="hidden" name="pageno" value="<?=$pageno?>"  />
	  <input type="hidden" name="showAllValue" value="0" />	  </td>
	  </tr>
    </table></td>
	<td width="55%" valign="top">
	<table>
		<tr>
			<td>			</td>
		</tr>
	</table>
	</td>
    </tr>
</table> <input type="hidden" name="entryTotalGrossWt" value="<?=$totalWt;?>">
					<input type="hidden" name="entryTotalBasketWt" value="<?=$grandTotalBasketWt?>">
					<input type="hidden" name="entryGrossNetWt" value="<?=$netGrossWt?>"> 
					<input type="hidden" name="countSaved" value="" />
					<input type="hidden" name="isSaved" value="<?=$saveChanges?>" />
 </td>
  </tr>
 </table>
  </form>
