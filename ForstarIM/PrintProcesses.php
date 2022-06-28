<?php
	require("include/include.php");
	#List All Process	
	$recordsFilterId	= $g["selFilter"];
	$selRateList		= $g["selRateList"];
	
	if ($recordsFilterId!=0) {			
		$processRecords = $processObj->processRecFilter($recordsFilterId, $selRateList);
	} else {		
		$processRecords	= $processObj->fetchAllRecords($selRateList);
	}
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="70%" align="center">
	<tr>
		<Td height="50" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;PRE-PROCESSING RATES MASTER</td>
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
							</tr>
							
							<?
								if($errDel!="")
								{
							?>
							<tr>
								<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
							</tr>
							<?
								}
							?>
							<tr>
								<td width="1" ></td>
								<td colspan="2" style="padding-left:10px;padding-right:10px;padding-bottom:10px;">
								<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($processRecords) > 0 )
												{
													$i	=	0;
											?>
											
											<tr  bgcolor="#f2f2f2" align="center">		
												<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Fish Name </td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Code </td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Process Sequence </td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Rate</td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Commission</td>												
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Criteria</td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">Yield <br>Average(%) </td>
												<td class="listing-head" style="padding-left:5px; padding-right:5px;">No.of Exception L.Centrs</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">No.of Exception Processors</td>
	</tr>
	<?php
	$fishName = "";
	$averageYield = "";
	foreach($processRecords as $fr)
	{
		$i++;
		$processId		=	$fr[0];
		$fishId			=	$fr[1];							
		$fishRec		=	$fishmasterObj->find($fishId);
		$fishName		=	stripSlash($fishRec[1]);
		$Processes		=	stripSlash($fr[2]);
		$Process		=	explode(",",$Processes);
		$averageYield		= $processObj->getYieldAverage($processId);			
		$processTime		=	$fr[3];
		$processRate		=	$fr[4];
		$processCommission	=	$fr[5];
		$selPPCriteria		= 	$fr[7];
		$processFlag		=	$fr[6];

		list($processRate, $processCommission, $selPPCriteria) = $processObj->getDefaultPreProcessRate($processId);
		$criteria = ($selPPCriteria==0)?"To":"From";
		/*
		$criteria	= "";
		if ($selPPCriteria==0) $criteria	= "To";
		else $criteria	= "From";
		*/
		$preProcessCode	=	$fr[10];

		$expLCenterRecords = $processObj->fetchAllExceptionCenterRecords($processId);	
		$noOfCenters	= "";
		if(sizeof($expLCenterRecords)>0) $noOfCenters = sizeof($expLCenterRecords);
		# Exception Processors
		$exceptionPreProcessors	= $processObj->fetchExptedProcessor($processId);
		$noOfExptProcessors = "";
		if(sizeof($exceptionPreProcessors)>0) $noOfExptProcessors = sizeof($exceptionPreProcessors);	
		
		?>
											<tr  bgcolor="WHITE"  >	
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$fishName;?></td>
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$preProcessCode;?></td>
	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">			
	<?php
	for($k=0; $k<sizeof($Process);$k++){								
		$displayProcess	=	stripSlash( $processcodeObj->findProcessCode($Process[$k]));
		if( sizeof($Process) > 2 && $k < sizeof($Process)-1 )	{
			$compareProcess = $Process[$k].",".$Process[$k+1];
			$checkUniqueRecords	=	$processObj->checkProcessesUniqueRecords($fishId,$compareProcess,$selRateList);
			if( sizeof($checkUniqueRecords)==0 )	{					
				$displayProcess .= "<font color=red>-></font>";
			} else $displayProcess .= "->";
		} else if ( $k < sizeof($Process)-1 ) $displayProcess .= "->";
		echo $displayProcess;
	}
	?>												
	</td>
	 <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$processRate;?></td>
									            <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$processCommission;?></td>
								                <td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=$criteria?></td>
								                <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><? echo number_format($averageYield,2,'.','');?></td>
												<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=$noOfCenters?></td>
<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><?=$noOfExptProcessors?></td>
										</tr>
											<?
												}
											?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="<?=$editId?>">
	<input type="hidden" name="editSelectionChange" value="0">	
	<?
		} else {
	?>
	<tr bgcolor="white">
		<td colspan="11"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>
	</table>
							  </td>
						  </tr>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>











			