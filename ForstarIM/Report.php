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
	'11'=>'Frozen Packing');
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
		case 2:
		{
		 $qry="SELECT $fld
			FROM t_phtmonitoring a
			LEFT JOIN t_unittransfer b ON ( a.lot_id = b.id ) 
			LEFT JOIN m_supplier_group c ON ( a.supplier_group = c.id ) 
			LEFT JOIN supplier d ON ( a.supplier = d.id ) 
			LEFT JOIN t_phtcertificate e ON ( a.pht_No = e.id ) ";
			//$query="select $fld from t_phtCertificate";
			$query	=$reportObj->getQuery($qry);
			$fieldName=Array('a.date_on'=>'Date','b.new_lot_Id'=>'RM Lot ID','d.name'=>'Supplier','c.supplier_group_name'=>'Supplier group','a.species'=>'Species','a.supply_qty'=>'Supply Qty','e.PHTCertificateNo'=>'PHT Certificate No','a.pht_Qty'=>'PHT Qty','a.set_off_Qty'=>'Set off Qty','a.balance_Qty'=>'Balance Qty');
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
		case 7:
		{				
			//CASE WHEN a.active = 1 THEN 'Confirm' ELSE 'Not Confirmed' END as active
			$fld = '';
			for($i=0; $i<$cnt; $i++)
			{
				if($reportField[$i] == 'a.active')
				{
					$activeField = "CASE WHEN a.active = 1 THEN 'Confirm' ELSE 'Not Confirmed' END as active";
					if($fld=='')
					{
						$fld = $activeField;
					}
					else
					{
						$fld.= ','.$activeField;
					}
				}
				else
				{
					if($fld=='')
					{
						$fld = $reportField[$i];
					}
					else
					{
						$fld.= ','.$reportField[$i];
					}
				}
			}
			$fieldName = array('a.new_lot_Id' => 'RM LOT ID','a.supplier_Details' => 'Supplier Details',
							   'b.name' => 'Current Unit','c.process_type' => 'Current Process Type',
							   'd.name' => 'Previous Unit','e.process_type' => 'Previous Process Unit',
							   'a.active' => 'Status');
			$qry = "select $fld from t_unittransfer a 
				left join m_plant b on a.current_Unit = b.id 
				left join m_lotid_process_type c on a.current_Stage = c.id 
				left join m_plant d on a.unit_Name = d.id 
				left join m_lotid_process_type e on a.process_Type = e.id ";
			$query=$reportObj->getQuery($qry);
			break;
		}
		case 8:
		{
			$idField = 0;
			$cnt=count($reportField);
			$fld = '';
			for($i=0; $i<$cnt; $i++)
			{
				// echo $reportField[$i];
				if($reportField[$i] != 'grade_count' && $reportField[$i] != 'count_code' && $reportField[$i] != 'weight' &&
				   $reportField[$i] != 'soft_precent' && $reportField[$i] != 'soft_weight' && $reportField[$i] != 'pkg_nos')
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
			$fieldName=Array('i.lot_Id'=>'RM Lot ID','a.data_sheet_sl_no'=>'Data Sheet Sl NO',
							 'a.data_sheet_date' => 'Data Sheet Date','b.gatePass'=>'Gate Pass','a.gatepass_details'=>'Gate Pass Details',
							 'e.pond_name' => 'Pond Name','a.pond_details'=>'Pond Details',
							 'a.farmer_at_harvest' => 'Farmer Harvest','a.product_species' => 'Product Species',
							 'j.name' => 'Purchase Supervisor','m.code' => 'Process Code',
							 'grade_count' => 'Grade Count','count_code' => 'Count Code',
							 'weight' => 'Weight','soft_precent' => 'Soft Precent','soft_weight' => 'Soft Weight',
							 'n.name' => 'Package Type','pkg_nos' => 'Package Nos',
							 'a.total_quantity' => 'Total Quantity','l.name' => 'Received Unit',
							 'k.name' => 'Receiving Supervisor','d.chemical_name' => 'Harvesting equipment',
							 'a.issued' => 'Issued','a.used' => 'Used','a.returned' => 'Returned',
							 'a.different' => 'Different');
			 $qry	= "select $fld from weighment_data_sheet a 
						   left join t_rmreceiptgatepass i on i.lot_Id = 
						   (select new_lot_Id from t_unittransfer where id=a.rm_lot_id) 
						   left join t_rmprocurmentorder b on i.procurment_Gate_PassId = b.id 
						   left join t_rmprocurmentorderentries c on b.id = c.rmProcurmentOrderId 
						   left join m_harvesting_chemical_master d on d.id = c.chemical 
						   left join m_pond_master e on e.id=a.pond_id 
						   left join supplier f on f.id = e.supplier 
						   left join m_landingcenter g on g.id = e.location 
						   left join m_state h on h.id = e.state 
						   left join m_employee_master j on j.id = a.purchase_supervisor 
						   left join m_employee_master k on k.id = a.receiving_supervisor 
						   left join m_plant l on l.id = a.received_at_unit 
						   left join m_processcode m on m.id = a.product_code 
						   left join m_packagingstructure n on a.package_type = n.id 
						   group by a.id ";
			$query=$reportObj->getQuery($qry);
			$mulQuery = $query;
			$a = array_flip($p['reportField']);
			// echo 'First Query<br/>';
			// echo '<pre>';
			// print_r($query);
			// echo '</pre>';
			// echo '**********<br/>';
			if(isset($a['grade_count']))
			{
				$fieldNameSelect = 'grade_count';
				$tableName = 'weighment_data_sheet_grade_count';
				$replaceVal = $a['grade_count'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						$temArray = array();
						foreach($mulQuery as  $qr)
						{							
							$id = $qr[$idField];
							$gradeCountValues = $reportObj->getMultipleFields($fieldNameSelect,$tableName,$id);
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $gradeCountValues;
							$v++;
						}
						break;
					}
				}
			}
			// echo 'After Grade count<br/>';
			// echo '<pre>';
			// print_r($query);
			// echo '</pre>';
			// echo '**********<br/>';
			if(isset($a['count_code']))
			{
				$fieldNameSelect = 'count_code';
				$tableName = 'weighment_data_sheet_count_code';
				$replaceVal = $a['count_code'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						foreach($mulQuery as  $qr)
						{							
							$id = $qr[$idField];
							$gradeCountValues = $reportObj->getMultipleFields($fieldNameSelect,$tableName,$id);
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $gradeCountValues;
							$v++;
						}
						break;
					}
				}
			}
			// echo 'After Count code<br/>';
			// echo '<pre>';
			// print_r($query);
			// echo '</pre>';
			// echo '**********<br/>';
			if(isset($a['weight']))
			{
				$fieldNameSelect = 'weight';
				$tableName = 'weighment_data_sheet_weight';
				$replaceVal = $a['weight'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						foreach($mulQuery as  $qr)
						{							
							$id = $qr[$idField];
							$gradeCountValues = $reportObj->getMultipleFields($fieldNameSelect,$tableName,$id);
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $gradeCountValues;
							$v++;
						}
						break;
					}
				}
			}
			if(isset($a['soft_precent']))
			{
				$fieldNameSelect = 'soft_precent';
				$tableName = 'weighment_data_sheet_soft_precent';
				$replaceVal = $a['soft_precent'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						foreach($mulQuery as  $qr)
						{							
							$id = $qr[$idField];
							$gradeCountValues = $reportObj->getMultipleFields($fieldNameSelect,$tableName,$id);
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $gradeCountValues;
							$v++;
						}
						break;
					}
				}
			}
			if(isset($a['soft_weight']))
			{
				$fieldNameSelect = 'soft_weight';
				$tableName = 'weighment_data_sheet_soft_weight';
				$replaceVal = $a['soft_weight'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						foreach($mulQuery as  $qr)
						{							
							$id = $qr[$idField];
							$gradeCountValues = $reportObj->getMultipleFields($fieldNameSelect,$tableName,$id);
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $gradeCountValues;
							$v++;
						}
						break;
					}
				}
			}
			if(isset($a['pkg_nos']))
			{
				$fieldNameSelect = 'pkg_nos';
				$tableName = 'weighment_data_sheet_pkg_nos';
				$replaceVal = $a['pkg_nos'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						foreach($mulQuery as  $qr)
						{							
							$id = $qr[$idField];
							$gradeCountValues = $reportObj->getMultipleFields($fieldNameSelect,$tableName,$id);
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $gradeCountValues;
							$v++;
						}
						break;
					}
				}
			}
			
			break;
		}
		case 9:
			{
			$idField = 0; $fld = '';
			$cnt=count($reportField);
			for($i=0; $i<$cnt; $i++)
			{
				if($reportField[$i] != 'weight' && $reportField[$i] != 'gradeType' )
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
	
			$fieldName=Array('b.new_lot_Id'=>'RM Lot ID','a.supplyDetails'=>'Supply Details','weight' => 'Weight','gradeType' => 'Grade Type','a.sumtotal'=>'Sum total','a.totalweight'=>'Total weight','a.difference'=>'Difference');
			//echo $value;
			$qry="SELECT $fld 
					FROM t_rmweightaftergrading a
					LEFT JOIN t_unittransfer b ON ( a.rmLotId = b.id )";
			$query=$reportathiObj->getQuery($qry);
			$mulQuery = $query;
			// $v = 0;
			$a = array_flip($p['reportField']);
			if(isset($a['weight']))
			{
				$replaceVal = $a['weight'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0; $temArray = array();
						foreach($mulQuery as  $qr)
						{	
													
							$id =$qr[$idField];
							$weghtValues = $reportObj->getWeight($id);
							
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $weghtValues;
							$v++;
							
							
							// for($j=$replaceVal;$j<5;$j++)
							// {
								// $query[$v][$j+1] = $query[$v][$j];
							// }
							// $query[$v][$replaceVal] = $weghtValues;
							// $v++;
						}
						break;
					}
				}
			}
			if(isset($a['gradeType']))
			{ 
			//echo "hii";
				$replaceVal = $a['gradeType'];
				for($i=0;$i<sizeof($p['reportField']);$i++)
				{
					if($i == $replaceVal)
					{
						$v = 0;
						foreach($mulQuery as  $qr)
						{	//print_r($qr);					
							 $id = $qr[$idField];
							$gradeValues = $reportObj->getgradetype($id);
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$temArray[$v][$j+1] = $query[$v][$j];
							}
							for($j=$replaceVal;$j<sizeof($p['reportField']);$j++)
							{
								$query[$v][$j] = $temArray[$v][$j];
							}
							$query[$v][$replaceVal] = $gradeValues;
							$v++;
							// for($j=$replaceVal;$j<sizeof($fieldName);$j++)
							// {
								// $query[$v][$j+1] = $query[$v][$j];
							// }
							// $query[$v][$replaceVal] = $gradeValues;
							// $v++;
						}
						break;
					}
				}
			}
			
			
			
						
				 break;
			}
			case 10:
			{
				$fieldName = array('untr.new_lot_Id' => 'Procurement No','a.date' => 'Date',
								   'mf.name' => 'Fish Name','mp.code' => 'Process Code',
								   'b.opening_bal_qty' => 'Opening Balance','b.arrival_qty' => 'Arrival Qty',
								   'b.total_qty' => 'Total Qty','b.total_preprocess_qty' => 'Total Preprocess Qty', 
								   'b.actual_yield' => 'Actual Yield','b.ideal_yield' => 'Ideal Field',
								   'b.diff_yield' => 'Different Yield','b.available_qty' => 'Available Qty');
				$qry = "select $fld  
						from t_dailypreprocess a 
						left join t_dailypreprocess_entries b on a.id = b.dailypreprocess_main_id 
						left join m_process mp on mp.id=b.process 
						left join m_fish mf on a.fish_id=mf.id 
						left join t_unittransfer untr on untr.id = b.lot_id 
						join pre_process_sequence frs on frs.processcode_id = substring(mp.processes,1,instr(mp.processes,',')-1) 
						join pre_process_sequence tos on tos.processcode_id = substring(mp.processes,length(mp.processes)-instr(reverse(mp.processes),',')+2) 
						order by a.date  ";
				$query=$reportObj->getQuery($qry);
				break;
			}
			case 11:
			{
				$fieldName = array('untr.new_lot_Id'=>'RM Lot ID','a.available_qty'=>'Available qty',
								   'a.select_date' => 'Date','mpc.code' => 'Process Code',
								   'mfs.rm_stage' => 'Freezing Stage','mfp.code' => 'Frozen Code',
								   '(select count(*) from t_dailyfrozenpacking_allocated_entry dfpae where b.id=dfpae.entry_id group by dfpae.entry_id) 
									as allocatedCount' => 'No.of MCs',
									'mcp.code' => 'MC Pkg',
									'((sum(tdfpg.number_mc)*mfp.filled_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.filled_wt)) as pkdQty, 
									sum(tdfpg.number_mc) as numMcs' => 'No.of LS',
									'((sum(tdfpg.number_mc)*mfp.decl_wt*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*mfp.decl_wt)) 
									as frozenQty' => 'Frozen Qty',
									'((sum(tdfpg.number_mc)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt)*mcp.number_packs)+(sum(tdfpg.number_loose_slab)*if(mfp.actual_filled_wt!=0,mfp.actual_filled_wt,mfp.filled_wt))) 
									as availableQty' => 'Pkd Qty',
									'sum(tdfpg.number_loose_slab) as numLS' => 'RM Used');
				//echo $value;
				  $qry 	= " select $fld				
					from t_dailyfrozenpacking_main a 
					left join t_dailyfrozenpacking_entry b on a.id=b.main_id 
					left join t_dailyfrozenpacking_grade tdfpg on b.id=tdfpg.entry_id 
					left join m_processcode mpc on mpc.id=b.processcode_id 
					left join m_freezingstage mfs on mfs.id=b.freezing_stage_id 
					left join m_frozenpacking mfp on mfp.id=b.frozencode_id 
					left join m_mcpacking mcp on b.mcpacking_id=mcp.id 
					left join t_unittransfer untr on untr.id = a.rm_lot_id 					
					group by a.id  
					order by a.select_date asc 
								";
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