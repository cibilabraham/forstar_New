<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT );
 session_start();
 require("lib/errHandler.php");
 require("lib/databaseConnect.php");
 //rekha updated code 
 require("lib/databaseConnect_taskmgmt.php");
 // end code 
 require("lib/ResultSetIterator.php");
 
 require("lib/constants.php");

 //echo($url_afterDelProcessCode);
 require("lib/user_class.php");
 require("lib/session_class.php");

 require("lib/config.php");

 require("lib/fishmaster_class.php");
 require("lib/grademaster_class.php");
 require("lib/qualitymaster_class.php");
 require("lib/landingcenter_class.php");
 require("lib/preprocessor_class.php");
 require("lib/subsupplier_class.php");
 require("lib/process_class.php");
 require("lib/processcode_class.php"); 
 require("lib/packing_class.php"); 
 require("lib/competitor_class.php");
 require("lib/plantsandunits_class.php");
 require("lib/companydetails_class.php"); 
 require("lib/dailycatchentry_class.php");
 require("lib/dailyrates_class.php");
 require("lib/dailypreprocess_class.php"); 
 require("lib/dailyprocessing_class.php"); 
 require("lib/competitorscatch_class.php");
 require("lib/supplieraccount_class.php");
 require("lib/supplierpayments_class.php");
 require("lib/processorsaccounts_class.php");
 require("lib/processorspayments_class.php");
 require("lib/fishcategory_class.php");
 require("lib/unitmaster_class.php");
 require("lib/registrationtype_class.php");

 require("lib/dailycatchreport_class.php");
 require("lib/dailycatchsummary_class.php");
 require("lib/dailyprocessingreport_class.php");
 require("lib/purchasestatement_class.php");
 require("lib/preprocessingreport_class.php");
 require("lib/manageusers_class.php");
 require("lib/managerole_class.php"); 
 require("lib/accesscontrol_class.php");
 require("lib/modulemanager_class.php");
  require("lib/FolderAccess_class.php"); 


 require("lib/eucode_class.php");
 require("lib/glaze_class.php");
 require("lib/customer_class.php");
 require("lib/brand_class.php");
 require("lib/freezing_class.php");
 require("lib/mcpacking_class.php");
 require("lib/freezingstage_class.php"); 
 require("lib/frozenpacking_class.php"); 
 require("lib/dailyfrozenpacking_class.php");
 require("lib/purchaseorder_class.php");
 require("lib/paymentterms_class.php");
 require("lib/status_class.php");
 require("lib/orderprocessing_class.php"); 
 require("lib/labellingstage_class.php"); 
 require("lib/usdvalue_class.php");
 require("lib/manageipaddress_class.php"); 
 require("lib/packagingstructure_class.php"); 
 require("lib/settlementsummary_class.php"); 
 require("lib/processratelist_class.php");
 require("lib/processorsettlementsummary_class.php");
 require("lib/container_class.php");
 require("lib/invoice_class.php");
 require("lib/FrozenStockAllocation_class.php");
 require("lib/dailyfrozenreglazing_class.php");
 
  require("lib/displayrecord_class.php");
  
  require("lib/repacking_class.php");
  require("lib/dailyfrozenrepacking_class.php");
 
  require("lib/ConvertIntegerToString.php");
  
  require("lib/purchasereport_class.php");
  require("lib/processingactivities_class.php");

  require("lib/processingrestriction_class.php");
  require("lib/frozenpackingreport_class.php");
  require("lib/manageconfirm_class.php");
  require("lib/rmsupplycost_class.php");	

  require("lib/wastageratepercentage_class.php");
  require("lib/localquantityreport_class.php");
  require("lib/dailythawing_class.php");

 require("lib/freezercapacity_class.php");
 require("lib/dailyactivitychart_class.php");
 require("lib/dailyfreezingchart_class.php");
 require("lib/paymentstatus_class.php");
 require("lib/SettlementHistory_class.php");
 require("lib/PreProcessingPaymentStatus_class.php");
 require("lib/HealthCertificate_class.php");
 require("lib/SupplierLedgerAccount_class.php");
 require("lib/DailyRMCB_class.php");

