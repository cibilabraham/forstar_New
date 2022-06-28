<?php
	require("include/include.php");
	//require_once('lib/ExporterMaster_ajax.php');
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$selection 		=	"?pageNo=".$p["pageNo"];

	
	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}

	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$reEdit = false;
	$confirmF=false;
	$printMode=false;
	
	
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirmF=true;	
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	//echo "The value of confirm is $confirmF";
	//----------------------------------------------------------
	
	//if ($g["print"]=="y")
	//{
	//	$printMode=true;
	//}

	if($p["cmdSave"]!="")
	{ 
		
		$rowCount = $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$setld		= $p["settled_".$i];
			$rate 		= $p["pkgRate_".$i];
			$totPkgAmt 	= $p["totPkgAmt_".$i];
			$numpack	= $p["numpack_".$i];
			$filledwt	= $p["filledwt_".$i];
			$gEntryIds 	= $p["gradeEntryId_".$i];
			$gnummc 	= $p["gnummc_".$i];
			$gnumls		= $p["gnumls_".$i];
			$dftRt		= $p["dftRt_".$i];
			//echo $dftRt.'<br/>'.$rate;
			if($dftRt==$rate)
			{
				$rateListId	= $p["rtLstId_".$i];
			}
			else
			{
				$rateListId="";
			}
			//echo $rateListId;
			//die();
			$groupGradeArr 	= explode(",",$gEntryIds);
			$groupNumMC	= explode(",",$gnummc);
			$groupNumLS	= explode(",",$gnumls);
			$totSlab = 0;
			$qty = 0;
			$gEntryId = "";
			
			for ($j=0; $j<sizeof($groupGradeArr); $j++) 
			{
				$gEntryId = $groupGradeArr[$j];
				$numMC	= $groupNumMC[$j];
				$numLS	= $groupNumLS[$j];
				$totSlab = ($numMC*$numpack)+$numLS;
				$qty     = $totSlab*$filledwt;
				$gradeTotalRate = $qty*$rate;
				$settled = "";
				if($isAdmin==true || $reEdit==true)
				{
					$settled = ($setld=="")?N:$setld;
				}

				//echo "<br>$gEntryId, $settled, $rate, $gradeTotalRate";
				if ($gEntryId!="" && $rate!="") 
				{			
					//$fprRecUpdated =$frznPkgAccountsObj->updateDFPGradeRec($gEntryId, $settled, $rate, $gradeTotalRate);
					$fprRecUpdated =$frznPkgAccountsObj->updateDFPGradeRec($gEntryId, $settled, $rate, $gradeTotalRate,$rateListId);
					
				}
				//echo "<br>TotSlab=>$gEntryId=$totSlab=R=$gradeTotalRate, $settled";
			}			
			//echo "<br>$gEntryIds";
		}
		if ($fprRecUpdated) 
		{
			$sessObj->createSession("displayMsg",$msg_succSaveFrznPkgAccounts);
			//$sessObj->createSession("nextPage",$url_afterSaveFrznPkgAccounts.$selection);
		} 
		else 
		{
			$err=$msg_failFrznPkgAccounts;
		}

}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All port of loading
	$fromDate=""; $tillDate="";
	
	if ($p["dateFrom"]) $dateFrom = $p["dateFrom"];
	if ($p["dateTo"]) $dateTill = $p["dateTo"];
	if($dateFrom!='' &&  $dateTill!='')
	{
		if ($dateFrom) $fromDate  = mysqlDateFormat($dateFrom);
		if ($dateTill) $tillDate  = mysqlDateFormat($dateTill);
		$selProcessorId=$p['selProcessor'];
		$frnPkgRecs	=	$frznPkgAccountsObj->getDFPRecQry($fromDate, $tillDate, $selProcessorId,$offset, $limit);
		$frnPkgRecsSize		=	sizeof($frnPkgRecs);
		$frznPkgAllRecs	=	$frznPkgAccountsObj->getAllRecords($fromDate, $tillDate, $selProcessorId);
		$grandTotFrznQty=0; $grandTotSlab=0; $grandTotPkdQty=0; $grandTotPkgAmt=0;
		if(sizeof($frznPkgAllRecs)>0)
		{
			foreach ($frznPkgAllRecs as $fprAll) 
			{
				$frznQty=$fprAll[15];
				$slb=$fprAll[16];
				$pkdQty=$fprAll[14];
				$pkgAmt=$fprAll[30];
				$grandTotFrznQty += $frznQty;
				$grandTotSlab += $slb;
				$grandTotPkdQty += $pkdQty;
				$grandTotPkgAmt += $pkgAmt;
			}
			$grandTotalFrznQty = number_format($grandTotFrznQty,2,'.',',');
			$grandTotalSlab = number_format($grandTotSlab,0,'',',');
			$grandTotalPkdQty = number_format($grandTotPkdQty,2,'.',',');
			$grandTotalPkgAmt = number_format($grandTotPkgAmt,2,'.',',');
		}
	}
	

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($frznPkgAllRecs);
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	$processorRecords = $preprocessorObj->getActiveProcessorRecs("FrznPkgAccounts.php", '');
	//$ON_LOAD_SAJAX = "Y";	
	$ON_LOAD_PRINT_JS	= "libjs/FrznPkgAccounts.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");		
