<?
	require("include/include.php");
	require_once("lib/ProductBatchReport_ajax.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	true;

	$userId		=	$sessObj->getValue("userId");
	
	/*-----------  Checking Access Control Level  ----------------*/
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/


	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["cmdCancel"]!="") $addMode = false;



	#Setting the Values
	if ($p["selProduct"]!="") $selProduct = $p["selProduct"];
	if ($p["selBatch"]!="") $selBatch = $p["selBatch"];


	if ($addMode || $editMode) {
		#List all Product Records
		//$productMasterRecords = $productMasterObj->fetchAllRecords();
		$productMasterRecords = $productMasterObj->fetchAllRecordsActiveProductMaster();
	}

	if ($selProduct) {
		$getProductBatchRecords = $productBatchReportObj->getProductBatchRecords($selProduct);
		#Get ingredient Recs
		$productIngredientRecs = $productBatchReportObj->fetchAllIngredients($selBatch);
		list($productGmsPerPouch, $fixedQty, $pouchPerBatch, $startTime, $endTime, $phFactor, $foFactor, $created) = $productBatchReportObj->getProductBatchSummaryRec($selBatch);

		#Gms per Pouch
		$gravyGmsPerPouch = number_format(($productGmsPerPouch-$fixedQty),2,'.','');

		$selDate	=	dateFormat($created);
		#Find the Selected product
		$selProductName = $productMasterObj->getProductName($selProduct);
		#Find the batch No
		$selBatchNo = $productBatchObj->getBatchNo($selBatch);

		$startTime	=	explode("-", $startTime);
		$selStartTime = $startTime[0].":".$startTime[1]."&nbsp;".$startTime[2];

		$stopTime	=	explode("-", $endTime);
		$selStopTime = $stopTime[0].":".$stopTime[1]."&nbsp;".$stopTime[2];


	}