//echo "hii";

 # Packing Starts Here
 require("lib/FrozenPackingQuickEntryList_class.php");
 require("lib/FPStkReportGroupList_class.php");
 require("lib/ProcessCodeSequence_class.php");

 #Inventory Start Here
 require("lib/category_class.php");
 require("lib/subcategory_class.php");
 require("lib/stockentry_class.php");
 require("lib/SupplierMaster_class.php");
 require("lib/supplierstock_class.php");
 require("lib/PurchaseOrderInventory_Class.php");
 require("lib/department_class.php");
 require("lib/goodsreceipt_class.php");
 require("lib/stockissuance_class.php");
 require("lib/stocksearch_class.php");
 require("lib/stockreport_class.php");
 require("lib/StockConsumption_class.php");
 require("lib/StockPurchaseReject_class.php");
 require("lib/PurchaseOrderReport_class.php");
 require("lib/StockSummary_class.php");
 require("lib/UnitGroup_class.php");
 require("lib/StockItemUnit_class.php");
 require("lib/SupplierRateList_class.php");	
 require("lib/FileManage_class.php");
 require("lib/ImportStock_class.php");
 require("lib/RevisePurchaseOrder_class.php");
 require("lib/StockReturn_class.php");
 require("lib/StockIssuanceReport_class.php");
 require("lib/CheckPointMaster_class.php");
 require("lib/StockGroup_class.php");
 require("lib/StockField_class.php");
 require("lib/stockrequisition_class.php");
 require("lib/PhysicalStockEntryInventory_class.php");
 require("lib/stockallocation_class.php");
 

 #Inventory Ends Here

 require("lib/Home_class.php");
 require("lib/ManageChallan_class.php");
 require("lib/ManageChallan_all_class.php");
 require("lib/ChallanVerification_class.php");
 require("lib/Managelotid_class.php");
  require("lib/ManageProcurmentPass_class.php");
 //require("lib/ManageProcurementGatePass_class.php");

 #Ingredient Starts Here
 require("lib/IngredientsMaster_class.php");
 require("lib/IngredientRateMaster_class.php");
 require("lib/IngredientRateList_class.php");
 require("lib/IngredientPO_class.php");
 require("lib/IngredientReceipt_class.php");
 require("lib/IngredientCriticalParameters_class.php");
 require("lib/ProductMaster_class.php");
 require("lib/ProductBatch_class.php");
 require("lib/ProductDetails_class.php");
 require("lib/IngredientCategory_class.php");
 require("lib/ProductCategory_class.php");
 require("lib/ProductState_class.php");
 require("lib/ProductGroup_class.php");
 require("lib/ProductBatchReport_class.php");
 require("lib/ProductionMatrixMaster_class.php");
 require("lib/IngredientReport_class.php");
 require("lib/ProductionManPower_class.php");
 require("lib/ProductionFishCutting_class.php");
 require("lib/ProductionMarketing_class.php");
 require("lib/ProductionTravel_class.php");
 require("lib/ProductionMatrix_class.php");
 require("lib/PackingLabourCost_class.php");
 require("lib/PackingSealingCost_class.php");
 require("lib/PackingCostMaster_class.php");
 require("lib/PackingMaterialCost_class.php");
 require("lib/PackingMatrix_class.php");
 require("lib/ProductMatrix_class.php");
 require("lib/StateMaster_class.php");
 require("lib/CityMaster_class.php");
 require("lib/DistributorMaster_class.php");
 require("lib/DistMarginRateList_class.php");
 require("lib/DistMarginStructure_class.php");	
 require("lib/SalesOrder_class.php");	
 require("lib/OrderDispatched_class.php");
 require("lib/RetailCounterMaster_class.php");
 require("lib/ProductConversion_class.php");
 require("lib/Claim_class.php");
 require("lib/ClaimProcessing_class.php");
 require("lib/MarginStructure_class.php");
 require("lib/ProductPricing_class.php");
 require("lib/ProductPriceRateList_class.php");
 require("lib/DistributorProductPrice_class.php"); 
 require("lib/RetailCounterStock_class.php"); 
 require("lib/ProductReport_class.php");
 require("lib/ComboMatrix_class.php");
 require("lib/AreaMaster_class.php");
 require("lib/Area_class.php");
 require("lib/PondMaster_class.php"); 
 require("lib/RmTestMaster_class.php");
  require("lib/VehicleType_class.php");
  require("lib/DriverMaster_class.php");
  require("lib/HarvestingEquipment_class.php");
  require("lib/HarvestingEquipmentMaster_class.php");
  require("lib/HarvestingChemicalMaster_class.php");
  require("lib/Designation_class.php");
  require("lib/EmployeeMaster_class.php");
  require("lib/VehicleMaster_class.php");
  require("lib/SealNumber_class.php");
  require("lib/SupplierGroup_class.php");
  require("lib/DAMSetting_class.php");

  require("lib/RMProcurmentOrder_class.php");
  require("lib/RMProcurmentGatePass_class.php");
  require("lib/PHTCertificate_class.php");
  require("lib/RMTestData_class.php");
  require("lib/RMReceiptGatePass_class.php");
  require("lib/UnitTransfer_class.php");
  require("lib/Soaking_class.php");
  require("lib/WeightmentAfterGrading_class.php");
  require("lib/PHTMonitoring_class.php");
  //echo "hii";
  require("lib/Report_class.php");
 // require("lib/Report_vel_class.php");
 // require("lib/Report_classathi.php");
 require("lib/VarianceReport_class.php");
  require("lib/FactoryUtilizationReport_class.php"); 
  
 require("lib/ProductionPlanning_class.php");
 require("lib/ProductionPlanningReport_class.php");
 require("lib/ManageRateList_class.php");
 require("lib/PurchaseIntent_class.php");
 require("lib/TaxMaster_class.php");
 require("lib/StateVatMaster_class.php");
 require("lib/SupplierIngredient_class.php"); 
 require("lib/ManageProduct_class.php");
 require("lib/ProductMRPMaster_class.php");
 require("lib/IngredientMainCategory_class.php");
 
 require("lib/SemiFinishProductMaster_class.php");
 require("lib/MCPkgWtMaster_class.php");
 require("lib/DistributorReport_class.php");
 require("lib/DistributorAccount_class.php");
 require("lib/PhysicalStockEntry_class.php");
 require("lib/StateVatRateList_class.php");
 require("lib/ProductStatus_class.php");
 require("lib/PhyStkEntryPacking_class.php");
 require("lib/TransporterMaster_class.php");
 require("lib/ZoneMaster_class.php");
 require("lib/WeightSlabMaster_class.php");
 //require("lib/AreaDemarcationMaster_class.php");
 require("lib/TransporterRateList_class.php");
 require("lib/TransporterRateMaster_class.php");
 //require("lib/TransporterOthers_class.php");
 require("lib/TransporterStatus_class.php");
 require("lib/TransporterAccount_class.php");
 require("lib/TransporterSettlementSummary_class.php");
 require("lib/TransporterPayments_class.php");
 require("lib/TransporterReport_class.php");
 require("lib/TransporterPaymentStatus_class.php");
 require("lib/SalesOrderReport_class.php");
 require("lib/PackingGroupMaster_class.php");
 require("lib/TransporterOtherCharges_class.php");
 require("lib/ProductIdentifierMaster_class.php"); 
 require("lib/DistributorReportDefinition_class.php"); 
 require("lib/InvoiceVerification_class.php"); 
 require("lib/PackingInstruction_class.php"); 	
 require("lib/AssignDocketNo_class.php");
 require("lib/TransporterWeightSlab_class.php");
 require("lib/SalesReport_class.php");
 require("lib/ManageGatePass_class.php");
 require("lib/SalesZoneMaster_class.php");


 require("lib/ChangesUpdateMaster_class.php");

 #Ingredient Ends Here
 require("lib/ManageID_class.php");
 require("lib/ManageDashboard_class.php");

  # Sales Team Management
 require("lib/SalesStaffMaster_class.php");
 require("lib/RetailCounterCategory_class.php");
 require("lib/RtCounterMarginRateList_class.php");
 require("lib/RtCounterMarginStructure_class.php");
 require("lib/DailySalesEntry_class.php");
 require("lib/SampleProductMaster_class.php");
 require("lib/SchemeMaster_class.php");
 require("lib/AssignScheme_class.php");
 require("lib/AssignRtCtDisplayCharge_class.php");
 require("lib/DailySalesReport_class.php");

 # Reports
 require("lib/StockHoldingCostReport_class.php");
 require("lib/StockWastageReport_class.php");
 require("lib/ProductionAnalysisReport_class.php");
 require("lib/DailyStockReport_class.php");


 # Others Master
 require("lib/BillingCompanyMaster_class.php");
 require("lib/DatabaseListMaster_class.php");
 require("lib/createdatabase_bakup_class.php");

 # Shipment
 require("lib/CountryMaster_class.php");
 require("lib/ShippingCompanyMaster_class.php");
 require("lib/AgentMaster_class.php");
 require("lib/InvoiceTypeMaster_class.php");
 require("lib/CarriageMode_class.php");
 require("lib/taskmaster_class.php");

	
 # System
 require("lib/ManageMenus_class.php");		
 require("lib/LogManager_class.php");		
 require("lib/ManageQuickLinks_class.php");	
 require("lib/RefreshTimeLimit_class.php");

 # Daily Freezing Chart
  require("lib/OperationType_class.php");
  require("lib/MonitoringParameters_class.php");	
  require("lib/InstalledCapacity_class.php");

 // require("lib/DailyFreezing_class.php");
  
 require("lib/DailyIceUsage_class.php");
 require("lib/ExciseDutyMaster_class.php");
 require("lib/ExcisableGoodsMaster_class.php");

  // Shipment Invoice Report
 require("lib/DNReport_class.php");
 require("lib/ShipmentInvoiceReport_class.php");