?>

<form name="frmFrznPkgAccounts" id="frmFrznPkgAccounts" action="FrznPkgAccounts.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
	<?if($err){?>
		<tr>
			<td height="40" align="center" class="err1" ><?=$err ?></td>
		</tr>
		<? }?>
		<tr>
			<td height="10" align="center" ></td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>							
							<?php	
							$bxHeader="Frozen Packing Accounts";
							include "template/boxTL.php";
							?>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<table cellpadding="0" cellspacing="0" align="center">
								<? 
								if(!$printMode)
								{ 
								?>
								<tr>
									<td>
									<? 
									if($edit==true)
									{
									?>
										<input type="submit" value=" Save " name="cmdSave" class="button" onClick="return validateFrznPkgAccounts('Y');">
									<? 
									} 
									?>
									<? 
									if($print==true) 
									{ 
									?>
										<input type="button" value=" Print " name="btnPrint" class="button" onclick="return printWindow('PrintFrznPkgAccounts.php?selProcessor=<?=$selProcessorId?>&fromDate=<?=$fromDate?>&tillDate=<?=$tillDate?>&offset=<?=$offset?>&limit=<?=$limit?>',700,600);" <? if(sizeof($frnPkgRecs)==0) echo "disabled";?>>
									<? 
									}
									?>
									</td>
								</tr>
								<? 
								} 
								?>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5" ></td>
					</tr>
					<? 
					if($errDel){
					?>
					<tr>
						<td colspan="3" height="15" align="center" class="err1"><?=$errDel?></td>
					</tr>
					<? 
					} 
					?>	
					<? 
					if(!$addMode || !$editMode)
					{ 
					?>
					<tr>
						<td height='5' colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">
							<table cellpadding="2"  width="40%" cellspacing="1" border="0" align="center" bgcolor="#999999" >
								<tr bgcolor="#e8edff">
									<td>
										<table>
											<tr>
												<td class="fieldName" nowrap>Select Date:</td>
												<td class="fieldName" nowrap>*From:</td>
												<td nowrap>					
													<input type="text" id="dateFrom" name="dateFrom" size="8" value="<?=$dateFrom?>" onchange="submitForm('dateFrom','dateTo', frmFrznPkgAccounts);" autocomplete="off" />
												</td>
												<td class="fieldName" nowrap>*To:</td>
												<td nowrap>
													<input type="text" id="dateTo" name="dateTo" size="8"  value="<?=$dateTill?>" onchange="submitForm('dateFrom','dateTo', frmFrznPkgAccounts);" autocomplete="off" />		
												</td>
												<td class="fieldName" nowrap="nowrap">*Pre-Processor:</td>
												<td>
													<select name="selProcessor" id="selProcessor">
														<option>--Select--</option>
														<? 
														foreach($processorRecords as $ppr) {
															$processorId	= $ppr[0];
															$processorName	= stripSlash($ppr[1]);
															$selected = ($selProcessorId==$processorId)?"selected":"";
														?>
														<option value="<?=$processorId?>" <?=$selected?>><?=$processorName?></option>
														<? }?>
													</select>
												</td>
												<td style="padding-left:10px; padding-right:10px;">
													<input type="submit" name="cmdSearch" class="button" value=" Search " onclick="return validateFrznPkgAccounts('N');" />
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td height='5' colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">
							<table cellpadding="1"  width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
							<?
							if( sizeof($frnPkgRecs) > 0 )
							{
							?>
								<thead>
								<? if($maxpage>1) { ?>
									<tr>
										<td colspan="16" style="padding-right:10px" class="navRow">
											<div align="right" class="navRow">
												<?php 				 			  
												$nav  = '';
												for($page=1; $page<=$maxpage; $page++)
												{
													if ($page==$pageNo)
													{
														$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
													}
													else
													{
														$nav.= " <a href=\"FrznPkgAccounts.php?pageNo=$page\" class=\"link1\">$page</a> ";
														//echo $nav;
													}
												}
												if ($pageNo > 1)
												{
													$page  = $pageNo - 1;
													$prev  = " <a href=\"FrznPkgAccounts.php?pageNo=$page\"  class=\"link1\"><<</a> ";
												}
												else
												{
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage)
												{
													$page = $pageNo + 1;
													$next = " <a href=\"FrznPkgAccounts.php?pageNo=$page\"  class=\"link1\">>></a> ";
												}
												else
												{
													$next = '&nbsp;'; // we're on the last page, don't print next link
													$last = '&nbsp;'; // nor the last page link
												}
											// print the navigation link
												$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
												echo $first . $prev . $nav . $next . $last . $summary; 
												?>	
												<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
										  </div>
										</td>
									</tr>
									<?
									}
									?>
									<tr align="center">
										<th nowrap style="padding-left:10px; padding-right:10px;">Fish</th>
										<th nowrap style="padding-left:10px; padding-right:10px;">Processcode</th>
										<th nowrap style="padding-left:10px; padding-right:10px;">Freezing <br>Stage</th>
										<th nowrap style="padding-left:10px; padding-right:10px;">Quality</th>
										<th nowrap style="padding-left:10px; padding-right:10px;">Frozen Code</th>
										<th nowrap style="padding-left:10px; padding-right:10px;">MC<br> Pkg</th>
										<th nowrap style="padding-left:10px; padding-right:10px;">Frozen Qty</th>	
										<th nowrap style="padding-left:10px; padding-right:10px;">Pkg<br> Wt</th>
										<th nowrap style="padding-left:10px; padding-right:10px;">Glaze<br> (%)</th>
										<th nowrap style="padding-left:10px; padding-right:10px;" >Net Wt</th><!--title="Wt without glaze"-->
										<th nowrap style="padding-left:10px; padding-right:10px;">Total Units</th>	
										<th nowrap style="padding-left:10px; padding-right:10px;">Qty</th>
										<th nowrap style="padding-left:10px; padding-right:10px;">Grade</th>
										<th nowrap style="padding-left:10px; padding-right:10px;">Rate</th>
										<th nowrap style="padding-left:10px; padding-right:10px;">Total Amt</th>
										<th nowrap style="padding-left:10px; padding-right:10px;">Setld</th>
										<th nowrap style="padding-left:10px; padding-right:10px;">Setld<br> Date</th>
									</tr>
								</thead>
								<tbody>
								<? $i=0;
								//echo sizeof($frnPkgRecs);
								$totalFrznQty=0; $totalSlab=0; $totPkdQty=0; $totalPkAmt=0; $rtLstId='';
								foreach($frnPkgRecs as $fpR)
								{	 $i++; 
									if(!$fpR[0])
									{	$selDate=$fpR[23];
										$processcodeid=$fpR[9];
										$freezingstageid=$fpR[10];
										$qualityid=$fpR[11];
										$frozencodeid=$fpR[12];
										$defaultRate=$frznPkgAccountsObj->getRate($selDate,$selProcessorId,$processcodeid,$freezingstageid,$qualityid,$frozencodeid);
										$rtLstId=$frznPkgAccountsObj->validFPRateList($selDate);
									}
									$settled=$fpR[28];
									$pkdqty=$fpR[14];
									//list($totFrznQty,$totSlab,$totPkdQty)=$frznPkgAccountsObj->calcTotQty($frozenqty,$slab,$pkdqty);

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
									(($pkgRate=='') || ($pkgRate=='0'))? $rateVal=$defaultRate:$rateVal=$pkgRate;
									//echo $pkgRate.'<br/>'.$defaultRate.'<br/>'.$rateVal;
									$pkAmt=$fpR[30];
									$totalFrznQty 	+= $frozenqty; 
									$totalSlab	+= $slab;
									$totalPkdQty	+= $pkdqty;
									$totalPkAmt+=$pkAmt;
									$totFrznQty = number_format($totalFrznQty,2,'.',',');
									$totSlab = number_format($totalSlab,0,'',',');
									$totPkdQty = number_format($totalPkdQty,2,'.',',');
									$totPkAmt = number_format($totalPkAmt,2,'.',',');
									
								?>
								<tr>		
									<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
										<?=$fishname?>
										<input type="hidden" name="gradeEntryId_<?=$i?>" id="gradeEntryId_<?=$i?>" size="5" style="text-align:right" value="<?=$gentryid?>" autocomplete="off" readonly />
										<input type="hidden" name="numpack_<?=$i?>" id="numpack_<?=$i?>" size="5" style="text-align:right" value="<?=$numpack?>" autocomplete="off" readonly />
										<input type="hidden" name="gnummc_<?=$i?>" id="gnummc_<?=$i?>" size="5" style="text-align:right" value="<?=$gnummc?>" autocomplete="off" readonly />
										<input type="hidden" name="gnumls_<?=$i?>" id="gnumls_<?=$i?>" size="5" style="text-align:right" value="<?=$gnumls?>" autocomplete="off" readonly />
										<input type="hidden" name="rtLstId_<?=$i?>" id="rtLstId_<?=$i?>" size="5" value="<?=$rtLstId?>" autocomplete="off" readonly />
										<input type="hidden" name="dftRt_<?=$i?>" id="dftRt_<?=$i?>" size="5" value="<?=$defaultRate?>" />
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
										<input type="hidden" name="filledwt_<?=$i?>" id="filledwt_<?=$i?>" size="5" style="text-align:right" value="<?=$filledwt?>" readonly />
									</td>
									<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$slab?></td>
									<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right">
										<?=$pkdqty?>
										<input type="hidden" name="pkdQty_<?=$i?>" id="pkdQty_<?=$i?>" size="5" style="text-align:right" value="<?=$pkdqty?>" readonly />
									</td>
									<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$grade?></td>
									<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right">
							
										<input type="text" name="pkgRate_<?=$i?>" id="pkgRate_<?=$i?>" size="5" style="text-align:right" value="<?=$rateVal?>" onkeyup="calcFPRAmt();" autocomplete="off" />
										<?php /*<input type="text" name="pkgRate_<?=$i?>" id="pkgRate_<?=$i?>" size="5" style="text-align:right" value="{setRate(fpR.pkgrate)}" onkeyup="calcFPRAmt();" autocomplete="off" />*/?>
										
									</td>
									<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right">
										<input type="text" name="totPkgAmt_<?=$i?>" id="totPkgAmt_<?=$i?>" size="8" value="<?=$pkAmt?>" style="text-align:right; border:none;" readonly />
									</td>	
									<td class="listing-item" nowrap align="center">&nbsp;
									<? if($settled=='Y')
									{
									?>
										<input name="settled_<?=$i?>" type="checkbox" id="settled_<?=$i?>" value="Y" class="chkBox" checked >
									<?
									}
									else
									{
									?>
										<input name="settled_<?=$i?>" type="checkbox" id="settled_<?=$i?>" value="Y" class="chkBox" />
									<?
									}
									?>
									</td>
									<td class="listing-item" nowrap align="center"><?=$setlddate?></td>
								</tr>
								<?
								
								}
											
								?>
								<input type="hidden" name="hidRowCount"id="hidRowCount" value="<?=$frnPkgRecsSize?>" >
								<input type="hidden" name="editId" value="" readonly>
								</tbody>
								<tr bgcolor="White">
									<TD colspan="6" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">
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
											<input type="text" name="totalAmt" id="totalAmt" size="8" value="<?=$totPkAmt?>" style="border:none; text-align:right;" readonly />
										</strong>
									</td>
									<td colspan="2">&nbsp;</td>
								</tr>
							<!-- Grand Total -->
								<tr bgcolor="White">
									<TD colspan="6" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">
										Grand Total:
									</TD>
									<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
										<strong><?=$grandTotalFrznQty?></strong>
									</td>
									<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
										&nbsp;
									</td>
									<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
										&nbsp;
									</td>
									<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
										&nbsp;
									</td>
									<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
										<strong><?=$grandTotalSlab?></strong>
									</td>
									<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
										<strong><?=$grandTotalPkdQty?></strong>
									</td>
									<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
										&nbsp;
									</td>
									<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
										&nbsp;
									</td>
									<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right" title="Rate entered grand total amt">
										<strong><?=$grandTotalPkgAmt?></strong>
									</td>
									<td colspan="2">&nbsp;</td>
								</tr>
								
								<? if($maxpage>1){?>
								<tr>
									<td colspan="16" style="padding-right:10px" class="navRow">
										<div align="right">
										<?php 				 			  
										$nav  = '';
										for($page=1; $page<=$maxpage; $page++)
										{
											if ($page==$pageNo)
											{
												$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
											}
											else
											{
												$nav.= " <a href=\"FrznPkgAccounts.php?pageNo=$page\" class=\"link1\">$page</a> ";
													//echo $nav;
											}
										}
										if ($pageNo > 1)
										{
											$page  = $pageNo - 1;
											$prev  = " <a href=\"FrznPkgAccounts.php?pageNo=$page\"  class=\"link1\"><<</a> ";
										}
										else
										{
											$prev  = '&nbsp;'; // we're on page one, don't print previous link
											$first = '&nbsp;'; // nor the first page link
										}

										if ($pageNo < $maxpage)
										{
											$page = $pageNo + 1;
											$next = " <a href=\"FrznPkgAccounts.php?pageNo=$page\"  class=\"link1\">>></a> ";
										}
										else
										{
											$next = '&nbsp;'; // we're on the last page, don't print next link
											$last = '&nbsp;'; // nor the last page link
										}
											// print the navigation link
										$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
										echo $first . $prev . $nav . $next . $last . $summary; 
										?>
										</div>
									</td>
								</tr>
								
							<? }?>
							
							<?
							}
							else
							{
							?>
							<!--	<tr><TD align="center" colspan="3"><?=$msgNoRecords;?></TD></tr>-->
							<?
							}
							?>
								
							</table>
						</td>
					</tr>
					
					<? }
					?>
					<tr>
						<td colspan="3"> 
							<table cellpadding="0" cellspacing="0" align="center">
								<? if(!$printMode)
								{ ?>
								<tr>
									<td>
									<? if($edit==true)
									{ ?>
										<input type="submit" value=" Save " name="cmdSave" class="button" onClick="return validateFrznPkgAccounts('Y');">
									<? } ?>
									<? if($print==true) 
									{ ?>
										<input type="button" value=" Print " name="btnPrint" class="button" onclick="return printWindow('PrintFrznPkgAccounts.php?selProcessor=<?=$selProcessorId?>&fromDate=<?=$fromDate?>&tillDate=<?=$tillDate?>&offset=<?=$offset?>&limit=<?=$limit?>',700,600);" <? if(sizeof($frnPkgRecs)==0) echo "disabled";?>>
									<? }
									?>
									</td>
								</tr>
								<? } ?>
							</table>
						</td>
					</tr>
					<tr>
						<td height='5' colspan="3">&nbsp;
						</td>
					</tr>
					
					</table>
					</td></tr>
					</table>
					</td></tr>
	</table>
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
<?php
if( sizeof($frnPkgRecs) > 0 )
{
?>
	<script>
	calcFPRAmt();
	</script>
<?php
}
?>

<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateTo",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateTo", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>





	


