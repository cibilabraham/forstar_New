<?php
	require("include/include.php");
	require("lib/WeighmentDataSheet_ajax.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$selStockId		=	"";
	$userId		=	$sessObj->getValue("userId");
	
	
	$selection = "?pageNo=".$p["pageNo"]."&selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"];
	
	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		header("Location: ErrorPage.php");
		die();
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	
/*	$hidEditId = "";
	if ($p["editId"]!="") {
		$hidEditId = $p["editId"];
	} else {
		$hidEditId = $p["hidEditId"];
	}
*/
	

	/*$phttag='{"tag":{"Supplier":"2","Pond":"19","Species":"65","SupplyQnty":"45","DatasheetDate":"24/12/2014","RmLotId":"41","CertificateSize":"2"},"items": [{"RowCnt":0,"certificateNo":"8","availableQnty":"25","balanceQnty":"20","qntyStatus":"1"},{"RowCnt":1,"certificateNo":"4","availableQnty":"675","balanceQnty":"655","qntyStatus":"0"}]  }';
	$objt=json_decode($phttag);
	//printr($objt);
	foreach ($objt->items as $item )
	{
		$certificate=$item->certificateNo;
		$availbleQnty=$item->availableQnty;
		$balanceQnty=$item->balanceQnty;
		$qntyStatus=$item->qntyStatus;
		//echo "certificateNo=".$certificate;
		
		
	}*/
	//$item=$objt->items;
	//printr($item);

	# Add Stock Issuance Start 
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
		$data_sheet_date=Date("d/m/Y");
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;	
	}	
	
	
	
	
	//$data_sheet_date = date('d/m/Y');
	$rm_lot_id = '';
	$data_sheet_slno = '';
	if ($p["cmdAdd"]!="" ) 
	{  
		//echo $p["cmdAdd"];
		//die();
		$inputState=$p["cmdAdd"];
		$supplyBalanceQty="";
		$mstatus = $p['mstatus'];
		$IsFromDB = $p['IsFromDB'];
		$rm_lot_id = $p['rm_lot_id'];
		$data_sheet_slno = $p['data_sheet_slno'];
		$data_sheet_date = mysqlDateFormat($p['data_sheet_date']);
		$receiving_supervisor = $p['receiving_supervisor'];
		$total_quantity = $p['total_quantity'];
		$procurementAvailable = $p['procurementAvailable'];
		$number_gen_id =	$p["number_gen_id"];
		if(isset($p['editWeighmentId']) && $p['editWeighmentId'] != '')
		{
			$objWeighmentDataSheet->updateWeightmentProcurementValues($p['editWeighmentId'],$rm_lot_id,$data_sheet_slno,$data_sheet_date,$receiving_supervisor,$total_quantity,$procurementAvailable,$userId );
			$lastId = $p['editWeighmentId'];//$databaseConnect->getLastInsertedId();
		}
		elseif($p['editWeighmentId']=="")
		{	
			//avoid duplicate entry
			$duplicate= $objWeighmentDataSheet->checkDuplicate($rm_lot_id);
			if(sizeof($duplicate)>0)
			{
				//
				$err = "Failed to add weightment data sheet. Please make sure the request number you have entered is not duplicate. ";
			}
			else
			{
				$objWeighmentDataSheet->addWeightmentProcurementValue($rm_lot_id,$data_sheet_slno,$data_sheet_date,$receiving_supervisor,$total_quantity,$procurementAvailable,$userId,$number_gen_id);
				$lastId = $databaseConnect->getLastInsertedId();
			}
		}
		
		#-----------------------------------------------------------------
				# insert last generated weightment number to manage chellan
		//$data_sheet_slno = $p["data_sheet_slno"];
		preg_match('/\d+/', $data_sheet_slno, $numMatch);
		$lastnum = $numMatch[0];
		$rmlastIdInsert	=$manageChallanObj->lastGeneratedProcurementId($lastnum,$number_gen_id);
		
		$supplierName       = $p['supplierName'];
		$pondName           = $p['pondName'];
		$procurementCenter 	= $p['procurementCenter'];
		$product_species    = $p['product_species'];
		$process_code       = $p['process_code'];
		$quality            = $p['quality'];
		$countCode          = $p['count_code'];
		$weight             = $p['weight'];
		$soft_precent       = $p['soft_precent'];
		$soft_weight        = $p['soft_weight'];
		$total_soft         = $p['total_soft'];
		$weightmentId		= $p['weightmentId'];
		$newData = $p['newData'];
		//$weightmentId=$p['weightmentId'];
		if(sizeof($countCode) > 0)
		{
			for($i=0; $i<sizeof($countCode);  $i++)
			{ //echo "hui";
			//echo $weightmentId[$i].'---'.$mstatus[$i].'<br/>';
			
				//if($mstatus[$i] != 'N' && $weightmentId[$i] == '')
				if($mstatus[$i] != 'N' &&  $inputState == 'Add')
				{
					//echo "hii";
					$weightmentIns=$objWeighmentDataSheet->addWeightmentSupplierProcurementValue($lastId, $supplierName[$i],$pondName[$i],$procurementCenter[$i],$product_species[$i],$process_code[$i],$quality[$i],$countCode[$i],$weight[$i],$soft_precent[$i],$soft_weight[$i],$newData[$i]);
					$weightmentEntryId = $databaseConnect->getLastInsertedId();
					
					###added on 8-1-2015

						$phtTagData=$p["phtTagData"][$i];
						//echo $j."----".$phtTagData.'<br/>';
						if($phtTagData!="")
						{
						$objt=json_decode($phtTagData);
						$supplyQty=$objt->tag->SupplyQnty;
						$rmLotId=$objt->tag->RmLotId;
						$certificateSize=$objt->tag->CertificateSize;
						//echo $supplyQty.'---'.$rmLotId.'---'.$certificateSize;
						//printr($objt->items);
							foreach($objt->items as $item )
							{	
								/*$availbleQnty=$item->availableQnty;
								$balanceQnty=$item->balanceQnty;
								$qntyStatus=$item->qntyStatus;	*/
								if($supplyBalanceQty=="")
								{
									$certificateId=$item->certificateNo;
									$certificateQty=$objWeighmentDataSheet->getCerificateQty($certificateId);
									$monitoringId=$objWeighmentDataSheet->getMonitoringId($certificateId);
									if($supplyQty>=$certificateQty)
									{
										$phtQuantity=$certificateQty;
										$setoffQuantity=$certificateQty;
										$balanceQty=0;
										//$weightmentEntryId='';
										$supplyQtyVal=$supplyQty;
										$adjustedQty=$certificateQty;
										$supplyBalanceQty=$supplyQty-$phtQuantity;
										//echo "pht_quantity".$pht_quantity."setoff_quantity".$setoff_quantity."balanceQty".$balanceQty."lastId".$lastId."supply_qty".$supply_qty."adjusted_qty".$adjusted_qty."supplyBalanceQty".$supplyBalanceQty.'<br/>';
										
									}
									else if($certificateQty>$supplyQty)
									{
										$phtQuantity=$certificateQty;
										$setoffQuantity=$supplyQty;
										$balanceQty=$certificateQty-$supplyQty;
										//weightmentEntryId='';
										$supplyQtyVal=$supplyQty;
										$adjustedQty=$supplyQty;
										$supplyBalanceQty=0;
										//echo "pht_quantity".$pht_quantity."setoff_quantity".$setoff_quantity."balanceQty".$balanceQty."lastId".$lastId."supply_qty".$supply_qty."adjusted_qty".$adjusted_qty."supplyBalanceQty".$supplyBalanceQty.'<br/>';
								
									}
									$certificateIns = $objWeighmentDataSheet->addPhtCertificateQuantity($monitoringId,$certificateId,$phtQuantity,$setoffQuantity,$balanceQty,$weightmentEntryId,$rmLotId,$supplyQtyVal,$adjustedQty,$supplyBalanceQty);

									//echo "pht_quantity".$pht_quantity."setoff_quantity".$setoff_quantity."balanceQty".$balanceQty."lastId".$lastId."supply_qty".$supply_qty."adjusted_qty".$adjusted_qty."supplyBalanceQty".$supplyBalanceQty.'<br/>';
								}
								else if($supplyBalanceQty!="")
								{
									$certificateId=$item->certificateNo;
									$certificateQty=$objWeighmentDataSheet->getCerificateQty($certificateId);
									$monitoringId=$objWeighmentDataSheet->getMonitoringId($certificateId);
									if($supplyBalanceQty>=$certificateQty)
									{
										$phtQuantity=$certificateQty;
										$setoffQuantity=$certificateQty;
										$balanceQty=0;
										//weightmentEntryId='';
										$supplyQtyVal=$supplyBalanceQty;
										$adjustedQty=$certificateQty;
										$supplyBalanceQty=$supplyBalanceQty-$phtQuantity;
									}
									else if($certificateQty>$supplyBalanceQty)
									{
										$phtQuantity=$certificateQty;
										$setoffQuantity=$supplyBalanceQty;
										$balanceQty=$certificateQty-$supplyBalanceQty;
										//weightmentEntryId='';
										$supplyQtyVal=$supplyBalanceQty;
										$adjustedQty=$supplyBalanceQty;
										$supplyBalanceQty=0;
									}
									//echo "pht_quantity".$pht_quantity."setoff_quantity".$setoff_quantity."balanceQty".$balanceQty."lastId".$lastId."supply_qty".$supply_qty."adjusted_qty".$adjusted_qty."supplyBalanceQty".$supplyBalanceQty.'<br/>';
									$certificateIns = $objWeighmentDataSheet->addPhtCertificateQuantity($monitoringId,$certificateId,$phtQuantity,$setoffQuantity,$balanceQty,$weightmentEntryId,$rmLotId,$supplyQtyVal,$adjustedQty,$supplyBalanceQty);
								}
							}
						}
				}
				//else if($mstatus[$i] != 'N' && $weightmentId[$i] != '')
				else if($mstatus[$i] != 'N'  && $inputState == 'Update')
				{	
					if($weightmentId[$i]=="")
					{
						$weightmentIns=$objWeighmentDataSheet->addWeightmentSupplierProcurementValue($lastId, $supplierName[$i],$pondName[$i],$procurementCenter[$i],$product_species[$i],$process_code[$i],$quality[$i],$countCode[$i],$weight[$i],$soft_precent[$i],$soft_weight[$i],$newData[$i]);
						$phtTagData=$p["phtTagData"][$i];
						//echo $j."----".$phtTagData.'<br/>';
						if($phtTagData!="")
						{
						$objt=json_decode($phtTagData);
						$supplyQty=$objt->tag->SupplyQnty;
						$rmLotId=$objt->tag->RmLotId;
						$certificateSize=$objt->tag->CertificateSize;
						//echo $supplyQty.'---'.$rmLotId.'---'.$certificateSize;
						//printr($objt->items);
							foreach($objt->items as $item )
							{	
								/*$availbleQnty=$item->availableQnty;
								$balanceQnty=$item->balanceQnty;
								$qntyStatus=$item->qntyStatus;	*/
								if($supplyBalanceQty=="")
								{
									$certificateId=$item->certificateNo;
									$certificateQty=$objWeighmentDataSheet->getCerificateQty($certificateId);
									$monitoringId=$objWeighmentDataSheet->getMonitoringId($certificateId);
									if($supplyQty>=$certificateQty)
									{
										$phtQuantity=$certificateQty;
										$setoffQuantity=$certificateQty;
										$balanceQty=0;
										//$weightmentEntryId='';
										$supplyQtyVal=$supplyQty;
										$adjustedQty=$certificateQty;
										$supplyBalanceQty=$supplyQty-$phtQuantity;
										//echo "pht_quantity".$pht_quantity."setoff_quantity".$setoff_quantity."balanceQty".$balanceQty."lastId".$lastId."supply_qty".$supply_qty."adjusted_qty".$adjusted_qty."supplyBalanceQty".$supplyBalanceQty.'<br/>';
										
									}
									else if($certificateQty>$supplyQty)
									{
										$phtQuantity=$certificateQty;
										$setoffQuantity=$supplyQty;
										$balanceQty=$certificateQty-$supplyQty;
										//weightmentEntryId='';
										$supplyQtyVal=$supplyQty;
										$adjustedQty=$supplyQty;
										$supplyBalanceQty=0;
										//echo "pht_quantity".$pht_quantity."setoff_quantity".$setoff_quantity."balanceQty".$balanceQty."lastId".$lastId."supply_qty".$supply_qty."adjusted_qty".$adjusted_qty."supplyBalanceQty".$supplyBalanceQty.'<br/>';
								
									}
									$certificateIns = $objWeighmentDataSheet->addPhtCertificateQuantity($monitoringId,$certificateId,$phtQuantity,$setoffQuantity,$balanceQty,$weightmentEntryId,$rmLotId,$supplyQtyVal,$adjustedQty,$supplyBalanceQty);

									//echo "pht_quantity".$pht_quantity."setoff_quantity".$setoff_quantity."balanceQty".$balanceQty."lastId".$lastId."supply_qty".$supply_qty."adjusted_qty".$adjusted_qty."supplyBalanceQty".$supplyBalanceQty.'<br/>';
								}
								else if($supplyBalanceQty!="")
								{
									$certificateId=$item->certificateNo;
									$certificateQty=$objWeighmentDataSheet->getCerificateQty($certificateId);
									$monitoringId=$objWeighmentDataSheet->getMonitoringId($certificateId);
									if($supplyBalanceQty>=$certificateQty)
									{
										$phtQuantity=$certificateQty;
										$setoffQuantity=$certificateQty;
										$balanceQty=0;
										//weightmentEntryId='';
										$supplyQtyVal=$supplyBalanceQty;
										$adjustedQty=$certificateQty;
										$supplyBalanceQty=$supplyBalanceQty-$phtQuantity;
									}
									else if($certificateQty>$supplyBalanceQty)
									{
										$phtQuantity=$certificateQty;
										$setoffQuantity=$supplyBalanceQty;
										$balanceQty=$certificateQty-$supplyBalanceQty;
										//weightmentEntryId='';
										$supplyQtyVal=$supplyBalanceQty;
										$adjustedQty=$supplyBalanceQty;
										$supplyBalanceQty=0;
									}
									//echo "pht_quantity".$pht_quantity."setoff_quantity".$setoff_quantity."balanceQty".$balanceQty."lastId".$lastId."supply_qty".$supply_qty."adjusted_qty".$adjusted_qty."supplyBalanceQty".$supplyBalanceQty.'<br/>';
									$certificateIns = $objWeighmentDataSheet->addPhtCertificateQuantity($monitoringId,$certificateId,$phtQuantity,$setoffQuantity,$balanceQty,$weightmentEntryId,$rmLotId,$supplyQtyVal,$adjustedQty,$supplyBalanceQty);
								}
							}
						}	
					
					
					}
					else
					{

						$weightmentUpdate=$objWeighmentDataSheet->updateWeightmentSupplierProcurementValue($weightmentId[$i],$lastId, $supplierName[$i],$pondName[$i],$procurementCenter[$i],$product_species[$i],$process_code[$i],$quality[$i],$countCode[$i],$weight[$i],$soft_precent[$i],$soft_weight[$i],$newData[$i]);
						$weightmentEntryId =$weightmentId[$i];
						$weightNew=$weight[$i];
						$weightOld=$p["oldWeight"][$i];
						//echo "wtNw".$weightNew."wtOld".$weightOld;
						$phtTagData=$p["phtTagData"][$i];
						$supplyQtyNew=$weightNew-$weightOld;
						###added on 14-1-2015
						if($weightNew==$weightOld)
						{	
							if($phtTagData!="")
							{
								$objt=json_decode($phtTagData);
								$supplyQty=$objt->tag->SupplyQnty;
								$rmLotId=$objt->tag->RmLotId;
								$certificateSize=$objt->tag->CertificateSize;
								
								foreach($objt->items as $item )
								{	
									$certificateId=$item->certificateNo;
									$availableQnty=$item->availableQnty;
									$balanceQnty=$item->balanceQnty;
									$qntyStatus=$item->qntyStatus;
									$PhtMonitoringEntryId=$item->phtMonitoringEntryId;
									$WeightmentEntryIdEdit=$item->weightmentEntryId;
									if($PhtMonitoringEntryId!="" && $WeightmentEntryIdEdit!="")
									{
									}
									else if($PhtMonitoringEntryId=="" && $WeightmentEntryIdEdit=="")
									{
										if($supplyBalanceQty=="")
										{
											$certificateQty=$objWeighmentDataSheet->getCerificateQty($certificateId);
											$monitoringId=$objWeighmentDataSheet->getMonitoringId($certificateId);
											if($supplyQty>=$certificateQty)
											{
												$phtQuantity=$certificateQty;
												$setoffQuantity=$certificateQty;
												$balanceQty=0;
												//$weightmentEntryId='';
												$supplyQtyVal=$supplyQty;
												$adjustedQty=$certificateQty;
												$supplyBalanceQty=$supplyQty-$phtQuantity;
											}
											else if($certificateQty>$supplyQty)
											{
												$phtQuantity=$certificateQty;
												$setoffQuantity=$supplyQty;
												$balanceQty=$certificateQty-$supplyQty;
												//weightmentEntryId='';
												$supplyQtyVal=$supplyQty;
												$adjustedQty=$supplyQty;
												$supplyBalanceQty=0;
											}
											$certificateIns = $objWeighmentDataSheet->addPhtCertificateQuantity($monitoringId,$certificateId,$phtQuantity,$setoffQuantity,$balanceQty,$weightmentEntryId,$rmLotId,$supplyQtyVal,$adjustedQty,$supplyBalanceQty);

										}
										else if($supplyBalanceQty!="")
										{
											$certificateId=$item->certificateNo;
											$certificateQty=$objWeighmentDataSheet->getCerificateQty($certificateId);
											$monitoringId=$objWeighmentDataSheet->getMonitoringId($certificateId);
											if($supplyBalanceQty>=$certificateQty)
											{
												$phtQuantity=$certificateQty;
												$setoffQuantity=$certificateQty;
												$balanceQty=0;
												//weightmentEntryId='';
												$supplyQtyVal=$supplyBalanceQty;
												$adjustedQty=$certificateQty;
												$supplyBalanceQty=$supplyBalanceQty-$phtQuantity;
											}
											else if($certificateQty>$supplyBalanceQty)
											{
												$phtQuantity=$certificateQty;
												$setoffQuantity=$supplyBalanceQty;
												$balanceQty=$certificateQty-$supplyBalanceQty;
												//weightmentEntryId='';
												$supplyQtyVal=$supplyBalanceQty;
												$adjustedQty=$supplyBalanceQty;
												$supplyBalanceQty=0;
											}
											//echo "pht_quantity".$pht_quantity."setoff_quantity".$setoff_quantity."balanceQty".$balanceQty."lastId".$lastId."supply_qty".$supply_qty."adjusted_qty".$adjusted_qty."supplyBalanceQty".$supplyBalanceQty.'<br/>';
											$certificateIns = $objWeighmentDataSheet->addPhtCertificateQuantity($monitoringId,$certificateId,$phtQuantity,$setoffQuantity,$balanceQty,$weightmentEntryId,$rmLotId,$supplyQtyVal,$adjustedQty,$supplyBalanceQty);
										}		
									}
								}
							}
						}
						else if($weightNew>$weightOld)
						{
							$supplyBalanceQty='';
							if($phtTagData!="")
							{	
								$objt=json_decode($phtTagData);
								$supplyQty=$objt->tag->SupplyQnty;
								$rmLotId=$objt->tag->RmLotId;
								$certificateSize=$objt->tag->CertificateSize;
								$alreadyExist=0;
								foreach($objt->items as $item )
								{	
									
									$certificateId=$item->certificateNo;
									$availableQnty=$item->availableQnty;
									$balanceQnty=$item->balanceQnty;
									$qntyStatus=$item->qntyStatus;
									$PhtMonitoringEntryId=$item->phtMonitoringEntryId;
									$WeightmentEntryIdEdit=$item->weightmentEntryId;
									//echo "hii".$PhtMonitoringEntryId.'----'.$WeightmentEntryIdEdit.'<br/>';
									//die();
									
									if($PhtMonitoringEntryId!="" && $WeightmentEntryIdEdit!="")
									{
										$alreadyExist=1;
									}
									else if($PhtMonitoringEntryId=="" && $WeightmentEntryIdEdit=="" && $alreadyExist==0)
									{	
										if($supplyBalanceQty=="")
										{	
											
											$certificateQty=$objWeighmentDataSheet->getCerificateQty($certificateId);
											$monitoringId=$objWeighmentDataSheet->getMonitoringId($certificateId);
											if($supplyQtyNew>=$certificateQty)
											{
												$phtQuantity=$certificateQty;
												$setoffQuantity=$certificateQty;
												$balanceQty=0;
												//$weightmentEntryId='';
												$supplyQtyVal=$supplyQtyNew;
												$adjustedQty=$certificateQty;
												$supplyBalanceQty=$supplyQtyNew-$phtQuantity;
											}
											else if($certificateQty>$supplyQtyNew)
											{
												$phtQuantity=$certificateQty;
												$setoffQuantity=$supplyQtyNew;
												$balanceQty=$certificateQty-$supplyQtyNew;
												//weightmentEntryId='';
												$supplyQtyVal=$supplyQtyNew;
												$adjustedQty=$supplyQtyNew;
												$supplyBalanceQty=0;
											}
											$certificateIns = $objWeighmentDataSheet->addPhtCertificateQuantity($monitoringId,$certificateId,$phtQuantity,$setoffQuantity,$balanceQty,$weightmentEntryId,$rmLotId,$supplyQtyVal,$adjustedQty,$supplyBalanceQty);
										}
										else if($supplyBalanceQty!="")
										{
											$certificateId=$item->certificateNo;
											$certificateQty=$objWeighmentDataSheet->getCerificateQty($certificateId);
											$monitoringId=$objWeighmentDataSheet->getMonitoringId($certificateId);
											if($supplyBalanceQty>=$certificateQty)
											{
												$phtQuantity=$certificateQty;
												$setoffQuantity=$certificateQty;
												$balanceQty=0;
												//weightmentEntryId='';
												$supplyQtyVal=$supplyBalanceQty;
												$adjustedQty=$certificateQty;
												$supplyBalanceQty=$supplyBalanceQty-$phtQuantity;
											}
											else if($certificateQty>$supplyBalanceQty)
											{
												$phtQuantity=$certificateQty;
												$setoffQuantity=$supplyBalanceQty;
												$balanceQty=$certificateQty-$supplyBalanceQty;
												//weightmentEntryId='';
												$supplyQtyVal=$supplyBalanceQty;
												$adjustedQty=$supplyBalanceQty;
												$supplyBalanceQty=0;
											}
											//echo "pht_quantity".$pht_quantity."setoff_quantity".$setoff_quantity."balanceQty".$balanceQty."lastId".$lastId."supply_qty".$supply_qty."adjusted_qty".$adjusted_qty."supplyBalanceQty".$supplyBalanceQty.'<br/>';
											$certificateIns = $objWeighmentDataSheet->addPhtCertificateQuantity($monitoringId,$certificateId,$phtQuantity,$setoffQuantity,$balanceQty,$weightmentEntryId,$rmLotId,$supplyQtyVal,$adjustedQty,$supplyBalanceQty);
										}		
									}
									else if($PhtMonitoringEntryId=="" && $WeightmentEntryIdEdit=="" && $alreadyExist==1)
									{ //echo	$supplyQtyNew=$weightNew-$weightOld;
									
										if($supplyBalanceQty=="")
										{
											$certificateQty=$objWeighmentDataSheet->getCerificateQty($certificateId);
											$monitoringId=$objWeighmentDataSheet->getMonitoringId($certificateId);
											if($supplyQtyNew>=$certificateQty)
											{
												$phtQuantity=$certificateQty;
												$setoffQuantity=$certificateQty;
												$balanceQty=0;
												//$weightmentEntryId='';
												$supplyQtyVal=$supplyQtyNew;
												$adjustedQty=$certificateQty;
												$supplyBalanceQty=$supplyQtyNew-$phtQuantity;
											}
											else if($certificateQty>$supplyQtyNew)
											{
												$phtQuantity=$certificateQty;
												$setoffQuantity=$supplyQtyNew;
												$balanceQty=$certificateQty-$supplyQtyNew;
												//weightmentEntryId='';
												$supplyQtyVal=$supplyQtyNew;
												$adjustedQty=$supplyQtyNew;
												$supplyBalanceQty=0;
											}
											$certificateIns = $objWeighmentDataSheet->addPhtCertificateQuantity($monitoringId,$certificateId,$phtQuantity,$setoffQuantity,$balanceQty,$weightmentEntryId,$rmLotId,$supplyQtyVal,$adjustedQty,$supplyBalanceQty);

										}
										else if($supplyBalanceQty!="")
										{
											$certificateId=$item->certificateNo;
											$certificateQty=$objWeighmentDataSheet->getCerificateQty($certificateId);
											$monitoringId=$objWeighmentDataSheet->getMonitoringId($certificateId);
											if($supplyBalanceQty>=$certificateQty)
											{
												$phtQuantity=$certificateQty;
												$setoffQuantity=$certificateQty;
												$balanceQty=0;
												//weightmentEntryId='';
												$supplyQtyVal=$supplyBalanceQty;
												$adjustedQty=$certificateQty;
												$supplyBalanceQty=$supplyBalanceQty-$phtQuantity;
											}
											else if($certificateQty>$supplyBalanceQty)
											{
												$phtQuantity=$certificateQty;
												$setoffQuantity=$supplyBalanceQty;
												$balanceQty=$certificateQty-$supplyBalanceQty;
												//weightmentEntryId='';
												$supplyQtyVal=$supplyBalanceQty;
												$adjustedQty=$supplyBalanceQty;
												$supplyBalanceQty=0;
											}
											//echo "pht_quantity".$pht_quantity."setoff_quantity".$setoff_quantity."balanceQty".$balanceQty."lastId".$lastId."supply_qty".$supply_qty."adjusted_qty".$adjusted_qty."supplyBalanceQty".$supplyBalanceQty.'<br/>';
											$certificateIns = $objWeighmentDataSheet->addPhtCertificateQuantity($monitoringId,$certificateId,$phtQuantity,$setoffQuantity,$balanceQty,$weightmentEntryId,$rmLotId,$supplyQtyVal,$adjustedQty,$supplyBalanceQty);
										}		
									}
								}
							}
						
						}
						else if($weightNew<$weightOld)
						{	$supplyBalanceQty='';
							if($phtTagData!="")
							{	$supplyQty=$weightNew;
								$objt=json_decode($phtTagData);
								//$supplyQty=$objt->tag->SupplyQnty;
								$rmLotId=$objt->tag->RmLotId;
								$certificateSize=$objt->tag->CertificateSize;
								foreach($objt->items as $item )
								{	
									$certificateId=$item->certificateNo;
									$availableQnty=$item->availableQnty;
									$balanceQnty=$item->balanceQnty;
									$qntyStatus=$item->qntyStatus;
									$PhtMonitoringEntryId=$item->phtMonitoringEntryId;
									$WeightmentEntryIdEdit=$item->weightmentEntryId;
									if($PhtMonitoringEntryId!="" && $WeightmentEntryIdEdit!="")
									{	
										//$alreadyExist=1;
										$monitoringId=$objWeighmentDataSheet->getMonitoringId($certificateId);
										if($supplyBalanceQty=="")
										{
											if($supplyQty>=$availableQnty)
											{	//echo $supplyQty.'--'.$availableQnty;
												$supplyBalanceQty=$supplyQty-$availableQnty;
												//echo $supplyBalanceQty;
												//die();
											}
											else if($availableQnty>$supplyQty)
											{
												$phtQuantity=$availableQnty;
												$setoffQuantity=$supplyQty;
												$balanceQty=$availableQnty-$supplyQty;
												//weightmentEntryId='';
												$supplyQtyVal=$supplyQty;
												$adjustedQty=$supplyQty;
												$supplyBalanceQty=0;

													$certificateIns = $objWeighmentDataSheet->updatePhtCertificateQuantity($monitoringId,$certificateId,$phtQuantity,$setoffQuantity,$balanceQty,$weightmentEntryId,$rmLotId,$supplyQtyVal,$adjustedQty,$supplyBalanceQty,$PhtMonitoringEntryId);
											### for updating certificate pht quantity
											$certificateVal=$objWeighmentDataSheet->getPhtQuantityLessValue($phtcertificateId,$phtQty);
											if(sizeof($certificateVal)>0)
											{
												foreach($certificateVal as $certifyQty)
												{
													if($balQty=="")
													{
														$monId=$certifyQty[0];
														$setOff=$certifyQty[4];
														$balQty=$balanceQty-$setOff;
														$objWeighmentDataSheet->updatePhtMonitoringQnty($balanceQty,$setOff,$balQty,$monId);
													}
													else
													{
														$phtQt=$balQty;
														$monId=$certifyQty[0];
														$setOff=$certifyQty[4];
														$balQty=$balQty-$setOff;
														$objWeighmentDataSheet->updatePhtMonitoringQnty($phtQt,$setOff,$balQty,$monId);	
													}
												}
											}
											
											####getting pht cerificate with same weightment Id

											$weightmentPhtMonitoring=$objWeighmentDataSheet->getPhtMonitoringWeightmentId($weightmentEntryId,$PhtMonitoringEntryId);
											foreach($weightmentPhtMonitoring as $wtPhtMont)
											{	$phtMonId=$wtPhtMont[0];
												$phtcerificateId=$wtPhtMont[2];
												$phtQt=$wtPhtMont[3];
												$objWeighmentDataSheet->deletePhtMonitoring($PhtMonitoringEntryId);
												$certificateVal=$objWeighmentDataSheet->getPhtQuantityLessValue($phtcertificateId);
												if(sizeof($certificateVal)>0)
												{
													foreach($certificateVal as $certifyQty)
													{
														if($balQty=="")
														{
															$monId=$certifyQty[0];
															$setOff=$certifyQty[4];
															$balQty=$phtQt-$setOff;
															$objWeighmentDataSheet->updatePhtMonitoringQnty($phtQt,$setOff,$balQty,$monId);
														}
														else
														{
															$phtQt=$balQty;
															$monId=$certifyQty[0];
															$setOff=$certifyQty[4];
															$balQty=$balQty-$setOff;
															$objWeighmentDataSheet->updatePhtMonitoringQnty($phtQt,$setOff,$balQty,$monId);	
														}
													}
												}
											}
											}
											
										}
										else if($supplyBalanceQty!='')
										{
											if($supplyBalanceQty>$availableQnty)
											{	//echo "hii".$supplyBalanceQty.'--'.$availableQnty;
												$supplyBalanceQty=$supplyBalanceQty-$availableQnty;
												//echo "hii".$supplyBalanceQty;
												//die();
											}
											else if($availableQnty>$supplyBalanceQty)
											{	//echo "hii".$supplyBalanceQty;
												//die();
												$phtQuantity=$availableQnty;
												$setoffQuantity=$supplyBalanceQty;
												$balanceQty=$availableQnty-$supplyBalanceQty;
												//weightmentEntryId='';
												$supplyQtyVal=$supplyBalanceQty;
												$adjustedQty=$supplyBalanceQty;
												$supplyBalanceQty=0;

													$certificateIns = $objWeighmentDataSheet->updatePhtCertificateQuantity($monitoringId,$certificateId,$phtQuantity,$setoffQuantity,$balanceQty,$weightmentEntryId,$rmLotId,$supplyQtyVal,$adjustedQty,$supplyBalanceQty,$PhtMonitoringEntryId);
													//die();
													$balsQty='';
													### for updating certificate pht quantity
													$certificateVal=$objWeighmentDataSheet->getPhtQuantityLessValue($certificateId,$phtQty);
													//die();
													if(sizeof($certificateVal)>0)
													{
														foreach($certificateVal as $certifyQty)
														{
															if($balsQty=="")
															{
																$monId=$certifyQty[0];
																$setOff=$certifyQty[4];
																$balsQty=$balanceQty-$setOff;
																$objWeighmentDataSheet->updatePhtMonitoringQnty($balanceQty,$setOff,$balsQty,$monId);
															}
															else
															{
																$phtQt=$balsQty;
																$monId=$certifyQty[0];
																$setOff=$certifyQty[4];
																$balsQty=$balsQty-$setOff;
																$objWeighmentDataSheet->updatePhtMonitoringQnty($phtQt,$setOff,$balsQty,$monId);	
															}
														}
													}
														$objWeighmentDataSheet->deletePhtMonitoring($phtMonId);
														####getting pht cerificate with same weightment Id

														$weightmentPhtMonitoring=$objWeighmentDataSheet->getPhtMonitoringWeightmentId($weightmentEntryId,$PhtMonitoringEntryId);
														$balQty='';
														foreach($weightmentPhtMonitoring as $wtPhtMont)
														{	$phtMonId=$wtPhtMont[0];
															$phtcerificateId=$wtPhtMont[2];
															$phtQt=$wtPhtMont[3];
															
															//die();
															$certificateVal=$objWeighmentDataSheet->getPhtQuantityLessValue($phtcertificateId);
															
															if(sizeof($certificateVal)>0)
															{
																foreach($certificateVal as $certifyQty)
																{
																	if($balQty=="")
																	{
																		$monId=$certifyQty[0];
																		$setOff=$certifyQty[4];
																		$balQty=$phtQt-$setOff;
																		$objWeighmentDataSheet->updatePhtMonitoringQnty($phtQt,$setOff,$balQty,$monId);
																	}
																	else
																	{
																		$phtQt=$balQty;
																		$monId=$certifyQty[0];
																		$setOff=$certifyQty[4];
																		$balQty=$balQty-$setOff;
																		$objWeighmentDataSheet->updatePhtMonitoringQnty($phtQt,$setOff,$balQty,$monId);	
																	}
																}
															}

															//$objWeighmentDataSheet->deletePhtMonitoring($phtMonId);
														}
												}
											
											}				//}

										}
										else if($PhtMonitoringEntryId=="" && $WeightmentEntryIdEdit=="")
										{
											if($supplyBalanceQty=="")
											{
												$certificateQty=$objWeighmentDataSheet->getCerificateQty($certificateId);
												$monitoringId=$objWeighmentDataSheet->getMonitoringId($certificateId);
												if($supplyQty>=$certificateQty)
												{
													$phtQuantity=$certificateQty;
													$setoffQuantity=$certificateQty;
													$balanceQty=0;
													//$weightmentEntryId='';
													$supplyQtyVal=$supplyQty;
													$adjustedQty=$certificateQty;
													$supplyBalanceQty=$supplyQty-$phtQuantity;
												}
												else if($certificateQty>$supplyQty)
												{
													$phtQuantity=$certificateQty;
													$setoffQuantity=$supplyQty;
													$balanceQty=$certificateQty-$supplyQty;
													//weightmentEntryId='';
													$supplyQtyVal=$supplyQty;
													$adjustedQty=$supplyQty;
													$supplyBalanceQty=0;
												}
												$certificateIns = $objWeighmentDataSheet->addPhtCertificateQuantity($monitoringId,$certificateId,$phtQuantity,$setoffQuantity,$balanceQty,$weightmentEntryId,$rmLotId,$supplyQtyVal,$adjustedQty,$supplyBalanceQty);

											}
											else if($supplyBalanceQty!="")
											{
												$certificateId=$item->certificateNo;
												$certificateQty=$objWeighmentDataSheet->getCerificateQty($certificateId);
												$monitoringId=$objWeighmentDataSheet->getMonitoringId($certificateId);
												if($supplyBalanceQty>=$certificateQty)
												{
													$phtQuantity=$certificateQty;
													$setoffQuantity=$certificateQty;
													$balanceQty=0;
													//weightmentEntryId='';
													$supplyQtyVal=$supplyBalanceQty;
													$adjustedQty=$certificateQty;
													$supplyBalanceQty=$supplyBalanceQty-$phtQuantity;
												}
												else if($certificateQty>$supplyBalanceQty)
												{
													$phtQuantity=$certificateQty;
													$setoffQuantity=$supplyBalanceQty;
													$balanceQty=$certificateQty-$supplyBalanceQty;
													//weightmentEntryId='';
													$supplyQtyVal=$supplyBalanceQty;
													$adjustedQty=$supplyBalanceQty;
													$supplyBalanceQty=0;
												}
												//echo "pht_quantity".$pht_quantity."setoff_quantity".$setoff_quantity."balanceQty".$balanceQty."lastId".$lastId."supply_qty".$supply_qty."adjusted_qty".$adjusted_qty."supplyBalanceQty".$supplyBalanceQty.'<br/>';
												$certificateIns = $objWeighmentDataSheet->addPhtCertificateQuantity($monitoringId,$certificateId,$phtQuantity,$setoffQuantity,$balanceQty,$weightmentEntryId,$rmLotId,$supplyQtyVal,$adjustedQty,$supplyBalanceQty);
											}		
										}

									}
								}
							}
						
					}

				}
				else if($mstatus[$i] == 'N' && $weightmentId[$i] != '')
				{
					
					$objWeighmentDataSheet->deleteWeightmentSupplierProcurementValue($weightmentId[$i]);
					$objWeighmentDataSheet->deleteWeightmentPhtTag($weightmentId[$i]);	
				}
				
			}
		}
		//die;
		if($weightmentIns)
		{
			$sessObj->createSession("displayMsg",$msg_succAddWeightmentData);
			$sessObj->createSession("nextPage",$url_afterAddWeightmentData.$selection);	
		}
		else if($weightmentUpdate)
		{
			$sessObj->createSession("displayMsg",$msg_succWeightmentDataUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateWeightmentData.$selection);
		}
		$p["cmdAdd"]="";
	}
	$supplierData = array(); $totalNotEditable = 0; $rm_lot_id_value = ''; $buttonName = 'Add';
	if($p['cmdEdit'])
	{
		$buttonName   =  'Update';
		$editId			=	$p["editId"];	
		$editDatas = $objWeighmentDataSheet->getEditDatas((int)$p['editId']);
		// print_r($editDatas);
		$weightmentId=$editDatas[0];
		$rm_lot_id            = $editDatas[1];
		$data_sheet_slno      = $editDatas[2];
		$data_sheet_date      = dateformat($editDatas[3]);
		$receiving_supervisor = $editDatas[8];
		$procurementAvailable=$editDatas[12];
		$totalNotEditable = $editDatas[17];
		$rm_lot_id_value = $editDatas[18];
		
		// $supplierData	=	$objWeighmentDataSheet->getSupplierDataView($editId);
		// print_r($supplierData);
	}
	// if ($p["btnConfirm"]!="")
	// {
	// echo '<pre>';print_r($p);echo '</pre>';
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$weightmentId	=	$p["delId_".$i];

			if ($weightmentId!="" && $isAdmin!="") {
				$deleteweightmentRecs	=	$objWeighmentDataSheet->deleteWeightmentGroup($weightmentId);
				$weightmentRecDel1	=	$objWeighmentDataSheet->deleteWeightmentPhtTagSupplier($weightmentId);	
				$weightmentRecDel2 =	$objWeighmentDataSheet->deleteWeightmentSupplier($weightmentId);
				/*$weightmentRecDel2 =$objWeighmentDataSheet->deleteWeightmentEquipment($weightmentId);
				$weightmentRecDel3 =$objWeighmentDataSheet->deleteWeightmentChemical($weightmentId);*/	
				
			}
		}
		if ($deleteProcurmentRecs) {
			$sessObj->createSession("displayMsg",$msg_succDelRMProcurment);
			$sessObj->createSession("nextPage",$url_afterDelRMProcurment.$selection);
		} else {
			$errDel	=	$msg_failDelRMProcurment;
		}
		$deleteProcurmentRecs	=	false;
		$hidEditId 	= "";
	}	
	
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$weightmentId	=	$p["confirmId"];
			$rmLotId=$p["rmLotId"];
			if ($weightmentId!="") {
				// Checking the selected fish is link with any other process
				$weightmentRecConfirm = $objWeighmentDataSheet->updateWeighmentconfirm($weightmentId);
				$rmlotRecConfirm = $objManageRMLOTID->updateRMLotIDconfirm($rmLotId);	
			}

		}
		if ($weightmentRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmWeightmentDataSheet);
			$sessObj->createSession("nextPage",$url_afterUpdateWeightmentData.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}

		if ($p["btnRlConfirm"]!="")
	{
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$weightmentId = $p["confirmId"];
			if ($weightmentId!="") {
				#Check any entries exist
				
					$weightmentRecConfirm = $objWeighmentDataSheet->updateWeighmentReleaseconfirm($weightmentId);
				
			}
		}
		if ($weightmentRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmWeightmentDataSheet);
			$sessObj->createSession("nextPage",$url_afterUpdateWeightmentData.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	
	// if(isset($p['confirmId']) && $p['confirmId'] != '')
	// {
		// $confirmId = (int) $p['confirmId'];
		// $weightmentRecConfirm = $objWeighmentDataSheet->updateWeighmentconfirm($confirmId);
		// $sessObj->createSession("displayMsg",$msg_succConfirmWeightmentDataSheet);
		// $sessObj->createSession("nextPage",$url_afterUpdateWeightmentData.$selection);
	// }
		## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") {
		$pageNo=$p["pageNo"];
	} else if ($g["pageNo"] != "") {
		$pageNo=$g["pageNo"];
	} else {
		$pageNo=1;
	}
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	# select records between selected date
	if ($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
	}
	
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$WeighmentDataSheetRecords	= $objWeighmentDataSheet->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$WeighmentDataSheetSize	= sizeof($WeighmentDataSheetRecords);
		$fetchAllStockIssuanceRecs = $objWeighmentDataSheet->fetchAllDateRangeRecords($fromDate, $tillDate);
	}
	$numrows	=  sizeof($fetchAllStockIssuanceRecs);
	$maxpage	=  ceil($numrows/$limit);
	
	$rmLotIds  = $objWeighmentDataSheet->getAllLotIds();
	$purchaseSupervisor = $objWeighmentDataSheet->getAllEmployee();
	$speciesArray = $objWeighmentDataSheet->getAllSpecies();
	$qualityList = $objWeighmentDataSheet->getAllQuality();
	//$suppliersList = $objWeighmentDataSheet->getAllSuppliers();
	$speciesAllVal = $objWeighmentDataSheet->getAllSpecies();
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/WeightmentDatasheet.js"; // For Printing JS in Head section
	
	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";
	
	if ($addMode) $heading	=	$label_addWeightmentData;
	else $heading	=$label_editWeightmentData;	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

	
	//printr($speciesAllVal);
