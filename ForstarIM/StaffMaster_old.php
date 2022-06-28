<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$fishId			=	"";	
	$fishName		=	"";
	$fishCode		=	"";
	
	$selection 		=	"?pageNo=".$p["pageNo"];
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirmF=false;
	
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
	//echo "The value of confirm is $confirmF";
	//----------------------------------------------------------

	# Add Staff Start 
	if( $p["cmdAddNew"]!="" ){
		$addMode		=	true;
	}
	if ($p["cmdAddStaff"]!="") {

		$name	=	addSlash(trim($p["name"]));
		$functions	=	addSlash(trim($p["functions"]));
		$cost	=	addSlash(trim($p["cost"]));
		$allowance = addSlash(trim($p["allowance"]));
		$effectiveDate	=mysqlDateFormat(trim($p["effectiveDate"]));
		$actualCost	=	addSlash(trim($p["actualCost"]));
		$type	=	addSlash(trim($p["type"]));
		$department	=trim($p["department"]);
		if ($name!="" && $functions!="" && $cost!="") {

			$chkDuplicate=$staffMasterObj->checkDuplicate($name,$functions,$department);
			if(!$chkDuplicate)
			{
				//$getDepartment  = 	$departmentMasterObj->getDepartmentType($department);
				$staffRecIns	=	$staffMasterObj->addStaff($name,$functions,$cost,$allowance,$effectiveDate,$actualCost,$type,$department,$userId);
			}
			if ($staffRecIns) {
				$sessObj->createSession("displayMsg",$msg_succAddStaff);
				$sessObj->createSession("nextPage",$url_afterAddStaff.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddStaff;
			}
			$staffRecIns		=	false;
		}

	}


	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$staffId	=	$p["confirmId"];
			if ($staffId!="") 
			{
				$staffDetails = $staffMasterObj->find($staffId);
				if($staffDetails!="")
				{
					$type 		= 	$staffDetails[5];
					$deptId 	=	$staffDetails[6];
					$actualCost = 	$staffDetails[8];
					
					// Checking the selected fish is link with any other process
					$staffRecConfirm = $staffMasterObj->updateStaffconfirm($staffId);
					
					//Department Name
					$getDepartment  = 	$departmentMasterObj->getDepartmentType($deptId);
					if($getDepartment[0] == "production")
					{
						//echo "production";
						if($type == "fixed")
						{
							$fixedDeptEntry = $productionPowerObj->checkFixedDeptEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($fixedDeptEntry!="")
							{	$fixedId 		 = $fixedDeptEntry[0];
								$newTotalCost 	 = $fixedDeptEntry[1];
								$totalCost	 	 = $fixedDeptEntry[2];
								$updateNewTotalCost = $newTotalCost + $actualCost;
								$updateTotalCost    = $totalCost + $actualCost;
								//Update m_production_fixed_manpower table
								$updateFixedPrdtn = $productionPowerObj->updateFixedPrdtnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
							}
							else
							{
								$addFixedPrdtn = $productionPowerObj->addProductionFixedPower($deptId,$actualCost,$actualCost,$userId);
							}
						}
						else 
						{
							$variableDeptEntry = $productionPowerObj->checkVariableDeptEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($variableDeptEntry!="")
							{	$variableId 	 = $variableDeptEntry[0];
								$newTotalCost 	 = $variableDeptEntry[1];
								$totalCost	 	 = $variableDeptEntry[2];
								$updateNewTotalCost = $newTotalCost + $actualCost;
								$updateTotalCost    = $totalCost + $actualCost;
								//Update m_production_variable_manpower table
								$updateVariablePrdtn = $productionPowerObj->updateVariablePrdtnCost($variableId,$updateNewTotalCost,$updateTotalCost);
							}
							else
							{
								$addVariablePrdtn = $productionPowerObj->addProductionVariablePower($deptId,$actualCost,$actualCost,$userId);
							}
						}
					}
					else if($getDepartment[0] == "marketing")
					{
						//echo "marketing";
						if($type == "fixed")
						{
							$fixedMktngEntry = $productionMarketingCostObj->checkFixedMktngEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($fixedMktngEntry!="")
							{	$fixedId 		 = $fixedMktngEntry[0];
								$newTotalCost 	 = $fixedMktngEntry[1];
								$totalCost	 	 = $fixedMktngEntry[2];
								$updateNewTotalCost = $newTotalCost + $actualCost;
								$updateTotalCost    = $totalCost + $actualCost;
								//Update m_production_fixed_marketing table
								$updateFixedMktng = $productionMarketingCostObj->updateFixedMktngCost($fixedId,$updateNewTotalCost,$updateTotalCost);
							}
							else
							{
								$addFixedMktng = $productionMarketingCostObj->addProductionFixedMarketing($deptId,$actualCost,$actualCost,$userId);
							}
						}
						else 
						{
							$variableMktngEntry = $productionMarketingCostObj->checkVariableMktngEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($variableMktngEntry!="")
							{	$variableId 	 = $variableMktngEntry[0];
								$newTotalCost 	 = $variableMktngEntry[1];
								$totalCost	 	 = $variableMktngEntry[2];
								$updateNewTotalCost = $newTotalCost + $actualCost;
								$updateTotalCost    = $totalCost + $actualCost;
								//Update m_production_variable_marketing table
								$updateVariableMktng = $productionMarketingCostObj->updateVariableMktngCost($variableId,$updateNewTotalCost,$updateTotalCost);
							}
							else
							{
								$addVariableMktng = $productionMarketingCostObj->addProductionVariableMarketing($deptId,$actualCost,$actualCost,$userId);
							}
						}
					}
					else if($getDepartment[0] == "operation")
					{
						//echo "operation";
						if($type == "fixed")
						{
							$fixedOprtnEntry = $productionOperationObj->checkFixedOperatnEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($fixedOprtnEntry!="")
							{	$fixedId 		 = $fixedOprtnEntry[0];
								$newTotalCost 	 = $fixedOprtnEntry[1];
								$totalCost	 	 = $fixedOprtnEntry[2];
								$updateNewTotalCost = $newTotalCost + $actualCost;
								$updateTotalCost    = $totalCost + $actualCost;
								//Update m_production_fixed_marketing table
								$updateFixedOprtn = $productionOperationObj->updateFixedOperatnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
							}
							else
							{
								$addFixedOprtn = $productionOperationObj->addProductionFixedOperation($deptId,$actualCost,$actualCost,$userId);
							}
						}
						else 
						{
							$variableOprtnEntry = $productionOperationObj->checkVariableOperatnEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($variableOprtnEntry!="")
							{	$variableId 	 = $variableOprtnEntry[0];
								$newTotalCost 	 = $variableOprtnEntry[1];
								$totalCost	 	 = $variableOprtnEntry[2];
								$updateNewTotalCost = $newTotalCost + $actualCost;
								$updateTotalCost    = $totalCost + $actualCost;
								//Update m_production_variable_marketing table
								$updateVariableOprtn = $productionOperationObj->updateVariableOperatnCost($variableId,$updateNewTotalCost,$updateTotalCost);
							}
							else
							{
								$addVariableOprtn = $productionOperationObj->addProductionVariableOperation($deptId,$actualCost,$actualCost,$userId);
							}
						}
					}
				}
			}
		}
		if ($staffRecConfirm) 
		{
			$sessObj->createSession("displayMsg",$msg_succConfirmStaff);
			$sessObj->createSession("nextPage",$url_afterDelStaff.$selection);
		} 
		else 
		{
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$staffId	=	$p["confirmId"];
			if ($staffId!="") 
			{
				
				$staffDetails = $staffMasterObj->find($staffId);
				if($staffDetails!="")
				{
					$type 		= 	$staffDetails[5];
					$deptId 	=	$staffDetails[6];
					$actualCost = 	$staffDetails[8];
					
					//Department Name
					$getDepartment  = 	$departmentMasterObj->getDepartmentType($deptId);
					if($getDepartment[0] == "production")
					{
						//echo "production";
						if($type == "fixed")
						{
							$fixedDeptEntry = $productionPowerObj->checkFixedDeptEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($fixedDeptEntry!="")
							{	$fixedId 		 = $fixedDeptEntry[0];
								$newTotalCost 	 = $fixedDeptEntry[1];
								$totalCost	 	 = $fixedDeptEntry[2];
								$updateNewTotalCost = $newTotalCost - $actualCost;
								$updateTotalCost    = $totalCost - $actualCost;
								//Update m_production_fixed_manpower table
								$updateFixedPrdtn = $productionPowerObj->updateFixedPrdtnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
							}
						}
						else 
						{
							$variableDeptEntry = $productionPowerObj->checkVariableDeptEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($variableDeptEntry!="")
							{	$variableId 	 = $variableDeptEntry[0];
								$newTotalCost 	 = $variableDeptEntry[1];
								$totalCost	 	 = $variableDeptEntry[2];
								$updateNewTotalCost = $newTotalCost - $actualCost;
								$updateTotalCost    = $totalCost - $actualCost;
								//Update m_production_variable_manpower table
								$updateVariablePrdtn = $productionPowerObj->updateVariablePrdtnCost($variableId,$updateNewTotalCost,$updateTotalCost);
							}
						}
					}
					else if($getDepartment[0] == "marketing")
					{
						//echo "marketing";
						if($type == "fixed")
						{
							$fixedMktngEntry = $productionMarketingCostObj->checkFixedMktngEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($fixedMktngEntry!="")
							{	$fixedId 		 = $fixedMktngEntry[0];
								$newTotalCost 	 = $fixedMktngEntry[1];
								$totalCost	 	 = $fixedMktngEntry[2];
								$updateNewTotalCost = $newTotalCost - $actualCost;
								$updateTotalCost    = $totalCost - $actualCost;
								//Update m_production_fixed_marketing table
								$updateFixedMktng = $productionMarketingCostObj->updateFixedMktngCost($fixedId,$updateNewTotalCost,$updateTotalCost);
							}
						}
						else 
						{
							$variableMktngEntry = $productionMarketingCostObj->checkVariableMktngEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($variableMktngEntry!="")
							{	$variableId 	 = $variableMktngEntry[0];
								$newTotalCost 	 = $variableMktngEntry[1];
								$totalCost	 	 = $variableMktngEntry[2];
								$updateNewTotalCost = $newTotalCost - $actualCost;
								$updateTotalCost    = $totalCost - $actualCost;
								//Update m_production_variable_marketing table
								$updateVariableMktng = $productionMarketingCostObj->updateVariableMktngCost($variableId,$updateNewTotalCost,$updateTotalCost);
							}
						}
					}
					else if($getDepartment[0] == "operation")
					{
						//echo "operation";
						if($type == "fixed")
						{
							$fixedOprtnEntry = $productionOperationObj->checkFixedOperatnEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($fixedOprtnEntry!="")
							{	$fixedId 		 = $fixedOprtnEntry[0];
								$newTotalCost 	 = $fixedOprtnEntry[1];
								$totalCost	 	 = $fixedOprtnEntry[2];
								$updateNewTotalCost = $newTotalCost - $actualCost;
								$updateTotalCost    = $totalCost - $actualCost;
								//Update m_production_fixed_marketing table
								$updateFixedOprtn = $productionOperationObj->updateFixedOperatnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
							}
						}
						else 
						{
							$variableOprtnEntry = $productionOperationObj->checkVariableOperatnEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($variableOprtnEntry!="")
							{	$variableId 	 = $variableOprtnEntry[0];
								$newTotalCost 	 = $variableOprtnEntry[1];
								$totalCost	 	 = $variableOprtnEntry[2];
								$updateNewTotalCost = $newTotalCost - $actualCost;
								$updateTotalCost    = $totalCost - $actualCost;
								//Update m_production_variable_marketing table
								$updateVariableOprtn = $productionOperationObj->updateVariableOperatnCost($variableId,$updateNewTotalCost,$updateTotalCost);
							}
						}
					}
				}
				#Check any entries exist
				$staffRecConfirm = $staffMasterObj->updateStaffReleaseconfirm($staffId);
			}
		}
		if ($staffRecConfirm)
		{
			$sessObj->createSession("displayMsg",$msg_succRelConfirmStaff);
			$sessObj->createSession("nextPage",$url_afterDelStaff.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
	}

	
	# Edit staff 
	if ($p["editId"]!="") {
		$editIt			=	$p["editId"];
		$editMode		=	true;
		$staffRec		=	$staffMasterObj->find($editIt);
		$staffId			=	$staffRec[0];
		$name		=	stripSlash($staffRec[1]);
		$functions		=	$staffRec[2] ;
		$cost		=	stripSlash($staffRec[3]);
		$allownce   =   stripSlash($staffRec[4]); 
		$type		=	stripSlash($staffRec[5]);
		($type=="variable")?$selvar="selected":$selvar="";
		($type=="fixed")?$selfix="selected":$selfix="";
		$departmentId		=	stripSlash($staffRec[6]);
		$effectiveDate		=	dateFormat($staffRec[7]);
		$actualCost		=	stripSlash($staffRec[8]);
	}

	if ($p["cmdSaveChange"]!="") 
	{
		
		$staffId		=	$p["hidStaffId"];
		$name	=	addSlash(trim($p["name"]));
		$functions	=	trim($p["functions"]);
		$hidFunctions	=	trim($p["hidFunctions"]);
		$cost	=	addSlash(trim($p["cost"]));
		$allowance = addSlash(trim($p["allowance"]));
		$type	=	addSlash(trim($p["type"]));
		$hidType	=trim($p["hidType"]);
		$department	=trim($p["department"]);
		$hidDepartment	=trim($p["hidDepartment"]);
		$effectiveDate	=mysqlDateFormat(trim($p["effectiveDate"]));
		$actualCost	=	addSlash(trim($p["actualCost"]));
		$hidActualCost = $p['hidActualCost'];
		//$departmentName = 

		
		if ($staffId!="" && $name!="" && $functions!="" && $cost!="") 
		{
			if($functions==$hidFunctions && $type==$hidType && $department==$hidDepartment)
			{
				$staffRecUptd		=	$staffMasterObj->updateStaff($staffId,$name,$functions,$cost,$allowance,$effectiveDate,$actualCost,$type,$department);
				
				//$getDepartment  	= 	$departmentMasterObj->getDepartmentType($department);
				/*if($actualCost!=$hidActualCost)
				{
					if($type == "fixed")
					{
						if($getDepartment[0] == "production")
						{
							$fixedDeptEntry = $productionPowerObj->checkFixedDeptEntry($department);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($fixedDeptEntry!="")
							{	
								$fixedId 		 = $fixedDeptEntry[0];
								$newTotalCost 	 = $fixedDeptEntry[1];
								$totalCost	 	 = $fixedDeptEntry[2];
								$newTotalCost-=$hidActualCost;
								$totalCost-=$hidActualCost;
								$updateNewTotalCost = $newTotalCost + $actualCost;
								$updateTotalCost    = $totalCost + $actualCost;
								//Update m_production_fixed_manpower table
								$updateFixedPrdtn = $productionPowerObj->updateFixedPrdtnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
							}
						}
						else if($getDepartment[0] == "marketing")
						{
							$fixedMktngEntry = $productionMarketingCostObj->checkFixedMktngEntry($department);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($fixedMktngEntry!="")
							{	$fixedId 		 = $fixedMktngEntry[0];
								$newTotalCost 	 = $fixedMktngEntry[1];
								$totalCost	 	 = $fixedMktngEntry[2];
								$newTotalCost-=$hidActualCost;
								$totalCost-=$hidActualCost;
								$updateNewTotalCost = $newTotalCost + $actualCost;
								$updateTotalCost    = $totalCost + $actualCost;
								//Update m_production_fixed_marketing table
								$updateFixedMktng = $productionMarketingCostObj->updateFixedMktngCost($fixedId,$updateNewTotalCost,$updateTotalCost);
							}
						}
						else if($getDepartment[0] == "operation")
						{
							$fixedOprtnEntry = $productionOperationObj->checkFixedOperatnEntry($department);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($fixedOprtnEntry!="")
							{	$fixedId 		 = $fixedOprtnEntry[0];
								$newTotalCost 	 = $fixedOprtnEntry[1];
								$totalCost	 	 = $fixedOprtnEntry[2];
								$newTotalCost-=$hidActualCost;
								$totalCost-=$hidActualCost;
								$updateNewTotalCost = $newTotalCost + $actualCost;
								$updateTotalCost    = $totalCost + $actualCost;
								//Update m_production_fixed_marketing table
								$updateFixedOprtn = $productionOperationObj->updateFixedOperatnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
							}
						}
					}
					else
					{
						if($getDepartment[0] == "production")
						{
							$variableDeptEntry = $productionPowerObj->checkVariableDeptEntry($department);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($variableDeptEntry!="")
							{	$variableId 	 = $variableDeptEntry[0];
								$newTotalCost 	 = $variableDeptEntry[1];
								$totalCost	 	 = $variableDeptEntry[2];
								$newTotalCost-=$hidActualCost;
								$totalCost-=$hidActualCost;
								$updateNewTotalCost = $newTotalCost + $actualCost;
								$updateTotalCost    = $totalCost + $actualCost;
								//Update m_production_variable_manpower table
								$updateVariablePrdtn = $productionPowerObj->updateVariablePrdtnCost($variableId,$updateNewTotalCost,$updateTotalCost);
							}
						}
						else if($getDepartment[0] == "marketing")
						{
							$variableMktngEntry = $productionMarketingCostObj->checkVariableMktngEntry($department);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($variableMktngEntry!="")
							{	$variableId 	 = $variableMktngEntry[0];
								$newTotalCost 	 = $variableMktngEntry[1];
								$totalCost	 	 = $variableMktngEntry[2];
								$newTotalCost-=$hidActualCost;
								$totalCost-=$hidActualCost;
								$updateNewTotalCost = $newTotalCost + $actualCost;
								$updateTotalCost    = $totalCost + $actualCost;
								//Update m_production_variable_marketing table
								$updateVariableMktng = $productionMarketingCostObj->updateVariableMktngCost($variableId,$updateNewTotalCost,$updateTotalCost);
							}
						}
						else if($getDepartment[0] == "operation")
						{
							$variableOprtnEntry = $productionOperationObj->checkVariableOperatnEntry($department);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($variableOprtnEntry!="")
							{	$variableId 	 = $variableOprtnEntry[0];
								$newTotalCost 	 = $variableOprtnEntry[1];
								$totalCost	 	 = $variableOprtnEntry[2];
								$newTotalCost-=$hidActualCost;
								$totalCost-=$hidActualCost;
								$updateNewTotalCost = $newTotalCost + $actualCost;
								$updateTotalCost    = $totalCost + $actualCost;
								//Update m_production_variable_marketing table
								$updateVariableOprtn = $productionOperationObj->updateVariableOperatnCost($variableId,$updateNewTotalCost,$updateTotalCost);
							}
						}
					}
				} */
			}
			else
			{
				$startDate=$staffMasterObj->getStartDate($staffId);
				//echo $effectiveDate.'--'.$startDate;
				//die();
				if($effectiveDate>$startDate)
				{	
					//echo "hii am in";
					$sDate		= explode("-",$effectiveDate);
					$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
					$ratelistOld =$staffMasterObj->updateStaffEndDate($endDate,$staffId);
					$staffRecIns	=	$staffMasterObj->addStaff($name,$functions,$cost,$allowance,$effectiveDate,$actualCost,$type,$department,$userId);
					/*if($department==$hidDepartment)
					{
						if($type == $hidType)
						{
							$getDepartment  	= 	$departmentMasterObj->getDepartmentType($department);
							if($actualCost!=$hidActualCost)
							{
								if($type == "fixed")
								{
									if($getDepartment[0] == "production")
									{
										$fixedDeptEntry = $productionPowerObj->checkFixedDeptEntry($department);
										$updateNewTotalCost = 0;
										$updateTotalCost = 0;
										if($fixedDeptEntry!="")
										{	
											$fixedId 		 = $fixedDeptEntry[0];
											$newTotalCost 	 = $fixedDeptEntry[1];
											$totalCost	 	 = $fixedDeptEntry[2];
											$newTotalCost-=$hidActualCost;
											$totalCost-=$hidActualCost;
											$updateNewTotalCost = $newTotalCost + $actualCost;
											$updateTotalCost    = $totalCost + $actualCost;
											//Update m_production_fixed_manpower table
											$updateFixedPrdtn = $productionPowerObj->updateFixedPrdtnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
										}
									}
									else if($getDepartment[0] == "marketing")
									{
										$fixedMktngEntry = $productionMarketingCostObj->checkFixedMktngEntry($department);
										$updateNewTotalCost = 0;
										$updateTotalCost = 0;
										if($fixedMktngEntry!="")
										{	$fixedId 		 = $fixedMktngEntry[0];
											$newTotalCost 	 = $fixedMktngEntry[1];
											$totalCost	 	 = $fixedMktngEntry[2];
											$newTotalCost-=$hidActualCost;
											$totalCost-=$hidActualCost;
											$updateNewTotalCost = $newTotalCost + $actualCost;
											$updateTotalCost    = $totalCost + $actualCost;
											//Update m_production_fixed_marketing table
											$updateFixedMktng = $productionMarketingCostObj->updateFixedMktngCost($fixedId,$updateNewTotalCost,$updateTotalCost);
										}
									}
									else if($getDepartment[0] == "operation")
									{
										$fixedOprtnEntry = $productionOperationObj->checkFixedOperatnEntry($department);
										$updateNewTotalCost = 0;
										$updateTotalCost = 0;
										if($fixedOprtnEntry!="")
										{	$fixedId 		 = $fixedOprtnEntry[0];
											$newTotalCost 	 = $fixedOprtnEntry[1];
											$totalCost	 	 = $fixedOprtnEntry[2];
											$newTotalCost-=$hidActualCost;
											$totalCost-=$hidActualCost;
											$updateNewTotalCost = $newTotalCost + $actualCost;
											$updateTotalCost    = $totalCost + $actualCost;
											//Update m_production_fixed_marketing table
											$updateFixedOprtn = $productionOperationObj->updateFixedOperatnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
										}
									}
								}
								else
								{
									if($getDepartment[0] == "production")
									{
										$variableDeptEntry = $productionPowerObj->checkVariableDeptEntry($department);
										$updateNewTotalCost = 0;
										$updateTotalCost = 0;
										if($variableDeptEntry!="")
										{	$variableId 	 = $variableDeptEntry[0];
											$newTotalCost 	 = $variableDeptEntry[1];
											$totalCost	 	 = $variableDeptEntry[2];
											$newTotalCost-=$hidActualCost;
											$totalCost-=$hidActualCost;
											$updateNewTotalCost = $newTotalCost + $actualCost;
											$updateTotalCost    = $totalCost + $actualCost;
											//Update m_production_variable_manpower table
											$updateVariablePrdtn = $productionPowerObj->updateVariablePrdtnCost($variableId,$updateNewTotalCost,$updateTotalCost);
										}
									}
									else if($getDepartment[0] == "marketing")
									{
										$variableMktngEntry = $productionMarketingCostObj->checkVariableMktngEntry($department);
										$updateNewTotalCost = 0;
										$updateTotalCost = 0;
										if($variableMktngEntry!="")
										{	$variableId 	 = $variableMktngEntry[0];
											$newTotalCost 	 = $variableMktngEntry[1];
											$totalCost	 	 = $variableMktngEntry[2];
											$newTotalCost-=$hidActualCost;
											$totalCost-=$hidActualCost;
											$updateNewTotalCost = $newTotalCost + $actualCost;
											$updateTotalCost    = $totalCost + $actualCost;
											//Update m_production_variable_marketing table
											$updateVariableMktng = $productionMarketingCostObj->updateVariableMktngCost($variableId,$updateNewTotalCost,$updateTotalCost);
										}
									}
									else if($getDepartment[0] == "operation")
									{
										$variableOprtnEntry = $productionOperationObj->checkVariableOperatnEntry($department);
										$updateNewTotalCost = 0;
										$updateTotalCost = 0;
										if($variableOprtnEntry!="")
										{	$variableId 	 = $variableOprtnEntry[0];
											$newTotalCost 	 = $variableOprtnEntry[1];
											$totalCost	 	 = $variableOprtnEntry[2];
											$newTotalCost-=$hidActualCost;
											$totalCost-=$hidActualCost;
											$updateNewTotalCost = $newTotalCost + $actualCost;
											$updateTotalCost    = $totalCost + $actualCost;
											//Update m_production_variable_marketing table
											$updateVariableOprtn = $productionOperationObj->updateVariableOperatnCost($variableId,$updateNewTotalCost,$updateTotalCost);
										}
									}
								}
							}
						}
						else
						{
							$getDepartment  	= 	$departmentMasterObj->getDepartmentType($hidDepartment);
							if($hidType == "fixed")
							{
								if($getDepartment[0] == "production")
								{
									$fixedDeptEntry = $productionPowerObj->checkFixedDeptEntry($hidDepartment);
									$updateNewTotalCost = 0;
									$updateTotalCost = 0;
									if($fixedDeptEntry!="")
									{	
										$fixedId 		 = $fixedDeptEntry[0];
										$newTotalCost 	 = $fixedDeptEntry[1];
										$totalCost	 	 = $fixedDeptEntry[2];
										$updateNewTotalCost = $newTotalCost - $hidActualCost;
										$updateTotalCost    = $totalCost - $hidActualCost;
										//Update m_production_fixed_manpower table
										$updateFixedPrdtn = $productionPowerObj->updateFixedPrdtnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
									}
								}
								else if($getDepartment[0] == "marketing")
								{
									$fixedMktngEntry = $productionMarketingCostObj->checkFixedMktngEntry($hidDepartment);
									$updateNewTotalCost = 0;
									$updateTotalCost = 0;
									if($fixedMktngEntry!="")
									{	$fixedId 		 = $fixedMktngEntry[0];
										$newTotalCost 	 = $fixedMktngEntry[1];
										$totalCost	 	 = $fixedMktngEntry[2];
										$updateNewTotalCost = $newTotalCost - $hidActualCost;
										$updateTotalCost    = $totalCost - $hidActualCost;
										//Update m_production_fixed_marketing table
										$updateFixedMktng = $productionMarketingCostObj->updateFixedMktngCost($fixedId,$updateNewTotalCost,$updateTotalCost);
									}
								}
								else if($getDepartment[0] == "operation")
								{
									$fixedOprtnEntry = $productionOperationObj->checkFixedOperatnEntry($hidDepartment);
									$updateNewTotalCost = 0;
									$updateTotalCost = 0;
									if($fixedOprtnEntry!="")
									{	$fixedId 		 = $fixedOprtnEntry[0];
										$newTotalCost 	 = $fixedOprtnEntry[1];
										$totalCost	 	 = $fixedOprtnEntry[2];
										$updateNewTotalCost = $newTotalCost - $hidActualCost;
										$updateTotalCost    = $totalCost - $hidActualCost;
										//Update m_production_fixed_marketing table
										$updateFixedOprtn = $productionOperationObj->updateFixedOperatnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
									}
								}
							}
							else
							{
								if($getDepartment[0] == "production")
								{
									$variableDeptEntry = $productionPowerObj->checkVariableDeptEntry($hidDepartment);
									$updateNewTotalCost = 0;
									$updateTotalCost = 0;
									if($variableDeptEntry!="")
									{	$variableId 	 = $variableDeptEntry[0];
										$newTotalCost 	 = $variableDeptEntry[1];
										$totalCost	 	 = $variableDeptEntry[2];
										$updateNewTotalCost = $newTotalCost - $hidActualCost;
										$updateTotalCost    = $totalCost - $hidActualCost;
										//Update m_production_variable_manpower table
										$updateVariablePrdtn = $productionPowerObj->updateVariablePrdtnCost($variableId,$updateNewTotalCost,$updateTotalCost);
									}
								}
								else if($getDepartment[0] == "marketing")
								{
									$variableMktngEntry = $productionMarketingCostObj->checkVariableMktngEntry($hidDepartment);
									$updateNewTotalCost = 0;
									$updateTotalCost = 0;
									if($variableMktngEntry!="")
									{	$variableId 	 = $variableMktngEntry[0];
										$newTotalCost 	 = $variableMktngEntry[1];
										$totalCost	 	 = $variableMktngEntry[2];
										$updateNewTotalCost = $newTotalCost - $hidActualCost;
										$updateTotalCost    = $totalCost - $hidActualCost;
										//Update m_production_variable_marketing table
										$updateVariableMktng = $productionMarketingCostObj->updateVariableMktngCost($variableId,$updateNewTotalCost,$updateTotalCost);
									}
								}
								else if($getDepartment[0] == "operation")
								{
									$variableOprtnEntry = $productionOperationObj->checkVariableOperatnEntry($hidDepartment);
									$updateNewTotalCost = 0;
									$updateTotalCost = 0;
									if($variableOprtnEntry!="")
									{	$variableId 	 = $variableOprtnEntry[0];
										$newTotalCost 	 = $variableOprtnEntry[1];
										$totalCost	 	 = $variableOprtnEntry[2];
										$updateNewTotalCost = $newTotalCost - $hidActualCost;
										$updateTotalCost    = $totalCost - $hidActualCost;
										//Update m_production_variable_marketing table
										$updateVariableOprtn = $productionOperationObj->updateVariableOperatnCost($variableId,$updateNewTotalCost,$updateTotalCost);
									}
								}
							}
							
							
							$getNewDepartment  	= 	$departmentMasterObj->getDepartmentType($department);
							if($type == "fixed")
							{
								if($getNewDepartment[0] == "production")
								{
									$fixedDeptEntry = $productionPowerObj->checkFixedDeptEntry($department);
									$updateNewTotalCost = 0;
									$updateTotalCost = 0;
									if($fixedDeptEntry!="")
									{	
										$fixedId 		 = $fixedDeptEntry[0];
										$newTotalCost 	 = $fixedDeptEntry[1];
										$totalCost	 	 = $fixedDeptEntry[2];
										$updateNewTotalCost = $newTotalCost + $actualCost;
										$updateTotalCost    = $totalCost + $actualCost;
										//Update m_production_fixed_manpower table
										$updateFixedPrdtn = $productionPowerObj->updateFixedPrdtnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
									}
									else
									{
										$addFixedPrdtn = $productionPowerObj->addProductionFixedPower($department,$actualCost,$actualCost,$userId);
									}
								}
								else if($getNewDepartment[0] == "marketing")
								{
									$fixedMktngEntry = $productionMarketingCostObj->checkFixedMktngEntry($department);
									$updateNewTotalCost = 0;
									$updateTotalCost = 0;
									if($fixedMktngEntry!="")
									{	$fixedId 		 = $fixedMktngEntry[0];
										$newTotalCost 	 = $fixedMktngEntry[1];
										$totalCost	 	 = $fixedMktngEntry[2];
										$updateNewTotalCost = $newTotalCost + $actualCost;
										$updateTotalCost    = $totalCost + $actualCost;
										//Update m_production_fixed_marketing table
										$updateFixedMktng = $productionMarketingCostObj->updateFixedMktngCost($fixedId,$updateNewTotalCost,$updateTotalCost);
									}
									else
									{
										$addFixedMktng = $productionMarketingCostObj->addProductionFixedMarketing($department,$actualCost,$actualCost,$userId);
									}
								}
								else if($getNewDepartment[0] == "operation")
								{
									$fixedOprtnEntry = $productionOperationObj->checkFixedOperatnEntry($department);
									$updateNewTotalCost = 0;
									$updateTotalCost = 0;
									if($fixedOprtnEntry!="")
									{	$fixedId 		 = $fixedOprtnEntry[0];
										$newTotalCost 	 = $fixedOprtnEntry[1];
										$totalCost	 	 = $fixedOprtnEntry[2];
										$updateNewTotalCost = $newTotalCost + $actualCost;
										$updateTotalCost    = $totalCost + $actualCost;
										//Update m_production_fixed_marketing table
										$updateFixedOprtn = $productionOperationObj->updateFixedOperatnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
									}
									else
									{
										$addFixedOprtn = $productionOperationObj->addProductionFixedOperation($department,$actualCost,$actualCost,$userId);
									}
								}
							}
							else
							{
								if($getNewDepartment[0] == "production")
								{
									$variableDeptEntry = $productionPowerObj->checkVariableDeptEntry($department);
									$updateNewTotalCost = 0;
									$updateTotalCost = 0;
									if($variableDeptEntry!="")
									{	$variableId 	 = $variableDeptEntry[0];
										$newTotalCost 	 = $variableDeptEntry[1];
										$totalCost	 	 = $variableDeptEntry[2];
										$updateNewTotalCost = $newTotalCost + $actualCost;
										$updateTotalCost    = $totalCost + $actualCost;
										//Update m_production_variable_manpower table
										$updateVariablePrdtn = $productionPowerObj->updateVariablePrdtnCost($variableId,$updateNewTotalCost,$updateTotalCost);
									}
									else
									{
										$addVariablePrdtn = $productionPowerObj->addProductionVariablePower($department,$actualCost,$actualCost,$userId);
									}
								}
								else if($getNewDepartment[0] == "marketing")
								{
									$variableMktngEntry = $productionMarketingCostObj->checkVariableMktngEntry($department);
									$updateNewTotalCost = 0;
									$updateTotalCost = 0;
									if($variableMktngEntry!="")
									{	$variableId 	 = $variableMktngEntry[0];
										$newTotalCost 	 = $variableMktngEntry[1];
										$totalCost	 	 = $variableMktngEntry[2];
										$updateNewTotalCost = $newTotalCost + $actualCost;
										$updateTotalCost    = $totalCost + $actualCost;
										//Update m_production_variable_marketing table
										$updateVariableMktng = $productionMarketingCostObj->updateVariableMktngCost($variableId,$updateNewTotalCost,$updateTotalCost);
									}
									else
									{
										$addVariableMktng = $productionMarketingCostObj->addProductionVariableMarketing($department,$actualCost,$actualCost,$userId);
									}
								}
								else if($getNewDepartment[0] == "operation")
								{
									$variableOprtnEntry = $productionOperationObj->checkVariableOperatnEntry($department);
									$updateNewTotalCost = 0;
									$updateTotalCost = 0;
									if($variableOprtnEntry!="")
									{	$variableId 	 = $variableOprtnEntry[0];
										$newTotalCost 	 = $variableOprtnEntry[1];
										$totalCost	 	 = $variableOprtnEntry[2];
										$updateNewTotalCost = $newTotalCost + $actualCost;
										$updateTotalCost    = $totalCost + $actualCost;
										//Update m_production_variable_marketing table
										$updateVariableOprtn = $productionOperationObj->updateVariableOperatnCost($variableId,$updateNewTotalCost,$updateTotalCost);
									}
									else
									{
										$addVariableOprtn = $productionOperationObj->addProductionVariableOperation($department,$actualCost,$actualCost,$userId);
									}
								}
							}
						}
					}
					else
					{
						
						$getDepartment  	= 	$departmentMasterObj->getDepartmentType($hidDepartment);
						if($hidType == "fixed")
						{
							if($getDepartment[0] == "production")
							{
								$fixedDeptEntry = $productionPowerObj->checkFixedDeptEntry($hidDepartment);
								$updateNewTotalCost = 0;
								$updateTotalCost = 0;
								if($fixedDeptEntry!="")
								{	
									$fixedId 		 = $fixedDeptEntry[0];
									$newTotalCost 	 = $fixedDeptEntry[1];
									$totalCost	 	 = $fixedDeptEntry[2];
									$updateNewTotalCost = $newTotalCost - $hidActualCost;
									$updateTotalCost    = $totalCost - $hidActualCost;
									//Update m_production_fixed_manpower table
									$updateFixedPrdtn = $productionPowerObj->updateFixedPrdtnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
								}
							}
							else if($getDepartment[0] == "marketing")
							{
								$fixedMktngEntry = $productionMarketingCostObj->checkFixedMktngEntry($hidDepartment);
								$updateNewTotalCost = 0;
								$updateTotalCost = 0;
								if($fixedMktngEntry!="")
								{	$fixedId 		 = $fixedMktngEntry[0];
									$newTotalCost 	 = $fixedMktngEntry[1];
									$totalCost	 	 = $fixedMktngEntry[2];
									$updateNewTotalCost = $newTotalCost - $hidActualCost;
									$updateTotalCost    = $totalCost - $hidActualCost;
									//Update m_production_fixed_marketing table
									$updateFixedMktng = $productionMarketingCostObj->updateFixedMktngCost($fixedId,$updateNewTotalCost,$updateTotalCost);
								}
							}
							else if($getDepartment[0] == "operation")
							{
								$fixedOprtnEntry = $productionOperationObj->checkFixedOperatnEntry($hidDepartment);
								$updateNewTotalCost = 0;
								$updateTotalCost = 0;
								if($fixedOprtnEntry!="")
								{	$fixedId 		 = $fixedOprtnEntry[0];
									$newTotalCost 	 = $fixedOprtnEntry[1];
									$totalCost	 	 = $fixedOprtnEntry[2];
									$updateNewTotalCost = $newTotalCost - $hidActualCost;
									$updateTotalCost    = $totalCost - $hidActualCost;
									//Update m_production_fixed_marketing table
									$updateFixedOprtn = $productionOperationObj->updateFixedOperatnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
								}
							}
						}
						else
						{
							if($getDepartment[0] == "production")
							{
								$variableDeptEntry = $productionPowerObj->checkVariableDeptEntry($hidDepartment);
								$updateNewTotalCost = 0;
								$updateTotalCost = 0;
								if($variableDeptEntry!="")
								{	$variableId 	 = $variableDeptEntry[0];
									$newTotalCost 	 = $variableDeptEntry[1];
									$totalCost	 	 = $variableDeptEntry[2];
									$updateNewTotalCost = $newTotalCost - $hidActualCost;
									$updateTotalCost    = $totalCost - $hidActualCost;
									//Update m_production_variable_manpower table
									$updateVariablePrdtn = $productionPowerObj->updateVariablePrdtnCost($variableId,$updateNewTotalCost,$updateTotalCost);
								}
							}
							else if($getDepartment[0] == "marketing")
							{
								$variableMktngEntry = $productionMarketingCostObj->checkVariableMktngEntry($hidDepartment);
								$updateNewTotalCost = 0;
								$updateTotalCost = 0;
								if($variableMktngEntry!="")
								{	$variableId 	 = $variableMktngEntry[0];
									$newTotalCost 	 = $variableMktngEntry[1];
									$totalCost	 	 = $variableMktngEntry[2];
									$updateNewTotalCost = $newTotalCost - $hidActualCost;
									$updateTotalCost    = $totalCost - $hidActualCost;
									//Update m_production_variable_marketing table
									$updateVariableMktng = $productionMarketingCostObj->updateVariableMktngCost($variableId,$updateNewTotalCost,$updateTotalCost);
								}
							}
							else if($getDepartment[0] == "operation")
							{
								$variableOprtnEntry = $productionOperationObj->checkVariableOperatnEntry($hidDepartment);
								$updateNewTotalCost = 0;
								$updateTotalCost = 0;
								if($variableOprtnEntry!="")
								{	$variableId 	 = $variableOprtnEntry[0];
									$newTotalCost 	 = $variableOprtnEntry[1];
									$totalCost	 	 = $variableOprtnEntry[2];
									$updateNewTotalCost = $newTotalCost - $hidActualCost;
									$updateTotalCost    = $totalCost - $hidActualCost;
									//Update m_production_variable_marketing table
									$updateVariableOprtn = $productionOperationObj->updateVariableOperatnCost($variableId,$updateNewTotalCost,$updateTotalCost);
								}
							}
						}
						
						
						$getNewDepartment  	= 	$departmentMasterObj->getDepartmentType($department);
						if($type == "fixed")
						{
							if($getNewDepartment[0] == "production")
							{
								$fixedDeptEntry = $productionPowerObj->checkFixedDeptEntry($department);
								$updateNewTotalCost = 0;
								$updateTotalCost = 0;
								if($fixedDeptEntry!="")
								{	
									$fixedId 		 = $fixedDeptEntry[0];
									$newTotalCost 	 = $fixedDeptEntry[1];
									$totalCost	 	 = $fixedDeptEntry[2];
									$updateNewTotalCost = $newTotalCost + $actualCost;
									$updateTotalCost    = $totalCost + $actualCost;
									//Update m_production_fixed_manpower table
									$updateFixedPrdtn = $productionPowerObj->updateFixedPrdtnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
								}
								else
								{
									$addFixedPrdtn = $productionPowerObj->addProductionFixedPower($department,$actualCost,$actualCost,$userId);
								}
							}
							else if($getNewDepartment[0] == "marketing")
							{
								$fixedMktngEntry = $productionMarketingCostObj->checkFixedMktngEntry($department);
								$updateNewTotalCost = 0;
								$updateTotalCost = 0;
								if($fixedMktngEntry!="")
								{	$fixedId 		 = $fixedMktngEntry[0];
									$newTotalCost 	 = $fixedMktngEntry[1];
									$totalCost	 	 = $fixedMktngEntry[2];
									$updateNewTotalCost = $newTotalCost + $actualCost;
									$updateTotalCost    = $totalCost + $actualCost;
									//Update m_production_fixed_marketing table
									$updateFixedMktng = $productionMarketingCostObj->updateFixedMktngCost($fixedId,$updateNewTotalCost,$updateTotalCost);
								}
								else
								{
									$addFixedMktng = $productionMarketingCostObj->addProductionFixedMarketing($department,$actualCost,$actualCost,$userId);
								}
							}
							else if($getNewDepartment[0] == "operation")
							{
								$fixedOprtnEntry = $productionOperationObj->checkFixedOperatnEntry($department);
								$updateNewTotalCost = 0;
								$updateTotalCost = 0;
								if($fixedOprtnEntry!="")
								{	$fixedId 		 = $fixedOprtnEntry[0];
									$newTotalCost 	 = $fixedOprtnEntry[1];
									$totalCost	 	 = $fixedOprtnEntry[2];
									$updateNewTotalCost = $newTotalCost + $actualCost;
									$updateTotalCost    = $totalCost + $actualCost;
									//Update m_production_fixed_marketing table
									$updateFixedOprtn = $productionOperationObj->updateFixedOperatnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
								}
								else
								{
									$addFixedOprtn = $productionOperationObj->addProductionFixedOperation($department,$actualCost,$actualCost,$userId);
								}
							}
						}
						else
						{
							if($getNewDepartment[0] == "production")
							{
								$variableDeptEntry = $productionPowerObj->checkVariableDeptEntry($department);
								$updateNewTotalCost = 0;
								$updateTotalCost = 0;
								if($variableDeptEntry!="")
								{	$variableId 	 = $variableDeptEntry[0];
									$newTotalCost 	 = $variableDeptEntry[1];
									$totalCost	 	 = $variableDeptEntry[2];
									$updateNewTotalCost = $newTotalCost + $actualCost;
									$updateTotalCost    = $totalCost + $actualCost;
									//Update m_production_variable_manpower table
									$updateVariablePrdtn = $productionPowerObj->updateVariablePrdtnCost($variableId,$updateNewTotalCost,$updateTotalCost);
								}
								else
								{
									$addVariablePrdtn = $productionPowerObj->addProductionVariablePower($department,$actualCost,$actualCost,$userId);
								}
							}
							else if($getNewDepartment[0] == "marketing")
							{
								$variableMktngEntry = $productionMarketingCostObj->checkVariableMktngEntry($department);
								$updateNewTotalCost = 0;
								$updateTotalCost = 0;
								if($variableMktngEntry!="")
								{	$variableId 	 = $variableMktngEntry[0];
									$newTotalCost 	 = $variableMktngEntry[1];
									$totalCost	 	 = $variableMktngEntry[2];
									$updateNewTotalCost = $newTotalCost + $actualCost;
									$updateTotalCost    = $totalCost + $actualCost;
									//Update m_production_variable_marketing table
									$updateVariableMktng = $productionMarketingCostObj->updateVariableMktngCost($variableId,$updateNewTotalCost,$updateTotalCost);
								}
								else
								{
									$addVariableMktng = $productionMarketingCostObj->addProductionVariableMarketing($department,$actualCost,$actualCost,$userId);
								}
							}
							else if($getNewDepartment[0] == "operation")
							{
								$variableOprtnEntry = $productionOperationObj->checkVariableOperatnEntry($department);
								$updateNewTotalCost = 0;
								$updateTotalCost = 0;
								if($variableOprtnEntry!="")
								{	$variableId 	 = $variableOprtnEntry[0];
									$newTotalCost 	 = $variableOprtnEntry[1];
									$totalCost	 	 = $variableOprtnEntry[2];
									$updateNewTotalCost = $newTotalCost + $actualCost;
									$updateTotalCost    = $totalCost + $actualCost;
									//Update m_production_variable_marketing table
									$updateVariableOprtn = $productionOperationObj->updateVariableOperatnCost($variableId,$updateNewTotalCost,$updateTotalCost);
								}
								else
								{
									$addVariableOprtn = $productionOperationObj->addProductionVariableOperation($department,$actualCost,$actualCost,$userId);
								}
							}
						}
						
					}	*/				
				}
				else
				{
					//echo "hii am out";
					$editMode	=	true;
					$errDate	=	$msg_failStaffDate;
				}
			}
		}
	
		if ($staffRecUptd || $staffRecIns) 
		{
			$sessObj->createSession("displayMsg",$msg_succStaffUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateStaff.$selection);
		} 
		else 
		{
			$editMode	=	true;
			$err		=	$msg_failStaffUpdate;
		}
		$staffRecUptd	=	false;
	}


	# Delete staff
	if ($p["cmdDelete"]!="") 
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$staffId	=	$p["delId_".$i];
			if ($staffId!="") 
			{
				// Checking the selected fish is link with any other process
				$staffRcrd = $staffMasterObj->fetchStaffDetail($staffId);
				if($staffRcrd!="")
				{
					$deptId 	= $staffRcrd[0];
					$staffType 	= $staffRcrd[1];
					$actualCost = $staffRcrd[2];
					$deptType 	= $staffRcrd[3];
					
					//Checking whether this department exists in corresponding production table based on staff type(fixed/variable) and department type(production/marketing/operation). If exists, then Update table.
					if($staffType == "fixed")
					{
						if($deptType == "production")
						{
							$fixedDeptEntry = $productionPowerObj->checkFixedDeptEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($fixedDeptEntry!="")
							{	
								$fixedId 		 = $fixedDeptEntry[0];
								$newTotalCost 	 = $fixedDeptEntry[1];
								$totalCost	 	 = $fixedDeptEntry[2];
								$updateNewTotalCost = $newTotalCost - $actualCost;
								$updateTotalCost    = $totalCost - $actualCost;
								//Update m_production_fixed_manpower table
								$updateFixedPrdtn = $productionPowerObj->updateFixedPrdtnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
							}
						}
						else if($deptType == "marketing")
						{
							$fixedMktngEntry = $productionMarketingCostObj->checkFixedMktngEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($fixedMktngEntry!="")
							{	$fixedId 		 = $fixedMktngEntry[0];
								$newTotalCost 	 = $fixedMktngEntry[1];
								$totalCost	 	 = $fixedMktngEntry[2];
								$updateNewTotalCost = $newTotalCost - $actualCost;
								$updateTotalCost    = $totalCost - $actualCost;
								//Update m_production_fixed_marketing table
								$updateFixedMktng = $productionMarketingCostObj->updateFixedMktngCost($fixedId,$updateNewTotalCost,$updateTotalCost);
							}
						}
						else if($deptType == "operation")
						{
							$fixedOprtnEntry = $productionOperationObj->checkFixedOperatnEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($fixedOprtnEntry!="")
							{	$fixedId 		 = $fixedOprtnEntry[0];
								$newTotalCost 	 = $fixedOprtnEntry[1];
								$totalCost	 	 = $fixedOprtnEntry[2];
								$updateNewTotalCost = $newTotalCost - $actualCost;
								$updateTotalCost    = $totalCost - $actualCost;
								//Update m_production_fixed_marketing table
								$updateFixedOprtn = $productionOperationObj->updateFixedOperatnCost($fixedId,$updateNewTotalCost,$updateTotalCost);
							}
						}
					}
					else
					{
						if($deptType == "production")
						{
							$variableDeptEntry = $productionPowerObj->checkVariableDeptEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($variableDeptEntry!="")
							{	$variableId 	 = $variableDeptEntry[0];
								$newTotalCost 	 = $variableDeptEntry[1];
								$totalCost	 	 = $variableDeptEntry[2];
								$updateNewTotalCost = $newTotalCost - $actualCost;
								$updateTotalCost    = $totalCost - $actualCost;
								//Update m_production_variable_manpower table
								$updateVariablePrdtn = $productionPowerObj->updateVariablePrdtnCost($variableId,$updateNewTotalCost,$updateTotalCost);
							}
						}
						else if($deptType == "marketing")
						{
							$variableMktngEntry = $productionMarketingCostObj->checkVariableMktngEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($variableMktngEntry!="")
							{	$variableId 	 = $variableMktngEntry[0];
								$newTotalCost 	 = $variableMktngEntry[1];
								$totalCost	 	 = $variableMktngEntry[2];
								$updateNewTotalCost = $newTotalCost - $actualCost;
								$updateTotalCost    = $totalCost - $actualCost;
								//Update m_production_variable_marketing table
								$updateVariableMktng = $productionMarketingCostObj->updateVariableMktngCost($variableId,$updateNewTotalCost,$updateTotalCost);
							}
						}
						else if($deptType == "operation")
						{
							$variableOprtnEntry = $productionOperationObj->checkVariableOperatnEntry($deptId);
							$updateNewTotalCost = 0;
							$updateTotalCost = 0;
							if($variableOprtnEntry!="")
							{	$variableId 	 = $variableOprtnEntry[0];
								$newTotalCost 	 = $variableOprtnEntry[1];
								$totalCost	 	 = $variableOprtnEntry[2];
								$updateNewTotalCost = $newTotalCost - $actualCost;
								$updateTotalCost    = $totalCost - $actualCost;
								//Update m_production_variable_marketing table
								$updateVariableOprtn = $productionOperationObj->updateVariableOperatnCost($variableId,$updateNewTotalCost,$updateTotalCost);
							}
						}
					}					
				}
				$staffRecDel = $staffMasterObj->deleteStaff($staffId);	
			}
		}
		if ($staffRecDel) 
		{
			$sessObj->createSession("displayMsg",$msg_succDelStaff);
			$sessObj->createSession("nextPage",$url_afterDelStaff.$selection);
		} 
		else 
		{
			$errDel	=	$msg_failDelStaff;
		}
		$staffRecDel	=	false;
		
		

	}
	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All Fishes		
	$staffMasterRecords	=	$staffMasterObj->fetchAllPagingRecords($offset, $limit);
	$staffMasterSize		=	sizeof($staffMasterRecords);
	
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($staffMasterObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
		
	/*
	# List all Fish Category;
	$sourceRecords = array();
	//if ($addMode || $editMode) $categoryRecords	= $fishcategoryObj->fetchAllRecords();
	if ($addMode || $editMode) { 
		$categoryRecords	= $fishcategoryObj->fetchAllRecordscategoryActive(); 
		$sourceRecords	    = $staffMasterObj->fetchAllSourceRecords();
	}
	*/

	$departmentRecords=$departmentMasterObj->fetchAllRecordsDepartmentactive();
	$proposedCost=$employeeCostCalculationObj->getProposedCost();
	$functionsRec=$staffRoleMasterObj->fetchAllRecordsRoleactive();
	//printr($proposedCost);
	if ($editMode) $heading = $label_editStaff;
	else $heading = $label_addStaff;

	//$help_lnk="help/hlp_addFishMaster.html";

	$ON_LOAD_PRINT_JS	= "libjs/staffmaster.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");		
