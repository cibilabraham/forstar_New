<?php
	require("include/include.php");
	require_once("lib/ChangesUpdateMaster_ajax.php");
	ob_start();

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------	
	$noOfDays 	= 6;  //Set No. of days to display for days 6
	$noOfMonths 	= 6;  //Set No. of Months to display for days 6
	/*$currentUrl="Home.php";*/
	//list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);
//echo $moduleId;
	/*$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}*/

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

	$dashBoardStock = $dashboardManagerObj->getReorderLevelStock();
?>
<!--
<div style='text-align:right;padding-right:20px;'>
<a style='text-decoration: none;' class="page_hint" href='http://192.168.1.199/forstarIMlatest/prosess-for-forstar-2.pdf'><strong>>> Click here to Download Proforma Invoice Document</strong></a><br>
<a style='text-decoration: none;' class="page_hint" href='http://192.168.1.199/forstarIMlatest/prosess-for-forstar.pdf'><strong>>> Click here to Download Invoice Document</strong></a><br>
</div>-->
<form name="frmHome" action="home.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	<tr><td height="10" align="center"></td></tr>
		<tr>
		<?php
			# Check RM Section Enabled
			list($rmEnabled, $dBoardSize, $selUserId) = $dashboardManagerObj->chkRMEnabled($roleId, $userId);
			if ($rmEnabled || $isAdmin)  {
				# id="DPS" Need
		?>
			<td valign="top" align="left" id="DPS">
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%">
					<tr>
						<td >
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bgh.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bgh.gif" class="pageName" style="font-size:11px; line-height:normal;" >&nbsp;Daily Production Last 7 Days</td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" align="left" >
		<table cellpadding="0"  width="50%" cellspacing="0" border="0" align="center" id="gradient-style">
		<?php
			if ($noOfDays > 0) {
				$i	=	0;
				$frznPkgActive = false;
				$rowSpan = (!$dashboardManagerObj->chkDashboardEnabled($roleId, 'RMQ', $selUserId) && !$isAdmin)?1:2;	
		?>
		<thead>
			<tr align="center">
				<th nowrap style="padding-left:5px; padding-right:5px;" rowspan="<?=$rowSpan?>">Date</th>
				<?php
					if ($dashboardManagerObj->chkDashboardEnabled($roleId, 'RMQ', $selUserId) || $isAdmin) {
				?>
				<th colspan="8" align="center" style="padding-left:5px; padding-right:5px;">RM Quantity</th>
				<? if ($dBoardSize>1 || $isAdmin) {?><Th rowspan="<?=$rowSpan?>">&nbsp;</Th><? }?>
				<?php
					}
				?>
				<?php
					if ($dashboardManagerObj->chkDashboardEnabled($roleId, 'PPQ', $selUserId) || $isAdmin) {
				?>
				<th align="center" nowrap="true" rowspan="<?=$rowSpan?>" style="padding-left:5px; padding-right:5px;">Qty for <br> Pre-Process<!--Pre-Processed <br/>Qty--></th>
				<? if ($dBoardSize>1 || $isAdmin) {?><Th rowspan="<?=$rowSpan?>">&nbsp;</Th><? }?>
				<?php
					}
				?>
				<?php
					if ($dashboardManagerObj->chkDashboardEnabled($roleId, 'FPQ', $selUserId) || $isAdmin) {
						$frznPkgActive = true;
				?>
				<th align="center" nowrap="true" rowspan="<?=$rowSpan?>" style="padding-left:5px; padding-right:5px;" title="Available qty for pkg">Qty for<br>Pkg</th>
				<th align="center" nowrap="true" rowspan="<?=$rowSpan?>" style="padding-left:5px; padding-right:5px;" title="Qty calc is based on Actual Filled wt">Qty Pkd<!--Frozen Packing <br/>Qty--></th>
				<th align="center" nowrap="true" rowspan="<?=$rowSpan?>" style="padding-left:5px; padding-right:5px;" title="Qty calc is based on Actual Filled wt">Pkg<br/>(%)</th>
				<th align="center" nowrap="true" rowspan="<?=$rowSpan?>" style="padding-left:5px; padding-right:5px;" title="Daily Raw Material Closing Balance."><!--Daily-->RM CB <br/>Qty</th>
				<?php
					}
				?>
			</tr>		
				<?php
					if ($dashboardManagerObj->chkDashboardEnabled($roleId, 'RMQ', $selUserId) || $isAdmin) {
				?>
			<tr align="center">				
				<th nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Effective<br> Qty</th>
				<th nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Adjust<br> Qty</th>
				<th nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Gd-Ct<br/> Adj</th>
				<th nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Total<br> Quantity</th>
				<th>&nbsp;</th>
				<th nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Local<br> Qty</th>
				<th nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Wastage<br> Qty</th>
				<th nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Soft<br> Qty</th>		
			</tr>
		</thead>
			<?php
				}
			?>
			<tbody>
			<?php
			if ($g["dpStartDate"]!="") $currentDate   = base64_decode($g["dpStartDate"]);
			else $currentDate   = date("d/m/Y");
			
			$dateC	   =	explode("/", $currentDate);
			$dpPrevDate =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],$dateC[0]-7,$dateC[2]));
			$dpNextDate =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],$dateC[0]+7,$dateC[2]));
			
			$totalQty = "";
			for ($i=0; $i<=$noOfDays; $i++) {
				$selDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]-$i,$dateC[2]));
				
				$challanDate = dateFormat($selDate);
				if ($dashboardManagerObj->chkDashboardEnabled($roleId, 'RMQ', $selUserId) || $isAdmin) {	
					list($rmQty, $adjustQty, $localQty, $wastageQty, $softQty, $gradeCountAdj) = $homeObj->getRMQty($selDate);	
					$totalQty = $rmQty + $adjustQty; // Total Qty = EffectiveQty + AdjustQty
					$displayGdCtAdj = "";
					if ($gradeCountAdj==0) $displayGdCtAdj = "-";
					else $displayGdCtAdj = "<a href='DailyCatchEntry_New.php?supplyFrom=$challanDate&supplyTill=$challanDate' class='link1' title='Please check Grade/Count Adj.'>?</a>";
				}

				# Pre-Processed Qty
				$totalPreProcessedQty = $homeObj->getTotalPreProcessedQty($selDate);

				# Frozen packing Qty
				$totalFPQty = $homeObj->getFrznPkgQty($selDate);

				# Daily RM CB Qty
				$totalRMCBQty = $homeObj->getDailyRMCBQty($selDate);

				# Qty For Pkg
				list($qtyForPkg, $prevDayCBQty) = $homeObj->qtyForPkg($selDate);

				# Calc Pkg Percent
				$pkgPercent = number_format(($totalFPQty/$qtyForPkg)*100,2,'.','');
			?>
			<tr>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;">
					<?=($totalQty!=0)?"<a href='ProductionAnalysisReport.php?dateFrom=$challanDate&dateTo=$challanDate' class='link1' title='Click here to analysis daily production.'>$challanDate</a>":$challanDate?>
				</td>
				<?php
					if ($dashboardManagerObj->chkDashboardEnabled($roleId, 'RMQ', $selUserId) || $isAdmin) {
				?>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
					<?=($rmQty!=0)?"<a href='DailyCatchEntry_New.php?supplyFrom=$challanDate&supplyTill=$challanDate' class='link1' title='Click here to view Daily catch entry list.'>".number_format($rmQty,2)."</a>":number_format($rmQty,2)?>
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
					<? echo number_format($adjustQty,2);?>
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center">
					<?=$displayGdCtAdj?>
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
					<?php if ($totalQty!=0) { ?>
					<a href="DailyCatchSummary.php?dateSelection=SD&selDate=<?=$challanDate?>&proSummary=Y&localQtyChk=Y&searchMode=QS&dateSelectFrom=WCD" class="link1">
						<?=number_format($totalQty,2);?>
					</a>
					<? } else {?>
						<?=number_format($totalQty,2);?>
					<? }?>
				</td>
				<td>&nbsp;</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><? echo number_format($localQty,2);?></td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><? echo number_format($wastageQty,2);?></td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><? echo number_format($softQty,2);?></td>
				<? if ($dBoardSize>1 || $isAdmin) {?><td>&nbsp;</td><? }?>
				<?php
					}
				?>
				<?php
					if ($dashboardManagerObj->chkDashboardEnabled($roleId, 'PPQ', $selUserId) || $isAdmin) {
				?>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
					<?=($totalPreProcessedQty!=0)?"<a href='DailyPreProcess.php?selDate=$challanDate' class='link1' title='Click here to view Daily Pre-process list.'>$totalPreProcessedQty</a>":"";?>
				</td>
				<? if ($dBoardSize>1 || $isAdmin) {?><td>&nbsp;</td><? }?>
				<?php
					}
				?>
				<?php
					if ($dashboardManagerObj->chkDashboardEnabled($roleId, 'FPQ', $selUserId) || $isAdmin) {
				?>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
					<?=($qtyForPkg!=0 && $rmQty!=0)?"<a href='DailyFrozenPacking.php?frozenPackingFrom=$challanDate&frozenPackingTill=$challanDate' class='link1' title='Click here to view Qty for Pkg.'>$qtyForPkg</a>":"";?>					
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
					<?=($totalFPQty!=0)?"<a href='DailyFrozenPacking.php?frozenPackingFrom=$challanDate&frozenPackingTill=$challanDate' class='link1' title='Click here to view Daily Frozen Packing list.'>$totalFPQty</a>":"";?>
					<?php
					//if ($pkgPercent!=0) echo "<br>($pkgPercent%)";
					?>
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
					<?=($pkgPercent!=0 && $prevDayCBQty!=0)?$pkgPercent:"&nbsp;";?>
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
					<?=($totalRMCBQty!=0)?"<a href='DailyRMCB.php?selFilterDate=$challanDate' class='link1' title='Click here to view Daily RM Closing Balance.'>$totalRMCBQty</a>":"";?>
				</td>
				<?php
					}
				?>
			</tr>
			<?php
				}
			?>
		</tbody>
		<tfoot>
			<tr bgcolor="White">
				<?php					
					$decCol = 0;
					if ($dBoardSize>1) {
						$addCol = $dBoardSize;
						$decCol = 1;
					}	
					else if ($dBoardSize==1) {
						$addCol = 0;
						if ($frznPkgActive) $decCol = 5;
					}
					else if ($dBoardSize==0) $addCol = 4;
						
					$pnColSpan = (8+($addCol*2)-$decCol);
					if ($pnColSpan>12) $pnColSpan = 15;
				?>
				<td colspan="<?=$pnColSpan?>">
					<a href="Home.php?dpStartDate=<?=base64_encode($dpPrevDate)?>&#DPS" class="homeNavLink">Prev</a>
				</td>
				<td align="right">
					<a href="Home.php?dpStartDate=<?=base64_encode($dpNextDate)?>&#DPS" class="homeNavLink">Next</a>
				</td>
			</tr>
		</tfoot>
			<?php
				} else {
			?>
			<tr bgcolor="white">
				<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
			</tr>
			<?php
				}
			?>
		</table>
									</td>
								</tr>								
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								
							</table>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		<td>&nbsp;&nbsp;</td>
		<?php
			}
		?>