?>
<script>
function addNewWeighmentMultipleTableRow(rm_lot_id)
{
	if(rm_lot_id == '')
	{
		var rm_lot_id = jQuery('#rm_lot_id').val();
	}
	if(rm_lot_id == '')
	{
		alert('Please choose the rm lot id');
	}
	else
	{
		xajax_getTableRowBasedRmLotId(rm_lot_id);
	}
}
/*jQuery(document).ready(function(){
	var rm_lot_id =	'<?php echo $rm_lot_id;?>';
	var editVal = '';
	<?php
		if($p['cmdEdit'])
		{
	?>
			editVal = 'edit';
	<?php 
		}
	?>
	if(rm_lot_id != '')
	{
		xajax_getTableRowBasedRmLotId(rm_lot_id,editVal)
	}
});*/
</script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">

<!--<link rel="stylesheet" href="libjs/jquery-ui.css">-->
<script src="libjs/jquery/jquery-1.10.2.js"></script>
<script src="libjs/jquery/jquery-ui.js"></script>

<form method="post" action="WeighmentDataSheet.php" id="WeighmentDataSheet" name="WeighmentDataSheet">
	<table width="70%" align="center" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<td height="20" align="center" class="err1"> </td>
			</tr>	
			<tr>
				<td height="10" align="center"></td>
			</tr>
			<?php
				if($p['cmdAddNew'] || $p['cmdEdit'])
				{
			?>
				<tr>
					<td>
						<table width="70%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
							<tbody>
								<tr>
									<td bgcolor="white">
									<!-- Form fields start -->
										<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
											<tbody>
												<tr>
													<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
													<td width="581" background="images/heading_bg.gif" colspan="2" class="pageName"> <?=$heading;?></td>
												</tr>												
												<tr>
													<td height="10"></td>
													<td>&nbsp;</td>
												</tr>
												<tr>
													<td align="center" colspan="2">
														<input type="button" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('WeighmentDataSheet.php');">&nbsp;&nbsp;
														<input type="submit" onclick="return weighmentFormValidation();" name="cmdAdd" id="cmdAdd" class="button" value="<?php echo $buttonName;?>"> &nbsp;&nbsp;												
													</td>													
												</tr>
												<tr>
													<td>
														<span class="fieldName" style="color:red; line-height:normal" id="requestNumExistTxt"></span>
													</td>
													<td>&nbsp;</td>
												</tr>
												<tr><td colspan="2"><span class="fieldName" style="color:red; line-height:normal" id="message" name="message"></span></td></tr>
												<tr>
													<td width="122" colspan="2" align="center">																								
														<table width="70%" border="0" id="newspaper-dce-rbt">
															<tbody>
																<tr>
																	<td align="center">
																		<table width="100%" align="center" cellspacing="0" cellpadding="0">
																			<tbody>
																				<tr>
																					<td nowrap="" class="fieldName">*Date of Entry</td>
																					<td>
																							<input type="text"  value="<?php echo $data_sheet_date;?>" size="9" id="data_sheet_date" name="data_sheet_date" >
																					</td>
																					<td nowrap="" class="fieldName"> Receiving supervisor : </td>
																					<td nowrap="">
																						<?php
																							if(sizeof($purchaseSupervisor) > 0)
																							{
																								echo '<select id="receiving_supervisor" name="receiving_supervisor">';
																								echo '<option value=""> -- Select Received supervisor --</option>';
																								foreach($purchaseSupervisor as $lotID)
																								{	
																									$sel = '';
																									if($receiving_supervisor == $lotID[0]) $sel = 'selected="selected"';
																								
																									echo '<option '.$sel.' value="'.$lotID[0].'">'.$lotID[1].'</option>';
																								}
																								echo '</select>';
																							}
																						?>
																					</td>
																				</tr>
																				<tr>
																					<td width="180" class="fieldName">* Rm Lot ID :</td>
																					<td class="listing-item">
																					<?php
																					if($p['cmdEdit'])
																					{
																					?>
																					<select onchange="addNewWeighmentMultipleTableRow(this.value);" name="rm_lot_id" id="rm_lot_id">
																						<option value="<?php echo $rm_lot_id;?>"> <?php echo $rm_lot_id_value;?></option>
																					</select>
																					<?php 
																					}
																					else
																					{
																					?>
																						<select onchange="addNewWeighmentMultipleTableRow(this.value);" name="rm_lot_id" id="rm_lot_id">
																							<option value=""> -- Select Lot ID --</option>
																							<?php
																								if(sizeof($rmLotIds) > 0)
																								{
																									foreach($rmLotIds as $lotID)
																									{	
																										$sel = '';
																										if($rm_lot_id == $lotID[0]) $sel = 'selected="selected"';
																													
																										echo '<option '.$sel.' value="'.$lotID[0].'">'.$lotID[1].'</option>';
																									}
																								}
																							?>
																						</select>
																					<?php
																					}
																					?>
																					</td>
																					<td align="right" class="fieldName" nowrap>* Data Sheet SL NO : </td>
																					<td class="listing-item">
																						<input type="text" id="data_sheet_slno" name="data_sheet_slno" value="<?php echo $data_sheet_slno;?>" readonly="readonly">
																					</td>														
																				</tr>				
																				<input type="hidden" value="<?=$weightmentId?>" name="hidWeightmentId">
																			</tbody>
																		</table>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>		
												</tr>
												<tr><td height="10%" colspan="2">&nbsp;</td></tr>
												<tr class="autoUpdate" id="autoUpdate">
													<td width="122" colspan="2" align="center">
														<table width="70%" border="0" style="margin:10px 10px 10px 10px;" id="newspaper-dce-rbt">
															<tbody>
																<tr>
																	<td align="center">
																		<table width="100%" style="padding:10px 10px 10px 10px;" align="center" cellspacing="0" cellpadding="0">
																			<tbody>
																				<tr>
																					<td colspan="2"></td>
																				</tr>
																				<tr>
																					<td width="40%" valign="top" align="center" colspan="3">
																						<table width="10%" bgcolor="#999999" cellspacing="1" cellpadding="3" name="tblWeighmentMultiple" id="tblWeighmentMultiple">
																							<tbody>
																								<tr bgcolor="#f2f2f2" align="center">
																									<td class="listing-head" nowrap> Supplier </td>
																									<td class="listing-head" nowrap> Farm Name </td>
																									<td class="listing-head" nowrap> Procurement Center </td>
																									<td class="listing-head" nowrap> Species </td>
																									<td class="listing-head" nowrap> Process Code </td>
																									<td class="listing-head" nowrap> Quality </td>
																									<td class="listing-head" nowrap> Count Code </td>																	
																									<td class="listing-head" nowrap> Weight </td>
																									<td class="listing-head" nowrap> Soft % </td>
																									<td class="listing-head" nowrap> Soft Weight </td>
																									<td class="listing-head" nowrap> Pht tag </td>
																									<td>
																									<?php
																									if($p['cmdEdit'])
																									{
																										echo '<input value="'.$editId.'" type="hidden" id="editWeighmentId" name="editWeighmentId" />';
																										echo '<input value="'.$procurementAvailable.'" type="hidden" id="procurementAvailable" name="procurementAvailable" />';
																									}
																									else
																									{
																									?>
																										<input type="hidden" value="" id="procurementAvailable" name="procurementAvailable">
																									<?php
																									}
																									?>
																									</td>
																									
																								</tr>
																								
																								<?php
																									$total_quantity = 0;$total_soft = 0;
																									if($p['cmdEdit'])
																									{
																									if($procurementAvailable == 1)
																										{
																											$supllierDatas = $objWeighmentDataSheet->getMultipleRowForEdit($editId);
																											if(sizeof($supllierDatas) > 0)
																											{ 
																												$i = 0; 
																												foreach($supllierDatas as $data)
																												{	
																													$supplierDropDown = '<select name="supplierName[]" id="supplierName_'.$i.'" />
																																			<option value="'.$data[1].'">'.$data[2].'</option>
																																		</select>';
																													$farmName = '<select name="pondName[]" id="pondName_'.$i.'" />
																																			<option value="'.$data[3].'">'.$data[4].'</option>
																																		</select>';
																													$procurementCenter = '<select name="procurementCenter[]" id="procurementCenter_'.$i.'" />
																																			<option value="'.$data[16].'">'.$data[17].'</option>
																																</select>';
																													$speciesArray 			= $objWeighmentDataSheet->filterSpeciesForEdit($data[3]);
																													
																													if($data[5]!="0")
																													{																													
																														$speciesArrayVal = $objWeighmentDataSheet->getAllSpecies();																												$speciesdropDown = '<select name="product_species[]" id="product_species_'.$i.'" onchange="xajax_processCode(this.value,'.$i.',0);">';
																														$speciesdropDown.= '<option value=""> --Select-- </option>';
																														foreach($speciesArrayVal as $speciesVal)
																														{	
																															// $speciesPrint = explode('$$',$species);
																															
																															$sel = '';
																															if($data[5] == $speciesVal[0]) $sel = 'selected';
																															
																															$speciesdropDown.= '<option '.$sel.' value="'.$speciesVal[0].'">'.$speciesVal[1].'</option>';
																														}
																														$speciesdropDown.= '</select>';
																													
																													
																													}
																													$processCode = '<select name="process_code[]" id="process_code_'.$i.'" />
																																			<option value="'.$data[7].'">'.$data[8].'</option>
																																		</select>';
																													if(sizeof($qualityList) > 0)
																													{
																														$qualityDropDown = '<select name="quality[]" id="quality_'.$i.'">';
																														$qualityDropDown.= '<option value=""> -- Select --</option>';
																														foreach($qualityList as $quality)
																														{
																															$sel = '';
																															if($data[9] == $quality[0]) $sel = 'selected';
																															
																															$qualityDropDown.= '<option '.$sel.' value="'.$quality[0].'"> '.$quality[1].' </option>';
																														}
																														$qualityDropDown.= '</select>';
																													}
																													else
																													{
																														$qualityDropDown = '<select name="quality[]" id="quality_'.$i.'" />
																																			<option value="'.$data[9].'">'.$data[10].'</option>
																																		</select>';
																													}

																													$countCode    = '<input type="text" value="'.$data[11].'" name="count_code[]" id="count_code_'.$i.'" size="10" />';
																													$weight       = '<input type="text" value="'.$data[12].'" name="weight[]" id="weight_'.$i.'" size="10" onkeyup="calculateWeightAndPercent();" /><input type="hidden" value="'.$data[12].'" name="oldWeight[]" id="oldWeight_'.$i.'" size="10" />';
																													$soft_precent = '<input type="text" value="'.$data[13].'" name="soft_precent[]" id="soft_precent_'.$i.'" size="10" onkeyup="calculateWeightAndPercent();" />';
																													$soft_weight  = '<input type="text" value="'.$data[14].'" name="soft_weight[]" readonly id="soft_weight_'.$i.'" size="10" />';
																													$total_quantity = $total_quantity + $data[12];
																													$total_soft = $total_soft + $data[14];
																													//echo$data[0];
																													$weightmentEntryId=$data[0];
																													$itemArray='';
																													$monitoringDetail = $objWeighmentDataSheet->getPhtMonitoringData($weightmentEntryId);
																													if(sizeof($monitoringDetail)>0)
																													{	
																														
																														$j=0;
																														//$itemArray=[]; 
																														
																														$certificateSize=count($monitoringDetail);
																														foreach($monitoringDetail as $monitoringDt)
																														{
																															$rowCnt=$j;
																															$phtMonitoringEntryId=$monitoringDt[0];
																															$certificateId=$monitoringDt[1];
																															$phtQty=$monitoringDt[2];
																															$adjustedQty=$monitoringDt[3];
																															$balanceQty=$monitoringDt[5];
																															$qntyStatus='1';
																															if($balanceQty==0)
																															{
																																$balanceQty=$monitoringDt[4];
																																$qntyStatus='0';

																															}
																															//$items=["RowCnt" => $rowCnt,"certificateNo" => $certificateId,"availableQnty" =>$phtQty,"balanceQnty" =>$balanceQty,"qntyStatus"=>$qntyStatus,"phtMonitoringEntryId"=>$phtMonitoringEntryId,"weightmentEntryId"=>$weightmentEntryId];
																															//$items="{'RowCnt':'".$rowCnt."','certificateNo':'".$certificateId."','availableQnty':'".$phtQty."','balanceQnty':'".$balanceQty."','qntyStatus':'".$qntyStatus."','phtMonitoringEntryId':'".$phtMonitoringEntryId."','weightmentEntryId':'".$weightmentEntryId."'}";
																															//$items='{"RowCnt" :'.$rowCnt.',"certificateNo" :'.$certificateId.',"availableQnty":'.$phtQty.',"balanceQnty" :'.$balanceQty.',"qntyStatus":'.$qntyStatus.',"phtMonitoringEntryId":'.$phtMonitoringEntryId.',"weightmentEntryId":'.$weightmentEntryId.'}';
																															$items='{"RowCnt" :"'.$rowCnt.'","certificateNo" :"'.$certificateId.'","availableQnty":"'.$phtQty.'","balanceQnty" :"'.$balanceQty.'","qntyStatus":"'.$qntyStatus.'","phtMonitoringEntryId":"'.$phtMonitoringEntryId.'","weightmentEntryId":"'.$weightmentEntryId.'"}';
																															//echo $items;
																															if($itemArray=="")
																															{
																																$itemArray.="[".$items;
																															}
																															else
																															{
																																$itemArray.=','.$items;
																															}
																															

																															//array_push($itemArray,$items);
																															$j++;
																															if($certificateSize==$j)
																															{
																																$itemArray.="]";
																															}
																														}
																														//$tag=["Supplier"=>$data[1],"Pond"=>$data[3],"Species"=>$data[5],"SupplyQnty" =>$data[12],"DatasheetDate"=>$data_sheet_date,"RmLotId"=>$rm_lot_id,"CertificateSize"=>$certificateSize];		
																														$tag='{"Supplier":"'.$data[1].'","Pond":"'.$data[3].'","Species":"'.$data[5].'","SupplyQnty":"'.$data[12].'","DatasheetDate":"'.$data_sheet_date.'","RmLotId":"'.$rm_lot_id.'","CertificateSize":"'.$certificateSize.'"}';		
																														$phtTag='{"tag":'.$tag.',"items":'.$itemArray.'}';
																														
																													}
																													//echo $phtTag;
																													//die();

																													$hiddenFields = "<input name='mstatus[]' type='hidden' id='mstatus_".$i."'>
																																	<input name='IsFromDB[]' type='hidden' id='IsFromDB_".$i."' value='N'>
																																	<input type='hidden' name='mrmId_".$i."' id='mrmId_".$i."'>
																																	<input type='hidden' name='newData[]' id='newData_".$i."' value='".$data[15]."'>
																																	<input type='hidden' name='phtTagData[]' id='phtTagData_".$i."' value='".$phtTag."'>
																																	<input type='hidden' name='weightmentId[]' id='weightmentId_".$i."' value='".$data[0]."'>";
																													echo '<tr id="mrow_'.$i.'" bgcolor="#f2f2f2" align="center">';
																													echo '<td class="listing-head" nowrap> '.$supplierDropDown.' </td>
																														  <td class="listing-head" nowrap> '.$farmName.' </td>
																														  <td class="listing-head" nowrap> '.$procurementCenter.'</td>
																														  <td class="listing-head" nowrap> '.$speciesdropDown.' </td>
																														  <td class="listing-head" nowrap> '.$processCode.' </td>
																														  <td class="listing-head" nowrap> '.$qualityDropDown.' </td>
																														  <td class="listing-head" nowrap> '.$countCode.' </td>																	
																														  <td class="listing-head" nowrap> '.$weight.' </td>
																														  <td class="listing-head" nowrap> '.$soft_precent.' </td>
																														  <td class="listing-head" nowrap> '.$soft_weight.' </td>
																														  <td class="listing-head" nowrap><a class="link1" onclick="getPhtCertificateEdit('.$i.');" href="#">link tag</a></td>
																														  ';
																													if($data[15]=='1')
																													{
																														echo '<td></td>';
																													}
																													else
																													{
																													?>
																														<td>
																															<a href='javascript:void(0);' onClick="hideTableRow('<?php echo $i;?>');" >
																																<img title="Click here to remove this item" SRC='images/delIcon.gif' BORDER='0' style='border:none;'>
																															</a>
																														</td>
																													<?php 
																													}
																													echo $hiddenFields;
																													echo '</tr>';
																													$i++;
																												}
																												
																											}
																											echo '<input type="hidden" name="rowcount" id="rowcount" value="'.sizeof($supllierDatas).'" />';
																										}
																										
																									}
																									else
																									{
																									echo '<input type="hidden" name="rowcount" id="rowcount" value="0" />';
																								?>
																								<tr>
																									<td  class="err1"  nowrap align="center" colspan="12"> No records found </td>
																								</tr>
																								<?php
																									}
																								?>
																							
																							
																							</tbody>
																								
																						</table>
																					</td>
																				</tr>
																																									
																				<tr>
																					<td height="10">
																						<input type="hidden"  id="hidTableRowCounts" name="hidTableRowCounts" value="<?=$i;?>">
																					</td>
																				</tr>
																				<tr>
																					<td nowrap="" style="padding-left:5px; padding-right:5px;">
																							<a href="javascript:void(0);" id="addRow" onclick="javascript:addNewWeighmentTableRow();" class="link1" title="Click here to add new item.">
																								<img border="0" src="images/addIcon.gif" style="border:none;padding-right:4px;vertical-align:middle;">Add New Item
																							</a>
																					</td>
																				</tr>
																				<tr>
																					<td height="10"></td>
																				</tr>
																				
												
																				<tr>
																					<td height="10"></td>
																				</tr>

																				<tr>
																					<td>
																						<table>
																							<tbody>
																							<tr>
																							<td width="47%" nowrap="" class="fieldName"> &nbsp;</td>
																								<td nowrap="" class="fieldName"> Total weight : </td>
																								<td nowrap="">
																									<input type="text" name="total_quantity" id="total_quantity" value="<?php echo $total_quantity;?>" readonly="readonly">																						
																								</td>
																								<td nowrap="" class="fieldName"> Total soft weight% </td>
																								<td nowrap="">
																									<input type="text" name="total_soft" id="total_soft" value="<?php echo $total_soft;?>" readonly="readonly">																						
																								</td>
																							</tr>
																							 <input type="hidden" name="number_gen_id" id="number_gen_id" size="9" value="<?=$number_gen_id;?>" readonly="readonly"  />
																						</tbody></table>
																					</td>
																					<td></td>
																				</tr>
																				<tr>
																					<td height="10"></td>
																				</tr>
																				<tr class="">
																					<td align="left">
																						<table>
																							<tbody>
																								<tr>
																									<td>
																										<table>
																											<tbody>
																												<tr>
																													<td valign="top" id="tblWeighmentEquipments"></td>
																													<td width="35"></td>
																													<td valign="top" id="tblWeighmentChemiclas"></td>
																												</tr>																					
																											</tbody>
																										</table>																	
																									</td>
																								</tr>
																							</tbody>
																						</table>																
																					</td>
																				</tr>
																				<tr class="">
																					<td align="left">
																						<table>
																							<tbody>
																								<tr>
																									<td>
																										<table>
																											<tbody>
																												<tr>
																													
																												</tr>																												
																											</tbody>
																										</table>																	
																									</td>
																								</tr>
																							</tbody>
																						</table>																
																					</td>
																				</tr>
																				<tr><td height="10%" colspan="2">&nbsp;</td></tr>
																			</tbody>
																		</table>
																	</td>
																</tr>
																
															</tbody>
														</table>
													</td>
												</tr>
												<tr><td height="10%" colspan="2">&nbsp;</td></tr>
												
												<tr>
													
													<td align="center" colspan="2">
														<input type="button" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('WeighmentDataSheet.php');">&nbsp;&nbsp;
														<input type="submit" name="cmdAdd" id="cmdAdd" onclick="return weighmentFormValidation();" class="button" value="<?php echo $buttonName;?>"> &nbsp;&nbsp;												
													</td>													
												</tr>
												<tr>
													<td>
														<span class="fieldName" style="color:red; line-height:normal" id="requestNumExistTxt"></span>
													</td>
													<td>&nbsp;</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
				<!-- Form fields end   -->	
					</td>
				</tr>
			<?php
				}
			?>
			<tr>
				<td height="10" align="center"></td>
			</tr>
			<tr>
				<td>
				
				<table width="75%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
					<tbody><tr>
						<td bgcolor="white">
							<!-- Form fields start -->
							<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
								<tbody><tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td nowrap="" background="images/heading_bg.gif" class="pageName">&nbsp;Weighment Data Sheet (Farm)  </td>
									<td nowrap="nowrap" background="images/heading_bg.gif" align="right">
									<table cellspacing="0" cellpadding="0">
									  <tbody><tr>
					<td nowrap="nowrap">
						<table cellpadding="0" cellspacing="0">
									<tr>
							<td class="listing-item"> From:</td>
												<td nowrap="nowrap"> 
										<? 
						if ($dateFrom=="") $dateFrom=date("d/m/Y");
						?>
								<input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>"></td>
							<td class="listing-item">&nbsp;</td>
								<td class="listing-item"> Till:</td>
										<td> 
										  <? 
						   if($dateTill=="") $dateTill=date("d/m/Y");
						  ?>
										  <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>"></td>
						   <td class="listing-item">&nbsp;</td>
								<td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
								<td class="listing-item" nowrap >&nbsp;</td>
							  </tr>
						</table>
					</td>
				</tr></tbody></table></td>
								</tr>
								<tr>
									<td height="10" colspan="3"></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table align="center" cellspacing="0" cellpadding="0">
											<tbody><tr>
												<td nowrap=""><input type="submit" onclick="return confirmDelete(this.form,'delId_',);" name="cmdDelete" class="button" value=" Delete ">&nbsp;<input type="submit" class="button" name="cmdAddNew" value=" Add New ">&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" 
												onClick="return printWindow('PrintWeightmentDatasheetAll.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"
												><? }?></td>
											</tr>
										</tbody></table>									</td>
								</tr>
								<tr>
									<td height="5" colspan="3"></td>
								</tr>
								<?php
									if( sizeof($WeighmentDataSheetRecords) > 0 )
									{
									$i	=	0;
								?>
										
									<tr>
										<td width="1" ></td>
											<td colspan="2" >
											<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
												<? if($maxpage>1){?>
										<tr bgcolor="#f2f2f2">
											<td colspan="12" align="right" style="padding-right:10px;" class="navRow">
											<div align="right">
											<?php
											$nav  = '';
												for ($page=1; $page<=$maxpage; $page++) 
												{
													if ($page==$pageNo) 
													{
														$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
													} 
													else 
													{
														$nav.= " <a href=\"WeighmentDataSheet.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
																	//echo $nav;
													}
												}
												if ($pageNo > 1) 
												{
													$page  = $pageNo - 1;
													$prev  = " <a href=\"WeighmentDataSheet.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
												} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage) {
													$page = $pageNo + 1;
													$next = " <a href=\"WeighmentDataSheet.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
												} else {
													$next = '&nbsp;'; // we're on the last page, don't print next link
													$last = '&nbsp;'; // nor the last page link
												}
															// print the navigation link
												$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
												echo $first . $prev . $nav . $next . $last . $summary; 
												?>	
												<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
												</div> </td>
												</tr>
											<? }?>
												
												<tr  bgcolor="#f2f2f2" >
													<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
													<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head" nowrap>Data Sheet No </td>
													<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head"  nowrap>RM LOT ID</td>
													<td align="center" style="padding-left:10px; padding-right:10px;" class="listing-head"  nowrap>Process Code</td>
													<td style="padding-left:10px; padding-right:10px;" class="listing-head"  nowrap>Count </td>
													<td style="padding-left:10px; padding-right:10px;" class="listing-head"  nowrap>Qty  </td>
													<td style="padding-left:10px; padding-right:10px;" class="listing-head"  nowrap>Soft% </td>
													<td style="padding-left:10px; padding-right:10px;" class="listing-head"  nowrap>Soft Qty </td>
													<td style="padding-left:10px; padding-right:10px;" class="listing-head"  nowrap>Pht Tag </td>	
													<td style="padding-left:10px; padding-right:10px;" class="listing-head"  nowrap>view </td>	
													
													<? if($confirm==true && ($manageconfirmObj->weightmentDataConfirmEnabled())){?>
																	<td class="listing-head">&nbsp;</td>
														<? }?>
													<!--<td class="listing-head"></td>-->
													<? if($edit==true){?>
													<!--<td class="listing-head"></td>-->
													<td class="listing-head"></td>
													<? }?>									
																							</tr>
													<?
													foreach ($WeighmentDataSheetRecords as $sir) {
														$i++;
														$weightmentId	=	$sir[0];
														$rm_lot_id		=$sir[1];
														$rm_lot_idNm		=$sir[17];
														$data_sheet_sl_no		=$sir[2];
														$supplierData	=	$objWeighmentDataSheet->getSupplierData($sir[0]);
														$entryDate		= dateFormat($sir[5]);
														$active=$sir[16];
														$processCode = '';$countCodes = '';$quantities = '';$softPercents = '';
														$softQty = '';
														if (sizeof($supplierData)>0) {
																	foreach ($supplierData as $cR) {	
																	$processCode.= $cR[15];
																	$countCodes.= $cR[7];
																	$quantities.= $cR[8];
																	$softPercents.= $cR[9];
																	$softQty.= $cR[10];
																	$processCode.= "<br/>";	
																	$countCodes.= "<br/>";
																	$quantities.= "<br/>";
																	$softPercents.= "<br/>";
																	$softQty.= "<br/>";
																}
															}
														// die;
													?>
													<tr  bgcolor="WHITE">
														<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$weightmentId;?>" class="chkBox"></td>
														<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$data_sheet_sl_no;?></td>
														<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rm_lot_idNm;?></td>
															
														<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
														 <?php
															echo $processCode;
															?>
														</td>
														<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
														<?php
															echo $countCodes;
															?>
														</td>
														<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
														<?php
															echo $quantities;
															?>
														</td>
														<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
														<?php
															echo $softPercents;
														?></td>
														<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
															<?php
															echo $softQty;
															?>
														</td>
														<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" >
															<? foreach($supplierData as $cR) 
															{	
																$weightmentEntryId=$cR[0];
																$phtTagStatus	=	$objWeighmentDataSheet->getPhtTagDetail($weightmentEntryId);
																//echo $phtTagStatus;
																if(sizeof($phtTagStatus)>0 && $phtTagStatus==0)
																{
																	echo "Full".'<br/>';
																}
																elseif(sizeof($phtTagStatus)>0 && $phtTagStatus>0)
																{
																	echo "Part".'<br/>';	
																}
																else
																{
																	echo '<br/>';
																}
														
																
															}
															?>
														</td>
														<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
															<!--<a href="javascript:printWindow('ViewRmProcurmentOrderDetails.php?procurmentId=<?=$procurementId?>&supplierGroup=<?=$supplierGroup?>&supplier=<?=$supplier?>&pondNamee=<?=$pondNamee?>',700,600)" class="link1" title="Click here to view details.">View Details</a>-->
															<a title="Click here to view details." class="link1" href="javascript:printWindow('ViewWeighmentDataSheetDetails.php?id=<?php echo $sir[0];?>',900,750)">View Details</a>
															
														</td>

														<? if ($confirm==true && ($manageconfirmObj->weightmentDataConfirmEnabled())){?>
															<td <?php if ($active==1) {?> class="listing-item" <?php }else {?>  <?php }?> width="45" align="center" >
															
															<?php 
															 if ($confirm==true){	
															if ($active=="0"){ ?>
															<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$weightmentId;?>,'confirmId'); assignValue(this.form,<?=$rm_lot_id;?>,'rmLotId');" >
															<?php } else if ($active==1){ if ($existingrecords==0) {?>
															<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$weightmentId;?>,'confirmId');" >
															<?php } } 
															}?>
												
															</td>
													<?}?>
												
														
												<? if($edit==true){?>
														<td width="60" align="center" class="listing-item">
															<?php if ($active!=1) {
															?><input type="submit" onclick="assignValue(this.form,'<?php echo $weightmentId;?>','editId');assignValue(this.form,'<?php echo $weightmentId;?>','editSelectionChange');this.form.action='WeighmentDataSheet.php';" name="cmdEdit" value=" Edit ">
														 <?php } ?></td>
															<? }?>
													</tr>
													<?php $equipmentName=""; $chemicalName="";?>
													<?
														}
													?>
													
													
													
													<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
													<input type="hidden" name="editId" value="">
													<input type="hidden" name="editSelectionChange" value="0">
													<input type="hidden" name="confirmId" value="">
													<input type="hidden" name="rmLotId" value="">
													<? if($maxpage>1){?>
										<tr bgcolor="#f2f2f2">
											<td colspan="12" align="right" style="padding-right:10px;" class="navRow">
											<div align="right">
											<?php
											$nav  = '';
												for ($page=1; $page<=$maxpage; $page++) 
												{
													if ($page==$pageNo) 
													{
														$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
													} 
													else 
													{
														$nav.= " <a href=\"WeighmentDataSheet.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
																	//echo $nav;
													}
												}
												if ($pageNo > 1) 
												{
													$page  = $pageNo - 1;
													$prev  = " <a href=\"WeighmentDataSheet.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
												} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
												}

												if ($pageNo < $maxpage) {
													$page = $pageNo + 1;
													$next = " <a href=\"WeighmentDataSheet.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
												} else {
													$next = '&nbsp;'; // we're on the last page, don't print next link
													$last = '&nbsp;'; // nor the last page link
												}
															// print the navigation link
												$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
												echo $first . $prev . $nav . $next . $last . $summary; 
												?>	
												<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
												</div> </td>
												</tr>
											<? }?>	
											</table>
										</td>
									</tr>
									

								<?php
									}
									else
									{
								?>
								<tr>
									<td width="1"></td>
									<td colspan="2">
										<table width="80%" border="0" bgcolor="#999999" align="center" cellspacing="1" cellpadding="2">
																						<tbody><tr bgcolor="white">
												<td height="10" align="center" class="err1" colspan="6">No records found.</td>
											</tr>	
																					</tbody></table>									</td>
								</tr>
								<?php 
									}
								?>
								<tr>
									<td height="5" colspan="3"></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table align="center" cellspacing="0" cellpadding="0">
											<tbody><tr>
												<td nowrap=""><input type="submit" onclick="return confirmDelete(this.form,'delId_',);" name="cmdDelete" class="button" value=" Delete ">&nbsp;<input type="submit" class="button" name="cmdAddNew" value=" Add New ">&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" 
												onClick="return printWindow('PrintWeightmentDatasheetAll.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"<? } ?></td>
											</tr>
										</tbody></table>									</td>
								</tr>
								<tr>
									<td height="5" colspan="3"></td>
								</tr>
							</tbody></table>						</td>
					</tr>
				</tbody></table>
				<!-- Form fields end   -->			
				</td>
			</tr>	
			<input type="hidden" name="hidStockItemStatus" id="hidStockItemStatus">
			<input type="hidden" name="hidEditId" value="">
			<tr>
				<td height="10"></td>
			</tr>
		</tbody>
	</table>
	</form>
	<div id="dialog" title="Link to PHT Certificate " style="display:none" >
		<!--<p>
		This is the default dialog which is useful for displaying information. The dialog window can be moved, resized and closed with the 'x' icon.
			
		</p>-->
	</div>


