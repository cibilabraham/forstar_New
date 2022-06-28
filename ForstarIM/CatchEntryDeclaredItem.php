<?php 
	require("include/include.php");
	ob_start();

//die();
	$err			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$supplierChallanNo	=	"";
	$supplierChallanDate	=	"";
	$declWeight		=	"";			
	$declCount		=	"";
	$declIce		=	"";
	
	if ($p["subSupplier"]!="") $subSupplier	= $p["subSupplier"];	

	if ($p["cmdCancel"]!="") {
		$subSupplier = "";
		$editMode = false;
		$addMode = false;
	}	
	
	if ($p["cmdAdd"]!="") {
		$currentId		=	$p["curEntryId"];
		$supplierChallanNo	=	$p["supplierChallanNo"];
		$Date1			=	explode("/",$p["supplierChallanDate"]);
		$supplierChallanDate	=	$Date1[2]."-".$Date1[1]."-".$Date1[0];
		$declWeight		=	$p["declWeight"];			
		$declCount		=	$p["declCount"];
		$declIce		=	$p["declIce"];
		$subSupplier		=	$p["subSupplier"];
		
		if ($currentId!="" && $declWeight!="" && $declCount!="") {
			$declaredRecIns=$dailycatchentryObj->addDeclaredDetails($currentId,$supplierChallanNo, $supplierChallanDate, $declWeight, $declCount, $declIce, $subSupplier);
		}
		
		if ($declaredRecIns!="") {
			$supplierChallanNo	=	"";
			$supplierChallanDate	=	"";
			$declWeight		=	"";			
			$declCount		=	"";
			$declIce		=	"";
			//$addMode	=	true;
			/*$sessObj->createSession("displayMsg",$msg_succAddDailyGrossWt);
			$sessObj->createSession("nextPage",$url_afterAddDailyGrossWt);*/
		} else {
			//$addMode	=	true;
			$err		=	$msg_failAddDeclared;
		}
		$declaredRecIns		=	false;
	}
	
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$declaredRec		=	$dailycatchentryObj->findDeclaredRec($editId);
		$editDeclRecordId	=	$declaredRec[0];
		$supplierChallanNo	=	$declaredRec[1];
		$eDate			=	explode("-",$declaredRec[2]);
		$supplierChallanDate	=	$eDate[2]."/".$eDate[1]."/".$eDate[0];	
		$declWeight		=	$declaredRec[3];			
		$declCount		=	$declaredRec[4];
		$declIce		=	$declaredRec[5];

		if ($p["editSelectionChange"]=='1') {
			$subSupplier	= $declaredRec[6];	
		} 
		/*else {
			$subSupplier	= $p["subSupplier"];	
		}
		*/
	}
	
	
	if ($p["cmdSaveChange"]!="") {
			$declaredRecordId 	= $p["hidDeclRecordId"];
			$supplierChallanNo	= $p["supplierChallanNo"];
			$Date1			= explode("/",$p["supplierChallanDate"]);
			$supplierChallanDate	= $Date1[2]."-".$Date1[1]."-".$Date1[0];
			$declWeight		= $p["declWeight"];			
			$declCount		= $p["declCount"];
			$declIce		= $p["declIce"];
			$subSupplier		= $p["subSupplier"];		
	
			if ($declaredRecordId!="") {
				$declaredUpdateRec = $dailycatchentryObj->updateDeclaredRecord($declaredRecordId, $supplierChallanNo, $supplierChallanDate, $declWeight, $declCount, $declIce, $subSupplier);	
			}
	
			if ($declaredUpdateRec) {
				$editMode=false;
				$supplierChallanNo		=	"";
				$supplierChallanDate	=	"";
				$declWeight				=	"";			
				$declCount				=	"";
				$declIce				=	"";
				//$sessObj->createSession("displayMsg",$msg_succUpdateQuality);
				//$sessObj->createSession("nextPage",$url_afterDelProcessor);
			} else {
				$err	=	$msg_failUpdateQuality;
			}
			$declaredUpdateRec	=	false;
		}
	
	
	if ($p["cmdDelete"]!="") {
	
			$rowCount	=	$p["hidRowCount"];
			for ($i=1; $i<=$rowCount; $i++) {
				$declRecordId	=	$p["delId_".$i];	
				if ($declRecordId!="") {
					$declaredRecDel	= $dailycatchentryObj->deleteDeclaredRec($declRecordId);	
				}	
			}
			/*if($processorRecDel) {
				$sessObj->createSession("displayMsg",$msg_succDelProcessor);
				$sessObj->createSession("nextPage",$url_afterDelProcessor);
			} else {
				$errDel	=	$msg_failDelProcessor;
			}
			$processorRecDel	=	false;*/
		}

	if ($p["mainSupplier"]=="") 	$mainSupplier	= $g["mainSupplier"];
	else				$mainSupplier	= $p["mainSupplier"];
	
	if ($p["landingCenter"]=="") 	$landingCenter	= $g["landingCenter"];
	else				$landingCenter	= $p["landingCenter"];
	

	# Get Sub Supplier Records
	$subSupplierRecords = array();	
	if ($mainSupplier!="" && $landingCenter!="") {
		$subSupplierRecords = $subsupplierObj->filterSubSupplierRecords($mainSupplier, $landingCenter);
	}
		
	#fetch all quality records
	if ($p["curEntryId"]=="") 	$currentId	= $g["entryId"];
	else						$currentId	= $p["curEntryId"];
	
	//echo "$currentId,$mainSupplier,$landingCenter";	
	
	if ($currentId!="") $declaredRecords	= $dailycatchentryObj->fetchAllDeclaredRecords($currentId);
	$declaredRecordsize	= sizeof($declaredRecords);
	
	//echo $_REQUEST["action"];
	if($_REQUEST["action"]=="displayMsg")
	{
		$data =$_POST["myData"];
		//echo "hhh".$data;
		$rec=json_decode($data);
		$subSupplier=$rec->SubSupplier;
		$supplierChallanNo=$rec->SupplierChallanNo;
		$duplicateChallan = $dailycatchentryObj->chkChallanNo($supplierChallanNo,$subSupplier);
		//echo "hiii".$duplicateChallan;
		if($duplicateChallan)
		{
			echo "Challan no already exist";
		}
		else
		{
			echo "";
		}
		//echo $msg;
		//header('content-type:application/json');
		exit;
	}
	//echo "hii".$_REQUEST["action"];
	//echo $data=$_POST["data"];
	