<!-- Second Column -->
	<?php
		# MIC
		if ($dashboardManagerObj->chkDashboardEnabled($roleId, 'MIC', $selUserId) || $isAdmin) {
			# Id = MCS
	?>
<td valign="top" id="MCS">
	<table cellpadding="0"  cellspacing="0" border="0" align="center"  width="70%">
					<tr>
						<td>
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bgg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bgg.gif" class="pageName" style="font-size:11px; line-height:normal;" >&nbsp;&nbsp;Missing Challan Last 7 Days</td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" align="left">
<!-- newspaper-b :: gradient-style -->
		<table cellpadding="0"  width="50%" cellspacing="0" border="0" align="center" id="gradient-style">
		<?
			if ($noOfDays > 0) {
				$i	=	0;
		?>
			<thead>
			<tr align="center">
				<th class="rounded-company" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Date</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Last <br>Challan No</th>
				<th class="rounded-q4" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Missing<br> Challan No.s</th>
			</tr>
			</thead>
			<tbody>
			<?php
			if ($g["mcStartDate"]!="") $currentDate = base64_decode($g["mcStartDate"]);
			else $currentDate   = date("d/m/Y");

			$dateC	   =	explode("/", $currentDate);

			$mcPrevDate =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],$dateC[0]-7,$dateC[2]));
			$mcNextDate =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],$dateC[0]+7,$dateC[2]));
		
			$totalQty = "";
			for ($i=0; $i<=$noOfDays; $i++) {
				$selDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]-$i,$dateC[2]));
				$challanDate = dateFormat($selDate);
				$prevDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]-($i+1),$dateC[2]));
				$prevChallanDate = dateFormat($prevDate);

				# Get Billing Comapny Recs
				$getBillCompanyRecs = $homeObj->getBillingCompanyWiseRecs($selDate);
				
			if (sizeof($getBillCompanyRecs)>0) { 
				foreach ($getBillCompanyRecs as $gbcr) {
					//$lastRMChallanNumber 	= $gbcr[1];
					$billingCompanyId	= $gbcr[2];
					# Find the Last RM Challan Number (Max Challan Number)
					$lastRMChallanNumber = $homeObj->getLastChallanNumber($selDate, $billingCompanyId);
					$alphaCode		= $gbcr[3];
					$displayRMChallanNo 	= $alphaCode.$lastRMChallanNumber;
					# Find the Prev Date Last Challan Number (Min Challan Number, Callan Date)
					list($prevLastRMChallanNumber, $selPrevDate)  =  $homeObj->getPrevLastChallanNumber($prevDate, $billingCompanyId, $selDate);
					
					# Find the Missing Challan Numbers
					$missingChallanRecords = array();
					if ($prevLastRMChallanNumber=="") {
						list($startingNumber, $endingNumber) = $manageChallanObj->getChallanRec($selDate, $billingCompanyId);
						$prevLastRMChallanNumber = $startingNumber;
					}
					//echo "<br/>$prevLastRMChallanNumber, $lastRMChallanNumber, $selDate,$billingCompanyId<br/>";	
					if ($prevLastRMChallanNumber!="" && $lastRMChallanNumber!="") {
						$missingChallanRecords = $homeObj->getMissingRecords($prevLastRMChallanNumber, $lastRMChallanNumber, $selDate, $billingCompanyId);
					}				
					$displayLink = "";				
					if (sizeof($missingChallanRecords)>0) $displayLink = "ChallanVerification.php?supplyFrom=".dateFormat($selPrevDate)."&supplyTill=$challanDate&billingCompany=$billingCompanyId&startCNum=$prevLastRMChallanNumber&endCNum=$lastRMChallanNumber";
					else $displayLink = "###";
					
			?>
			<tr>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;">
					<? if ($isAdmin!="" && sizeof($missingChallanRecords)>0) {?>
						<a href="<?=$displayLink?>" class="link1" title="Click here to cancel the challan No.s">
						<?=$challanDate;?>
					</a>
					<? } else {?>
						<?=$challanDate;?>
					<? }?>	
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$displayRMChallanNo?></td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;">
					<?php
					$numCol = 3;
					if (sizeof($missingChallanRecords)>0) {
						if (sizeof($missingChallanRecords)<=10) {
							$nextRec = 0;
							$missingChallan = "";
						        foreach ($missingChallanRecords as $key=>$value) {
								$missingChallan = $value;
								$nextRec++;

								if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $missingChallan;
								if($nextRec%$numCol == 0) echo "<br/>";
							}
						} else {  # If size greater than 10
							echo sizeof($missingChallanRecords)."&nbsp;Challan Missed";
						}
					} else if ($lastRMChallanNumber!="") echo "NIL";
					?>
					
				</td>		
			</tr>
				<?php
					} // Billing company For Loop ends Here
	
					} else { // Billing company Check Ends
				?>
			<tr>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;">		
						<?=$challanDate;?>					
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?//$lastRMChallanNumber?></td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;">&nbsp;</td>		
			</tr>
			
			<?
				} // Billing company else ends herre 
			?>
			
			<?
				}
			?>
			</tbody>
			<tfoot>
				<tr bgcolor="White">
				<td>
					<a href="Home.php?mcStartDate=<?=base64_encode($mcPrevDate)?>&#MCS" class="homeNavLink">Prev</a>
				</td>
				<td>&nbsp;</td>
				<td align="right">
					<a href="Home.php?mcStartDate=<?=base64_encode($mcNextDate)?>&#MCS" class="homeNavLink">Next</a>		
				</tr>
			</tfoot>

			<?php
				} else {
			?>
			<tr bgcolor="white">
				<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
	</table>
	</td>
	</tr>
	</table>
</td>
	<td>&nbsp;&nbsp;</td>
	<?php
		}
	?>
	</tr>	
	<tr><TD height="10"></TD></tr>