//require("lib/CommonProcess_ajax.php");	
//$ON_LOAD_SAJAX = true;

 //require("lib/bar_class.php"); //for uploading
  
 
 // For weightment data sheet
 require("lib/WeighmentDataSheet_class.php");
 require("lib/ManageRMLOTID_class.php");
 
 require("lib/DocumentationInstructions_class.php");
 require("lib/LoadingPort_class.php");
 require("lib/CommonReason_class.php");
 require("lib/ExporterMaster_class.php");
 require("lib/FrozenPackingRate_class.php");	
 require("lib/FrozenPackingRateList_class.php");	
 require("lib/FrozenPackingRateGrade_class.php");
 require("lib/FrznPkgAccounts_class.php");
 require("lib/FrozenPackRating_class.php");



 
require("lib/manageseal_class.php");
require("lib/rmvariancereport_class.php");
require("lib/PendingRMLotReport_class.php");
require("lib/FreezingReport_class.php");
require("lib/RMFreezingCalenderReport_class.php");
//require("lib/FGStockReport_class.php");
require("lib/LotwiseSMBSReport_class.php");
require("lib/YearwiseProductionReport_class.php");
require("lib/YearwisePurchaseReport_class.php");

//require("lib/dailyfrozenpackinglist_class.php");

###recipe rte
 require("lib/RecipeMainCategory_class.php");
 require("lib/RecipeSubCategory_class.php");
 require("lib/RecipeMaster_class.php");
 require("lib/IngredientPhysicalStock_class.php");


 require("lib/ProductionWorkingHours_class.php");
 require("lib/FuelRate_class.php");
 require("lib/StaffMaster_class.php");
 require("lib/DepartmentMasterRTE_class.php");
 
 require("lib/ProductionPower_class.php");
 require("lib/ProductionMarketingCost_class.php");
 require("lib/EmployeeCostCalculation_class.php");
 require("lib/StaffRoleMaster_class.php");
 require("lib/ProductionOtherCost_class.php");
 require("lib/ProductionFuelPrice_class.php");
 require("lib/ProductionHoldingCost_class.php");
 require("lib/ProductionAdvertisement_class.php");
require("lib/PackingMaterial_class.php");
require("lib/ProductionOperation_class.php");

require("lib/SecondaryProcessCode_class.php");