?>

<form name="frmStaffMaster" action="StaffMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<!--<tr><td height="10" align="center"><a href="FishCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr>-->
		<? if($errDate!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$errDate;?></td>			
		</tr>
		<?}?>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>			
		</tr>
		<?}?>
		<?
			if( ($editMode || $addMode) && $disabled) {
		?>
		<tr style="display:none;">
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
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
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">
													<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('StaffMaster.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddStaff(document.frmStaffMaster);">
												</td>
												<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('StaffMaster.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAddStaff" class="button" value=" Add " onClick="return validateAddStaff(document.frmStaffMaster);">		
												</td>
												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StaffMaster.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddStaff(document.frmStaffMaster);">
												</td>
												<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StaffMaster.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAddStaff" class="button" value=" Add " onClick="return validateAddStaff(document.frmStaffMaster);">		
												</td>
												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
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
		<?
		}
		# Listing Fish Starts
		?>
		<tr>
			<td height="10" align="center" ></td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="Staff Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" align="center">
										<table width="50%">
										<?
											if( $editMode || $addMode) {
										?>
											<tr>
												<td>
													<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%">
														<tr>
															<td>
																<!-- Form fields start -->
																<?php			
																	$entryHead = $heading;
																	require("template/rbTop.php");
																?>
																<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
																	<tr>
																		<td width="1" ></td>
																		<td colspan="2" >
																			<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
																				<tr>
																					<td colspan="2" height="10" ></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>
																					<td colspan="2" align="center">
																						<input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('StaffMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddStaff(document.frmStaffMaster);">
																					</td>
																					<?} else{?>
																					<td  colspan="2" align="center">
																						<input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('StaffMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAddStaff" class="button" value=" Add " onClick="return validateAddStaff(document.frmStaffMaster);">		
																					</td>
																					<?}?>
																				</tr>
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap >*Name</td>
																					<td><INPUT TYPE="text" NAME="name" size="15" value="<?=$name;?>"></td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap >*Function</td>
																					<td>
																						<select name="functions" id="functions">
																							<option value="">--Select--</option>
																							<? foreach($functionsRec as $fr)
																							{
																								$frId=$fr[0];
																								$frName=$fr[1];
																								($functions==$frId)?$sel="selected":$sel="";
																							?>
																							<option value="<?=$frId?>" <?=$sel?>><?=$frName?></option>
																							<? 
																							} 
																							?>
																						</select>
																						<input type="hidden" name="hidFunctions" id="hidFunctions" value="<?=$functions?>" />
																					</td>
																				</tr>								
																				<tr>
																					<td class="fieldName" nowrap >*Salary</td>
																					<td><INPUT TYPE="text"  name="cost" id="cost" size="15" value="<?=$cost;?>" onkeyUp="totalCost();"	autocomplete="off" onkeypress="return isNumber (event);">
																						<INPUT TYPE="hidden" name="proposedCost" id="proposedCost" size="15" value=<?=$proposedCost?>>
																					</td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap >Allowance</td>
																					<td><INPUT TYPE="text"  name="allowance" id="allowance" size="15" value="<?=$allownce;?>" onkeyUp="totalCost();"	autocomplete="off" onkeypress="return isNumber (event);">
																					</td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap >*Effective Date</td>
																					<td><INPUT TYPE="text" name="effectiveDate" id="effectiveDate" size="15" value="<?=$effectiveDate;?>" onfocus="displayCalendar();"></td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap >*Actual cost</td>
																					<td><INPUT TYPE="text" name="actualCost" id="actualCost" size="15" value="<?=$actualCost;?>" readonly></td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap >*Type</td>
																					<td>
																						<select name="type" id="type">
																							<option value="">--Select-</option>
																							<option value="variable" <?=$selvar?>>Variable</option>
																							<option value="fixed" <?=$selfix?>>Fixed</option>
																						</select>
																						<input type="hidden" name="hidType" id="hidType" value="<?=$type?>" />
																					</td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap >*Department</td>
																					<td>
																						<select name="department" id="department">
																							<option value="">--select--</option>
																							<?
																							foreach($departmentRecords as $dr)
																							{
																								$departmentIds		=	$dr[0];
																								$departmentName	=	stripSlash($dr[1]);
																								$selected = ($departmentIds==$departmentId)?"selected":""
																								?>
																							<option value="<?=$departmentIds?>" <?=$selected?>><?=$departmentName?></option>
																							<? }?>
																						</select>
																						<input type="hidden" name="hidDepartment" id="hidDepartment" value="<?=$departmentId?>" />
																						<input type="hidden" name="hidActualCost" id="hidActualCost" value="<?=$actualCost?>" />
																					</td>
																				</tr>
																				<tr>
																					<td colspan="2"  height="10" ><input type="hidden" name="hidStaffId" value="<?=$staffId;?>"></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>
																					<td colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StaffMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddStaff(document.frmStaffMaster);">	
																					</td>
																					<?} else{?>
																					<td  colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StaffMaster.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAddStaff" class="button" value=" Add " onClick="return validateAddStaff(document.frmStaffMaster);">	
																					</td>
																					<?}?>
																				</tr>
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
																<?php
																	require("template/rbBottom.php");
																?>
															</td>
														</tr>
													</table>
													<!-- Form fields end   -->
												</td>
											</tr>	
											<?
												}			
												# Listing Fish Starts
											?>
										</table>
									</td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$staffMasterSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><?}?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStaffMaster.php',700,600);"><? }?></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="4" height="5" ></td>
								</tr>
								<?
								if($errDel!="")
								{
								?>
								<tr>
									<td colspan="4" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
								}
								?>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table  cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">							
										<?
										if( sizeof($staffMasterRecords) > 0 )
										{
											$i	=	0;
										?>
										<thead>
											<? if($maxpage>1){?>
											<tr>
												<td colspan="6" align="right" style="padding-right:10px;">
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
														$nav.= " <a href=\"StaffMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
														//echo $nav;
														}
													}
													if ($pageNo > 1)
													{
													$page  = $pageNo - 1;
													$prev  = " <a href=\"StaffMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
													}
													else
													{
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage)
													{
													$page = $pageNo + 1;
													$next = " <a href=\"StaffMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
										<? }?>
										<tr >
											<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
											<th>Name</th>
											<th nowrap>Function</th>
											<th nowrap>Salary </th>
											<th nowrap>Allowance</th>
											<th nowrap>Actual Cost</th>
											<? if($edit==true){?>	<th class="listing-head"></th><? }?>
											<? if($confirmF==true){?>	<th class="listing-head"></th><? }?>
										</tr>
									</thead>
									<tbody>
									<?
										$displayStatus = "";
										foreach($staffMasterRecords as $sr)
										{
											$i++;
											$staffId		=	$sr[0];
											$staffName	=	stripSlash($sr[1]);
											$staffFunction=	stripSlash($sr[10]);
											$staffCost	=	$sr[3];			
											$active=$sr[4];
											$actualCost	=	$sr[8];	
											$allowance  =   $sr[9];
											//echo "existing count is $existingcount";
											//echo $confirmF;
														
									?>
										<tr   <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>   >
											<td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$staffId;?>" class="chkBox"></td>
											<td class="listing-item" nowrap >&nbsp;&nbsp;<?=$staffName;?></td>
											<td class="listing-item" nowrap="nowrap">&nbsp;&nbsp;<?=$staffFunction;?>&nbsp;</td>
											<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$staffCost?></td>
											<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$allowance?></td>
											<td class="listing-item" nowrap>&nbsp;&nbsp;<?=$actualCost?></td>
											<? if($edit==true){?>
											<td class="listing-item" width="45" align="center"><?php if ($active!=1) { ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$staffId;?>,'editId'); this.form.action='StaffMaster.php';" ><?php }
											?>
											</td> 
											<? }?>
											<? if ($confirmF==true){?>
											<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
											<?php if ($active==0){ ?>
											<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$staffId;?>,'confirmId');" >
											<?php } else if ($active==1){ if ($existingcount==0) {?>
											
											<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$staffId;?>,'confirmId');" >
											<?php } ?>
											<?php }?>
											<? }?>
											</td>
										</tr>
										<?
											}
										?>
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="">
											<input type="hidden" name="confirmId" value="">
										<? if($maxpage>1){?>
										<tr>
											<td align="right" style="padding-right:10px" colspan="6" class="navRow">
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
														$nav.= " <a href=\"StaffMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
													//echo $nav;
													}
												}
												if ($pageNo > 1)
												{
													$page  = $pageNo - 1;
													$prev  = " <a href=\"StaffMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
												}
												else
												{
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}
												if ($pageNo < $maxpage)
												{
													$page = $pageNo + 1;
													$next = " <a href=\"StaffMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
									</tbody>
									<?
									}
									else
									{
									?>
										<tr bgcolor="white">
											<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
										</tr>	
									<?
									}
									?>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="4" height="5" ></td>
							</tr>
							<tr >	
								<td colspan="4">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$staffMasterSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"  ><?}?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStaffMaster.php',700,600);"><? }?></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="4" height="5" ></td>
							</tr>								
						</table>
						<?php
							include "template/boxBR.php"
						?>
					</td>
				</tr>
			</table>
					<!-- Form fields end   -->
		</td>
	</tr>	
	<tr>
		<td height="10"></td>
	</tr>
			<!--<tr><td height="10" align="center"><a href="FishCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr>-->
</table>
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
