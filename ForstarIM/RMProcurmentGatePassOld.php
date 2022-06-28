<?php
	require("include/include.php");
	require_once('lib/RMProcurmentOrder_ajax.php');
	$err			=	"";
	
	$procurmentGatePassId = '';
	$gate_pass_id         = '';
	$out_time             = '';
	$seal_no              = '';
	$out_seal_no          = '';
	$labours              = array();
	$supervisor           = '';
	
	if(isset($_GET['gate_pass_id']))
	{
		$gate_pass_id = base64_decode($_GET['gate_pass_id']);
		$checkGatePass = $rmProcurmentOrderObj->checkGatePass($gate_pass_id);
		if($checkGatePass == 0)
		{
			// $sessObj->createSession("displayMsg",'Procurement Gate pass does not exists ');
			// $sessObj->createSession("nextPage",'RMProcurmentOrder.php');
		}
		else
		{
			$gatePassDetails = $rmProcurmentOrderObj->getGatePassDetails($gate_pass_id);
			if(sizeof($gatePassDetails) > 0)
			{
				// print_r($gatePassDetails);
				$outTimeOrg = explode(' ',$gatePassDetails[2]);
				$outTimeVal = explode('-',$outTimeOrg[0]);
				// if($outTimeVal[1] < 10)
				// {
				$out_time   = $outTimeVal[2].'-'.(int)$outTimeVal[1].'-'.$outTimeVal[0].' '.$outTimeOrg[1];
				// }
				// $out_time    = $gatePassDetails[2];
				$seal_no     = $gatePassDetails[3];
				$labours     = explode(',',$gatePassDetails[5]);
				$supervisor  = $gatePassDetails[6];
				
			}
		}
	}
	else
	{
		// $sessObj->createSession("displayMsg",'Procurement Gate pass does not exists ');
		// $sessObj->createSession("nextPage",'RMProcurmentOrder.php');
	}
	if(isset($p['out_time']))
	{
		$out_time = $p['out_time'];
	}
	if(isset($p['seal_no']))
	{
		$seal_no = $p['seal_no'];
	}
	if(isset($p['labours']))
	{
		$labours = $p['labours'];
	}
	if(isset($p['supervisor']))
	{
		$supervisor = $p['supervisor'];
	}
	$sealNoList   = $rmProcurmentOrderObj->getAllSealNos($seal_no);
	$employeeList = $rmProcurmentOrderObj->getAllEmployee();
	
	if(isset($p['cmdGenerate']))
	{
		// print_r($p);die;
		$outTimeOrg = explode(' ',$p['out_time']);
		$outTimeVal = explode('-',$outTimeOrg[0]);
		if($outTimeVal[1] < 10)
		{
			$outTime   = $outTimeVal[2].'-0'.$outTimeVal[1].'-'.$outTimeVal[0].' '.$outTimeOrg[1];
		}
		else
		{
			$outTime   = $outTimeVal[2].'-'.$outTimeVal[1].'-'.$outTimeVal[0].' '.$outTimeOrg[1];
		}
		$laboursIns = implode(',',$p['labours']);
		$updateArray = array(  	'gate_pass_id' => $gate_pass_id,
								'out_time'     => $outTime,
								'seal_no'      => $p['seal_no'],
								'labours'      => $laboursIns,
								'supervisor'   => $p['supervisor']
								);
		$check = $rmProcurmentOrderObj->checkGatePassExsits($gate_pass_id);
		if($check)
		{
			$rmProcurmentOrderObj->updateGenerateGatePass($updateArray,$check);
		}
		else
		{
			$rmProcurmentOrderObj->generateGatePass($updateArray);
		}
		$sessObj->createSession("displayMsg",'Procurement Gate pass ID generate successfully');
		$sessObj->createSession("nextPage",'RMProcurmentOrder.php');
	}
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<script language="javascript" type="text/javascript" src="libjs/datetimepicker.js"></script>
	<form name="RMProcurmentGatePass" action="RMProcurmentGatePass.php?gate_pass_id=<?php echo $_GET['gate_pass_id'];?>" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
		<!-- Added for gate pass id generation -->
	
	<tr>
	<td class="listing-item" align="center" colspan="2">
		<table border="2" width="70%">
		<tbody><tr>
		<td>
			
			<table  width="100%">
			<tbody>
			
			<tr>
			<td width='60%' valign="top">
			<?php
			$left_l=true;
			$entryHead = "";
			$rbTopWidth = "";
			require("template/rbTop.php");
		?>
			<table align="left"  cellpadding="0" cellspacing="0" width="100%">
			
			
			<tr>
				<td nowrap="" class="fieldName_pgpi"> Gate Pass ID</td>
				<td>
					<input type="text" value="<?php echo $gate_pass_id;?>"  size="15" readonly="readonly">
				</td>
			</tr>
			<tr>
				<td nowrap="" class="fieldName_pgpi">* Out Time</td>
				<td>
					<input type="hidden" name="gate_pass_id" id="gate_pass_id" value="<?php echo $gate_pass_id;?>" />
					<input type="text" value="<?php echo $out_time;?>" required name="out_time" id="out_time" size="10" value="" autocomplete="off">
					<a nowrap="" href="javascript:NewCal('out_time','ddmmyyyy',true,24);">pick Date Time</a>
				</td>
			</tr>
			</table>
			<?php
			require("template/rbBottom.php");
		?>
			</td>
			<td width='40%' valign="top">
			<?php
			$left_l=true;
			$entryHead = "";
			$rbTopWidth = "";
			require("template/rbTop.php");
		?>
			<table align="left" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td nowrap="" class="fieldName_pgpi">Seal No[Out Seal]</td>
				<td>
					<?php
						if(sizeof($sealNoList) > 0)
						{
							echo '<select name="seal_no" id="seal_no" required>';
								foreach($sealNoList as $sealNo)
								{
									$sel = '';
									if($seal_no == $sealNo[0]) $sel = 'selected="selected"';
									echo '<option value="'.$sealNo[0].'" '.$sel.'>'.$sealNo[1].'</option>';
								}
							echo '</select>';
						}
						echo '<input type="hidden" name="editSealNo" id="editSealNo" value="'.$seal_no.'" />';
					?>
				</td>
			</tr>
			<tr>
			<td nowrap="" class="fieldName_pgpi">Other Seal No:</td>
			<td>
			<?php
						if(sizeof($sealNoList) > 0)
						{
							echo '<select name="seal_no" id="seal_no" required>';
								foreach($sealNoList as $sealNo)
								{
									$sel = '';
									if($seal_no == $sealNo[0]) $sel = 'selected="selected"';
									echo '<option value="'.$sealNo[0].'" '.$sel.'>'.$sealNo[1].'</option>';
								}
							echo '</select>';
						}
						echo '<input type="hidden" name="editSealNo" id="editSealNo" value="'.$seal_no.'" />';
					?>
			
			</td>
			</tr>
			
			</table>
			<?php
			require("template/rbBottom.php");
		?>
			</td>
			</tr>
			<tr colspan="2">
			<td width='50%' valign="top">
			<?php
			$left_l=true;
			$entryHead = "";
			$rbTopWidth = "";
			require("template/rbTop.php");
		?>
			
			<?php
				$i=1;
				if(sizeof($labours) > 0)
				{
					foreach($labours as $labour)
					{
			?>
						<div id="addRow<?php echo $i;?>">
							<table align="center" cellpadding="0" cellspacing="0" width="50%">
								<tbody>
									<tr>
										<td nowrap="" class="fieldName_pgpi"></td>
										<td>
											<input type="text" value="<?php echo $labour;?>" maxlength="10" size="10" id="labours" name="labours[]">
											<?php
												if($i != 1)
												{
											?>											
											<img border="0" title="Click here to remove this item" src="images/delIcon.gif" style="border:none;" onclick="deleteThisLabour('<?php echo $i;?>')">
											<?php
											}
											?>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
			<?php
						$i++;
					}
				}
				else
				{
			?>
			<div id="addRow<?php echo $i;?>">
				<table align="center" cellpadding="0" cellspacing="0" width="50%">
					<tr>
						<td nowrap="" class="fieldName_pgpi"> Labours </td>
						<td><input type="text" name="labours[]" id="labours" size="10" maxlength="10"></td>
					</tr>
				</table>
			</div>
			<?php
				}
			?>
			<div id="addRow<?php echo $i+1;?>"></div>
			
			</td>
			</tr>
			<tr>
			<td>
			<table>
			<tr>
				<td nowrap="" class="fieldName_pgpi"></td>
				<td>
					<a href="javascript:void(0);" id='addRow' onclick="javascript:addNewLabour();"  
					class="link1" title="Click here to add new item.">
						<img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >
						Add New
					</a>
					<input type="hidden" name="rowCountLab" value="<?php echo $i+1;?>" id="rowCountLab" />
				</td>
			</tr>
			<tr>
				<td nowrap="" class="fieldName_pgpi">Supervisor</td>
				<td>
					<?php
						if(sizeof($employeeList) > 0)
						{
							echo '<select id="supervisor" name="supervisor" required>';
								foreach($employeeList as $employee)
								{
									$sel = '';
									if($supervisor == $employee[0]) $sel = 'selected="selected"';
									echo '<option value="'.$employee[0].'" '.$sel.'>'.$employee[1].'</option>';
								}
							echo '</select>';
						}
					?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
				<?php
				if(sizeof($sealNoList) > 0)
				{
				?>
				<input type="submit" value="Generate" class="button"  name="cmdGenerate" />
				<?php
				}
				else
				{
					echo '<a href="SealNumber.php"> Add new Seal Number </a>';
				}
				?>
				</td>
			</tr>
			</table>
			<?php
			require("template/rbBottom.php");
		?>
			</td>
			</tr>
			
		</td>
		</tr>
		</tbody></table>
	</td>
         </tr>
	</table>
	</tr>
	</table>
	<SCRIPT LANGUAGE="JavaScript">
	
	function addNewLabour()
	{
		var rowCount = jQuery('#rowCountLab').val();
		// alert(rowCount);
		var newRow = parseInt(rowCount) + 1;
		htmlVal = '<table><tr><td nowrap="" class="fieldName_pgpi"></td>';
		htmlVal+= '<td><input type="text" name="labours[]" id="labours" size="10" maxlength="10">';
		htmlVal+= '<img onclick="deleteThisLabour('+rowCount+')" border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item">';
		htmlVal+= '</td></tr></table>';
		htmlAppend = '<div id="addRow'+newRow+'"></div>';
		// alert('addRow'+rowCount);
		jQuery('#addRow'+rowCount).html(htmlVal);
		jQuery(htmlAppend).insertAfter('#addRow'+rowCount);
		jQuery('#rowCountLab').val(newRow);
	}
	function deleteThisLabour(rowCount)
	{
		jQuery('#addRow'+rowCount).html('');
	}
	// Calendar.setup 
	// (	
		// {
			// inputField  : "out_time",         // ID of the input field
			// eventName	  : "click",	    // name of event
			// button : "out_time", 
			// ifFormat    : "%d/%m/%Y",    // the date format
			// singleClick : true,
			// step : 1
		// }
	// );
	//-->
	</SCRIPT>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>