/*Sulabha Test Class */
//require("lib/SulabhaTest_class.php");
/*Sulabha Test Class */
require("lib/ProcessMaster_class.php");
require("lib/TransporterCost_class.php");
require("lib/ExportMaster_class.php");
require("lib/PaymentMaster_class.php");

 $databaseConnect_taskmgmt			=	new DatabaseConnect_taskmgmt();
 $databaseConnect			=	new DatabaseConnect();

 // $databaseConnect_taskmgmt			=	new DatabaseConnect_taskmgmt();
 $sessObj					=	new Session($databaseConnect);
 $userObj					=	new User($databaseConnect,$sessObj);
 $fishmasterObj				=	new FishMaster($databaseConnect);
 $grademasterObj			=	new GradeMaster($databaseConnect);
 $qualitymasterObj			=	new QualityMaster($databaseConnect);
 $landingcenterObj			=	new LandingCenter($databaseConnect);
 $preprocessorObj			=	new PreProcessor($databaseConnect);
 
 $objWeighmentDataSheet		=   new WeighmentDataSheet($databaseConnect);
 $objManageRMLOTID			=   new ManageRMLOTID($databaseConnect);
 
 $subsupplierObj			=	new SubSupplier($databaseConnect);
 $processObj				=	new Process($databaseConnect);
 $processcodeObj			=	new ProcessCode($databaseConnect);
 $packinggoodsObj			=	new PackingGoods($databaseConnect);
 $competitorObj				=	new Competitor($databaseConnect);
 $plantandunitObj			=	new PlantMaster($databaseConnect);
 $companydetailsObj			=	new CompanyDetails($databaseConnect);
 
 $dailycatchentryObj		=	new DailyCatchEntry($databaseConnect);
 $dailyratesObj				=	new DailyRates($databaseConnect);
 $dailypreprocessObj		=	new DailyPreProcessMain($databaseConnect);
 
 $dailyprocessingObj		=	new DailyProcessing($databaseConnect);
 
 $competitorscatchObj		=	new CompetitorsCatch($databaseConnect);
 $supplieraccountObj		=	new SupplierAccount($databaseConnect);
 $supplierpaymentsObj		=	new SupplierPayments($databaseConnect);
 $processorsaccountsObj		=	new ProcessorsAccounts($databaseConnect);
 $processorspaymentsObj		=	new ProcessorsPayments($databaseConnect);
 $fishcategoryObj			=	new FishCategory($databaseConnect);
 $unitmasterObj				=	new UnitMaster($databaseConnect);
 $damSettingObj				=	new DAMSetting($databaseConnect);
 
 
 $dailycatchreportObj		=	new DailyCatchReport($databaseConnect);
 $dailycatchsummaryObj		=	new DailyCatchSummary($databaseConnect);
 $dailyprocessingreportObj	=	new DailyProcessingReport($databaseConnect);
 $purchasestatementObj		=	new PurchaseStatement($databaseConnect);
 $preprocessingreportObj	=	new PreProcessingReport($databaseConnect);

 $manageusersObj			=	new ManageUsers($databaseConnect);
 $manageroleObj				=	new ManageRole($databaseConnect);
 $folderaccessObj			=	new FolderAccess($databaseConnect);
 
 $roleId					=	$sessObj->getValue("userRole");
 $accesscontrolObj			=	new AccessControl($databaseConnect,$roleId);
 $modulemanagerObj			=	new ModuleManager($databaseConnect);
 
 $eucodeObj					=	new EuCode($databaseConnect);
 $glazeObj					=	new Glaze($databaseConnect);
 $customerObj				=	new Customer($databaseConnect);
 $brandObj					=	new Brand($databaseConnect);
 $freezingObj				=	new Freezing($databaseConnect);
 $mcpackingObj				=	new MCPacking($databaseConnect);
 $freezingstageObj			=	new FreezingStage($databaseConnect);
 
 $frozenpackingObj			=	new FrozenPacking($databaseConnect); 
 $paymenttermsObj			=	new PaymentTerms($databaseConnect);
 $statusObj					=	new Status($databaseConnect);
 
 //$dailyfrozenpackinglistObj  =  new DailyFrozenPackingList($databaseConnect);
 $dailyfrozenpackingObj		=	new DailyFrozenPackingNew($databaseConnect);
 $purchaseorderObj			=	new PurchaseOrder($databaseConnect);
 $orderprocessingObj		=	new OrderProcessing($databaseConnect);
 $labellingstageObj			=	new LabellingStage($databaseConnect);
 $usdvalueObj				=	new USDValue($databaseConnect);
 $manageipaddressObj		=	new ManageIPAddress($databaseConnect);
 $packagingstructureObj		=	new PackagingStructure($databaseConnect);
 $settlementsummaryObj		=	new SettlementSummary($databaseConnect);
 $processratelistObj		=	new ProcessRateList($databaseConnect);
 $processorsettlementsummaryObj	=	new ProcessorSettlementSummary($databaseConnect);
 $containerObj				=	new Container($databaseConnect);
 $invoiceObj				=	new Invoice($databaseConnect);
 $frozenStockAllocationObj	=	new FrozenStockAllocation($databaseConnect);

 $displayrecordObj			=	new DisplayRecord($databaseConnect);
 
 $repackingObj				=	new Repacking($databaseConnect);
 $dailyfrozenrepackingObj	=	new DailyFrozenRePacking($databaseConnect);
 $dailyfrozenreglazingObj	=	new DailyFrozenReGlazing($databaseConnect);
 $purchasereportObj			=	new PurchaseReport($databaseConnect);
 $processingactivityObj		=	new ProcessingActivity($databaseConnect);
 $processingrestrictionObj	=	new ProcessingRestriction($databaseConnect);
 $frozenpackingreportObj	=	new FrozenPackingReport($databaseConnect);
 $manageconfirmObj			=	new ManageConfirm($databaseConnect);

 $rmsupplycostObj			=	new RMSupplyCost($databaseConnect);
 $wastageratepercentageObj	=	new WastageRatePercentage($databaseConnect);
 $localquantityreportObj	=	new LocalQuantityReport($databaseConnect);

 $dailythawingObj			=	new DailyThawing($databaseConnect);

 $freezercapacityObj		=	new FreezerCapacity($databaseConnect);
 $dailyactivitychartObj		=	new DailyActivityChart($databaseConnect);
 $dailyFreezingChartObj		=	new DailyFreezingChart($databaseConnect);

 $paymentstatusObj			=	new PaymentStatus($databaseConnect);
 $settlementHistoryObj		=	new SetlementHistory($databaseConnect);
 
 $preProcessPaymentStatusObj=	new PreProcessingPaymentStatus($databaseConnect);	
 $healthCertificateObj		=	new HealthCertificate($databaseConnect);
 $supplierLedgerAccountObj	=	new SupplierLedgerAccount($databaseConnect);	
 $dailyRMCBObj				= 	new DailyRMClosingBalance($databaseConnect);


 # Packing Starts Here
 $frznPkngQuickEntryListObj	=	new FrozenPackingQuickEntryList($databaseConnect);
 $fpStkReportGroupListObj	=	new FPStkReportGroupList($databaseConnect);
 $processCodeSequenceObj	= 	new ProcessCodeSequence($databaseConnect);

