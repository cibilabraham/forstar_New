<?
require("include/include.php");

	$numEntries	=	"";
	
	
	if($p["entryId"] ==""){
		$lastId	=	$g["lastId"];
	}
	else {
		$lastId	=	$p["entryId"];
	}

		
#paging variables 

if($p["curBasketWt"]!=""){
	$basketWt =	$p["curBasketWt"];
	}
	else {
	$basketWt		=	$g["basketWt"];
	}


#Add new entry 
	$count = $p["hidTotalCount"];
	

# Paging 

$limit=180;
$pageno	=	1;
$offset = ($pageno - 1) * $limit; 


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

<form name="frmPrintCatchEntryGrossWt_New.php" action="PrintCatchEntryGrossWt_New.php" method="post">
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<? 
		$col=12;
		for($i=1;$i<=$col;$i++)
			{
		?>
			
	  <td width="9%">
    <table cellpadding="0" cellspacing="0">
					<tr bgcolor="#f2f2f2" class="listing-head">
						<td width="10%">No:</td>
						<td width="80%">GWt</td>
						<td width="10%">BWt</td>
					</tr>
					<? 
					$row=15;
					$sos	=	($i-1)*$row+1;
					$totalGrossWt	=	"";
						$totalBasketWt	=	"";
						$netWt			=	"";
					for($j=1;$j<=$row;$j++)
						{
						$id	=(($i-1)*$row)+$j;
						
						$num	=	180;
						
						$hidId="";
						$gwt="";
						$bwt="";
						
			
				if ( $id <= sizeof($grossRecords) )	{
					$rec = $grossRecords[$id-1];
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
					
				}	
						if ( $id < $num) $nextControl = "grossWt_".($id+1);
						else $nextControl = "cmdSaveChange";
						
					?>
					<tr>
						<td nowrap class="listing-item"><?=$id?></td>
					  <td class="listing-item"><?=$gwt?></td>
						<td class="listing-item"><?=$basketWt;?></td>
						
					</tr>
					
					<? }?>
					<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$id?>" >
					<tr>
					<td class="fieldName">Tot</td>
					  <td><span class="listing-item"><input type="text" size="3" name="totWt_<?=$i?>" id="totWt_<?=$i?>" style="border:none; text-align:right" readonly value="<?=$totalGrossWt?>" /></span></td>
					  <td><span class="listing-item"><input name="basketWt_<?=$i?>" id="basketWt_<?=$i?>" type="text" style="border:none; text-align:right" size="3" readonly value="<?=$totalBasketWt?>" />
					  </span></td>
					</tr>
					<tr><td colspan="3" class="fieldName">Net:<span class="listing-item"><input type="text" size="3" name="netWt_<?=$i?>" id="netWt_<?=$i?>" style="border:none; text-align:right" readonly value="<?=$netWt?>" />&nbsp;Kg</span></td></tr>
			  </table>
			</td>
			<td width="1">&nbsp;</td>
			<td width="1" bgcolor="#CCCCCC">&nbsp;</td>
			<input type="hidden" name="hidColumnCount" id="hidColumnCount" value="<?=$i?>" >
				<? }?>	
			
	</tr>
<tr><td  colspan="14" align="center"><input type="hidden" name="entryId" value="<?=$lastId?>" /><input name="dailyBasketWt" type="hidden" size="3" /><input name="curBasketWt" type="hidden" size="3" value="<?=$basketWt?>" /><input type="hidden" name="entryTotalGrossWt" value="<?=$totalWt;?>">
					<input type="hidden" name="entryTotalBasketWt" value="<?=$grandTotalBasketWt?>">
					<input type="hidden" name="entryGrossNetWt" value="<?=$netGrossWt?>" > 
					<input type="hidden" name="countSaved" value="" />
					<input type="hidden" name="isSaved" value="<?=$saveChanges?>" /></td>
</tr>
</table> 
</form>
