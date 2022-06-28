<?php
class WeightmentAfterGrading
{  
	
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function WeightmentAfterGrading(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	function getSupplierDetail($rmLotId)
	{
		 $qry	=	"select id, farmer_at_harvest,total_quantity from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
		$result	= $this->databaseConnect->getRecord($qry);
		
		return $result;
	}
	
	# Filter grade List
	function getGrade($gradeId)
	{
	
		//$qry	=h	"select id, grade_count from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
		//$qry	=	"select id, grade_count from weighment_data_sheet_grade_count where main_id='$gradeId' order by id asc";
		$qry	=	"select id, grade_count from t_weightment_data_entries where weightment_data_sheet_id='$gradeId' order by id asc";
		//echo $qry;
		// $result	= $this->databaseConnect->getRecords($qry);
		// return $result;
		//$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	
	# Filter get weight
	function getWeight($rmLotId)
	{
		$qry	=	"select id, total_quantity from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
		
	}
	
	function getGradeId($rmLotId)
	{
		$qry	=	"select id  from weighment_data_sheet where rm_lot_id='$rmLotId' ";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
		
	}
	//function addWeightAfterGrading($rmLotId, $supplier,$pondName, $total, $effectiveWeight,$difference,$userId)
	function addWeightAfterGrading($materialType,$rmLotId, $supplier,$pondName, $total, $effectiveWeight,$userId)
	{
		$qry	= "insert into t_rmweightaftergrading(material_type,rmLotId,supplier_name,pond_name,sumtotal,totalweight,created_on, created_by) values('$materialType','$rmLotId','$supplier','$pondName','$total','$effectiveWeight', Now(),'$userId')";
		//$qry	= "insert into t_rmweightaftergrading(rmLotId, supplyDetails,sumtotal,totalweight,difference,created_on, created_by) values('$rmLotId','$supplyDetails','$total','$totalwt','$difference', Now(),'$userId')";
		//echo $qry;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	//function addWeightAfterGradingDetails($lastId,$fish_id,$process_code_id,$count_code, $grandsingle, $weightsingle,$lotidStatus,$userId)
	function addWeightAfterGradingDetails($lastId,$fish_id,$process_code_id, $grandsingle, $weightsingle,$lotidStatus,$userId)
	{
 	 $qry	= "insert into t_rmweightaftergradingdetails(weightment_grading_id,fish_id,process_code_id,gradeID,weight,lotid_available,created_on, created_by) 
				   values('$lastId','$fish_id','$process_code_id','$grandsingle','$weightsingle','$lotidStatus', Now(),'$userId')";
		 //echo $qry;die;
			
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	
	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{	
		// $fromDate = dta
		// $fromDate = date('Y-m-d H:i:s',strtotime($fromDate.'-1 day'));
		// $tillDate = date('Y-m-d H:i:s',strtotime($tillDate.'+1 day'));
		
		 $qry	= "select  a.ID,b.rm_lotid,b.alpha_character,c.name,d.pond_name,a.sumtotal,a.totalweight,a.difference,a.active from `t_rmweightaftergrading` a 
				   left join t_manage_rm_lotid b on a.rmLotId=b.id  
				   left join supplier c on a.supplier_name=c.id 
				   left join m_pond_master d on a.pond_name=d.id 
				   where a.created_on >= '$fromDate' and a.created_on <= '$tillDate' order by a.created_on desc limit $offset, $limit";
		
		// $qry	= "select  a.ID,b.lot_id,c.name,d.pond_name,a.sumtotal,a.totalweight,a.difference from `t_rmweightaftergrading` a 
				   // left join t_rmreceiptgatepass b on a.rmLotId=b.id  
				   // left join supplier c on a.supplier_name=c.id 
				   // left join m_pond_master d on a.pond_name=d.id 
				   // where a.created_on >= '$fromDate' and a.created_on <= '$tillDate' order by a.created_on desc limit $offset, $limit";
		//$qry	= "select  ID, rmLotId, supplyDetails,sumtotal,totalweight,difference from `t_rmweightaftergrading` where created_on>='$fromDate' and created_on<='$tillDate' order by created_on desc limit $offset, $limit";
		// echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	# Returns all RM Test Data
	function fetchAllRecords()
	{	
		
		$qry	= "select  a.ID,b.rm_lotid,b.alpha_character,c.name,d.pond_name,a.sumtotal,a.totalweight,a.difference,a.active from `t_rmweightaftergrading` a 
				   left join t_manage_rm_lotid b on a.rmLotId=b.id  
				   left join supplier c on a.supplier_name=c.id 
				   left join m_pond_master d on a.pond_name=d.id 
				    order by a.created_on desc ";
		/*$qry	= "select  a.ID,b.rm_lotid,b.alpha_character,c.name,d.pond_name,a.sumtotal,a.totalweight,a.difference from `t_rmweightaftergrading` a 
		join t_manage_rm_lotid b on a.rmLotId=b.id  
		join supplier c on a.supplier_name=c.id 
		join m_pond_master d on a.pond_name=d.id order by a.created_on desc ";*/
		//$qry	= "select ID, rmLotId, supplyDetails,sumtotal,totalweight,difference from `t_rmweightaftergrading` order by created_on desc ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	// for pagination
	function fetchAllDateRangeRecords($fromDate, $tillDate) 
	{
		 $qry	= "select  a.ID,b.rm_lotid,b.alpha_character,c.name,d.pond_name,a.sumtotal,a.totalweight,a.difference,a.active from `t_rmweightaftergrading` a 
		left join t_manage_rm_lotid b on a.rmLotId=b.id  
		left join supplier c on a.supplier_name=c.id 
		left join m_pond_master d on a.pond_name=d.id  where a.created_on>='$fromDate' and a.created_on<='$tillDate' order by a.created_on desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getWeightAfterGradingDetail($WeightGradingId)
	{	
		$qry	=	"select a.id,a.gradeID,a.weight,a.fish_id,a.process_code_id,
		a.count_code, a.process_code_id ,a.lotid_available,c.name,b.code from t_rmweightaftergradingdetails a left join m_processcode b on a.process_code_id=b.id left join m_fish c on a.fish_id=c.id where weightment_grading_id='$WeightGradingId' order by ID asc";
		/*$qry	=	"select id, gradeID,weight,fish_id,process_code_id,count_code,
					(select group_concat(id,'$$',code) FROM m_processcode  WHERE fish_id = t_rmweightaftergradingdetails.fish_id ) as process_codes ,lotid_available
					from t_rmweightaftergradingdetails where weightment_grading_id='$WeightGradingId' order by ID asc";*/
		//$qry	=	"select id, gradeType,weight from t_rmweightaftergradingdetails where gradeID='$WeightGradingId' order by ID asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getLotNm($LotId)
	{
		$qry	=	"select id, new_lot_Id from t_unittransfer where id='$LotId' order by id asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function getGradeNm($gradeID)
	
	{	
	$qry	=	"select id, code from m_grade where id='$gradeID' order by id asc";
	//echo $qry;
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	
	function getGradeName($mainID)
	{
		 $qry	=	"select id, grade_count from weighment_data_sheet_grade_count where main_id='$mainID' order by id asc";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	function deleteWeightAfterGrading($WeightGradingId)
	{
		$qry	= " delete from t_rmweightaftergrading where ID=$WeightGradingId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
		function deleteWeightAfterGradingDetails($WeightGradingId)
	{
		$qry	= " delete from  t_rmweightaftergradingdetails where weightment_grading_id	=$WeightGradingId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function fetchAllWeightAfterGrading($WeightGradingId)
	{	$qry	= "select  a.ID,b.rm_lotid,b.alpha_character,c.name,d.pond_name,a.sumtotal,a.totalweight,a.difference,a.rmLotId from `t_rmweightaftergrading` a 
				   left join t_manage_rm_lotid b on a.rmLotId=b.id  
				   left join supplier c on a.supplier_name=c.id 
				   left join m_pond_master d on a.pond_name=d.id  where a.ID='$WeightGradingId' ";
		//$qry	= "select ID, rmLotId, supplyDetails,sumtotal,totalweight,difference from `t_rmweightaftergrading` where ID='$WeightGradingId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function find($WeightAfterGradingId)
	{
		$qry	= "select *,
				 (select CONCAT(alpha_character,rm_lotid) from t_manage_rm_lotid 
				 where id = t_rmweightaftergrading.rmLotId) as rm_lot_id_value from t_rmweightaftergrading where id=$WeightAfterGradingId";
		return $this->databaseConnect->getRecord($qry);
	}
	
	//function updateweightmentAfterGrading($editId,$rmLotId, $supplier,$pondName, $total, $effectiveWeight,$difference)
	function updateweightmentAfterGrading($editId,$rmLotId, $supplier,$pondName, $total, $effectiveWeight)
	{
		$qry	= " update t_rmweightaftergrading set rmLotId='$rmLotId', supplier_name='$supplier',pond_name='$pondName',sumtotal='$total', totalweight='$effectiveWeight' where id='$editId'";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	function updateweightmentAfterGradingDetails($rmId,$fish_id,$process_code_id,$count_code, $grandsingle, $weightsingle,$lotidStatus)
	//function updateweightmentAfterGradingDetails($gradeId,$editId, $grandsingle, $weightsingle,$userId)
	{	
		$qry	= "update t_rmweightaftergradingdetails set fish_id = '$fish_id', process_code_id = '$process_code_id' , count_code = '$count_code',
				   gradeID='$grandsingle', weight='$weightsingle',lotid_available='$lotidStatus' where id=$rmId";
		// $qry	= "update t_rmweightaftergradingdetails set gradeID='$editId', gradeType='$grandsingle', weight='$weightsingle' where id=$gradeId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	function deleteweightmentGradingDetails($gradeId)
	{
		$qry	= "delete from  t_rmweightaftergradingdetails where id=$gradeId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function getAllLotIds_old()
	{	
	
		$qry	= "select a.id,CONCAT(a.alpha_character,a.rm_lotid) as lot_Id from t_manage_rm_lotid a inner join t_dailycatch_main b on b.rm_lot_id = a.id where a.lot_id_origin='0' and a.status='0' and a.id not in(select rmLotId from t_rmweightaftergrading)group by b.rm_lot_id";
		 //$qry	= "select a.id,CONCAT(a.alpha_character,a.rm_lotid) as lot_Id from t_manage_rm_lotid a  inner join weighment_data_sheet b on b.rm_lot_id = a.id where a.lot_id_origin='0' and a.status='0' and a.id not in(select rmLotId from t_rmweightaftergrading)";
		//$qry	= "select id,lot_Id from `t_rmreceiptgatepass` where active='1'";
		//$qry	= "select id,new_lot_Id from t_unittransfer where active='1'";
		// $qry="SELECT b.id, b.lot_Id FROM weighment_data_sheet a INNER JOIN t_rmreceiptgatepass b ON a.rm_lot_id = b.id WHERE a.active =  '1'";
		/*$qry = "select c.id,CONCAT(c.alpha_character,c.rm_lotid) as lot_Id from `t_rmreceiptgatepass` a 
				inner join t_rm_receipt_gatepass_supplier b on a.id = b.receipt_gatepass_id 
				inner join t_manage_rm_lotid c on b.id = c.receipt_id 
				inner join weighment_data_sheet d on d.rm_lot_id = c.id";
		$qry.= " where c.id not in (select rmLotId from t_rmweightaftergrading)";*/
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllLotIds($material_type)
	{	
		$qry	= "select a.id,CONCAT(a.alpha_character,a.rm_lotid) as lot_Id from t_manage_rm_lotid a inner join t_dailycatch_main b on b.rm_lot_id = a.id where a.id not in  (select lot_id_origin from t_manage_rm_lotid)  and a.status='0' and a.id not in(select rmLotId from t_rmweightaftergrading where material_type='$material_type')group by b.rm_lot_id";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	function getAllLotIdsPreprocess($material_type)
	{	
	
		$qry	= "select a.id,CONCAT(a.alpha_character,a.rm_lotid) as lot_Id from t_manage_rm_lotid a inner join t_dailypreprocess_rmlotid b on b.rm_lot_id = a.id where a.id not in  (select lot_id_origin from t_manage_rm_lotid) and a.status='0' and a.id not in(select rmLotId from t_rmweightaftergrading where material_type='$material_type')group by b.rm_lot_id";
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	function filterSupplierList($rmLotId)
	{
	$qry="select a.id,a.payment,b.name from t_dailycatch_main a join supplier b on a.payment=b.id where a.rm_lot_id='$rmLotId' order by  b.name asc";
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[1]] = $v[2];
		}
		return $resultArr;
	}
	function filterPondList($supplier,$rmLotId)
	{
	$qry="select a.id,a.pond_name,b.pond_name from t_dailycatch_main a join m_pond_master b on a.pond_name=b.id where a.rm_lot_id='$rmLotId' and a.payment='$supplier' order by b.pond_name asc";
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[1]] = $v[2];
		}
		return $resultArr;
	}
	function filterSpeciesList($pondName,$rmLotId)
	{
	 $qry="SELECT id FROM t_dailycatch_main  WHERE pond_name = '$pondName' and rm_lot_id='$rmLotId'";
	//$qry="SELECT b.id, b.lot_Id FROM weighment_data_sheet a INNER JOIN t_rmreceiptgatepass b ON a.rm_lot_id = b.id WHERE a.active =  '1'";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function filterFishList($mainID)
	{
	 $qry="SELECT fish,fish_code,effective_wt FROM t_dailycatchentry  WHERE main_id = '$mainID'";
	//$qry="SELECT b.id, b.lot_Id FROM weighment_data_sheet a INNER JOIN t_rmreceiptgatepass b ON a.rm_lot_id = b.id WHERE a.active =  '1'";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function filterGradeList($process_code_id)
	{	
		$qry="SELECT id,code FROM m_grade  WHERE id in (SELECT grade_id FROM m_processcode2grade  WHERE processcode_id = '$process_code_id' and unit_select='f')";
		
		//$qry="SELECT b.id, b.lot_Id FROM weighment_data_sheet a INNER JOIN t_rmreceiptgatepass b ON a.rm_lot_id = b.id WHERE a.active =  '1'";
		//$result	= $this->databaseConnect->getRecords($qry);
		//return $result;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	
	}
	function filterGradeListEdit($process_code_id)
	{
		$qry="SELECT id,code FROM m_grade  WHERE id in (SELECT grade_id FROM m_processcode2grade  WHERE processcode_id = '$process_code_id' and unit_select='f')";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getSupplierNm($rm_lot_id)
	{
		// $qry	=	"select id, pond_details,count_code from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
				$qry="SELECT DISTINCT a.id, a.name
					FROM supplier a
					LEFT JOIN t_weightment_data_entries b ON b.supplier_name = a.id
					LEFT JOIN weighment_data_sheet c ON c.id = b.weightment_data_sheet_id
					WHERE c.rm_lot_id = '$rm_lot_id'";
		
	//	select a.id,c.vehicle_Number,d.name,e.name,a.supplier_Challan_No from t_rmreceiptgatepass a left join weighment_data_sheet b on b.rm_lot_id=a.id left join m_vehicle_master c on c.id=a.vehicle_number left join m_billing_company d on d.id=a.Company_Name left join m_plant e on e.id=a.unit where b.rm_lot_id='1'
		
		$result	= $this->databaseConnect->getRecords($qry);
		
		return $result;
	}
	function getPond($rm_lot_id,$supplier)
	{
		// $qry	=	"select id, pond_details,count_code from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
				$qry="SELECT  a.id, a.pond_name
					FROM m_pond_master a
					LEFT JOIN t_weightment_data_entries b ON b.pond_name = a.id
					LEFT JOIN weighment_data_sheet c ON c.id = b.weightment_data_sheet_id
					WHERE c.rm_lot_id = '$rm_lot_id' and b.supplier_name='$supplier' ";
		
	//	select a.id,c.vehicle_Number,d.name,e.name,a.supplier_Challan_No from t_rmreceiptgatepass a left join weighment_data_sheet b on b.rm_lot_id=a.id left join m_vehicle_master c on c.id=a.vehicle_number left join m_billing_company d on d.id=a.Company_Name left join m_plant e on e.id=a.unit where b.rm_lot_id='1'
		
		$result	= $this->databaseConnect->getRecords($qry);
		
		return $result;
	}
	function getGradeVal($fishCode)
	{
	$qry="SELECT id,code FROM m_grade  WHERE id in (SELECT grade_id FROM m_processcode2grade  WHERE processcode_id = '$fishCode' and unit_select='f')";
		//$qry	=	"select id, pond_details,count_code from weighment_data_sheet where rm_lot_id='$rmLotId' order by id asc";
				// $qry="SELECT a.id,a.code FROM m_grade a
					// LEFT JOIN t_rmweightaftergradingdetails b ON b.gradeID = a.id
					// WHERE b.weightment_grading_id = '$editweightmentGradingId'";
		
	//	select a.id,c.vehicle_Number,d.name,e.name,a.supplier_Challan_No from t_rmreceiptgatepass a left join weighment_data_sheet b on b.rm_lot_id=a.id left join m_vehicle_master c on c.id=a.vehicle_number left join m_billing_company d on d.id=a.Company_Name left join m_plant e on e.id=a.unit where b.rm_lot_id='1'
		
		$result	= $this->databaseConnect->getRecords($qry);
		
		return $result;
	}
	function getCompayAndUnit($rmLotId)
	{
		$qry = "SELECT a.company_id,b.name,a.unit_id,c.name FROM t_manage_rm_lotid a 
				LEFT JOIN m_billing_company b ON b.id = a.company_id 
				LEFT JOIN m_plant c ON c.id = a.unit_id 
				WHERE a.id = '".$rmLotId."'";
		$result = $this->databaseConnect->getRecord($qry);
		// print_r($result);
		return $result;
	}
	
	function getFishesInDailycatchentry($rmLotId)
	{
		$qry = "SELECT a.fish FROM t_dailycatchentry a LEFT JOIN t_dailycatch_main b ON b.id = a.main_id WHERE b.rm_lot_id =  '".$rmLotId."' and a.fish !=  ''";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllFishes($rmLotId)
	{	
		$qry = "SELECT id,name FROM m_fish WHERE id IN (SELECT a.fish FROM t_dailycatchentry a left join t_dailycatch_main b on b.id=a.main_id where b.rm_lot_id ='".$rmLotId."')";
		//$qry = "SELECT id,name FROM m_fish WHERE id IN 
		//		(SELECT product_species FROM t_weightment_data_entries WHERE weightment_data_sheet_id = 
		//		(SELECT id FROM weighment_data_sheet WHERE rm_lot_id = '".$rmLotId."')) ";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllFishesInDailyPreprocess($rmLotId)
	{	
		$qry = "SELECT id,name FROM m_fish WHERE id IN (SELECT fish_id FROM t_dailypreprocess_rmlotid where rm_lot_id='".$rmLotId."')";
		//$qry = "SELECT id,name FROM m_fish WHERE id IN 
		//		(SELECT product_species FROM t_weightment_data_entries WHERE weightment_data_sheet_id = 
		//		(SELECT id FROM weighment_data_sheet WHERE rm_lot_id = '".$rmLotId."')) ";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllFishesMaster()
	{
		$qry = "SELECT id,name FROM m_fish where active='1' order by name asc";
		$result = $this->databaseConnect->getRecords($qry);
		return $result;	
	}
	function getAllProcessCode($fishId)
	{
		$qry="SELECT id,code FROM m_processcode  WHERE fish_id = '".$fishId."' ";
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	function getAllProcessCodeList($fishId)
	{
		$qry="SELECT id,code FROM m_processcode  WHERE fish_id = '".$fishId."' ";
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getProcessCode($fishId,$rmlotid)
	{	
		$qry="SELECT id,code FROM m_processcode  WHERE id in (SELECT a.fish_code FROM t_dailycatchentry a left join t_dailycatch_main b on b.id=a.main_id where b.rm_lot_id ='".$rmlotid."' and a.fish='".$fishId."') ";
		//$qry="SELECT id,code FROM m_processcode  WHERE fish_id = '".$fishId."' ";
		
		//$qry="SELECT b.id, b.lot_Id FROM weighment_data_sheet a INNER JOIN t_rmreceiptgatepass b ON a.rm_lot_id = b.id WHERE a.active =  '1'";
		//$result	= $this->databaseConnect->getRecords($qry);
		//return $result;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	
	}
	function getEffectiveWeightold($rmLotId)
	{
		$effectiveWeight = 0;
		$qry = "SELECT SUM(effective_wt) FROM t_dailycatchentry  WHERE main_id IN  
				(SELECT id FROM t_dailycatch_main  WHERE rm_lot_id = '".$rmLotId."')";
		$result = $this->databaseConnect->getRecord($qry);
		if(sizeof($result) > 0) { 
			$effectiveWeight = $result[0];
		}
		return $effectiveWeight;
	}
	function getEffectiveWeight($rmLotId)
	{
		$effectiveWeight = 0;
			
		$qry = "SELECT total_quantity FROM weighment_data_sheet WHERE rm_lot_id = '".$rmLotId."'";
		$result = $this->databaseConnect->getRecord($qry);
		if(sizeof($result) > 0) { 
			$effectiveWeight = $result[0];
		}
		return $effectiveWeight;
		//return $result;
	}
	function getWeightmentSizeofRmlotId($rmLotId)
	{
		$qry="SELECT a.id FROM  t_weightment_data_entries a  left join weighment_data_sheet b on b.id=a.weightment_data_sheet_id where b.rm_lot_id ='".$rmLotId."'";
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function updateWeighmentgradingconfirm($weighmentId){
		$qry	= "update t_rmweightaftergrading set active='1' where id=$weighmentId";
 	//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	function updateWeighmentgradingReleaseconfirm($weighmentId){
	$qry	= "update t_rmweightaftergrading set active='0' where id=$weighmentId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	function getProcessCodeDailyProcess($fishId,$rmlotid)
	{
		
		$qry="select id,code from m_processcode where id in  (select SUBSTRING_INDEX(c.processes,',',-1) from  t_dailypreprocess_entries_rmlotid a left join t_dailypreprocess_rmlotid  b on a.dailypreprocess_main_id=b.id left join m_process c  on c.id=a.process  where b.rm_lot_id='$rmlotid' and b.fish_id='$fishId')";
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select --');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	function getProcessCodeList($fishId,$rmlotid)
	{
		$qry="SELECT id,code FROM m_processcode  WHERE id in (SELECT a.fish_code FROM t_dailycatchentry a left join t_dailycatch_main b on b.id=a.main_id where b.rm_lot_id ='".$rmlotid."' and a.fish='".$fishId."') ";
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getProcessCodeDailyProcessList($fishId,$rmlotid)
	{
		$qry="select id,code from m_processcode where id in  (select SUBSTRING_INDEX(c.processes,',',-1) from  t_dailypreprocess_entries_rmlotid a left join t_dailypreprocess_rmlotid  b on a.dailypreprocess_main_id=b.id left join m_process c  on c.id=a.process  where b.rm_lot_id='$rmlotid' and b.fish_id='$fishId')";
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		return $result;
	}
}
?>