# System
	$idManagerObj 			= 	new IdManager($databaseConnect);
	$dashboardManagerObj	= 	new ManageDashboard($databaseConnect);

#Procuirment Start Here
$registrationTypeObj		=	new RegistrationType($databaseConnect);
$areaObj					=	new Area($databaseConnect);
$pondMasterObj				=	new PondMaster($databaseConnect);
$rmTestMasterObj			=	new RmTestMaster($databaseConnect);
$vehicleTypeObj				=	new VehicleType($databaseConnect);
$driverMasterObj			=	new DriverMaster($databaseConnect);
$harvestingEquipmentObj		=	new HarvestingEquipment($databaseConnect);
$harvestingEquipmentMasterObj    =	new HarvestingEquipmentMaster($databaseConnect);
$harvestingChemicalMasterObj    =	new HarvestingChemicalMaster($databaseConnect);
$designationObj				=	new Designation($databaseConnect);
$employeeMasterObj			=	new EmployeeMaster($databaseConnect);
$vehicleMasterObj			=	new VehicleMaster($databaseConnect);
$sealNumberObj				=	new SealNumber($databaseConnect);
$supplierGroupObj			=	new SupplierGroup($databaseConnect);

$rmProcurmentOrderObj		=	new ProcurementOrder($databaseConnect);
$rmProcurmentGatePassObj    =	new ProcurementGatePass($databaseConnect);

###commented by athira on 8-12-2014
/*$previousPageNameArr = explode('.php',basename($_SERVER['HTTP_REFERER']));
if(isset($previousPageNameArr[0]) && $previousPageNameArr[0]=='RMProcurmentGatePass')
{
	$userId		    =	$sessObj->getValue("userId");
	$loginTime      =   $sessObj->getValue("loginTime");
	$rmProcurmentGatePassObj->deleteSealAssigned($userId,$loginTime);
}*/

$phtCertificateObj		=	new PHTCertificate($databaseConnect);
$rmTestDataObj			=	new RMTestData($databaseConnect);
$rmReceiptGatePassObj   =	new RMReceiptGatePass($databaseConnect);
$unitTransferObj		=	new UnitTransfer($databaseConnect);
$soakingObj				=	new Soaking($databaseConnect);
$weightmentAfterGradingObj   =	new WeightmentAfterGrading($databaseConnect);
$phtMonitorngObj		=	new PHTMonitoring($databaseConnect);

$reportObj				=	new Report($databaseConnect);
//$reportVelObj    =	new Report_vel($databaseConnect);
//$reportathiObj   =	new Reportathi($databaseConnect);

$varianceReportObj      =	new VarianceReport($databaseConnect);
	
#Inventory Start Here
 $categoryObj			=	new Category($databaseConnect);
 $subcategoryObj		=	new SubCategory($databaseConnect);
 $stockObj			=	new Stock($databaseConnect);
  /* Changed on 08-11-08*/
 $supplierMasterObj		=	new SupplierMaster($databaseConnect);
 $supplierstockObj		=	new SupplierStock($databaseConnect);
 $purchaseOrderInventoryObj	=	new PurchaseOrderInventory($databaseConnect);
 $departmentObj			=	new Department($databaseConnect);
 $goodsreceiptObj		=	new GoodsReceipt($databaseConnect);
 $stockissuanceObj		=	new StockIssuance($databaseConnect);
 $stocksearchObj		=	new StockSearch($databaseConnect);
 $stockreportObj		=	new StockReport($databaseConnect);
 $stockConsumptionObj		=	new StockConsumption($databaseConnect);
 $stockPurchaseRejectObj	=	new StockPurchaseReject($databaseConnect);
 $purchaseOrderReportObj	=	new PurchaseOrderReport($databaseConnect);
 $stockSummaryObj		=	new StockSummary($databaseConnect);
 $unitGroupObj			=	new UnitGroup($databaseConnect);
 $stockItemUnitObj		=	new StockItemUnit($databaseConnect);
 $supplierRateListObj		= 	new SupplierRateList($databaseConnect);
 $fileManageObj 		= 	new FileManagement($databaseConnect);
 $importStockObj 		= 	new ImportStock();
 $revisePurchaseOrderObj	= 	new RevisePurchaseOrder($databaseConnect);
 $stockReturnObj		= 	new StockReturn($databaseConnect);
 $checkPointObj			= 	new CheckPointMaster($databaseConnect);
 $stockGroupObj			= 	new StockGroup($databaseConnect);
 $stockFieldObj			= 	new StockField($databaseConnect);
 $stockRequisitionObj		=	new StockRequisition($databaseConnect);
 $physicalStockInventoryObj		=	new PhysicalStockInventory($databaseConnect);
 $stockAllocationObj		=	new StockAllocation($databaseConnect);

