<?php 
	require("include/include.php");
	require_once('lib/Report_ajax.php');
	
	
	$companyName = $p['companyName'];
	$reportName  = $p['reportName'];
	$transcationName = $p['transcationName'];
	$reportField = $p['reportField'];
	$err			=	"";
	$errDel			=	"";
	//$editMode		=	false;
	//$addMode		=	false;
	//$selStockId		=	"";
	$userId		=	$sessObj->getValue("userId");
	//$genReqNumber	= "";

	//$selection = "?pageNo=".$p["pageNo"]."&selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"];

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	/*-----------  Checking Access Control Level  ----------------*/

	// $add	=false;
	// $edit	=false;
	// $del	=false;
	// $print	=false;
	// $confirm=false;
	
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
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/
	
	
	
	
	

	
	
	
	# List all records
	//$departmentRecords	= $departmentObj->fetchAllRecordsActivedept();
	//$rmLotId	= $unitTransferObj->fetchAllRecords();
	$comapanyDetails	= $billingCompanyObj->fetchAllRecords();
	$transcationNameRecords	= Array('1'=>'PHT Certificate','2'=>'PHT Monitoring','3'=>'RM Procurment order','4'=>'RM TestData','5'=>'RM Receipt Gate Pass','6'=>'Soaking','7'=>'Unit Transfer','8'=>'Weightment Data Sheet','9'=>'RM Weightment After Grading','10'=>'Pre Processing',
	'11'=>'Frozen Packing','12'=>'','13'=>'','14'=>'','15'=>'','16'=>'','17'=>'','18'=>'','19'=>'','20'=>'',
	'21'=>'','22'=>'','23'=>'','24'=>'','25'=>'','26'=>'','27'=>'','28'=>'','29'=>'','30'=>'',
	'31'=>'','32'=>'','33'=>'','34'=>'','35'=>'','36'=>'','37'=>'','38'=>'','39'=>'','40'=>'');
	//$reportObj->fetchAllProcurmentMenus();
	
	
	
	//if ($editMode) $heading	=	$label_editSoaking;
	//else $heading	=	$label_addSoaking;
		
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/Report.js"; // For Printing JS in Head section

	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmReport" action="Report.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											
											<input type="hidden" name="hidSoakingId" value="<?=$editsoakngDataId;?>">
											
										
											<tr>
											  <td colspan="2" nowrap class="fieldName" >
			<table width="200" align="center">
					
					
							<tr>
								   <td class="fieldName" nowrap>*Company Name:</td>
								 
												<td  height="10" ><select name="companyName" id="companyName" >
											  <option value="">--select--</option>
											  <?
												foreach($comapanyDetails as $un)
													{
														$companyId		=	$un[0];
														$comapanyNamee	=	stripSlash($un[1]);
															$selected = ($companyName==$companyId)?"selected":""
														
											?>
											  <option value="<?=$companyId?>" <?=$selected?>><?=$comapanyNamee?></option>
											  <? }?>
										        </select>										      </td>
								</tr>
								
								<tr>
                                	<td class="fieldName" nowrap>*Report Name:&nbsp;</td>
                                     <td><INPUT TYPE="text" NAME="reportName" id="reportName" size="15" value="<?=$reportName?>"></td>
										
							   </tr>
							
							<tr>
							   <td class="fieldName" nowrap>*Transaction Name:</td>
							   <td  height="10" ><select name="transcationName" id="transcationName" onchange="xajax_getField(document.getElementById('transcationName').value,'');">
											  <option value="">--select--</option>
											  <?
												foreach($transcationNameRecords as $transcationId => $transactionName)
													{
														// $transcationId		=	$un[0];
														// $transactionName	=	stripSlash($un[1]);
															$selected = '';
															if($transcationName==$transcationId) $selected = 'selected="selected"';
														
											?>
											  <option value="<?=$transcationId?>" <?=$selected?>><?=$transactionName?></option>
											  <? }?>
										        </select>										      </td>
							   
							</tr>
												
						  
							  <tr>
                                	<td class="fieldName" align='right'>*Report  Field:&nbsp;</td>
                                         <td  height="10" ><select name="reportField[]"  id="reportField" multiple style="width:150px" >
											 
											  
										        </select>										      </td>
                                                </tr>
												
							 
												
							
                                              </table></td>
					  </tr>
					<tr>
					  <td colspan="2">&nbsp;</td>
					</tr>					
	

	
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
	

												<td colspan="2" align="center">
												<input type="submit" name="search" class="button" value=" Search" onClick="return validateReport(document.frmReport);">&nbsp;&nbsp;
																						</td>
												
												
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	

		
				
		
		<tr>
			<td height="10"></td>
		</tr>
		<?php
		
		if ($p["search"]!="") {
	//$view=true;
	$reportName=$p["reportName"];
	$companyName=$p["companyName"];
	$company=$reportObj->findCompany($companyName);
	$transcationName=$p["transcationName"];
	$reportField=$p["reportField"];
	$cnt=count($reportField);
		for($i=0; $i<$cnt; $i++)
		{
		if($i==0)
		{
		$value=$reportField[$i];
		}
		else
		{
		$value=$value.','.$reportField[$i];
		}
		 $fld=$value;
		}
	switch($transcationName)
	{
		case 1:
		{
		$qry="SELECT $fld
			FROM t_phtcertificate a
			LEFT JOIN m_fishcategory b ON ( a.species = b.id ) 
			LEFT JOIN m_supplier_group c ON ( a.supplier_group = c.id ) 
			LEFT JOIN supplier d ON ( a.supplier = d.id ) 
			LEFT JOIN m_pond_master e ON ( a.pond_Name = e.id ) ";
			//$query="select $fld from t_phtCertificate";
			$query	=$reportObj->getQuery($qry);
			$fieldName=Array('a.PHTCertificateNo'=>'PHT Certificate No','b.category'=>'Species','c.supplier_group_name'=>'Supplier group','d.name'=>'Supplier','e.pond_name'=>'Pond Name','e.pond_qty'=>'PHT Quantity',' a.date_of_issue'=>'Date of Issue','a.date_of_expiry'=>'Date of expiry','a.received_date'=>'Received date');
		break;
		}
		case 3:
		{
		$idField = 0;
			$cnt=count($reportField);
			$fld = '';
			for($i=0; $i<$cnt; $i++)
			{
			
			// echo $reportField[$i];
				if($reportField[$i] != 'driver_Name' && $reportField[$i] != 'vehicle_No' && $reportField[$i] != 'equipment_Name' &&
				   $reportField[$i] != 'max_equipment' && $reportField[$i] != 'equipment_issued' && $reportField[$i] != 'difference'
				   && $reportField[$i] != 'chemical' && $reportField[$i] != 'chemical_required' && $reportField[$i] != 'chemical_issued')
				{
					if($fld=='')
					{
						$fld = $reportField[$i];
					}
					else
					{
						$fld.= ','.$reportField[$i];
					}
					$idField++;
				}
			}
		if($fld == '') { $fld = 'a.id';}
			else { $fld = $fld.',a.id'; }
		
		$fieldName=Array('a.gatePass'=>'Procurment Gate Pass number','b.name'=>'Company','c.supplier_group_name'=>'Suppler Group Name','d.name'=>'Supplier Name','a.supplier_address'=>'Supplier address','e.pond_name'=>'Pond Name','a.pond_address'=>'Pond Address','a.date_of_entry'=>'Date Of Entry','driver_Name'=>'Driver Name','vehicle_No'=>'Vehicle Number','equipment_Name'=>'Equipment Name','max_equipment'=>'Maximum Equipment','equipment_issued'=>'Equipment Issued','difference'=>'Difference','chemical'=>'Chemical Name','chemical_required'=>'Chemical Required','chemical_issued'=>'Chemical Issued');
			//echo $value;
			//$qry="select $fld from t_rmprocurmentorder a left join m_companydetails b on b.id=a.company left join m_supplier_group c on c.id=a.suppler_group_name left join supplier d on d.id=a.supplier_name left join m_pond_master e on e.id=a.pond_name ";
			$qry="select $fld
			from t_rmprocurmentorder a 
			left join m_companydetails b on b.id=a.company 
			left join m_supplier_group c on c.id=a.suppler_group_name 
			left join supplier d on d.id=a.supplier_name 
			left join m_pond_master e on e.id=a.pond_name ";
			$query=$reportObj->getQuery($qry);
			$mulQuery = $query;
			// $v = 0;
			$a = array_flip($p['reportField']);
			if(isset($a['driver_Name']))
			{
				$replaceVal = $a['driver_Name'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						$temArray = array();
						foreach($mulQuery as  $qr)
						{							
							//$id = $qr[8];
							$id = $qr[$idField];
							$driverName = $reportObj->getDriverName($id);
							
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $driverName;
							$v++;
						}
						break;
					}
				}
			}
			
			
			
			if(isset($a['vehicle_No']))
			{
				$replaceVal = $a['vehicle_No'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						$temArray = array();
						foreach($mulQuery as  $qr)
						{							
							//$id = $qr[8];
							$id = $qr[$idField];
							$vehicleNo = $reportObj->getVehicleNumber($id);
							
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $vehicleNo;
							$v++;
						}
						break;
					}
				}
			}
			
			if(isset($a['equipment_Name']))
			{
				$replaceVal = $a['equipment_Name'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						$temArray = array();
						foreach($mulQuery as  $qr)
						{							
							//$id = $qr[8];
							$id = $qr[$idField];
							$equipmentName = $reportObj->getEquipmentName($id);
							
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $equipmentName;
							$v++;
						}
						break;
					}
				}
			}
			
			
			
			if(isset($a['max_equipment']))
			{
				$replaceVal = $a['max_equipment'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						$temArray = array();
						foreach($mulQuery as  $qr)
						{							
							//$id = $qr[8];
							$id = $qr[$idField];
							$equipmentValues = $reportObj->getMaxEquipment($id);
							
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $equipmentValues;
							$v++;
						}
						break;
					}
				}
			}
			
			if(isset($a['equipment_issued']))
			{
				$replaceVal = $a['equipment_issued'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						$temArray = array();
						foreach($mulQuery as  $qr)
						{							
							//$id = $qr[8];
							$id = $qr[$idField];
							$equipmentIssued = $reportObj->getEquipmentIssued($id);
							
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $equipmentIssued;
							$v++;
						}
						break;
					}
				}
			}
			
			if(isset($a['difference']))
			{
				$replaceVal = $a['difference'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						$temArray = array();
						foreach($mulQuery as  $qr)
						{							
							//$id = $qr[8];
							$id = $qr[$idField];
							$diff = $reportObj->getDifference($id);
							
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $diff;
							$v++;
						}
						break;
					}
				}
			}
			
			
			if(isset($a['chemical']))
			{
				$replaceVal = $a['chemical'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						$temArray = array();
						foreach($mulQuery as  $qr)
						{							
							//$id = $qr[8];
							$id = $qr[$idField];
							$chemicalName = $reportObj->getChemicalName($id);
							
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $chemicalName;
							$v++;
						}
						break;
					}
				}
			}
			
			
			
			
			
			if(isset($a['chemical_required']))
			{
				$replaceVal = $a['chemical_required'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						$temArray = array();
						foreach($mulQuery as  $qr)
						{							
							//$id = $qr[8];
							$id = $qr[$idField];
							$chemicalReq = $reportObj->getChemicalReq($id);
							
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $chemicalReq;
							$v++;
						}
						break;
					}
				}
			}
			
			if(isset($a['chemical_issued']))
			{
				$replaceVal = $a['chemical_issued'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						$temArray = array();
						foreach($mulQuery as  $qr)
						{							
							//$id = $qr[8];
							$id = $qr[$idField];
							$chemicalIssued = $reportObj->getChemicalIssued($id);
							
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $chemicalIssued;
							$v++;
						}
						break;
					}
				}
			}
			
			if(isset($a['chemical_issued']))
			{
				$replaceVal = $a['chemical_issued'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						$temArray = array();
						foreach($mulQuery as  $qr)
						{							
							//$id = $qr[8];
							$id = $qr[$idField];
							$chemicalIssued = $reportObj->getChemicalIssued($id);
							
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $chemicalIssued;
							$v++;
						}
						break;
					}
				}
			}
			
			
			
			break;
			
		}
	
		case 4:
		{
			$fieldName=Array('b.name'=>'Unit Name','c.new_lot_Id'=>'Lot id','d.test_name'=>'Test name','a.test_method'=>'Test Method','date_of_testing'=>'Date of Testing','result'=>'Result');
			//echo $value;
			$qry="select $fld from t_rmtestdata a left join m_plant b on b.id=a.unit left join t_unittransfer c on c.id=a.lot left join m_rmtest_master d on d.id=a.test_name ";
			$query=$reportObj->getQuery($qry);
			//echo $query[0];
			//return $this->databaseConnect->getRecords($qry);
			
			//$field=Array('unit'=>'Unit Name','lot'=>'Lot id','test_name'=>'Test name','test_method'=>'Test Method','date_of_testing'=>'Date of Testing','result'=>'Result');
			break;
		}
		case 5:
		{	
			
			$fieldName=Array('b.process_type'=>'Process Type','a.lot_Id'=>'Lot Id','c.gatePass'=>'Procurment Gate Pass Number','d.vehicle_number'=>'Vehicle Number','e.name_of_person'=>'Driver Name','f.seal_number'=>'In Seal number','a.result'=>'Result','a.seal_No'=>'Seal numbers','g.seal_number'=>'Seals Returned','h.name'=>'Verified by','a.labours'=>'Labours','i.name'=>'Company Name','j.name'=>'Unit','a.supplier_Challan_No'=>'Supplier Challan No','a.supplier_Challan_Date'=>'Supplier Challan date','a.date_Of_Entry'=>'Date of entry');
			$qry="select $fld from t_rmreceiptgatepass a left join m_lotid_process_type b on b.id=a.process_type left join t_rmprocurmentorder c on c.id=a.procurment_Gate_PassId left join m_vehicle_master d on d.id=a.vehicle_Number left join m_driver_master e on e.id=a.driver left join m_seal_master f on f.id=a.in_Seal left join m_seal_master g on g.id=a.out_Seal left join m_employee_master h on h.id=a.verified left join m_companydetails i on i.id=a.Company_Name left join m_plant j on j.id=a.unit";
			$query=$reportObj->getQuery($qry);
			break;
		}
		case 6:
		{	
			$fieldName=Array('b.new_lot_Id'=>'Lot Id','c.process_type'=>'Processing Stage','a.supplier_Details'=>'Supplier Challan Number','a.available_Qty'=>'Available Quantity','a.soak_In_Count'=>'Soak In Count','a.soak_In_Qty'=>'Soak In quantity','a.soak_In_Time'=>'Soak in Time','a.soak_Out_Count'=>'Soak out count','a.soak_Out_Qty'=>'Soak Out quantity','a.soak_Out_Time'=>'Soak Out Time','a.temperature'=>'Temperature','a.gain'=>'Gain','d.chemical_name'=>'Chemical Used','a.chemcal_Qty'=>'Chemical Quantity');
			
			$qry="select $fld from t_soaking a left join t_unittransfer b on b.id=a.rm_lot_Id left join m_lotid_process_type c on c.id=a.processing_Stage left join m_harvesting_chemical_master d on d.id=a.chemcal_Used ";
			$query=$reportObj->getQuery($qry);
			break;
		}
	}
		
	
	
	
	
	
		?>
		<tr>
			<td align="center"> <h1><? echo $reportName; ?> </h1> </td>
		</tr>
		<tr>
			<td align="center"> <h2><? echo  $company[0]; ?></h2> </td>
		</tr>
		<tr>
			<td>
				<table width="99%" border="0" bgcolor="#999999" align="center" cellspacing="1" cellpadding="0" class="print">
              <tbody><tr bgcolor="#f2f2f2" align="center"> 
			 <?php  if ($reportField) {
			 
			 

			 foreach ($reportField as $rId) {
			
				if(isset($fieldName[$rId]))
				{
			//$reportId =	$transcationNameRecords[$rId];
			?>
                <th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head"><?php echo $fieldName[$rId]; ?> </th>
               <?php 	}}
		} ?>
			     </tr>
             
               
			  <?php
			  if(sizeof($query) > 0)
			  {
			  foreach($query as $fld)
			  {
			  ?><tr bgcolor="#FFFFFF"> 
			  <?php
			  for($i=0; $i<$cnt; $i++)
				{
			  ?>
                <td nowrap="" height="25" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php echo $fld[$i]; ?></td>
			<?php
			}
			}
			}
			else
			{
				 echo '<td nowrap="" colspan="'.sizeof($reportField).'" height="25"> No records found </td>';
			}
			?>
              
              </tr>
					
					
					
			  </tbody></table>
			</td>
		</tr>
		<?php
		}
		?>
	</table>


	
	
	
	

	
	</form>
	<script>
	$(document).ready(function(){
		var transcationName = $('#transcationName').val();
		if(transcationName != '')
		{
			xajax_getField(document.getElementById('transcationName').value,'');
		}
	});
	function afterLoadTrans()
	{
		var reportField = document.getElementsByName('reportField[]');
			for(i=0;i<reportField.length;i++)
			{
			<?php
				if(sizeof($reportField) > 0)
				{
					foreach($reportField as $report)
					{
			?>
					if(reportField[i].value == '<?php echo $report;?>')
					{
						reportField[i].selected = true;
					}
			<?php					
					}
				}
			?>
			}
	}
	</script>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>