<tr>	
<td valign="top" colspan="3">
	<table>
	<TR>
	<TD valign="top" id="SOS">	
	<?php
		# SOI
		if ($dashboardManagerObj->chkDashboardEnabled($roleId, 'SOI', $selUserId) || $isAdmin) {
	?>
	<table cellpadding="0"  cellspacing="0" border="0" align="left"  width="70%">
					<tr>
						<td>
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bgg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bgg.gif" class="pageName" style="font-size:11px; line-height:normal;" >&nbsp;&nbsp;Sales Order Last 7 Months</td>
								</tr>
								<tr>
									<td width="1" ></td>
		<td colspan="2">
		<table>
		    <TR><TD valign="top">
		<table cellpadding="0"  width="50%" cellspacing="0" border="0" align="center" id="gradient-style">
		<?php
			if ($noOfMonths > 0) {
				$i	=	0;
		?>
			<thead>
			<tr align="center">
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Month</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;" onMouseover="ShowTip('Total Sales Order value');" onMouseout="UnTip();">Total Amt</th>				
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;" onMouseover="ShowTip('Total Pending Order value');" onMouseout="UnTip();">Pending Amt</th>				
			</tr>
			</thead>			
			<tbody>
			<?php
			if ($g["soStartDate"]!="") $currentDate   = base64_decode($g["soStartDate"]);
			else $currentDate   = date("d/m/Y");

			$dateC	   =	explode("/", $currentDate);

			$selDate = "";
			$billedAmt = "";
			$pendingOrderAmt = "";

			$soDateArr = array();
			for ($i=0; $i<=$noOfMonths; $i++) {
				$selMonth = date("M",mktime(0, 0, 0,$dateC[1]-$i,1,$dateC[2]));	
				$month   =  date("m",mktime(0, 0, 0,$dateC[1]-$i,1,$dateC[2]));	
				$selYear   =  date("Y",mktime(0, 0, 0,$dateC[1]-$i,1,$dateC[2]));	
				$lastDateOfMonth=date('t',mktime(0, 0, 0,$dateC[1]-$i,1,$dateC[2]));
				$firstdate   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1]-$i,1,$dateC[2]));
				$soDateArr[$i] = $firstdate;
				$lastdate   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1]-$i, $lastDateOfMonth, $dateC[2]));
				# C= Complete, P=Pending
				$billedAmt	 = $homeObj->getSOBilledAmt($month, 'C', $selYear);
				$pendingOrderAmt = $homeObj->getSOBilledAmt($month, 'P', $selYear);
				
				$selDYear   =  date("y",mktime(0, 0, 0,$dateC[1]-$i,1,$dateC[2]));	
				$displayMonth = $selMonth."&nbsp;".$selDYear;
			?>
			<tr>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left">
					<?=$displayMonth;?>
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
					<?php
						 if ($billedAmt!=0)  { 
					?>
					<a href="SalesOrderReport.php?dateFrom=<?=$firstdate?>&dateTill=<?=$lastdate?>&selStatus=C&reportType=DIST&redirect=H&dateSelection=INV" class="link1" title="Click here to view detailed report">
						<?=number_format($billedAmt,2,'.',',');?>
					</a>
					<?php
						} 
					?>
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
					
					<?php
						 if ($pendingOrderAmt!=0)  { 
					?>
					<a href="SalesOrderReport.php?dateFrom=<?=$firstdate?>&dateTill=<?=$lastdate?>&selStatus=P&reportType=DIST&redirect=H&dateSelection=INV" class="link1" title="Click here to view detailed report">
						<?=number_format($pendingOrderAmt,2,'.',',');?>
					</a>
					<?php
						} 
					?>
				</td>
			</tr>
			<?php
				}
			?>
			</tbody>
			<tfoot>
				<?php
						# Next Date
						$nSOStDate = $soDateArr[0];
						$nSODate	   =	explode("/", $nSOStDate); 
						$nextSODate = date("d/m/Y",mktime(0,0,0,$nSODate[1]+7,$nSODate[0],$nSODate[2]));

						# prev Date
						$pSOStDate = $soDateArr[sizeof($soDateArr)-1];
						$pSODate	   =	explode("/",$pSOStDate);
						$prevSODate = date("d/m/Y",mktime(0,0,0,$pSODate[1]-1,$pSODate[0],$pSODate[2]));
						//echo "Prev=$prevSODate, N=$nextSODate ";	
				?>
				<tr bgcolor="White">
				<td>
					<a href="Home.php?soStartDate=<?=base64_encode($prevSODate)?>&#SOS" class="homeNavLink">Prev</a>
				</td>
				<td>&nbsp;</td>
				<td align="right">
					<a href="Home.php?soStartDate=<?=base64_encode($nextSODate)?>&#SOS" class="homeNavLink">Next</a>		
				</tr>
			</tfoot>
			<?php
				} else {
			?>
			<tr bgcolor="white">
				<td colspan="3"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
			</tr>
			<?php
				}
			?>
		</table>
		</TD>
		<th valign="top">
	<table cellpadding="0"  width="50%" cellspacing="0" border="0" align="center" id="gradient-style">
		<?php
		if ($noOfDays > 0) {
			$i	=	0;
			$nextDays  = $g["nextDays"];
			
			if ($g["startDate"]!="") $currentDate   = base64_decode($g["startDate"]);
			else $currentDate   = date("d/m/Y");
			$dateC	   =	explode("/", $currentDate);
				
				$displayArr = array();
				if ($nextDays=="") {
					$daysAhead = 3;
					$daysBack = 3;				
					$c = 0;
					for ($dh=$daysAhead; $dh>0; $dh--) {
						$sDDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]+$dh,$dateC[2]));
						$displayArr[$c] = $sDDate;
						$c++;
					}
					if ($nextDays=="") $displayArr[$c++] = date("Y-m-d");
					for ($db=1; $db<=$daysBack; $db++) {
						$sDDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],($dateC[0]-$db),$dateC[2]));
						$displayArr[$c++] = $sDDate;
					}
				} 
	
				if ($nextDays!="") {
					$sDispatchDate = "";
					for ($i=0; $i<=$noOfDays; $i++) {
						$sDispatchDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]-$i,$dateC[2]));
						$displayArr[$i] = $sDispatchDate;
					}
				}

				/*
				if ($nextDays!="") {
					$dateF	   =	explode("-", $getDate); // 2009-08-01 : 1/8/09
					
					$cnt = 0;	
					$inc = 0;			
					for ($i=$noOfDays; $i>=0; $i--) {
						if ($nextDays>0) $inc = $i+1;
						else if ((int)$nextDays<0) $inc = (-$i-1);
						//echo "<br>".$sos = ($i-1)*$nextDays+1;
						//echo $inc;
						$sDispatchDate = date("Y-m-d",mktime(0,0,0,$dateF[1],$dateF[2]+$inc,$dateF[0]));
						$displayArr[$cnt] = $sDispatchDate;
						$cnt++;
					}
				}
				*/
				
				
		?>
			<thead>
			<tr align="center">
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Despatch Date</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;" onMouseover="ShowTip('Invoice Nos based on Despatch date.');" onMouseout="UnTip();">Despatch Inv No</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;" onMouseover="ShowTip('Invoice Nos based on delivery date.');" onMouseout="UnTip();">Delivery Inv No.</th>			
			</tr>
			</thead>
			<!--<tfoot>
				<tr bgcolor="White">
				<td>
					<input type="hidden" name="prevStartDate" value="<?//=$displayArr[sizeof($displayArr)-1]?>">
					<a href="Home.php?nextDays=<?=-7?>&startDate=<?//=base64_encode($displayArr[sizeof($displayArr)-1])?>" class="homeNavLink">Prev</a></td>
				<td>&nbsp;</td>
				<td align="right"><a href="Home.php?nextDays=<?=7?>&startDate=<?//=base64_encode($displayArr[0])?>" class="homeNavLink">Next</a>
					<input type="hidden" name="nextStartDate" value="<?//=$displayArr[0]?>"></td>
				</tr>
			</tfoot>-->
			<tbody>
			<?php			
			foreach ($displayArr as $daKey=>$selDispatchDate) {
				
				# Despatch Details
				$dispatchSORecs = $homeObj->getSOBasedOnDespatchDate($selDispatchDate);
				# Delivery Details
				$deliverySORecs = $homeObj->getSOBasedOnDeliveryDate($selDispatchDate);

				$displayDespatchDate = "";
				if (date("Y-m-d")==$selDispatchDate) $displayDespatchDate= "<b>".dateFormat($selDispatchDate)."</b>";
				else $displayDespatchDate= dateFormat($selDispatchDate);
			?>
			<tr>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left">
					<?php
						 if (sizeof($dispatchSORecs)>0 || sizeof($deliverySORecs)>0)  { 
					?>
					<a href="SalesOrderReport.php?dateFrom=<?=dateFormat($selDispatchDate)?>&dateTill=<?=dateFormat($selDispatchDate)?>&selStatus=&reportType=DIST&redirect=H" class="link1" title="Click here to view detailed report">
						<?=$displayDespatchDate?>
					</a>
					<?php
						}  else {
					?>
						<?=$displayDespatchDate?>
					<? }?>
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center">
					<?php
					$numCol = 3;
					if (sizeof($dispatchSORecs)>0) {
						$nextRec=	0;
						$invoiceNo = "";
						foreach ($dispatchSORecs as $dsor) {
							$soNo 	= $dsor[0];		
							$invType = $dsor[1];			
							$pfNo 	= $dsor[2];
							$saNo	= $dsor[3];
							$invoiceNo = "";
							if ($soNo!=0) $invoiceNo=$soNo;
							else if ($invType=='T') $invoiceNo = "P$pfNo";
							else if ($invType=='S') $invoiceNo = "S$saNo";
							$nextRec++;

							if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $invoiceNo;
							if($nextRec%$numCol == 0) echo "<br/>";
						}
					}
					?>
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center">
					<?php
					$numDInvLine = 3;
					if (sizeof($deliverySORecs)>0) {			
						$nextDInvRec	=	0;
						$dInvoiceNo = "";			
					        foreach ($deliverySORecs as $dsor) {
							$soNo 	= $dsor[0];		
							$invType = $dsor[1];			
							$pfNo 	= $dsor[2];
							$saNo	= $dsor[3];		
// 							# Get Inv No (Config)	
							$dInvoiceNo = getInvFormat($invType, $soNo, $pfNo, $saNo);
							$nextDInvRec++;

							if($nextDInvRec>1) echo "&nbsp;,&nbsp;"; echo $dInvoiceNo;
							if($nextDInvRec%$numDInvLine == 0) echo "<br/>";
						}
					}
					?>
				</td>
				
			</tr>
			<?php
				}
			?>
			</tbody>
			<tfoot>
				<tr bgcolor="White">
				<td>
					<?php
						# Next Date
						$nStDate = $displayArr[0];
						$nSDate	   =	explode("-", $nStDate);
						$nextDespatchDate = date("d/m/Y",mktime(0,0,0,$nSDate[1],$nSDate[2]+7,$nSDate[0]));

						# prev Date
						$pStDate = $displayArr[sizeof($displayArr)-1];
						$pSDate	   =	explode("-",$pStDate);
						$prevDespatchDate = date("d/m/Y",mktime(0,0,0,$pSDate[1],$pSDate[2]-1,$pSDate[0]));
					?>
					<a href="Home.php?nextDays=<?=-7?>&startDate=<?=base64_encode($prevDespatchDate)?>&#SOS" class="homeNavLink">Prev</a>
				</td>
				<td>&nbsp;</td>
				<td align="right">
					<a href="Home.php?nextDays=<?=7?>&startDate=<?=base64_encode($nextDespatchDate)?>&#SOS" class="homeNavLink">Next</a>	
				</tr>
			</tfoot>
			<?php
				} else {
			?>
			<tr bgcolor="white">
				<td colspan="3"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
			</tr>
			<?php
				}
			?>
		</table>
		</td>
		</TR>
	</table>
	</td>
	</tr>
		<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								
							</table>
						</td>
					</tr>
				</table>
	<?php
		}
	?>
	</TD>
	<td>&nbsp;&nbsp;</td>
	<td valign="top">
	<?php
		# Distributor Account
		if ($dashboardManagerObj->chkDashboardEnabled($roleId, 'DAC', $selUserId) || $isAdmin) {

			# Pending cheque display days
			list($pChqDays, $crBalDisplayLimit, $overdueDisplayLimit) = $dashboardManagerObj->getPendingChqDisplayDays();
									
			# Current Financial Year
			$dateFrom = date("d/m/Y", mktime(0, 0, 0, 04, 01, (date("Y")-1)));
			$dateTill = date("d/m/Y");

			$pendingChqTillDate = date("d/m/Y", mktime(0, 0, 0, date("m"), (date("d")+$pChqDays), date("Y")));
			//$fYearMonth = date("Ym", mktime(0, 0, 0, 04, 01, (date("Y")-1)));
			//$tYearMonth = date("Ym");

			//$depositedChequeRecs = $homeObj->getDepositedChqRecs($yearMonth);
			//$depositedChequeRecSize = sizeof($depositedChequeRecs);
			# Get Low Balance Dist Recs						
			//$lowBalDistRecs = $homeObj->getNegativeCreditLimitRecs(mysqlDateFormat($dateFrom), mysqlDateFormat($dateTill));
			//$lowBalDistRecSize = sizeof($lowBalDistRecs);
			//$chqDepositedDist = $homeObj->chqDepositedDistributor($yearMonth);

			$distACRecs =  $homeObj->getDistAccountRecs(mysqlDateFormat($dateFrom), mysqlDateFormat($dateTill), mysqlDateFormat($pendingChqTillDate));
			//usort($distACRecs, 'cmp_name');
			$distACRecSize = sizeof($distACRecs);
			//printr($distACRecs);

			if ($distACRecSize>0) {
	?>
	<table cellpadding="0"  cellspacing="0" border="0" align="left"  width="70%">
					<tr>
						<td>
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bgg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bgg.gif" class="pageName" style="font-size:11px; line-height:normal;"  nowrap>&nbsp;&nbsp;Distributor Account Details</td>
								</tr>
								<tr>
									<td width="1"></td>
		<td colspan="2">
		<table>
		    <TR><TD valign="top">
		<table cellpadding="0"  width="50%" cellspacing="0" border="0" align="center" id="gradient-style">
		<?php
			if ($distACRecSize>0) {
				$i = 0;
		?>
			<thead>
			<!--<tr align="center">
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;" onMouseover="ShowTip('Current month pending cheque details.');" onMouseout="UnTip();" colspan="2">Pending Cheques</th>
			</tr>-->
			<tr align="center">
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Distributor</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Credit Balance</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Pending Cheques</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">Overdue Amt</th>
			</tr>
			</thead>			
			<tbody>
			<?php
				$totOverdueAmt = 0;
				foreach ($distACRecs as $distributorId=>$dar) {
					$distributorName	= $dar[0];
					$creditBalanceAmt	= $dar[1];
					$pendingAmt 		= $dar[2];
					$showPmnt		= $dar[3];
					$overDueAmt		= $dar[4];
					$overDueInvoices	= $dar[5];
					
					$displayPendingChqs = "";
					if ($pendingAmt!=0) {
						$displayPendingChqs = "<span onMouseover=\"ShowTip('$showPmnt');\" onMouseout=\"UnTip();\"><a href='DistributorAccount.php?selectFrom=$dateFrom&selectTill=$pendingChqTillDate&distributorFilter=$distributorId&filterType=PE' class='home-link5'>".number_format($pendingAmt,2,'.',',')."</a></span>";
					}

					$displayOverdueInv = "";
					if ($overDueAmt!=0) {
						$totOverdueAmt += $overDueAmt;
						//if ((float)$overDueAmt>(float)$overdueDisplayLimit) 
						$displayOverdueInv = "<span onMouseover=\"ShowTip('$overDueInvoices');\" onMouseout=\"UnTip();\"><a href='DistributorReport.php?dateFrom=$dateFrom&dateTill=$dateTill&selDistributor=$distributorId&distOverdue=1&cmdSearch=1' class='home-link5'>".number_format($overDueAmt,2,'.',',')."</a></span>";
					}
			?>
			<tr>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left">
					<a href="DistributorAccount.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>&distributorFilter=<?=$distributorId?>&filterType=VE" class="home-link1" title="Click here to view details">
						<?=$distributorName?>
					</a>
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
					<?//=($creditBalanceAmt!=0 && $crBalDisplayLimit==0)?number_format($creditBalanceAmt,2,'.',','):($creditBalanceAmt>$crBalDisplayLimit)?number_format($creditBalanceAmt,2,'.',','):"";?>
					<?=($creditBalanceAmt!=0)?number_format($creditBalanceAmt,2,'.',','):"";?>
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
					<?=$displayPendingChqs?>
				</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">
					<?=$displayOverdueInv?>
				</td>
			</tr>			
			<?php
				}
			?>	
			</tbody>
	<tfoot>
		<tr>
			<td colspan="3" class="listing-head" align="right" style="color:#004080;">Total:</td>
			<td colspan="3" class="home-listing-item" align="right" style="color:#353535;">
				<strong><?=number_format($totOverdueAmt,2,'.',',');?></strong>
			</td>
		</tr>
	</tfoot>
			<?php
				} 
			?>
				</table>
		</TD>
		</TR>
	</table>
	</td>
	</tr>
	<tr>
		<td colspan="3" height="5" ></td>
	</tr>
	</table>
		</td>
		</tr>
	</table>
	<?php
		 } // Size check ends
	} // User rights ends here
	?>
	</td>	
	</TR>
	</table>