#Inventory Ends Here

 $homeObj			=	new Home($databaseConnect);
 $manageChallanObj		=	new ManageChallan($databaseConnect);
 $manageChallanAllObj		=	new ManageChallanAll($databaseConnect);
 $challanVerificationObj	=	new ChallanVerification($databaseConnect);
 $manageLotIdObj		=	new Managelotid($databaseConnect);
 $manageProcurmentPassObj		=	new ManageProcurmentPass($databaseConnect);
 
 //$ManageProcurementGatePassObj		=	new ManageProcurementGatePass($databaseConnect);

 #Ingredient Starts Here
 $ingredientMasterObj		=	new IngredientMaster($databaseConnect);
 $ingredientRateMasterObj	=	new IngredientRateMaster($databaseConnect);
 $ingredientRateListObj		=	new IngredientRateList($databaseConnect);
 $ingredientPurchaseorderObj	=	new IngredientPurchaseOrder($databaseConnect);
 $ingredientReceiptObj		=	new IngredientReceipt($databaseConnect);
 $ingredientCriticalParametersObj		=	new IngredientCriticalParameters($databaseConnect);
 $productMasterObj		=	new ProductMaster($databaseConnect);
 $productBatchObj		=	new ProductBatch($databaseConnect);
 $productDetailsObj		=	new ProductDetails($databaseConnect);
 $ingredientCategoryObj		=	new IngredientCategory($databaseConnect);
 $productCategoryObj		=	new ProductCategory($databaseConnect);
 $productStateObj		=	new ProductState($databaseConnect);
 $productGroupObj		=	new ProductGroup($databaseConnect);
 $productBatchReportObj		=	new ProductBatchReport($databaseConnect);
 $productionMatrixMasterObj	=	new ProductionMatrixMaster($databaseConnect);
 $productionManPowerObj		=	new ProductionManPower($databaseConnect);
 $productionFishCuttingObj	=	new ProductionFishCutting($databaseConnect);
 $productionMarketingObj	=	new ProductionMarketing($databaseConnect);
 $productionTravelObj		=	new ProductionTravel($databaseConnect);
 $productionMatrixObj		=	new ProductionMatrix($databaseConnect); // Transaction page File
 $packingLabourCostObj		=	new PackingLabourCost($databaseConnect);
 $packingSealingCostObj		=	new PackingSealingCost($databaseConnect);
 $packingCostMasterObj		=	new PackingCostMaster($databaseConnect);
 $packingMaterialCostObj	=	new PackingMaterialCost($databaseConnect);
 $packingMatrixObj		=	new PackingMatrix($databaseConnect);
 $productMatrixObj		=	new ProductMatrix($databaseConnect);
 $stateMasterObj		=	new StateMaster($databaseConnect);
 $cityMasterObj			=	new CityMaster($databaseConnect);
 $areaMasterObj			=	new AreaMaster($databaseConnect);	
 $distributorMasterObj		=	new DistributorMaster($databaseConnect);
 $distMarginRateListObj		=	new DistributorMarginRateList($databaseConnect);
 $distMarginStructureObj	= 	new DistributorMarginStructure($databaseConnect);
 $retailCounterMasterObj	=	new RetailCounterMaster($databaseConnect);
 $productConversionObj		= 	new ProductConversion($databaseConnect);
 $claimObj			= 	new Claim($databaseConnect);
 $claimProcessingObj		= 	new ClaimProcessing($databaseConnect);
 $marginStructureObj		= 	new MarginStructure($databaseConnect);
 $productPricingObj		= 	new ProductPricing($databaseConnect);
 $productPriceRateListObj	=	new ProductPriceRateList($databaseConnect);
 $distProductPriceObj		=	new DistributorProductPrice($databaseConnect);
 $retailCounterStockObj		=	new RetailCounterStock($databaseConnect);
 $productReportObj		=	new ProductReport($databaseConnect);
 $comboMatrixObj		=	new ComboMatrix($databaseConnect);
 $manageRateListObj		=	new ManageRateList($databaseConnect);
 $productionPlanningObj		= 	new ProductionPlanning($databaseConnect); 
 $productionPlanningReportObj	= 	new ProductionPlanningReport($databaseConnect);
 $purchaseIntentObj		= 	new PurchaseIntent($databaseConnect); 
 $taxMasterObj			= 	new TaxMaster($databaseConnect); 
 $stateVatMasterObj		= 	new StateVatMaster($databaseConnect); 
 $supplierIngredientObj		= 	new SupplierIngredient($databaseConnect); 
 $manageProductObj		= 	new ManageProduct($databaseConnect); 
 $productMRPMasterObj		= 	new ProductMRPMaster($databaseConnect); 
 $ingMainCategoryObj		=	new IngredientMainCategory($databaseConnect);
 $semiFinishProductObj		=	new SemiFinishedProduct($databaseConnect);
 $mcPkgWtMasterObj		= 	new MCPkgWtMaster($databaseConnect);
 $distributorReportObj		= 	new DistributorReport($databaseConnect);
 $distributorAccountObj		= 	new DistributorAccount($databaseConnect);
 $physicalStockEntryObj		= 	new PhysicalStockEntry($databaseConnect);
 $stateVatRateListObj		=	new StateVatRateList($databaseConnect);
 $productStatusObj		=	new ProductStatus($databaseConnect);
 $phyStkEntryPackingObj		= 	new PhysicalStockEntryPacking($databaseConnect);
 $transporterMasterObj		=	new TransporterMaster($databaseConnect);	
 $zoneMasterObj			=	new ZoneMaster($databaseConnect);
 $weightSlabMasterObj		=	new WeightSlabMaster($databaseConnect);
 //$areaDemarcationMasterObj	= 	new AreaDemarcationMaster($databaseConnect);
 $transporterRateListObj	=	new TransporterRateList($databaseConnect);
 $transporterRateMasterObj	=	new TransporterRateMaster($databaseConnect);	
 //$transporterOthersObj		= 	new TransporterOthers($databaseConnect);
 $transporterStatusObj		= 	new TransporterStatus($databaseConnect);	
 $transporterAccountObj			= 	new TransporterAccount($databaseConnect);
 $transporterSettlementSummaryObj = 	new TransporterSettlementSummary($databaseConnect);
 $transporterPaymentsObj		=	new TranspoterPayments($databaseConnect);
 $transporterReportObj			=	new TransporterReport($databaseConnect);
 $transporterPaymentStatusObj	= 	new TransporterPaymentStatus($databaseConnect);
 $salesOrderReportObj			=	new SalesOrderReport($databaseConnect);
 $packingGroupMasterObj			=	new PackingGroupMaster($databaseConnect);
 $transporterOtherChargesObj    = 	new TransporterOtherCharges($databaseConnect);
 $productIdentifierObj			= 	new ProductIdentifierMaster($databaseConnect);
 $distReportDefinitionObj		= 	new DistributorReportDefinition($databaseConnect);
 $invoiceVerificationObj		=	new InvoiceVerification($databaseConnect);  	
 $packingInstructionObj			=	new PackingInstruction($databaseConnect);  	
 $assignDocketNoObj				=	new AssignDocketNo($databaseConnect);
 $salesOrderObj					= 	new SalesOrder($databaseConnect);
 $orderDispatchedObj			= 	new OrderDispatched($databaseConnect);
 $transporterWeightSlabObj		= 	new TransporterWeightSlab($databaseConnect);
 $salesReportObj				=	new SalesReport($databaseConnect);
 $manageGatePassObj				=	new ManageGatePass($databaseConnect);
 $salesZoneObj					=	new SalesZoneMaster($databaseConnect);
 $taskMasterObj		=	new TaskMaster($databaseConnect);

 
 
