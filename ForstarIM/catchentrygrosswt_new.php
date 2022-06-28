<?php
require_once("include/include.php");
ob_start();

	$numEntries	=	"";
	$recDel	=	false;
	
	if ($p["entryId"] =="") $lastId	=	$g["lastId"];
	else $lastId	=	$p["entryId"];
	
	#paging variables 
	if($p["curBasketWt"]!="") {
		$basketWt =	$p["curBasketWt"];
	} else if ($decTotalWt!="" || $decTotalWt!=0 || $p["declNetWt"]!="") {
		$basketWt = $decTotalWt;
	} else $basketWt		=	$g["basketWt"];
	
	#Add new entry 
	$count = $p["hidTotalCount"];
	
	# Paging 
	$limit	= 300;
	$pageno	= 1;
	$offset = ($pageno - 1) * $limit; 

	#Gross Weight Add and Save changes -------------
	if ($p["cmdSaveChange"]!="") {
		$rowCount	= $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$grossId	= $p["grossId_".$i];
			$grossWt	= $p["grossWt_".$i];
			$basketWt	= $p["grossBasketWt_".$i];
			$entryId	= $p["entryId"];
			
			if ($grossId=="" && $entryId!="" && $grossWt!="" && $grossWt!=0) {
				$dailyGrossRecIns = $dailycatchentryObj->addGrossWt($grossWt, $basketWt, $entryId);
				$saveChanges	= $p["countSaved"];
			} else if ($grossId!="" && $entryId!="" && $grossWt!="" && $grossWt!=0) {
				$grossUpdateRec = $dailycatchentryObj->updateGrossWt($grossId, $grossWt, $basketWt, $entryId);	
				$saveChanges	= $p["countSaved"];
			}
			else if (($grossId!="" || $grossId!=0) && $entryId!="" && ($grossWt!="" && $grossWt==0)) {
							$grossdeleteRec = $dailycatchentryObj->deleteGrossEntryWt($grossId);
						}
						else if (($grossId!="" || $grossId!=0) && $entryId!="" && $grossWt=="") {
							
							$grossdeleteRec = $dailycatchentryObj->deleteGrossEntryWt($grossId);
						}
		}
		if ($grossUpdateRec) {
			//$sessObj->createSession("displayMsg",$msg_succUpdateQuality);
			//$sessObj->createSession("nextPage",$url_afterDelProcessor);
		} else $err	=	$msg_failUpdateGross;
		$grossUpdateRec	=	false;
	}

	#Delete gross List
	if ($p["cmdDelete"]!="") {
		$rowCount	= $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$grossId	= $p["delId_".$i];
			if ($grossId!="") {
				$grossRecDel = $dailycatchentryObj->deleteGrossEntryWt($grossId);	
				$recDel	= true;
			}
		}
	}	
		
	if ($g["newWt"]!="") {	
		$resetBasketWt		= $g["newWt"];
		$entryId		= $p["entryId"];
		if ($resetBasketWt!="" && $entryId!=""){
			$updateBasketWtRec = $dailycatchentryObj->updateBasketWt($resetBasketWt, $entryId);
		}
	}		
		
	if ($p["entryId"]=="") $entryId=$g["lastId"];
	else $entryId = $lastId;

	#List All Gross Wt based on paging
	$grossRecords	= $dailycatchentryObj->fetchAllPagingRecords($entryId, $offset, $limit);
	//$grossRecords	= array();
	$grossRecSize	= sizeof($grossRecords);
	if ($grossRecSize>0) {
		list($totalWt, $grandTotalBasketWt, $netGrossWt) = $dailycatchentryObj->getCountDetails($entryId);
	}
	#count all Gross Records
	//$countGrossRecords	=	$dailycatchentryObj->fetchAllGrossRecords($entryId);
	/*
	foreach ($grossRecords as $cgr) {
			$countGrossWt		= $cgr[1];
			$totalWt		= $totalWt+$countGrossWt;
			$countGrossBasketWt	= $cgr[2];
			$grandTotalBasketWt	= $grandTotalBasketWt + $countGrossBasketWt;
			$netGrossWt		= $totalWt - $grandTotalBasketWt;
	}
	*/
