<?php
//require_once("lib/databaseConnect.php");
//require_once("PHTCertificate_class.php");
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	
$xajax->configure('statusMessages', true);
	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}
  		}
			
		function addDropDownOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}
	}
	
	
	
	function getField($transactionId,$selId)
	{
		
		$objResponse 			= new xajaxResponse();
		
		$databaseConnect 		= new DatabaseConnect();
		
		$reportathiObj 	= new Reportathi($databaseConnect);
		
		
		switch($transactionId)
		{
		case 1:
			{
				//$tableName="t_phtcertificate";
				$fieldName=Array('a.PHTCertificateNo'=>'PHT Certificate No','b.category'=>'Species','c.supplier_group_name'=>'Supplier group','d.name'=>'Supplier','e.pond_name'=>'Pond Name','e.pond_qty'=>'PHT Quantity',' a.date_of_issue'=>'Date of Issue','a.date_of_expiry'=>'Date of expiry','a.received_date'=>'Received date');
				break;
			}
		case 2:
			{
				$fieldName=Array('a.date_on'=>'Date','b.new_lot_Id'=>'RM Lot ID','d.name'=>'Supplier','c.supplier_group_name'=>'Supplier group','a.species'=>'Species','a.supply_qty'=>'Supply Qty','e.PHTCertificateNo'=>'PHT Certificate No','a.pht_Qty'=>'PHT Qty','a.set_off_Qty'=>'Set off Qty','a.balance_Qty'=>'Balance Qty');
				break;
			}
		case 3:
			{
				//$tableName="t_rmprocurmentorder";
				$fieldName=Array('a.gatePass'=>'Procurement Id','c.name'=>'Company','d.supplier_group_name'=>'Supplier group name','e.name'=>'Supplier name','e.address'=>'Supplier address','f.pond_name'=>'Pond name','f.address'=>'Pond address','a.date_of_entry'=>'Date of entry','driver_Name'=>'Driver Name','vehicle_No'=>'Vehicle No','equipment_Name'=>'Equipment Name','max_equipment'=>'Max equipment','equipment_issued'=>'Equipment issued','difference'=>'Difference', 'chemical'=>'Chemical','chemical_required'=>'Chemical required','chemical_issued'=>'Chemical issued');
				break;
			}
		case 4:
			{
				//$tableName="t_rmtestdata";
				$fieldName=Array('unit'=>'Unit Name','lot'=>'Lot id','test_name'=>'Test name','test_method'=>'Test Method','date_of_testing'=>'Date of Testing','result'=>'Result');
				break;
			}
		case 5:
			{
				$tableName="t_rmreceiptgatepass";
				break;
			}
		case 6:
			{
				$tableName="t_soaking";
				break;
			}
		case 7:
			{
				$tableName="t_unittransfer";
				break;
			}
		case 8:
			{
				$tableName="weighment_data_sheet";
				break;
			}
		case 9:
			{
				
				$fieldName=Array('b.new_lot_Id'=>'RM Lot ID','a.supplyDetails'=>'Supply Details','weight' => 'Weight','gradeType' => 'Grade Type','a.sumtotal'=>'Sum total','a.totalweight'=>'Total weight','a.difference'=>'Difference');
				break;
				//$tableName="t_rmweightaftergrading";
				//$fieldName=Array('b.new_lot_Id'=>'RM Lot ID','a.supplyDetails'=>'Supply Details','a.sumtotal'=>'Sum total','a.totalweight'=>'Total weight','a.difference'=>'Difference');
				//break;
			}
		case 10:
			{
				$tableName="t_dailypreprocess_entries";
				break;
			}
		case 11:
			{
				$tableName="t_dailyfrozenpacking_main";
				break;
			}
			
		}
		//$fields=$reportObj->describe($tableName);
		//$objResponse->alert($fields);
		
		
		if (sizeof($fieldName)>0) {
		
			addDropDownOptions("reportField", $fieldName, $selId, $objResponse);
		
		}
	
		return $objResponse;
	}
	
	
	$xajax->register(XAJAX_FUNCTION,'getField', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	
	$xajax->ProcessRequest();
?>