###recipe spex rte
 $recpMainCategoryObj		=	new RecipeMainCategory($databaseConnect);
 $recpSubCategoryObj		=	new RecipeSubCategory($databaseConnect);
 $recipeMasterObj			=	new RecipeMaster($databaseConnect);

 // Changes Updation	
 $changesUpdateMasterObj		= 	new ChangesUpdateMaster($databaseConnect, $salesOrderObj, $taxMasterObj, $marginStructureObj, $distMarginStructureObj, $distMarginRateListObj, $manageRateListObj);

 $ingredientReportObj			=	new IngredientReport($databaseConnect);
 #Ingredient Ends Here
 
  # Sales Team Management
 $salesStaffMasterObj			= 	new SalesStaffMaster($databaseConnect);	
 $retailCounterCategoryObj		= 	new RetailCounterCategory($databaseConnect);
 $rtCountMarginRateListObj		= 	new RetailCounterMarginRateList($databaseConnect);
 $rtCounterMarginStructureObj	= 	new RetailCounterMarginStructure($databaseConnect);
 $dailySalesEntryObj			=	new DailySalesEntry($databaseConnect);
 $sampleProductMasterObj		=	new SampleProduct($databaseConnect);
 $schemeMasterObj				=	new SchemeMaster($databaseConnect);
 $assignSchemeObj				=	new AssignSchemeMaster($databaseConnect);
 $assignRtCtDisChargeObj		=	new AssignRtCtDisplayChargeMaster($databaseConnect);
 $dailySalesReportObj			=	new DailySalesReport($databaseConnect);

 # Shipment Obj
 $countryMasterObj				=	new CountryMaster($databaseConnect);
 $shippingCompanyMasterObj		= 	new ShippingCompanyMaster($databaseConnect);
 $agentMasterObj				= 	new AgentMaster($databaseConnect);
 $invoiceTypeMasterObj			= 	new InvoiceTypeMaster($databaseConnect);
 $carriageModeObj				= 	new CarriageMode($databaseConnect);

  # Reports
 $stockHoldingCostReportObj		=	new StockHoldingCostReport($databaseConnect);
 $stockWastageReportObj			= 	new StockWastageReport($databaseConnect);
 $stockIssuanceReportObj		= 	new StockIssuanceReport($databaseConnect);	
 $productionAnalysisReportObj	=	new ProductionAnalysisReport($databaseConnect);
 $dailyStockReportObj			=	new DailyStockReport($databaseConnect);