?>
<html>
<head>
<TITLE></TITLE>
<?php
	if ($grossRecSize>0) {
?>
<script language="JavaScript" type="text/javascript">	
	parent.document.frmDailyCatch.entryGrossNetWt.value='<?=$netGrossWt?>';
	parent.document.frmDailyCatch.entryTotalGrossWt.value='<?=$totalWt?>';
	parent.document.frmDailyCatch.entryTotalBasketWt.value='<?=$grandTotalBasketWt?>';
	parent.document.frmDailyCatch.totalGrossWt.value	='<?=$totalWt?>';
	parent.document.frmDailyCatch.totalBasketWt.value	='<?=$grandTotalBasketWt?>';	
</script>
<?php
	}
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript" src="libjs/dailycatchentry.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/generalFunctions.js"></script>
</head>
<body>
<form name="frmEntryGrossWt" action="catchentrygrosswt_new.php" method="post">
<table cellpadding="0" cellspacing="0" border="0">	
	<tr>
		<?php
		$col = 20;
		for($i=1;$i<=$col;$i++) {
		?>			
	<td width="9%">
    	<table cellpadding="0" cellspacing="0">
		<tr bgcolor="#f2f2f2" class="listing-head" align="center" style="line-height:normal; font-size:11px;">
			<td width="10%">No:</td>
			<td width="80%">GWt</td>
			<td width="5%">BWt</td>
		</tr>
		<?php 
			$row=15;
			$sos	=	($i-1)*$row+1;
			$totalGrossWt	=	"";
			$totalBasketWt	=	"";
			$netWt			=	"";
			for($j=1;$j<=$row;$j++) {
				$id	=(($i-1)*$row)+$j;
				$num	=	300;
				$hidId="";
				$gwt="";
				$bwt="";			
				if ( $id <= sizeof($grossRecords) )	{
					$rec = $grossRecords[$id-1];
					$hidId=$rec[0];
					$gwt=$rec[1];
					$bwt=$rec[2];
					if ($bwt=="") {
						$basketWt	=	$g["basketWt"];
					} else if($bwt!="") {
						$basketWt	=	$bwt;
					} else {
						$basketWt =	$p["curBasketWt"];	
					}
					$totalGrossWt	=	$totalGrossWt+$gwt;
					if($decTotalWt!="" || $decTotalWt!=0 || $p["declNetWt"]!=0 ) {
						$totalBasketWt 	=	$decTotalWt;
					} else {
						$totalBasketWt	=	$totalBasketWt + $bwt;
					}
					$netWt	=	$totalGrossWt - $totalBasketWt;
					
				}	
				if ( $id < $num) $nextControl = "grossWt_".($id+1);
				else $nextControl = "cmdSaveChange";
			?>
			<tr>
				<td nowrap align="left">
					<table cellpadding="0" cellspacing="0">
					<TR>
					<TD><input type="checkbox" name="delId_<?=$id;?>" id="delId_<?=$id;?>" class="chkBox" value="<?=$hidId;?>"></TD>
					<td class="listing-item" style="line-height:normal;" align="left"><?=$id?></td>
					</TR></table>
				</td>
				<td>
				<input type="hidden" name="grossId_<?=$id;?>" id="grossId_<?=$id;?>" value="<?=$hidId?>" readonly="true" />
				<input type="text" name="grossWt_<?=$id;?>" id="grossWt_<?=$id;?>" value="<?=$gwt?>" size="3" style="text-align:right" tabindex="<?=$id;?>" onkeypress="parent.document.frmDailyCatch.saveChangesOk.value='';return focusNext(event,'document.frmEntryGrossWt','<?=$nextControl?>','<?=$i?>','<?=$sos?>',<?=$row?>);" onchange="totalWt('<?=$i?>','<?=$sos?>',<?=$row?>);" autocomplete="off" />
				</td>
				<td>
					<input type="text" name="grossBasketWt_<?=$id;?>" id="grossBasketWt_<?=$id;?>" value="<?=$basketWt;?>"  size="3" style="text-align:right; width:30px;" maxlength="5" autocomplete="off" />
				</td>
			</tr>
		<? }?>
		<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$id?>" >
					<tr>
					<td class="fieldName1" style="text-align:left;">Tot</td>
					  <td class="listing-item"><input type="text" size="3" name="totWt_<?=$i?>" id="totWt_<?=$i?>" style="border:none; text-align:right" readonly value="<?=($totalGrossWt!=0)?number_format($totalGrossWt,2,'.',''):"";?>" /></td>
					  <td class="listing-item" style="padding-right:1px;"><input name="basketWt_<?=$i?>" id="basketWt_<?=$i?>" type="text" style="border:none; text-align:right" size="3" readonly value="<?=($totalBasketWt!=0)?number_format($totalBasketWt,2,'.',''):"";?>" />
					 </td>
					</tr>
				<tr><td colspan="3" class="fieldName1" style="text-align:left;">Net:<span class="listing-item"><input type="text" size="3" name="netWt_<?=$i?>" id="netWt_<?=$i?>" style="border:none; text-align:right" readonly value="<?=($netWt!=0)?number_format($netWt,2,'.',''):"";?>" />&nbsp;Kg</span></td></tr>
			  </table>
			</td>
			<td width="1" background="images/VL.png" style="background-repeat: repeat-y; line-height: normal;">&nbsp;</td>
			<!--<td width="1">&nbsp;</td>-->
			<!--<td width="1" bgcolor="#CCCCCC">&nbsp;</td>-->
			<input type="hidden" name="hidColumnCount" id="hidColumnCount" value="<?=$i?>" >
		<? }?>				
	</tr>
