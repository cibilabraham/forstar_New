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
		
		$reportObj 	= new Report($databaseConnect);
		
		
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
				$tableName="t_phtmonitoring";
				break;
			}
		case 3:
			{
				//$tableName="t_rmprocurmentorder";
				$fieldName=Array('a.gatePass'=>'Procurment Gate Pass number','b.name'=>'Company','c.supplier_group_name'=>'Suppler Group Name','d.name'=>'Supplier Name','a.supplier_address'=>'Supplier address','e.pond_name'=>'Pond Name','a.pond_address'=>'Pond Address','a.date_of_entry'=>'Date Of Entry','driver_Name'=>'Driver Name','vehicle_No'=>'Vehicle Number','equipment_Name'=>'Equipment Name','max_equipment'=>'Maximum Equipment','equipment_issued'=>'Equipment Issued','difference'=>'Difference','chemical'=>'Chemical Name','chemical_required'=>'Chemical Required','chemical_issued'=>'Chemical Issued');
				break;
			}
		case 4:
			{
				//$tableName="t_rmtestdata";
				$fieldName=Array('b.name'=>'Unit Name','c.new_lot_Id'=>'Lot id','d.test_name'=>'Test name','a.test_method'=>'Test Method','date_of_testing'=>'Date of Testing','result'=>'Result');
				break;
			}
		case 5:
			{	
				//$fieldName=Array('process_type'=>'Process Type','lot_Id'=>'Lot Id','procurment_Gate_PassId'=>'Procurment Gate Pass Number','vehicle_Number'=>'Vehicle Number','driver'=>'Driver Name','in_Seal'=>'In Seal number','result'=>'Result','seal_No'=>'Seal numbers','out_Seal'=>'Seals Returned','verified'=>'Verified by','labours'=>'Labours','Company_Name'=>'Company Name','unit'=>'Unit','supplier_Challan_No'=>'Supplier Challan No','supplier_Challan_Date'=>'Supplier Challan date','date_Of_Entry'=>'Date of entry');
				$fieldName=Array('b.process_type'=>'Process Type','a.lot_Id'=>'Lot Id','c.gatePass'=>'Procurment Gate Pass Number','d.vehicle_number'=>'Vehicle Number','e.name_of_person'=>'Driver Name','f.seal_number'=>'In Seal number','a.result'=>'Result','a.seal_No'=>'Seal numbers','g.seal_number'=>'Seals Returned','h.name'=>'Verified by','a.labours'=>'Labours','i.name'=>'Company Name','j.name'=>'Unit','a.supplier_Challan_No'=>'Supplier Challan No','a.supplier_Challan_Date'=>'Supplier Challan date','a.date_Of_Entry'=>'Date of entry');
				break;
			}
		case 6:
			{
				$fieldName=Array('b.new_lot_Id'=>'Lot Id','c.process_type'=>'Processing Stage','a.supplier_Details'=>'Supplier Challan Number','a.available_Qty'=>'Available Quantity','a.soak_In_Count'=>'Soak In Count','a.soak_In_Qty'=>'Soak In quantity','a.soak_In_Time'=>'Soak in Time','a.soak_Out_Count'=>'Soak out count','a.soak_Out_Qty'=>'Soak Out quantity','a.soak_Out_Time'=>'Soak Out Time','a.temperature'=>'Temperature','a.gain'=>'Gain','d.chemical_name'=>'Chemical Used','a.chemcal_Qty'=>'Chemical Quantity');
				//$tableName="t_soaking";
				break;
			}
		case 7:
			{
				//$tableName="t_unittransfer";
				$fieldName=Array('rm_lot_Id'=>'Current Lot Id','supplier_Details'=>'Supplier Challan number','current_Unit'=>'Current unit','current_Stage'=>'Current stage','unit_Name'=>'New unit','process_Type'=>'New Stage','new_lot_Id'=>'new lot Id');
			
				break;
			}
		case 8:
			{
				$tableName="weighment_data_sheet";
				break;
			}
		case 9:
			{
				$tableName="t_rmweightaftergrading";
				break;
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
		// $fields=$reportObj->describe($tableName);
		//$objResponse->alert($fields);
		
		
		if (sizeof($fieldName)>0) {
		
			addDropDownOptions("reportField", $fieldName, $selId, $objResponse);
		
		}
	
		return $objResponse;
	}
	
	
	$xajax->register(XAJAX_FUNCTION,'getField', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	
	$xajax->ProcessRequest();
?>