// $barObj			=	new Bar($databaseConnect); //for uploading
 
  # Others Master	
  $billingCompanyObj			=	new BillingCompanyMaster($databaseConnect);
  # Database Backup
  $DatabaseListObj =	new DatabaseListMaster($databaseConnect);
  $CreateDBBackupObj =	new CreateDBBackup($databaseConnect);
 
 # System
  $manageMenuObj				=	new ManageMenus($databaseConnect);
  $manageQuickLinksObj			=	new ManageQuickLinks($databaseConnect);
  $logManagerObj				=	new LogManager($databaseConnect, $sessObj);
  $refreshTimeLimitObj			=	new RefreshTimeLimit($databaseConnect);

 # Daily Freezing Chart
 $operationTypeObj 				=	new OperationType($databaseConnect);
 $monitoringParametersObj 		=	new MonitoringParameters($databaseConnect);
 $installedCapacityObj 			=	new InstalledCapacity($databaseConnect);
// $dailyFreezingObj 			= new DailyFreezing($databaseConnect);

 $dailyIceUsageObj				=	new DailyIceUsage($databaseConnect);

 $exciseDutyMasterObj			=	new ExciseDutyMaster($databaseConnect); 
 $excisableGoodsMasterObj		=	new ExcisableGoodsMaster($databaseConnect);

 // Shipment Report
 $dnReportObj					= 	new DebitNoteReport($databaseConnect);
 $shipmentInvoiceReportObj		= 	new ShipmentInvoiceReport($databaseConnect);

 $docInstructionsObj			=	new DocumentationInstructions($databaseConnect);
 $loadingPortObj				=	new LoadingPort($databaseConnect);
 $commonReasonObj				=	new CommonReason($databaseConnect);
 $exporterMasterObj				=	new ExporterMaster($databaseConnect);
 $frozenPackingRateObj			=	new FrozenPackingRate($databaseConnect);
 $frozenPackingRateListObj		=	new FrozenPackingRateList($databaseConnect);
 $frozenPackingRateGradeObj		=	new FrozenPackingRateGrade($databaseConnect);
 $frznPkgAccountsObj			=	new FrznPkgAccounts($databaseConnect);
 $frznPkgRatingObj				=	new FrozenPackRating($databaseConnect); 	
 	
	
$manageSealObj					=	new ManageSeal($databaseConnect);
$rmVarianceReportObj			=	new RMVarianceReport($databaseConnect);
$pendingRMLotReportObj			=	new PendingRMLotReport($databaseConnect);
$freezingReportObj				=	new FreezingReport($databaseConnect);
$rmFreezingCalendarReportObj	=	new RMFreezingCalendarReport($databaseConnect);
$factoryUtilizationReportObj	=	new FactoryUtilizationReport($databaseConnect);
$lotwiseSMBSReportObj			=	new LotwiseSMBSReport($databaseConnect);
$yearwiseProductionReportObj	=	new YearwiseProductionReport($databaseConnect);
$yearwisePurchaseReportObj		=	new YearwisePurchaseReport($databaseConnect);
//$fgStockReportObj				=	new FGStockReport($databaseConnect);


$productionWorkingHoursObj		=	new ProductionWorkingHours($databaseConnect);
$fuelRateObj			=	new FuelRate($databaseConnect);
$staffMasterObj			=	new StaffMaster($databaseConnect);
$departmentMasterObj			=	new DepartmentMaster($databaseConnect);

$productionPowerObj			=	new ProductionPower($databaseConnect);
$productionMarketingCostObj			=	new ProductionMarketingCost($databaseConnect);
$productionMarketingCostObj			=	new ProductionMarketingCost($databaseConnect);
$productionOtherCostObj			=	new ProductionOtherCost($databaseConnect);
$productionFuelPriceObj			=	new ProductionFuelPrice($databaseConnect);
$staffRoleMasterObj				=	new StaffRoleMaster($databaseConnect);
$productionHoldingCostObj		=	new ProductionHoldingCost($databaseConnect);
$employeeCostCalculationObj		= new EmployeeCostCalculation($databaseConnect);
$productionAdvertisementObj		= new ProductionAdvertisement($databaseConnect);
$packingMaterialObj				= new PackingMaterial($databaseConnect);
$productionOperationObj			= new ProductionOperation($databaseConnect);

$ingredientPhysicalStockObj	=new IngredientPhysicalStock($databaseConnect);
$secondaryProcessCodeObj			= new SecondaryProcessCode($databaseConnect);

/*Sulabha Test Class Object*/
//sulabhaTestObj = new SulabhaTest($databaseConnect);
/*Sulabha Test Class Object*/

/* Transporter Cost Class Object */

$transportCostMasterObj		=	new TransporterCost($databaseConnect);

/* Transporter Cost Class Object */

/*Process Master Starts*/
$processMasterObj = new ProcessMaster($databaseConnect);
/*Process Master Ends*/

/*Export Master Object */
$exportMasterObj = new ExportMaster($databaseConnect);
/*Export Master Object */

/* Payment Master Object */
$paymentMasterObj	=	new PaymentMaster($databaseConnect);
/* Payment Master Object */


 $sessObj->chkLogin($insideIFrame);
 
  #Getting Curret URL
  $currentFile = $_SERVER["SCRIPT_NAME"];
  $parts = Explode('/', $currentFile);
  $currentUrl = $parts[count($parts) - 1];
  #USD VALUE
  $oneUSD	=	$usdvalueObj->findUSDValue();
  #NUM. ROWS TO BE DISPLAYED
  $limit 	=	$displayrecordObj->findDisplayRecord();

  # Get Logged User Id
  $userId	= $sessObj->getValue("userId");

  # Create Log file	
  $logManagerObj->createLogFile($currentUrl);
  $dailycatchentryObj->ininclude();
  $supplierstockObj->deleteSupplierQtyZeroRecord();
	


	
?>