?>

<html>
	<head>
		<TITLE></TITLE>
		<link href="libjs/style.css" rel="stylesheet" type="text/css">
		<?php
		#Server Date
		 $serverDate = strtotime("now");
		?>
		
		<script language="javascript"> var servertimeOBJ=new Date(<?=$serverDate?>*1000);</script>
		<script language="JavaScript" type="text/JavaScript" src="libjs/dailycatchentry.js"></script>
		<script type="text/javascript" src="libjs/generalFunctions.js"></script>
		<link href="libjs/calendar-win2k-cold-1.css" type=text/css rel=stylesheet>
		<SCRIPT src="libjs/calendar.js" type=text/javascript></SCRIPT>
		<SCRIPT src="libjs/calendar-en.js" type=text/javascript></SCRIPT>
		<SCRIPT src="libjs/calendar-setup_3.js" type=text/javascript></SCRIPT>
		<script src="libjs/jquery/jquery-1.3.2.min.js" type="text/javascript"></script>
		<script>
		function chkChallanStat()
		{
			//alert("hii");
			var supplier=$("#subSupplier").val();
			if(supplier=="")
			{
				supplier=parent.document.getElementById("mainSupplier").value;
			}
			var supplierChallanNo=$("#supplierChallanNo").val();
			if(supplier!="" && supplierChallanNo!="")
			{
				var datas={"SubSupplier":supplier,"SupplierChallanNo":supplierChallanNo};
				$.ajax({
					type:"POST",
					//dataType:'json',// if specifies datatype then sucess data will be in the json array format $response_array['status'] = 'success'; and also header('Content-type: application/json');
					url:"CatchEntryDeclaredItem.php?action=displayMsg",
					data:{myData:JSON.stringify(datas)},
					success:function(data)
					{
						$('#showStatus').html(data);
						if(data=="")
						{
							$("#cmdAdd").attr('disabled', false);
						}
						else
						{
							$("#cmdAdd").attr('disabled', true);
						}
					}
			  });
			}
		}
		</script>
	</head>
	<body marginheight="0" marginwidth="0" bgcolor="#e8edff">
		<form name="frmDeclaredEntry" action="CatchEntryDeclaredItem.php" method="post">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" id="newspaper-dce-decl">
				<tbody>
					<? if($err!="" ){?>
					<tr>
						<td colspan="3" align="center" class="err1" ><?=$err;?></td>
					</tr>
					<?}?>
					<tr>
						<td colspan="3" align="center">
							<table width="100%" border="0" id="newspaper-dce-decl" align="center">
								<tbody>
									<tr><td id="supChallan" colspan="4"></td></tr>
									<tr>
										<td class="fieldName1" nowrap="true">Sub Supp</td>
										<td colspan="4">			
											<select name="subSupplier" id="subSupplier">
												<option value="">SELF</option>
												<?php
												 foreach ($subSupplierRecords as $fr) {
													$subSupplierId		= $fr[0];
													$subSupplierName	= stripSlash($fr[1]);				
													$selected		= "";
													if ($subSupplierId==$subSupplier) $selected	= "selected";
												?>
												<option value="<?=$subSupplierId?>" <?=$selected?>> <?=$subSupplierName?> </option>
												<? }?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="fieldName1" style="line-height:normal" nowrap>Sup. <br/>Challan No</td>
										<td><input name="supplierChallanNo" type="text" id="supplierChallanNo" size="6" value="<?=$supplierChallanNo?>" autocomplete="off"  onkeyup="chkChallanStat();" /></td>
										<!--<td>&nbsp;</td>-->
										<td class="fieldName1">Date</td>
										<td>
										<? if($editMode==false) $supplierChallanDate = date("d/m/Y");?>
										<input type="text" id="supplierChallanDate" name="supplierChallanDate" size="7" value="<?=$supplierChallanDate?>" autocomplete="off" /></td>
									</tr>
									<tr>
										<td id="showStatus" style="color:red; font-size:11px" colspan="4"></td>
									</tr>
									<tr>
										<td class="fieldName1" style="line-height:normal">Weight</td>
										<td><input name="declWeight" id="declWeight" type="text" size="4" style=" text-align:right" value="<?=$declWeight?>" autocomplete="off" /></td>
										<!--<td>&nbsp;</td>-->
										<td class="fieldName1">Count</td>
										<td><input name="declCount" id="declCount" type="text" size="4" value="<?=$declCount?>" autocomplete="off" /></td>
									</tr>
									<tr>
										<td class="fieldName1" style="line-height:normal">Ice</td>
										<td nowrap="nowrap" class="listing-item">
										<? if($editMode==false) $declIce=0; ?>
										<input name="declIce" type="text" id="declIce" style="text-align: right;" value="<?=$declIce?>" size="3" autocomplete="off">
										Kg</td>
										<td colspan="2">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="5" align="center"><input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('CatchEntryDeclaredItem.php');" />&nbsp;
										<? if($editMode==false){?><input name="cmdAdd" type="submit" class=button id="cmdAdd" value="Add" onclick="return validateDeclaredEntry(document.frmDeclaredEntry);"><? }?> <? if($editMode==true){?><input type="submit" name="cmdSaveChange" class="button" value=" Save Changes "  onclick="return validateDeclaredEntry(document.frmDeclaredEntry);"><? }?>
										<input type="hidden" name="curEntryId" value="<?=$currentId?>">
										<input type="hidden" name="mainSupplier" value="<?=$mainSupplier?>">
										<input type="hidden" name="landingCenter" value="<?=$landingCenter?>">	
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<?php
					if (sizeof($declaredRecords)) {
						$i	=	0;
					?>
					<tr><TD height="5"></TD></tr>	
					<tr>
						<td colspan="3">
							<table width="100%" border="0" cellpadding="0" cellspacing="1" id="newspaper-b1">
								<thead>
									<tr align="center">
										<th nowrap><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
										<th style="padding-left:1px; padding-right:1px;">Sub-Sup.</th>
										<th style="padding-left:1px; padding-right:1px;">Chall No </th>
										<th nowrap style="padding-left:1px; padding-right:1px;">Date</th>
										<th nowrap style="padding-left:1px; padding-right:1px;">Wt</th>
										<th nowrap style="padding-left:1px; padding-right:1px;">Count</th>
										<th nowrap style="padding-left:1px; padding-right:1px;">Ice</th>
										<th nowrap align="center">&nbsp;</th>
									</tr>
								</thead>
								<tbody>
								<?
								$totalDeclWt = 0;
								foreach($declaredRecords as $dr) {
									$i++;
									$declRecordId		=	$dr[0];
									$supplierChallanNo	=	$dr[1];
									$eDate			=	explode("-",$dr[2]);
									$supplierChallanDate	=	$eDate[2]."/".$eDate[1]."/".$eDate[0];	
									$declWeight		=	$dr[3];	
									$totalDeclWt 		+=	$declWeight;		
									$declCount		=	$dr[4];
									$declIce		=	$dr[5];

									$selSubSupplierId	= $dr[6];
									$subSupplierName = "";
									if ($selSubSupplierId!=0) {
										$subsupplierRec		= $subsupplierObj->find($selSubSupplierId);
										$subSupplierName	= stripSlash($subsupplierRec[2]);
									} else $subSupplierName = "SELF";			
								?>
									<tr>
										<td class="listing-item" nowrap align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" class="chkBox" value="<?=$declRecordId;?>" ></td>
										<td class="listing-item" style="padding-left:1px; padding-right:1px;"><?=$subSupplierName?></td>
										<td class="listing-item" nowrap style="padding-left:1px; padding-right:1px;"><?=$supplierChallanNo?></td>
										<td class="listing-item" nowrap style="padding-left:1px; padding-right:1px;"><?=$supplierChallanDate?></td>
										<td class="listing-item" nowrap style="padding-left:1px; padding-right:1px;" align="right"><?=$declWeight?></td>
										<td class="listing-item" nowrap style="padding-left:1px; padding-right:1px;"><?=$declCount?></td>
										<td class="listing-item" nowrap style="padding-left:1px; padding-right:1px;"><?=$declIce?></td>
										<td class="listing-item" nowrap align="center" style="padding-left:1px; padding-right:1px;"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$declRecordId?>,'editId');assignValue(this.form,'1','editSelectionChange');"></td>
									</tr>     
									<? }?>
									  <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
									  <input type="hidden" name="editId" value="">
									  <input type="hidden" name="editSelectionChange" value="0">
									  <input type="hidden" name="hidDeclRecordId" value="<?=$editDeclRecordId?>" />
									<tr>
										<td colspan="4" align="right" class="listing-head">Total:</td>
										<td style="padding-left:1px; padding-right:1px;" class="listing-item" align="left" colspan="4">
										<strong><? echo number_format($totalDeclWt,2)?></strong>
										<input name="totalDeclWt" type="hidden" size="5" value="<?=$totalDeclWt?>" style="border:none; text-align:right">
										</td>
									<!--    <td align="center">&nbsp;</td>
										<td align="center">&nbsp;</td>
										<td align="center">&nbsp;</td>-->
									</tr>
									<tr bgcolor="White">
										<td colspan="8" align="center"><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$declaredRecordsize;?>);"></td>	  
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<? }?>
				</tbody>
			</table>
<script language="javascript">
function getValueEntryId()
{
	var curr_Id	= parent.document.frmDailyCatch.catchEntryNewId.value;
	document.frmDeclaredEntry.curEntryId.value	=	curr_Id;
}
getValueEntryId();
</script>

<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "supplierChallanDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplierChallanDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
<? 
	if( sizeof($declaredRecords)>0){
?>
<script language="javascript">
	parent.document.frmDailyCatch.totalDeclaredWt.value='<?=$totalDeclWt?>';
</script>
<? } else {?>
<script language="javascript">
	parent.document.frmDailyCatch.totalDeclaredWt.value='<?=$totalDeclWt?>';
</script>
<? }?>
		</form>
	</body>
</html>
<?php
$outputContents = ob_get_contents(); 
ob_end_clean();
echo $outputContents;
?>