<? if ($addMode) {?>
<SCRIPT LANGUAGE="JavaScript">
$(document).ready(function() {
//xajax_generateDatasheet();
});
</SCRIPT>
<? }?>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	var fldId  = 0;
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "data_sheet_date",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "data_sheet_date", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);

	
	Calendar.setup 
	(	
		{
			inputField  : "selectFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	
	
	
	Calendar.setup 
	(	
		{
			inputField  : "selectTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	function addNewWeighmentTableRow()
	{
		var rm_lot_id = document.getElementById('rm_lot_id').value;
		if(rm_lot_id == '')
		{
			alert('Please choose the rm lot id');
		}
		else
		{
			addNewRowProcurementAvailable();
		
		/*	var procurementAvailable = document.getElementById('procurementAvailable').value;
			 //alert(procurementAvailable);
			if(procurementAvailable == 1)
			{
				addNewRowProcurementAvailable();
			}
			else
			{
				//alert('Can not update without supplier ');
				alert('Multiple data entry cannot be possible for data with out procurement');
			}
			*/
		}
	}

	function addNewCertificateTableRow(rowCnt)
	{
		
		addCerificate('tblAddCerificateDetail','','','','',rowCnt);

	}

	
	function addNewCertificateTableRowEdit(rowCnt)
	{
		//alert("hii");
		var weight=document.getElementById('weight_'+rowCnt).value;
		var oldWeight=document.getElementById('oldWeight_'+rowCnt).value;
		//alert(weight+'---'+oldWeight);
		if((parseInt(weight)==parseInt(oldWeight)) || (parseInt(weight)<parseInt(oldWeight)))
		{
			jQuery("#addNew").hide();
		}
		else
		{	addCerificateEdit('tblAddCerificateDetail','','','','',rowCnt);
			jQuery("#addNew").show();
			
		}
		
			
	}
	
</script>


<?php 
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>