$ON_LOAD_SAJAX = "Y";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmProductBatchReport" action="ProductBatchReport.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="60%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Product Batch Report</td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="95%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
		<tr>
			<? if($print==true){?>
			<td  colspan="2" align="center"><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProductBatchReport.php?selBatch=<?=$selBatch?>&selProduct=<?=$selProduct?>',700,600);">&nbsp;&nbsp;							
			</td>
			<?}?>
		</tr>
		<input type="hidden" name="hidProductBatchId" value="<?=$editProductBatchId;?>">
		<tr>
			<td class="fieldName" nowrap >&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
											<tr>
											  <td colspan="2" nowrap class="fieldName" >
					<table width="200">
						<tr>
                                                  <td class="fieldName">*Product:</td>
                                                  <td class="listing-item">
						<!-- <select name="selProduct" id="selProduct" onchange="this.form.submit();">-->
						<select name="selProduct" id="selProduct" onchange="functionLoad(this)">
						<option value="">-- Select --</option>
						<?
						foreach ($productMasterRecords as $pmr) {
							$productId	=	$pmr[0];
							$productCode	=	$pmr[1];
							$productName	=	$pmr[2];
							$selected = "";
							if ($selProduct==$productId) $selected = "Selected";
						?>
						<option value="<?=$productId?>" <?=$selected?>><?=$productName?></option>
						  <? }?>
                                                  </select></td>
                                                </tr>
						<tr>
                                                  <td class="fieldName" nowrap>*Batch No: </td>
                                                  <td class="listing-item">
						 <select name="selBatch" id="selBatch" onchange="this.form.submit();">
						<option value="">-- Select --</option>
						<?
						foreach ($getProductBatchRecords as $pbr) {
							$batchId	=	$pbr[0];
							$batchNo	=	$pbr[1];
							$selected = "";
							if ($selBatch==$batchId) $selected = "Selected";
						?>
						<option value="<?=$batchId?>" <?=$selected?>><?=$batchNo?></option>
						  <? }?>
                                                  </select></td>
                                                </tr>

                                                                      <tr>
                                                                           <TD>
                                                                                <br>
                                                                           </TD>
                                                                           <TD>
                                                                           </TD>
                                                                      </tr>
                                                                 </table></td>
				  </tr>
			<?
			  if (sizeof($productIngredientRecs) > 0) {
				$i=0;
			  ?>
				<tr><TD height="10"></TD></tr>
					<tr><TD>
				<table width="500" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddItem">
			     <tr bgcolor="#FFFFFF" align="center">
				<td class="fieldName" style="padding-left:2px; padding-right:2px;" colspan="5" width="200">&nbsp;</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Code:</td>
				<td class="listing-item" style="padding-left:2px; padding-right:2px;"><?=$selBatchNo?></td>
                              </tr>
                                </table>
				</TD></tr>
				<tr><TD height="10"></TD></tr>
				<tr><TD>
				<table width="500" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddItem">
			     <tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" colspan="2">Product.</td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=$selProductName?></strong></td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Date:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$selDate?></td>
                              </tr>
				<tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" colspan="2">Net Wt (GM).</td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$productGmsPerPouch?>&nbsp;gm</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Start Time:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$selStartTime?></td>
                              </tr>
				<tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" colspan="2">Fixed Wt (GM).</td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap=""><?=$fixedQty?>&nbsp;gm</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">End Time:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$selStopTime?></td>
                              </tr>
				<tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" colspan="2">Gravy Wt (GM).</td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$gravyGmsPerPouch?>&nbsp;gm</td>
				<? if ($foFactor==0) {?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">No.of Bottles :</td>
				<? } else {?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">No.of Pouches:</td>
				<? }?>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><strong><?=$pouchPerBatch?></strong></td>
                              </tr>
				<tr bgcolor="#FFFFFF">
				<td class="fieldName" style="padding-left:5px; padding-right:5px;" colspan="2"></td>
                                <td class="listing-item" style="padding-left:5px; padding-right:5px;"></td>
				<? if ($foFactor==0) {?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">PH Value:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$phFactor?></td>
				<? } else {?>
				<td class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">F0 Value:</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$foFactor?></td>
				<? }?>
                              </tr>
                                </table>
				</TD></tr>
				<tr><TD height="10"></TD></tr>
				<tr>
				  <td colspan="2" nowrap>
			   <?
			 // if (sizeof($productIngredientRecs) > 0) {
			//	$i=0;
			  ?>
			<table width="500" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddItem">
			     <tr bgcolor="#f2f2f2" align="center">
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">No.</td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;">RM/Ingredients</td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;">Qty Used<br>(Before Cleaning)</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Yield(%)</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Purchase Price of RM</td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;">Cost of RM</td>
                              </tr>
				<?
				$totalIngredientCost = 0;
			 	foreach ($productIngredientRecs as $pir)	{
					$i++;
					$ingredientId	= $pir[2];
					$ingredientName = $pir[5];
					$quantity	= $pir[3];
					$lastPrice = $pir[4];
					$ingredientCost = $quantity*$lastPrice;
					$totalIngredientCost += $ingredientCost;
					
				?>
                                <tr bgcolor="#FFFFFF">
					<td class="listing-item" style="padding-left:2px; padding-right:2px;" align="center"><?=$i?></td>
                                	<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left"><?=$ingredientName?></td>
                                        <td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$quantity?></td>
					<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" nowrap><?=$quangtity?></td>
					<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$lastPrice?></td>
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right">	<?=$ingredientCost?></td>
                                </tr>
				<?
					 }
				?>
				
			     <tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;" align="center">Total Cost</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><strong><?=$totalIngredientCost?></strong></td>
                              </tr>
				<? 
				$costPerPouch = number_format(($totalIngredientCost/$pouchPerBatch),3,'.','');
				?>
				 <tr bgcolor="#FFFFFF">
				<td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
                                <td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;"></td>
				<td class="listing-head" style="padding-left:2px; padding-right:2px;" align="center">Cost/Pouch</td>
				<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><strong><?=$costPerPouch?></strong></td>
                              </tr>
                                </table>
			  <? }?>
			  </td>
			    </tr>
				<input type="hidden" name="hidItemCount" id="hidItemCount" value="<?=$i;?>">

				
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
												<? if($print==true){?>

												<td  colspan="2" align="center">

<input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintProductBatchReport.php?selBatch=<?=$selBatch?>&selProduct=<?=$selProduct?>',700,600);">										</td>
												<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				</td>
		</tr>
		<?
			}
		?>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td></td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
	</table>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
<script language="javascript">
function functionLoad(formObj)
	{
		//alert("hai");
		showFnLoading(); 
		formObj.form.submit();
	}


</script>