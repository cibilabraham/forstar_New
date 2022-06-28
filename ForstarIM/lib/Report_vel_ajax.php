<?php
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
				$tableName="t_rmprocurmentorder";
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
				$fieldName=Array('process_type'=>'Process Type','lot_Id'=>'Lot Id','procurment_Gate_PassId'=>'Procurment Gate Pass Number','vehicle_Number'=>'Vehicle Number','driver'=>'Driver Name','in_Seal'=>'In Seal number','result'=>'Result','seal_No'=>'Seal numbers','out_Seal'=>'Seals Returned','verified'=>'Verified by','labours'=>'Labours','Company_Name'=>'Company Name','unit'=>'Unit','supplier_Challan_No'=>'Supplier Challan No','supplier_Challan_Date'=>'Supplier Challan date','date_Of_Entry'=>'Date of entry');
				//$tableName="t_rmreceiptgatepass";
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
				$fieldName = array('a.new_lot_Id' => 'RM LOT ID','a.supplier_Details' => 'Supplier Details',
							   'b.name' => 'Current Unit','c.process_type' => 'Current Process Type',
							   'd.name' => 'Previous Unit','e.process_type' => 'Previous Process Unit',
							   'a.active' => 'Status');
				break;
			}
		case 8:
			{
				$fieldName=Array('i.lot_Id'=>'RM Lot ID','a.data_sheet_sl_no'=>'Data Sheet Sl NO',
						 'a.data_sheet_date' => 'Data Sheet Date','b.gatePass'=>'Gate Pass',
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
				break;
			}
		case 9:
			{
				$fieldName=Array('b.new_lot_Id'=>'RM Lot ID','a.supplyDetails'=>'Supply Details','weight' => 'Weight','a.sumtotal'=>'Sum total','a.totalweight'=>'Total weight','a.difference'=>'Difference');
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