<tr>
<td  colspan="14" align="center">
	<input type="hidden" name="entryId" id="entryId" value="<?=$lastId?>" />
	<input type="hidden" name="dailyBasketWt" id="dailyBasketWt" size="3" />
	<input type="hidden" name="curBasketWt" id="curBasketWt" size="3" value="<?=$basketWt?>" />
	<input type="hidden" name="entryTotalGrossWt" id="entryTotalGrossWt" value="<?=$totalWt;?>">
	<input type="hidden" name="entryTotalBasketWt" id="entryTotalBasketWt" value="<?=$grandTotalBasketWt?>">
	<input type="hidden" name="entryGrossNetWt" id="entryGrossNetWt" value="<?=$netGrossWt?>" > 
	<input type="hidden" name="countSaved" id="countSaved" value="" />
	<input type="hidden" name="isSaved" id="isSaved" value="<?=$saveChanges?>" />
	<input type="hidden" name="declNetWt" id="declNetWt" value="<?=$decTotalWt?>" />
	<table>
		<tr>
		  <td></td>
		  <td>
		<table><tr>
	  <td nowrap>&nbsp;
	    <input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$grossRecSize;?>);">&nbsp;<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="assignValue(this.form,'1','countSaved'); return validateGrossEntry(document.frmEntryGrossWt); ">
		</td></tr>
	</table>
	</td></tr></table>
		</td></tr>
</table>
<? if($saveChanges!="" || $recDel==true){?>
<script language="javascript" type="text/javascript">
	parent.document.frmDailyCatch.saveChangesOk.value='<?=$saveChanges?>';
	//for find actual total
	findActualWt(parent.document.frmDailyCatch);
</script>
<? }?>
<script type="text/javascript" language="javascript">
 	var curr_basketWt	=	 parent.document.frmDailyCatch.dailyBasketWt.value;	
	document.frmEntryGrossWt.dailyBasketWt.value	=	curr_basketWt;
	function validateGrossEntry(form)
	{
		var fishName	=	parent.document.frmDailyCatch.fish.value;
		var fishCode 	= 	parent.document.frmDailyCatch.processCode.value;	
		if (fishName=="") {
			alert("Please select the fish");
			parent.document.frmDailyCatch.fish.focus();
			return false;
		}
		if (fishCode=="") {
			alert("Please select the Process Code");
			parent.document.frmDailyCatch.processCode.focus();
			return false;
		}
		return true;
	}
</script> 
</form>
</body>
</html>
<?php
$outputContents = ob_get_contents(); 
ob_end_clean();
echo $outputContents;
/*
$arr = explode("\r\n",$outputContents);
$output = $arr[0]; // implode($arr," ");
//$output=str_replace("\n","\\\n",$outputContents);
echo $output;
die();
//$output = htmlspecialchars($output);
$patterns[0] = '/"/';
$patterns[1] = '/\'/';
$patterns[2] = '/\n/';
$replacements[0] = '\"';
$replacements[1] = '\\\'';
$replacements[2] = '';
//$output = preg_replace($patterns, $replacements, $output);
*/
?>
<!--<script language="JavaScript" type="text/javascript">
	if ( window.frames['catchentrygrosswt'] )	{
			window.frames['catchentrygrosswt'].innerHTML="<?//=$output?>";		
		}
		else	{
			//alert(document.getElementById("catchentrygrosswt").contentWindow.document);
		document.getElementById("catchentrygrosswt").contentDocument.innerHTML="<?//=$output?>";
		}
</script>-->