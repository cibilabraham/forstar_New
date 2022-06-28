<?php
	require("include/include.php");
# select record between selected date

	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];
	$companyNm = $g["company"];
	$unitNm = $g["unit"];
	$processStage = $g["processingStage"];
	

	if ($dateFrom!="" && $dateTill!="") {	
		$fromDate = mysqlDateFormat($dateFrom);	
		$tillDate = mysqlDateFormat($dateTill);
		$manageRmLotIdRecords =$objManageRMLOTID->fetchAllDateRangeRecords($fromDate, $tillDate,$companyNm,$unitNm,$processStage);
		$manageRmLotIdRecssize	=	sizeof($manageRmLotIdRecords);	
	}
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="90%" align="center">
	<tr>
		<Td height="10" ></td>
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;
	 	Manage RM Lot Id </td>
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
								<td colspan="2" style="padding-left:5px; padding-right:5px;" >
									<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
									<?php
									if($manageRmLotIdRecssize > 0 ) {
											$i	=	0;
									?>
									
									<tr  bgcolor="#f2f2f2" align="center">		
										<th class="listing-head" style="padding-left:10px; padding-right:10px;">RM lot ID</th>
												<th class="listing-head" style="padding-left:10px; padding-right:10px;">Company Name</th>
												<th class="listing-head" style="padding-left:10px; padding-right:10px;">Unit</th>
												<th class="listing-head" style="padding-left:10px; padding-right:10px;">Current Processing Stage </th>
												<th class="listing-head" style="padding-left:10px; padding-right:10px;">History </th>
									</tr>
									<?
									foreach($manageRmLotIdRecords as $cr) 
									{
										$i++;
										$rmmainId		=	$cr[0];
										$rmExist = $objManageRMLOTID->lotIdExist($rmmainId);
										$companyname		=	$cr[8];
										$unit		=	$cr[9];
										$rmlotidnum		=	$cr[5];
										$alpha		=	$cr[6];
										$processingStage= $objManageRMLOTID->getRMProgressStage($rmmainId);
										//$processingStage		=	$cr[7];
										$originId		=	$cr[10];
										$active=$cr[11];
									?>
											
									<tr bgcolor="white"  bgcolor="#afddf8"  
										<?php /*<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$rmmainId;?>" ></td>*/?> >
										<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$alpha.$rmlotidnum;?></td>
										<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$companyname;?></td>
										<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$unit;?></td>
										<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
										<?php
											echo $processingStage;
										?>
										</td>
										<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
										<?php
										if($originId==0)
										{
											echo "No";
										}
										else
										{
										?>
										
											<?php
											$detailsvalue="";
											$displayRMLotIdHistory=$objManageRMLOTID->getLotIdTotalvalueHistory($originId);
											//echo sizeof($displayRMLotIdHistory);
											if(sizeof($displayRMLotIdHistory)>0) {
											$detailsvalue.='<table width=100% border=1 cellspacing=0 cellpadding=2><tr bgcolor=#D9F3FF ><th  class=listing-head>RM lot ID</th><th  class=listing-head>Company Name</th><th  class=listing-head>Unit</th></tr>';
											foreach($displayRMLotIdHistory as $displayRMLotId )
											{
												foreach($displayRMLotId as $drm)
												{
													$historyrmlotid= $drm[0];	
													$historyrmlot= $drm[1].$drm[2];
													$historyunit= $drm[11];
													$historycompany=$drm[12];
													$detailsvalue.='<tr bgcolor=#f2f2f2><td class=listing-item>'.$historyrmlot.'&nbsp;</td><td class=listing-item nowrap>'.$historycompany.'&nbsp;</td><td class=listing-item nowrap>'.$historyunit.'&nbsp;</td></tr>';
												}
											} 
												
														
											$detailsvalue.="</table>";
												
										}
										?>
										<a onMouseOver="ShowTip('<?=$detailsvalue?>');" onMouseOut="UnTip();">yes</a>
									<?php
									}
									?>
									</td>
									</tr>
									<?php
									}
									?>
									<?
									}
									else
									{
									?>
									<tr bgcolor="white">
										<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
									</tr>	
									<?
									}?>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
		  </table>
			<!-- Form fields end   -->
		</td>
	</tr>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>