</td>
	</tr>
<!-- Second Row Ends here -->
<?php if($accesscontrolObj->homeAccessInv($roleId))$acessInv=true;
//echo "the value of $acessInv";
if ($acessInv=="true"){
?>
<tr><td>&nbsp;
<table><tr><td  colspan="2" background="images/heading_bgh.gif" class="pageName" style="font-size:11px; line-height:normal;" >Stock items with less than reorder level</td></tr></table>


</td><td>&nbsp;</td></tr>

<tr><td>&nbsp;
<table cellpadding="0"  width="50%" cellspacing="0" border="0" align="center" id="gradient-style">
<thead>
<tr><th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">&nbsp;Name</th><th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">&nbsp;Quantity</th>
<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">&nbsp;ReOrder Level</th><th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px;">&nbsp;Unit</th>

</tr>
<thead>
<tbody>
<?php
foreach($dashBoardStock as $dBS){
	?>

<tr>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?php echo $dBS[0];?>&nbsp;</td><td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?php echo $dBS[1];?>&nbsp;</td>
				<td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?php echo $dBS[2];?>&nbsp;</td><td class="home-listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?php echo $dBS[4];?>&nbsp;</td>
			</tr>
			
			<?php }?>

</tbody>



</table>


</td><td>&nbsp;</td></tr>

<?php }?>





	<tr>
		<td height="10"></td>
	</tr>
	<tr><td height="10" align="center"></td></tr>
	</table>


	
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>