<?
	require("include/include.php");
	
	$processor	=	$g["selProcessor"]; 
	$fromDate	=	$g["fromDate"];
	$tillDate	=	$g["tillDate"];
	$offset		=	$g["offset"];
	$limit		=	$g["limit"];
	$selRMLotId =   $g["selRMLotId"];
	$frnPkgRecs	=	$frznPkgAccountsObj->getDFPRecQryNew($fromDate, $tillDate, $processor,$offset, $limit,$selRMLotId);
	$processorRec		=	$preprocessorObj->find($processor);
	$processorName		=	$processorRec[2];
	$pageNo		= 1;
	# Default Yield Tolerance
	$defaultYieldTolerance  = $displayrecordObj->getDefaultYieldTolerance();	
?>
<html>
<head>
<title>Frozen Packing Accounts</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#f2f2f2">
	<tr bgcolor="#FFFFFF">
		<td colspan="12" align="center" class="pageName">Frozen Packing Accounts</td>
	</tr>
	<?
	if (sizeof($frnPkgRecs)) 
	{
	$i	=	0;
	?>
    <tr bgcolor="#FFFFFF">
        <td colspan="12" align="center" class="fieldName">Of M/s <?=$processorName?></td>
    </tr>
	<tr bgcolor="#FFFFFF">
		<td colspan="12" align="center">&nbsp;</td>
	</tr>
	<tr bgcolor="#FFFFFF">
        <th colspan="12" align="center">
			<table width="100%" class="print">				
				<tr bgcolor="#f2f2f2" align="center">
					<th nowrap style="padding-left:10px; padding-right:10px;">Rm Lot Id</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Fish</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Processcode</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Freezing <br>Stage</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Quality</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Frozen Code</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">MC<br> Pkg</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Frozen Qty</th>	
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Pkg<br> Wt</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Glaze<br> (%)</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Net Wt</th><!--title="Wt without glaze"-->
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Total Units</th>	
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Qty</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Grade</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Rate</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Total Amt</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" class="listing-head">Setld<br> Date</th>
				</tr>
               <? $i=0;
				//echo sizeof($frnPkgRecs);
				$totalFrznQty=0; $totalSlab=0; $totPkdQty=0; $totalPkAmt=0;
				foreach($frnPkgRecs as $fpR)
				{	 
					$i++; 
					if(!$fpR[0])
					{	
						$selDate=$fpR[23];
						$processcodeid=$fpR[9];
						$freezingstageid=$fpR[10];
						$qualityid=$fpR[11];
						$frozencodeid=$fpR[12];
						$defaultRate=$frznPkgAccountsObj->getRate($selDate,$selProcessorId,$processcodeid,$freezingstageid,$qualityid,$frozencodeid);
					}
					$settled=$fpR[28];
					$pkdqty=$fpR[14];
					$fishname=$fpR[3];
					$processcode=$fpR[5];
					$freezingstage=$fpR[6];
					$qualityname=$fpR[7];
					$frozencode=$fpR[8];
					$mcpkgcode=$fpR[31];
					$frozenqty=$fpR[15];
					$declwt=$fpR[18];
					$glaze=$fpR[17];
					$filledwt=$fpR[19];
					$slab=$fpR[16];
					$c=$fpR[14];
					$grade=$fpR[21];
					$gentryid=$fpR[24];
					$numpack=$fpR[25];
					$gnummc=$fpR[26];
					$gnumls=$fpR[27];
					$setlddate=$fpR[29];
					$pkgRate=$fpR[22];
					$pkAmt=$fpR[30];
					$totalFrznQty 	+= $frozenqty; 
					$totalSlab	+= $slab;
					$totalPkdQty	+= $pkdqty;
					$totalPkAmt+=$pkAmt;
					$totFrznQty = number_format($totalFrznQty,2,'.',',');
					$totSlab = number_format($totalSlab,0,'',',');
					$totPkdQty = number_format($totalPkdQty,2,'.',',');
					$totPkAmt = number_format($totalPkAmt,2,'.',',');
					($fpR[32]!="0" && $fpR[32]!="")?$rmlotid=$fpR[32]:$rmlotid="";
					($fpR[33]!="0" && $fpR[33]!="")?$rmlotNm=$fpR[33]:$rmlotNm="";
					?>
					<tr>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rmlotNm?>
						</td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
							<?=$fishname?>
						</td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$processcode?></td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$freezingstage?></td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$qualityname?></td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$frozencode?></td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$mcpkgcode?></td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$frozenqty?></td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$declwt?></td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$glaze?></td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right">
							<?=$filledwt?>
						</td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$slab?></td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right">
						<?=$pkdqty?>
						</td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$grade?></td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$pkgRate?>
						</td>
						<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$pkAmt?>
						</td>	
						<td class="listing-item" nowrap align="center"><?=$setlddate?></td>
					</tr>
					<?
					}
					?>
					<tr bgcolor="White">
						<TD colspan="7" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">
							Total:
						</TD>
						<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
							<strong><?=$totFrznQty?></strong>
						</td>
						<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
							<strong>			
							</strong>
						</td>
						<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
							<strong>			
							</strong>
						</td>
						<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
							<strong>			
							</strong>
						</td>
						<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
							<strong><?=$totSlab?></strong>
						</td>
						<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
							<strong><?=$totPkdQty?></strong>
						</td>
						<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
							<strong>			
							</strong>
						</td>
						<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
							<strong>			
							</strong>
						</td>
						<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
							<strong>
							<?=$totPkAmt?>
							</strong>
						</td>
						<td colspan="2">&nbsp;</td>
					</tr>
				</table>
			</td>
          </tr>
		
		<?
		}
		?>
 <SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>
</body>
</html>